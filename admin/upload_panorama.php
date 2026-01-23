<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

if(!defined('THREED_PATH')) die('Hacking attempt!');

// Extract multires panorama files and create initial pano representative picture
add_event_handler ('upload_file', 'threed_upload_panorama');
function threed_upload_panorama($representative_ext, $file_path) {
	global $page, $conf;

	// exit immediately if extension does not correspond so an other driver can do the job
	if (isset($representative_ext) or (get_extension($file_path) != 'zip')) {
		return $representative_ext;
	}

	$representative_ext = null;
	$zip = new ZipArchive;
	$res = @$zip->open($file_path);
	if ($res === TRUE)
	{
		if($zip->locateName('panos/') !== false and $zip->locateName('tour.xml') !== false) {
			$framework = 'krpano';
		} elseif(0) {
			$framework = 'pannellum';
		} elseif(0) {
			$framework = '3dvista';
		} else {
			$zip->close();
			$page['errors'][] = l10n('No panorama found in ZIP archive');
			return null;
		}

		if($zip->extractTo(dirname($file_path))) {
			$zip->close();
			switch ($framework) {
				case 'krpano':
					prepare_krpano($file_path);
					break;
				case 'pannellum':
					prepare_pannellum($file_path);
					break;
				case '3dvista':
					prepare_3dvista($file_path);
					break;
			}
//			if ($conf['delArchive']) {
//				@unlink ($file_path);
//			}
			$page['infos'][] = l10n('krpano archive extracted successfully');
		}
		else {
			$zip->close();
			$page['errors'][] = l10n('ZIP archive extraction failed');
		}
	}
	else
	{
		$page['errors'][] = l10n('couldn\'t open ZIP archive');
	}
	if (count($page['errors']) == 0) {
		$representative_ext = 'jpg';
	}
	return $representative_ext;
}

function prepare_krpano ($file_path)
{
	global $page;
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
					$page['errors'][] = l10n('Representative image creation failed');
				}
			}
		}
		$xml_file = $dir . "/tour.xml";
		if(file_exists($xml_file)) {
			$content = file_get_contents($xml_file);
			$content = str_replace(array('url="skin/', 'url="plugins/'), array('url="%VIEWER%/skin/', 'url="%VIEWER%/plugins/'), $content);
			file_put_contents($xml_file, $content);
			// rename xml file
			rename($xml_file, get_filename_wo_extension($file_path).'.xml');
		}
		else
		{
			$page['errors'][] = l10n('No xml file found');
		}
	}
	else
	{
		$page['errors'][] = l10n('The archive format is not krpano compatible');
	}
}


function prepare_pannellum($file_path)
{
	// TODO
}

function prepare_3dvista($file_path)
{
	// TODO
}

// Add pano_type to the database
add_event_handler('loc_end_add_uploaded_file', 'threed_pano_set_flags');
function threed_pano_set_flags($image_infos)
{
	global $page;

	$file_path = $image_infos ['path'];
	$dir = dirname($file_path);
	$ext = get_extension($file_path);

	if($ext == 'zip')
	{
		if(is_dir($dir . '/panos') and file_exists(get_filename_wo_extension($file_path).'.xml')) {
			$type = 'krpano';
		} elseif(0) {
			$type = 'pannellum';
		} elseif(0) {
			$type = '3Dvista';
		} else {
			$page['errors'][] = l10n('No supported panorama directory structure');
			return;
		}
		$query = 'UPDATE ' .IMAGES_TABLE. ' SET pano_type=\'' . $type . '\' WHERE id=' .$image_infos ['id']. ';';
		pwg_query($query);
	}
}


?>