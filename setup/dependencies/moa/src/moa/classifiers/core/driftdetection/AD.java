/*
 *    AD.java 
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
 * All Detectors Detect Drift (AD)
 * published as: 
 *     Woźniak M., Ksieniewicz P., Cyganek B., Walkowiak K.: 
 *     Ensembles of Heterogeneous Concept Drift Detectors - Experimental Study
 *     In: Saeed K., Homenda W. (eds) Computer Information Systems and Industrial Management. 
 *     CISIM 2016. Lecture Notes in Computer Science, vol 9842. Springer, Cham
 *     DOI: https://doi.org/10.1007/978-3-319-45378-1_48
 * 
 */

package moa.classifiers.core.driftdetection;
import moa.core.ObjectRepository;
import moa.options.StringOption;
import moa.options.ClassOption;
import moa.tasks.TaskMonitor;

public class AD extends AbstractChangeDetector {

    private static final long serialVersionUID = -3518369648142099719L;

    public StringOption DetectorsOption = new StringOption("detectors", 'd',
            "detectors - comma separated values", "HDDM_A_Test,HDDM_W_Test,DDM");  
           
    private int driftLevel = 0;   
    private int numChangeDetectorsDecision = 0;
    protected ChangeDetector[] changeDetectorPool;   

    public AD()
    {
    	ensemble();
    	
    	numChangeDetectorsDecision = changeDetectorPool.length;
        
        resetLearning();     
        
        System.out.println("" + 
  			this.getClass().getSimpleName()
  			+ " - Parameters: "
  			+ "drift detection method to use -d (" + DetectorsOption.getValue() +")"
            );  
        
    }
    
    @Override
    public void resetLearning() {
        this.isChangeDetected = false;
    }        
    
    @Override
    public void input(double prediction) {  //	AD      
        driftLevel = 0;
        
        for(ChangeDetector driftDetection : changeDetectorPool) {        	
        	driftDetection.input(prediction);      	            		
        	if (driftDetection.getChange()){ 		
        		if(++driftLevel >= numChangeDetectorsDecision){ //drift level        			
                	this.isChangeDetected = true;
        		}
        	}     	
        }

        if(this.isChangeDetected){	        	
        	for(ChangeDetector driftDetection : changeDetectorPool){         		
        		driftDetection.resetLearning();		// reset detector
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
    	
    	numChangeDetectorsDecision = changeDetectorPool.length;
        
        resetLearning();  
        
        System.out.println("" + 
      			this.getClass().getSimpleName()
      			+ " - Parameters: "
      			+ "drift detection method to use -d (" + DetectorsOption.getValue() +")"
                ); 
        
    }
}