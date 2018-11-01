/*
 *    DDE.java
 *    Copyright (C) 2017 Maciel, Barros 
 *    @authors Bruno I. F. Maciel (bifm@cin.ufpe.br)
 *             	Roberto S. M. Barros (roberto@cin.ufpe.br) 
 *             
 *    @version $Version: 1 $
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
 */
package moa.classifiers.core.driftdetection;
import java.util.Arrays;

import moa.core.ObjectRepository;
import moa.options.IntOption;
import moa.options.StringOption;
import moa.options.ClassOption;
import moa.tasks.TaskMonitor;

/**
 * DDE.M1: Drift Detection Ensemble Method 1
 * published as:
 * <p>Bruno I. F. Maciel and Roberto S. M. Barros</p>
 * 
 * Inspired in DDE method, published as: 
 * <p> Bruno I. F. Maciel, Silas Garrido T. C. Santos and Roberto S. M. Barros: 
 *     A Lightweight Concept Drift Detection Ensemble. 
 *     27th IEEE International Conference on Tools with Artificial Intelligence 
 *     (ICTAI) Vietri sul Mare, Italy, November 9-11, 2015</p>
 * 
 * @author Bruno Iran Ferreira Maciel (bifm@cin.upe.br)
 * @author Roberto Souto Maior Barros (roberto@cin.ufpe.br)
 * @version $Revision: 1 $
 */
public class DDE extends AbstractChangeDetector {

    private static final long serialVersionUID = -3518369648142099719L;
    
    public IntOption maxValueOption = new IntOption(
            "maxValue",
            'x',
            "Max Value",
            100, 0, Integer.MAX_VALUE);
    
    public StringOption DetectorsOption = new StringOption("detectors", 'd',
            "detectors - comma separated values", "HDDM_A_Test,HDDM_W_Test,DDM");    
            
    public IntOption driftLevelOption = new IntOption("sensibility",
            's', "Number of detectors needed to identify warning or drift.", 1, 1, 5);
                
    protected int outlier = 300;    
    protected int [] result;
    private double driftLevel = 0;
    private double warningLevel = 0;      
    protected int instNumber, index;    
    private double minDriftWeight = 0;           
    protected ChangeDetector[] changeDetectorPool;    
        
    public DDE()
    {
    	ensemble();
    	
        result = new int [changeDetectorPool.length];
        minDriftWeight = this.driftLevelOption.getValue();
        outlier =  this.maxValueOption.getValue();
        
        resetLearning();
        
//        System.out.println("" + 
//    			this.getClass().getSimpleName()
//    			+ " - Parameters: "        			
//    			+ " Max Value -x " + outlier 
//    			+ ", Drift detection method to use -d (" + DetectorsOption.getValue() +")"
//                + ", Number of detectors needed to identify warning or drift -s " + minDriftWeight 
//                );
        
    }
    
    @Override
    public void resetLearning() {
    	
    	for(ChangeDetector changeDetector : changeDetectorPool){ 
        	changeDetector.resetLearning();
        } 
    	
    	Arrays.fill(result, 0);
    	
        this.isWarningZone = false;
        this.isChangeDetected = false;
    }        
    

    @Override
    public void input(double prediction) {

    	instNumber++;        
        driftLevel = 0;
        warningLevel = 0;
        index = 0;
        
        for(ChangeDetector changeDetector : changeDetectorPool)  {        	
        	changeDetector.input(prediction);        	
        	if(result[index] < 1){//not in drift
        		          	            		
            	if (changeDetector.getChange()){//drift
            		result[index] = instNumber;
            		driftLevel += 1; 
            	}else   {
            	    if (changeDetector.getWarningZone()) {//warning
            	    	warningLevel += 1; 
            	    }
            	}
        	}else{//in drift      
        		if(result[index] + outlier < instNumber){//reset drift
        			result[index] = 0;  
        		}else{//in drift
        			driftLevel += 1;
        		}            	
        	}
        	
        	if(driftLevel >= minDriftWeight){//drift
        		break;
        	}
        	index++;
        }
        
        
        if(warningLevel+driftLevel < minDriftWeight){//stable        	
    		this.isWarningZone = false;	
        }else{
    		if(driftLevel >= minDriftWeight){//drift   
    			resetLearning();
    			this.isInitialized = true;
            	this.isChangeDetected = true;            	
//            	System.out.println(instNumber+" DRIFT driftLevel="+driftLevel+", warningLevel="+warningLevel);
        	}else{ 
            	this.isWarningZone = true; 
//            	System.out.println(instNumber+" WARNING driftLevel="+driftLevel+", warningLevel="+warningLevel);
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
    		
        result = new int [changeDetectorPool.length];
        minDriftWeight = this.driftLevelOption.getValue();
        outlier =  this.maxValueOption.getValue();
        
        resetLearning();
        
//        System.out.println("" + 
//    			this.getClass().getSimpleName()
//    			+ " - Parameters: "        			
//    			+ " Max Value -x " + outlier 
//    			+ ", Drift detection method to use -d (" + DetectorsOption.getValue() +")"
//                + ", Number of detectors needed to identify warning or drift -s " + minDriftWeight 
//                );  
    }
}