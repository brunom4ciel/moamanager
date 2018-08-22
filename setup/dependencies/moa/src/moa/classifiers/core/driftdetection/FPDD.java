/*
 *    FPDD.java
 *    Copyright (C) 2017 Cabral, Barros
 *    @authors Danilo R. L. Cabral (drlc@cin.ufpe.br)
 *             Roberto S. M. Barros (roberto@cin.ufpe.br) 
 *    @version $Version: 1 $
 *
 *    Evolved from STEPD.java
 *    Copyright (C) 2015 Santos, Barros
 *    @authors Silas Garrido T. de Carvalho Santos (sgtcs@cin.ufpe.br)
 *             Roberto S. M. Barros (roberto@cin.ufpe.br) 
 *    @version $Version: 3 $
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
 * Fisher Proportions Drift Detector (FPDD)
 * published as:
 * <p> Danilo R. L. Cabral and Roberto S. M. Barros
 *     Concept drift detection based on Fisher's Exact test 
 *     Information Sciences 442-443C (2018) pp. 220-234.
 *     DOI: https://doi.org/10.1016/j.ins.2018.02.054
 *     
 * Inspired in STEPD method, published as: 
 * <p> Kyosuke Nishida and Koichiro Yamauchi: 
 *     Detecting Concept Drift Using Statistical Testing. 
 *     Discovery Science 2007, Springer, vol 4755 of LNCS, pp. 264-269. </p>
 */

package moa.classifiers.core.driftdetection;

import moa.core.ObjectRepository;
import moa.options.IntOption;
import moa.options.FloatOption;
import moa.tasks.TaskMonitor;
import weka.core.Statistics;

public class FPDD extends AbstractChangeDetector {

    private static final long serialVersionUID = -3518369648142099802L;

    public IntOption windowSizeOption = new IntOption("windowSize",
            'r', "Window Size.",
            30, 0, 1000);

    public FloatOption alphaDriftOption = new FloatOption("alphaDrift",
            'o', "Drift Significance Level.", 
            0.003, 0.0, 1.0);

    public FloatOption alphaWarningOption = new FloatOption("alphaWarning",
            'w', "Warning Significance Level.", 
            0.05, 0.0, 1.0);

    private int windowSize;
    private double alphaDrift, alphaWarning;

    private byte[] storedPredictions;
    private int firstPosition, lastPosition;

    private int wo, wr, rr, wp, rp;
    private int no, nr, np;
    private double[] factorial;
    private int maximum, i;
    private double f;
    private double p0, p;
    private double Z;

    public void initialize() {
        windowSize = this.windowSizeOption.getValue();
        alphaDrift = this.alphaDriftOption.getValue();
        alphaWarning = this.alphaWarningOption.getValue();
        storedPredictions = new byte[windowSize];
        resetLearning();
        maximum = 2 * windowSize;
        factorial = new double[maximum + 1];
        f = 1;
        factorial[0] = 1;
        for (i = 1; i <= maximum; i++) {
            f = f * i;
            factorial[i] = f;
        }
        p0 = Math.pow(factorial[windowSize], 2);
//        System.out.println("FPDD - Parameters: Window Size = " + windowSize + " | Alpha Drift = " + alphaDrift + " | Alpha Warning = " + alphaWarning + ".");
    }

    @Override
    public void resetLearning() {
        firstPosition = 0;
        lastPosition = -1;   // This means storedPredictions is empty.
        wo = wr = 0;
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

        if (nr == windowSize) {   // Recent window is full.
            wo = wo + storedPredictions[firstPosition];  // Oldest prediction in recent window.
            no++;                                   // is moved to old window.
            wr = wr - storedPredictions[firstPosition];
            firstPosition++;   // Start of recent window moves.
            if (firstPosition == windowSize) {
                firstPosition = 0;
            }
        } else {   // Recent window grows.
            nr++;
        }

        lastPosition++;   // Adds prediction at the end of recent window.
        if (lastPosition == windowSize) {
            lastPosition = 0;
        }
        storedPredictions[lastPosition] = (byte) prediction;
        wr += (int) prediction;

        this.isWarningZone = false;

        if (no >= windowSize) {   // The same as: (no + nr) >= 2 * windowSize.

            wp = Math.round((wo * nr) / no);
            np = nr;

            rr = nr - wr;
            rp = np - wp;
            
            if (wr < 5 || rr < 5 || wp < 5 || rp < 5) {
                p = factorial[wr + wp] / factorial[wr] / factorial[wp] * factorial[rr + rp] / factorial[rr] / factorial[rp];
                p = p * p0 / factorial[maximum];
                
                p = 2 * p; // Two tailed test.
            } else {
                Z = wp + wr;
                Z = (Math.abs(wr - wp) - 1) / Math.sqrt(Z * (maximum - Z) / maximum);

                p = Statistics.normalProbability(Math.abs(Z));
                p = 2 * (1 - p);
            }

            if (p < alphaDrift) {
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
