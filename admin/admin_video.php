<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

defined('THREED_PATH') or die('Hacking attempt!');

// Check access and exit if user has no admin rights
check_status(ACCESS_ADMINISTRATOR);

include_once ('include/upload_video.inc.php');

global $template;

check_input_parameter('image_id', $_GET, false, PATTERN_ID);

$query = 'SELECT path, name, representative_ext FROM '.IMAGES_TABLE.' WHERE id = '.$_GET['image_id'].';';
$result = pwg_query($query);
$row = pwg_db_fetch_assoc($result);
$file_path = $row['path'];
$name= $row['name'];
$repExt= $row['representative_ext'];

$representative_file_path = dirname($file_path).'/pwg_representative/';
$representative_file_path.= get_filename_wo_extension(basename($file_path)).'.jpg';
$uploader_errors = array();

if (isset($_POST['submit']))
{
    $file = array();
    
    if($_FILES['file']['error'] == UPLOAD_ERR_OK)
    {
        $error= threed_video_upload_thumbnail($_FILES['file'], $representative_file_path);
        if ($repExt == 'jpg')
        {
           delete_element_derivatives(array('path' => $file_path,
                                            'representative_ext' => 'jpg',));
        }
        else
        {
            list($width, $height) = getimagesize($file_path);
            $update = array(
                'width' => $width,
                'height' => $height,
                'representative_ext' => 'jpg',
            );
            single_update(IMAGES_TABLE, $update, array('id' => $_GET['image_id']));
        }    

        if ($error != null)
            $uploader_errors['file']['upload'] = $error;
    }
    else if($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE)
    {
        $uploader_errors['file']['file_too_large'] = l10n('File exceeds the upload_max_filesize directive in php.ini');
    } 
    else
    {
        $uploader_errors['file']['no_file'] = l10n('Specify a file to upload');
    }
    
    if (count($uploader_errors) == 0)
    {
        array_push($page['infos'], l10n('File uploaded succesfully'));
    }
    else
    {
        array_push($page['errors'], l10n('There have been errors. See below'));
        $template->assign('threed_uploader_errors', $threed_uploader_errors);
        $template->assign('file_uploader', $_POST['file_uploader']);
    }
}
    
$template->assign('name', $name);
$template->set_filename('plugin_admin_content', dirname(__FILE__). '/template/admin_video.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');


function threed_video_upload_thumbnail($thumbnail, $representative_file_path)
{
    if ($thumbnail['type'] != 'image/jpeg')
        return l10n('bad file type');
    // Move temporary file to destination directory
    if (!move_uploaded_file($thumbnail['tmp_name'], $representative_file_path))
        return l10n('Can\'t upload file to galleries directory');

    threed_thumbnail_video_watermark ($representative_file_path);    
    return null;
}

?>
