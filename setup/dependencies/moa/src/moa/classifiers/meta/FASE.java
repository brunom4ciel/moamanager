/*
 *    FASE.java
 *
 *    @author Isvani Frias-Blanco (ifriasb@udg.co.cu)
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

package moa.classifiers.meta;

import moa.classifiers.AbstractClassifier;
import moa.classifiers.Classifier;
import moa.classifiers.core.driftdetection.HDDM_A_Test;
import moa.core.DoubleVector;
import moa.core.InstancesHeader;
import moa.core.Measurement;
import moa.core.MiscUtils;
import moa.options.ClassOption;
import moa.options.IntOption;
import moa.streams.InstanceStream;
import weka.core.Attribute;
import weka.core.DenseInstance;
import weka.core.FastVector;
import weka.core.Instance;
import weka.core.Instances;

public class FASE extends AbstractClassifier {

    private static final long serialVersionUID = 1L;

    public ClassOption metaLearnerOption = new ClassOption("metaLearner", 'm',
            "Classifier to train.", Classifier.class, "drift.AdaptiveClassifier");

    public ClassOption baseLearnerOption = new ClassOption("baseLearner", 'b',
            "Classifier to train.", Classifier.class, "drift.AdaptiveClassifier");

    public IntOption ensembleSizeOption = new IntOption("ensembleSize", 's',
            "The number of models in the bag.", 10, 1, Integer.MAX_VALUE);

    protected InstancesHeader streamHeader;

    protected Classifier metaClassifier;

    protected Classifier[] ensemble;

    protected HDDM_A_Test[] estimator;

    @Override
    public void resetLearningImpl() {
        this.metaClassifier = (Classifier) getPreparedClassOption(this.metaLearnerOption);
        this.metaClassifier.resetLearning();
        this.ensemble = new Classifier[this.ensembleSizeOption.getValue()];
        Classifier baseLearner = (Classifier) getPreparedClassOption(this.baseLearnerOption);
        baseLearner.resetLearning();
        for (int i = 0; i < this.ensemble.length; i++) {
            this.ensemble[i] = baseLearner.copy();
        }
        this.estimator = new HDDM_A_Test[this.ensemble.length];
        for (int i = 0; i < this.ensemble.length; i++) {
            this.estimator[i] = new HDDM_A_Test();
        }
    }

    @Override
    public void trainOnInstanceImpl(Instance inst) {
        trainMetaLearner(inst);
        boolean Change = false;
        for (int i = 0; i < this.ensemble.length; i++) {
            int k = MiscUtils.poisson(1.0, this.classifierRandom);
            if (k > 0) {
                Instance weightedInst = (Instance) inst.copy();
                weightedInst.setWeight(inst.weight() * k);
                this.ensemble[i].trainOnInstance(weightedInst);
            }
            boolean correctlyClassifies = this.ensemble[i].correctlyClassifies(inst);
            this.estimator[i].input(correctlyClassifies ? 0 : 1);
            if (this.estimator[i].getChange() == true) {
                Change = true;
            }
        }
        if (Change) {
            double max = 0.0;
            int imax = -1;
            for (int i = 0; i < this.ensemble.length; i++) {
                if (max < this.estimator[i].getEstimation()) {
                    max = this.estimator[i].getEstimation();
                    imax = i;
                }
            }
            if (imax != -1) {
                this.ensemble[imax].resetLearning();
                this.estimator[imax] = new HDDM_A_Test();
            }
        }
    }

    @Override
    protected Measurement[] getModelMeasurementsImpl() {
        return new Measurement[]{new Measurement("ensemble size",
            this.ensemble != null ? this.ensemble.length : 0)};
    }
    
    @Override
    public void setModelContext(InstancesHeader ih) {
        super.setModelContext(ih);
        generateNominalHeader();
        this.metaClassifier.setModelContext(this.streamHeader);
    }

    @Override
    public void getModelDescription(StringBuilder out, int indent) {
        // TODO Auto-generated method stub
    }

    @Override
    public boolean isRandomizable() {
        return true;
    }

    @Override
    public double[] getVotesForInstance(Instance inst) {
        int ensembleSize = this.ensembleSizeOption.getValue();
        DoubleVector classVote[] = new DoubleVector[ensembleSize];
        for (int i = 0; i < ensembleSize; i++) {
            classVote[i] = new DoubleVector(this.ensemble[i].getVotesForInstance(inst));
        }
        Instance metaInstance = nextNominalAttInstance(classVote, -1);
        return this.metaClassifier.getVotesForInstance(metaInstance);
    }

    @Override
    public String getPurposeString() {
        return "Stacking for evolving data streams using HDDM.";
    }

    protected void generateNominalHeader() {
        FastVector attributes = new FastVector();
        FastVector nominalAttVals = new FastVector();

        for (int i = 0; i < this.getModelContext().numClasses(); i++) {
            nominalAttVals.addElement("value" + (i + 1));
        }

        for (int i = 0; i < this.ensembleSizeOption.getValue(); i++) {
            attributes.addElement(new Attribute("nominal" + (i + 1),
                    nominalAttVals));
        }

        FastVector classLabels = new FastVector();
        for (int i = 0; i < this.getModelContext().numClasses(); i++) {
            classLabels.addElement("class" + (i + 1));
        }
        attributes.addElement(new Attribute("class", classLabels));
        this.streamHeader = new InstancesHeader(new Instances(
                getCLICreationString(InstanceStream.class), attributes, 0));
        this.streamHeader.setClassIndex(this.streamHeader.numAttributes() - 1);
    }

    protected Instance nextNominalAttInstance(DoubleVector[] classVote, int classValue) {
        double[] attVals = new double[this.ensembleSizeOption.getValue()];
        InstancesHeader header = this.streamHeader;
        Instance inst = new DenseInstance(header.numAttributes());
        for (int i = 0; i < attVals.length; i++) {
            int maxIndex = classVote[i].maxIndex();
            attVals[i] = maxIndex >= 0.0 ? maxIndex : 0;
            inst.setValue(i, attVals[i]);
        }
        inst.setDataset(header);
        if (classValue >= 0) {
            inst.setClassValue(classValue);
        }
        return inst;
    }

    protected void trainMetaLearner(Instance inst) {
        int ensembleSize = this.ensembleSizeOption.getValue();
        DoubleVector classVote[] = new DoubleVector[ensembleSize];
        for (int i = 0; i < ensembleSize; i++) {
            classVote[i] = new DoubleVector(this.ensemble[i].getVotesForInstance(inst));
        }
        Instance metaInstance = nextNominalAttInstance(classVote, (int) inst.classValue());
        this.metaClassifier.trainOnInstance(metaInstance);
    }
}
