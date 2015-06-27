<?php
include('crawler.php');
include('routes.php');

//TODO: Über das Admin-Panel einstellbar machen
define('WP_SITE_URL', get_bloginfo('wpurl'));
define('OLD_SITE_URL', get_bloginfo('url'));
define('NEW_SITE_URL', 'http://testsite.local');
define('NEW_GENERATOR', 'Wordpress Static HTML Export');
define('OUTPUT_DIR', '/opt/www/testsite/');

/*
//TODO: Replace Directories Feature 
define('NEW_THEME_FOLDER_NAME', 'theme');
define('NEW_THEME_DIR', OUTPUT_DIR.NEW_THEME_FOLDER_NAME.'/');
define('NEW_CONTENT_FOLDER_NAME', 'content');
define('NEW_INCLUDE_FOLDER_NAME', 'includes');*/

define('CRAWL_ROUNDS', 3);

Crawler::applyAllFilters();