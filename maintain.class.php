<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class ThreeD_maintain extends PluginMaintain
{
  private $installed = false;
  
  private $default_conf = array(
    'chromeCast' => 0,
    'openGraph'  => 0,
    'video_autoplay' => 0,
    'video_description' => '',
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
