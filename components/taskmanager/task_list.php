<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\task_list;

defined('_EXEC') or die();

use \Exception;
use moam\core\AppException;
use PDO;

class TaskList
{

    private $DB = null;

    public function __construct($DB)
    {
        $this->DB = $DB;
    }

    public function selectFromSuperUser($limit_start=0, $limit_end=40)
    {
        $data = array();

        try {

            $sql = "SELECT
            execution_history.execution_history_id,
			process_type.process_type,
            process_type.process_type_id,
            substring(execution_history.script,1,200) as script,
            execution_history.process_initialized,
            execution_history.process_closed,
            substring(execution_history.command,1,1550) as command,
            execution_history.source,
            execution_history.pid
                
		FROM execution_history
       INNER JOIN process_type ON
    process_type.process_type_id = execution_history.process_type_id
		where execution_history.process_closed IS NULL and pid is not null
		ORDER by execution_history.process_type_id asc, execution_history_id DESC"
            . " LIMIT ?,?";
            
            $rs = $this->DB->prep_query($sql);
                
            $rs->bindParam(1, $limit_start, PDO::PARAM_INT);
            $rs->bindParam(2, $limit_end, PDO::PARAM_INT);
            
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

    public function selectFromUser($user_id, $limit_start=0, $limit_end=40)
    {
        $data = array();

        try {

            $sql = "SELECT 
            execution_history.execution_history_id,
			process_type.process_type,
            process_type.process_type_id,
            substring(execution_history.script,1,200) as script,
            execution_history.process_initialized,
            execution_history.process_closed,
            substring(execution_history.command,1,1550) as command,
            execution_history.source,
            execution_history.pid
			
		FROM execution_history
       INNER JOIN process_type ON 
    process_type.process_type_id = execution_history.process_type_id
		WHERE  user_id=? and execution_history.process_closed is null and pid is not null
		
		ORDER by execution_history.process_type_id asc, 
                execution_history_id DESC LIMIT ?,?";
                        
            $rs = $this->DB->prep_query($sql);
            
            $rs->bindParam(1, $user_id, PDO::PARAM_INT);
            $rs->bindParam(2, $limit_start, PDO::PARAM_INT);
            $rs->bindParam(3, $limit_end, PDO::PARAM_INT);
            
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
    
    
    
    public function pid_setNull($execution_history_id)//, $pid)//, $user_id)
    {
        $result = false;
        
        try {
//             exit($execution_history_id . "=". $pid ."=". $user_id);
            $rs = $this->DB->prep_query("UPDATE execution_history set
                
                execution_history.pid = null

        		WHERE  execution_history.execution_history_id = ? and process_closed is null
                        ");
            
            $rs->bindParam(1, $execution_history_id, PDO::PARAM_INT);
//             $rs->bindParam(2, $user_id, PDO::PARAM_INT);
//             $rs->bindParam(2, $pid, PDO::PARAM_INT);
            
            // open transaction
            $this->DB->beginTransaction();
            
            // execute query
            if ($rs->execute()) {
                
                $result = true;
                
                // confirm transaction
                $this->DB->commit();
                
            } else {
                throw new Exception($this->DB->getErrorMessage($rs));
            }            
            
            
        } catch (Exception $e) {
            
            // back transaction
            $this->DB->rollback();
            
            throw new Exception($e->getMessage());
        }
        
        return $result;
    }
    
    
}

?>