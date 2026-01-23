<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

// Create initial video representative picture
add_event_handler('begin_delete_elements', 'threed_delete');
function threed_delete ($ids)
{
	foreach($ids as $id)
	{
		if (is_pano($id))
		{
			$infos = get_image_infos($id);
			$path = $infos['path'];
			deltree(dirname($path).'/panos');
			// @unlink($path);
			unlink(str_replace('zip', 'xml', $path));
		}
	}
}

?>
