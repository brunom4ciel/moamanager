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

define("AGRAWALGENERATOR", "AgrawalGenerator");
define("SINEGENERATOR", "SineGenerator");
define("MIXEDGENERATOR", "MixedGenerator");
define("STAGGERGENERATOR", "STAGGERGenerator");

class GeneratorDatasets
{

    public function getFixedValue($value = "", $default = "", $prefix = "", $sufix = "")
    {
        $result = "";
        try {

            if ($value == "")
                $result = $default;
            else
                $result = $prefix . $value . $sufix;
        } catch (AppException $e) {
            throw new AppException($e->getMessage());
        }

        return $result;
    }

    function getFixedVarValue($varname = "", $default = "", $prefix = "", $sufix = "")
    {
        $result = "";

        if (! isset($_POST[$varname]))
            $result = $default;
        else if (is_numeric($_POST[$varname]))
            $result = $prefix . $_POST[$varname] . $sufix;

        return $result;
    }

    private function compareToSet($variable, $compare = array(""), $default)
    {
        $result = "";

        if (isset($variable)) {
            if (in_array($variable, $compare))
                $result = $default;
            else
                $result = $variable;
        } else {
            $result = $default;
        }

        return $result;
    }

    private function generateValuesDriftsPositions($instance_limit, $drift_length, $sorter = "asc")
    {
        $result = array();
        $total = 0;
        $divisao = ceil($instance_limit / ($drift_length + 1));

        for ($i = 0; $i <= $drift_length; $i ++) {
            $total += $divisao;
            array_push($result, $total);
        }

        if ($sorter == "asc")
            asort($result);
        else
            arsort($result);

        $result2 = array();

        foreach ($result as $key => $item) {
            array_push($result2, $item);
        }

        return $result2;
    }

    public function WaveformGeneratorDrift($instance_limit, $drift_length, $drift_width = 0, $drift_position = 0, $numberAttributesDrift = 7, $instanceRandomSeed = 1, $addNoise = "")
    {
        $result = "";

        $ConceptDriftStream = "";
        $generators = "";
        $f = 2;
        $divisao = ceil($instance_limit / ($drift_length + 1));
        $posicao_atual = 0;
        $database = "WaveformGeneratorDrift";
        $d = $numberAttributesDrift;
        $z = $instanceRandomSeed;
        $n = $addNoise;

        $d = $this->generateValuesDriftsPositions(40, $drift_length);
        $p = $this->generateValuesDriftsPositions($instance_limit, $drift_length);

        for ($i = 1; $i <= $drift_length; $i ++) {

            $ConceptDriftStream .= "-s (ConceptDriftStream ";

            if ($drift_position > 0)
                $posicao_atual += $drift_position;
            else
                $posicao_atual += $divisao;

            if ($i == 1) {

                $generators .= " (generators.$database -d " . $d[($i - 1)] . " -i $z $n) -d ";
                // $d=1;
                $generators .= " (generators.$database -d " . $d[($i)] . ") -p " . $p[($i - 1)] . " -w $drift_width) -d";
                // $d++;
            } else {

                $generators .= " (generators.$database -d " . $d[($i)] . ") -p " . $p[($i - 1)] . " -w $drift_width) -d";
                // $d++;
            }

            // if($d>40)
            // $d = $numberAttributesDrift;
        }

        $result = $ConceptDriftStream . " -s " . substr($generators, 0, count($generators) - 4) . "";

        return $result;
    }

    /*
     * public function WaveformGeneratorDrift($instance_limit, $drift_length, $drift_width=0, $drift_position=0){
     *
     * $result = "";
     *
     * $ConceptDriftStream = "";
     * $generators="";
     * $f = 2;
     * $divisao = ceil($instance_limit/($drift_length+1));
     * $posicao_atual=0;
     * $database = "WaveformGeneratorDrift";
     *
     * $values = $this->generateValuesDriftsPositions($instance_limit, $drift_length, "asc");
     *
     * //var_dump($values);
     *
     * for($i=1; $i<=$drift_length;$i++){
     *
     * $ConceptDriftStream .= "-s (ConceptDriftStream ";
     *
     * if($drift_position>0)
     * $posicao_atual += $drift_position;
     * else
     * $posicao_atual = $values[$i-1];//$posicao_atual += $divisao;
     *
     * if($i==1){
     * $generators .= " generators.$database -d";
     * }
     *
     * $generators .= " generators.$database -p $posicao_atual -w $drift_width) -d";
     *
     * }
     *
     * $result = $ConceptDriftStream." -s ".substr($generators,0, count($generators)-4)."";
     *
     * return $result;
     * }
     */
    public function LEDGeneratorDrift($instance_limit, $drift_length, $drift_width = 0, $drift_position = 0, $numberAttributesDrift = 7, $instanceRandomSeed = 1, $noisePercentage = 10)
    {
        $result = "";

        $ConceptDriftStream = "";
        $generators = "";
        $f = 2;
        $divisao = ceil($instance_limit / ($drift_length + 1));
        $posicao_atual = 0;
        $database = "LEDGeneratorDrift";
        $d = $numberAttributesDrift;
        $z = $instanceRandomSeed;
        $n = $noisePercentage;

        for ($i = 1; $i <= $drift_length; $i ++) {

            $ConceptDriftStream .= "-s (ConceptDriftStream ";

            if ($drift_position > 0)
                $posicao_atual += $drift_position;
            else
                $posicao_atual += $divisao;

            if ($i == 1) {

                $generators .= " (generators.$database -d $d -i $z -n $noisePercentage) -d ";
                $d = 1;
                $generators .= " (generators.$database -d $d) -p $posicao_atual -w $drift_width) -d";
                $d ++;
            } else {

                $generators .= " (generators.$database -d $d) -p $posicao_atual -w $drift_width) -d";
                $d ++;
            }

            if ($d > $numberAttributesDrift)
                $d = 1;
        }

        $result = $ConceptDriftStream . " -s " . substr($generators, 0, count($generators) - 4) . "";

        return $result;
    }

    /*
     * public function LEDGeneratorDrift($instance_limit, $drift_length, $drift_width=0, $drift_position=0){
     *
     * $result = "";
     *
     * $ConceptDriftStream = "";
     * $generators="";
     * $f = 2;
     * $divisao = ceil($instance_limit/($drift_length+1));
     * $posicao_atual=0;
     * $database = "LEDGeneratorDrift";
     *
     *
     * for($i=1; $i<=$drift_length;$i++){
     *
     * $ConceptDriftStream .= "-s (ConceptDriftStream ";
     *
     * if($drift_position>0)
     * $posicao_atual += $drift_position;
     * else
     * $posicao_atual += $divisao;
     *
     * $generators .= " generators.$database -p $posicao_atual -w $drift_width) -d";
     *
     * }
     *
     * $result = $ConceptDriftStream." -s ".substr($generators,0, count($generators)-4)."";
     *
     * return $result;
     * }
     */
    public function RandomRBFGeneratorDrift($instance_limit, $drift_length, $drift_width = 0, $drift_position = 0, $numClasses = 2, $modelRandomSeed = 1, $instanceRandomSeed = 1, $numAtts = 10, $numCentroids = 10, $numDriftCentroids = 50, $speedChange = 0)
    {

        /*
         * default MOA
         *
         * speedChange -s 0
         * numDriftCentroids -k 50
         * modelRandomSeed -r 1
         * instanceRandomSeed -i 1
         * numClasses -c 2
         * numAtts -a 10
         * numCentroids -n 50
         *
         */
        $result = "";

        $ConceptDriftStream = "";
        $generators = "";
        $f = 2;
        $divisao = ceil($instance_limit / ($drift_length + 1));
        $posicao_atual = 0;
        $database = "RandomRBFGeneratorDrift";

        // 3.4028234663852886E38 -k 30 -c 6 -a 40 -n 50)
        // -d (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 35 -c 6 -a 40 -n 50) -p 2000 -w 1)
        // -d (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 40 -c 6 -a 40 -n 50) -p 4000 -w 1)
        // -d (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 45 -c 6 -a 40 -n 50) -p 6000 -w 1)
        // -d (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 50 -c 6 -a 40 -n 50) -p 8000 -w 1) -r 40 -c -i 10000 -f 10 -q 10

        $s = $this->compareToSet($speedChange, array(
            ""
        ), "3.4028234663852886E38");
        $k = $this->compareToSet($numDriftCentroids, array(
            ""
        ), "50");
        $r = $this->compareToSet($modelRandomSeed, array(
            ""
        ), "1");
        $i = $this->compareToSet($instanceRandomSeed, array(
            ""
        ), "1");
        $a = $this->compareToSet($numAtts, array(
            ""
        ), "40");
        $c = $this->compareToSet($numClasses, array(
            ""
        ), "6");
        $n = $this->compareToSet($numCentroids, array(
            ""
        ), "50");

        // $s = ($speedChange==""?"3.4028234663852886E38":$speedChange);
        // $k = ($numCentroids == "" ? 30 : $numCentroids);
        // $n = ($numDriftCentroids == "" ? 50 : $numDriftCentroids);
        // $c = ($numClasses==""?40:$numClasses);
        // $a = ($numAtts==""?6:$numAtts);
        // $r = ($modelRandomSeed==""?1:$modelRandomSeed);
        // $z = ($instanceRandomSeed==""?1:$instanceRandomSeed);
        //

        /*
         *
         * EvaluateInterleavedTestThenTrain2 -l (drift.SingleClassifierDrift -d HDDM_A_Test)
         * -s (ConceptDriftStream -s (ConceptDriftStream -s (ConceptDriftStream -s (ConceptDriftStream -s
         * (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 30 -c 6 -a 40 -n 50)
         * -d (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 35 -c 6 -a 40 -n 50) -p 2000 -w 1)
         * -d (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 40 -c 6 -a 40 -n 50) -p 4000 -w 1)
         * -d (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 45 -c 6 -a 40 -n 50) -p 6000 -w 1)
         * -d (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 50 -c 6 -a 40 -n 50) -p 8000 -w 1) -r 40 -c -i 10000 -f 10 -q 10
         *
         * EvaluateInterleavedTestThenTrain2 -l (drift.SingleClassifierDrift -d (DDM -n 30 -w 2 -o 3))
         * -s (ConceptDriftStream -s (ConceptDriftStream -s (ConceptDriftStream -s (ConceptDriftStream -s
         * (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 50 -r 1 -i 1 -c 6 -a 40 -n 1)
         * -d (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 51 -c 6 -a 40 -n 1) -p 2000 -w 1)
         * -d (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 51 -c 6 -a 40 -n 1) -p 4000 -w 1)
         * -d (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 52 -c 6 -a 40 -n 1) -p 6000 -w 1)
         * -d (generators.RandomRBFGeneratorDrift -s 3.4028234663852886E38 -k 53 -c 6 -a 40 -n 1) -p 8000 -w 1) -r 40 -c -i 10000 -f 10 -q 10
         *
         */
        // $k_parts = array();

        // if(floor($k/($drift_length))>=5){
        // $k_parts = array(50,45,40,35,30,25,20,15,10,5);
        // }else{
        // $k_value = floor($k/($drift_length));
        //
        // $value_sum=0;
        // for($i=0; $i < $drift_length; $i++){
        //
        // if($i==0){
        // //$k_parts[] = $k;
        // }else{
        //
        // $value_sum += $k_value;
        // }
        //
        // $k_parts[] = intval($k - $value_sum);
        // }
        // }

        $k_parts = array();
        $k_sum = 5;

        for ($i = 1; $i <= $drift_length + 1; $i ++) {

            $k_parts[] = $k_sum;

            $k_sum += 5;
            $k_parts[] = $k_sum;

            if ($k_sum == 50)
                $k_sum = 5;
            else
                $k_sum += 5;
        }

        // var_dump($k_parts);
        // exit();

        for ($y = 1; $y <= $drift_length; $y ++) {

            $ConceptDriftStream .= "-s (ConceptDriftStream ";

            $k = $k_parts[$y];

            // echo $drift_length-$y." = ".$k."\n";

            if ($drift_position > 0)
                $posicao_atual += $drift_position;
            else
                $posicao_atual += $divisao;

            if ($y == 1) {

                $k_excecao = $k_parts[0];

                $generators .= " (generators.$database -s $s -k $k_excecao -r $r -i $i -c $c -a $a -n $n) -d ";
                // $f++;
                // $k++;
                $generators .= " (generators.$database -s $s -k $k -c $c -a $a -n $n) -p $posicao_atual -w $drift_width) -d";
                // $f=1;
            } else {

                $generators .= " (generators.$database -s $s -k $k -c $c -a $a -n $n) -p $posicao_atual -w $drift_width) -d";
                // $f++;
                // $k++;
            }

            // if($f>3)
            // $f = 1;

            // if($numDriftCentroids>$numDriftCentroids)
            // $k = $numDriftCentroids;
        }

        $result = $ConceptDriftStream . " -s " . substr($generators, 0, count($generators) - 4) . "";

        return $result;
    }

    // $instance_limit, $drift_length, $drift_width=0, $drift_position=0){
    public function SineGenerator($instance_limit, $drift_length, $drift_width = 0, $drift_position = 0, $instanceRandomSeed = 1, $function = 1, $supperssirrelevantAttributes = "", $balanceClasses = "")
    {

        /*
         * default MOA
         *
         * instanceRandomSeed -i 1
         * function -f 1 (max =4)
         * supperssirrelevantAttributes -s (não marcado)
         * balanceClasses -b (não marcado)
         *
         */
        $result = "";

        $ConceptDriftStream = "";
        $generators = "";
        $f = 1;
        $divisao = ceil($instance_limit / ($drift_length + 1));
        $posicao_atual = 0;
        $database = SINEGENERATOR;

        $instanceRandomSeed = $this->compareToSet($instanceRandomSeed, array(
            ""
        ), "1");

        $i = $this->getFixedValue($instanceRandomSeed, "1", " -i ");
        // $f = $this->getFixedValue($function, "1", " -f ");
        $s = $this->getFixedValue($supperssirrelevantAttributes, "", " -s ");
        $b = $this->getFixedValue($balanceClasses, "", " -b ");

        // $i = $this->compareToSet($instanceRandomSeed, array(""), "1");
        // $f = $this->compareToSet($function, array(""), "1");
        // $s = $this->compareToSet($supperssirrelevantAttributes, array(""), "");
        // $b = $this->compareToSet($balanceClasses, array(""), "");

        // $s = ($speedChange==""?"3.4028234663852886E38":$speedChange);
        // $k = ($numCentroids == "" ? 30 : $numCentroids);
        // $n = ($numDriftCentroids == "" ? 50 : $numDriftCentroids);
        // $c = ($numClasses==""?40:$numClasses);
        // $a = ($numAtts==""?6:$numAtts);
        // $r = ($modelRandomSeed==""?1:$modelRandomSeed);
        // $z = ($instanceRandomSeed==""?1:$instanceRandomSeed);
        //

        for ($y = 1; $y <= $drift_length; $y ++) {

            $ConceptDriftStream .= "-s (ConceptDriftStream ";

            if ($drift_position > 0)
                $posicao_atual += $drift_position;
            else
                $posicao_atual += $divisao;

            if ($y == 1) {

                $generators .= " (generators.$database -f $f" . $i . $s . $b . ") -d ";
                $f ++;
                // $k++;
                $generators .= " (generators.$database -f $f" . $i . $s . $b . ") -p $posicao_atual -w $drift_width) -d";
                $f = 2;
            } else {

                $generators .= " (generators.$database -f $f" . $i . $s . $b . ") -p $posicao_atual -w $drift_width) -d";

                // $k++;
            }

            if ($f > 3)
                $f = 0;

            $f ++;

            // if($numDriftCentroids>$numDriftCentroids)
            // $k = $numDriftCentroids;
        }

        $result = $ConceptDriftStream . " -s " . substr($generators, 0, count($generators) - 4) . "";

        return $result;
    }

    public function AgrawalGenerator($instance_limit, $drift_length, $drift_width = 0, $drift_position = 0, $instanceRandomSeed = "1", $function = "1", $peturbFraction = "0.05", $balanceClasses = "")
    {

        /*
         * default MOA
         *
         * function -f 1
         * instanceRandomSeed -i 1
         * peturbFraction -p 0.05
         * balanceClasses -b (não marcado)
         *
         */
        $result = "";

        $ConceptDriftStream = "";
        $generators = "";

        $divisao = ceil($instance_limit / ($drift_length + 1));
        $posicao_atual = 0;
        $database = AGRAWALGENERATOR;

        $instanceRandomSeed = $this->compareToSet($instanceRandomSeed, array(
            ""
        ), "1");

        $i = $this->getFixedValue($instanceRandomSeed, "1", " -i ");
        // $f = $this->getFixedValue($function, "1", " -f ");
        $s = $this->getFixedValue($peturbFraction, "0.05", " -p ");
        $b = $this->getFixedValue($balanceClasses, "", " -b ");

        //
        // exit($drift_length);

        if ($drift_length + 1 > 10) {

            $qtd = ceil(($drift_length + 1) / 10);

            // $qtd = floor($qtd_lajer)+1;

            $f = (10 * $qtd) - ($drift_length);

            if ($f == 0)
                $f = 1;

            // exit("qtd= ".$qtd.", f= ".$f.", drift_length= ".$drift_length);

            // $f = ($drift_length+1)-10;
        } else {
            $f = 10 - ($drift_length);
        }

        for ($y = 1; $y <= $drift_length; $y ++) {

            $ConceptDriftStream .= "-s (ConceptDriftStream ";

            if ($drift_position > 0)
                $posicao_atual += $drift_position;
            else
                $posicao_atual += $divisao;

            if ($y == 1) {

                $generators .= " (generators.$database -f " . $f . $i . $s . $b . ") -d ";
                $f ++;
                // $k++;
                if ($f > 10)
                    $f = 1;

                $generators .= " (generators.$database -f " . $f . $i . $s . $b . ") -p $posicao_atual -w $drift_width) -d";
                // $f=2;
            } else {

                $generators .= " (generators.$database -f " . $f . $i . $s . $b . ") -p $posicao_atual -w $drift_width) -d";

                // $k++;
            }

            if ($f > 9)
                $f = 0;

            $f ++;

            // if($numDriftCentroids>$numDriftCentroids)
            // $k = $numDriftCentroids;
        }

        $result = $ConceptDriftStream . " -s " . substr($generators, 0, count($generators) - 4) . "";

        return $result;
    }

    public function MixedGenerator($instance_limit, $drift_length, $drift_width = 0, $drift_position = 0)
    {
        return $this->Generator(MIXEDGENERATOR, $instance_limit, $drift_length, $drift_width, $drift_position, 2);
    }

    // public function AgrawalGenerator($instance_limit, $drift_length, $drift_width=0, $drift_position=0){
    //
    // return $this->Generator(AGRAWALGENERATOR,
    // $instance_limit,
    // $drift_length,
    // $drift_width,
    // $drift_position);
    // }

    // public function SineGenerator($instance_limit, $drift_length, $drift_width=0, $drift_position=0){
    //
    // return $this->Generator(SINEGENERATOR,
    // $instance_limit,
    // $drift_length,
    // $drift_width,
    // $drift_position);
    // }
    public function STAGGERGenerator($instance_limit, $drift_length, $drift_width = 0, $drift_position = 0)
    {
        return $this->Generator(STAGGERGENERATOR, $instance_limit, $drift_length, $drift_width, $drift_position);
    }

    private function Generator($database = "", $instance_limit, $drift_length, $drift_width = 0, $drift_position = 0, $max_f = 3)
    {
        $result = "";

        $ConceptDriftStream = "";
        $generators = "";
        $f = 2;
        $divisao = ceil($instance_limit / ($drift_length + 1));
        $posicao_atual = 0;

        for ($i = 1; $i <= $drift_length; $i ++) {

            $ConceptDriftStream .= "-s (ConceptDriftStream ";

            if ($drift_position > 0)
                $posicao_atual += $drift_position;
            else
                $posicao_atual += $divisao;

            if ($i == 1) {

                $generators .= " (generators.$database -f $f) -d ";

                if ($f >= $max_f)
                    $f = 1;
                else
                    $f ++;

                $generators .= " (generators.$database -f $f) -p $posicao_atual -w $drift_width) -d";
                // $f=1;

                if ($f == 1)
                    $f = $max_f;
                else
                    $f = 1;
            } else {

                $generators .= " (generators.$database -f $f) -p $posicao_atual -w $drift_width) -d";
                $f ++;
            }

            if ($f > $max_f)
                $f = 1;
        }

        $result = $ConceptDriftStream . " -s " . substr($generators, 0, count($generators) - 4); // ." ";

        return $result;
    }

    /*
     * private function Generator($database="",$instance_limit, $drift_length, $drift_width=0, $drift_position=0){
     *
     * $result = "";
     *
     * $ConceptDriftStream = "";
     * $generators="";
     * $f = 2;
     * $divisao = ceil($instance_limit/($drift_length+1));
     * $posicao_atual=0;
     *
     *
     *
     * for($i=1; $i<=$drift_length;$i++){
     *
     * $ConceptDriftStream .= " -s (ConceptDriftStream ";
     *
     * if($i==1){
     *
     * $generators .= " (generators.$database -f $f) -d ";
     *
     * $f++;
     *
     * if($drift_length>1)
     * if($drift_position>0){
     *
     * //$generators .= " (generators.STAGGERGenerator -f $f) -p $posicao_atual -w $drift_width) -d ";
     * $posicao_atual = $drift_position;
     * }
     * else{
     *
     * $posicao_atual = $divisao;
     * }
     *
     * $generators .= " (generators.$database -f $f) -p $posicao_atual -w $drift_width) -d ";
     *
     * $f=1;
     *
     * }else{
     *
     * if($drift_position>0){
     *
     * $posicao_atual += $drift_position;
     * //$generators .= " (generators.STAGGERGenerator -f $f) -p $posicao_atual -w $drift_width) -d ";
     * }else{
     *
     * $posicao_atual += $divisao;
     *
     * }
     *
     * $generators .= " (generators.$database -f $f) -p $posicao_atual -w $drift_width) -d ";
     *
     * $f++;
     * }
     *
     * if($f>3)
     * $f = 1;
     *
     * }
     *
     * $result = $ConceptDriftStream." -s ".substr($generators,0, count($generators)-6)." ";
     *
     * return $result;
     * }
     *
     */
}


