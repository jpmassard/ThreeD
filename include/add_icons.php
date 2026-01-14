<?php
// +-----------------------------------------------------------------------+
// | ThreeD - a 3D photo, video and 360 panorama extension for Piwigo      |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2014-2026 Jean-Paul MASSARD         https://jpmassard.fr |
// +-----------------------------------------------------------------------+

defined('THREED_PATH') or die('Hacking attempt!');

function threed_add_icons ($pictures)
{
  global $template, $conf, $threed_image_exts, $threed_video_exts;

    for ($i=0; $i < count($pictures); $i++) 
    {
        $ext = get_extension($pictures[$i]['path']);
        $is_video = in_array($ext, $threed_video_exts);
        $is_photo = in_array($ext, $threed_image_exts) or in_array($ext, $conf['picture_ext']);
        $is3D = $pictures[$i]['is3D'];

        if($conf['threed']['icon2Dvideo'] and !$is3D and $is_video) $show_icons[$i][] = 'icon_2D';
        if($conf['threed']['icon3Dvideo'] and $is3D and $is_video) $show_icons[$i][] = 'icon_3D';
        if($conf['threed']['icon2Dphoto'] and !$is3D and $is_photo) $show_icons[$i][] = 'icon_2D';
        if($conf['threed']['icon3Dphoto'] and $is3D and $is_photo) $show_icons[$i][] = 'icon_3D';
        if($conf['threed']['icon360Pano'] and $pictures[$i]['pano_type'] != 'none') $show_icons[$i][] = 'icon_360';
    }
    
    switch($conf['threed']['iconposition'])
    {
        case 'topleft': $x = 0; $y = 0; break;
        case 'topright': $x = 100; $y = 0; break;
        case 'center': $x = 50; $y = 50; break;
        case 'bottomleft': $x = 0; $y = 100; break;
        case 'bottomright': $x = 100; $y = 100; break;
        case 'custom': $x = $conf['threed']['iconxpos']; $y = $conf['threed']['iconypos']; break;
    }
    $template->assign(array(
        'icons'=> json_encode($show_icons),
        'path' => THREED_PATH,
        'x' => $x,
        'y' => $y,
        'alpha' => $conf['threed']['iconalpha'],
    ));
    $template->set_filename('threed_add_icons', THREED_PATH . 'template/add_icons.tpl');
    $template->parse('threed_add_icons');
}

?>
