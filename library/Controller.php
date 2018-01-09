<?php

/**
 * Base Controller
 *
 * @author      Bobby Tran <bobby-tran@email.cz>
 * @copyright   Copyright (c) 2017, Bobby Tran
 */
abstract class Controller
{
    /**
     * File
     * @var string
     */
    protected $view;

    /**
     * Variables to extract in $view
     * @var array
     */
    protected $data = array();

    /**
     * Meta for seo
     * @var array
     */
    protected $metaHeader = array();

    /**
     * Link additional scripts
     * @var array
     */
    protected $addScripts = array();

    /**
     * Link additional styles
     * @var array
     */
    protected $addStyles = array();

    /**
     * Default language
     * @var string
     */
    public $lang = DEFAULT_LANG;

    /**
     * Services
     * @var stdClass
     */
    protected $service;

    /**
     * Database service
     * @var stdClass
     */
    protected $db;

    /**
     * Current user
     * @var stdClass
     */
    protected $user;

    /**
     * Params in URL
     * @var array
     */
    public $params = array();

    /**
     * Layout view
     * @var string
     */
    protected $layout = 'layout';

    /**
     * View blocks
     * @var array
     */
    protected $block = array();

    /**
     * Opens a secure session
     */
    protected function secureSession()
    {
        ini_set("session.use_trans_sid", false);
        ini_set("session.cookie_httponly", true);
        ini_set("session.use_strict_mode", true);
        $sessionName = 'splendidSessionName';
        $secure = USING_HTTPS;
        if (ini_set('session.use_only_cookies', 1) === false) {
            die('Error: Could not initiate a safe session.');
        }
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params($cookieParams['lifetime'], $cookieParams['path'], $cookieParams['domain'], $secure, true);
        session_name($sessionName);
        session_start();
        session_regenerate_id();
        $this->generateCSRFToken();
    }

    /**
     * Escape output
     * @param $x array|string
     * @return array|string
     */
    protected function secureXSS($x = null)
    {
        if (!isset($x)) {
            return null;
        }
        else if (is_string($x)) {
            return htmlspecialchars($x, ENT_QUOTES);
        }
        else if (is_array($x))
        {
            foreach($x as $k => $v) {
                $x[$k] = $this->secureXSS($v);
            }
            return $x;
        }
        else {
            return $x;
        }
    }

    /**
     * Brute force protection
     */
    protected function secureDos()
    {
        $banSeconds     = 60;   // Ban user for how many seconds
        $accessCount    = 10;  // After how many accesses
        $limitTime      = 150;   // Interval between each access

        if (isset($_SESSION['ban_for_dos']) && microtime(true) - $_SESSION['ban_for_dos'] < $banSeconds) {
            header("HTTP/1.1 429 Too Many Requests");
            exit();
        }

        if (!isset($_SESSION['bf_key']))
            $_SESSION['bf_key'] = microtime(true)*1000;
        if (!isset($_SESSION['bf_count']))
            $_SESSION['bf_count'] = 0;

        if (microtime(true)*1000 - $_SESSION['bf_key'] < $limitTime) {
            $_SESSION['bf_count']++;
        } else {
            $_SESSION['bf_count'] = 0;
        }

        if ($_SESSION['bf_count'] > $accessCount) {
            $_SESSION['ban_for_dos'] = microtime(true);
        }

        $_SESSION['bf_key'] = microtime(true)*1000;
    }

    /**
     * Generates CSRF token
     */
    protected function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token']))
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /**
     * Regenerates CSRF token
     */
    protected function regenerateCSRFToken()
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /**
     * Set services, header, additional scripts, styles, view etc.
     */
    protected function beforeProcess() {}

    /**
     * After process
     */
    protected function afterProcess() {}

    /**
     * Redirect
     * @param string
     */
    protected function redirect($url = null)
    {
        if ($url === null) {
            header('Refresh: 0');
        } else {
            header('Location: /' . $url);
        }
        header('Connection: close');
        exit;
    }

    /**
     * Slows down loading time in ms
     * @param $ms
     */
    protected function boostDown($ms) {
        usleep($ms * 1000);
    }

}