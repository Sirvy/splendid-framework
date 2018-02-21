<?php

namespace Splendid;

use PDO;
use Exception;

/**
 * Database framework
 *
 * @author      Bobby Tran <bobby-tran@email.cz>
 * @copyright   Copyright (c) 2017, Bobby Tran
 */
class Db
{
    /**
     * @var PDO
     */
    private $connection;

    /**
     * @var array
     */
    private $settings = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_EMULATE_PREPARES => false
    );


    /**
     * Creates DB connection or dies when failed
     *
     * @param string
     * @param string
     * @param string
     * @param string
     */
    public function connect($host, $user, $passwd, $database) {
        if (!isset($this->connection)) {
            try {
                $this->connection = @new PDO(
                    "mysql:host=$host;dbname=$database",
                    $user,
                    $passwd,
                    $this->settings
                );
            } catch (Exception $e) {
                die("Database connection error.");
            }
        }
    }


    public function getConnection()
    {
        return $this->connection;
    }


    /**
     * Checks if app is connected to db
     *
     * @return bool
     */
    public function connected() {
        return isset($this->connection);
    }


    /**
     * Gets last inserted ID
     *
     * @return int
     */
    public function getLastInsertId()
    {
        return $this->connection->lastInsertId();
    }


    /**
     * Executes SQL query directly
     *
     * @param $query
     * @return mixed
     */
    public function dbexec($query)
    {
        if (DEV_MODE) Debugger::$sqlQueries[] = $query;
        return $this->connection->exec($query);
    }


    /**
     * Inserts or updates row in Db
     *
     * @param string
     * @param array
     * @return int
     */
    public function exec($query, array $args = array())
    {
        $sth = $this->connection->prepare($query);
        $i = 0;
        foreach($args as $arg) {
            $i++;
            $sth->bindParam($i, $arg);
        }
        $sth->execute($args);
        if (DEV_MODE) Debugger::$sqlQueries[] = $sth->queryString;
        return $sth->rowCount();
    }


    /**
     * Gets row from Db
     *
     * @param string
     * @param array $args
     * @param bool $fetchArray
     * @internal param $array
     * @return object
     */
    public function get($query, array $args = array(), $fetchArray = false)
    {
        $sth = $this->connection->prepare($query);
        $sth->execute($args);
        if (DEV_MODE) Debugger::$sqlQueries[] = $sth->queryString;
        return $sth->fetch($fetchArray ? PDO::FETCH_BOTH : PDO::FETCH_OBJ);
    }


    /**
     * Gets rows from Db
     *
     * @param string
     * @param array $args
     * @param bool $fetchArray
     * @internal param $array
     * @return array
     */
    public function getAll($query, array $args = array(), $fetchArray = false)
    {
        $sth = $this->connection->prepare($query);
        $sth->execute($args);
        if (DEV_MODE) Debugger::$sqlQueries[] = $sth->queryString;
        return $sth->fetchAll($fetchArray ? PDO::FETCH_BOTH : PDO::FETCH_OBJ);
    }


    /**
     * Selects rows
     *
     * @param $table
     * @param $columns
     * @param null $conditions
     * @param array $args
     * @param bool $fetchArray
     * @return array
     */
    public function select($table, $columns, $conditions = null, array $args = array(), $fetchArray = false)
    {
        if (empty($columns)) {
            $columns = '*';
        }
        $query = "SELECT " . $columns . " FROM `" . $table . "`";
        if (!empty($conditions)) {
            $query .= "WHERE " . $conditions;
        }
        return $this->getAll($query, $args, $fetchArray);
    }


    /**
     * Selects one row
     *
     * @param $table
     * @param $columns
     * @param null $conditions
     * @param array $args
     * @param bool $fetchArray
     * @return object
     */
    public function selectOne($table, $columns, $conditions = null, array $args = array(), $fetchArray = false)
    {
        if (empty($columns)) {
            $columns = '*';
        }
        $query = "SELECT " . $columns . " FROM `" . $table . "`";
        if (!empty($conditions)) {
            $query .= "WHERE " . $conditions;
        }
        return $this->get($query, $args, $fetchArray);
    }


    /**
     * Updates rows
     *
     * @param $table
     * @param array $data
     * @param null $conditions
     * @param array $args
     * @return int
     */
    public function update($table, array $data, $conditions = null, array $args = array())
    {
        $query = "UPDATE `" . $table . "` SET ";
        $set = [];
        foreach($data as $col => $val) {
            $set[] = "`" . $col . "` = :" . $col;
            $args[':' . $col] = $val;
        }
        $query .= implode(', ', $set);
        if (!empty($conditions)) {
            $query .= " WHERE " . $conditions;
        }
        return $this->exec($query, $args);
    }


    /**
     * Inserts rows
     *
     * @param $table
     * @param array $data
     * @param bool $lastId
     * @return int
     */
    public function insert($table, array $data, $lastId = false)
    {
        $columns = [];
        $values = [];
        $rv = [];
        foreach ($data as $column => $value) {
            $columns[] = '`' . $column . '`';
            $values[] = ':' . $column;
            $rv[':' . $column] = $value;
        }
        $c = implode(', ', $columns);
        $v = implode(', ', $values);

        $sth = $this->connection->prepare("INSERT INTO `" . $table . "`(" . $c . ") VALUES(" . $v . ")");
        $sth->execute($rv);
        if (DEV_MODE) Debugger::$sqlQueries[] = $sth->queryString;
        return ($lastId ? $this->getLastInsertId() : $sth->rowCount());
    }


    /**
     * Inserts n-rows into Db
     *
     * @param string
     * @param array
     * @return int
     */
    public function multiInsert($table, array $rows)
    {
        $columns = [];
        $rv = [];
        $v = [];

        foreach($rows[0] as $column => $value) {
            $columns[] = '`' . $column . '`';
        }
        $c = implode(', ', $columns);

        foreach($rows as $row) {
            $values = [];
            foreach ($row as $column => $value) {
                $values[] = '?';
                $rv[] = $value;
            }
            $v[] = '(' . implode(', ', $values) . ')';
        }
        $fv = implode(', ', $v);

        $sth = $this->connection->prepare("INSERT INTO `" . $table . "`(" . $c . ") VALUES " . $fv);
        $sth->execute($rv);

        if (DEV_MODE) Debugger::$sqlQueries[] = $sth->queryString;
        return $sth->rowCount();
    }


    /**
     * Begin PDO transaction
     *
     * @return mixed
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }


    /**
     * Commit PDO transaction
     *
     * @return mixed
     */
    public function commitTransaction()
    {
        return $this->connection->commit();
    }


    /**
     * Rollback PDO transaction
     *
     * @return mixed
     */
    public function rollBackTransaction()
    {
        return $this->connection->rollBack();
    }

}