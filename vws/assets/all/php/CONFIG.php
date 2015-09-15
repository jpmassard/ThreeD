<?php

/**
 * CONFIG.php
 * @author Volker "Bill" Schuelbe
 * @copy (c)2014 - All Rights Reserved
 */

/**
 * VIDEO_DIR
 * Central location for all videos on this server.
 */
define('VIDEO_DIR', '/video/');

/**
 * VIDEO_LOADER
 * Path to the VideoLoader.php video streaming script. If the path is set to
 * NULL, the server will be relied on to serve the requested byte ranges.
 */
define('VIDEO_LOADER', 'plugins/ThreeD/vws/assets/loader/VideoLoader/VideoLoader.php');
// define('VIDEO_LOADER', NULL);
 
?>