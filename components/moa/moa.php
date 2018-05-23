<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\moa;

defined('_EXEC') or die();

use moam\core\AppException;
use moam\libraries\core\log\ExecutionHistory;
use PDO;

class Moa
{

    private $DB = null;

    private $path_process = "";

    private $path_workspace = "";

    private $user_name = "";

    private $path_moa = "";

    private $path_moa_bin = "";

    private $moa_bin_name = "";

    private $path_source = "";

    private $execution_history;

    /**
     *
     * @return string
     */
    public function getPath_process()
    {
        return $this->path_process;
    }

    /**
     *
     * @return string
     */
    public function getPath_workspace()
    {
        return $this->path_workspace;
    }

    /**
     *
     * @return string
     */
    public function getUser_name()
    {
        return $this->user_name;
    }

    /**
     *
     * @return string
     */
    public function getPath_moa()
    {
        return $this->path_moa;
    }

    /**
     *
     * @return string
     */
    public function getPath_moa_bin()
    {
        return $this->path_moa_bin;
    }

    /**
     *
     * @return string
     */
    public function getMoa_bin_name()
    {
        return $this->moa_bin_name;
    }

    /**
     *
     * @return string
     */
    public function getPath_source()
    {
        return $this->path_source;
    }

    /**
     *
     * @param string $path_process
     */
    public function setPath_process($path_process)
    {
        $this->path_process = $path_process;
    }

    /**
     *
     * @param string $path_workspace
     */
    public function setPath_workspace($path_workspace)
    {
        $this->path_workspace = $path_workspace;
    }

    /**
     *
     * @param string $user_name
     */
    public function setUser_name($user_name)
    {
        $this->user_name = $user_name;
    }

    /**
     *
     * @param string $path_moa
     */
    public function setPath_moa($path_moa)
    {
        $this->path_moa = $path_moa;
    }

    /**
     *
     * @param string $path_moa_bin
     */
    public function setPath_moa_bin($path_moa_bin)
    {
        $this->path_moa_bin = $path_moa_bin;
    }

    /**
     *
     * @param string $moa_bin_name
     */
    public function setMoa_bin_name($moa_bin_name)
    {
        $this->moa_bin_name = $moa_bin_name;
    }

    /**
     *
     * @param string $path_source
     */
    public function setPath_source($path_source)
    {
        $this->path_source = $path_source;
    }

    public function __construct($DB)
    {
        $this->DB = $DB;
        $this->execution_history = new ExecutionHistory($DB);
    }

    public function insert_execution_history($process_type_id, $script, $process_initialized, $command, $source, $PID, $user_id)
    {
        $execution_history_id = null;
        try {
            $execution_history_id = $this->execution_history->process_initialized($user_id, $process_type_id, $script, $process_initialized, $command, $source, $PID);
        } catch (AppException $e) {

            throw new AppException($e->getMessage());
        }

        return $execution_history_id;
    }

    public function update_execution_history($execution_history_id, $process_closed)
    {
        try {
            // $process_closed = date_create()->format('Y-m-d H:i:s');

            if ($execution_history_id != null) {
                $this->execution_history->closed_process($execution_history_id, $process_closed);
            }
        } catch (AppException $e) {

            throw new AppException($e->getMessage());
        }
    }

    public function make($methodology_id, $user_id)
    {
        try {
            $rs = $this->DB->prep_query("SELECT methodology, alias
										FROM methodology
										WHERE
										 	user_id = ?
										AND methodology_id = ?");

            $rs->bindParam(1, $user_id, PDO::PARAM_INT);
            $rs->bindParam(2, $methodology_id, PDO::PARAM_INT);

            // open transaction
            $this->DB->beginTransaction();

            // execute query
            if ($rs->execute()) {

                // confirm transaction
                // $this->DB->commit();

                if ($rs->rowCount() > 0) {

                    // get data values
                    while ($row = $rs->fetch()) {

                        $methodology = "Make Copy " . $row["methodology"];
                        $alias = $row["alias"];

                        $rs2 = $this->DB->prep_query("INSERT INTO
											 methodology
									 (methodology,
										alias,
										user_id
										)
										VALUES (?,?,?)");

                        $rs2->bindParam(1, $methodology, PDO::PARAM_STR);
                        $rs2->bindParam(2, $alias, PDO::PARAM_STR);
                        $rs2->bindParam(3, $user_id, PDO::PARAM_INT);

                        if ($rs2->execute()) {} else {
                            throw new AppException($this->DB->getErrorMessage($rs2));
                        }
                    }
                }
            } else {

                throw new AppException($this->DB->getErrorMessage($rs));
            }

            // confirm transaction
            $this->DB->commit();
        } catch (AppException $e) {
            // back transaction
            $this->DB->rollback();

            throw new AppException($e->getMessage());
        }
    }

    public function export($methodology_id, $user_id)
    {
        $export_data = array();

        try {

            $rs = $this->DB->prep_query("SELECT methodology, alias
										FROM methodology
										WHERE
										 user_id = ?
										AND methodology_id = ?");

            $rs->bindParam(1, $user_id, PDO::PARAM_INT);
            $rs->bindParam(2, $methodology_id, PDO::PARAM_INT);

            // open transaction
            $this->DB->beginTransaction();

            if ($rs->execute()) {

                if ($rs->rowCount() > 0) {
                    // copy list itens
                    $list_data = array();

                    // get data values
                    while ($row = $rs->fetch()) {

                        $list_data[] = array(
                            "methodology_id" => $methodology_id,
                            "methodology" => $row["methodology"],
                            "alias" => $row["alias"]
                        );
                    }

                    $export_data[] = array(
                        "type" => "methodology",
                        "list" => $list_data
                    );
                }
            } else {

                throw new AppException($this->DB->getErrorMessage($rs));
            }

            // confirm transaction
            $this->DB->commit();
        } catch (AppException $e) {
            // back transaction
            $this->DB->rollback();

            throw new AppException($e->getMessage());
        }

        return $export_data;
    }

    public function selectFromUser($user_id)
    {
        $data = array();

        try {

            $rs = $this->DB->prep_query("SELECT 
			methodology.methodology_id,
			methodology.methodology,
			methodology.created,
			methodology.modified,
			methodology.alias
			
		FROM methodology
				
		WHERE  methodology.user_id=?
		
		ORDER by methodology.methodology asc");

            $rs->bindParam(1, $user_id, PDO::PARAM_INT);

            // execute query
            if ($rs->execute()) {
                $data = $rs;
            } else {

                throw new AppException($this->DB->getErrorMessage($rs));
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $data;
    }

    public function listFromUser($user_id)
    {
        $data = array();

        try {

            $rs = $this->DB->prep_query("SELECT
                    		methodology.methodology_id as id,
                    		methodology.methodology as name
                                    
                    		FROM methodology
                                    
                    		where
                    				methodology.user_id = ?
                                    
                    		ORDER by methodology.methodology asc");

            $rs->bindParam(1, $user_id, PDO::PARAM_INT);

            // execute query
            if ($rs->execute()) {

                if ($rs->rowCount() > 0) {
                    // get data values
                    $data = $rs;
                }
            } else {

                throw new AppException($this->DB->getErrorMessage($rs));
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $data;
    }

    public function select($methodology_id, $user_id)
    {
        $data = array();

        try {

            $rs = $this->DB->prep_query("SELECT methodology, alias
									FROM methodology 
									WHERE 
									 user_id = ?
									AND methodology_id = ?");

            $rs->bindParam(1, $user_id, PDO::PARAM_INT);
            $rs->bindParam(2, $methodology_id, PDO::PARAM_INT);

            // execute query
            if ($rs->execute()) {
                $data = $rs;
            } else {

                throw new AppException($this->DB->getErrorMessage($rs));
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $data;
    }

    public function insert($methodology, $alias, $user_id)
    {
        $method_id = null;

        try {

            $rs = $this->DB->prep_query("INSERT
										INTO methodology 
											(methodology, 
											user_id, 
											alias, 
											created)
										VALUES (?,?,?,
										now())");

            $rs->bindParam(1, $methodology, PDO::PARAM_STR);
            $rs->bindParam(2, $user_id, PDO::PARAM_INT);
            $rs->bindParam(3, $alias, PDO::PARAM_STR);

            // open transaction
            $this->DB->beginTransaction();

            // execute query
            if ($rs->execute()) {
                $method_id = $this->DB->lastInsertId();
            } else {
                throw new AppException($this->DB->getErrorMessage($rs));
            }

            // confirm transaction
            $this->DB->commit();
        } catch (AppException $e) {
            // back transaction
            $this->DB->rollback();

            throw new AppException($e->getMessage());
        }

        return $method_id;
    }

    public function update($methodology_id, $methodology, $alias, $user_id)
    {
        try {

            $rs = $this->DB->prep_query("UPDATE
												 methodology
										SET methodology = ?	,
											modified = now(),
											alias = ?
								
										WHERE 
											user_id = ?
										AND methodology_id = ?");

            $rs->bindParam(1, $methodology, PDO::PARAM_STR);
            $rs->bindParam(2, $alias, PDO::PARAM_STR);
            $rs->bindParam(3, $user_id, PDO::PARAM_INT);
            $rs->bindParam(4, $methodology_id, PDO::PARAM_INT);

            // open transaction
            $this->DB->beginTransaction();

            // execute query
            if ($rs->execute()) {} else {
                throw new AppException($this->DB->getErrorMessage($rs));
            }

            // confirm transaction
            $this->DB->commit();
        } catch (AppException $e) {
            // back transaction
            $this->DB->rollback();

            throw new AppException($e->getMessage());
        }
    }
}

?>