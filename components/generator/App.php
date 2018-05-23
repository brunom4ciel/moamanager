<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\generator;

use moam\core\AppException;
defined('_EXEC') or die();

/**
 * Gerencia a execução da aplicação
 *
 * @author Bruno Maciel <brunom4ciel@gmail.com>
 *        
 */
abstract class App
{

    protected static $_values = array();

    protected static $arrayLearnersMethods = array();

    protected static $arrayDetectorsParameters = array();

    protected static $parameters = array();

    protected static $result_scripts = array();

    protected static $prefix = "objbm";

    public static function setAttribute($varName, $val)
    {
        self::$_values[$varName] = $val;
    }

    public static function getAttribute($varName)
    {
        try {

            if (! isset(self::$_values[$varName]))
                self::setAttribute($varName, NULL); // throw new Exception( 'Atributo nao existe '.': '.$varName );
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
        // catch (Exception $e) {
        // echo "#Erro Fatal ('{$e->getMessage()}')\n{$e}\n";
        // }

        return self::$_values[$varName];
    }


    public static function getFixedVarValue($varname = "", $default = "", $prefix = "", $sufix = "")
    {
        $result = "";
        try {

            /*
             * $valueVar = self::getAttribute($varname);
             *
             * if(is_null($valueVar))
             * $result = $default;
             * else
             * $result = $prefix.$valueVar.$sufix;
             */

            if (! isset($_POST[$varname]))
                $result = $default;
            else
                $result = $prefix . $_POST[$varname] . $sufix;
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $result;
    }

    function getFixedVarValueNumber($varname = "", $default = "", $prefix = "", $sufix = "")
    {
        $result = "";
        try {

            /*
             * $valueVar = self::getAttribute($varname);
             *
             * if(is_null($valueVar))
             * $result = $default;
             * else
             * if(is_numeric($valueVar))
             * $result = $prefix.$valueVar.$sufix;
             */

            if (! isset($_POST[$varname]))
                $result = $default;
            else if (is_numeric($_POST[$varname]))
                $result = $prefix . $_POST[$varname] . $sufix;
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $result;
    }

    public static function get_POST($varname = "", $default = "")
    {
        $result = "";

        try {

            if (! isset($_POST[$varname]))
                $result = $default;
            else
                $result = $_POST[$varname];
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $result;
    }

    public static function getScripts()
    {
        $json = "";

        try {

            $json = self::toJSON(self::$result_scripts);
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $json;
    }

    private static function toJSON($result_script)
    {
        $json = "";

        try {

            $json = json_encode($result_script);
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $json;
    }

    /**
     * atribui valores as variáveis
     */
    public static function getParameters_POST()
    {
        try {

            if (! empty($_POST[self::$prefix . "task"])) {

                foreach ($_POST as $key => $value)
                    if (is_array($value))
                        foreach ($value as $key2 => $item)
                            $_POST[strtolower($key)][strtolower($key2)] = base64_decode($item);
                    else {
                        // $$key = base64_decode($value);
                        $_POST[strtolower($key)] = base64_decode($value);
                    }

                self::setAttribute('task', self::get_POST(self::$prefix . "task"));
                self::setAttribute('learn', self::get_POST(self::$prefix . "learn"));
                self::setAttribute('driftdetect', self::get_POST(self::$prefix . "driftdetect"));
                self::setAttribute('dataset', self::get_POST(self::$prefix . "dataset"));

                self::setAttribute('instance_limit', self::get_POST("instance_limit"));
                self::setAttribute('sample_frequency', self::get_POST("sample_frequency"));
                self::setAttribute('mem_check_frequency', self::get_POST("mem_check_frequency"));
                self::setAttribute('drift_position', "");

                self::setAttribute('drift_length', self::get_POST("drift_length"));
                self::setAttribute('drift_width', self::get_POST("drift_width"));

                self::setAttribute('repetition', self::get_POST("repetition"));
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

    private static function loadSettings()
    {
        $arrayLearnersMethodsParameters = array(
            "DDM" => array(
                array(
                    "name" => "n",
                    "default" => "30",
                    "label" => "minNumInstances",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "w",
                    "default" => "2",
                    "label" => "warningLevel",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "o",
                    "default" => "3",
                    "label" => "outControlLevel",
                    "type" => "text",
                    "list" => array()
                )
            ),
            "EDDM" => array(
                array(
                    "name" => "n",
                    "default" => "30",
                    "label" => "minNumInstances",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "w",
                    "default" => "0.95",
                    "label" => "warningLevel",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "o",
                    "default" => "0.9",
                    "label" => "outControlLevel",
                    "type" => "text",
                    "list" => array()
                )
            ),
            "STEPD" => array(
                array(
                    "name" => "r",
                    "default" => "30",
                    "label" => "W",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "w",
                    "default" => "0.003",
                    "label" => "AlphaD",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "o",
                    "default" => "0.05",
                    "label" => "AlphaW",
                    "type" => "text",
                    "list" => array()
                )
            ),
            "STEPD_New" => array(
                array(
                    "name" => "r",
                    "default" => "30",
                    "label" => "W",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "w",
                    "default" => "0.003",
                    "label" => "AlphaD",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "o",
                    "default" => "0.05",
                    "label" => "AlphaW",
                    "type" => "text",
                    "list" => array()
                )
            ),
            "FSDD" => array(
                array(
                    "name" => "r",
                    "default" => "30",
                    "label" => "W",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "w",
                    "default" => "0.003",
                    "label" => "AlphaD",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "o",
                    "default" => "0.05",
                    "label" => "AlphaW",
                    "type" => "text",
                    "list" => array()
                )
            ),
            "EADD" => array(
                array(
                    "name" => "r",
                    "default" => "30",
                    "label" => "W",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "w",
                    "default" => "0.003",
                    "label" => "AlphaD",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "o",
                    "default" => "0.05",
                    "label" => "AlphaW",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "f",
                    "default" => "0.1",
                    "label" => "Alpha Variance",
                    "type" => "text",
                    "list" => array()
                )
            ),
            "ADWINChangeDetector" => array(
                array(
                    "name" => "a",
                    "default" => "0.002",
                    "label" => "DeltaAdwin",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "m",
                    "default" => "32",
                    "label" => "mintClock",
                    "type" => "text",
                    "list" => array()
                )
            ),
            "EWMAChartDM" => array(
                array(
                    "name" => "n",
                    "default" => "30",
                    "label" => "minNumInstances",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "l",
                    "default" => "0.02",
                    "label" => "Lambda",
                    "type" => "text",
                    "list" => array()
                )
            ),
            "EnsembleDriftDetectionMethods" => array(
                array(
                    "name" => "n",
                    "default" => "30",
                    "label" => "minNumInstances",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "c",
                    "default" => " ",
                    "label" => "chanceDetectors",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "l",
                    "default" => "0.02",
                    "label" => "prediction",
                    "type" => "list",
                    "list" => array(
                        "max",
                        "min",
                        "majority"
                    )
                )
            ),
            "HDDM_A_Test" => array(
                array(
                    "name" => "d",
                    "default" => "0.001",
                    "label" => "driftConfidence",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "w",
                    "default" => "0.005",
                    "label" => "warningConfidence",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "t",
                    "default" => "One-sided",
                    "label" => "typeOfTest",
                    "type" => "list",
                    "list" => array(
                        "Two-sided",
                        "One-sided"
                    )
                )
            ),
            "HDDM_W_Test" => array(
                array(
                    "name" => "d",
                    "default" => "0.001",
                    "label" => "driftConfidence",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "w",
                    "default" => "0.005",
                    "label" => "warningConfidence",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "m",
                    "default" => "0.05",
                    "label" => "Lambda",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "t",
                    "default" => "One-sided",
                    "label" => "typeOfTest",
                    "type" => "list",
                    "list" => array(
                        "One-sided",
                        "Two-sided"
                    )
                )
            ),

            "PageHinkleyDM" => array(
                array(
                    "name" => "n",
                    "default" => "30",
                    "label" => "minNumInstances",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "d",
                    "default" => "0.005",
                    "label" => "delta",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "l",
                    "default" => "0",
                    "label" => "Lambda",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "a",
                    "default" => "1",
                    "label" => "alpha.",
                    "type" => "text",
                    "list" => array()
                )
            ),

            "SeqDrift1ChangeDetector" => array(

                array(
                    "name" => "d",
                    "default" => "0.01",
                    "label" => "deltaSeqDrift1",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "w",
                    "default" => "0.1",
                    "label" => "deltaWarningOption",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "b",
                    "default" => "200",
                    "label" => "blockSeqDrift1Option",
                    "type" => "text",
                    "list" => array()
                )
            ),

            "SeqDrift2ChangeDetector" => array(
                array(
                    "name" => "d",
                    "default" => "0.01",
                    "label" => "deltaSeq2Drift",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "b",
                    "default" => "200",
                    "label" => "blockSeqDrift2Option",
                    "type" => "text",
                    "list" => array()
                )
            ),

            "GeometricMovingAverageDM" => array(
                array(
                    "name" => "n",
                    "default" => "30",
                    "label" => "minNumInstances",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "l",
                    "default" => "0",
                    "label" => "lambda",
                    "type" => "text",
                    "list" => array()
                ),

                array(
                    "name" => "a",
                    "default" => "0.99",
                    "label" => "alpha.",
                    "type" => "text",
                    "list" => array()
                )
            )
        );

        $arrayMethods = array();
        // global $arrayLearnersMethodsParameters;

        foreach ($arrayLearnersMethodsParameters as $key => $item) {
            $arrayMethods[] = $key;
        }

        self:
        $arrayLearnersMethods = $arrayMethods;

        // self:$arrayLearnersMethods = array("DDM","EDDM","STEPD","ADWINMethod",
        // "ADWINChangeDetector","EWMAChartDM","EnsembleDriftDetectionMethods",
        // "HDDM_A_Test","HDDM_W_Test","PageHinkleyDM","GeometricMovingAverageDM","SeqDrift1ChangeDetector", "SeqDrift2ChangeDetector");
        //

        $arrayMethodsParameters = array();
        // exit("bruno");
        foreach ($arrayLearnersMethodsParameters as $key => $item) {
            // $arrayMethodsParameters[] = strtolower($key);

            $paramenters = array();
            foreach ($item as $key2 => $item2) {

                foreach ($item2 as $key3 => $item3) {

                    // var_dump($key3);
                    if ($key3 == "name") {
                        array_push($paramenters, $item3);
                    }
                }
            }
            //
            $arrayMethodsParameters[strtolower($key)] = $paramenters;
        }

        self::$arrayDetectorsParameters = $arrayMethodsParameters;

        //
        // $detectors = array("ddm"=>array("n","w","o"),
        // "eddm"=>array("n","w","o"),
        // "stepd"=>array("r","w","m"),
        // "adwinchangedetector"=>array("a","m"),
        // "ewmachartdm"=>array("n","l"),
        // "ensembledriftdetectionmethods"=>array("d","w"),
        // "hddm_a_test"=>array("d","w","t"),
        // "hddm_w_test"=>array("d","w","m","t"),
        // "pagehinkleydm"=>array("n","d","l","a"),
        // "geometricmovingaveragedm"=>array("n","l","a"),
        // "SeqDrift1ChangeDetector"=>array("d","w","b"),
        // "SeqDrift2ChangeDetector"=>array("d","b"));
    }

    private static function getlistOrder($keyname)
    {
        $list = array();

        foreach ($_POST as $key => $value) {
            if (is_array($value))
                foreach ($value as $key2 => $item) {

                    // echo substr($key, 0, strlen(self::$prefix.$keyname)) ."==".self::$prefix.$keyname."\n";
                    if (substr($key, 0, strlen(self::$prefix . $keyname)) == self::$prefix . $keyname) {

                        if (substr($key, strlen($key) - 5) == "order") {

                            $method = substr($key, strpos($key, "_") + 1);
                            $method = substr($method, 0, strrpos($method, "_"));
                            $method = strtolower($method);

                            $append_ok = true;

                            foreach ($list as $key_sub => $value_sub) {

                                // echo $list[$key_sub]["method"];
                                if ($list[$key_sub]["method"] == $method) {

                                    $append_ok = false;
                                }
                            }

                            if ($append_ok == false) {} else {

                                if ($_POST[strtolower($key)][strtolower($key2)] != "") {

                                    if (is_numeric($_POST[strtolower($key)][strtolower($key2)])) {

                                        array_push($list, array(
                                            "method" => $method,
                                            "order" => $_POST[strtolower($key)][strtolower($key2)]
                                        ));
                                    }
                                }
                            }

                            //
                            // echo $key."=".$_POST[strtolower($key)][strtolower($key2)]."\n";// = base64_decode($item);
                        }
                    }
                }

            else {
                // $$key = base64_decode($value);
                // $_POST[strtolower($key)] = base64_decode($value);
            }
            // objbmlearn
        }

        $aux = array();
        $min = 0;
        $sizeOfList = count($list);
        $order = 0;

        for ($i = 0; $i < $sizeOfList; $i ++) {

            $min = 999;
            foreach ($list as $key => $item) {

                foreach ($item as $key2 => $item2) {
                    if ($min == 999) {
                        if ($key2 == "order") {
                            $order = $key;
                            $min = $item2;
                        }
                    } else {
                        if ($key2 == "order")
                            if ($min > $item2) {
                                $order = $key;
                                $min = $item2;
                            }
                    }
                }
            }
            $aux[] = $list[$order];
            unset($list[$order]);
        }

        return $aux;
    }

    /**
     * Inicializa a aplicação
     */
    public static function instance()
    {
        try {

            self::getParameters_POST();

            self::loadSettings();

            if (! empty(self::getAttribute('task'))) {

                $GeneratorDatasets = self::generatorDB(self::getAttribute('instance_limit'), self::getAttribute('drift_length'), self::getAttribute('drift_width'), self::getAttribute('drift_position'), self::getAttribute('dataset'));

                $result_scripts = self::generatorLearn($GeneratorDatasets, self::getAttribute('task'), self::getAttribute('learn'));

                $result_scripts = self::setMOAParameters($result_scripts, self::getAttribute('instance_limit'), self::getAttribute('sample_frequency'), self::getAttribute('mem_check_frequency'), self::getAttribute('repetition'));

                $objResult = array();

                foreach ($result_scripts as $key => $item) {

                    array_push($objResult, array(
                        "dataset" => $item["dataset_name"],
                        "script" => $item["strategy"] . " " . $item["method"] . " " . $item["dataset_script"] . " " . $item["moa_parameters"]
                    ));
                }

                self::$result_scripts = $objResult;
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }
    }

    private static function getParametersDatasets($instance_limit, $drift_length, $drift_width, $drift_position, $dataset)
    {
        $generators = new GeneratorDatasets();
        $dataBaseGenerated = array();

        switch ($dataset) {
            case "AgrawalGenerator":

                $base = $generators->AgrawalGenerator($instance_limit, $drift_length, $drift_width, $drift_position);

                $dataBaseGenerated = array(
                    "dataset_name" => $dataset,
                    "dataset_script" => $base
                );
                break;

            case "MixedGenerator":

                $base = $generators->MixedGenerator($instance_limit, $drift_length, $drift_width, $drift_position);

                $dataBaseGenerated = array(
                    "dataset_name" => $dataset,
                    "dataset_script" => $base
                );

                break;

            case "SineGenerator":

                // $base = $generators->SineGenerator($instance_limit,
                // $drift_length,
                // $drift_width,
                // $drift_position);

                $base = $generators->SineGenerator($instance_limit, $drift_length, $drift_width, $drift_position, getFixedVarValue("sinegenerator" . "_f", "", ""), getFixedVarValue("sinegenerator" . "_f", "", ""), getFixedVarValue("sinegenerator" . "_s", "", ""), getFixedVarValue("sinegenerator" . "_b", "", ""));

                $dataBaseGenerated = array(
                    "dataset_name" => $dataset,
                    "dataset_script" => $base
                );

                break;

            case "RandomRBFGeneratorDrift":

                $base = $generators->RandomRBFGeneratorDrift($instance_limit, $drift_length, $drift_width, $drift_position, getFixedVarValue("randomrbfgeneratordrift" . "_c", "", ""), getFixedVarValue("randomrbfgeneratordrift" . "_k", "", ""), getFixedVarValue("randomrbfgeneratordrift" . "_i", "", ""), getFixedVarValue("randomrbfgeneratordrift" . "_a", "", ""), getFixedVarValue("randomrbfgeneratordrift" . "_n", "", ""), getFixedVarValue("randomrbfgeneratordrift" . "_k", "", ""), getFixedVarValue("randomrbfgeneratordrift" . "_s", "", ""));

                $dataBaseGenerated = array(
                    "dataset_name" => $dataset,
                    "dataset_script" => $base
                );

                break;

            case "LEDGeneratorDrift":

                $base = $generators->LEDGeneratorDrift($instance_limit, $drift_length, $drift_width, $drift_position);

                $dataBaseGenerated = array(
                    "dataset_name" => $dataset,
                    "dataset_script" => $base
                );

                break;

            case "WaveformGeneratorDrift":

                $base = $generators->WaveformGeneratorDrift($instance_limit, $drift_length, $drift_width, $drift_position);

                $dataBaseGenerated = array(
                    "dataset_name" => $dataset,
                    "dataset_script" => $base
                );

                break;

            case "STAGGERGenerator":

                $base = $generators->STAGGERGenerator($instance_limit, $drift_length, $drift_width, $drift_position);

                $dataBaseGenerated = array(
                    "dataset_name" => $dataset,
                    "dataset_script" => $base
                );
                break;
            case "Airlines":

                $base = "-s (ArffFileStream -f /opt/moa/datasets/airlines.arff)";
                $dataBaseGenerated = array(
                    "dataset_name" => $dataset,
                    "dataset_script" => $base
                );
                break;
            case "Covtype":

                $base = "-s (ArffFileStream -f /opt/moa/datasets/covtype.arff)";
                $dataBaseGenerated = array(
                    "dataset_name" => $dataset,
                    "dataset_script" => $base
                );
                break;
            case "Elec":

                $base = "-s (ArffFileStream -f /opt/moa/datasets/elec.arff)";
                $dataBaseGenerated = array(
                    "dataset_name" => $dataset,
                    "dataset_script" => $base
                );
                break;
            case "Poker":

                $base = "-s (ArffFileStream -f /opt/moa/datasets/poker.arff)";
                $dataBaseGenerated = array(
                    "dataset_name" => $dataset,
                    "dataset_script" => $base
                );
                break;
        }

        return $dataBaseGenerated;
    }


    private static function generatorDB($instance_limit, $drift_length, $drift_width, $drift_position, $dataset)
    {
        $generators = new GeneratorDatasets();
        $dataBaseGenerated = array();

        try {

            $base = "";

            $order_datasets = self::getlistOrder("dataset");

            foreach ($order_datasets as $key_methods => $item_element) {
                // echo $order_methods[$key_methods]["method"]."--";
                // for($z=0;$z<count($driftdetect);$z++){
                foreach ($dataset as $key2 => $item2) {
                    // echo "=========";

                    // echo $order_methods[$key_methods]["method"]."==". strtolower($driftdetect[$z]);

                    if ($order_datasets[$key_methods]["method"] == strtolower($dataset[$key2])) {

                        $aux = self::getParametersDatasets($instance_limit, $drift_length, $drift_width, $drift_position, $dataset[$key2]);

                        $dataBaseGenerated[] = $aux;

                        unset($dataset[$key2]);
                        break;
                    }
                }
            }

            foreach ($dataset as $key2 => $item2) {

                // $learner_ = self::getParametersMethodList($dataset[$key2]);

                $aux = self::getParametersDatasets($instance_limit, $drift_length, $drift_width, $drift_position, $dataset[$key2]);

                $dataBaseGenerated[] = $aux;
            }

            //
            //
            // foreach($dataset as $key => $stream){
            //
            // $aux = self::getParametersDatasets($instance_limit,
            // $drift_length,
            // $drift_width,
            // $drift_position,
            // $stream);
            //
            // $dataBaseGenerated[] = $aux;
            // }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $dataBaseGenerated;
    }


    private static function generatorLearn($dataBaseGenerated, $task, $learn)
    {
        try {

            $resultClientQuery = array();

            foreach ($learn as $key => $learner) {

                switch ($learner) {
                    case 'NaiveBayes':

                        $resultClientQuery = self::generatorLearnNaiveBayes($dataBaseGenerated, $task);

                        break;
                    case 'SingleClassifierDrift':

                        $resultClientQuery = self::generatorLearnSingleClassifierDrift($dataBaseGenerated, $task, self::getAttribute('driftdetect'));

                        break;
                    case 'HoeffdingTree':
                        break;
                    case 'CDDE':

                        $resultClientQuery = self::generatorLearnCDDE($dataBaseGenerated, $task, $learner);
                        break;
                    case 'DDE':

                        $resultClientQuery = self::generatorLearnDDE($dataBaseGenerated, $task, $learner);
                        break;
                    case 'PairedLearners':
                        break;
                }
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $resultClientQuery;
    }

    private static function generatorLearnNaiveBayes($dataBaseGenerated, $task)
    {
        $resultClientQuery = array();

        try {

            foreach ($dataBaseGenerated as $key => $item) {

                array_push($resultClientQuery, array(
                    "dataset_name" => $dataBaseGenerated[$key]['dataset_name'],
                    "dataset_script" => $item['dataset_script'],
                    "strategy" => $task,
                    "method" => ""
                ));
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $resultClientQuery;
    }

    private static function getParametersMethod($learner_method, $parameters_list)
    {
        $result = "";

        foreach ($parameters_list as $key => $item) {
            $result .= getFixedVarValue(self::$prefix . strtolower($learner_method) . "_" . $item, "", " -" . $item . " ");
        }

        return trim($result);
    }

    private static function getParametersMethodList($detector)
    {
        $detectors = self::$arrayDetectorsParameters;

        // $detectors = array("ddm"=>array("n","w","o"),
        // "eddm"=>array("n","w","o"),
        // "stepd"=>array("r","w","m"),
        // "adwinchangedetector"=>array("a","m"),
        // "ewmachartdm"=>array("n","l"),
        // "ensembledriftdetectionmethods"=>array("d","w"),
        // "hddm_a_test"=>array("d","w","t"),
        // "hddm_w_test"=>array("d","w","m","t"),
        // "pagehinkleydm"=>array("n","d","l","a"),
        // "geometricmovingaveragedm"=>array("n","l","a"),
        // "SeqDrift1ChangeDetector"=>array("d","w","b"),
        // "SeqDrift2ChangeDetector"=>array("d","b"));
        // $arrayLearnersMethodsParameters
        $list = $detectors[strtolower($detector)];

        $parameters = self::getParametersMethod($detector, $list);

        $result = "-l (drift.SingleClassifierDrift -d (" . $detector . " " . $parameters . "))";

        // var_dump($result);
        return $result;
    }

    private static function generatorLearnSingleClassifierDrift($dataBaseGenerated, $task, $driftdetect)
    {
        $resultClientQuery = array();

        $order_methods = self::getlistOrder("driftdetect");

        try {
            // exit($learner);

            // self::getParametersMethodList("ddm");

            foreach ($order_methods as $key_methods => $item_element) {
                // echo $order_methods[$key_methods]["method"]."--";
                // for($z=0;$z<count($driftdetect);$z++){
                foreach ($driftdetect as $key2 => $item2) {
                    // echo "=========";

                    // echo $order_methods[$key_methods]["method"]."==". strtolower($driftdetect[$z]);

                    if ($order_methods[$key_methods]["method"] == strtolower($driftdetect[$key2])) {

                        $learner_ = self::getParametersMethodList($driftdetect[$key2]);
                        // echo $learner_.", ";

                        foreach ($dataBaseGenerated as $key => $item) {

                            array_push($resultClientQuery, array(
                                "dataset_name" => $dataBaseGenerated[$key]['dataset_name'],
                                "dataset_script" => $item['dataset_script'],
                                "strategy" => $task,
                                "method" => $learner_
                            ));
                        }

                        unset($driftdetect[$key2]);
                        break;
                    }
                }
            }

            foreach ($driftdetect as $key2 => $item2) {

                $learner_ = self::getParametersMethodList($driftdetect[$key2]);

                foreach ($dataBaseGenerated as $key => $item) {

                    array_push($resultClientQuery, array(
                        "dataset_name" => $dataBaseGenerated[$key]['dataset_name'],
                        "dataset_script" => $item['dataset_script'],
                        "strategy" => $task,
                        "method" => $learner_
                    ));
                }
            }

            // var_dump($resultClientQuery);
            // exit("bruno");
            //
            // for($z=0;$z<count($driftdetect);$z++){
            //
            //
            // //$learner_ = "-l (drift.SingleClassifierDrift -d (".$driftdetect[$z]."))";
            //
            // $learner_method = $driftdetect[$z];
            //
            // //echo "==".getFixedVarValue(self::$prefix.strtolower($learner_method)."_n",""," -n ");
            //
            //
            //
            //
            // //echo $learner_; echo count($dataBaseGenerated);
            //
            // foreach($dataBaseGenerated as $key=>$item){
            // array_push($resultClientQuery,
            // array("dataset_name"=>$dataBaseGenerated[$key]['dataset_name'],
            // "dataset_script"=>$item['dataset_script'],
            // "strategy"=>$task,
            // "method"=>$learner_));
            //
            // }

            // }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $resultClientQuery;
    }

    private static function generatorLearnDDE($dataBaseGenerated, $task, $learner)
    {
        $resultClientQuery = array();

        try {
            // var_dump($_POST);exit();
            $method_parameters = array(
                "s",
                "x",
                "y",
                "z"
            );
            $parameters = "";

            foreach ($method_parameters as $key => $item) {
                // echo $_POST[$prefix.$learner."_".$item]."<br>";
                $parameters .= self::getFixedVarValue(self::$prefix . strtolower($learner) . "_" . $item, "", " -" . $item . " ");
            }

            $learner_ = " -l (drift.DDE" . $parameters . ") ";

            foreach ($dataBaseGenerated as $key => $item) {

                array_push($resultClientQuery, array(
                    "dataset_name" => $dataBaseGenerated[$key]['dataset_name'],
                    "dataset_script" => $task . " " . $learner_ . " " . $item['dataset_script']
                ));
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $resultClientQuery;
    }

    private static function generatorLearnCDDE($dataBaseGenerated, $task, $learner)
    {
        $resultClientQuery = array();

        try {

            $method_parameters = array(
                "c",
                "d",
                "e",
                "g",
                "s",
                "x",
                "y",
                "z",
                "q"
            );
            $parameters = "";

            foreach ($method_parameters as $key => $item) {
                // echo $_POST[$prefix.$learner."_".$item]."<br>";
                $parameters .= self::getFixedVarValue(self::$prefix . strtolower($learner) . "_" . $item, "", " -" . $item . " ");
            }

            $learner_ = " -l (drift.CDDE " . $parameters . ") ";

            foreach ($dataBaseGenerated as $key => $item) {

                array_push($resultClientQuery, array(
                    "dataset_name" => $dataBaseGenerated[$key]['dataset_name'],
                    "dataset_script" => $task . " " . $learner_ . " " . $item['dataset_script']
                ));
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $resultClientQuery;
    }

    public static function getFixedValue($varname = "", $default = "", $prefix = "", $sufix = "")
    {
        $result = "";
        try {

            if ($varname == "")
                $result = $default;
            else
                $result = $prefix . $varname . $sufix;
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $result;
    }

    private static function setMOAParameters($scriptsMOA, $instance_limit, $sample_frequency, $mem_check_frequency, $repetition)
    {
        $resultClientQuery = array();

        $real_datasets = array(
            "Airlines",
            "Covtype",
            "Elec",
            "Poker"
        );

        try {

            $r = self::getFixedValue($repetition, "", "-r ", " -c ");
            $i = self::getFixedValue($instance_limit, "", "-i ", " ");
            $f = self::getFixedValue($sample_frequency, "", "-f ", " ");
            $q = self::getFixedValue($mem_check_frequency, "", "-q ", "");

            // ." -i $instance_limit -f $sample_frequency -q $mem_check_frequency";

            $parameters = $r . $i . $f . $q;

            // exit($parameters);

            $r = self::getFixedValue($repetition, "", "-r ", " -c ");
            $i = "-i -1 ";
            $f = self::getFixedValue($sample_frequency, "", "-f ", " ");
            $q = self::getFixedValue($mem_check_frequency, "", "-q ", "");

            $parameters_real_datasets = $r . $i . $f . $q;

            foreach ($scriptsMOA as $key => $item) {

                if (in_array($item['dataset_name'], $real_datasets)) {

                    array_push($resultClientQuery, array(
                        "dataset_name" => $item['dataset_name'],
                        "dataset_script" => $item['dataset_script'],
                        "strategy" => $item['strategy'],
                        "method" => $item['method'],
                        "moa_parameters" => $parameters_real_datasets
                    ));
                } else {

                    array_push($resultClientQuery, array(
                        "dataset_name" => $item['dataset_name'],
                        "dataset_script" => $item['dataset_script'],
                        "strategy" => $item['strategy'],
                        "method" => $item['method'],
                        "moa_parameters" => $parameters
                    ));
                }
            }
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $resultClientQuery;
    }
}

?>
