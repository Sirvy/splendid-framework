<?php

/**
 * Autoloader
 *
 * @author      Bobby Tran <bobby-tran@email.cz>
 * @copyright   Copyright (c) 2017, Bobby Tran
 */

mb_internal_encoding("UTF-8");

function autoload($class)
{
    $class = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $class);
    $directory = dirname(__DIR__) . '/app/library/';
    if (preg_match('/\SController$/', $class)) {
        $directory = dirname(__DIR__) . '/' . basename(__DIR__) . '/app/controllers/';
    }
    else if (preg_match('/Model$/', $class)) {
        $directory = dirname(__DIR__) . '/' . basename(__DIR__) . '/app/models/';
    }

    if (!file_exists($directory . $class . ".php")) {
        if (DEV_MODE) die("Error in Admin autoload, file {$directory}{$class}.php not found.");
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
