<?php
include('crawler.php');
include('StaticHTMLExportOptions.php');

$options = get_option(StaticHTMLExportOptions::getOptionsName());
define('WP_SITE_URL', get_bloginfo('wpurl'));
define('OLD_SITE_URL', get_bloginfo('url'));
define('NEW_SITE_URL', $options['new_site_url']);
define('OUTPUT_DIR', $options['output_dir']);
define('CRAWL_ROUNDS', (int)$options['crawl_rounds']);
define('NEW_GENERATOR', 'Wordpress Static HTML Export');


//TODO: Replace Directories Feature 
define('NEW_THEME_FOLDER_NAME', 'theme');
define('NEW_CONTENT_FOLDER_NAME', 'content');
define('NEW_INCLUDE_FOLDER_NAME', 'includes');

Crawler::applyAllFilters();