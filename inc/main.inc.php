<?php
include('crawler.php');
include('routes.php');

//TODO: Über das Admin-Panel einstellbar machen
define('WP_SITE_URL', get_bloginfo('wpurl'));
//define('WP_SITE_DIR', parse_url(WP_SITE_URL, PHP_URL_PATH));
define('OLD_SITE_URL', get_bloginfo('url'));
define('NEW_SITE_URL', 'http://testsite.local');
//define('NEW_SITE_URL', 'http://tim-vaio/testoutput');
define('NEW_GENERATOR', 'Static HTML Export');
define('OUTPUT_DIR', '/opt/www/testsite/');
//define('OUTPUT_DIR', '/opt/www/testoutput/');
define('NEW_THEME_FOLDER_NAME', 'theme');
define('NEW_THEME_DIR', OUTPUT_DIR.NEW_THEME_FOLDER_NAME.'/');
define('NEW_CONTENT_FOLDER_NAME', 'content');
define('NEW_INCLUDE_FOLDER_NAME', 'includes');

define('CRAWL_ROUNDS', 2);

Crawler::applyAllFilters();