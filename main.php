<?php
/*
Plugin Name: Static HTML Export
Plugin URI: http://workspace.local
Description: Erstellt aus einer Wordpress-Seite eine komplett statische HTML Seite (kein PHP oder MySQL nötig).
Version: 0.1
Author: Tim Pangritz
Author URI: http://tim.pangritz.com
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define('PLUGIN_DIR', __DIR__.'/');
define('PLUGIN_URL', plugins_url('', __FILE__));
define('TP_DEBUG', false);
include(__DIR__.'/inc/main.inc.php');


add_action('plugins_loaded', ['StaticHTMLExportOptions', 'getInstance']);


remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');