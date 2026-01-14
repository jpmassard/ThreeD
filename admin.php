<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

if(!defined('THREED_PATH')) die('Hacking attempt!');

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

check_status(ACCESS_ADMINISTRATOR);

global $template, $page, $conf, $admin_photo_base_url;

$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : 'config';

$errors = array();

if (isset($_POST['save_config']))
{
    $configThreed = array(
        'chromeCast'     => isset($_POST['chromecast']) ? true : false,
        'openGraph'      => isset($_POST['opnGraphAllowed']) ? true : false,
        'video_autoplay' => isset($_POST['video_autoplay']) ? true : false,
        'video_autoloop' => isset($_POST['video_autoloop']) ? true : false,
        'icon2Dphoto'    => isset($_POST['photo2d']) ? true : false,
        'icon2Dvideo'    => isset($_POST['video2d']) ? true : false,
        'icon3Dphoto'    => isset($_POST['photo3d']) ? true : false,
        'icon3Dvideo'    => isset($_POST['video3d']) ? true : false,
        'icon360Pano'    => isset($_POST['pano360']) ? true : false,
        'iconposition'   => $_POST['iconposition'],
        'iconxpos'       => $_POST['iconposition']=='custom' ? $_POST['iconxpos'] : 50,
        'iconypos'       => $_POST['iconposition']=='custom' ? $_POST['iconypos'] : 50,
        'iconalpha'      => $_POST['iconalpha'],
    );

    // make some test on critiical values
    $oor = l10n(' must be in the range [0..100]');
    if($configThreed['iconxpos'] < 0 or $configThreed['iconxpos'] > 100 ) $errors['xpos'] = 'x pos'. $oor;
    if($configThreed['iconypos'] < 0 or $configThreed['iconypos'] > 100 ) $errors['ypos'] = 'y pos'. $oor;
    if($configThreed['iconalpha'] < 0 or $configThreed['iconalpha'] > 100 ) $errors['opacity'] = 'opacity'. $oor;

  if(count($errors) == 0)
  {
    conf_update_param('threed', $configThreed, true);
    $page['infos'][] = l10n('Your configuration settings are saved');
  }
  unset($_POST['save_config']);
} 

elseif (isset($_POST['save_settings']))
{
    include_once(PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php');

    $image_id = $page['tab'];
    $img_infos = get_image_infos($image_id, true);
    $file_path = $img_infos['path'];

    if(isset($_POST['3Dmaterial']) and $_POST['3Dmaterial'] == 'on')
    {
        set_3D_material($image_id, 1);
    }
    else
    {
        set_3D_material($image_id, 0);
    }
    if(isset($_POST['unzipArchive']) and $_POST['unzipArchive'] == 'on')
    {
        $zip = new ZipArchive;
        $res = @$zip->open($file_path);
        if ($res === TRUE)
        {
            if($zip->extractTo(dirname($file_path)))
            {
                $zip->close();
                if (isset($_POST['delArchive']) && $_POST['delArchive'] == 'on')
                {
                    @unlink ($file_path);
                }
                if ($_POST['framework'] == 'krpano')
                {
                    $framework = 1;
                    prepare_krpano($file_path);
                }
                else
                {
                    $framework = 2;
                    prepare_pannellum($file_path);
                }
                $query = 'UPDATE '.IMAGES_TABLE. ' SET isPano=' .$framework. ', representative_ext=\'jpg\' WHERE id='.$image_id;
                pwg_query($query);
            }
            else
            {
                $zip->close();
                $errors [] = l10n('ZIP archive extraction failed');
            }
            $page['infos'][] = l10n('krpano archive extracted successfully');
        }
        else
        {
            $errors [] = l10n('couldn\'t open ZIP archive');
        }
    }
    if(isset($_POST['uploadRepresentative']) && $_POST['uploadRepresentative'] == 'on')
    {
        if(isset($_FILES['rFile']) )
        {
             $name = $_FILES['rFile']['name'];
             if($name != '')
             {
                // Create the new file in pwg_representative sub-directory
                $representative_file_path = dirname($file_path).'/pwg_representative/' . get_filename_wo_extension(basename($file_path)).'.';

                // In replace case, delete old representative file and derivatives
                if($img_infos['representative_ext'] != null)
                {
                    @unlink($representative_file_path . $img_infos['representative_ext']);
                    delete_element_derivatives($img_infos);
                }
                $rExt = get_extension($name);
                $representative_file_path.= $rExt;

                prepare_directory(dirname($representative_file_path));
                move_uploaded_file($_FILES['rFile']['tmp_name'], $representative_file_path);

                $query = 'UPDATE ' . IMAGES_TABLE . ' SET representative_ext=\'' . $rExt . '\' WHERE id=\'' . $image_id .'\';';
                pwg_query($query);

                // Everything is OK, tell this to admin
                $page['infos'][] = l10n('The representative picture was updated');

            }
            else
            {
                $errors[] = l10n('A representative picture must be selected');
            }
        }
    }
    unset($_POST['save_settings']);
}

$themeconf = $template->get_template_vars('themeconf');

$tabsheet = new tabsheet();

if('config' == $page['tab'])
{
    $tabsheet->add('config', l10n('Configuration'), THREED_ADMIN . '-config');
    $tabsheet->select('config');
    $tabsheet->assign();
    
    // template
    $template->set_filename('threed_admin_content', THREED_PATH .'admin/template/config.tpl');
    $template->assign(array(
        'threed' => $conf['threed'],
        'theme' => $themeconf,
    ));
}
else
{
    check_input_parameter('tab', $_GET, false, PATTERN_ID);
    $image_id = $_GET['tab'];
    if($image_id == '')
    {
        return; // needed when two clicks on tabsheet... TODO
    }
    $admin_photo_base_url = get_root_url().'admin.php?page=photo-'. $image_id;
    
    $tabsheet->set_id('photo');
    $tabsheet->select('threed');
    $tabsheet->assign();
    
    $img_infos = get_image_infos($image_id, true);
    $file_path = $img_infos['path'];
    $ext = get_extension($file_path);

    if($ext == 'zip' or $img_infos['isPano'])
    {
        $template->set_filename('threed_admin_content', THREED_PATH .'admin/template/pano_admin.tpl');
        
        $template->assign(array(
            'ADMIN_PAGE_TITLE' => l10n('Edit Panorama') . ' <span class="image-id">#'.$image_id.'</span>',
            'isPano' => $img_infos['isPano'],
            'image_id' => (int)$image_id,
            'threed' => $conf['threed'],
            'theme' => $themeconf,
        ));
    }
    else
    {
        $template->set_filename('threed_admin_content', THREED_PATH .'admin/template/image_admin.tpl');
        
        $template->assign(array(
            'ADMIN_PAGE_TITLE' => l10n('Edit photo') .' <span class="image-id">#'.$image_id.'</span>',
            'is3D' => $img_infos['is3D'],
            'image_id' => (int)$image_id,
            'threed' => $conf['threed'],
            'theme' => $themeconf,
        ));
    }
}

if(count($errors) != 0)
{
	$template->assign('errors', $errors);
}
$template->assign_var_from_handle('ADMIN_CONTENT', 'threed_admin_content');
unset($errors);


function prepare_krpano ($file_path)
{
    global $errors;
    $dir = dirname($file_path);
    $files = scandir($dir . '/panos');
    if($files)
    {
        $files = array_diff($files, array('.', '..'));
        foreach($files as $file)
        {
            if(is_dir($dir . '/panos/' . $file))
            {
                // Copy the krpano thumb.jpg file in pwg_representative sub-directory
                $representative_file_path = dirname($file_path).'/pwg_representative';
                prepare_directory($representative_file_path);
                $jpgpath = get_filename_wo_extension(basename($file_path)).'.jpg';
                if (!copy ($dir . '/panos/' . $file . '/thumb.jpg', $representative_file_path . '/' . $jpgpath))
                {
                    $errors[] = l10n('Representative image creation failed');
                }
            }
        }
        $files = glob($dir . "/*.xml");
        if(count($files) > 0)
        {
            $content = file_get_contents($files[0]);
            $content = str_replace(array('url="skin/', 'url="plugins/'), array('url="%VIEWER%/skin/', 'url="%VIEWER%/plugins/'), $content);
            file_put_contents($files[0], $content);
            // rename xml file
            rename($files[0], get_filename_wo_extension($file_path).'.xml');
        }
        else
        {
            $errors[] = l10n('No xml file found');
        }
    }
    else
    {
        $errors[] = l10n('The archive format is not krpano compatible');
    }
}


function prepare_pannellum($file_path)
{
    
}


?>
