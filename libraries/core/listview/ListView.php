<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\listview;

defined('_EXEC') or die();

class Item
{

    public $value = null;

    public $ic = null;

    public $group = null;

    public $color = null;

    public $col = 0;

    public $row = 0;

    public function add($value = "", $ic = 0, $group = "", $color = "", $row = 0, $col = 0)
    {
        $data = new Item();

        $data->value = $value;
        $data->ic = $ic;
        $data->group = $group;
        $data->color = $color;
        $data->col = $col;
        $data->row = $row;

        return $data;
    }
}

class ListItem extends Item
{

    private $cols = array();

    private $rows = array();

    public function ListItemClear()
    {
        $this->cols = array();
        $this->rows = array();
    }

    public function ListItemAdd($value = "", $ic = 0, $group = "", $color = "")
    {
        $row = $this->countRows();
        $col = $this->countCols();

        $data = self::add($value, $ic, $group, $color, $row, $col);
        $this->cols[] = $data;
    }

    public function newRow()
    {
        $this->rows[] = $this->cols;
        $this->cols = array();
    }

    public function countCols()
    {
        return count($this->cols);
    }

    public function countRows()
    {
        return count($this->rows);
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function getCols()
    {
        return $this->cols;
    }

    public function getRow($index)
    {
        if (isset($this->rows[$index]))
            return $this->rows[$index];
        else
            return false;
    }
}

class ListView extends ListItem
{

    private $columns = array();

    private $orientation = "vertical";

    private $classfiedByLine = false;

    private $classfiedByAll = false;

    private $averageColumnByGroup = false;

    private $averageLine = false;

    private $averageColumnByAll = false;

    private $precision = 2;

    private $tablename = "";

    private $showColumnsHeaders = true;

    private $colors = array();

    private $showDataType = 0;

    private $orderClassified = "desc";

    private $imageBg = array();

    private $dirTmpl = "";

    private $grayScale = false;

    const TYPE_VALUE = 0;

    const TYPE_IC = 1;

    const TYPE_VALUEIC = 2;

    public function setShowData($type = "value")
    {
        if ($type == "value") {
            $this->showDataType = self::TYPE_VALUE;
        } else {
            if ($type == "ic") {
                $this->showDataType = self::TYPE_IC;
            } else {
                if ($type == "value+ic") {
                    $this->showDataType = self::TYPE_VALUEIC;
                }
            }
        }
    }

    public function getShowData()
    {
        return $this->showDataType;
    }

    public function __construct()
    {
        $this->colors = array(
            "#00FF00",
            "#00FFFF",
            "#FFFF00",
            "#FF7F24",
            "#ffccff"
        );
        $this->imageBg = array(
            "bg-black-twill-2.png",
            "bg-black-twill-3.png",
            "bg-black-twill-4.png",
            "bg-black-twill-5.png",
            "bg-black-twill-6.png"
        );
    }

    public function setGrayScale($active = false)
    {
        $this->grayScale = $active;
    }

    public function getGrayScalel()
    {
        return $this->grayScale;
    }

    public function setDirTmpl($dirTmpl = "")
    {
        $this->dirTmpl = $dirTmpl;
    }

    public function getDirTmpl()
    {
        return $this->dirTmpl;
    }

    public function setOrderClassified($order = "desc")
    {
        if ($order == "desc")
            $this->orderClassified = $order;
        else if ($order == "asc")
            $this->orderClassified = $order;
        else
            $this->orderClassified = "desc";
    }

    public function getOrderClassified()
    {
        return $this->orderClassified;
    }

    public function colorAdd($color)
    {
        $this->colors[] = $color;
    }

    public function colorClear()
    {
        $this->colors = array();
    }

    public function colorRemove($index)
    {
        if (! empty($this->getColor($index)))
            unset($this->colors[$index]);
    }

    public function getColor($index)
    {
        $result = "";

        if (isset($this->colors[$index]))
            $result = $this->colors[$index];
        else
            $result = "";

        return $result;
    }

    public function getImageBg($index)
    {
        $result = "";

        if (isset($this->imageBg[$index]))
            $result = $this->imageBg[$index];
        else
            $result = "";

        return $result;
    }

    public function getColors()
    {
        return $this->colors;
    }

    public function setTableName($name)
    {
        $this->tablename = $name;
    }

    public function getTableName()
    {
        return $this->tablename;
    }

    private function number_format_precision($num, $precision = 2)
    {
        return number_format($this->cutNum($num, $precision), $precision);
    }

    private function cutNum($num, $precision = 2)
    {
        return floor($num) . substr($num - floor($num), 1, $precision + 1);
    }

    public function setPrecision($precision = 2)
    {
        $this->precision = $precision;
    }

    public function getPrecision()
    {
        return $this->precision;
    }

    public function setAverageColumnByGroup($active = false)
    {
        $this->averageColumnByGroup = $active;
    }

    public function getAverageColumnByGroup()
    {
        return $this->averageColumnByGroup;
    }

    public function setAverageLine($active = false)
    {
        $this->averageLine = $active;
    }

    public function getAverageLine()
    {
        return $this->averageLine;
    }

    public function setAverageColumnByAll($active = false)
    {
        $this->averageColumnByAll = $active;
    }

    public function getAverageColumnByAll()
    {
        return $this->averageColumnByAll;
    }

    public function setClassifiedByLine($active = false)
    {
        $this->classfiedByLine = $active;
    }

    public function getClassifiedByLine()
    {
        return $this->classfiedByLine;
    }

    public function setClassifiedByAll($active = false)
    {
        $this->classfiedByAll = $active;
    }

    public function getClassifiedByAll()
    {
        return $this->classfiedByAll;
    }

    public function getOrientationColumnsHeaders()
    {
        return $this->orientation;
    }

    public function orientationColumnsHeaders($orientation = "vertical")
    {
        if ($orientation == "vertical")
            $this->orientation = $orientation;
        else if ($orientation == "horizontal")
            $this->orientation = $orientation;
    }

    public function countColumns()
    {
        return count($this->columns);
    }

    public function ColumnsHeadersItemAdd($column)
    {
        $this->columns[] = $column;
    }

    public function getColumnsHeaders()
    {
        return $this->columns;
    }

    public function setShowColumnsHeaders($visible = true)
    {
        $this->showColumnsHeaders = $visible;
    }

    public function getShowColumnsHeaders()
    {
        return $this->showColumnsHeaders;
    }

    public function sumArray($data)
    {
        $result = null;

        if (isset($data)) {
            $result = 0;

            for ($i = 0; $i < count($data); $i ++)
                $result += $data[$i];
        }

        return $result;
    }

    public function classifiedColors($data, $colors = null)
    {
        if (is_null($colors)) {
            $elements_classifieds_colors = array(
                "#00FF00",
                "#00FFFF",
                "#FFFF00",
                "#FF7F24",
                "#FF00FF"
            );
            $elements_classifieds_bg = array(
                "bg-black-mamba.png",
                "bg-black-twill.png",
                "bg-carbon-fibre.png",
                "bg-dark-exa.png",
                "bg-gun-metal.png"
            );
        } else
            $elements_classifieds_colors = $colors;

        // $elements_classfieds_order = array("First", "second", "Third", "Fourth", "Fifth");
        $classifeds_order = array();

        // foreach($elements_classifieds_colors as $elements)
        // $classifeds_order[] = null;

        foreach ($data as $value) {

            // var_dump($element);
            // exit();
            // foreach($element as $value){
            // for($i=0; $i <count($classifeds_order); $i++){
            // $col = $data->value;

            if (is_numeric($value)) {

                if ($this->getOrderClassified() == "desc") {

                    for ($i = 0; $i < count($elements_classifieds_colors); $i ++) {

                        if (! isset($classifeds_order[$i]))
                            $classifeds_order[$i] = $value;

                        if ($value > $classifeds_order[$i]) {

                            if (($i + 1) < count($elements_classifieds_colors)) {
                                $classifeds_order[$i + 1] = $classifeds_order[$i];
                            } else {
                                $classifeds_order[$i] = $classifeds_order[$i];
                            }

                            $classifeds_order[$i] = $value;
                            break;
                        }
                    }
                } else {

                    for ($i = 0; $i < count($elements_classifieds_colors); $i ++) {

                        if (! isset($classifeds_order[$i]))
                            $classifeds_order[$i] = $value;

                        if ($value < $classifeds_order[$i]) {

                            if (($i + 1) < count($elements_classifieds_colors)) {
                                $classifeds_order[$i + 1] = $classifeds_order[$i];
                            } else {
                                $classifeds_order[$i] = $classifeds_order[$i];
                            }

                            $classifeds_order[$i] = $value;
                            break;
                        }
                    }
                }
            }
            // }
        }

        // rsort($classifeds_order);

        // ******************************************************
        // para remover as duplicatas da lista ordenada

        $classifeds_order2 = $classifeds_order;
        $last = 0;

        for ($i = 0; $i < count($classifeds_order); $i ++) {

            if ($i == 0)
                $last = $classifeds_order[$i];
            else {

                if ($last == $classifeds_order[$i]) {
                    unset($classifeds_order2[$i]);
                } else {
                    $last = $classifeds_order[$i];
                }
            }
        }

        $classifeds_order3 = array();

        foreach ($classifeds_order2 as $element) {
            $classifeds_order3[] = $element;
        }

        return $classifeds_order3;
    }

    public function getCelDataNumber($data, $datatype, $precision = 2)
    {
        $result = "";

        // if(is_numeric($data->value)){

        if ($datatype == self::TYPE_VALUE)
            $result = $data->value;
        else if ($datatype == self::TYPE_IC)
            $result = $data->ic;
        else if ($datatype == self::TYPE_VALUEIC)
            if (is_numeric($data->value))
                $result = $data->value;

        // }

        return $result;
    }

    public function getCelData($data, $datatype, $precision = 2)
    {
        $result = "";

        if ($datatype == self::TYPE_VALUE)
            $result = $data->value;
        else if ($datatype == self::TYPE_IC)
            if (is_null($data->ic))
                $result = $data->value;
            else
                $result = $data->ic;
        else if ($datatype == self::TYPE_VALUEIC)
            if (is_numeric($data->value))
                $result = $data->value . " ±" . $data->ic;
            else
                $result = $data->value;

        return $result;
    }

    public function grid()
    {
        $result = "";

        if (self::countRows() == 0) {
            if (self::countCols() > 0)
                self::newRow();
            // $this->row[] = self::getCols();
        }

        $table2 = "";

        if (self::countRows() > 0) {

            $max = null;

            if ($this->getClassifiedByAll() or $this->getClassifiedByLine()) {

                $elements_classifieds_colors = array(
                    "#00FF00",
                    "#00FFFF",
                    "#FFFF00",
                    "#FF7F24",
                    "#FF00FF"
                );
                // $elements_classfieds_order = array("First", "second", "Third", "Fourth", "Fifth");
                $classifeds_order = array();

                // foreach($elements_classifieds_colors as $elements)
                // $classifeds_order[] = null;

                foreach (self::getRows() as $element) {

                    foreach ($element as $data) {
                        // for($i=0; $i <count($classifeds_order); $i++){
                        // $col = $data->value;

                        $cel_number = $this->getCelDataNumber($data, $this->getShowData(), $this->getPrecision());

                        if (is_numeric($cel_number)) {

                            $col = $this->number_format_precision($cel_number, $this->getPrecision());

                            if ($this->getOrderClassified() == "desc") {

                                for ($i = 0; $i < count($elements_classifieds_colors); $i ++) {

                                    if (! isset($classifeds_order[$i]))
                                        $classifeds_order[$i] = $col;

                                    if ($col > $classifeds_order[$i]) {

                                        if (($i + 1) < count($elements_classifieds_colors)) {
                                            $classifeds_order[$i + 1] = $classifeds_order[$i];
                                        } else {
                                            $classifeds_order[$i] = $classifeds_order[$i];
                                        }

                                        $classifeds_order[$i] = $col;
                                        break;
                                    }
                                }
                            } else {

                                for ($i = 0; $i < count($elements_classifieds_colors); $i ++) {

                                    if (! isset($classifeds_order[$i]))
                                        $classifeds_order[$i] = $col;

                                    if ($col < $classifeds_order[$i]) {

                                        if (($i + 1) < count($elements_classifieds_colors)) {
                                            $classifeds_order[$i + 1] = $classifeds_order[$i];
                                        } else {
                                            $classifeds_order[$i] = $classifeds_order[$i];
                                        }

                                        $classifeds_order[$i] = $col;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ($this->countColumns() > 0) {

                if ($this->getShowColumnsHeaders()) {

                    $table2 .= "<thead>
	 								<tr>";
                    foreach ($this->getColumnsHeaders() as $col) {

                        if ($this->getOrientationColumnsHeaders() == "vertical")
                            $table2 .= "<th><text class='vertical-text'>" . $col . "</text></th>";
                        else
                            $table2 .= "<th>" . $col . "</th>";
                    }

                    if ($this->getAverageLine()) {
                        if ($this->getOrientationColumnsHeaders() == "vertical")
                            $table2 .= "<th><text class='vertical-text'>Average</text></th>";
                        else
                            $table2 .= "<th>Average</th>";
                    }

                    $table2 .= "</tr>
								</thead>";
                }
            }

            $table2 .= "<tbody>\n";

            foreach (self::getRows() as $element) {

                $table2 .= "\n<tr>\n";

                foreach ($element as $data) {

                    if ($this->getAverageColumnByGroup()) {

                        if (! isset($last_group_id)) {

                            // $last_group_id = $data->group;
                            $last_group_id_dritf = true;
                        } else {

                            if ($last_group_id == $data->group) {
                                // o mesmo
                            } else {
                                // mudou
                                $last_group_id_dritf = true;
                            }

                            // $last_group_id = $data->group;
                        }

                        if ($last_group_id_dritf) {

                            $last_group_id_dritf = false;

                            if (isset($matriz)) {

                                if (count($matriz) > 0) {

                                    // var_dump($matriz);exit();

                                    $sumCol = array();
                                    $countRows = 0;

                                    foreach ($matriz as $keyGroup => $grupos) {

                                        if ($last_group_id == $keyGroup) {

                                            foreach ($grupos as $keyRow => $rows) {

                                                foreach ($rows as $keyCols => $cols) {

                                                    if (isset($sumCol[$keyCols]))
                                                        $sumCol[$keyCols] += floatval($matriz[$keyGroup][$keyRow][$keyCols][0]);
                                                    else
                                                        $sumCol[$keyCols] = floatval($matriz[$keyGroup][$keyRow][$keyCols][0]);
                                                }
                                                $countRows ++;
                                            }
                                        }
                                    }

                                    foreach ($sumCol as $key => $value) {

                                        $sumCol[$key] = $this->number_format_precision(($value / $countRows), $this->getPrecision());
                                    }

                                    $order = $this->classifiedColors($sumCol, $this->getColors());

                                    // $matriz = null;

                                    // $table2 .= "</tr>\n<tr style='border-top:2px solid #000000'><th><b>Average</b></th>";
                                    $table2 .= "<th><b>Average</b></th>";
                                    // exit($table2);
                                    foreach ($sumCol as $key => $value) {

                                        $index_color = null;

                                        foreach ($order as $key => $item) {

                                            if ($value == $item) {
                                                $index_color = $key;
                                                break;
                                            }
                                        }

                                        if (is_null($index_color)) {

                                            $table2 .= "<th>" . $value . "</th>";
                                        } else {

                                            if ($this->getGrayScalel() == true) {

                                                $table2 .= "<th style='color:#000000;font-weight:bold;background-image: url(\"" . $this->getDirTmpl() . "images/" . $this->getImageBg($index_color) . "\");'>" . $value . "</th>";
                                            } else {

                                                $table2 .= "<th style='color:#000000;background-color: " . $this->getColor($index_color) . ";font-weight:bold;'>" . $value . "</th>";
                                            }
                                        }
                                    }

                                    if ($this->getAverageLine()) {
                                        $table2 .= "<th></th>";
                                    }

                                    $table2 .= "\n</tr>\n\n<tr>\n"; // echo $table2;exit("****fim");
                                }
                            }
                        } else {}

                        $last_group_id = $data->group;
                    }

                    $cel_number = $this->getCelDataNumber($data, $this->getShowData(), $this->getPrecision());
                    $cel_data = $this->getCelData($data, $this->getShowData(), $this->getPrecision());

                    if (is_numeric($cel_number)) { // $data->value)){

                        // $data->value = $this->number_format_precision($data->value, $this->getPrecision());

                        if ($this->getAverageColumnByGroup() == true or $this->getAverageColumnByAll() == true) {

                            if (isset($matriz)) {
                                $matriz[$data->group][$data->row][$data->col][] = $cel_number; // $data->value;
                            } else {
                                $matriz = array();
                                $matriz[$data->group][$data->row][$data->col][] = $cel_number; // $data->value;
                            }
                        }

                        if ($this->getAverageLine()) {

                            if (isset($average)) {
                                $average[] = $cel_number; // $data->value;
                            } else {
                                $average = array();
                                $average[] = $cel_number; // $data->value;
                            }
                        }
                    }

                    // **************************************************
                    //
                    // max values by line
                    if ($this->getClassifiedByLine()) {

                        $max = null;
                        // $cel_number_value = null;
                        // foreach($line as $cols){
                        foreach ($element as $datas) {

                            // $cols = $datas->value;
                            $cel_number_value = $this->getCelDataNumber($datas, $this->getShowData(), $this->getPrecision());

                            if (is_numeric($cel_number_value)) {

                                if (is_null($max)) {

                                    $max = $cel_number_value;
                                } else {

                                    if ($this->getOrderClassified() == "desc") {
                                        if ($cel_number_value > $max)
                                            $max = $cel_number_value;
                                    } else {
                                        if ($cel_number_value < $max)
                                            $max = $cel_number_value;
                                    }
                                }
                            }
                        }
                    }
                    //
                    // **************************************************

                    if ($this->getClassifiedByAll()) {

                        $equals = false;

                        for ($index_color = 0; $index_color < count($classifeds_order); $index_color ++) {

                            $cols = $cel_data; // $data->value;

                            if ($classifeds_order[$index_color] == $cols) {

                                if ($this->getClassifiedByLine()) {

                                    if ($max == $cols) {

                                        if ($this->getGrayScalel() == true) {

                                            $table2 .= "<th style='color:#000000;font-weight:bold;background-image: url(\"" . $this->getDirTmpl() . "images/" . $this->getImageBg($index_color) . "\");'>" . $cols . "</th>";
                                        } else {

                                            $table2 .= "<th style='color:#000000;background-color: " . $this->getColor($index_color) . ";font-weight:bold;'>" . $cols . "</th>";
                                        }

                                        // $table2 .= "<th style='background-color:".$elements_classifieds_colors[$w].";font-weight:bold;'>".$cols."</th>";
                                    } else {

                                        if ($this->getGrayScalel() == true) {

                                            $table2 .= "<th style='color:#000000;background-image: url(\"" . $this->getDirTmpl() . "images/" . $this->getImageBg($index_color) . "\");'>" . $cols . "</th>";
                                        } else {

                                            $table2 .= "<th style='color:#000000;background-color: " . $this->getColor($index_color) . ";'>" . $cols . "</th>";
                                        }

                                        // $table2 .= "<th style='background-color:".$elements_classifieds_colors[$w]."'>".$cols."</th>";
                                    }
                                } else {

                                    if ($this->getGrayScalel() == true) {

                                        $table2 .= "<th style='color:#000000;background-image: url(\"" . $this->getDirTmpl() . "images/" . $this->getImageBg($index_color) . "\");'>" . $cols . "</th>";
                                    } else {

                                        $table2 .= "<th style='color:#000000;background-color: " . $this->getColor($index_color) . ";'>" . $cols . "</th>";
                                    }

                                    // $table2 .= "<th style='background-color:".$elements_classifieds_colors[$w]."'>".$cols."</th>";
                                }

                                $equals = true;
                                break;
                            }
                        }

                        if (! $equals) {

                            if ($this->getClassifiedByLine()) {

                                if ($max == $cols) {

                                    if ($this->getGrayScalel() == true) {
                                        $table2 .= "<th style='font-weight:bold;'>" . $cols . "</th>";
                                    } else {
                                        $table2 .= "<th style='background-color: #FBEFEF;font-weight:bold;'>" . $cols . "</th>";
                                    }

                                    // $table2 .= "<th style='background-color: #FBEFEF;font-weight:bold;'>".$cols."</th>";
                                } else {
                                    if (is_numeric($cel_number))
                                        $table2 .= "<th>" . $cols . "</th>";
                                    else
                                        $table2 .= "<th style='max-width:120px;width:auto;text-align:left;'>" . $cols . "</th>";
                                }
                            } else {

                                if (is_numeric($cel_number))
                                    $table2 .= "<th>" . $cols . "</th>";
                                else
                                    $table2 .= "<th style='max-width:120px;width:auto;text-align:left;'>" . $cols . "</th>";
                            }
                        }
                    } else {

                        if ($this->getClassifiedByLine()) {

                            $equals = false;

                            for ($w = 0; $w < count($classifeds_order); $w ++) {

                                $cols = $cel_number; // $data->value;

                                if ($classifeds_order[$w] == $cols) {

                                    if ($max == $cols) {

                                        if ($this->getGrayScalel() == true) {
                                            $table2 .= "<th style='font-weight:bold;'>" . $cols . "</th>";
                                        } else {
                                            $table2 .= "<th style='background-color: #FBEFEF;font-weight:bold;'>" . $cols . "</th>";
                                        }

                                        // $table2 .= "<th style='background-color: #FBEFEF;font-weight:bold;'>".$cols."</th>";
                                    } else {
                                        if (is_numeric($cel_number))
                                            $table2 .= "<th>" . $cols . "</th>";
                                        else
                                            $table2 .= "<th style='max-width:120px;width:auto;text-align:left;'>" . $cols . "</th>";

                                        // $table2 .= "<th style=''>".$cols."</th>";
                                    }

                                    $equals = true;
                                    break;
                                }
                            }

                            if (! $equals) {

                                if ($max == $cols) {

                                    if ($this->getGrayScalel() == true) {
                                        $table2 .= "<th style='font-weight:bold;'>" . $cols . "</th>";
                                    } else {
                                        $table2 .= "<th style='background-color: #FBEFEF;font-weight:bold;'>" . $cols . "</th>";
                                    }

                                    // $table2 .= "<th style='background-color: #FBEFEF;font-weight:bold;'>".$cols."</th>";
                                } else {
                                    if (is_numeric($cel_number))
                                        $table2 .= "<th>" . $cols . "</th>";
                                    else
                                        $table2 .= "<th style='max-width:120px;width:auto;text-align:left;'>" . $cols . "</th>";
                                }
                            }
                        } else {

                            if (is_numeric($cel_number))
                                $table2 .= "<th>" . $cel_data . "</th>";
                            else
                                $table2 .= "<th style='max-width:120px;width:auto;text-align:left;'>" . $cel_data . "</th>";
                        }
                    }
                }

                if ($this->getAverageLine()) {
                    if (isset($average)) {
                        $table2 .= "<th>" . $this->number_format_precision($this->sumArray($average) / count($average), $this->getPrecision()) . "</th>";

                        $average = null;
                    } else {
                        // $table2 .= "<th></th>";
                    }
                } else {
                    // $table2 .= "<th></th>";
                }

                $table2 .= "\n</tr>\n";
            }

            if (self::countCols() > 0) {

                $table2 .= "\n<tr>\n";

                // **************************************************
                //
                // max values by line
                if ($this->getClassifiedByLine()) {

                    $max = null;
                    // $cel_number_value = null;
                    // foreach($line as $cols){
                    foreach (self::getCols() as $datas) {

                        // $cols = $datas->value;
                        $cel_number_value = $this->getCelDataNumber($datas, $this->getShowData(), $this->getPrecision());

                        if (is_numeric($cel_number_value)) {

                            if (is_null($max)) {

                                $max = $cel_number_value;
                            } else {

                                if ($this->getOrderClassified() == "desc") {
                                    if ($cel_number_value > $max)
                                        $max = $cel_number_value;
                                } else {
                                    if ($cel_number_value < $max)
                                        $max = $cel_number_value;
                                }
                            }
                        }
                    }
                }
                //
                // **************************************************

                // exit($max);

                foreach (self::getCols() as $data) {

                    $cel_number = $this->getCelDataNumber($data, $this->getShowData(), $this->getPrecision());
                    $cel_data = $this->getCelData($data, $this->getShowData(), $this->getPrecision());

                    if (is_numeric($cel_number)) { // $data->value)){

                        // $data->value = $this->number_format_precision($data->value, $this->getPrecision());

                        if ($this->getAverageColumnByGroup() == true or $this->getAverageColumnByAll() == true) {

                            if (isset($matriz)) {
                                $matriz[$data->group][$data->row][$data->col][] = $cel_number; // $data->value;
                            } else {
                                $matriz = array();
                                $matriz[$data->group][$data->row][$data->col][] = $cel_number; // $data->value;
                            }
                        }

                        if ($this->getAverageLine()) {

                            if (isset($average)) {
                                $average[] = $cel_number; // $data->value;
                            } else {
                                $average = array();
                                $average[] = $cel_number; // $data->value;
                            }
                        }
                    } else {}

                    if (is_numeric($cel_number)) {

                        if ($max == $cel_number) {

                            $table2 .= "<th style='font-weight:bold;'>" . $cel_number . "</th>";
                        } else {

                            $table2 .= "<th>" . $cel_number . "</th>";
                        }
                    } else
                        $table2 .= "<th style='max-width:120px;width:auto;text-align:left;'>" . $cel_data . "</th>";

                    // $table2 .= "<th>".$cel_data."</th>";
                }

                if ($this->getAverageLine()) {
                    if (isset($average)) {
                        $table2 .= "<th>" . $this->number_format_precision($this->sumArray($average) / count($average), $this->getPrecision()) . "</th>";

                        $average = null;
                    } else {
                        $table2 .= "<th></th>";
                    }
                }

                $table2 .= "</tr>";

                if ($this->getAverageColumnByGroup()) {

                    $sumCol = array();
                    $countRows = 0;

                    foreach ($matriz as $keyGroup => $grupos) {

                        if ($last_group_id == $keyGroup) {

                            foreach ($grupos as $keyRow => $rows) {

                                foreach ($rows as $keyCols => $cols) {

                                    if (isset($sumCol[$keyCols]))
                                        $sumCol[$keyCols] += floatval($matriz[$keyGroup][$keyRow][$keyCols][0]);
                                    else
                                        $sumCol[$keyCols] = floatval($matriz[$keyGroup][$keyRow][$keyCols][0]);
                                }
                                $countRows ++;
                            }
                        }
                    }

                    foreach ($sumCol as $key => $value) {

                        $sumCol[$key] = $this->number_format_precision(($value / $countRows), $this->getPrecision());
                    }

                    // var_dump($sumCol);

                    $order = $this->classifiedColors($sumCol, $this->getColors());

                    // $matriz = null;

                    $table2 .= "\n\n<tr>\n<th><b>Average</b></th>";
                    // exit($table2);
                    foreach ($sumCol as $key => $value) {

                        $index_color = null;

                        foreach ($order as $key => $item) {

                            if ($value == $item) {
                                $index_color = $key;
                                break;
                            }
                        }

                        if (is_null($index_color)) {

                            $table2 .= "<th>" . $value . "</th>";
                        } else {

                            if ($this->getGrayScalel() == true) {

                                $table2 .= "<th style='color:#000000;font-weight:bold;background-image: url(\"" . $this->getDirTmpl() . "images/" . $this->getImageBg($index_color) . "\");'>" . $value . "</th>";
                            } else {

                                $table2 .= "<th style='color:#000000;font-weight:bold;background-color: " . $this->getColor($index_color) . ";'>" . $value . "</th>";
                            }

                            // $table2 .= "<th style='background-color: ".$this->getColor($index_color).";font-weight:bold;'>".$value."</th>";
                        }

                        // echo $value.", ";
                    }

                    if ($this->getAverageLine()) {
                        $table2 .= "<th></th>";
                    }

                    $table2 .= "\n</tr>\n\n<tr>\n"; // echo $table2;exit("****fim");
                }

                if ($this->getAverageColumnByAll()) {

                    // var_dump($matriz);exit();

                    $sumCol = array();
                    $countRows = 0;

                    foreach ($matriz as $keyGroup => $grupos) {

                        foreach ($grupos as $keyRow => $rows) {

                            foreach ($rows as $keyCols => $cols) {

                                if (isset($sumCol[$keyCols]))
                                    $sumCol[$keyCols] += floatval($matriz[$keyGroup][$keyRow][$keyCols][0]);
                                else
                                    $sumCol[$keyCols] = floatval($matriz[$keyGroup][$keyRow][$keyCols][0]);
                            }
                            $countRows ++;
                        }
                    }

                    foreach ($sumCol as $key => $value) {

                        $sumCol[$key] = $this->number_format_precision(($value / $countRows), $this->getPrecision());
                    }

                    $order = $this->classifiedColors($sumCol, $this->getColors());

                    // $matriz = null;

                    $table2 .= "<tr style='border-top:2px solid #000000'><th><b>Average All</b></th>";
                    // exit($table2);
                    foreach ($sumCol as $key => $value) {

                        $index_color = null;

                        foreach ($order as $key => $item) {

                            if ($value == $item) {
                                $index_color = $key;
                                break;
                            }
                        }

                        if (is_null($index_color)) {

                            $table2 .= "<th>" . $value . "</th>";
                        } else {

                            if ($this->getGrayScalel() == true) {

                                $table2 .= "<th style='color:#000000;font-weight:bold;background-image: url(\"" . $this->getDirTmpl() . "images/" . $this->getImageBg($index_color) . "\");'>" . $value . "</th>";
                            } else {

                                $table2 .= "<th style='color:#000000;font-weight:bold;background-color: " . $this->getColor($index_color) . ";'>" . $value . "</th>";
                            }

                            // $table2 .= "<th style='background-color: ".$this->getColor($index_color).";font-weight:bold;'>".$value."</th>";
                        }
                    }

                    if ($this->getAverageLine()) {
                        $table2 .= "<th></th>";
                    }

                    $table2 .= "</tr>"; // echo $table2;exit("****fim");
                }
            }

            $table2 .= "</tbody>";

            if ($this->getTableName() == "") {
                $result = "" . "<table class=\"excel_data\">" . $table2 . "</table>";
            } else {

                $result .= "
						
						
						
						
						
						<div style='float:left;display:block; width:100%;    align-items: center;justify-content: center;'>
						
							<div class='rotulo' style=\"width:auto; height: 100%;float:left;\">
						
						

        " . $this->getTableName() . "


						
				
							</div>
					
							<div style=\"float:right\">
								<table class=\"excel_data\">" . $table2 . "</table>	
							</div>
												
						</div>
						";

                /*
                 *
                 * $result =""
                 * ."<div style=\"display:block;width:auto;float:left;\"><nav class='rotulo'>".$this->getTableName()."</nav>"
                 * ."<table class=\"excel_data\">"
                 * .$table2
                 * ."</table></div>"
                 * ;
                 *
                 */
            }

            /*
             * $result ="<div class=\"divTable\" style=\"width: auto; border: 1px solid #000;\">
             * <div class=\"divTableBody\">
             * <div class=\"divTableRow\">
             * <div class=\"divTableCell\" style='width:auto;max-width:150px;height:200px;'>"
             * ."<p class='rotulo'>teste xcvxc wert</p>"
             * ."</div>
             * <div class=\"divTableCell\">"
             * ."<table class=\"excel_data\">"
             * .$table2
             * ."</table>"
             * ."</div>
             * </div>
             * </div>
             * </div>";
             */

            /*
             * $result = "<div id=\"container_table\"><div id=\"container_table_row\">"//<div style='float:left;display:block;'><div class='vertical-text'>teste xcvxc wert</div>
             * ."<div id='container_table_left' style='width:auto;height:50px;'>"."<div class='vertical-text'>teste xcvxc wert</div>"."</div>"
             * ."<div id='container_table_left'><table class=\"excel_data\">
             * ".$table2."
             * </table></div>";
             *
             * $result .= "</div></div>";
             */
        }

        return $result;
    }
}

