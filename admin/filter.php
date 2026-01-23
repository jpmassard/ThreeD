<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

defined('THREED_PATH') or die('Hacking attempt!');


// Add the filter to the list
add_event_handler('get_batch_manager_prefilters', 'threed_add_bm_filter');
function threed_add_bm_filter($prefilters)
{
	load_language('plugin.lang', dirname(__FILE__).'/');

	array_push($prefilters,
		array('ID' => '3d_images', 'NAME' => l10n('3D images')),
		array('ID' => 'not_3d_images', 'NAME' => l10n('All images but 3D')),
		array('ID' => 'pano_images', 'NAME' => l10n('Panoramas images')),
		array('ID' => 'not_pano_images', 'NAME' => l10n('All images but panoramas'))
	);

	return $prefilters;
}

// Get images according to the prefilter
add_event_handler('perform_batch_manager_prefilters', 'threed_perform_bm_filter', 50, 2);
function threed_perform_bm_filter($filter_sets, $prefilter)
{
	switch($prefilter) {
		case '3d_images': $condition = 'is3d=\'true\';';
			break;
		case 'not_3d_images': $condition = 'is3d=\'false\';';
			break;
		case 'pano_images': $condition = 'pano_type<>\'none\';';
			break;
		case 'not_pano_images': $condition = 'pano_type=\'none\';';
			break;
	}

	$query = 'SELECT id FROM ' .IMAGES_TABLE.' WHERE ' . $condition;
	$filter_sets[] = array_from_query($query, 'id');

	return $filter_sets;
}

// Are the filters set correctly ?
add_event_handler('element_set_global_action', 'threed_element_set_bm_global_action');
function threed_element_set_bm_global_action($action)
{
	if (in_array(@$_SESSION['bulk_manager_filter']['prefilter'], array('3d_images', 'not_3d_images', 'pano_images', 'not_pano_images')) and $action == 'threed') {
		// let's refresh the page because the current might be modified
		redirect(get_root_url().'admin.php?page='.$_GET['page']);
	}
}

?>