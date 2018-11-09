package moa.streams.generators;

import java.util.ArrayList;
import java.util.List;
import java.util.Random;

import weka.core.Attribute;
import weka.core.DenseInstance;
import weka.core.Instance;
import weka.core.Instances;
import moa.core.InstancesHeader;
import moa.core.ObjectRepository;
import moa.options.AbstractOptionHandler;
import moa.options.FlagOption;
import moa.options.IntOption;
import moa.streams.InstanceStreamGenerators;
import moa.tasks.TaskMonitor;

public class SineGenerator extends AbstractOptionHandler implements InstanceStreamGenerators {

    public static final int NUM_IRRELEVANT_ATTRIBUTES = 2;

    public IntOption instanceRandomSeedOption = new IntOption(
            "instanceRandomSeed", 'i',
            "Seed for random generation of instances.", 1);

    public IntOption functionOption = new IntOption("function", 'f',
            "Classification function used, as defined in the original paper.",
            1, 1, 4);

    public FlagOption suppressIrrelevantAttributesOption = new FlagOption(
            "suppressIrrelevantAttributes", 's',
            "Reduce the data to only contain 2 relevant numeric attributes.");

    public FlagOption balanceClassesOption = new FlagOption("balanceClasses",
            'b', "Balance the number of instances of each class.");

    protected InstancesHeader streamHeader;

    protected Random instanceRandom;

    protected boolean nextClassShouldBeZero;

    protected interface ClassFunction {

        public int determineClass(double x, double y);
    }

    protected static ClassFunction[] classificationFunctions = {
        // Values below the curve y = sin(x) are classified as positive.
        new ClassFunction() {

            @Override
            public int determineClass(double x, double y) {
                return (y < Math.sin(x)) ? 0 : 1;
            }
        },
        // Values below the curve y = sin(x) are classified as negative.
        new ClassFunction() {

            @Override
            public int determineClass(double x, double y) {
                return (y >= Math.sin(x)) ? 0 : 1;
            }
        },
        // Values below the curve y = 0.5 + 0.3*sin(3*PI*x) are classified as positive.
        new ClassFunction() {

            @Override
            public int determineClass(double x, double y) {
                return (y < 0.5 + 0.3 * Math.sin(3 * Math.PI * x)) ? 0 : 1;
            }
        },
        // Values below the curve y = 0.5 + 0.3*sin(3*PI*x) are classified as negative.
        new ClassFunction() {

            @Override
            public int determineClass(double x, double y) {
                return (y >= 0.5 + 0.3 * Math.sin(3 * Math.PI * x)) ? 0 : 1;
            }
        },};

    @Override
    public void getDescription(StringBuilder sb, int indent) {

    }

    @Override
    public InstancesHeader getHeader() {
        return this.streamHeader;
    }

    @Override
    public long estimatedRemainingInstances() {
        return -1;
    }

    @Override
    public boolean hasMoreInstances() {
        return true;
    }

    @Override
    public Instance nextInstance() {
        double a1 = 0, a2 = 0, group = 0;

        boolean desiredClassFound = false;
        while (!desiredClassFound) {
            a1 = this.instanceRandom.nextDouble();
            a2 = this.instanceRandom.nextDouble();
            group = classificationFunctions[this.functionOption.getValue() - 1].determineClass(a1, a2);
            if (!this.balanceClassesOption.isSet()) {
                desiredClassFound = true;
            } else {
                // balance the classes
                if ((this.nextClassShouldBeZero && (group == 0))
                        || (!this.nextClassShouldBeZero && (group == 1))) {
                    desiredClassFound = true;
                    this.nextClassShouldBeZero = !this.nextClassShouldBeZero;
                } // else keep searching
            }
        }
        // construct instance
        InstancesHeader header = getHeader();
        Instance inst = new DenseInstance(header.numAttributes());
        inst.setValue(0, a1);
        inst.setValue(1, a2);
        inst.setDataset(header);
        if (!this.suppressIrrelevantAttributesOption.isSet()) {
            for (int i = 0; i < NUM_IRRELEVANT_ATTRIBUTES; i++) {
                inst.setValue(i + 2, this.instanceRandom.nextDouble());
            }
        }
        inst.setClassValue(group);
        return inst;
    }

    @Override
    public boolean isRestartable() {
        return true;
    }

    @Override
    public void restart() {
        this.instanceRandom = new Random(
                this.instanceRandomSeedOption.getValue());
        this.nextClassShouldBeZero = false;
    }

    @Override
    public void changeRandomSeed(int value) {
        this.instanceRandom = new Random(value);
    }

    @Override
    protected void prepareForUseImpl(TaskMonitor monitor,
            ObjectRepository repository) {
        ArrayList<Attribute> attributes = new ArrayList();

        int numAtts = 2;
        if (!this.suppressIrrelevantAttributesOption.isSet()) {
            numAtts += NUM_IRRELEVANT_ATTRIBUTES;
        }
        for (int i = 0; i < numAtts; i++) {
            attributes.add(new Attribute("att" + (i + 1)));
        }

        List classLabels = new ArrayList();
        classLabels.add("positive");
        classLabels.add("negative");
        Attribute classAtt = new Attribute("class", classLabels);
        attributes.add(classAtt);

        this.streamHeader = new InstancesHeader(new Instances(
                getCLICreationString(InstanceStreamGenerators.class), attributes, 0));
        this.streamHeader.setClassIndex(this.streamHeader.numAttributes() - 1);
        restart();
    }
    
    @Override
    public List<Integer> getDriftPositions() {
        return new ArrayList<>();
    }
    
    @Override
    public List<Integer> getDriftWidths() {
        return new ArrayList<>();
    }
}
