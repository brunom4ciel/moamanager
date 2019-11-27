/*
 *    DiversityForDealingWithDrifts.java
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
import moa.classifiers.core.driftdetection.ChangeDetector;
import weka.core.Instance;

import moa.core.Measurement;
import moa.options.ClassOption;
import moa.options.FloatOption;
import weka.core.Utils;

/**
 *  
 * <p> Leandro L. Minku and Xin Yao: DDD: A New Ensemble Approach for 
 *     Dealing with Concept Drift. IEEE Transactions on Knowledge and
 *     Data Engineering, Vol. 24, No. 4, April 2012. </p>
 * 
 * @author Silas Garrido (sgtcs@cin.ufpe.br)
 *
 */

public class DiversityForDealingWithDrifts extends AbstractClassifier {
        
    private static final long serialVersionUID = 1L;

    public ClassOption baseLearnerOption = new ClassOption("baseLearner", 'l',
            "Classifier to train.", Classifier.class, "meta.OzaBag -l bayes.NaiveBayes -s 25");
    
    public ClassOption driftDetectionMethodOption = new ClassOption("driftDetectionMethod", 'd',
             "Drift detection method to use.", ChangeDetector.class, "EDDM");
    
    public FloatOption multiplierConstantOption = new FloatOption("W",
            'w', "Weight of the old low diversity ensemble", 1.00);
    
    public FloatOption lowDiversityOption = new FloatOption("Pl",
            'p', "Low Diversity", 1.00);
    
    public FloatOption highDiversityOption = new FloatOption("Ph",
            'h', "High Diversity", 0.1);
    
    protected boolean beforeDrift, newClassifierReset;
    protected Classifier hNl, hNh, hOl, hOh, hWarning; // New low, new high, old low and old high ensemble diversity
    protected double accNl, accNh, accOl, accOh; // Accuracies
    protected double stdNl, stdNh, stdOl, stdOh; // Standard Deviations
    
    protected ChangeDetector driftDetectionMethod;
    protected int drift;
    
    public static final int DDM_INCONTROL_LEVEL = 0;
    public static final int DDM_WARNING_LEVEL = 1;
    public static final int DDM_OUTCONTROL_LEVEL = 2;
    
    public Classifier newEnsembleClassifier( double diversity ) {
        Classifier ensembleClassifier = ((Classifier) getPreparedClassOption(this.baseLearnerOption)).copy();
        ensembleClassifier.resetLearning();
        ((OzaBag)ensembleClassifier).changeDiversity(diversity);
        
        return ensembleClassifier;
    }

    @Override
    public void resetLearningImpl() {
        this.hNl = newEnsembleClassifier(this.lowDiversityOption.getValue());
        this.hNh = newEnsembleClassifier(this.highDiversityOption.getValue());
        this.hWarning = newEnsembleClassifier(this.lowDiversityOption.getValue());
        this.beforeDrift = this.newClassifierReset = true;
        this.accNl = this.accNh = this.accOl = this.accOh = 0.0;
        this.stdNl = this.stdNh = this.stdOl = this.stdOh = 0.0;
        this.driftDetectionMethod = ((ChangeDetector) getPreparedClassOption(this.driftDetectionMethodOption)).copy();
    }
    
    public double[] weightedMajority ( Classifier h1, Classifier h2, Classifier h3, 
            double w1, double w2, double w3, Instance inst ) {
        int index;
        double[] combinedVote = new double[inst.numClasses()];
        
        double[] vote1 = h1.getVotesForInstance(inst);
        double[] vote2 = h2.getVotesForInstance(inst);
        double[] vote3 = h3.getVotesForInstance(inst);
        
        index = Utils.maxIndex(vote1);
        combinedVote[index] += w1;
        
        index = Utils.maxIndex(vote2);
        combinedVote[index] += w2;
        
        index = Utils.maxIndex(vote3);
        combinedVote[index] += w3;
        
        Utils.normalize(combinedVote);
        
        return combinedVote;
    }

    @Override
    public void trainOnInstanceImpl(Instance inst) {
        if ( !this.beforeDrift ) {
            this.accNl = ((OzaBag)this.hNl).getAccuracy();
            this.accNh = ((OzaBag)this.hNh).getAccuracy();
            this.accOl = ((OzaBag)this.hOl).getAccuracy();
            this.accOh = ((OzaBag)this.hOh).getAccuracy();
            this.stdNl = ((OzaBag)this.hNl).getStandardDeviation();
            this.stdNh = ((OzaBag)this.hNh).getStandardDeviation();
            this.stdOl = ((OzaBag)this.hOl).getStandardDeviation();
            this.stdOh = ((OzaBag)this.hOh).getStandardDeviation();
        }
        
        int trueClass = (int)inst.classValue();
        boolean prediction;
        if (Utils.maxIndex(hNl.getVotesForInstance(inst)) == trueClass) { 
            prediction = true;
        } else {
            prediction = false;
        }
        
        this.driftDetectionMethod.input(prediction ? 0.0 : 1.0);
        this.drift = DDM_INCONTROL_LEVEL;
        if (this.driftDetectionMethod.getChange()) {
            this.drift =  DDM_OUTCONTROL_LEVEL;
        }
        if (this.driftDetectionMethod.getWarningZone()) {
            this.drift =  DDM_WARNING_LEVEL;
        }
        switch ( this.drift ) {
            case DDM_WARNING_LEVEL:
                if ( this.newClassifierReset ) {
                    this.hWarning.resetLearning();
                    this.newClassifierReset = false;
                }
            
                this.hWarning.trainOnInstance(inst);
                break;
                
            case DDM_OUTCONTROL_LEVEL: 
                if (( this.beforeDrift ) || ( !this.beforeDrift && this.accNl > this.accOh )) {
                    this.hOl = this.hNl.copy();
                } else {
                    this.hOl = this.hOh.copy();
                }
            
                this.hOh = this.hNh.copy();
                this.hNl = this.hWarning.copy();
                this.hNh = newEnsembleClassifier(this.highDiversityOption.getValue());
                this.hWarning = newEnsembleClassifier(this.lowDiversityOption.getValue());
                this.accNl = this.accNh = this.accOl = this.accOh = 0.0;
                this.stdNl = this.stdNh = this.stdOl = this.stdOh = 0.0;
                this.beforeDrift = false;
                break;
                
            case DDM_INCONTROL_LEVEL:
                this.newClassifierReset = true;
                break;
        }
        
        if ( !this.beforeDrift ) {
            if (( this.accNl > this.accOh ) && ( this.accNl > this.accOl )) {
                this.beforeDrift = true;
            } else {
                if ( ((this.accOh-this.stdOh) > (this.accNl+this.stdNl)) 
                        && (( this.accOh-this.stdOh ) > ( this.accOl+this.stdOl )) ) {                    
                    this.hNl = this.hOh.copy();
                    this.accNl = this.accOh;
                    this.beforeDrift = true;
                }
            }
        }
        
        this.hNl.trainOnInstance(inst);
        this.hNh.trainOnInstance(inst);

        if ( !this.beforeDrift ) {
            ((OzaBag)(this.hOl)).changeDiversity(this.lowDiversityOption.getValue());
            ((OzaBag)(this.hOh)).changeDiversity(this.lowDiversityOption.getValue());
            this.hOl.trainOnInstance(inst);
            this.hOh.trainOnInstance(inst);
        }
    }

    @Override
    public double[] getVotesForInstance(Instance inst) {
        if ( this.beforeDrift ) {
            return this.hNl.getVotesForInstance(inst);
        } else {     
            double sumAcc = this.accNl+this.accOl*this.multiplierConstantOption.getValue()+this.accOh;
            double wNl = (sumAcc == 0.0) ? 1.0/3.0 : this.accNl/sumAcc;
            double wOl = (sumAcc == 0.0) ? 1.0/3.0 : this.accOl*this.multiplierConstantOption.getValue()/sumAcc;
            double wOh = (sumAcc == 0.0) ? 1.0/3.0 : this.accOh/sumAcc;
            
            return weightedMajority(this.hNl, this.hOl, this.hOh, wNl, wOl, wOh, inst);
        }
    }

    @Override
    public boolean isRandomizable() {
        return false;
    }

    @Override
    public void getModelDescription(StringBuilder out, int indent) {
        // TODO Auto-generated method stub
    }

    @Override
    protected Measurement[] getModelMeasurementsImpl() {
        return null;
    }
}
