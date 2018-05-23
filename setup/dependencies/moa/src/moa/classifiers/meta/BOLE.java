/*
 *    BOLE.java
 *    Copyright (C) 2015 Santos, Barros
 *    @authors Silas Garrido T. de Carvalho Santos (sgtcs@cin.ufpe.br)
 *             Roberto Souto Maior de Barros (roberto@cin.ufpe.br) 
 *    @version $Version: 1 $
 *
 *    Evolved from AdaptableDiversityBasedOnlineBoosting.java
 *    Copyright (C) 2014 Federal University of Pernambuco, Pernambuco, Brazil
 *    @author Silas Garrido <sgtcs@cin.ufpe.br>
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
 * Boosting-like Online Learning Ensemble (BOLE), 
 * published as:
 * <p> Roberto Souto Maior de Barros, Silas Garrido T. de Carvalho Santos 
 *     and Paulo Mauricio Gonçalves Jr.: 
 *     A Boosting-like Online Learning Ensemble. 
 *     ... detalhes da publicação... </p>
 *
 * Inspired in (and generalized from) ADOB method, published as: 
 *     Silas Garrido Teixeira de Carvalho Santos, Paulo Mauricio Gonçalves Júnior,
 *     Geyson Daniel dos Santos Silva, and Roberto Souto Maior de Barros: 
 *     Speeding Up Recovery From Concept Drifts. 
 *     In book: Machine Learning and Knowledge Discovery in Databases, 
 *     ECML/PKDD 2014, Part III, LNCS 8726, pp. 179-194. 09/2014. 
 *     http://dx.doi.org/10.1007/978-3-662-44845-8_12.
 *
 */

package moa.classifiers.meta;

import moa.classifiers.AbstractClassifier;
import moa.classifiers.Classifier;
import moa.core.DoubleVector;
import moa.core.Measurement;
import moa.core.MiscUtils;
import moa.options.ClassOption;
import moa.options.FlagOption;
import moa.options.FloatOption;
import moa.options.IntOption;
import weka.core.Instance;

public class BOLE extends AbstractClassifier {
    private static final long serialVersionUID = -3099808642980080785L;
    // private int cnt = 0;
    private double memberWeight;
    private double key_acc; 
    private int key_position, i, j;
    private int maxAcc, minAcc, pos;
    private double lambda_d, k;
    private boolean correct, okay;
    private double em, Bm;

    public ClassOption baseLearnerOption = new ClassOption("baseLearner", 
            'l', "Classifier to train.", Classifier.class, "trees.HoeffdingTree");

    public IntOption ensembleSizeOption = new IntOption("ensembleSize", 
            's', "The size of the ensemble - number of models to boost.", 
            10, 1, 1000);

    public FlagOption pureBoostOption = new FlagOption("pureBoost", 
            'p', "Boost with weights only; no poisson.");

    public IntOption breakVotesOption = new IntOption("breakVotes", 
            'b', "Break Votes? 0=no, 1=yes", 
            1, 0, 1);

    public FloatOption errorBoundOption = new FloatOption("errorBound", 
            'e', "Error bound percentage for allowing experts to vote.",
            0.5, 0.1, 1.0);

    public FloatOption weightShiftOption = new FloatOption("weightShift", 
            'w', "Weight shift associated with the error bound.",
            0.0, 0.0, 5.0); 

    protected final int ensembleSize = this.ensembleSizeOption.getValue();
    protected Classifier[] ensemble;
    protected int[] orderPosition;
    protected double[] scms;
    protected double[] swms;

    @Override
    public String getPurposeString() {
        return "Boosting-like Online Learning Ensemble";
    }

    @Override
    public void resetLearningImpl() {
        this.ensemble = new Classifier[ensembleSize];
        this.orderPosition = new int[ensembleSize];
        Classifier baseLearner = (Classifier) getPreparedClassOption(this.baseLearnerOption);
        baseLearner.resetLearning();
        for (i = 0; i < ensembleSize; i++) {
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
        for (i = 0; i < ensembleSize; i++) {
            acc[i] = this.scms[this.orderPosition[i]] + this.swms[this.orderPosition[i]];
            if (acc[i] != 0.0) {
                acc[i] = this.scms[this.orderPosition[i]] / acc[i];
            }
        }
        
	// Sort by accuracy in ascending order
        for (i = 1; i < ensembleSize; i++) {
            key_position = this.orderPosition[i];
            key_acc = acc[i];
            j = i - 1;
            while ( (j >=0) && (acc[j] < key_acc) ) {
                this.orderPosition[j+1] = this.orderPosition[j];
                acc[j+1] = acc[j];
                j--;
            }
            this.orderPosition[j+1] = key_position;
            acc[j+1] = key_acc;
        }
        
        correct = false; 
        maxAcc = 0; 
        minAcc = ensembleSize - 1; 
        lambda_d = 1.0; 
        //System.out.printf("Instância %d\n\n", ++cnt);
        for (i = 0; i < ensembleSize; i++) {
            if (correct) {
                pos = this.orderPosition[maxAcc];
                maxAcc++;
            } else {
                pos = this.orderPosition[minAcc];
                minAcc--;
            }
            
            if (this.pureBoostOption.isSet())
                k = lambda_d;
            else
                k = MiscUtils.poisson(lambda_d, this.classifierRandom);
            
            if (k > 0.0) {
                Instance weightedInst = (Instance) inst.copy();
                weightedInst.setWeight(inst.weight() * k);
                this.ensemble[pos].trainOnInstance(weightedInst);
            }

	    // Increases or decreases lambda based on the prediction of instance
            if (this.ensemble[pos].correctlyClassifies(inst)) {
                this.scms[pos] += lambda_d;
                lambda_d *= (this.trainingWeightSeenByModel / (2 * this.scms[pos]));
                correct = true;
            } else {
                this.swms[pos] += lambda_d; 
                lambda_d *= (this.trainingWeightSeenByModel / (2 * this.swms[pos]));
                correct = false;
            }
        }
    }

    protected double getEnsembleMemberWeight(int i) {
        if ( (this.scms[i] > 0.0) && (this.swms[i] > 0.0) ) { 
            em = this.swms[i] / (this.scms[i] + this.swms[i]);
            if (em <= this.errorBoundOption.getValue()) {
                Bm = em / (1.0 - em);
                okay = true;
                return Math.log(1.0 / Bm); 
            } 

        }
        okay = false;
        return 0.0;
    }

    public double[] getVotesForInstance(Instance inst) {
        DoubleVector combinedVote = new DoubleVector(); 
        //System.out.printf("%d - ", ++cnt);
        for (i = 0; i < ensembleSize; i++) {
            memberWeight = getEnsembleMemberWeight(i) + this.weightShiftOption.getValue(); 
            //System.out.printf("memberWeight = %f\n\n", memberWeight);
            if (okay) {
                DoubleVector vote = new DoubleVector(this.ensemble[i].getVotesForInstance(inst));
                if (vote.sumOfValues() > 0.0) {
                    vote.normalize();
                    vote.scaleValues(memberWeight);
                    combinedVote.addValues(vote);
                } 
            } 
            else 
                if (this.breakVotesOption.getValue() == 1) {
                    break;
                }
        } 
        //System.out.printf("\n");
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
