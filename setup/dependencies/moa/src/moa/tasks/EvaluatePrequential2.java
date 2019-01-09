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
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import moa.classifiers.Classifier;
import moa.core.Measurement;
import moa.core.ObjectRepository;
import moa.core.TimingUtils;
import moa.evaluation.ClassificationPerformanceEvaluator;
import moa.evaluation.LearningCurve;
import moa.evaluation.LearningEvaluation;
import moa.options.ClassOption;
import moa.options.FileOption;
import moa.options.FlagOption;
import moa.options.FloatOption;
import moa.options.IntOption;
import moa.streams.InstanceStreamGenerators;
import weka.core.Instance;
import weka.core.Utils;

import org.apache.commons.math3.distribution.TDistribution;

/**
 * Task for evaluating a classifier on a stream by testing then training with
 * each example in sequence.
 *
 * @author Richard Kirkby (rkirkby@cs.waikato.ac.nz)
 * @author Albert Bifet (abifet at cs dot waikato dot ac dot nz)
 * @version $Revision: 7 $
 */
public class EvaluatePrequential2 extends MainTask {

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

    public IntOption windowSizeOption = new IntOption("windowsize", 'z',
            "Number of instances to dissimilarity calculation.", 100,
            30, Integer.MAX_VALUE);
    
    public IntOption specificSeedOption = new IntOption("specificseed", 'm',
            "Specific number seed", 1,
            1, Integer.MAX_VALUE);
    
    @Override
    public Class<?> getTaskResultType() {
        return LearningCurve.class;
    }
    
    private int[] measuresTDD;
	private int[] measuresARLSum;
	private int[] measuresARLCount;
	private int[] conceptDriftFirstInstance;
	private int[] lastDriftIssue;
	
	public void DriftMeasures(List<Integer> positions, int instanceLimit) {
		
		//Add elements for the left and right stream bounds
		int conceptsBoundsCount = positions.size() + 2;
		
		this.conceptDriftFirstInstance = new int[conceptsBoundsCount];
		this.measuresTDD = new int[conceptsBoundsCount];
		this.measuresARLSum = new int[conceptsBoundsCount];
		this.measuresARLCount = new int[conceptsBoundsCount];
		this.lastDriftIssue = new int[conceptsBoundsCount];
		
		
		for (int i = 0; i < conceptsBoundsCount; i++) {
			
			this.conceptDriftFirstInstance[i] = (( i > 0 && i < (conceptsBoundsCount - 1) ) ? positions.get(i - 1) : 0);
			this.measuresTDD[i] = -1;
			this.measuresARLSum[i] = 0;
			this.measuresARLCount[i] = 0;
			this.lastDriftIssue[i] = 0;
		}
		
		//by default the first concept has TTD zero
		this.measuresTDD[0] = 0;
		
		//Define the lower bound of the first concept and the upper bound of the last concept.
		this.conceptDriftFirstInstance[0] = 0;
		this.conceptDriftFirstInstance[conceptsBoundsCount - 1] = instanceLimit;
	}
	
	public void assessDriftIssues(int driftIssue) {
		
		int lastDriftPosition = 0;
		
		for (int i = 1; i < this.conceptDriftFirstInstance.length; i++) {
			
			lastDriftPosition = i - 1;
			if(this.conceptDriftFirstInstance[lastDriftPosition] <= driftIssue 
					&&  driftIssue < this.conceptDriftFirstInstance[i]) {
				
				if (this.measuresTDD[lastDriftPosition] < 0) {
					
					//The first drift issue will be assessed as a true detection
					this.measuresTDD[lastDriftPosition] = (driftIssue - this.conceptDriftFirstInstance[lastDriftPosition]);
				}else {
					
					if (this.lastDriftIssue[lastDriftPosition] == 0) {
						
						this.lastDriftIssue[lastDriftPosition] = driftIssue;
						this.measuresARLSum[lastDriftPosition] += (driftIssue - this.conceptDriftFirstInstance[lastDriftPosition]);
						
					}else {
						
						this.measuresARLSum[lastDriftPosition] += (driftIssue - this.lastDriftIssue[lastDriftPosition]);
						this.lastDriftIssue[lastDriftPosition] = driftIssue;
						
					}
					
					this.measuresARLCount[lastDriftPosition]++;
				}
			}
		}
		
		
	}
	
	public int[] getByConceptMeasuresARLSum() {
		return this.measuresARLSum;
	}
	
	public int[] getByConceptMeasuresARLCount() {
		return this.measuresARLCount;
	}
	
	public int[] getByConceptMeasuresTTD() {
		return this.measuresTDD;
	}
	
	public int[] getconceptDriftFirstInstance() {
		return this.conceptDriftFirstInstance;
	}
	
	public String measuresDriftDebug() {
		String out = "";
		
        out = "\n\nConcept\tARLCount\tARLSum\tTTD\tdrift";
        for (int i= 0; i < (this.getByConceptMeasuresARLCount().length - 1); i++) {
        	out += "\n"+ i
        			+ "\t" + this.getByConceptMeasuresARLCount()[i]
        			+ "\t\t " + ((this.getByConceptMeasuresARLSum()[i] == 0)?((this.conceptDriftFirstInstance[i + 1] - this.conceptDriftFirstInstance[i])+"*"):this.getByConceptMeasuresARLSum()[i])
        			+ "\t " + ((this.getByConceptMeasuresTTD()[i] < 0)?((this.conceptDriftFirstInstance[i + 1] - this.conceptDriftFirstInstance[i])+"*"):this.getByConceptMeasuresTTD()[i])
        			+ "\t" +  this.getconceptDriftFirstInstance()[i];
        			
        	
        }
        
        return out;
	}

	
	/**
	 * Mean Time between False Alarms (lazy result)
	 * @return double[]
	 */
	public double[] lazyMeasureMTFA() {
		int count = 0;
		double measure = 0.0;
		double[] fractional = {0.0,0.0};
		
		for ( int i = 0; i < (this.measuresARLSum.length - 1); i++) {
			
			if (this.measuresARLCount[i] > 0) {
				measure += this.measuresARLSum[i]/(double)this.measuresARLCount[i];
			}else {
				measure += (this.conceptDriftFirstInstance[i + 1] - this.conceptDriftFirstInstance[i]);
			}
			count++;
		}
		
		fractional[0] = measure;
		fractional[1] = count;
		
		return fractional;
	}
	

	
	/**
	 * Mean Time to Detection (lazy result)
	 * @return
	 */
	public double[] lazyMeasureMTD() {
		int count = 0;
		double measure = 0.0;
		double[] fractional = {0.0,0.0};
		
		for ( int i = 1; i < (this.measuresTDD.length - 1); i++) {
			if (this.measuresTDD[i] > 0 ) {
				measure += this.measuresTDD[i];
			}else {
				measure += (this.conceptDriftFirstInstance[i + 1] - this.conceptDriftFirstInstance[i]);
			}
			count++;
		}
		
		fractional[0] = measure;
		fractional[1] = count;
		
		return fractional;
	}
	
	/**
	 * Missed Detection Rate  (lazy result)
	 * @return
	 */
	public double[] lazyMeasureMDR() {
		int count = 0;
		double measure = 0.0;
		double[] fractional = {0.0,0.0};
		
		for ( int i = 1; i < (this.measuresTDD.length - 1); i++) {
			if (this.measuresTDD[i] < 0 ) {
				measure++;				
			}
			count++;
		}

		fractional[0] = measure;
		fractional[1] = count;
		
		return fractional;
	}
	
	
    private double calcStandardDeviation(double u, List<Double> mean) {
        double s = 0.0;
        int size = (int) mean.size();
        for (int i = 0; i < size; i++) {
            if ( !Double.isNaN(mean.get(i)) ) {
                s += ((mean.get(i) - u) * (mean.get(i) - u));
            }
        }

        return Math.sqrt(s / ((double) (size - 1)));
    }

    private void printStatistics( List<Double> typeMean, String type ) {
        double n, u, s, sum=0.0;
        n = typeMean.size();
        System.out.println(type+":");
        
        for (Double TM : typeMean) {
        	if(TM < 1) {
        		System.out.printf("%.4f\n", TM);
        	}else {
        		System.out.printf("%.2f\n", TM);
        	}
            
            sum += TM;
        }
        
        u = sum / n;
        s = calcStandardDeviation(u, typeMean);
        
        if(u < 1) {
        	System.out.printf("Mean (CI) = %.4f", u);
        }else {
        	System.out.printf("Mean (CI) = %.2f", u);
        }        
        
        if ( repetitionOption.getValue() > 1 ) {
            TDistribution t = new TDistribution(repetitionOption.getValue()-1);
            double tic = t.inverseCumulativeProbability(1-((1-alphaOption.getValue())/2.0)) * (s / Math.sqrt(n));
            
            if(tic < 1) {
            	System.out.printf(" (+-%.4f)\n\n", tic);
            }else {
            	System.out.printf(" (+-%.2f)\n\n", tic);
            }
            
        } else {
            System.out.printf(" (+-N/A)\n\n");
        }
    }
    
    /*private void printStatisticsDecimal( List<Double> typeMean, String type, int decimal ) {
        double n, u, s, sum=0.0;
        n = typeMean.size();
        System.out.println(type+":");
        
        for (Double TM : typeMean) {
            System.out.printf("%.2f\n", TM);
            sum += TM;
        }
        
        u = sum / n;
        s = calcStandardDeviation(u, typeMean);
        
        if ( repetitionOption.getValue() > 1 ) {
            TDistribution t = new TDistribution(repetitionOption.getValue()-1);
            System.out.printf("Mean (CI) = %.2f (+-%.2f)\n\n", u, t.inverseCumulativeProbability(1-((1-alphaOption.getValue())/2.0)) * (s / Math.sqrt(n)));
        } else {
            System.out.printf("Mean (CI) = %.2f (+-N/A)\n\n", u);
        }
    }*/
    
    private String printMetrics1( List<Double> FN, List<Double> FP, List<Double> TN, List<Double> TP ) {
        String results="";
        double n, uFN,uFP,uTN,uTP, sFN,sFP,sTN,sTP, sumFN,sumFP,sumTN,sumTP;
        sumFN=sumFP=sumTN=sumTP=0.0;
        n = FN.size();
        
        System.out.println("FN\tFP\tTN\tTP");
        
        for ( int i=0; i<n; i++ ) {
            System.out.printf("%.0f\t",FN.get(i)); System.out.printf("%.0f\t",FP.get(i));
            System.out.printf("%.0f\t",TN.get(i)); System.out.printf("%.0f\n",TP.get(i));
            
            sumFN += FN.get(i); sumFP += FP.get(i);
            sumTN += TN.get(i); sumTP += TP.get(i);
        }
        
        uFN = sumFN / n; uFP = sumFP / n;
        uTN = sumTN / n; uTP = sumTP / n;
        
        sFN = calcStandardDeviation(uFN, FN); sFP = calcStandardDeviation(uFP, FP);
        sTN = calcStandardDeviation(uTN, TN); sTP = calcStandardDeviation(uTP, TP);
        
        if ( repetitionOption.getValue() > 1 ) {
        TDistribution t = new TDistribution(repetitionOption.getValue()-1);
            System.out.printf("Mean (CI) = ");
            System.out.printf("%.2f (+-%.2f)\t", uFN, t.inverseCumulativeProbability(1-((1-alphaOption.getValue())/2.0)) * (sFN / Math.sqrt(n)));
            System.out.printf("%.2f (+-%.2f)\t", uFP, t.inverseCumulativeProbability(1-((1-alphaOption.getValue())/2.0)) * (sFP / Math.sqrt(n)));
            System.out.printf("%.2f (+-%.2f)\t", uTN, t.inverseCumulativeProbability(1-((1-alphaOption.getValue())/2.0)) * (sTN / Math.sqrt(n)));
            System.out.printf("%.2f (+-%.2f)\n\n", uTP, t.inverseCumulativeProbability(1-((1-alphaOption.getValue())/2.0)) * (sTP / Math.sqrt(n)));
        } else {
            System.out.printf("Mean (CI) = ");
            System.out.printf("%.2f (+-N/A)\t", uFN);
            System.out.printf("%.2f (+-N/A)\t", uFP);
            System.out.printf("%.2f (+-N/A)\t", uTN);
            System.out.printf("%.2f (+-N/A)\n\n", uTP);
        }
       
        results += String.format("\t%.0f\t%.0f\t%.0f\t\t%.0f\t", sumFN,sumFP,sumTN,sumTP);
        
        return results;
    }
    
    private String printMetrics2( List<Double> FN, List<Double> FP, List<Double> TN, List<Double> TP ) {
        double sumFN,sumFP,sumTN,sumTP, precision,recall,MCC,F1;
        String results="";
        sumFN=sumFP=sumTN=sumTP=0.0;
        precision=recall=MCC=F1=0.0;
        
        for ( int i=0; i<FN.size(); i++ ) {
            sumFN += FN.get(i); sumFP += FP.get(i);
            sumTN += TN.get(i); sumTP += TP.get(i);
        }
        
        if ( sumTP != 0.0 ) {
            precision = (sumTP)/(sumTP+sumFP);
            recall = (sumTP)/(sumTP+sumFN);
        }
        
        if ( sumFP+sumTP != 0.0 ) {
            MCC = (sumTP*sumTN-sumFP*sumFN)/Math.sqrt((sumTP+sumFP)*(sumTP+sumFN)*(sumTN+sumFP)*(sumTN+sumFN));
        }
        
        if ( precision != 0.0 ) {
            F1 = 2*precision*recall/(precision+recall);
        }
        
        results += String.format("%f\t%.9f\t%.9f\t%.9f",precision,recall,MCC,F1);
        
        return results;
    }
    
    private double MCC(double sumFP, double sumFN, double sumTP, double sumTN)
    {
    	double result = 0;
    	
    	if ( sumFP+sumTP != 0.0 ) {
            result = (sumTP*sumTN-sumFP*sumFN)/Math.sqrt((sumTP+sumFP)*(sumTP+sumFN)*(sumTN+sumFP)*(sumTN+sumFN));
        }
//    	
//    	if(result <0) {
//    		
//    		System.out.println("sumFP: "+sumFP+",sumFN: "+sumFN+",sumTP: "+sumTP+",sumTN: "+sumTN+",result:"+result);
//    		System.exit(0);
//    	}
    	
    	return result;
    }
    
    private String printMetrics3( List<Double> measureMDR, 
    								List<Double> measureMTD,  
    								List<Double> measureMTFA, 
    								List<Double> measureMTR) {
        double sumMDR,sumMTD,sumMTFA,sumMTR,MDR,MTD,MTFA,MTR;
        int n;
        String results="";
        sumMDR=sumMTD=sumMTFA=sumMTR=0.0;
        MDR=MTD=MTFA=MTR=0.0;
        n = measureMDR.size();
        
        for ( int i=0; i<n; i++ ) {
        	sumMDR += measureMDR.get(i);         	
        	sumMTD += measureMTD.get(i);         	
        	sumMTFA += measureMTFA.get(i);        	
        	sumMTR += measureMTR.get(i); 
        }
        
        if ( sumMDR != 0.0 ) {
            MDR = sumMDR / n;
        }
        
        if ( sumMTD != 0.0 ) {
            MTD = sumMTD / n;
        }
        
        if ( sumMTFA != 0.0 ) {
        	MTFA = sumMTFA / n;
        }
        
        if ( sumMTR != 0.0 ) {
        	MTR = sumMTR / n;
        }
        
        results = String.format("\t%.2f\t%.2f\t%.2f\t%.2f", MDR, MTFA, MTD, MTR);
        
        return results;
    }
    
    
    private void printStatistics3( List<Double> typeMean, String type ) {
        double n, u, s, sum=0.0;
        n = typeMean.size();
        System.out.println(type+":");
        
        for (Double TM : typeMean) {
            System.out.printf("%.9f\n", TM);
            sum += TM;
        }
        
        u = sum / n;
        s = calcStandardDeviation(u, typeMean);
        
        if ( repetitionOption.getValue() > 1 ) {
            TDistribution t = new TDistribution(repetitionOption.getValue()-1);
            System.out.printf("Mean (CI) = %.9f (+-%.2f)\n\n", u, t.inverseCumulativeProbability(1-((1-alphaOption.getValue())/2.0)) * (s / Math.sqrt(n)));
        } else {
            System.out.printf("Mean (CI) = %.9f (+-N/A)\n\n", u);
        }
    }
    
    
//    private void printStatistics4( List<Integer> typeMean, String type ) {
//        int n;
//        n = typeMean.size();
//        System.out.println(type+":");
//        
//        for (Integer TM : typeMean) {
//            System.out.println(TM);
//        }
//        
//        System.out.println("Count Drifts: "+n);
//    }
    
    
    private String printDistanceDriftTable ( List<Double> cd, List<Integer> positions ) {
        System.out.println("Drift point distance:");
        
        double u, s; String results="";
        int sz = positions.size(), pos = 0;
        double sum[] = new double[sz], freq[] = new double[sz];
        for ( int i=0; i<cd.size(); i++ ) {
            if ( cd.get(i) != -1.0 ) {
                System.out.printf("%.2f\t", cd.get(i)-positions.get(pos));
                sum[pos] += cd.get(i)-positions.get(pos);
                freq[pos] += 1.0;
            } else {
                System.out.printf("FN\t");
            }
            
            pos = (pos+1)%sz;
            if ( (i+1)%sz == 0 ) {
                System.out.printf("\n");
            }
        }
        
        List<Double> tempValues = new ArrayList<>();
        List<Double> tempAllValues = new ArrayList<>();
        double sumAll = 0.0;
        if ( sz > 0 ) {
            System.out.printf("Drifts Means = ");
        } else {
            System.out.printf("Drifts Means = N/A (+-N/A)\t");
        }
        for ( int i=0; i<sz; i++ ) {
            tempValues.clear();
            u = sum[i] / freq[i];
            for ( int j=i; j<cd.size(); j+=sz ) {
                if ( cd.get(j) != -1 ) {
                    tempValues.add(cd.get(j)-positions.get(i));
                    tempAllValues.add(cd.get(j)-positions.get(i));
                    sumAll += (cd.get(j)-positions.get(i));
                }
            }
            s = calcStandardDeviation(u, tempValues);
            if ( Double.isNaN(u) ) {
                System.out.printf("N/A (+-N/A)\t");
            } else {
                if ( repetitionOption.getValue() > 1 ) {
                    TDistribution t = new TDistribution(repetitionOption.getValue()-1);
                    System.out.printf("%.2f (+-%.2f)\t", u, t.inverseCumulativeProbability(1-((1-alphaOption.getValue())/2.0)) * (s / Math.sqrt(freq[i])));
                } else {
                    System.out.printf("%.2f (+-N/A)\t", u);
                }
            }
        } System.out.printf("\n");
        u = sumAll / tempAllValues.size();
        if ( Double.isNaN(u) ) {
            System.out.printf("General Mean = N/A\n\n");
            results += "N/A\t\t";
        } else {
            System.out.printf("General Mean = %.2f\n\n", u);
            results += String.format("%.2f\t\t", u);
        }
        
        return results;
    }

    public double log2(double d)
    {
    	return Math.log(d)/Math.log(2.0);
    }
     
    public double entropy(double p)
    {   
		if (p == 0 || p == 1) return 1;
		   
		double q = 1 - p;
		   
		return -p * log2(p) - q * log2(q);
    }
    
    public double entropy2(double[] frequency_array)
    {
    	double entropy = 0;
	    for(int i=0;i<frequency_array.length;i++) {
	    	
	        if(frequency_array[i]!=0) {
			    // calculate the probability of a particular drift occuring
			    double probabilityOfDrift=(double)frequency_array[i];
	
			    // calculate the next value to sum to previous entropy calculation
			    double value = probabilityOfDrift * (Math.log(probabilityOfDrift) / Math.log(2));
	            	    entropy = entropy + value;
	            	    
//        	    System.out.println("entropy: "+entropy+", probabilityOfDrift: "+probabilityOfDrift);
	        }
        }
	    entropy *= -1;    	
    	
//	    System.out.println("final entropy: "+entropy);
	    
    	return entropy;    	
    }
    
    
    public double entropy3(double frequency_value)
    {
    	double entropy = 0;
	    	
	    if(frequency_value!=0)
	    {
			    // calculate the next value to sum to previous entropy calculation
			    entropy = frequency_value * (Math.log(frequency_value) / Math.log(2));
			    entropy *= -1; 
	    }
        	    
    	return entropy;    	
    }
    
    
    @Override
    protected Object doMainTask(TaskMonitor monitor, ObjectRepository repository) {
        LearningCurve learningCurve = new LearningCurve("learning evaluation instances");
//        List<Measurement> measurementList = new LinkedList<>();
        List<Double> meanAccuracy = new ArrayList<>(), meanTime = new ArrayList<>(), meanMemory = new ArrayList<>(), 
        		meanDissimilarity = new ArrayList<>(), meanTP = new ArrayList<>(), measureMCC = new ArrayList<>();
        List<Double> FP = new ArrayList<>(), FN = new ArrayList<>(), TP = new ArrayList<>(), TN = new ArrayList<>();
        List<Double> CD = new ArrayList<>();
        List<Integer> positions, widths;
        int specificseed = specificSeedOption.getValue();
        
        //ROHGI
		//double[] measureMDR = {0.0,0.0}, measureMTD = {0.0,0.0}, measureMTFA = {0.0,0.0}, measureAux = {0.0,0.0};
		
//        List<Double> measureMDR0 = new ArrayList<>();
//        List<Double> measureMDR1 = new ArrayList<>();
//        
//        List<Double> measureMTD0 = new ArrayList<>();
//        List<Double> measureMTD1 = new ArrayList<>();
//        
//        List<Double> measureMTFA0 = new ArrayList<>();
//        List<Double> measureMTFA1 = new ArrayList<>();
        
        List<Double> measureMDR = new ArrayList<>();
        List<Double> measureMTD = new ArrayList<>();
        List<Double> measureMTFA = new ArrayList<>();
        List<Double> measureMTR = new ArrayList<>();
        
        //List<Integer> pointsDrifts = new ArrayList<>();
        double accuracy, ctime, memory, fp, fn, tp, tn;
        int frequency, curr, sz, wt, middlePos;
        boolean toleranceArea, drift;
        String lineValues = "";
        boolean driftp = false;
//        int numberInstances = 0;
//        double[] detectDrifts = new double[60];
//        int indexDetectDrift = 0;
//        final int detectDrifts_length = detectDrifts.length+1;
        double entropyValue = 0;
        
         int [] sp;	// stored predictions
         int ci;	// current index
         int cs;	// current sum
         int ws = this.windowSizeOption.getValue(); 	// window size
//         int inw;	// instance number from window
//        private int in;	// instance number from stream
        
//         int repeat = 0;//repetitionOption.getValue();
//         numberInstances = 0;
//         inw = 0;
//         double probabilidadeMedia = 0;
//         double probabilidadeMediaFinal = 0;
//         int contador = 0;
         sp = new int[ws];
//         inw = 0;
		 ci = cs = 0;
		 
//		//ROHGI
// 		Arrays.fill(measureMDR, 0); 
// 		Arrays.fill(measureMTD, 0); 
// 		Arrays.fill(measureMTFA, 0); 
 		
        for (int i = 1; i <= this.repetitionOption.getValue(); i++) 
        {        	
            learningCurve = new LearningCurve("learning evaluation instances");
            prepareClassOptions(monitor, repository);
            accuracy = ctime = memory = 0.0; frequency = curr = 0;
            fp = fn = 0.0;
            drift = false;
            Classifier learner = (Classifier) getPreparedClassOption(this.learnerOption);
            learner.resetLearning();
            InstanceStreamGenerators stream = (InstanceStreamGenerators) getPreparedClassOption(this.streamOption);
            
            if ( this.changeSeedAutoOption.isSet() )
            {
                stream.changeRandomSeed(i);
            }
            else
            {
            	stream.changeRandomSeed(specificseed);
            }
            
            positions = stream.getDriftPositions();
            widths = stream.getDriftWidths();
            sz = widths.size();
            ClassificationPerformanceEvaluator evaluator = (ClassificationPerformanceEvaluator) getPreparedClassOption(this.evaluatorOption);
            evaluator.reset();
            learner.setModelContext(stream.getHeader());
            int maxInstances = this.instanceLimitOption.getValue();
            int instancesProcessed = 0;
            int maxSeconds = this.timeLimitOption.getValue();
            int secondsElapsed = 0;
            monitor.setCurrentActivity("Evaluating learner...", -1.0);

            //ROHGI
            DriftMeasures(positions, maxInstances);
            
            
            File dumpFile = this.dumpFileOption.getFile();
            PrintStream immediateResultStream = null;
            if (dumpFile != null) 
            {
                try
                {
                    if (dumpFile.exists()) 
                    {
                        immediateResultStream = new PrintStream(
                                new FileOutputStream(dumpFile, true), true);
                    } 
                    else 
                    {
                        immediateResultStream = new PrintStream(
                                new FileOutputStream(dumpFile), true);
                    }
                } 
                catch (Exception ex) 
                {
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
            boolean preciseCPUTiming = TimingUtils.enablePreciseTiming();
            long evaluateStartTime = TimingUtils.getNanoCPUTimeOfCurrentThread();
            long lastEvaluateStartTime = evaluateStartTime;
            double RAMHours = 0.0;
//            numberInstances = 0;
//            inw = 0;
//            repeat++;
//            probabilidadeMedia = 0;
//            probabilidadeMediaFinal = 0;
//            contador = 0;
//            inw = 0;
            ci = cs = 0;    	
            Arrays.fill(sp, 0); 
//            Arrays.fill(detectDrifts, 0);
//            indexDetectDrift = 0;
            entropyValue = 0;
            
            while (stream.hasMoreInstances()
                    && ((maxInstances < 0) || (instancesProcessed < maxInstances))
                    && ((maxSeconds < 0) || (secondsElapsed < maxSeconds))) {
            	
                Instance trainInst = stream.nextInstance();
                Instance testInst = (Instance) trainInst.copy();
                
//                numberInstances++;
//                inw++;
                
                int trueClass = (int) testInst.classValue();
                boolean prediction2;
                if (Utils.maxIndex(learner.getVotesForInstance(testInst)) == trueClass) {
                    prediction2 = true;
                } else {
                    prediction2 = false;
                }
                cs += sp[ci++] = (prediction2 == true ? 0:1);	 		
        		ci = (ci == ws ? 0:ci); //	adjust the index position
        		cs -= sp[ci]; //	decrementer value the sum
        		
                
                if (testInst.classIsMissing() == false) {
                    // Added for semisupervised setting: test only if we have the label
                    double[] prediction = learner.getVotesForInstance(testInst);
                    // Output prediction
                    if (outputPredictionFile != null) {
                        outputPredictionResultStream.println(Utils.maxIndex(prediction) + "," + testInst.classValue());
                    }
                    evaluator.addResult(testInst, prediction);
                }
                
                learner.trainOnInstance(trainInst);
                instancesProcessed++;
                		
                if (instancesProcessed == 1) { // Checks for drifts in dataset     
                	if(learner.isChangeDetectMethod()) {
                		drift = true;
                	}
                }

                if (drift) {
                    toleranceArea = false;
                    if (curr < sz) {
                    	middlePos = (int)(widths.get(curr)*0.5);
                        wt = middlePos + this.toleranceOption.getValue();// se gradual
                        if (positions.get(curr) <= instancesProcessed 
                        		&& instancesProcessed < positions.get(curr) + wt) {                        	
                            toleranceArea = true;
                            if(learner.isChangeDetectMethodPosition()) {
                                CD.add((double) instancesProcessed);
                                curr++;
                                driftp = true;
                            } else if (instancesProcessed+1 >= positions.get(curr) + wt) {
                                CD.add(-1.0);
                                fn++;
                                curr++;
                            }
                        }
                    }

                    if (learner.isChangeDetectMethodPosition() == true && !toleranceArea) {
                        fp++;
                        driftp = true;
                    }
                    
                    if(driftp)
                    {
                    	driftp = false;
                    	                            
                        //ROHGI
                        if (positions.size() > 0) {
                        	//Cast to int...
                        	assessDriftIssues((int)instancesProcessed);
                        }
                       
                		double err = 0;
                		
                		if(cs > 0)
                		{
                			err = ((double) cs / ws);
                		}

                		entropyValue += entropy3(err);

                		ci = cs = 0;    	
                        Arrays.fill(sp, 0); 
                    }
                    
                }
                
                
//                measurementList.addAll(Arrays.asList(learner.getModelMeasurements()));
//
//                if (instancesProcessed == 1) { // Checks for drifts in dataset
//                    for (int z = 0; z < measurementList.size(); z++) {
//                        if (measurementList.get(z).getName().equals("Change detected")) {
//                            pos = z;
//                            drift = true;
//                            break;
//                        }
//                    }
//                }

//                
//                
//                if (drift) {
//                	
//                    toleranceArea = false;
//                    if (curr < sz) {
//                        middlePos = (int)(widths.get(curr)*0.5);
//                        wt = middlePos + this.toleranceOption.getValue();
//                        realPos = (int) instancesProcessed;
//                        if (positions.get(curr) <= realPos && realPos < positions.get(curr) + wt) {
//                            toleranceArea = true;
//                            
//                            if (measurementList.get(pos).getValue() == 1.0) { // Drift
//                                CD.add((double) realPos);
//                                curr++;
//                                driftp = true;
//                            } else if (realPos+1 >= positions.get(curr) + wt) {
//                                CD.add(-1.0);
//                                fn++;
//                                curr++;
//                            }                            
//                            
//                        }
//                    }
//
//                    if (measurementList.get(pos).getValue() == 1.0 && !toleranceArea) {
//                        fp++;
//                        driftp = true;
//                    }
//                    
//                    
//                    if(driftp)
//                    {
//                    	driftp = false;
//                    	
//                            
//                        //ROHGI
//                        if (positions.size() > 0) {
//                        	//Cast to int...
//                        	assessDriftIssues((int)instancesProcessed);
//                        }
//                       
//                    	
//                    	
////                    	int windowSize = 0;     
//                        
////                        if(inw > ws)
////                        {
////                        	windowSize = ws;
////                        }
////                        else
////                        {
////                        	windowSize = inw;
////                        }
//                		
////                		contador++;
//                		double err = 0;
////                		double p = 0;
//                		
//                		if(cs > 0)
//                		{
//                			err = ((double) cs / ws);
////                			System.out.println("numberInstances = "+err+", cs = "+cs+", ws = "+ws);
////                			System.exit(0);
//////                			p = entropy(err);
//                		}
//                    	
////                    	probabilidadeMedia += p;//+numberInstances;
////                    	
////                    	double q = (probabilidadeMedia);// / contador;
////                    	//q /= repeat;
////                    	
////                    	probabilidadeMediaFinal = q;
//                		entropyValue += entropy3(err);
//                		
//                    	//detectDrifts[indexDetectDrift] = err;
//                    	//indexDetectDrift++;
//                    	//if(indexDetectDrift > detectDrifts_length)
//                    	//{
//                    	//	indexDetectDrift = 0;
//                    	//}
//                    	
////                    	System.out.println("numberInstances = "+numberInstances+", inw = "
////                      + inw+", cs = "+cs 
////                      +", contador = "+contador
////                      +", ws = "+ws+", p = "+(1 - err)+", q = "+(err) );      
//                    	
////                    	System.out.println("numberInstances = "+numberInstances+", inw = "
////                                + inw+", cs = "+cs 
////                                +", repeat = "+repeat
////                                +", contador = "+contador
////                                +", ws = "+ws+", p = "+p+", q = "+q +", errors = "+(err) );                  	
//                    	
//                    	                    			
//                    	//pointsDrifts.add(numberInstances);
////                        inw = 0;
//                		ci = cs = 0;    	
//                        Arrays.fill(sp, 0); 
//                    }
//                }

//                measurementList.clear();

                if (instancesProcessed % this.sampleFrequencyOption.getValue() == 0
                        || stream.hasMoreInstances() == false) {
                    long evaluateTime = TimingUtils.getNanoCPUTimeOfCurrentThread();
                    double time = TimingUtils.nanoTimeToSeconds(evaluateTime - evaluateStartTime);
                    double timeIncrement = TimingUtils.nanoTimeToSeconds(evaluateTime - lastEvaluateStartTime);
                    double RAMHoursIncrement = learner.measureByteSize() / (1024.0 * 1024.0 * 1024.0); //GBs
                    RAMHoursIncrement *= (timeIncrement / 3600.0); //Hours
                    RAMHours += RAMHoursIncrement;
                    lastEvaluateStartTime = evaluateTime;
                    learningCurve.insertEntry(new LearningEvaluation(
                            new Measurement[]{
                                new Measurement(
                                        "learning evaluation instances",
                                        instancesProcessed),
                                new Measurement(
                                        "evaluation time ("
                                        + (preciseCPUTiming ? "cpu "
                                                : "") + "seconds)",
                                        time),
                                new Measurement(
                                        "model cost (RAM-Hours)",
                                        RAMHours)
                            },
                            evaluator, learner));

                    if (immediateResultStream != null) {
                        if (firstDump) {
                            immediateResultStream.println(learningCurve.headerToString());
                            firstDump = false;
                        }
                        immediateResultStream.println(learningCurve.entryToString(learningCurve.numEntries() - 1));
                        immediateResultStream.flush();
                    }
                    
                    accuracy += evaluator.getPerformanceMeasurements()[1].getValue();
                    ctime += time;
                    memory += RAMHours*1024*1024*1024; //Bytes/s
                    frequency++;
                }
                if (instancesProcessed % INSTANCES_BETWEEN_MONITOR_UPDATES == 0) {
                    if (monitor.taskShouldAbort()) {
                        return null;
                    }
                    long estimatedRemainingInstances = stream.estimatedRemainingInstances();
                    if (maxInstances > 0) {
                        long maxRemaining = maxInstances - instancesProcessed;
                        if ((estimatedRemainingInstances < 0)
                                || (maxRemaining < estimatedRemainingInstances)) {
                            estimatedRemainingInstances = maxRemaining;
                        }
                    }
                    monitor.setCurrentActivityFractionComplete(estimatedRemainingInstances < 0 ? -1.0
                            : (double) instancesProcessed
                            / (double) (instancesProcessed + estimatedRemainingInstances));
                    if (monitor.resultPreviewRequested()) {
                        monitor.setLatestResultPreview(learningCurve.copy());
                    }
                    secondsElapsed = (int) TimingUtils.nanoTimeToSeconds(TimingUtils.getNanoCPUTimeOfCurrentThread()
                            - evaluateStartTime);
                }
            }
            if (immediateResultStream != null) {
                immediateResultStream.close();
            }
            if (outputPredictionResultStream != null) {
                outputPredictionResultStream.close();
            }
            
            tp = stream.getDriftPositions().size()-fn;
            tn = (double)instancesProcessed-(double)stream.getDriftPositions().size()-fp;
            
            meanAccuracy.add(accuracy/frequency);
            meanTime.add(ctime/frequency);
            meanMemory.add(memory/frequency);
            FP.add(fp);
            FN.add(fn);
            TP.add(tp);
            TN.add(tn);
            
//            double mcc = this.MCC(fp, fn, tp, tn);
            
            measureMCC.add(this.MCC(fp, fn, tp, tn));
            
            double [] measureAux = lazyMeasureMDR();            
            double MDR = (measureAux[0] / measureAux[1]);
            
            measureAux = lazyMeasureMTD();            
            double MTD = ((measureAux[0]) / measureAux[1]);
            
        	measureAux = lazyMeasureMTFA();            
            double MTFA = (measureAux[0] / measureAux[1]);                        
            
            double MTR = 0;
            
           // if(MTD != 0) {
            	MTR = (1 - (MDR))*(MTFA)/(MTD);
            //}
            
            measureMDR.add(MDR);
            measureMTD.add(MTD);
            measureMTFA.add(MTFA);
            measureMTR.add(MTR);
            
            //double w = entropy2(detectDrifts);
            
           //if(w == 0)
           //{
            	meanDissimilarity.add(entropyValue);
           // }
            //else
            //{
            //	meanDissimilarity.add(((double)w)); ///contador
            //}
            
            //Accuracy = TP+TN/TP+FP+FN+TN
            //tn = stream.getDriftPositions().size();
            //meanTP.add(((double) (tp+tn)/(tp+fp+fn+tn))*100);
            
            //Accuracy = TP/TP+FN = Precision
            meanTP.add(((double) tp/(tp+fp))*100);
            
          //System.out.println(this.measuresDriftDebug());
            
            //System.out.println("frequency="+w);
            //stream.getDriftPositions().size());//probabilidadeMediaFinal / stream.getDriftPositions().size());
            
            
            if ( !this.changeSeedAutoOption.isSet() )
            {
                break;
            }
        }
        
        InstanceStreamGenerators stream = (InstanceStreamGenerators) getPreparedClassOption(this.streamOption);
        
        printStatistics(meanAccuracy,"Accuracy");
        printStatistics(meanTime,"Time");
        printStatistics(meanMemory,"Memory (B/s)");
        printStatistics3(meanDissimilarity,"Dissimilarity");
        printStatistics(meanTP,"Accuracy Detection");
        printStatistics(measureMDR,"MDR");
        printStatistics(measureMTD,"MTD");
        printStatistics(measureMTFA,"MTFA");
        printStatistics(measureMTR,"MTR");
        printStatistics(measureMCC,"MCC");
        
        lineValues += printDistanceDriftTable(CD, stream.getDriftPositions());
        lineValues += printMetrics1(FN,FP,TN,TP);
        lineValues += printMetrics2(FN,FP,TN,TP);
        lineValues += printMetrics3(measureMDR, measureMTD, measureMTFA, measureMTR);
                
        System.out.println("Mean Distance\t\tFN\tFP\tTN\t\tTP\tPrecision\tRecall\t\tMCC\t\tF1\t\tMDR\tMTFA\tMTD\tMTR");
        System.out.println(lineValues);
        
        
//        //printStatistics4(pointsDrifts,"Drifts");
//        lineValues += printDistanceDriftTable(CD, stream.getDriftPositions());
//        lineValues += printMetrics1(FN,FP,TN,TP);
//        lineValues += printMetrics2(FN,FP,TN,TP);        
//                
//        System.out.println("Mean Distance\t\tFN\tFP\tTN\t\tTP\tPrecision\tRecall\t\tMCC\t\tF1");
//        System.out.println(lineValues);
//
////        lineValues = printMetrics3(probabilidadeMediaFinal);
////        System.out.print("Measure of Dissimilarities Detection\t");
////        System.out.println(lineValues);
        


        return learningCurve;
    }
}
