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
include(__DIR__.'/inc/main.inc.php');
define('PLUGIN_DIR', __DIR__.'/');

add_action('admin_menu', 'addStaticHTMLExportAdminMenu');

function addStaticHTMLExportAdminMenu()
{
	add_menu_page('Static HTML Export', 'Static HTML Export', 'manage_options', PLUGIN_DIR.'main.php', array('Routes', 'AdminMainScreen'));	
	add_submenu_page(PLUGIN_DIR.'main.php', 'Delete Output', 'Delete Output', 'manage_options', PLUGIN_DIR.'main.php?action=deleteOutput', ['Routes', 'AdminDeleteOutput']);
	add_submenu_page(PLUGIN_DIR.'main.php', 'Tests', 'Tests', 'manage_options', PLUGIN_DIR.'main.php?action=tests', ['Routes', 'ShowTests']);
}


//add_action('after_switch_theme', ['Crawler', 'exportWordpressAsHTML']);

remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');