<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\user;

defined('_EXEC') or die();

use moam\core\AppException;
use moam\core\Framework;
use PDOException;
use PDO;

class User
{

    private $json = array();

    private $data = "";

    // private $passphrase = "";
    private $db;

    public function __construct($dbpdo)
    {
        $this->db = $dbpdo;
    }

    public function user_exists($email)
    {
        $result = false;

        try {

            $data_db = $this->db->prep_query("SELECT count(*) exist
										FROM user
										WHERE email = ?");

            $data_db->bindParam(1, $email, PDO::PARAM_STR);

            // open transaction
            $this->db->beginTransaction();

            // execute query
            $data_db->execute();

            // confirm transaction
            $this->db->commit();

            $db_result_error = $data_db->errorInfo();

            if ($db_result_error[2] != "") {

                $error = $db_result_error[2];

                throw new AppException(get_class() . ' error: database ' . $error);
            } else {

                $data_db = $data_db->fetchAll();

                if ($data_db[0]["exist"] > 0) {
                    $result = true;
                }
            }
        } catch (AppException $e) {

            throw new AppException(get_class() . ' error: database ' . __FUNCTION__);
        } catch (PDOException $e) {

            throw new AppException(get_class() . ' error: database ' . $e->getMessage());
        }

        return $result;
    }

    public function create($email, $password, $user_type_id = 2, $workspace)
    {
        $result = false;

        try {

            $name = $email;

            $data_db = $this->db->prep_query("INSERT INTO
										user (email, password, name, user_type_id, workspace)
									VALUES
										(?,?,?,?,?)");

            $data_db->bindParam(1, $email, PDO::PARAM_STR);
            $data_db->bindParam(2, $password, PDO::PARAM_STR);
            $data_db->bindParam(3, $name, PDO::PARAM_STR);
            $data_db->bindParam(4, $user_type_id, PDO::PARAM_INT);
            $data_db->bindParam(5, $workspace, PDO::PARAM_STR);

            // open transaction
            $this->db->beginTransaction();

            // execute query
            $data_db->execute();

            // confirm transaction
            $this->db->commit();

            $db_result_error = $data_db->errorInfo();

            if ($db_result_error[2] != "") {

                $error = $db_result_error[2];

                throw new AppException(get_class() . ' error: database ' . $error);
            }

            $result = true;
        } catch (AppException $e) {

            $this->db->rollback();

            throw new AppException(get_class() . ' error: database ' . __FUNCTION__);
        } catch (PDOException $e) {

            $this->db->rollback();

            throw new AppException(get_class() . ' error: database ' . $e->getMessage());
        }

        return $result;
    }

    public function remove($email)
    {
        try {

            $data_db = $this->db->prep_query("SELECT count(*) exist
										FROM user
										WHERE email = ?");

            $data_db->bindParam(1, $email, PDO::PARAM_STR);

            if ($data_db->execute()) {
                $data_db = $data_db->fetchAll();

                if ($data_db[0]["exist"] > 0) {

                    // remover

                    $data_db = $this->db->prep_query("DELETE FROM
										user
									WHERE email = ?");

                    $data_db->bindParam(1, $email, PDO::PARAM_STR);

                    if ($data_db->execute()) {
                        $result = true;
                    } else {
                        $result = false;
                    }
                }
            } else {
                $db_result_error = $data_db->errorInfo();

                $error = "";

                if ($db_result_error[2] != "")
                    $error = $db_result_error[2];

                throw new AppException(get_class() . ' error: database ' . $error);
            }
        } catch (AppException $e) {
            throw new AppException(get_class() . ' error: database ' . __FUNCTION__);
        }
    }

    public function login($email, $password)
    {
        $result = false;

        try {

            $data_db = $this->db->prep_query("SELECT count(*) exist
										FROM user
										WHERE email = ? AND password = ?");

            $data_db->bindParam(1, $email, PDO::PARAM_STR);
            $data_db->bindParam(2, $password, PDO::PARAM_STR);

            if ($data_db->execute()) {
                $data_db = $data_db->fetchAll();
               
                if ($data_db[0]["exist"] > 0) {
                    $result = true;
                }
            } else {
                $db_result_error = $data_db->errorInfo();

                $error = "";

                if ($db_result_error[2] != "")
                    $error = $db_result_error[2];

                throw new AppException(get_class() . ' error: database ' . $error);
            }
        } catch (AppException $e) {
            throw new AppException(get_class() . ' error: database');
        }

        return $result;
    }

    public function getCredentials($email)
    {
        $result = array();

        try {

            $data_db = $this->db->prep_query("SELECT user_id, user_type_id, password, workspace
										FROM user
										WHERE email = ? ");

            $data_db->bindParam(1, $email, PDO::PARAM_STR);

            if ($data_db->execute()) {
                $data_db = $data_db->fetchAll();

                if (count($data_db[0]) > 0) {
                    $result = array(
                        "email" => $email,
                        "password" => $data_db[0]["password"],
                        "user_id" => $data_db[0]["user_id"],
                        "type" => $data_db[0]["user_type_id"],
                        "workspace" => $data_db[0]["workspace"]
                    );
                }
            } else {
                $db_result_error = $data_db->errorInfo();

                $error = "";

                if ($db_result_error[2] != "")
                    $error = $db_result_error[2];

                throw new AppException(get_class() . ' error: database ' . $error);
            }
        } catch (AppException $e) {
            throw new AppException(get_class() . ' error: database ' . __FUNCTION__);
        }

        return $result;
    }

    public function changePassword($email, $newpwd)
    {
        $result = false;

        try {

            $data_db = $this->db->prep_query("SELECT count(*) exist
										FROM user
										WHERE email = ?");

            $data_db->bindParam(1, $email, PDO::PARAM_STR);

            if ($data_db->execute()) {
                $data_db = $data_db->fetchAll();

                if ($data_db[0]["exist"] > 0) {

                    // remover

                    $data_db = $this->db->prep_query("UPDATE
										user
									password = ?
									WHERE email = ?");

                    $data_db->bindParam(1, $email, PDO::PARAM_STR);
                    $data_db->bindParam(2, $newpwd, PDO::PARAM_STR);

                    if ($data_db->execute()) {
                        $result = true;
                    } else {
                        $result = false;
                    }
                }
            } else {
                $db_result_error = $data_db->errorInfo();

                $error = "";

                if ($db_result_error[2] != "")
                    $error = $db_result_error[2];

                throw new AppException(get_class() . ' error: database ' . $error);
            }
        } catch (AppException $e) {
            throw new AppException(get_class() . ' error: database ' . __FUNCTION__);
        }

        return $result;
    }
}