<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD          http://jpmassard.fr |
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

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

define('THREED_DIRECTORY', PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)) . '/');

class ThreeD_maintain extends PluginMaintain
{
	private $installed = false;
	
	private $default_conf = array(
		'chromeCast' => 0,
		'openGraph'  => 1,
		'video_autoplay' => 1,
		'video_autoloop' => 0,
		'icon_2Dphoto' => 0,
		'icon_2Dvideo' => 0,
		'icon_3Dphoto' => 1,
		'icon_3Dvideo' => 1,
		'icon_360Pano' => 1,
		'icon_position' => 'center',
		'icon_xpos' => 0,
		'icon_ypos' => 0,
		'icon_alpha' => 80,
		);
	
	function __construct($plugin_id)
	{
		parent::__construct($plugin_id);
	}

	function install($plugin_version, &$errors=array())
	{
		global $conf;
	
		// add config parameter
		if (empty($conf['threed']))
		{
			// conf_update_param well serialize and escape array before database insertion
			// the third parameter indicates to update $conf['threed'] global variable as well
			conf_update_param('threed', $this->default_conf, true);
			// Add columns is3D and pano_type. Ensure all existing pictures have is3D false
			$query = 'ALTER TABLE ' .IMAGES_TABLE. ' ADD is3D ENUM(\'true\',\'false\') NOT NULL DEFAULT \'false\', ADD pano_type ENUM(\'none\',\'krpano\',\'pannellum\',\'3dvista\') NOT NULL DEFAULT \'none\';';
			pwg_query($query);
			// If plugin have been uninstalled, search JPS and MPO files and mark them 3D
			$query = 'UPDATE ' .IMAGES_TABLE. ' SET is3D=\'true\' WHERE path LIKE \'%.jps\' OR path LIKE \'%.mpo\'';
			pwg_query($query);
		}
		$this->installed = true;
	}
	
	function activate($plugin_version, &$errors=array())
	{
		if (!$this->installed)
		{
			$this->install($plugin_version, $errors);
		}
		copy (PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php', THREED_DIRECTORY.'functions_upload.inc.php.old');
		copy (THREED_DIRECTORY.'functions_upload.inc.php.new', PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php');
	}

	function deactivate()
	{
		copy (THREED_DIRECTORY.'functions_upload.inc.php.old', PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php');
	}

	function uninstall()
	{
		conf_delete_param('threed');
		$query = 'ALTER TABLE ' .IMAGES_TABLE. ' DROP COLUMN is3D, DROP COLUMN pano_type;';
		pwg_query($query);
		$this->deactivate();
	}
}

?>
