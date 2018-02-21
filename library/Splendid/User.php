<?php

namespace Splendid;

/**
 * User Manager
 *
 * @author      Bobby Tran <bobby-tran@email.cz>
 * @copyright   Copyright (c) 2017, Bobby Tran
 */
class User {

    /**
     * Current user ID
     * @var int
     */
    public $id;

    /**
     * Current user username
     * @var string
     */
    public $username;

    /**
     * Current user security token
     * @var string
     */
    private $token;

    /**
     * Optional data
     * @var array
     */
    private $data;

    /**
     * User constructor.
     */
    public function __construct() {
        if (isset($_SESSION['user_id'])) $this->id = $_SESSION['user_id'];
        if (isset($_SESSION['user_name'])) $this->username = $_SESSION['user_name'];
        if (isset($_SESSION['user_token'])) $this->token = $_SESSION['user_token'];
        if (isset($_SESSION['user_data'])) $this->data = $_SESSION['user_data'];
    }

    /**
     * Creates session for logged in user
     * @param $id
     * @param null $username
     */
    public function login($id, $username = null) {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $username;
        $_SESSION['user_token'] = hash('SHA512', $id . $username . $_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * Checks if is current user logged in
     * @return bool
     */
    public function isLoggedIn() {
        if (!$this->id) return false;
        if (hash('SHA512', $this->id . $this->username . $_SERVER['HTTP_USER_AGENT']) !== $_SESSION['user_token'])
            return false;
        return true;
    }

    /**
     * Logs out current user
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) unset($_SESSION['user_id']);
        if (isset($_SESSION['user_name'])) unset($_SESSION['user_name']);
        if (isset($_SESSION['user_token'])) unset($_SESSION['user_token']);
        if (isset($_SESSION['user_data'])) unset($_SESSION['user_data']);
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    /**
     * Sets current user optional data
     * @param $data
     */
    public function setData($data) {
        $_SESSION['user_data'] = $data;
    }

    /**
     * Adds data to current user optional data
     * @param $key
     * @param $value
     */
    public function addData($key, $value) {
        $_SESSION['user_data'][$key] = $value;
    }

    /**
     * Returns current user optional data
     * @param $key
     * @return mixed
     */
    public function getData($key = NULL) {
        return $key === NULL ? $_SESSION['user_data'] : $_SESSION['user_data'][$key];
    }
}