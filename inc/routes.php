<?php

class Routes 
{

	public static function AdminMainScreen()
	{
		Crawler::exportWordpressAsHTML();
	}

	public static function AdminDeleteOutput()
	{
		Crawler::removeOutputFiles();
	}



}
