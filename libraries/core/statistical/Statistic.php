<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\statistical;

defined('_EXEC') or die();

class Statistic
{

    public function moda($S)
    {
        // $soma = 0;
        $mod = 0;
        $cont_ant = 0;

        sort($S);

        for ($i = 0; $i <= count($S) - 1; $i ++) {
            // $soma += $S[$i];
            $mod = $S[$i];
            $cont = 0;

            for ($j = $i + 1; $j <= count($S) - 1; $j ++) {
                if ($mod == $S[$j]) {
                    $cont ++;
                }
            }

            if ($cont > $cont_ant) {
                $moda = $mod;
                $cont_ant = $cont;
            }
        }

        return $moda;
    }

    public function kurtose($S)
    {
        sort($S);

        $contagem = count($S);
        $media = $this->average($S);
        $variancia = $this->variance($S, $media);
        $desv_pad = $this->standard_deviation($variancia);

        // $minimo = $S[0];
        // $maximo = $S[count($S)-1];

        // $amp = $maximo - $minimo;
        // $erro_pad = $desv_pad/sqrt($contagem);

        // $cv = $desv_pad/$media;
        // $assimet = 0;
        $curt = 0;

        for ($p = 0; $p <= count($S) - 1; $p ++) {
            // $assimet += (($S[$p]-$media)/$desv_pad) * (($S[$p]-$media)/$desv_pad) * (($S[$p]-$media)/$desv_pad);
            $curt += (($S[$p] - $media) / $desv_pad) * (($S[$p] - $media) / $desv_pad) * (($S[$p] - $media) / $desv_pad) * (($S[$p] - $media) / $desv_pad);
        }

        // $assimetria = (($contagem*$assimet)/(($contagem-1)*($contagem-2)));
        $result = (($contagem * ($contagem + 1) * $curt) / (($contagem - 1) * ($contagem - 2) * ($contagem - 3))) - ((3 * ($contagem - 1) * ($contagem - 1)) / (($contagem - 2) * ($contagem - 3)));

        return $result;
    }

    public function skewness($S)
    {
        $result = 0;
        /*
         * $q1 = $this->quartile($S, 1);
         * $q2 = $this->quartile($S, 2);
         * $q3 = $this->quartile($S, 3);
         *
         * $result = ($q3 + $q1 - 2 * $q2) / $q3-$q1;
         */

        sort($S);

        // print_r($S);

        // foreach($S as $item){
        // echo $item.", ";
        // }

        $contagem = count($S);
        $media = $this->average($S);
        $variancia = $this->variance($S, $media);
        $desv_pad = $this->standard_deviation($variancia);

        // echo $desv_pad;
        // exit();

        // $minimo = $S[0];
        // $maximo = $S[count($S)-1];

        // $amp = $maximo - $minimo;
        // $erro_pad = $desv_pad/sqrt($contagem);

        // $cv = $desv_pad/$media;
        $assimet = 0;
        // $curt = 0;

        for ($p = 0; $p <= count($S) - 1; $p ++) {
            $assimet += (($S[$p] - $media) / $desv_pad) * (($S[$p] - $media) / $desv_pad) * (($S[$p] - $media) / $desv_pad);
            // $curt += (($S[$p]-$media)/$desv_pad) * (($S[$p]-$media)/$desv_pad) * (($S[$p]-$media)/$desv_pad) * (($S[$p]-$media)/$desv_pad);
        }

        $result = (($contagem * $assimet) / (($contagem - 1) * ($contagem - 2)));
        // $curtose = (($contagem*($contagem+1)*$curt)/(($contagem-1)*($contagem-2)*($contagem-3)))-((3*($contagem-1)*($contagem-1))/(($contagem-2)*($contagem-3)));

        return $result;
    }

    public function quartil_pos($S, $quartil = 2)
    {
        $i = $quartil;
        $n = count($S);

        $Qi = ($i * ($n + 1)) / 4;

        return round($Qi) - 1;
    }

    public function median($S)
    {
        $median = 0;

        $countS = count($S);

        if ($countS % 2 == 0) {

            $m1 = $S[($countS / 2) - 1];
            $m2 = $S[($countS / 2)];

            $median = ($m1 + $m2) / 2;
        } else {

            $median = $S[($countS / 2)];
        }

        return $median;
    }

    /**
     * Quartiles
     *
     * @param (int) $RNR
     * @return float
     */
    public function quartile($S, $quartil = 1)
    {
        sort($S);

        $contagem = count($S);

        if ($contagem % 2 == 0) {
            // numero par

            $mediana = ($S[$contagem / 2] + $S[($contagem / 2) - 1]) / 2;
            $n_quart = $contagem / 2;

            if ($n_quart % 2 == 0) {

                $ind_q1 = $n_quart / 2;
                $ind_q3 = intval(($contagem + $n_quart) / 2);
                $quartil1 = ((($S[$ind_q1] + $S[$ind_q1 - 1]) / 2) + ($S[$ind_q1])) / 2;
                $quartil3 = ((($S[$ind_q3] + $S[$ind_q3 - 1]) / 2) + ($S[$ind_q3 - 1])) / 2;
            } else {

                $ind_q1 = intval($n_quart / 2);
                $ind_q3 = intval(($contagem + $n_quart) / 2);
                $quartil1 = ((($S[$ind_q1] + $S[$ind_q1 + 1]) / 2) + ($S[$ind_q1])) / 2;
                $quartil3 = ((($S[$ind_q3] + $S[$ind_q3 + 1]) / 2) + ($S[$ind_q3 - 1])) / 2;
            }
        } else {
            // numero ímpar

            $ind_med = intval($contagem / 2);
            $mediana = $S[$ind_med];
            $n_quart = $ind_med + 1;
            if ($n_quart % 2 == 0) { // Quartis
                $ind_q1 = $n_quart / 2;
                $ind_q3 = intval(($contagem + $n_quart - 1) / 2);
                $quartil1 = ($S[$ind_q1] + $S[$ind_q1 - 1]) / 2;
                $quartil3 = ($S[$ind_q3] + $S[$ind_q3 - 1]) / 2;
            } else {
                $ind_q1 = intval($n_quart / 2);
                $ind_q3 = intval(($contagem + $n_quart - 1) / 2);
                $quartil1 = $S[$ind_q1];
                $quartil3 = $S[$ind_q3];
            }
        }

        if ($quartil == 1)
            return $quartil1;
        else if ($quartil == 2)
            return $mediana;
        else if ($quartil == 3)
            return $quartil3;
        else
            return;
    }

    /**
     * Test hipotesis - hypothesis test for two independent samples and known variance
     *
     * @param (int) $RNR
     * @return float
     */
    public function hypothesis_two_sample_independent_by_standard_error($X1, $X2, $var1, $var2, $n1, $n2, $level_standard_error = 1, $TwoTailed = true)
    {
        $result = false;

        $sigmaXd = sqrt(($var1 / $n1) + ($var2 / $n2));
        $Xd = ($X1 + $X2) / 2;

        $valCanc = $X1 + ($level_standard_error * $sigmaXd);
        // $valCanc = sqrt(pow($valCanc,2));//para deixar o número positivo

        // sxvar_dump($Xd);
        // exit();

        if ($Xd >= $valCanc) { // iguais
            $result = true;
        } else { // diferentes
            $result = false;
        }

        return (boolean) $result;
    }

    /**
     * Test hipotesis - hypothesis test for two dependent samples and known variance
     *
     * @param (int) $RNR
     * @return float
     */
    public function hypothesis_two_dependent_known_variance($S1, $S2, $alpha = 10, $TwoTailed = true)
    {
        $result = false;

        if ($TwoTailed)
            $alpha = (50 - ($alpha / 2));

        $z = self::z_table($alpha);

        $miH0 = 0; // hipotese nula

        $n = count($S1);
        $p = $this->average_two_dependent($S1, $S2);

        $di = $p["di"];
        $di2 = $p["di2"];

        $sigma_d = $this->standard_deviation_two_dependent_samples($di, $di2, $n);

        $zCalc = ($di - $miH0) / $sigma_d;

        $RNR = $z * $sigma_d;

        if ($RNR >= $zCalc) { // iguais
            $result = true;
        } else { // diferentes
            $result = false;
        }

        // echo "X1 = ".$X1.", X2 = ".$X2.", zCalc = ".$zCalc.", RNR = ".$RNR.", sigma = ".$sigmaXd.", Z = ".$z.", var1 = ".$var1.", var2 = ".$var2;
        // var_dump($result);
        // exit();

        return (boolean) $result;
    }

    /**
     * Test hipotesis - hypothesis test for two independent samples and known variance
     *
     * @param (int) $RNR
     * @return float
     */
    public function hypothesis_two_independent_known_variance($X1, $X2, $var1, $var2, $n1, $n2, $alpha = 10, $TwoTailed = true)
    {
        $result = false;

        $sigmaXd = sqrt(($var1 / $n1) + ($var2 / $n2)); // echo "x1=".$X1.", X2=".$X2.", S=".$sigmaXd.", ";
        $Xd = $X1 - $X2;
        $miH0 = 0; // hipotese nula

        $zCalc = ($Xd - $miH0) / $sigmaXd;

        $zCalc = sqrt(pow($zCalc, 2)); // para deixar o número positivo

        if ($TwoTailed)
            $alpha = (50 - ($alpha / 2));

        $z = self::z_table($alpha);

        $RNR = $z * $sigmaXd;

        // echo "m1=".$X1."<br>";
        // echo "m2=".$X2."<br>";
        // echo "Xd=".$Xd."<br>";
        // echo "sigma=".$sigmaXd."<br>";

        // echo "z=".$z."<br>";
        // echo "zCalc=".$zCalc."<br>";
        // echo "RNR=".$RNR;
        // exit();

        // echo "zCalc = ".$zCalc.", RNR=".$RNR;

        if ($RNR >= $zCalc) { // iguais
            $result = true;
        } else { // diferentes
            $result = false;
        }

        // echo "<br>";

        // echo "X1 = ".$X1.", X2 = ".$X2.", zCalc = ".$zCalc.", RNR = ".$RNR.", sigma = ".$sigmaXd.", Z = ".$z.", var1 = ".$var1.", var2 = ".$var2;
        // var_dump($result);
        // exit();

        return (boolean) $result;
    }

    /**
     * Z table
     *
     * @param int $RNR
     * @return float
     */
    public function z_table($alpha = 47.5)
    {
        $result = 0;

        if ($alpha > 49.87) {
            $result = 3;
        } else {

            // two-tailed
            switch ($alpha) {
                case 49.95: // 99.9% - alpa 0.1%
                    $result = 3.9;
                    break;
                case 49.75: // 99.5% - alpa 0.5%
                    $result = 2.81;
                    break;
                case 49.5: // 99% - alpa 1%
                    $result = 2.57;
                    break;
                case 48.5: // 97 - alpa 3%
                    $result = 2.17;
                    break;
                case 47.5: // 95 - alpa 5%
                    $result = 1.96;
                    break;
                case 45: // 90 - alpa 10%
                    $result = 1.64;
                    break;
                case 40: // 80 - alpha 20%
                    $result = 1.28;
                    break;
                // default://
                // $result = 1.96;
            }
        }

        return (float) $result;
    }

    /**
     * Z test statistic
     *
     * @param float $standard_deviation
     * @param float $RNR
     * @param boolean $TwoTailed
     * @return float
     */
    public function z_test($standard_deviation = 0, $alpha = 10, $TwoTailed = true)
    {
        if ($TwoTailed)
            $alpha = (50 - ($alpha / 2));

        $z = self::z_table($alpha);

        $result = $z * $standard_deviation;

        return (float) $result;
    }

    /**
     * Function that calculate average or mean value of array by two sample dependent
     *
     * @param array $S
     * @return float
     */
    public function average_two_dependent($S1, $S2)
    {
        if (! is_array($S1) || ! is_array($S2)) {
            $result = - 1;
        } else {

            $di_sum = 0;
            $di2_sum = 0;

            for ($i = 0; $i < count($S1); $i ++) {

                $di = $S1[$i] - $S2[$i];

                $di_sum += $di;
                $di2_sum += pow($di, 2);
            }

            $result = array(
                "di" => $di_sum / count($S1),
                "di2" => $di2_sum
            );
        }

        return $result;
    }

    /**
     * Function that calculate average or mean value of array
     *
     * @param (array) $S
     * @return float
     */
    public function average($S)
    {
        if (! is_array($S)) {
            $result = 0;
        } else {
            $result = array_sum($S) / count($S);
        }

        return (float) $result;
    }

    /**
     * Calculate variance of array
     *
     * @param (array) $S
     * @param float $mi
     * @return float
     */
    public function variance($S = array(), $mi = 0)
    {
        $result = 0;

        foreach ($S as $Si) {

            $result += pow($Si - $mi, 2);
        }

        $result /= count($S);

        return (float) $result;
    }

    /**
     * Calculate standard deviation of array, by definition it is square root of variance
     *
     * @param (array) $S
     * @return float
     */
    public function standard_deviation($variance = 0)
    {
        return (float) sqrt($variance);
    }

    /**
     * Calculate standard deviation of array, by definition it is square root of variance
     *
     * @param (array) $S
     * @param float $n
     * @param float $N
     * @return float
     */
    public function sample_standard_deviation($variance = 0, $n = 0, $N = 0)
    {
        if ($N > 0) {
            $result = sqrt($variance / $n) * sqrt($N - $n / $N - 1); // factor of correction
        } else {
            $result = sqrt($variance / $n);
        }

        return (float) $result;
    }

    /**
     * Calculate standard deviation two independent samples
     *
     * @param (array) $S
     * @param float $n
     * @param float $N
     * @return float
     */
    public function standard_deviation_two_independent_samples($var1, $var2)
    {
        if ($N > 0) {
            $result = sqrt($variance / $n) * sqrt($N - $n / $N - 1); // factor of correction
        } else {
            $result = sqrt($variance / $n);
        }

        return (float) $result;
    }

    /**
     * Calculate standard deviation two dependent samples
     *
     * @param (array) $S
     * @param float $n
     * @param float $N
     * @return float
     */
    public function standard_deviation_two_dependent_samples($di, $di2, $n)
    {

        // if (!is_array($S1)) {
        // $result = -1;

        // }else{

        // $n = count($S1);
        // $p = $this->average_two_dependent($S1, $S2);

        // $di = $p["di"];
        // $di2 = $p["di2"];
        $sd2 = (1 / ($n - 1));
        $sd2 = $sd2 * ($di2 - (pow($di, 2) / $n));

        $sd = sqrt($sd2);

        $result = $sd / sqrt($n);

        // }

        return (float) $result;
    }
}






