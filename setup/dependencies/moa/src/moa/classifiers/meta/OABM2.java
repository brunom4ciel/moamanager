/*
 *    OABM2.java
 *    Copyright (C) 2016 Federal University of Pernambuco, Pernambuco, Brazil
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
 * Online AdaBoost-based M2 (OABM2)
 *
 * @author Silas Garrido (sgtcs@cin.ufpe.br)
 * @version $Revision: 1 $
 */
public class OABM2 extends AbstractClassifier {

    private static final long serialVersionUID = 1L;

    @Override
    public String getPurposeString() {
        return "Incremental on-line boosting of Oza and Russell.";
    }

    public ClassOption baseLearnerOption = new ClassOption("baseLearner", 'l',
            "Classifier to train.", Classifier.class, "bayes.NaiveBayes");

    public IntOption ensembleSizeOption = new IntOption("ensembleSize", 's',
            "The number of models to boost.", 10, 1, Integer.MAX_VALUE);

    public FlagOption pureBoostOption = new FlagOption("pureBoost", 'p',
            "Boost with weights only; no poisson.");
    
    public IntOption weightPrecisionOption = new IntOption("weightPrecision", 'w',
            "Weight Precision", 50, 10, Integer.MAX_VALUE);

    protected Classifier[] ensemble;

    protected double[][][] weightPrecision;
    protected double[][] sumW;
    protected double[] sumLambda;
    protected double[] pseudoloss;
    protected double[] E, B;
    protected boolean first;

    @Override
    public void resetLearningImpl() {
        this.ensemble = new Classifier[this.ensembleSizeOption.getValue()];
        Classifier baseLearner = (Classifier) getPreparedClassOption(this.baseLearnerOption);
        baseLearner.resetLearning();
        for (int i = 0; i < this.ensemble.length; i++) {
            this.ensemble[i] = baseLearner.copy();
        }
        this.sumLambda = new double[this.ensemble.length];
        this.pseudoloss = new double[this.ensemble.length];
        this.E = new double[this.ensemble.length];
        this.B = new double[this.ensemble.length];
        this.first = true;
    }
    
    public double sumOfValues ( double v[], int yi ) {
        double ans=0.0;
        for ( int i=0; i<v.length; i++ ) {
            if ( i != yi ) {
                ans += v[i];
            }
        }
        
        return ans;
    }

    @Override
    public void trainOnInstanceImpl(Instance inst) {
        double lambda_d = 1.0;
        int yi = (int) inst.classValue();
        int dimension = inst.numClasses();
        double[] w = new double[dimension];
        double[] q = new double[dimension];
        DoubleVector h = new DoubleVector(new double[dimension]);

        if (first) {
            weightPrecision = new double[this.weightPrecisionOption.getValue()][this.ensemble.length][dimension];
            sumW = new double[this.ensemble.length][dimension];
            first = false;
        }
        
        for (int i = 0; i < dimension; i++) {
            if (i != yi) {
                w[i] = lambda_d;
            }
        }

        for (int i = 0; i < this.ensemble.length; i++) {
            lambda_d = ((sumOfValues(w,yi) / (dimension-1)));
            
            double k = this.pureBoostOption.isSet() ? lambda_d : MiscUtils.poisson(lambda_d, this.classifierRandom);
            if (k > 0.0) {
                Instance weightedInst = (Instance) inst.copy();
                weightedInst.setWeight(inst.weight() * k);
                this.ensemble[i].trainOnInstance(weightedInst);
            }
            
            DoubleVector h_tmp = new DoubleVector(this.ensemble[i].getVotesForInstance(inst));
            
            double sv = sumOfValues(w, yi); int size = h_tmp.numValues();
            for (int j = 0; j < dimension; j++) {
                if (j != yi) {
                    q[j] = w[j] / sv;
                }
                
                if ( j < size ) {
                    h.setValue(j, h_tmp.getValue(j));
                } else {
                    h.setValue(j, 0.0);
                }
            }

           // h.plausibility();

            double tmp = 0.0;
            for (int j = 0; j < dimension; j++) { // Calc pseudoloss
                if (j != yi) {
                    tmp += (h.getValue(j) * q[j]);
                }
            }

            sumLambda[i] += lambda_d;
            pseudoloss[i] += (lambda_d * (1 - h.getValue(yi) + tmp));

            E[i] = 0.5 * (pseudoloss[i] / sumLambda[i]); if (E[i] == 0.0) continue;
            B[i] = E[i] / (1 - E[i]);
                
            double approximateB, interval, denominator=1.0, diff = Double.MAX_VALUE;
            for (int j = 0; j < dimension; j++) { // Calc new "w"
                if (j != yi) {
                    tmp = (0.5 * (1 + h.getValue(yi) - h.getValue(j))); // Calc exponent
                        
                    sumW[i][j] += w[j];
                    interval = 1.0/this.weightPrecisionOption.getValue();
                    for ( int z=0; z<this.weightPrecisionOption.getValue(); z++ ) {
                        approximateB = interval*(z+1);
                        approximateB = approximateB/(1.0-approximateB); // Beta Function
                        weightPrecision[z][i][j] += (w[j] * Math.pow(approximateB,tmp));
                        if ( (Math.abs(approximateB-B[i])) < diff ) {
                            diff = Math.abs(approximateB-B[i]);
                            denominator = weightPrecision[z][i][j]/sumW[i][j];
                        }
                    }
                        
                    w[j] = (w[j] * Math.pow(B[i],tmp))/denominator;
                }
            }
        }
    }

    protected double getEnsembleMemberWeight(int i) {
        return Math.log(1.0 / B[i]);
    }

    public double[] getVotesForInstance(Instance inst) {
        DoubleVector combinedVote = new DoubleVector();
        for (int i = 0; i < this.ensemble.length; i++) {
            double memberWeight = getEnsembleMemberWeight(i);
            DoubleVector vote = new DoubleVector(this.ensemble[i].getVotesForInstance(inst));
            if (vote.sumOfValues() > 0.0) {
                vote.normalize();
                vote.scaleValues(memberWeight);
                combinedVote.addValues(vote);
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
            this.ensemble != null ? this.ensemble.length : 0)};
    }

    @Override
    public Classifier[] getSubClassifiers() {
        return this.ensemble.clone();
    }
}

