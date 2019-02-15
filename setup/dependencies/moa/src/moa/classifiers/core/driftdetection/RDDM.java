/*
 *    RDDM.java
 *    Copyright (C) 2016 Barros, Cabral, Gon�alves
 *    @authors Roberto S. M. Barros (roberto@cin.ufpe.br) 
 *             Danilo Cabral (danilocabral@danilocabral.com.br)
 *             Paulo M. Gon�alves Jr. (paulomgj@gmail.com)
 *    @version $Version: 1 $
 *    
 *    Evolved from DDM.java
 *    Copyright (C) 2008 University of Waikato, Hamilton, New Zealand
 *    @author Manuel Baena (mbaena@lcc.uma.es)
 *    @version $Revision: 7 $
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
 * Reactive Drift Detection Method (RDDM) 
 * published as:
 * <p> Roberto S. M. Barros, Danilo R. L. Cabral, Paulo M. Gon�alves Jr.,
 *     and Silas G. T. C. Santos: 
 *     RDDM: Reactive Drift Detection Method. 
 *     Expert Systems With Applications 90C (2017) pp. 344-355.
 *     DOI: 10.1016/j.eswa.2017.08.023
 *
 * Inspired in DDM method, published as: 
 *     Jo�o Gama, Pedro Medas, Gladys Castillo, and Pedro Pereira Rodrigues.
 *     Learning with Drift Detection. 
 *     SBIA 2004, LNCS, Vol. 3171, pp 286-295. 
 *
 */

package moa.classifiers.core.driftdetection;

import moa.core.ObjectRepository;
import moa.options.IntOption;
import moa.options.FloatOption;
import moa.tasks.TaskMonitor;

public class RDDM extends AbstractChangeDetector {
    private static final long serialVersionUID = -489867468386968209L;

    public IntOption minNumInstancesOption = new IntOption("minNumInstances", 
            'n', "Minimum number of instances before monitoring changes.",
            129, 0, Integer.MAX_VALUE);

    public FloatOption warningLevelOption = new FloatOption("warningLevel", 
            'w', "Warning Level.",
            1.773, 1.0, 4.0);

    public FloatOption driftLevelOption = new FloatOption("driftLevel", 
            'o', "Drift Level.",
            2.258, 1.0, 5.0);

    public IntOption maxSizeConceptOption = new IntOption("maxSizeConcept", 
            'x', "Maximum Size of Concept.",
            40000, 1, Integer.MAX_VALUE);

    public IntOption minSizeStableConceptOption = new IntOption("minSizeStableConcept", 
            'y', "Minimum Size of Stable Concept.",
            7000, 1, 20000);
            
    public IntOption warnLimitOption = new IntOption("warnLimit", 
            'z', "Warning Limit of instances",
            1400, 1, 20000);

    private int minNumInstances;
    private double warningLevel;
    private double driftLevel;
    private int maxSizeConcept;
    private int minSizeStableConcept;
    private int warnLimit;
    
    private int m_n;
    private double m_p;
    private double m_s;
    private double m_pmin;
    private double m_smin;
    private double m_psmin;

    private byte [] storedPredictions;
    private int numStoredInstances, firstPos, lastPos, pos, i;
    private int lastWarnInst, lastWarnPos;
    private int instNum;
    private boolean rddmDrift;

    public void initialize() {
        minNumInstances = this.minNumInstancesOption.getValue();
        warningLevel = this.warningLevelOption.getValue();
        driftLevel = this.driftLevelOption.getValue();
        maxSizeConcept = this.maxSizeConceptOption.getValue();
        minSizeStableConcept = this.minSizeStableConceptOption.getValue();
        warnLimit = this.warnLimitOption.getValue();
        storedPredictions = new byte[minSizeStableConcept];
        numStoredInstances = 0;
        firstPos = 0;
        lastPos = -1;   // This means storedPredictions is empty.
        lastWarnPos  = -1;
        lastWarnInst = -1;
        instNum = 0;
        rddmDrift = false;
        this.isChangeDetected = false;
        
        resetLearning();
        m_pmin = Double.MAX_VALUE;
        m_smin = Double.MAX_VALUE;
        m_psmin = Double.MAX_VALUE; 
//        System.out.println("RDDM - Params. MAX=" + maxSizeConcept + " MIN=" + 
//        		minSizeStableConcept + " warnLimit =" + warnLimit);
    }

    @Override
    public void resetLearning() {
        m_n = 1;
        m_p = 1;
        m_s = 0;
        if (this.isChangeDetected) {
            m_pmin = Double.MAX_VALUE;
            m_smin = Double.MAX_VALUE;
            m_psmin = Double.MAX_VALUE;
        }
    }

    @Override
    public void input(double prediction) {   // In MOA, 1.0=false, 0.0=true.
        if (!this.isInitialized) {
            initialize();
            this.isInitialized = true;
        }
        if (rddmDrift) {
            resetLearning();
    	    if (lastWarnPos != -1) {
    	    	firstPos = lastWarnPos;
    	    	numStoredInstances = lastPos - firstPos + 1;
    	    	if (numStoredInstances <= 0) {
    	    	    numStoredInstances += minSizeStableConcept;
    	    	}
    	    } 
    	    
    	    pos = firstPos;
    	    for (i = 0; i < numStoredInstances; i++) {
                m_p = m_p + (storedPredictions[pos] - m_p) / m_n;
                m_s = Math.sqrt(m_p * (1 - m_p) / m_n);
                if (this.isChangeDetected && (m_n > minNumInstances) && (m_p + m_s < m_psmin)) {
                    m_pmin = m_p;
                    m_smin = m_s;
                    m_psmin = m_p + m_s;
                }
                m_n++;
                pos = (pos + 1) % minSizeStableConcept;
            }
    	    
            lastWarnPos = -1;
	    lastWarnInst = -1;
            rddmDrift = false;
            this.isChangeDetected = false;
        }

        lastPos = (lastPos + 1) % minSizeStableConcept;   // Adds prediction at the end of the window.
        storedPredictions[lastPos] = (byte) prediction;
        if (numStoredInstances < minSizeStableConcept) {   // The window grows.
            numStoredInstances++;
        } else {   // The window is full.
            firstPos = (firstPos + 1) % minSizeStableConcept;    // Start of the window moves.
            if (lastWarnPos == lastPos) { 
                lastWarnPos = -1;
            }
        }
	    
        m_p = m_p + (prediction - m_p) / m_n;
        m_s = Math.sqrt(m_p * (1 - m_p) / m_n);

        instNum++;
        m_n++;
        this.estimation = m_p;
        this.isWarningZone = false;

        if (m_n <= minNumInstances) {
            return;
        }

        if (m_p + m_s < m_psmin) {
            m_pmin = m_p;
            m_smin = m_s;
            m_psmin = m_p + m_s;
        }

        if (m_p + m_s > m_pmin + driftLevel * m_smin) {  // DDM Drift
            this.isChangeDetected = true;
            rddmDrift = true;
            if (lastWarnInst == -1) {   // DDM Drift without previous warning
            	firstPos = lastPos;
                numStoredInstances = 1;
            }
    	    return;
        }

        if (m_p + m_s > m_pmin + warningLevel * m_smin) {  // Warning Level
            // Warning level for warnLimit consecutive instances will force drifts
            if ((lastWarnInst != -1) && (lastWarnInst + warnLimit <= instNum)) { 
                this.isChangeDetected = true;
                rddmDrift = true;
                firstPos = lastPos;
                numStoredInstances = 1;
                lastWarnPos = -1;
                lastWarnInst = -1;
                return;
            } 
            // Warning Zone
            this.isWarningZone = true;
            if (lastWarnInst == -1) {
                lastWarnInst = instNum;
                lastWarnPos = lastPos;
            }
        } else {   // Neither DDM Drift nor Warning - disregard false warnings
            lastWarnInst = -1;
            lastWarnPos  = -1;
        }
        if (m_n > maxSizeConcept && (!isWarningZone)) {  // RDDM Drift
            rddmDrift = true;
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
