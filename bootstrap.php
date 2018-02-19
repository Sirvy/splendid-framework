<?php
/**
 * Bootstrap
 *
 * @author      Bobby Tran <bobby-tran@email.cz>
 * @copyright   Copyright (c) 2017, Bobby Tran
 */

header('X-Frame-Options: DENY');

$params = [];
$isModule = false;

/*
 * Load configuration files
 */
require_once('config/config.php');
if (file_exists(DB_CONFIG)) {
    require_once(DB_CONFIG);
} else {
    if (DEV_MODE) die("Database configuration file not found.");
    header("HTTP/1.1 500 Internal Server Error");
    exit();
}


/*
 * Check load time in develop mode
 * Print all errors in develop mode
 */
if (DEV_MODE) {
    $start_time = microtime(true);
    ini_set('display_errors', '1');
}
error_reporting((DEV_MODE) ? E_ALL : 0);

/*
 * Parse URL
 */
if (isset($_GET['page']) and !empty($_GET['page']))
{
    $getPage = htmlspecialchars($_GET['page']);
    $params = explode('/', $getPage);
}


/*
 * Loader
 */
if (isset($params[0]) and !empty($params[0]) and file_exists( '../' . $params[0] . 'Module/')) {
    $isModule = true;
    require_once( '../' . $params[0] . 'Module/loader.php' );
} else {
    require_once('loader.php');
}


/*
 * Start application
 */

$app = new Application;
if ($isModule) {
    if (isset($params[1]) and !empty($params[1])) {
        $app->setController($params[1]);
    }
}
else {
    if (isset($params[0]) and !empty($params[0])) {
        $app->setController($params[0]);
    }
}
$app->params = $params;
$app->process();
$app->render();


if (DEV_MODE) {
    $sqls = \Splendid\Debugger::$sqlQueries;
    $classes = \Splendid\Debugger::$classes;
    $files = \Splendid\Debugger::$files;
    echo "
	<div style='position: fixed; bottom: 0; right: 0; padding: 5px; background: #f0f0f0; border: 1px solid #e0e0e0; font-family: arial; font-size: 13px; box-shadow: 0px 0px 8px #a0a0a0;'>
		<b>DEV</b> mode is <b>ON</b><br>
		<span title='" . implode('&#013;&#013;', $sqls) . "'>Executed " . count($sqls) . " queries.</span><br>
		<span title='" . implode('&#013;&#013;', $classes) . "'>Loaded " . count($classes) . " classes.</span><br>
		<span title='" . implode('&#013;&#013;', $files) . "'>Loaded " . count($files) . " files.</span><br>
		Loadtime: " . round(microtime(true) - $start_time, 5) * 1000 . " ms<br>
	</div>";
}
