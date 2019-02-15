/*
 *    ADDM.java
 *    Copyright (C) 2019 Higaldo, Maciel and Barros 
 *    @authors Juan I. G. Hidalgo (jigh@cin.ufpe.br)
 *    			Bruno I. F. Maciel (bifm@cin.ufpe.br)
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

import moa.core.ObjectRepository;
import moa.options.StringOption;
import moa.tasks.TaskMonitor;
 
/**
 * ADDM: Artificial Drift detection method.
 * published as:  
 * <p>Juan I. G Hidalgo, Bruno I. F. Maciel and Roberto S. M. Barros:
 * Experimenting with Prequential Variations for Data Stream Learning Evaluation
 * Wiley</p>
 *  
 * @author Juan I. González Hidalgo (jigh@cin.ufpe.br)
 * @author Bruno Iran Ferreira Maciel (bifm@cin.upe.br)
 * @author Roberto Souto Maior Barros (roberto@cin.ufpe.br)
 * @version $Revision: 1 $
 */
public class ADDM extends AbstractChangeDetector {

    private static final long serialVersionUID = -3518369648142099719L;
    
    public StringOption driftOption = new StringOption("drifts", 'd',
            "drifts points - comma separated values", "");
    
    public StringOption warningOption = new StringOption("warning", 'w',
            "warning points labels - comma separated values", "");    
    
    private int []drift = null; 
    private int instanceNumber;
    private int []warning = null;
    
    public void initialize() {
    	
    	String valueList = driftOption.getValue();
    	
    	if(!valueList.equals(""))
    	{
	    	String[] split = valueList.split(",");    	
	    	if(split.length > 0)
        	{
		    	drift = new int[split.length];
		    	
		    	for (int i = 0; i < split.length; i++) 
		    	{
		    		drift[i] = Integer.parseInt(split[i]);
		    	}
        	}else
        	{
        		drift = new int[1];
        		drift[0] = Integer.parseInt(valueList);
        	}
    	}
    	
    	valueList = warningOption.getValue();
    	
    	if(!valueList.equals(""))
    	{
    		String[] split = valueList.split(",");

        	if(split.length > 0)
        	{    		
        		warning = new int[split.length];
            	
            	for (int i = 0; i < split.length; i++) 
            	{
            		warning[i] = Integer.parseInt(split[i]);
            	}

        	}else
        	{
        		warning = new int[1];
        		warning[0] = Integer.parseInt(valueList);
        	}
        		
    	}

    	resetLearning();
    }
    
    @Override
    public void resetLearning() {    	
    	this.isChangeDetected = false;
    	this.isWarningZone = false;
    }

    @Override
    public void input(double prediction) {
        
    	if (!this.isInitialized) {
            initialize();
            this.isInitialized = true;
        }
    	
        instanceNumber++;
        
        if(this.isWarningZone == false){
        
	        if(warning != null)
	        {
	        	for(int w: warning)
	            {
	            	if(instanceNumber == w)
	            	{
	            		this.isWarningZone = true;		
	            		//System.out.println("WARNING "+instanceNumber);
	            	}
	            }
	        }
        }

        if(drift != null)
        {
	        for(int d: drift)
	        {	        	
	        	if(instanceNumber == d)
	        	{
	        		this.isWarningZone = false;
	        		this.isChangeDetected = true;
	        		//System.out.println("DRIFT "+instanceNumber);
	        	}
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

    }
}
