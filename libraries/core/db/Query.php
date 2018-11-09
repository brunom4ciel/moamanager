<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\db;

defined('_EXEC') or die();

use moam\core\AppException;
use PDO;

class Query
{

    private $DB = null;

    public function __construct($DB)
    {
        $this->DB = $DB;
    }

    private function convertArrayWhere($array)
    {
        $where = "";

        if (count($array) > 0) {
            foreach ($array as $field) {

                if (isset($field["logical_operator"])) {
                    $where .= ' ' . $field["logical_operator"] . ' ';
                }

                $where .= $field["field"] . ' ' . $field["cf_operator"] . ' ' . $field["value"];
            }
        }

        return $where;
    }

    private function convertArraySelect($array)
    {
        $select = "";

        if (count($array) > 0) {
            foreach ($array as $field) {
                if (! empty($select)) {
                    $select .= ", ";
                }

                if (isset($field["alias"])) {
                    $select .= $field["field"] . ' ' . $field["alias"];
                } else {
                    $select .= $field["field"];
                }
            }
        }

        return $select;
    }

    public function select($array_fields, $array_filters, $array_tables)
    {
        $data = array();

        try {
            $select = $this->convertArraySelect($array_fields);
            $where = $this->convertArrayWhere($array_filters);

            if (! empty($where)) {
                $where = " WHERE " . $where;
            }

            echo "SELECT " . $select . " FROM " . $table . " " . $where;

            exit("");

            $rs = $this->DB->prep_query("SELECT evaluation_id,
							evaluation, created, modified
							FROM evaluation
                
		                  WHERE
                            user_id = ?
                
		                  ORDER BY evaluation.created ASC");

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
}

    