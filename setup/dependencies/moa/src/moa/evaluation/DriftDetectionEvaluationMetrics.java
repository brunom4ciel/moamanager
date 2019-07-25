package moa.evaluation;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

//import moa.classifiers.Classifier;
import moa.evaluation.DriftDetectionEvaluationMetrics;

public class DriftDetectionEvaluationMetrics extends NamesMetrics{

	/**
	 * 
	 */
	
	public List<Integer> assessDriftIssues = new ArrayList<>();
	
	public double meanAccuracy;
	public double meanTime;
	public double meanMemory;
	public double meanEntropy;
//    public double FP;
//    public double FN;
//    public double TP;
//    public double TN;
    public List<Double> CD = new ArrayList<>();
    public List<Integer> positions;
    public List<Integer> widths;
    public double fp, fn, tp, tn;
    public int curr, sz, wt, middlePos;
    public int tolerance;
    public String lineValues = "";
    private boolean drift = false;
    private boolean driftp = false;
    
    private double entropyValue;
    
    public double getEntropyValue() {
		return entropyValue;
	}

	public void setEntropyValue(double entropyValue) {
		this.entropyValue = entropyValue;
	}


	List<Double> measureMDR = new ArrayList<>();
    List<Double> measureMTD = new ArrayList<>();
    List<Double> measureMTFA = new ArrayList<>();
    List<Double> measureMTR = new ArrayList<>();
    
    
    private long maxInstances;
    
    private int windowSize;
    
    int [] sp;	// stored predictions
    int ci;	// current index
    int cs;	// current sum
//    int ws;// = this.windowSizeOption.getValue(); 	// window size
        
    
//    private final String[] namesMetrics = {"Accuracy","Time","Memory (B/s)"
//    								,"Dissimilarity"
//    								,"MDR","MTD","MTFA","MTR"
//    								,"Precision","Recall","MCC","F1"};
//    private boolean[] defaultsMetrics = {true, true, true
//    								, true
//    								, true, true, true, true
//    								, true, true, true, true}; 
//    
//    public String[] getNames() {        
//        return namesMetrics;
//    }
//    
//    public boolean[] getDefaultEnabled() {
//        return defaultsMetrics;
//    }
    
//    public void setDefaultEnabled(boolean[] defaults) {
//        this.defaultsMetrics = defaults;
//    }
    
	public long getMaxInstances() {
		return maxInstances;
	}

	public void setMaxInstances(long maxInstances) {
		this.maxInstances = maxInstances;		
	}
	
    public void resetMetrics() {
    	setDrift(false);
    	curr = 0;
        fp = fn = 0.0;
        
//        ws = getWindowSize();
        sp = new int[getWindowSize()];
		ci = cs = 0;		 
    }
    
    public int getWindowSize() {
		return windowSize;
	}

	public void setWindowSize(int windowSize) {
		this.windowSize = windowSize;
		sp = new int[windowSize];
	}

	public int getTolerance() {
		return tolerance;
	}

	public void setTolerance(int tolerance) {
		this.tolerance = tolerance;
	}
	
    public boolean isDrift() {
		return drift;
	}

	public void setDrift(boolean drift) {
		this.drift = drift;
	}
	
	private boolean EOF(long instancesProcessed) {
		return (instancesProcessed == this.getMaxInstances()? true:false);
	}
	
    public void input(long instancesProcessed, 
//    		boolean isChangeDetectMethod, 
    		boolean isChangeDetectMethodPosition,
    		int prediction
    		) {//,Double accuracy, Double ctime, Double memory, int frequency) {
		
//    	if(this.isACCURACY()) {
//    		this.meanAccuracy = (accuracy/frequency);
//    	}
//    	
//    	if(this.isTIME()) {
//    		this.meanTime = (ctime/frequency);
//    	}
//    	
//    	if(this.isMEMORY()) {
//    		this.meanMemory  = (memory/frequency);
//    	}
		
		if (isDrift()) {
            boolean toleranceArea = false;
            
            if(this.isENTROPY()) {
            	cs += sp[ci++] = prediction;	 		
        		ci = (ci == getWindowSize() ? 0:ci); //	adjust the index position
        		cs -= sp[ci]; //	decrementer value the sum
            }  		
            
            
            if (curr < sz) {
            	
            	middlePos = (int)(widths.get(curr)*0.5);
                wt = middlePos + getTolerance();// se gradual
                
                if (positions.get(curr) <= instancesProcessed 
                		&& instancesProcessed < positions.get(curr) + wt) {                        	
                    toleranceArea = true;

                    if(isChangeDetectMethodPosition) {
                    	
                    	if(this.isPRECISION() 
                        		||	this.isRECALL()
                        		||	this.isMCC()
                        		||	this.isF1()
                        		||	this.isACCURACY_DETECTION()
//                        		||	this.isKAPPA_DETECTION()
//                        		||	this.isYOUDEN_DETECTION()
                        		||	this.isFN()
                        		||	this.isFP()
                        		||	this.isTN()
                        		||	this.isTP()
                        		||  this.isDRIFT_POINT_DISTANCE()
                            	||  this.isDRIFT_MEANS()
                            	||	this.isDRIFT_GENERAL_MEAN()
                            	||	this.isFN_FP_TN_TP() ) {  
                    		CD.add((double) instancesProcessed);
                    	}
                    	
                        curr++;
                        driftp = true;
                    } else if (instancesProcessed+1 >= positions.get(curr) + wt) {
                    	
                    	if(this.isPRECISION() 
                        		||	this.isRECALL()
                        		||	this.isMCC()
                        		||	this.isF1()
                        		||	this.isACCURACY_DETECTION()
//                        		||	this.isKAPPA_DETECTION()
//                        		||	this.isYOUDEN_DETECTION()
                        		||	this.isFN()
                        		||	this.isFP()
                        		||	this.isTN()
                        		||	this.isTP()
                        		||  this.isDRIFT_POINT_DISTANCE()
                            	||  this.isDRIFT_MEANS()
                            	||	this.isDRIFT_GENERAL_MEAN()
                            	||	this.isFN_FP_TN_TP() ) {  
                    		CD.add(-1.0);
                    		fn++;
                    	}
                    	
                        curr++;
                    }                    
                }
            }

            
            
            if (isChangeDetectMethodPosition == true && !toleranceArea) {
            	
            	if(this.isPRECISION() 
                		||	this.isRECALL()
                		||	this.isMCC()
                		||	this.isF1()
                		||	this.isACCURACY_DETECTION()
//                		||	this.isKAPPA_DETECTION()
//                		||	this.isYOUDEN_DETECTION()
                		||	this.isFN()
                		||	this.isFP()
                		||	this.isTN()
                		||	this.isTP()
                		||  this.isDRIFT_POINT_DISTANCE()
                    	||  this.isDRIFT_MEANS()
                    	||	this.isDRIFT_GENERAL_MEAN()
                    	||	this.isFN_FP_TN_TP() ) {  
            		fp++;
            	}
                driftp = true;
            }
            
            if (isChangeDetectMethodPosition == true) {
            	if(this.isMDR()
                		||	this.isMTD()
                		||	this.isMTFA()
                		||	this.isMTR()) {  
            		
            		//ROHGI
                    if (positions.size() > 0) {
                    	//Cast to int...
                    	assessDriftIssues.add((int)instancesProcessed);
                    }                    
            	}
            }
            
//            if (learner.isChangeDetectMethodPosition() == true) {
//            	driftp = true;
//            }
            
            if(driftp)
            {
            	driftp = false;
            	                        
//            	if(this.isMDR()
//                		||	this.isMTD()
//                		||	this.isMTFA()
//                		||	this.isMTR()) {  
//            		
//            		//ROHGI
//                    if (positions.size() > 0) {
//                    	//Cast to int...
//                    	assessDriftIssues.add((int)instancesProcessed);
//                    }
//                    
//            	}
                
                
                
                if(this.isENTROPY()) {
                	double err = 0;
            		
            		if(cs > 0)
            		{
            			err = ((double) cs / getWindowSize());
            		}

            		entropyValue += entropy3(err);

            		ci = cs = 0;    	
                    Arrays.fill(sp, 0); 
                }
               
                
                
                
            }
            
            
            if(this.EOF(instancesProcessed)) {
            	if(this.isPRECISION() 
                		||	this.isRECALL()
                		||	this.isMCC()
                		||	this.isF1()
                		||	this.isACCURACY_DETECTION()
                		||	this.isFN()
                		||	this.isFP()
                		||	this.isTN()
                		||	this.isTP()
                		||  this.isDRIFT_POINT_DISTANCE()
                    	||  this.isDRIFT_MEANS()
                    	||	this.isDRIFT_GENERAL_MEAN()
                    	||	this.isFN_FP_TN_TP()            			
        			) {
                		
                	this.tp = (this.positions.size()-this.fn);
                	this.tn = ((double)this.getMaxInstances()-(double)this.positions.size()-this.fp);

            	}
            }
            
        }
		
		
		
    	
		
	}

    /*
	 * end data stream
	 * 
	 */
    public void end(Double accuracy, Double ctime, Double memory, int frequency) {
    			
		if(this.isACCURACY()) {
    		this.meanAccuracy = (accuracy/frequency);
    	}
    	
    	if(this.isTIME()) {
    		this.meanTime = (ctime/frequency);
    	}
    	
    	if(this.isMEMORY()) {
    		this.meanMemory  = (memory/frequency);
    	}
    	
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
    
    


}
