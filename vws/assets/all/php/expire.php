<?php 

/**
 * expire.php
 * @author Volker "Bill" Schuelbe
 * @copy (c)2014 - All Rights Reserved
 */

/**
 * Kill the script with an error message on a nice, bright error page.
 * @param $msg {string} The error message to display.
 */
function expire($msg) {
header('Content-Type: text/html;charset=utf-8');
$out = <<< EndOf_expire
<html lang="en">
    <head>
        <title>PHP Script Error</title>
		<meta charset="utf-8" />
		<meta name = "viewport" 
			content = "width=device-width, initial-scale=1.0" />
		<!-- <meta name="viewport" content="width=device-width, 
			initial-scale=1, maximum-scale=1, user-scalable=no" /> -->
		<link rel = "shortcut icon" 
			type = "image/x-icon" title = "proggies2go&trade;" 
			href="/inc/vws/assets/all/image/shortcut.ico" />
		<link rel = "apple-touch-icon" 
			type = "image/x-icon" title = "proggies2go&trade;" 
			href="/inc/vws/assets/all/image/shortcut.ico" />
        <style type="text/css">
            body {
                margin: 25px;
                padding: 25px;
                border: 1px solid black;
                background-color: #FFCC00;
                font-family: sans-serif;
                font-size: 12pt;
                color: black;
            }
            h3 {
                font-size: 14pt;
                font-weight: bold;
                font-style: italic;
            }
            p {
                font-weight: bold;
            }
            hr {
                color: black;
                margin: 35px 0px 35px 0px;
            }
            .cr {
                font-size: 10pt;
                font-weight: normal;
            }
        </style>
    </head>
    <body>
        <h3>PHP Script Error:</h3>
        <p>$msg</p>
        <hr size="1">
        <p class="cr">
            &copy;2014 Volker &ldquo; Bill &rdquo; Schuelbe 
			&ndash; All Rights Reserved
        </p>
    </body>
</html>
EndOf_expire;
die($out);
}

/**
 * Return the value of a query variable, or kill the script.
 * @param {string} Name of the mandatory query variable.
 * @return {string} The value of the request variable.
 */
function requireQueryVar($varName) {
	$var = $_REQUEST[$varName];
	if(!isset($var)) {
		expire('The "' . $varName . '" query variable is required.');
	}
	return $var;
}

?>