<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

defined('THREED_PATH') or die('Hacking attempt!');

// Add a prefilter
add_event_handler('loc_begin_admin', 'threed_set_prefilter_modify');
// add_event_handler('loc_begin_admin_page', 'threed_modify_submit', 45 );

// Change the variables used by the function that changes the template
add_event_handler('loc_begin_admin_page', 'threed_modify_params');

function threed_set_prefilter_modify()
{
	global $template;
	
	$template->set_prefilter('picture_modify', 'threed_modify');
}

function threed_modify($content) // TODO find a more universal way to do this
{
	$search = "#<strong>{'Creation date'#";

	// thanks to Copyright plugin...
	$replacement = '<strong>{\'ThreeD image type\'|@translate}</strong>
		<br>
		<div style="border:1px solid #ffcbb3;border-radius:2px;margin:0 1em;padding:7px">
			<input type="radio" name="img_type" value="normal" {if $isnormal}checked{/if}>{\' Normal\'|translate}<br>
			<input type="radio" name="img_type" value="stereo" {if $is3d}checked{/if}>{\' Stereoscopic\'|translate}<br>
			<input type="radio" name="img_type" value="pano" {if $pano != "none"}checked{/if}>{\' Panorama\'|translate}{if $pano != "none"} ({$pano}){/if}<br>
		</div>
  <p>
		<strong>{\'Creation date\'';

    return preg_replace($search, $replacement, $content);
}

function threed_modify_params()
{
	if (!isset($_GET['page']) or 'photo' != $_GET['page'] or !isset($_GET['image_id'])) {
		return;
	}

	check_input_parameter('image_id', $_GET, false, PATTERN_ID);

	global $template, $threed_image_exts;

	$image_id = $_GET['image_id'];
	$img_infos = get_image_infos($image_id);
	$ext = get_extension($img_infos['path']);

	if(isset($_POST['submit'])) {
		if($_POST['img_type'] == 'pano') {
			$pano_type = check_pano($img_infos);
			if($pano_type != null and $img_infos['is3D'] == 'false') {
				set_pano($image_id, $pano_type);
				$img_infos['pano_type'] = $pano_type;
			}
		} elseif($_POST['img_type'] == 'stereo') {
			if($ext == 'jpg' and $img_infos['pano_type'] == 'none') {
				set_3D_material($image_id, 'true');
				$img_infos['is3D'] = 'true';
			}
		} else {
			set_pano($image_id, 'none');
			$img_infos['pano_type'] = 'none';
			if(!in_array($ext, $threed_image_exts)) {
				set_3D_material($image_id, 'false');
				$img_infos['is3D'] = 'false';
			}
		}
	}

	$template->assign(array(
		"is3d"     => $img_infos['is3D'] == 'true',
		"pano"   => $img_infos['pano_type'],
		"isnormal" => $img_infos['pano_type'] == 'none' and $img_infos['is3D'] == 'false',
	));
}


?>