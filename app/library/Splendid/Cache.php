<?php

namespace Splendid;


/**
 * Cache manager
 *
 * @author		Bobby Tran <bobby-tran@email.cz>
 * @copyright	Copyright (c) 2017, Bobby Tran
 */
class Cache
{
    /**
     * Path for tmp folder
     * @var string
     */
    private $tmp;

    /**
     * File expiration in seconds
     * @var int
     */
    private $exp;

    /**
     * Cache constructor.
     * @param $tmp
     * @param int $exp
     * @throws \Exception
     */
    public function  __construct($tmp, $exp = 10)
    {
        $this->tmp = $tmp;
        $this->exp = $exp;

        if (!file_exists($this->tmp)) {
            throw new \Exception("Directory " . $this->tmp . " was not found.");
        }
    }

    /**
     * Checks if cached file exists (within expiration)
     * @param $key
     * @return bool
     */
    public function exist($key)
    {
        return ((file_exists($this->tmp . '/' . $key)) && ((time() - filemtime($this->tmp . '/' . $key)) < $this->exp));
    }

    /**
     * Loads cached file if exists
     * @param $key
     * @return bool
     */
    public function load($key)
    {
        if ($this->exist($key)) {
            echo file_get_contents($this->tmp . '/' . $key);
            return true;
        } else {
            ob_start();
            return false;
        }
    }

    /**
     * Saves cache file
     * @param $key
     */
    public function save($key)
    {
        if (!$this->exist($key)) {
            $content = ob_get_contents();
            file_put_contents($this->tmp . '/' . $key, $content);
        }
    }

}