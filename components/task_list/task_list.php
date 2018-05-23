<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\task_list;

defined('_EXEC') or die();

use moam\core\AppException;
use PDO;

class TaskList
{

    private $DB = null;

    public function __construct($DB)
    {
        $this->DB = $DB;
    }

    public function selectFromSuperUser()
    {
        $data = array();

        try {

            $rs = $this->DB->prep_query("SELECT
            execution_history.execution_history_id,
			process_type.process_type,
            process_type.process_type_id,
            substring(execution_history.script,1,200) as script,
            execution_history.process_initialized,
            execution_history.process_closed,
            substring(execution_history.command,1,250) as command,
            execution_history.source,
            execution_history.pid
                
		FROM execution_history
       INNER JOIN process_type ON
    process_type.process_type_id = execution_history.process_type_id
		where execution_history.process_closed IS NULL
		ORDER by execution_history_id DESC LIMIT 0,20");

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

    public function selectFromUser($user_id)
    {
        $data = array();

        try {

            $rs = $this->DB->prep_query("SELECT 
            execution_history.execution_history_id,
			process_type.process_type,
            process_type.process_type_id,
            substring(execution_history.script,1,200) as script,
            execution_history.process_initialized,
            execution_history.process_closed,
            substring(execution_history.command,1,250) as command,
            execution_history.source,
            execution_history.pid
			
		FROM execution_history
       INNER JOIN process_type ON 
    process_type.process_type_id = execution_history.process_type_id
		WHERE  user_id=?
		
		ORDER by execution_history_id DESC LIMIT 0,20");

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

    public function is_pid_from_user($pid, $user_id)
    {
        $result = false;

        try {

            $rs = $this->DB->prep_query("SELECT

                        execution_history.execution_history_id
                
        		FROM execution_history
        
        		WHERE  user_id=?
                        and pid = ?");

            $rs->bindParam(1, $user_id, PDO::PARAM_INT);
            $rs->bindParam(2, $pid, PDO::PARAM_INT);

            // execute query
            if ($rs->execute()) {
                if ($rs->rowCount() > 0) {
                    $result = true;
                }
            } else {

                throw new AppException($this->DB->getErrorMessage($rs));
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $result;
    }
}

?>