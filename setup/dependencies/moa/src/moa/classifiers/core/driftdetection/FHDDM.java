
package moa.classifiers.core.driftdetection;

import moa.core.ObjectRepository;
import moa.options.FloatOption;
import moa.options.IntOption;
import moa.tasks.TaskMonitor;

/**
 *  <p>Fast Hoeffding Drift Detection Method (FHDDM)</p>
 *  <p>A. Pesaranghader, H.L. Viktor, Fast Hoeffding Drift Detection Method for Evolving Data Streams.
 *  In the Proceedings of ECML-PKDD 2016.</p>
 *  
 *  @author Ali Pesaranghader (alipsgh@gmail.com)
 *  @version $Revision: 7 $
 */
public class FHDDM extends AbstractChangeDetector {
	
	private static final long serialVersionUID = -4653946196029696120L;
	
    public IntOption slidingWinSizeOption = new IntOption("slidingWinSize",'s',"The size of Sliding Window.", 25, 0, Integer.MAX_VALUE);
    public FloatOption deltaOption = new FloatOption("delta", 'd', "The confidence level. The default value is E-6.", 0.000001, 0, 1);
    
    public static int win_size;
    public static double delta;
    public static double epsilon;

    private int[] trend;
    private int n_one;
    private double p_max;
    private int cursor;

    public FHDDM() {
        resetLearning();
    }

    @Override
    public void resetLearning() {
        win_size = slidingWinSizeOption.getValue();
        delta = deltaOption.getValue();
        epsilon = Math.sqrt((Math.log(1 / delta)) / (2 * win_size));
        trend = new int[win_size];
        p_max = 0;
        cursor = 0;
    }

    @Override
    public void input(double prediction) {
    	
        if (this.isChangeDetected == true || this.isInitialized == false) {
            resetLearning();
            this.isInitialized = true;
        }
    	
    	boolean drift_status = false;
    	boolean warning_status = false;
    	
    	if(cursor <= win_size){
    		cursor += 1;
    	}
    	
		if (cursor > win_size){
			n_one -= trend[0];
			for(int i = 0; i < win_size; i++){
				if(i == win_size - 1){
					trend[i] = 0;
				} else {
					trend[i] = trend[i + 1];
				}
			}
			cursor -= 1;
		}
			
		if (prediction == 0){
			trend[cursor - 1] = 1;
			n_one += 1;
		} else {
			trend[cursor - 1] = 0;
		}
    	
    	if (cursor >= win_size){
    		double p_one = ((double) n_one) / win_size;
    		if (p_max < p_one){
    			p_max = p_one;
    		}
    		drift_status = (p_max - p_one > epsilon) ? true : false;
    	}
    	
    	this.isWarningZone = warning_status;
    	this.isChangeDetected = drift_status;
    	
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