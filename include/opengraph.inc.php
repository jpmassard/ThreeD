<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo and video extension for Piwigo                    |
// | Add OpenGraph tags to category pages and 3D pictures                  |
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

// Prepare header for Open Graph meta tags
function threed_loc_end_page_header ()
{
    global $conf, $template;
    if ($conf['threed']['openGraph'] == 1)
        $template->set_prefilter('header', 'threed_header');
}

function threed_header($content, &$smarty)
{
  // replace html tag
  $search = '<html ';
  $replacement = '<html xmlns:og="http://ogp.me/ns#" ';

  return preg_replace('#'.$search.'#', $replacement, $content);
 }

function threed_loc_begin_index()
{
    global $template, $page, $conf;
    if ($conf['threed']['openGraph'] == 0)
        return;
        
    if (array_key_exists('category', $page))
    {
        set_make_full_url();
        $url = duplicate_index_url();
        unset_make_full_url();

        $category = $page['category'];
        $title= $category['name'];
        $comment= $category ['comment'] | ' ';
        if (!empty($category['representative_picture_id']))
        {
            $query = '
            SELECT id, file, path, representative_ext
            FROM '.IMAGES_TABLE.'
            WHERE id = '.$category['representative_picture_id'].'
            ;';

            $result = pwg_query($query);
            if (pwg_db_num_rows($result) > 0)
            {
                $element = pwg_db_fetch_assoc($result);
                $img = get_absolute_root_url().substr (original_to_representative( get_element_path($element), $element['representative_ext'] ),4);
            }
        }
        else
            $img = ' ';
    }
    else
    {
        // Main page
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
            'DESCRIPTION'     => strip_tags($comment)
            )
	);
    $template->append('head_elements', $template->parse('threed_header_meta', true));
}
