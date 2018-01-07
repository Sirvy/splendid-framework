<?php

namespace Splendid;

/**
 * Service Manager
 *
 * @author		Bobby Tran <bobby-tran@email.cz>
 * @copyright	Copyright (c) 2017, Bobby Tran
 */
class Service {

    /**
     * Array of services
     * @var array
     */
    private $objects = [];

    /**
     * Stores new service
     * @param $key
     * @param $object
     */
    public function store($key, $object) {
        $this->objects[$key] = $object;
    }

    /**
     * Returns service
     * @param $key
     * @return mixed
     */
    public function get($key) {
        return $this->objects[$key];
    }

    /**
     * Checks if service exists
     * @param $key
     * @return bool
     */
    public function exist($key) {
        return isset($this->objects[$key]);
    }

}