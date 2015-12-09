<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo and video extension for Piwigo                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2015 Jean-Paul MASSARD                              |
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

class ThreeD_maintain extends PluginMaintain
{
  private $installed = false;
  
  private $default_conf = array(
    'chromeCast' => 0,
    'openGraph'  => 0,
    'video_autoplay' => 0,
    'video_autoloop' => 0,
    );
    
  private $table;
  
  function __construct($plugin_id)
  {
    global $prefixeTable;
    
    parent::__construct($plugin_id);
    
    // $this->table = $prefixeTable . 'threed_image_video';
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
    }
    $this->installed = true;
  }

  function activate($plugin_version, &$errors=array())
  {
    if (!$this->installed)
    {
      $this->install($plugin_version, $errors);
    }
  }

  function deactivate()
  {
  }

  function uninstall()
  {
    conf_delete_param('threed');
  }
}
  
?>
