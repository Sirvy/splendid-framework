<?php

use Splendid\Db;


/**
 * Base Model
 *
 * @author		Bobby Tran <bobby-tran@email.cz>
 * @copyright	Copyright (c) 2017, Bobby Tran
 */
class Model
{
    /**
     * Database
     * @var stdClass
     */
    protected $db;


    /**
     * Constructor
     * Database connection
     * @param Db $db
     * @internal param $db
     */
    public function __construct(Db $db = null)
    {
        if (isset($db)) {
            $this->db = $db;
            if (!$this->db->connected()) {
                require_once(DB_CONFIG);
                $this->db->connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
            }
        }
    }

}