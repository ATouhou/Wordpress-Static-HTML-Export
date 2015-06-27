<?php

function recurse_copy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
} 


function rglob($pattern='*', $flags = 0, $path='')
{
    $paths=glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
    $files=glob($path.$pattern, $flags);
    foreach ($paths as $path) { $files=array_merge($files,rglob($pattern, $flags, $path)); }
    return $files;
}

function deleteNonEmptyDir($dir) 
{
   if (is_dir($dir)) 
   {
        $objects = scandir($dir);

        foreach ($objects as $object) 
        {
            if ($object != "." && $object != "..") 
            {
                if (filetype($dir . "/" . $object) == "dir")
                {
                    deleteNonEmptyDir($dir . "/" . $object); 
                }
                else
                {
                    unlink($dir . "/" . $object);
                }
            }
        }

        reset($objects);
        rmdir($dir);
    }
}

function tp_log($msg, $output = false)
{	
    if( TP_DEBUG !== true ){ return; }
	file_put_contents(PLUGIN_DIR.'log.txt', $msg.PHP_EOL, FILE_APPEND);
	
	if( $output )
	{
		echo $msg.PHP_EOL;
	}
}
