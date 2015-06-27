<?php

class StaticHTMLExportOptions
{
	private $showOptionsScreen = null;
	private $options_name = 'static_html_export_options';
	private $options_default = array();

	static private $instance = null;
	static public $version = '0.1';

	public static function getInstance()
	{
		if( self::$instance == null )
		{
			self::$instance = new StaticHTMLExportOptions();
		}
		
		return self::$instance;
	}

	private function __construct()
	{
		$this->options_default = array(
			'new_site_url' => get_bloginfo('url'),
			'output_dir' => PLUGIN_DIR.'output/',
			'crawl_rounds' => 3
		);

		add_action('admin_menu', array($this, 'addAdminMenu'));
	}


	static public function getOptionsName()
	{
		$obj = StaticHTMLExportOptions::getInstance();
		return $obj->options_name;
	}


	public function addAdminMenu()
	{
		$this->showOptionsScreen = add_menu_page(
			'Static HTML Export', 
			'Static HTML Export', 
			'manage_options', 
			PLUGIN_DIR.'main.php', 
			array($this, 'showOptions')
		);
	}


	public function showOptions()
	{
		if( get_option($this->options_name) === false )
		{
			// set default options
			update_option($this->options_name, $this->options_default);
		}

		if ( !empty($_POST) && !wp_verify_nonce('_wpnonce') )
		{
			if( isset($_POST['submit']) )
			{
				$options = array(
					'new_site_url' => $_POST['new_site_url'],
					'output_dir' => $_POST['output_dir'],
					'crawl_rounds' => $_POST['crawl_rounds']
				);

				update_option($this->options_name, $options);
			}
			elseif( isset($_POST['run']) )
			{
				Crawler::runWordpressExport();
				$run_status = 'done'; //TODO: quick and dirty... :D
			}
		}


		$options = get_option($this->options_name);
		include(PLUGIN_DIR.'views/settings.php');
	}
}