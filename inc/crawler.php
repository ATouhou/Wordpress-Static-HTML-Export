<?php
require_once('functions.php');

class Crawler 
{
	static private $use_curl = false;

	static private $urls_to_download = array();
	static private $urls_downloaded = array();

	static private $replace_dirs = [
		'/wp-content/themes' => NEW_THEME_FOLDER_NAME,
		'/wp-includes' => NEW_INCLUDE_FOLDER_NAME,
		'/wp-content' => NEW_CONTENT_FOLDER_NAME,
	];

	public static function exportWordpressAsHTML()
	{
		self::removeOutputFiles();
		self::copyCurrentTheme();

		self::exportWordpress();

		self::removeAllPHPFilesOfDir(OUTPUT_DIR);

		tp_log('Wordpress expored finished');
	}

	public static function exportWordpress()
	{
		if(!function_exists('file_get_contents') || !ini_get('allow_url_fopen') )
		{
			self::$use_curl = true;
			//TODO: curl support  
			exit('allow_url_fopen not allowed or file_get_contents doesnt exist');
		}

		// homepage export
		self::enqueueUrl(OLD_SITE_URL);	

		$pages = get_pages();
		foreach($pages as $page)
		{
			self::enqueueUrl(get_permalink($page->ID));
		}

		$posts = get_posts();
		foreach($posts as $post)
		{
			self::enqueueUrl(get_permalink($post->ID));
		}

		for($r=1; $r <= CRAWL_ROUNDS; $r++)
		{
			self::exportPages();
		}
	}

	public static function exportPages()
	{
		$urls = self::$urls_to_download;
		foreach($urls as $url)
		{
			if( self::urlDownloaded($url) )
			{
				// url already crawled
				self::markUrlAsCrawled($url);
				continue;
			}
			echo '.';
			$html = apply_filters('tp_site_html', self::getContentOfUrl($url));
			self::enqueueUrls(self::getAllInternalUrls($html));
			self::replaceUrls($html);
			$static_dir = self::getStaticDirOfUrl($url);
			// dir exists?
			if( !file_exists(OUTPUT_DIR.$static_dir) )
			{
				mkdir(OUTPUT_DIR.$static_dir, 0777, true);
			}
			$static_file = self::getStaticFileOfUrl($url);
			// file exists?
			if( !file_exists(OUTPUT_DIR.$static_dir.$static_file) )
			{
				file_put_contents(OUTPUT_DIR.$static_dir.$static_file, $html);
			}

			self::markUrlAsCrawled($url);
		}
	}

	public static function enqueueUrl($url)
	{
		if( !preg_match('/\.php$/i', $url) 
			&& !in_array($url, self::$urls_downloaded) )
		{
			self::$urls_to_download[] = $url;
		}
	}

	public static function enqueueUrls($urls)
	{
		if( is_array($urls) )
		{
			foreach($urls as $url)
			{
				self::enqueueUrl($url);
			}
		}
	}

	public static function markUrlAsCrawled($url)
	{
		$index = array_search($url,self::$urls_to_download);
		if($index !== false){
		    unset(self::$urls_to_download[$index]);
		}

		if( !in_array($url, self::$urls_downloaded) )
		{
			self::$urls_downloaded[] = $url;
		}
	}

	public static function urlDownloaded($url)
	{
		return in_array($url, self::$urls_downloaded);
	}

	public static function getAllInternalUrls(&$html)
	{
		preg_match_all(
			'/((?:'.preg_quote(WP_SITE_URL, '/').'|'.preg_quote(OLD_SITE_URL, '/').')[^\"|^\'|\)|^<|^>|^#]+)/i', 
			$html, 
			$matches
		);

		if( isset($matches[1]) )
		{
			return array_unique($matches[1]);
		}

		return array(); // nothing found
	}

	public static function replaceUrls(&$html)
	{
		$urls = array(
			WP_SITE_URL,
			OLD_SITE_URL,
			str_replace('/', '\/', WP_SITE_URL),
			str_replace('/', '\/', OLD_SITE_URL)
		);

		$html = str_replace($urls, NEW_SITE_URL, $html);
	}

	public static function getContentOfUrl($url)
	{
		if( self::$use_curl )
		{
			//TODO: Curl support
		}
		else
		{
			return file_get_contents($url);
		}
	}

	public static function copyCurrentTheme()
	{
		/*
		//TODO: Rename dir feature
		recurse_copy(get_template_directory(), NEW_THEME_DIR);
		tp_log('Current theme copyed');

		self::removeAllPHPFilesOfDir(NEW_THEME_DIR);
		tp_log('All .php in '.NEW_THEME_DIR.' deleted');*/

		$theme_dir = self::getStaticDirOfUrl(get_template_directory_uri());
		mkdir(OUTPUT_DIR.$theme_dir, 0777, true);
		recurse_copy(get_template_directory(), OUTPUT_DIR.$theme_dir);
		self::removeAllPHPFilesOfDir(OUTPUT_DIR.$theme_dir);
	}

	public static function removeOutputFiles()
	{
		$files = glob(OUTPUT_DIR.'*');
		
		foreach($files as $file)
		{
			if( is_dir($file) )
			{
				deleteNonEmptyDir($file);
			}
			else
			{
				unlink($file);
			}
		}

		tp_log('Output deleted');
	}

	public static function removeAllPHPFilesOfDir($dir)
	{
		$php_files = rglob('*.php', null, $dir);
		
		foreach($php_files as $php_file)
		{
			unlink($php_file);
		}
	}

	public static function getStaticDirOfUrl($url)
	{
		$url = str_replace([WP_SITE_URL, OLD_SITE_URL], '', $url);
		$dir = parse_url($url, PHP_URL_PATH);

		if( $dir == '/' || $dir == '' ){ return ''; }

		// replace dirs
		/*foreach(self::$replace_dirs as $replace_dir=>$new_name)
		{
			echo $replace_dir, ' - ', $new_name, PHP_EOL;
			if( $replace_dir[0] != '/')
			{ 
				$replace_dir = '/'.$replace_dir; 
			}

			if( strpos($dir, $replace_dir) == 0 )
			{
				// replace dir 
				$dir = preg_replace('/^'.preg_quote($replace_dir, '/').'/i', $new_name, $dir);
				break;
			}
		}*/

		// e.g. /category/something/ or /feed
		if( mb_substr($dir, -1, 1) == '/'
			|| !preg_match('/\/?.*\..*$/i', $dir))
		{	
			$dir = trim($dir, '/');
			return $dir.'/';
		}

		// without file
		preg_match('/(.*)\//i', $dir, $match);
		if( isset($match[1]) )
		{
			$dir = trim($match[1], '/');
			return $match[1].'/';
		}

		return '';
	}

	public static function getStaticFileOfUrl($url)
	{
		$path = parse_url($url, PHP_URL_PATH);
		$query = parse_url($url, PHP_URL_QUERY);

		preg_match('/\/([^\/]*\.[^\/]*)$/i', $path, $match);

		if( isset($match[1]) )
		{
			return $match[1];
		}

		return 'index.html';
	}



	/** custom FILTER **/
	public static function applyAllFilters()
	{
		add_filter('tp_site_html', ['Crawler', 'filter_remove_wordpress_header_links']);
		add_filter('tp_site_html', ['Crawler', 'filter_change_generator']);
	}
	
	// Replace all urls to files of the theme
	/*public static function filter_replace_theme_urls($html)
	{   //TODO: Das gleiche jeweils auch einmal "escaped" durchfuehren, wegen json-objects
		// replace theme urls
		$html = str_replace(get_template_directory_uri(), NEW_SITE_URL.'/'.NEW_THEME_FOLDER_NAME, $html);
		// replace wp-include urls
		$html = str_replace(includes_url(), NEW_SITE_URL.'/'.NEW_INCLUDE_FOLDER_NAME.'/', $html);
		// replace wp-content urls
		return str_replace(content_url(), NEW_SITE_URL.'/'.NEW_CONTENT_FOLDER_NAME, $html);
	}*/

	// remove pingback, EditURI and co.
	public static function filter_remove_wordpress_header_links($html)
	{
		$pattern = array(
			'/<link[^>]*rel=[\'|\"]pingback[\'|\"][^>]*>/i',
			'/<link[^>]*rel=[\'|\"]EditURI[\'|\"][^>]*>/i',
			'/<link[^>]*rel=[\'|\"]pingback[\'|\"][^>]*>/i',
			'/<link[^>]*rel=[\'|\"]shortlink[\'|\"][^>]*>/i'
		);
		return preg_replace($pattern, '', $html);
	}

	// change generator 
	public static function filter_change_generator($html)
	{
		return preg_replace('/<meta[^>]*name="generator"[^>]*>/i', '<meta name="generator" content="'.NEW_GENERATOR.'">', $html);
	}

	// replace alle other urls
	/*public static function filter_replace_all_other_urls($html)
	{
		// normal urls
		$html = str_replace(OLD_SITE_URL, NEW_SITE_URL, $html);

		// escaped urls like urls in json-code 
		$old_site_escaped = str_replace('/', '\/', OLD_SITE_URL);
		$new_site_escaped = str_replace('/', '\/', NEW_SITE_URL);
		$html = str_replace($old_site_escaped, $new_site_escaped, $html);

		return $html;
	}

	// download all used files
	public static function filter_download_all_used_files($html)
	{
		preg_match_all('/'.preg_quote(OLD_SITE_URL, '/').'[^\"|^\'|\)|^<|^>|^#]+/i', $html, $treffer);

		$treffer = array_unique($treffer[0]);

		foreach($treffer as $url)
		{
			$output_dir = Crawler::getStaticDirOfUrl($url);
			tp_log('URL '.$url.' has outputdir '.$output_dir);
			$file = str_replace([OLD_SITE_URL, $output_dir], '', $url);

			if( $file == '' || $file == '/' )
			{
				$file = 'index.html';
			}

			// create dir if not exist
			if( !file_exists(OUTPUT_DIR.$output_dir) )
			{
				tp_log('create output dir '.OUTPUT_DIR.$output_dir.' of URL '.$url);
				mkdir(OUTPUT_DIR.$output_dir, 0777, true);
			}

			// create file
			if( !file_exists(OUTPUT_DIR.$output_dir.$file) )
			{
				tp_log('Lade URL '.$url.' nach '.OUTPUT_DIR.$output_dir.$file);
				file_put_contents(OUTPUT_DIR.$output_dir.$file, '');
				$content = apply_filters('tp_site_html_without_download', file_get_contents($url));
				file_put_contents(OUTPUT_DIR.$output_dir.$file, $content);
			}
		}

		return $html;
	}*/
}