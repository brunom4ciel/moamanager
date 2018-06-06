<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\generator;

defined('_EXEC') or die();

function getFixedVarValueNumber($varname = "", $default = "", $prefix = "", $sufix = "")
{
    $result = "";

    if (! isset($_POST[$varname]))
        $result = $default;
    else if (is_numeric($_POST[$varname]))
        $result = $prefix . $_POST[$varname] . $sufix;

    return $result;
}

function getFixedVarValue($varname = "", $default = "", $prefix = "", $sufix = "")
{
    $result = "";

    if (! isset($_POST[$varname]))
        $result = $default;
    else
        $result = $prefix . $_POST[$varname] . $sufix;

    return $result;
}

function sortArrayElements($arrayIn, $sorter = "asc")
{
    if ($sorter == "asc")
        asort($arrayIn);
    else
        arsort($arrayIn);

    $arrayOut = array();

    foreach ($arrayIn as $key => $item) {
        array_push($arrayOut, $item);
    }

    return $arrayOut;
}

$arrayTask = array(
    "GeneticAlgorithm",
    "EvaluateInterleavedTestThenTrain",
    "EvaluateInterleavedTestThenTrain2",
    "EvaluatePrequential",
    "EvaluatePrequential2"
);

$arrayTask = sortArrayElements($arrayTask, "asc");

$arrayLearners = array(
    "NaiveBayes",
    "HoeffdingTree",
    "SingleClassifierDrift"
);

$arrayLearners = sortArrayElements($arrayLearners, "asc");

$arrayLearnersParameters = array(
    "NaiveBayes" => array(),
    "HoeffdingTree" => array(),
    "SingleClassifierDrift" => array(
        array(
            "name" => "l",
            "default" => "",
            "label" => "Classifier",
            "type" => "list",
            "list" => array(
                "bayes.NaiveBayes" => "bayes.NaiveBayes",
                "trees.HoeffdingTree" => "trees.HoeffdingTree"
            )
        )
    )

);

$arrayLearnersMethods = array(
    "DDM",
    "EDDM",
    "STEPD",
    "STEPD_New",
    "FSDD",
    "EADD",
    "ADWINChangeDetector",
    "EWMAChartDM",
    "EnsembleDriftDetectionMethods",
    "HDDM_A_Test",
    "HDDM_W_Test",
    "PageHinkleyDM",
    "GeometricMovingAverageDM",
    "SeqDrift1ChangeDetector",
    "SeqDrift2ChangeDetector"
);

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

$arrayLearnersMethods = sortArrayElements($arrayLearnersMethods, "asc");

$arrayDatasets = array(
    "AgrawalGenerator",
    "LEDGeneratorDrift",
    "MixedGenerator",
    "RandomRBFGeneratorDrift",
    "STAGGERGenerator",
    "SineGenerator",
    "WaveformGeneratorDrift",
    "Airlines",
    "Covtype",
    "Elec",
    "Poker"
);

$arrayDatasets = sortArrayElements($arrayDatasets, "asc");

?>