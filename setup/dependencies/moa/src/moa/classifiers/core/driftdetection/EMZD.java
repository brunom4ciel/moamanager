/*
 *    EMZD.java
 *    Copyright (C) 2016 Cabral, Barros
 *    @authors Danilo R. L. Cabral (drlc@cin.ufpe.br)
 *    Roberto S. M. Barros (roberto@cin.ufpe.br)
 *    @version $Version: 1 $
 *   
 *    Evolved from EDDM.java
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
 * Equal Means Z-Test Concept Drift Detector (EMZD): published as:
 * <p>
 * Danilo R. L. Cabral and Roberto S. M. Barros: 
 *     ... detalhes da publicacao... </p>
 *
 * Inspired in EDDM method, published as: 
 *     Manuel Baena-Garcia, Jose Del Campo-Avila, Raul Fidalgo, 
 *     Albert Bifet, Ricard Gavalda, Rafael Morales-Bueno. 
 *     Early Drift Detection Method. 
 *     In Fourth International Workshop on Knowledge Discovery
 *     from Data Streams, 2006, pp 77-86.
 *
 */

package moa.classifiers.core.driftdetection;

import moa.core.ObjectRepository;
import moa.tasks.TaskMonitor;
import moa.options.IntOption;
import moa.options.FloatOption;
import weka.core.Statistics;

public class EMZD extends AbstractChangeDetector {
    
    private static final long serialVersionUID = -3518369648142099801L;

    public IntOption minNumInstancesOption = new IntOption("minNumInstances",
            'n', "The minimum number of instances before permitting detecting change.",
            30, 0, Integer.MAX_VALUE);

    public IntOption minNumErrorsOption = new IntOption("minNumErrors",
            'e', "The minimum number of errors before permitting detecting change.",
            30, 0, Integer.MAX_VALUE);

    public FloatOption alphaDriftOption = new FloatOption("alphaDrift",
            'o', "Drift Significance Level.", 
            0.22, 0.0, 1.0);

    public FloatOption alphaWarningOption = new FloatOption("alphaWarning",
            'w', "Warning Significance Level.", 
            0.25, 0.0, 1.0);

    private int instances, actualError, lastError;

    private double errors, distance, squaredDistance, meanErrors, maxMeanErrors, varianceErrors, maxVarianceErrors, maxErrors, Z;

    public EMZD() {
        resetLearning();
    }

    @Override
    public void resetLearning() {
        instances = 0;
        errors = 0.0;
        actualError = 0;
        distance = 0.0;
        squaredDistance = 0.0;
        maxMeanErrors = 0.0;

        this.estimation = 0.0;
    }

    @Override
    public void input(double prediction) {
        if (this.isChangeDetected || !this.isInitialized) {
            resetLearning();
            this.isInitialized = true;
            this.isChangeDetected = false;
        }

        this.isWarningZone = false;

        instances++;

        if (prediction == 1.0) {
            errors += 1.0;
            lastError = actualError;
            actualError = instances;

            distance = distance + (actualError - lastError);
            squaredDistance = squaredDistance + Math.pow((actualError - lastError), 2);

            meanErrors = distance / errors;
            this.estimation = meanErrors;
            this.delay = 0;

            if (errors > 1.0) {
                varianceErrors = (errors * squaredDistance - Math.pow(distance, 2)) / (errors * (errors - 1));
            } else {
                varianceErrors = 0.0;
            }

            if (instances > this.minNumInstancesOption.getValue()) {
                if (meanErrors > maxMeanErrors) {
                    maxMeanErrors = meanErrors;
                    maxVarianceErrors = varianceErrors;
                    maxErrors = errors;
                } else if (errors > this.minNumErrorsOption.getValue()) {
                    Z = Math.abs((maxMeanErrors - meanErrors) / Math.sqrt(varianceErrors / errors + maxVarianceErrors / maxErrors));
                    Z = Statistics.normalProbability(Math.abs(Z));
                    Z = 1 - Z;

                    if (Z < this.alphaDriftOption.getValue()) {
                        this.isChangeDetected = true;
                    } else if (Z < this.alphaWarningOption.getValue()) {
                        this.isWarningZone = true;
                    }
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
