<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

defined('THREED_PATH') or die('Hacking attempt!');

add_event_handler('picture_pictures_data', 'threed_prepare_picture');
function threed_prepare_picture($picture)
{
	if ($picture['current']['is3D'] == 'true')
	{
		// remove default parser
		remove_event_handler('render_element_content', 'default_picture_content', EVENT_HANDLER_PRIORITY_NEUTRAL);
		// add custom parser
		add_event_handler('render_element_content', 'threed_render_image', EVENT_HANDLER_PRIORITY_NEUTRAL-10, 2);
		add_event_handler('get_element_metadata_available', 'threed_metadata_available');
	}
	elseif ($picture['current']['pano_type'] != 'none')
	{
		add_event_handler('render_element_content', 'threed_render_panorama', EVENT_HANDLER_PRIORITY_NEUTRAL-10, 2);
	}
	
	return $picture;
}


function threed_render_image($content, $image)
{
	global $conf, $template, $threed_image_exts;

	$element = $image['element_url'];
	if (!isset($element) or !empty($content)) {
		// nothing to do or someone hooked us
		return $content;
	} 
	
	$template->set_filenames(array(
		'threed_photo_content' => THREED_PATH . 'template/photo3d.tpl',
		'threed_video_content' => THREED_PATH . 'template/video3d.tpl',
	));

	$extension = strtolower(get_extension($element));
	$is_image = in_array($extension, $threed_image_exts) || $extension == 'jpg';
	
	$template->assign( 
		array(
			'PHPWG_ROOT_PATH' => PHPWG_ROOT_PATH,
			'THREED_PATH'     => THREED_PATH,
			'THREED_CONF'     => $conf['threed'],
			'SRC_IMG'         => $element,
			'EXTENSION'       => $extension,
			'REPRESENT'       => get_absolute_root_url().substr ($image['src_image']->rel_path, 2), //TODO : use derivative if possible
			'URL'             => get_absolute_root_url().$image['url'],
			'IMAGE'           => $image,
		 )
	);
	return $template->parse($is_image ? 'threed_photo_content' : 'threed_video_content', true);
}

function threed_render_panorama($content, $image)
{
	global $conf, $template;

	$element = $image['element_url'];
	if (!isset($element) or !empty($content)) {
		return $content;
	}
	$ext = get_extension($element);
	$template->set_filename('threed_pano_content', THREED_PATH . 'template/panorama.tpl'); 
	$template->assign( 
		array(
			'THREED_PATH'     => THREED_PATH,
			'EXT'             => $ext,
			'THREED_CONF'     => $conf['threed'],
			'SRC_XML'         => get_filename_wo_extension($element).'.xml',
			'REPRESENT'       => get_absolute_root_url().substr ($image['src_image']->rel_path, 2),
			'URL'             => get_absolute_root_url().$image['url'],
			'IMAGE'           => $image,
		 )
	);
	return $template->parse('threed_pano_content', true);
}

function threed_metadata_available($status, $path) {
	return true;  //TODO
}

?>