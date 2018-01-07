<?php

mb_internal_encoding("UTF-8");

/**
 * Autoloader
 *
 * @author      Bobby Tran <bobby-tran@email.cz>
 * @copyright   Copyright (c) 2017, Bobby Tran
 */ 

function autoload($class)
{
	$class = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $class);
	$directory = '../app/library/';
    if (preg_match('/\SController$/', $class)) {
    	$directory = '../app/controllers/';
    }
    else if (preg_match('/Model$/', $class)) {
    	$directory = '../app/models/';
    }
    
    if (!file_exists($directory . $class . ".php")) {
		if (DEV_MODE) die("Error in autoload, file {$directory}{$class}.php not found.");
		else header("HTTP/1.1 500 Internal Server Error");
	} else {
    	require($directory . $class . '.php');
    }

    if (DEV_MODE) {
        \Splendid\Debugger::$classes[] = $class;
        \Splendid\Debugger::$files[] = $directory . $class . '.php';
    }
}

spl_autoload_register("autoload");
