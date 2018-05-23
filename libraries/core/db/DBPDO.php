<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\db;

defined('_EXEC') or die();

use PDO;
use moam\core\AppException;
use PDOException;

class DBPDO
{

    public $pdo;

    private $error;

    private $database_name;

    private $database_user;

    private $database_pass;

    private $database_host;

    function __construct($database_name, $database_host, $database_user, $database_pass)
    {
        $this->database_name = $database_name;
        $this->database_host = $database_host;
        $this->database_user = $database_user;
        $this->database_pass = $database_pass;

        $this->connect();
    }

    public function getErrorMessage($data_db = null)
    {
        $result = "";

        if (! is_null($data_db)) {
            $db_result_error = $data_db->errorInfo();

            if ($db_result_error[2] != "") {
                $result = $db_result_error[2];
            }
        }

        return $result;
    }

    function lastInsertId()
    {

        // Gets this table's last sequence value
        $query = "SELECT LAST_INSERT_ID();";

        try {

            // open transaction
            // $this->beginTransaction();

            // prepare query
            $temp_q_id = $this->prep_query($query);

            // execute query
            $temp_q_id->execute();

            // confirm transaction
            // $this->commit();

            if ($temp_q_id) {
                $lastId = $temp_q_id->fetch(PDO::FETCH_NUM);

                return ($lastId[0]) ? $lastId[0] : false;
            }
        } catch (PDOException $e) {

            // back transaction
            // $this->rollback();

            exit("Error: " . $e->getMessage());
        }
    }

    /*
     * function lastInsertId($name=null){
     * if(is_null($name))
     * return $this->pdo->lastInsertId();
     * else
     * return $this->pdo->lastInsertId($name);
     * }
     */
    function rollback()
    {
        return $this->pdo->rollback();
    }

    function commit()
    {
        return $this->pdo->commit();
    }

    function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    function prep_query($query)
    {
        return $this->pdo->prepare($query);
    }

    /*
     * function bindParam($keyname, $keyvalue, $pdo_param=null, $length=null){
     * if($length == null){
     * $this->pdo->bindParam($keyname, $keyvalue, $pdo_param);
     * }else{
     * $this->pdo->bindParam($keyname, $keyvalue, $pdo_param, $length);
     * }
     * }
     */
    function bindParam($keyname, $keyvalue, $pdo_param = null, $length = null)
    {
        if (is_null($pdo_param)) {
            switch (true) {
                case is_bool($keyvalue):
                    $pdo_param = PDO::PARAM_BOOL;
                    break;
                case is_int($keyvalue):
                    $pdo_param = PDO::PARAM_INT;
                    break;
                case is_null($keyvalue):
                    $pdo_param = PDO::PARAM_NULL;
                    break;
                default:
                    $pdo_param = PDO::PARAM_STR;
            }
        }

        if ($length == null) {

            $this->pdo->bindParam($keyname, $keyvalue, $pdo_param);
        } else {

            $this->pdo->bindParam($keyname, $keyvalue, $pdo_param, $length);
        }
    }

    function connect()
    {
        if (! $this->pdo) {

            $dsn = 'mysql:dbname=' . $this->database_name . ';host=' . $this->database_host;

            $user = $this->database_user;
            $password = $this->database_pass;

            try {
                $this->pdo = new PDO($dsn, $user, $password, array(
                    PDO::ATTR_PERSISTENT => true
                ));
                return true;
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                die($this->error);
                return false;
            }
        } else {
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            return true;
        }
    }

    function table_exists($table_name)
    {
        $stmt = $this->prep_query('SHOW TABLES LIKE ?');
        $stmt->execute(array(
            $this->add_table_prefix($table_name)
        ));
        return $stmt->rowCount() > 0;
    }

    function execute($query, $values = null)
    {
        if ($values == null) {
            $values = array();
        } else if (! is_array($values)) {
            $values = array(
                $values
            );
        }
        $stmt = $this->prep_query($query);
        $stmt->execute($values);
        return $stmt;
    }

    function fetch($query, $values = null)
    {
        if ($values == null) {
            $values = array();
        } else if (! is_array($values)) {
            $values = array(
                $values
            );
        }
        $stmt = $this->execute($query, $values);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function fetchAll2($query, $values = null, $key = null)
    {
        if ($values == null) {
            $values = array();
        } else if (! is_array($values)) {
            $values = array(
                $values
            );
        }
        $stmt = $this->execute($query, $values);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Allows the user to retrieve results using a
        // column from the results as a key for the array
        if ($key != null && $results[0][$key]) {
            $keyed_results = array();
            foreach ($results as $result) {
                $keyed_results[$result[$key]] = $result;
            }
            $results = $keyed_results;
        }
        return $results;
    }

    // function lastInsertId(){
    // return $this->pdo->lastInsertId();
    // }
}
