<?php

/**
 * Splendid Framework config
 *
 * @author      Bobby Tran <bobby-tran@email.cz>
 * @copyright   Copyright (c) 2017, Bobby Tran
 */

/* Database configuration file path */
define('DB_CONFIG', dirname(__DIR__) . '/config/db_config.php');

/* Allow application to use text from language content */
define('USE_LANG', true);

/* Path to directory of language packs */
define('LANG_DIR', '../library/language');

/* Default language pack */
define('DEFAULT_LANG', 'en');

/* Set true if HTTPS */
define('USING_HTTPS', false);

/* Set application to development mode */
define('DEV_MODE', true); // ! Set FALSE in production !
