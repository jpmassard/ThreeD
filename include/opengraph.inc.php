<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo and video extension for Piwigo                    |
// | Add OpenGraph tags to category pages and 3D pictures                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
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

// Prepare header for Open Graph meta tags
add_event_handler('loc_end_page_header', function () {
	global $conf, $template;

	if ($conf['threed']['openGraph'] == 1 and script_basename()=='index') {
		$template->set_prefilter('header', 'threed_header_replace');
	}
});

function threed_header_replace($content, $smarty)
{
	$search = '<html ';
	$replacement = '<html xmlns:og="http://ogp.me/ns#" ';
	return preg_replace('#'.$search.'#', $replacement, $content);
}

add_event_handler('loc_begin_index', function () {
	global $template, $page, $conf;

	if ($conf['threed']['openGraph'] == 1 and script_basename()=='index') {
			if (array_key_exists('category', $page)) {
			$type = 'gallery';
			set_make_full_url();
			$url = duplicate_index_url();
			unset_make_full_url();
	
			$category = $page['category'];
			$title= $category['name'];
			$comment= $category ['comment'] ?: ' ';
			if (!empty($category['representative_picture_id']))
			{
				$img = '';
				$ext = '';
				$query = 'SELECT id, file, path, representative_ext FROM '.IMAGES_TABLE.' WHERE id = '.$category['representative_picture_id'].';';
				$result = pwg_query($query);
				if (pwg_db_num_rows($result) > 0)
				{
					$element = pwg_db_fetch_assoc($result);
					$ext = get_extension($element['path']);
					if($ext == 'jpg') {
						$img = get_absolute_root_url().get_element_url($element);
					} else {
						$img = get_absolute_root_url().substr (original_to_representative( get_element_url($element), $element['representative_ext'] ),4);
					}
				}
				else
					return;
			}
			else
				return;
		}
		else
		{
			// Main page
			$type = 'website';
			$url= get_absolute_root_url();
			$title= $conf['gallery_title'];
			$img= '';
			$comment= l10n('This gallery contains 3D photos and videos');
		}

		$template->set_filename('threed_header_meta', THREED_PATH . 'template/header.tpl');
		$template->assign(
			array(
				'TITLE'       => $title,
				'URL'		  => $url,
				'IMAGE'       => $img,
				'DESCRIPTION'     => strip_tags($comment),
				'TYPE'          => $type
				)
		);
		$template->append('head_elements', $template->parse('threed_header_meta', true));
	}
});
