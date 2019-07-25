/*
 *    EvaluatePrequential.java
 *    Copyright (C) 2007 University of Waikato, Hamilton, New Zealand
 *    @author Richard Kirkby (rkirkby@cs.waikato.ac.nz)
 *    @author Albert Bifet (abifet at cs dot waikato dot ac dot nz)
 *
 *    This program is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation; either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program. If not, see <http://www.gnu.org/licenses/>.
 *    
 */
package moa.tasks;

import java.io.File;
import java.io.FileOutputStream;
import java.io.PrintStream;
import moa.classifiers.Classifier;
import moa.core.ObjectRepository;
import moa.core.TimingUtils;
import moa.evaluation.ClassificationPerformanceEvaluator;
import moa.evaluation.LearningCurve;
import moa.options.ClassOption;
import moa.options.FileOption;
import moa.options.FlagOption;
import moa.options.FloatOption;
import moa.options.IntOption;
import moa.options.MultiChoiceOption;
import moa.streams.InstanceStreamGenerators;
import weka.core.Instance;
import weka.core.Utils;
import moa.evaluation.DriftDetectionEvaluationMeasures;
import moa.evaluation.DriftDetectionEvaluationMetrics;


/**
 * Task for evaluating a classifier on a stream by testing then training with
 * each example in sequence.
 *
 * @author Richard Kirkby (rkirkby@cs.waikato.ac.nz)
 * @author Albert Bifet (abifet at cs dot waikato dot ac dot nz)
 * @version $Revision: 7 $
 */
public class EvaluatePrequentialUFPEforDetectors extends MainTask {

    @Override
    public String getPurposeString() {
        return "Evaluates a classifier on a stream by testing then training with each example in sequence.";
    }

    private static final long serialVersionUID = 1L;

    public ClassOption learnerOption = new ClassOption("learner", 'l',
            "Classifier to train.", Classifier.class, "bayes.NaiveBayes");

    public ClassOption streamOption = new ClassOption("stream", 's',
            "Stream to learn from.", InstanceStreamGenerators.class,
            "generators.RandomTreeGenerator");

    public ClassOption evaluatorOption = new ClassOption("evaluator", 'e',
            "Classification performance evaluation method.",
            ClassificationPerformanceEvaluator.class,
            "WindowClassificationPerformanceEvaluator");

    public IntOption repetitionOption = new IntOption("repetition", 'r',
            "Number of times that training will be repeated", 1,
            1, Integer.MAX_VALUE);

    public FlagOption changeSeedAutoOption = new FlagOption("changeSeedAuto", 'c',
            "Change random seed of chosen stream automatically.");

    public IntOption instanceLimitOption = new IntOption("instanceLimit", 'i',
            "Maximum number of instances to test/train on  (-1 = no limit).",
            100000000, -1, Integer.MAX_VALUE);

    public IntOption timeLimitOption = new IntOption("timeLimit", 't',
            "Maximum number of seconds to test/train for (-1 = no limit).", -1,
            -1, Integer.MAX_VALUE);

    public IntOption sampleFrequencyOption = new IntOption("sampleFrequency",
            'f',
            "How many instances between samples of the learning performance.",
            100000, 0, Integer.MAX_VALUE);

    public IntOption memCheckFrequencyOption = new IntOption(
            "memCheckFrequency", 'q',
            "How many instances between memory bound checks.", 100000, 0,
            Integer.MAX_VALUE);

    public FileOption dumpFileOption = new FileOption("dumpFile", 'd',
            "File to append intermediate csv results to.", null, "csv", true);

    public FileOption outputPredictionFileOption = new FileOption("outputPredictionFile", 'o',
            "File to append output predictions to.", null, "pred", true);

    public FloatOption alphaOption = new FloatOption("alpha",
            'a', "Confidence Interval: [0.90 = 90%]; [0.95 = 95%]; [0.99 = 99%]", 0.95, 0.90, 0.99);
    
    public IntOption toleranceOption = new IntOption("tolerance",
            'b', "Tolerance to detect FP, FN and CD", 100, 0, Integer.MAX_VALUE);

    public IntOption windowSizeOption = new IntOption("windowSizeOfDissimilarity", 'z',
            "Number of instances to dissimilarity calculation.", 100,
            30, Integer.MAX_VALUE);
    
    
    public MultiChoiceOption formatDataOption = new MultiChoiceOption(
			"formatData", 'y',
			"Return Format Data", new String[]{
			"XML", "PainText"}, new String[]{
			"xml", "plaintext"},
			1); 
    
    public MultiChoiceOption isMetricsOption = new MultiChoiceOption(
			"isMetrics", 'm',
			"isMetrics?", new String[]{
			"False", "True"}, new String[]{
			"false", "true"},
			0); 
    
//    public MultiChoiceOption isEntropyOption = new MultiChoiceOption(
//			"isEntropy", 'm',
//			"isEntropy?", new String[]{
//			"False", "True"}, new String[]{
//			"false", "true"},
//			0); 
//    
//    public MultiChoiceOption isMDROption = new MultiChoiceOption(
//			"isMDR", 'n',
//			"isMDR?", new String[]{
//			"False", "True"}, new String[]{
//			"false", "true"},
//			1); 
//    
//    public MultiChoiceOption isMTDOption = new MultiChoiceOption(
//			"isMTD", 'p',
//			"isMTD?", new String[]{
//			"False", "True"}, new String[]{
//			"false", "true"},
//			1);
//    
//    public MultiChoiceOption isMTFAOption = new MultiChoiceOption(
//			"isMTFA", 'u',
//			"isMTFA?", new String[]{
//			"False", "True"}, new String[]{
//			"false", "true"},
//			1);
//    
//    public MultiChoiceOption isMTROption = new MultiChoiceOption(
//			"isMTR", 'v',
//			"isMTR?", new String[]{
//			"False", "True"}, new String[]{
//			"false", "true"},
//			1);
//    
//    public MultiChoiceOption isPrecisionOption = new MultiChoiceOption(
//			"isPrecision", 'x',
//			"isPrecision?", new String[]{
//			"False", "True"}, new String[]{
//			"false", "true"},
//			1);
//    
//    public MultiChoiceOption isRecallOption = new MultiChoiceOption(
//			"isRecall", 'w',
//			"isRecall?", new String[]{
//			"False", "True"}, new String[]{
//			"false", "true"},
//			1);
//    
//    public MultiChoiceOption isMCCOption = new MultiChoiceOption(
//			"isMCC", 'k',
//			"isMCC?", new String[]{
//			"False", "True"}, new String[]{
//			"false", "true"},
//			1);
//    
//    
//    public MultiChoiceOption isF1Option = new MultiChoiceOption(
//			"isF1", 'g',
//			"isF1?", new String[]{
//			"False", "True"}, new String[]{
//			"false", "true"},
//			1); 
//    
//    public MultiChoiceOption isDRIFT_POINT_DISTANCEOption = new MultiChoiceOption(
//			"isDRIFT_POINT_DISTANCE", 'h',
//			"isDRIFT_POINT_DISTANCE?", new String[]{
//			"False", "True"}, new String[]{
//			"false", "true"},
//			1); 
//    
//    public MultiChoiceOption isFN_FP_TN_TPOption = new MultiChoiceOption(
//			"isFN_FP_TN_TP", 'j',
//			"isFN_FP_TN_TP?", new String[]{
//			"False", "True"}, new String[]{
//			"false", "true"},
//			1); 
    
    
//    public FlagOption isAccuracyOption = new FlagOption("isAccuracy", 'y',
//            "Metric Accuracy Prequential.");
//    
//    public FlagOption isTimeOption = new FlagOption("isTime", 'g',
//            "Metric Time.");
//    
//    public FlagOption isMemoryOption = new FlagOption("isMemory", 'h',
//            "Metric Memory.");
    
    @Override
    public Class<?> getTaskResultType() {
        return LearningCurve.class;
    }

    @Override
    protected Object doMainTask(TaskMonitor monitor, ObjectRepository repository) {
    	
    	//LearningCurve learningCurve = new LearningCurve("");//learning evaluation instances");
    		            
        double accuracy, ctime, memory;
        int frequency;
        boolean drift;
        
        int indexMetrics = 0;
        DriftDetectionEvaluationMetrics[] ddemetrics = new DriftDetectionEvaluationMetrics[this.repetitionOption.getValue()];
        DriftDetectionEvaluationMeasures ddemeasures = new DriftDetectionEvaluationMeasures();
        
        ddemeasures.setACCURACY(true);
        ddemeasures.setTIME(true);
        ddemeasures.setMEMORY(true);
        
        boolean flagValue = false;
        
        flagValue = (isMetricsOption.getChosenIndex() == 1 ? true:false);
        
    	ddemeasures.setENTROPY(flagValue);
    	ddemeasures.setMDR(flagValue);
    	ddemeasures.setMTD(flagValue);
    	ddemeasures.setMTFA(flagValue);
    	ddemeasures.setMTR(flagValue);
    	ddemeasures.setPRECISION(flagValue);
    	ddemeasures.setRECALL(flagValue);
    	ddemeasures.setMCC(flagValue);
    	ddemeasures.setF1(flagValue);
    	ddemeasures.setACCURACY_DETECTION(flagValue);
//    	ddemeasures.setKAPPA_DETECTION(flagValue);
//    	ddemeasures.setYOUDEN_DETECTION(flagValue);         
    	ddemeasures.setDRIFT_POINT_DISTANCE(flagValue);
    	ddemeasures.setDRIFT_GENERAL_MEAN(false);
    	ddemeasures.setFN_FP_TN_TP(flagValue);
    	
    	
//        flagValue = (isEntropyOption.getChosenIndex() == 1 ? true:false);
//        ddemeasures.setENTROPY(flagValue);
//        
//        flagValue = (isMDROption.getChosenIndex() == 1 ? true:false);
//        ddemeasures.setMDR(flagValue);
//   
//        flagValue = (isMTDOption.getChosenIndex() == 1 ? true:false);
//        ddemeasures.setMTD(flagValue);
//        
//        flagValue = (isMTFAOption.getChosenIndex() == 1 ? true:false);
//        ddemeasures.setMTFA(flagValue);
//        
//        flagValue = (isMTROption.getChosenIndex() == 1 ? true:false);
//        ddemeasures.setMTR(flagValue);
//        
//        flagValue = (isPrecisionOption.getChosenIndex() == 1 ? true:false);
//        ddemeasures.setPRECISION(flagValue);
//        
//        flagValue = (isRecallOption.getChosenIndex() == 1 ? true:false);
//        ddemeasures.setRECALL(flagValue);
//        
//        flagValue = (isMCCOption.getChosenIndex() == 1 ? true:false);
//        ddemeasures.setMCC(flagValue);
//        
//        flagValue = (isF1Option.getChosenIndex() == 1 ? true:false);
//        ddemeasures.setF1(flagValue);
        
//        flagValue = (isDRIFT_POINT_DISTANCEOption.getChosenIndex() == 1 ? true:false);
//        ddemeasures.setDRIFT_POINT_DISTANCE(flagValue);
//        
//        ddemeasures.setDRIFT_MEANS(false); 
//        ddemeasures.setDRIFT_GENERAL_MEAN(false);
//        
//        flagValue = (isFN_FP_TN_TPOption.getChosenIndex() == 1 ? true:false);
//        ddemeasures.setFN_FP_TN_TP(flagValue);
        
        if(flagValue) {
        	ddemeasures.setFN(flagValue);
        	ddemeasures.setFP(flagValue);
        	ddemeasures.setTN(flagValue);
        	ddemeasures.setTP(flagValue);
        }
    	
    	
        ddemeasures.setRepetition(this.repetitionOption.getValue());
        ddemeasures.setAlpha(this.alphaOption.getValue());
        
        for (int i = 1; i <= this.repetitionOption.getValue(); i++) {
            //learningCurve = new LearningCurve("learning evaluation instances");
            prepareClassOptions(monitor, repository);
            accuracy = ctime = memory = 0.0; 
            frequency = 0;

            drift = false;
            Classifier learner = (Classifier) getPreparedClassOption(this.learnerOption);
            learner.resetLearning();
            
            InstanceStreamGenerators stream = (InstanceStreamGenerators) getPreparedClassOption(this.streamOption);
            stream.restart();
            
            if ( this.changeSeedAutoOption.isSet() ) {
                stream.changeRandomSeed(i);
            }

            if(stream.getDriftPositions().size() > 0) {
        		
            	indexMetrics = i-1; 
//            	System.out.println("indexMetrics: "+indexMetrics);
            }
            
            ddemetrics[indexMetrics] = new DriftDetectionEvaluationMetrics();
            ddemetrics[indexMetrics].resetMetrics();
            
            ddemetrics[indexMetrics].setACCURACY(ddemeasures.isACCURACY());
            ddemetrics[indexMetrics].setTIME(ddemeasures.isTIME());
            ddemetrics[indexMetrics].setMEMORY(ddemeasures.isMEMORY());
            
            if(stream.getDriftPositions().size() > 0) {
            	        		
//            	indexMetrics = i-1;            
                
                ddemetrics[indexMetrics].setWindowSize(this.windowSizeOption.getValue());
                ddemetrics[indexMetrics].setTolerance(this.toleranceOption.getValue());
                ddemetrics[indexMetrics].positions = stream.getDriftPositions(); // posicao da mudança inserida no script
                ddemetrics[indexMetrics].widths = stream.getDriftWidths();
                ddemetrics[indexMetrics].sz = ddemetrics[indexMetrics].widths.size(); // quantidade de mudanças inseridas no script
                ddemetrics[indexMetrics].setMaxInstances(this.instanceLimitOption.getValue());
            
                
                ddemetrics[indexMetrics].setENTROPY(ddemeasures.isENTROPY());
                ddemetrics[indexMetrics].setMDR(ddemeasures.isMDR());
                ddemetrics[indexMetrics].setMTD(ddemeasures.isMTD());
                ddemetrics[indexMetrics].setMTFA(ddemeasures.isMTFA());
                ddemetrics[indexMetrics].setMTR(ddemeasures.isMTR());
                
                ddemetrics[indexMetrics].setPRECISION(ddemeasures.isPRECISION());
                ddemetrics[indexMetrics].setRECALL(ddemeasures.isRECALL());
                ddemetrics[indexMetrics].setMCC(ddemeasures.isMCC());
                ddemetrics[indexMetrics].setF1(ddemeasures.isF1());
                
                ddemetrics[indexMetrics].setACCURACY_DETECTION(ddemeasures.isACCURACY_DETECTION());
//                ddemetrics[indexMetrics].setKAPPA_DETECTION(ddemeasures.isKAPPA_DETECTION());
//                ddemetrics[indexMetrics].setYOUDEN_DETECTION(ddemeasures.isYOUDEN_DETECTION());
                
                ddemetrics[indexMetrics].setDRIFT_POINT_DISTANCE(ddemeasures.isDRIFT_POINT_DISTANCE());
                ddemetrics[indexMetrics].setDRIFT_MEANS(ddemeasures.isDRIFT_MEANS()); 
                ddemetrics[indexMetrics].setDRIFT_GENERAL_MEAN(ddemeasures.isDRIFT_GENERAL_MEAN());
                ddemetrics[indexMetrics].setFN_FP_TN_TP(ddemeasures.isFN_FP_TN_TP());
                
                ddemetrics[indexMetrics].setFN(ddemeasures.isFN_FP_TN_TP());
                ddemetrics[indexMetrics].setFP(ddemeasures.isFN_FP_TN_TP());
                ddemetrics[indexMetrics].setTN(ddemeasures.isFN_FP_TN_TP());
                ddemetrics[indexMetrics].setTP(ddemeasures.isFN_FP_TN_TP());
                
            }
//            else {
//            	ddemetrics = null;
//            }
                        
            ClassificationPerformanceEvaluator evaluator = (ClassificationPerformanceEvaluator) getPreparedClassOption(this.evaluatorOption);
            evaluator.reset();
            
            learner.setModelContext(stream.getHeader());
            int maxInstances = this.instanceLimitOption.getValue();
            long instancesProcessed = 0;
            int maxSeconds = this.timeLimitOption.getValue();
            int secondsElapsed = 0;
            
            monitor.setCurrentActivity("Evaluating "+i+" of "+ this.repetitionOption.getValue(), -1);//"Evaluating learner...", -1.0);

            File dumpFile = this.dumpFileOption.getFile();
            PrintStream immediateResultStream = null;
            if (dumpFile != null) {
                try {
                    if (dumpFile.exists()) {
                        immediateResultStream = new PrintStream(
                                new FileOutputStream(dumpFile, true), true);
                    } else {
                        immediateResultStream = new PrintStream(
                                new FileOutputStream(dumpFile), true);
                    }
                } catch (Exception ex) {
                    throw new RuntimeException(
                            "Unable to open immediate result file: " + dumpFile, ex);
                }
            }
            //File for output predictions
            File outputPredictionFile = this.outputPredictionFileOption.getFile();
            PrintStream outputPredictionResultStream = null;
            if (outputPredictionFile != null) {
                try {
                    if (outputPredictionFile.exists()) {
                        outputPredictionResultStream = new PrintStream(
                                new FileOutputStream(outputPredictionFile, true), true);
                    } else {
                        outputPredictionResultStream = new PrintStream(
                                new FileOutputStream(outputPredictionFile), true);
                    }
                } catch (Exception ex) {
                    throw new RuntimeException(
                            "Unable to open prediction result file: " + outputPredictionFile, ex);
                }
            }
            boolean firstDump = true;
//                boolean preciseCPUTiming = TimingUtils.enablePreciseTiming();
            long evaluateStartTime = TimingUtils.getNanoCPUTimeOfCurrentThread();
            long lastEvaluateStartTime = evaluateStartTime;
            double RAMHours = 0.0;
            while (stream.hasMoreInstances()
                    && ((maxInstances < 0) || (instancesProcessed < maxInstances))
                    && ((maxSeconds < 0) || (secondsElapsed < maxSeconds))) {
            	
                Instance trainInst = stream.nextInstance();
                Instance testInst = (Instance) trainInst.copy();
                                
                /*
                 * ------------------------------------------------------------
                 * Drift metrics
                 * 
                 */
                int prediction2 = 0;
                
                if(ddemeasures.isENTROPY()) {
                	int trueClass = (int) testInst.classValue();
                	if (Utils.maxIndex(
            			learner.getVotesForInstance(testInst)) == trueClass) {
                        prediction2 = 0;//true
                    } else {
                        prediction2 = 1;//false
                    }
                }
                
                if (instancesProcessed == 1) { // Checks for drifts in dataset     
                	if(learner.isChangeDetectMethod() && ddemetrics[indexMetrics].sz > 0) {        
                		drift = true;
                		ddemeasures.setChangeDetectMethod(drift);
                		ddemetrics[indexMetrics].setDrift(drift);                		
                	}
                } 
                /*
                 * ------------------------------------------------------------
                 */
                
                
        		
        		
                if (testInst.classIsMissing() == false) {
                    // Added for semisupervised setting: test only if we have the label
                    double[] prediction = learner.getVotesForInstance(testInst);
                    // Output prediction
                    if (outputPredictionFile != null) {
                        outputPredictionResultStream.println(
                		Utils.maxIndex(prediction) + "," + testInst.classValue());
                    }
                    evaluator.addResult(testInst, prediction);
                }
        		
                learner.trainOnInstance(trainInst);
                instancesProcessed++;

                if (instancesProcessed % this.sampleFrequencyOption.getValue() == 0
                        || stream.hasMoreInstances() == false) {
                	
                    long evaluateTime = TimingUtils.getNanoCPUTimeOfCurrentThread();
                    double time = TimingUtils.nanoTimeToSeconds(evaluateTime - evaluateStartTime);
                    double timeIncrement = TimingUtils.nanoTimeToSeconds(evaluateTime - lastEvaluateStartTime);
                    double RAMHoursIncrement = learner.measureByteSize() / (1024.0 * 1024.0 * 1024.0); //GBs
                    RAMHoursIncrement *= (timeIncrement / 3600.0); //Hours
                    RAMHours += RAMHoursIncrement;
                    lastEvaluateStartTime = evaluateTime;

                    if (immediateResultStream != null) {
                        if (firstDump) {
                            immediateResultStream.println("");//learningCurve.headerToString());
                            firstDump = false;
                        }
                        immediateResultStream.println("");//learningCurve.entryToString(learningCurve.numEntries() - 1));
                        immediateResultStream.flush();
                    }
                    
                    accuracy += evaluator.getPerformanceMeasurements()[1].getValue();
                    ctime += time;
                    memory += RAMHours*1024*1024*1024; //Bytes/s
                    frequency++;
                }
                
                /*
                 * input to calculation drift metrics
                 */
                if(drift) {
	        		ddemetrics[indexMetrics].input(instancesProcessed 
	                		,learner.isChangeDetectMethodPosition()
	                		,prediction2);
//                		,accuracy, ctime, memory, frequency);
                }
                
                if (instancesProcessed % INSTANCES_BETWEEN_MONITOR_UPDATES == 0) {
                    if (monitor.taskShouldAbort()) {
                        return null;
                    }
                }
            }
            
            
            
            if (immediateResultStream != null) {
                immediateResultStream.close();
            }
            if (outputPredictionResultStream != null) {
                outputPredictionResultStream.close();
            }
                  
            /*
             * end data stream
             */
            
            ddemetrics[indexMetrics].end(accuracy, ctime, memory, frequency);
            
            	            
        }
        
        
        if( ddemeasures.isACCURACY()
        		|| ddemeasures.isTIME()
        		|| ddemeasures.isMEMORY()
        		|| ddemeasures.isENTROPY()
        		|| ddemeasures.isMDR()
        		|| ddemeasures.isMTD()
        		|| ddemeasures.isMTFA()
        		|| ddemeasures.isMTR()        
        		|| ddemeasures.isPRECISION()
        		|| ddemeasures.isRECALL()
        		|| ddemeasures.isMCC()
        		|| ddemeasures.isF1()
        		|| ddemeasures.isFN()
        		|| ddemeasures.isFP()
        		|| ddemeasures.isTN()
        		|| ddemeasures.isTP()
        		|| ddemeasures.isACCURACY_DETECTION()
//        		|| ddemeasures.isKAPPA_DETECTION()
//        		|| ddemeasures.isYOUDEN_DETECTION()
        		) {
        	
        	if(ddemetrics != null) {
        		ddemeasures.setDdemetrics(ddemetrics);
                ddemeasures.getPrintData(this.formatDataOption.getChosenIndex());
        	}        	
        }
        
      
        
        return new LearningCurve("");//learningCurve;
    }
}
