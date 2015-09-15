<?php

/**
 * path.php
 * @author Volker "Bill" Schuelbe
 * @copy (c)2014 - All Rights Reserved
 */

/**
 * Standardize any path to the PHP/Unix slash format.
 * @param $path {string} Path to normalize.
 * @param $utf {string} Flag to encode (+) or decode (-) the path to UTF-8.
 * @return {string} Normalized PHP/Unix path.
 */
function unixPath($path, $utf) {
	//
	// TODO:
	// realpath($path);
	//
	// Unescape apostrophies:
	//
	// $path = preg_replace("/\\\'/", "'", $path);
	$path = str_replace("\\'", "'", $path);
    //
	// Replace backslashes with forward slashes:
	//
    $path = str_replace('\\', '/', $path);
	//
	// Eliminate multiple slashes:
	//
    $path = preg_replace('/\/+/', '/', $path);
    //
	$test = '';
	$parts = array();
	$segments = explode('/', $path);
    foreach($segments as $segment) {
        if($segment != '.') {
            $test = array_pop($parts);
            if(is_null($test)) {
                $parts[] = $segment;
			}
            else if($segment == '..') {
                if($test == '..') {
                    $parts[] = $test;
				}
                if($test == '..' || $test == '') {
                    $parts[] = $segment;
				}
            }
            else {
                $parts[] = $test;
                $parts[] = $segment;
            }
        }
    }
    $path = implode('/', $parts);
	//
	// Treatment of chars:
	//
	if($utf == '+') {
		return utf8_encode($path);
	}
	else if($utf == '-') {
		return utf8_decode($path);
	}
	else {
		return $path;
	}
}

/**
 * Standardize any path to the Windows backslash format.
 * @param $path {string} Path to normalize.
 * @param $utf {string} Flag to encode (+) or decode (-) the path to UTF-8.
 * @return {string} Normalized Windows path.
 */
function winPath($path, $utf) {
	$path = unixPath($path, $utf);
	// $path = preg_replace('/\//', '\\', $path);
	$path = str_replace('/', '\\', $path);
	return $path;
}

?>