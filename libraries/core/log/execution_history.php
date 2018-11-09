<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\log;

defined('_EXEC') or die();

use moam\core\AppException;
use PDO;

class ExecutionHistory
{

    private $DB = null;

    public function __construct($DB)
    {
        $this->DB = $DB;
    }

    public function process_initialized($user_id, $process_type_id, $script, $process_initialized, $command, $source, $pid)
    {
        $id = null;

        try {

            $rs = $this->DB->prep_query("INSERT
										INTO execution_history
											(
                                            user_id, 
                                            process_type_id, 
                                            script, 
                                            process_initialized,

                                            process_closed,
                                            command, 
                                            source, 
                                            pid)
										VALUES (?,?,?,?,
                                                NULL,?,?,?)");

            $rs->bindParam(1, $user_id, PDO::PARAM_INT);
            $rs->bindParam(2, $process_type_id, PDO::PARAM_INT);
            $rs->bindParam(3, $script, PDO::PARAM_STR);
            $rs->bindParam(4, $process_initialized, PDO::PARAM_STR);
            // $rs->bindParam(5, $process_closed, PDO::PARAM_STR);
            $rs->bindParam(5, $command, PDO::PARAM_STR);
            $rs->bindParam(6, $source, PDO::PARAM_STR);
            $rs->bindParam(7, $pid, PDO::PARAM_INT);

            // open transaction
            $this->DB->beginTransaction();

            // execute query
            if ($rs->execute()) {
                $id = $this->DB->lastInsertId();
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

        return $id;
    }

    public function closed_process($execution_history_id, 
        // $user_id,
        $process_closed)
    {
        try {

            $rs = $this->DB->prep_query("UPDATE
												 execution_history
										SET 											
                                            
                                            process_closed = ?
	            
										WHERE

											execution_history_id = ?");

            $rs->bindParam(1, $process_closed, PDO::PARAM_STR);
            // $rs->bindParam(2, $user_id, PDO::PARAM_INT);
            $rs->bindParam(2, $execution_history_id, PDO::PARAM_INT);

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