<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo and video extension for Piwigo                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2014-2016 Jean-Paul MASSARD          http://jpmassard.fr |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

defined('THREED_PATH') or die('Hacking attempt!');

// for pwg_image_infos()
// include_once(PHPWG_ROOT_PATH . 'admin/include/functions_upload.inc.php');

function pwg_image_infos($path)
{
  list($width, $height) = getimagesize($path);
  $filesize = floor(filesize($path)/1024);

  return array(
    'width'  => $width,
    'height' => $height,
    'filesize' => $filesize,
    );
}

function threed_render_image($content, $image)
{
	global $conf, $template, $threed_image_exts;
	//
	$element = $image['element_url'];
    if (!isset($element) or !empty($content))
	{ // nothing to do or someone hooked us - so we skip;
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
            'REPRESENT'       => get_absolute_root_url().substr ($image['src_image']->rel_path, 2),
            'URL'		      => get_absolute_root_url().$image['url'],
            'DESCRIPTION'     => $image['comment'] | '',
            'AUTHOR'          => $image['author'] | '',
	        'FILE_INFO'       => pwg_image_infos($element),
	     )
	);
	return $template->parse($is_image ? 'threed_photo_content' : 'threed_video_content', true);
}

/**
 * some stuff at the begining of picture.php
 */
function threed_prepare_picture($picture)
{
	global $threed_image_exts, $threed_video_exts;
    
    $extension = strtolower(get_extension($picture['current']['file']));
	if (in_array($extension, $threed_image_exts) || in_array($extension, $threed_video_exts) || $extension == 'jpg')
    {
        // remove default parser
        remove_event_handler('render_element_content', 'default_picture_content', EVENT_HANDLER_PRIORITY_NEUTRAL);
        // add custom parser
        add_event_handler('render_element_content', 'threed_render_image', EVENT_HANDLER_PRIORITY_NEUTRAL-10, 2);
        add_event_handler('get_element_metadata_available', 'threed_metadata_available');
    }
    return $picture;
}

function threed_metadata_available($status, $path){
	return true;
}
