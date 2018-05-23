/*
 *    AdaptableDiversityBasedOnlineBoosting.java
 *    Copyright (C) 2014 Federal University of Pernambuco, Pernambuco, Brazil
 *    @author Silas Garrido (sgtcs@cin.ufpe.br)
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
 *    
 */

package moa.classifiers.meta;

import moa.classifiers.AbstractClassifier;
import moa.classifiers.Classifier;
import weka.core.Instance;

import moa.core.DoubleVector;
import moa.core.Measurement;
import moa.core.MiscUtils;
import moa.options.ClassOption;
import moa.options.FlagOption;
import moa.options.IntOption;

/**
 * Adaptable Diversity-based Online Boosting (ADOB) is a modified version
 * of the online boosting, as proposed by Oza and Russell, which is aimed
 * at speeding up the experts recovery after concept drifts.
 *
 * The original implementation of the Online Boosting, proposed by Oza and
 * Russell, was made by Richard Kirkby (rkirkby@cs.waikato.ac.nz), available 
 * at MOA Framework.
 */

public class AdaptableDiversityBasedOnlineBoosting extends AbstractClassifier {

    private static final long serialVersionUID = 1L;

    @Override
    public String getPurposeString() {
        return "Adaptable Diversity-based Online Boosting";
    }

    public ClassOption baseLearnerOption = new ClassOption("baseLearner", 'l',
            "Classifier to train.", Classifier.class, "trees.HoeffdingTree");

    public IntOption ensembleSizeOption = new IntOption("ensembleSize", 's',
            "The number of models to boost.", 10, 1, Integer.MAX_VALUE);

    public FlagOption pureBoostOption = new FlagOption("pureBoost", 'p',
            "Boost with weights only; no poisson.");
    
    protected final int ensembleSize=this.ensembleSizeOption.getValue();

    protected Classifier[] ensemble;
    
    protected int[] orderPosition;

    protected double[] scms;

    protected double[] swms;

    @Override
    public void resetLearningImpl() {
        this.ensemble = new Classifier[ensembleSize];
        this.orderPosition = new int[ensembleSize];
        Classifier baseLearner = (Classifier) getPreparedClassOption(this.baseLearnerOption);
        baseLearner.resetLearning();
        for (int i = 0; i < ensembleSize; i++) {
            this.ensemble[i] = baseLearner.copy();
            this.orderPosition[i] = i;
        }
        this.scms = new double[ensembleSize];
        this.swms = new double[ensembleSize];
    }

    @Override
    public void trainOnInstanceImpl(Instance inst) {
	// Calculates current accuracy of experts
        double[] acc = new double[ensembleSize];
        for ( int i=0; i<ensembleSize; i++ ) {
            acc[i] = this.scms[this.orderPosition[i]] + this.swms[this.orderPosition[i]];
            if ( acc[i] != 0.0 ) {
                acc[i] = this.scms[this.orderPosition[i]] / acc[i];
            }
        }
        
	// Sort by accuracy in ascending order
        double key_acc; int key_position, j;
        for ( int i=1; i<ensembleSize; i++ ) {
            key_position = this.orderPosition[i];
            key_acc = acc[i];
            j = i-1;
            while ( j>=0 && acc[j]<key_acc ) {
                this.orderPosition[j+1] = this.orderPosition[j];
                acc[j+1] = acc[j];
                j--;
            }
            this.orderPosition[j+1] = key_position;
            acc[j+1] = key_acc;
        }
        
        boolean correct=false; int pos;
        double lambda_d = 1.0; int maxAcc=0, minAcc=ensembleSize-1;
        for (int i = 0; i < ensembleSize; i++) {
            if ( correct ) {
                pos = this.orderPosition[maxAcc];
                maxAcc++;
            } else {
                pos = this.orderPosition[minAcc];
                minAcc--;
            }
            
            double k;
            if ( this.pureBoostOption.isSet() ) {
                k = lambda_d;
            } else {
                k = MiscUtils.poisson(lambda_d, this.classifierRandom);
            }
            
            if (k > 0.0) {
                Instance weightedInst = (Instance) inst.copy();
                weightedInst.setWeight(inst.weight() * k);
                this.ensemble[pos].trainOnInstance(weightedInst);
            }

	    // Increases or decreases lambda based on the prediction of instance
            if (this.ensemble[pos].correctlyClassifies(inst)) {
                this.scms[pos] += lambda_d;
                lambda_d *= this.trainingWeightSeenByModel / (2 * this.scms[pos]);
                correct = true;
            } else {
                this.swms[pos] += lambda_d;
                lambda_d *= this.trainingWeightSeenByModel / (2 * this.swms[pos]);
                correct = false;
            }
        }
    }

    protected double getEnsembleMemberWeight(int i) {
        if ( this.scms[i]>0.0 && this.swms[i]>0.0 ) {
            double em = this.swms[i] / (this.scms[i] + this.swms[i]);
            if (em <= 0.5) {
                double Bm = em / (1.0 - em);
                return Math.log(1.0 / Bm);
            }
        }
        return 0.0;
    }

    public double[] getVotesForInstance(Instance inst) {
        DoubleVector combinedVote = new DoubleVector();
        for (int i = 0; i < ensembleSize; i++) {
            double memberWeight = getEnsembleMemberWeight(i);
            if (memberWeight > 0.0) {
                DoubleVector vote = new DoubleVector(this.ensemble[i].getVotesForInstance(inst));
                if (vote.sumOfValues() > 0.0) {
                    vote.normalize();
                    vote.scaleValues(memberWeight);
                    combinedVote.addValues(vote);
                }
            } else {
                break;
            }
        }
        
        return combinedVote.getArrayRef();
    }

    public boolean isRandomizable() {
        return true;
    }

    @Override
    public void getModelDescription(StringBuilder out, int indent) {
        // TODO Auto-generated method stub
    }

    @Override
    protected Measurement[] getModelMeasurementsImpl() {
        return new Measurement[]{new Measurement("ensemble size",
                    this.ensemble != null ? ensembleSize : 0)};
    }

    @Override
    public Classifier[] getSubClassifiers() {
        return this.ensemble.clone();
    }
}
