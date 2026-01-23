<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

defined('THREED_PATH') or die('Hacking attempt!');

// Add template for threed single batch mode
add_event_handler('loc_end_element_set_unit', 'threed_loc_end_element_set_unit');
function threed_loc_end_element_set_unit()
{
	global $template;
	
	//$template->assign('THREED_PATH', THREED_PATH);
	//$template->append('PLUGINS_BATCH_MANAGER_UNIT_ELEMENT_SUBTEMPLATE', 'plugins/ThreeD/admin/template/batch_single.tpl');
}


/*    for (let key_index in pluginValues) {
        let pluginValues_selector = pluginValues[key_index].selector;
        let full_selector = $("#picture-" + pictureId + " " + pluginValues_selector);
        let pluginValues_value = full_selector.val();
        ajax_data[pluginValues[key_index].api_key] = pluginValues_value;
*/

?>
