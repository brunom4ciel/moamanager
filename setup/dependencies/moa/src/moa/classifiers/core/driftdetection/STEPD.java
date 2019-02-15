/*
 *    STEPD.java
 *    Copyright (C) 2015 Santos, Barros
 *    @authors Silas Garrido T. de Carvalho Santos (sgtcs@cin.ufpe.br)
 *             Roberto S. M. Barros (roberto@cin.ufpe.br) 
 *    @version $Version: 2 $
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

/**
 * Statistical Test of Equal Proportions method (STEPD), 
 * published as:
 * <p> Kyosuke Nishida and Koichiro Yamauchi: 
 *     Detecting Concept Drift Using Statistical Testing. 
 *     Discovery Science 2007: 264-269. </p>
 */

package moa.classifiers.core.driftdetection;

import moa.core.ObjectRepository;
import moa.options.IntOption;
import moa.options.FloatOption;
import moa.tasks.TaskMonitor;
import weka.core.Statistics;

public class STEPD extends AbstractChangeDetector {
    private static final long serialVersionUID = -3518369648142099719L;
    
    public IntOption windowSizeOption = new IntOption("windowSize", 
            'r', "Recent Window Size.",
            30, 0, 1000);
        
    public FloatOption alphaDOption = new FloatOption("AlphaD",
            'o', "Drift Significance Level.", 0.003, 0.001, 0.05);
    
    public FloatOption alphaWOption = new FloatOption("AlphaW",
            'w', "Warning Significance Level.", 0.05, 0.01, 0.2);

    private int windowSize;
    private double alphaDrift, alphaWarn;
    
    private byte [] storedPredictions;
    private int firstPos, lastPos;
    
    private double ro, rr;
    private int m_n, no, nr;
    private double m_p, p, Z, pValue, sizesInvSum;

    public void initialize() {
    	windowSize = this.windowSizeOption.getValue();
    	alphaDrift = this.alphaDOption.getValue();
    	alphaWarn = this.alphaWOption.getValue();
        storedPredictions = new byte[windowSize];
        resetLearning();
    }

    @Override
    public void resetLearning() {
    	firstPos = 0;
    	lastPos = -1;   // This means storedPredictions is empty.
        m_p = 1.0;
        m_n = 1;
        ro = rr = 0.0;
        no = nr = 0;
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
        	ro = ro + storedPredictions[firstPos];  // Oldest prediction in recent window 
            no++;                                   // is moved to old window,
            rr = rr - storedPredictions[firstPos];
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
        }
        storedPredictions[lastPos] = (byte) prediction;
        rr += prediction;
        
        if (no >= windowSize) {   // The same as: (no + nr) >= 2 * windowSize.
            sizesInvSum = 1.0/no + 1.0/nr;
            p = (ro + rr) / (no + nr);
            Z = Math.abs(ro/no - rr/nr);
            Z = Z - sizesInvSum / 2;
            Z = Z / Math.sqrt(p * (1.0-p) * sizesInvSum);
            
            Z = Statistics.normalProbability(Math.abs(Z));
            pValue = 2 * (1 - Z);
            
            if (pValue < alphaDrift) {
                this.isChangeDetected = true;
            } else { 
            	if (pValue < alphaWarn) {
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