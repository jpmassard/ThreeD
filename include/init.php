<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

defined('THREED_PATH') or die('Hacking attempt!');

add_event_handler('init', 'threed_init');
function threed_init() {
	global $conf,$threed_image_exts;

	// Add 3D extension support for images
	$threed_image_exts= array('jps', 'mpo');

	// Add 3D extension support for videos and 3D videos
	global $threed_video_exts;
	$threed_video_exts= array('mp4', 'webm');

	$conf['upload_form_all_types']= true;
	$conf['file_ext'] = array_merge ($conf['file_ext'], $threed_image_exts);
	$conf['file_ext'] = array_merge ($conf['file_ext'], $threed_video_exts);

	// load plugin language file
	load_language('plugin.lang', THREED_PATH);
	
	// prepare threed configuration
	$conf['threed'] = safe_unserialize($conf['threed']);
}

// Add some usefull functions
function is_3D_material($id)
{
	$query = 'SELECT is3D FROM '.IMAGES_TABLE. ' WHERE id=\''.$id. '\';';
	$element_info = pwg_db_fetch_assoc(pwg_query($query));
	return $element_info ['is3D'] == 'true';
}

function is_pano($id)
{
	$query = 'SELECT pano_type FROM '.IMAGES_TABLE. ' WHERE id=\''.$id. '\';';
	$element_info = pwg_db_fetch_assoc(pwg_query($query));
	return $element_info ['pano_type'] != 'none';
}

function set_3D_material($id, $val)
{
	$query = 'UPDATE '.IMAGES_TABLE. ' SET is3D=\'' . $val . '\' WHERE id=\''.$id. '\';';
	pwg_query($query);
}

function set_pano($id, $val)
{
	$query = 'UPDATE '.IMAGES_TABLE. ' SET pano_type=\'' . $val . '\' WHERE id=\''.$id. '\';';
	pwg_query($query);
}

function check_pano($image_infos) {
	$file_path = $image_infos ['path'];
	$dir = dirname($file_path);
	$ext = get_extension($file_path);

	if($ext == 'zip')
	{
		if(is_dir($dir . '/panos') and file_exists(get_filename_wo_extension($file_path).'.xml')) {
			return 'krpano';
		} elseif(0) {
			return 'pannellum';
		} elseif(0) {
			return '3Dvista';
		} else {
			return null;
		}
	} elseif ($ext == 'jpg') { // TODO warn the admin...
		return 'pannellum';
	} else {
		return null;
	}

}

?>
