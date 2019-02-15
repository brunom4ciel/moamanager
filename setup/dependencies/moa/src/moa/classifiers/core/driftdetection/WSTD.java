/*
 *    WSTD.java - Wilcoxon rank sum test drift detector 
 *    Copyright (C) 2016 Barros, Hidalgo, Cabral
 *    @authors Roberto S. M. Barros (roberto@cin.ufpe.br)
 *             Juan Isidro González Hidalgo (jigh@cin.ufpe.br)
 *             Danilo Cabral (danilocabral@danilocabral.com.br)
 *    @version $Version: 1 $
 *    
 *    Evolved from STEPD.java
 *    Copyright (C) 2015 Santos, Barros
 *    @authors Silas Garrido T. de Carvalho Santos (sgtcs@cin.ufpe.br)
 *             Roberto S. M. Barros (roberto@cin.ufpe.br) 
 *    @version $Version: 3 $
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
 * Wilcoxon rank sum test drift detector
 * published as:
 * <p> Roberto S. M. Barros, Juan I. G. Hidalgo, and Danilo R. L. Cabral, 
 *     Wilcoxon rank sum test drift detector 
 *     Neurocomputing 275C (2018) pp. 1954-1963. </p>
 *     DOI: 10.1016/j.neucom.2017.10.051
 */

package moa.classifiers.core.driftdetection;

import moa.core.ObjectRepository;
import moa.options.IntOption;
import moa.options.FloatOption;
import moa.tasks.TaskMonitor;
import weka.core.Statistics;

public class WSTD extends AbstractChangeDetector {
    private static final long serialVersionUID = -3518369648142099722L;
    
    public IntOption windowSizeOption = new IntOption("windowSize", 
            'r', "Recent Window Size.",
            30, 0, 1000);
        
    public FloatOption alphaDriftOption = new FloatOption("alphaDrift",
            'o', "Drift Significance Level.", 0.003, 0.0, 1.0);

    public FloatOption alphaWarningOption = new FloatOption("alphaWarning",
            'w', "Warning Significance Level.", 0.05, 0.0, 1.0);

    public IntOption maxOlderWindowSizeOption = new IntOption("maxOldWindowSize", 
            'm', "Maximum Older Window Size.",
            500, 30, 10000);
        

    private int windowSize, maxOldWinSize;
    private double alphaDrift, alphaWarning;
    
    private byte [] storedPredictions, oldStoredPreds;
    private int firstPos, lastPos, oldFirstPos, oldLastPos;
    
    private int no, nr, aux, m_n;
    private double ro, rr, wo, wr, p, z;
    private double rTotal, rRanks, wRanks, sumRec, sumOld, sumSmaller, m_p;

    public void initialize() {
    	windowSize = this.windowSizeOption.getValue();
    	alphaDrift = this.alphaDriftOption.getValue();
    	alphaWarning = this.alphaWarningOption.getValue();
    	maxOldWinSize = this.maxOlderWindowSizeOption.getValue();
        storedPredictions = new byte[windowSize];
        oldStoredPreds = new byte[maxOldWinSize];
        resetLearning();
    }

    @Override
    public void resetLearning() {
    	firstPos = oldFirstPos = 0;
    	lastPos = oldLastPos = -1;   // This means both arrays are empty.
        m_p = 1.0;
        m_n = 1;
        no = nr = 0;
        wo = wr = 0.0;
        this.isChangeDetected = false;
    }

    @Override
    public void input(double prediction) {   // In MOA, 1.0=false, 0.0=true.
        if (!this.isInitialized) {
            initialize();
            this.isInitialized = true;
        } else {
            if (this.isChangeDetected) {
                resetLearning();
            }
        }

        m_p = m_p + (prediction - m_p) / (double) m_n;
        m_n++;

        this.estimation = m_p;
        this.isWarningZone = false;
        this.delay = 0;

        if (nr == windowSize) {   // Recent window is full.

            if (no == maxOldWinSize) {   // Older window is full.
                wo = wo - oldStoredPreds[oldFirstPos];
                oldFirstPos++;   // Start of older window moves.
                if (oldFirstPos == maxOldWinSize) {
            	    oldFirstPos = 0;
                }
            } else {   // Older window grows.
                no++;
            }

            // Oldest prediction in recent window is added to the older window.
            oldLastPos++;
            if (oldLastPos == maxOldWinSize) {
                oldLastPos = 0;
            };
            oldStoredPreds[oldLastPos] = storedPredictions[firstPos];
            wo += oldStoredPreds[oldLastPos]; 

            wr = wr - storedPredictions[firstPos];
            firstPos++;   // Start of recent window moves.
            if (firstPos == windowSize) {
            	firstPos = 0;
            }
        } else {   // Recent window grows.
            nr++;
        }
        
        lastPos++;   // Adds prediction at the end of recent window.
        if (lastPos == windowSize) {
            lastPos = 0;
        };
        storedPredictions[lastPos] = (byte) prediction;
        wr += prediction;

        if (no >= windowSize) {   // The same as: (no + nr) >= 2 * windowSize.
            ro = no - wo;  // Number of correct predictions in older  window
            rr = nr - wr;  // Number of correct predictions in recent window

            // Wilcoxon test calculation

            // Simplified calculation of ranks for the Wilcoxon test
            rTotal = ro + rr;  // Total number of  correct  predictions
            rRanks = (1.0 + rTotal) / 2.0;  // Ranks of correct predictions
            wRanks = rTotal + ((1.0 + wo + wr) / 2.0);  // Ranks of wrong predictions


            // Calculation of smaller Weighted sum for the Wilcoxon test
            sumOld = (rRanks * ro) + (wRanks * wo);  // Weighted sum of older  window predictions
            sumRec = (rRanks * rr) + (wRanks * wr);  // Weighted sum of recent window predictions

            if (sumOld < sumRec) {
                sumSmaller = sumOld;
       	    } else {
                sumSmaller = sumRec;
            }

            // Calculation of pvalue        
            aux = no + nr + 1;
            z = (sumSmaller - (nr * aux / 2.0)) / Math.sqrt(no * nr * aux / 12.0);
            p = Statistics.normalProbability(Math.abs(z));
            p = 2 * (1 - p); 
 
            if (p < alphaDrift) {   // Detections of Drifts and Warnings
                this.isChangeDetected = true;
            } else { 
            	if (p < alphaWarning) {
                    this.isWarningZone = true;
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
