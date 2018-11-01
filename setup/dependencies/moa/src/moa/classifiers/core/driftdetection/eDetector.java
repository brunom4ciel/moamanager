/*
 *    eDetector.java 
 *    Copyright (C) 2017 Maciel, Barros 
 *    @authors Bruno I. F. Maciel (bifm@cin.ufpe.br)
 *             	Roberto S. M. Barros (roberto@cin.ufpe.br) 
 *             
 *    @version $Version: 1 $
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */

/**
 * A Selective Detector Ensemble for Concept Drift Detection - eDetector
 * published as: 
 *     Du, Lei and Song, Qinbao and Zhu, Lei and Zhu, Xiaoyan:
 *     A Selective Detector Ensemble for Concept Drift Detection.
 *     The Computer Journal, Volume 58, Issue 3, 1 March 2015, Pages 457–471.
 *     DOI: https://doi.org/10.1093/comjnl/bxu050
 * 
 */

package moa.classifiers.core.driftdetection;
import moa.core.ObjectRepository;
import moa.options.StringOption;
import moa.options.ClassOption;
import moa.tasks.TaskMonitor;

public class eDetector extends AbstractChangeDetector {

    private static final long serialVersionUID = -3518369648142099719L;

    public StringOption DetectorsOption = new StringOption("detectors", 'd',
            "detectors - comma separated values", "HDDM_A_Test,HDDM_W_Test,DDM");  
           
    boolean lastWarningZone = false;
    int numberInstance = 0;
    protected ChangeDetector[] changeDetectorPool;  
        
    public eDetector()
    {
        ensemble();
    	
        lastWarningZone = false;
        numberInstance = 0;
        
        resetLearning();     
        
        System.out.println("" + 
  			this.getClass().getSimpleName()
  			+ " - Parameters: "
  			+ "drift detection method to use -d (" + DetectorsOption.getValue() +")"
            );          
    }

    @Override
    public void resetLearning() {
    	super.resetLearning();
    	this.isWarningZone = false;
        this.isChangeDetected = false;
        lastWarningZone = false;
    }    

    @Override
    public void input(double prediction) {  //	E-DETECTOR 
    	this.isWarningZone = false;
    	
        for(ChangeDetector driftDetection : changeDetectorPool) {        	
        	driftDetection.input(prediction);      	            		
        	if (driftDetection.getChange()){	// drift level			
                this.isChangeDetected = true;
        	}else{
        		if (driftDetection.getWarningZone()){	// warning level			
        			this.isWarningZone = true;
            	}
        	}
        }       
        
        if(this.isChangeDetected == true){
        	for(ChangeDetector driftDetection : changeDetectorPool){         		
        		driftDetection.resetLearning();		// reset detector
            } 
    	}else{
    		if(this.isWarningZone == true){
    			lastWarningZone = true;
    		}else{
    			if(lastWarningZone ==  true){ //	step j+1
    				this.isWarningZone = true;
        			lastWarningZone = false;
    			}	
    		}    		
    	}
    }

    public void ensemble()
    {
        
        String valueList = DetectorsOption.getValue();
    	
    	if(!valueList.equals("")){
	    	String[] split = valueList.split(",");    	
	    	if(split.length > 0){
	    		changeDetectorPool = new ChangeDetector[split.length];
		    	
		    	for (int i = 0; i < split.length; i++) {
		    		
		    		changeDetectorPool[i] = ((ChangeDetector) 
        					((ClassOption) new ClassOption("driftDetectionMethod", 'd',
				            "Drift detection method", ChangeDetector.class, split[i]))
        					.materializeObject(null, null)).copy();
		    	}
        	}else{
        		changeDetectorPool = new ChangeDetector[1];

        		changeDetectorPool[0] = ((ChangeDetector) 
    					((ClassOption) new ClassOption("driftDetectionMethod", 'd',
			            "Drift detection method", ChangeDetector.class, valueList))
    					.materializeObject(null, null)).copy();
        		
        	}
    	}
    	
    }
    
    @Override
    public void getDescription(StringBuilder sb, int indent) {
        // TODO Auto-generated method stub
    }

    @Override
    protected void prepareForUseImpl(TaskMonitor monitor,
            ObjectRepository repository) {
        // TODO Auto-generated method stub
        
        ensemble();
    	
        lastWarningZone = false;
        numberInstance = 0;
        
        resetLearning();     
        
        System.out.println("" + 
  			this.getClass().getSimpleName()
  			+ " - Parameters: "
  			+ "drift detection method to use -d (" + DetectorsOption.getValue() +")"
            );  
        
    }
}