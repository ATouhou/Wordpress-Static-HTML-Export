<?php

class Routes 
{

	public static function AdminMainScreen()
	{
		echo '<pre>';
		tp_log('Copy Site', true);
		/*tp_log(Crawler::getStaticDirOfUrl('/feed'), true);
		tp_log(Crawler::getStaticDirOfUrl('/2015/05/test-article/'), true);
		tp_log(Crawler::getStaticDirOfUrl('/2015/05/test-article'), true);
		tp_log(Crawler::getStaticDirOfUrl('/js/jquery.js?sadsad=asdasd'), true);*/
		Crawler::exportWordpressAsHTML();
	}

	public static function AdminDeleteOutput()
	{
		Crawler::removeOutputFiles();
	}

	public static function ShowTests()
	{
		echo '<pre>';

		$links = array();

		$pages = get_pages();
		foreach($pages as $page)
		{
			$links[] = get_permalink($page->ID);
		}

		$posts = get_posts();
		foreach($posts as $post)
		{
			$links[] = get_permalink($post->ID);
		}

		$links = array_unique($links);

		foreach($links as $link)
		{
			echo 'Link '.$link.' Ordner '.Crawler::getStaticDirOfUrl($link).PHP_EOL;
		}

	}


}
