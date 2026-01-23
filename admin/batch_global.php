<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

defined('THREED_PATH') or die('Hacking attempt!');

//check_status(ACCESS_ADMINISTRATOR);

// Add threed drop down menu to the batch manager
add_event_handler('loc_end_element_set_global', 'threed_batch_global');
function threed_batch_global()
{
	global $template, $page;

	$template->set_filename('threed_batch_global', dirname(__FILE__).'/template/batch_global.tpl');
	switch($page['prefilter']) {
		case 'not_3d_images':
			$threed_actions = array('make_3d', 'set_representative');
			break;
		case '3d_images':
			$threed_actions = array('make_normal');
			break;
		case 'not_pano_images':
			$threed_actions = array('make_pano');
			break;
		case 'pano_images':
			$threed_actions = array('make_3d', 'make_normal');
			break;
	}

	$template->assign('threed_options', array(
		'opt1' => 'toto',
		'opt2' => 'titi',
	));


	// Add info on the "choose action" dropdown in the batch manager
	$template->append('element_set_global_plugins_actions', array(
		'ID' => 'threed',
		'NAME' => l10n('Set 3D  options'),
		'CONTENT' => $template->parse('threed_batch_global', true)
		)
	);
}

// Process the submit action
add_event_handler('element_set_global_action', 'threed_batch_global_submit', 50, 2);
function threed_batch_global_submit($action, $collection)
{
	// If not called by threed
	if ($action != 'threed') {
		return;
	}

/*	$crID = pwg_db_real_escape_string($_POST['copyrightID']);

	// Delete any previously assigned copyrights
	if (count($collection) > 0) {
		$query = sprintf(
			'DELETE
			FROM %s
			WHERE media_id IN (%s)
			;',
		COPYRIGHTS_MEDIA, implode(',', $collection));
		pwg_query($query);
	}

	// If you assign no copyright, dont put them in the table
	if ($crID != '') {
		// Add the copyrights from the submit form to an array
		$edits = array();
		foreach ($collection as $image_id) {
			array_push(
				$edits,
				array(
					'media_id' => $image_id,
					'cr_id' => $crID,
				)
			);
		}

		// Insert the array into the database
		mass_inserts(
			COPYRIGHTS_MEDIA,		// Table name
			array_keys($edits[0]),	// Columns
			$edits					// Data
		);
	}
*/
}

?>