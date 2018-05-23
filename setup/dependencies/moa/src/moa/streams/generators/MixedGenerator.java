package moa.streams.generators;

import java.util.ArrayList;
import java.util.List;
import java.util.Random;

import moa.core.InstancesHeader;
import moa.core.ObjectRepository;
import moa.options.AbstractOptionHandler;
import moa.options.FlagOption;
import moa.options.IntOption;
import moa.streams.InstanceStreamGenerators;
import moa.tasks.TaskMonitor;
import weka.core.Attribute;
import weka.core.DenseInstance;
import weka.core.Instance;
import weka.core.Instances;

public class MixedGenerator extends AbstractOptionHandler implements InstanceStreamGenerators {

    public IntOption functionOption = new IntOption("function", 'f',
			"Classification function used, as defined in the original paper.",
			1, 1, 2);
	
	public IntOption instanceRandomSeedOption = new IntOption(
			"instanceRandomSeed", 'i',
			"Seed for random generation of instances.", 1);

    public FlagOption balanceClassesOption = new FlagOption("balanceClasses",
            'b', "Balance the number of instances of each class.");

    protected InstancesHeader streamHeader;

	protected Random instanceRandom;

    protected boolean nextClassShouldBeZero;

	protected interface ClassFunction {
		public int determineClass(double v, double w, double x, double y);
	}

	protected static ClassFunction[] classificationFunctions = {
			new ClassFunction() {

				@Override
				public int determineClass(double v, double w, double x, double y) {
					boolean z = y < 0.5 + 0.3*Math.sin(3*Math.PI*x);
					if((v == 1 && w == 1) || (v == 1 && z) || (w == 1 && z)) {
						return 0;
					} else return 1;
				}
			},
			new ClassFunction() {

				@Override
				public int determineClass(double v, double w, double x, double y) {
					boolean z = y < 0.5 + 0.3*Math.sin(3*Math.PI*x);
					if((v == 1 && w == 1) || (v == 1 && z) || (w == 1 && z)) {
						return 1;
					} else return 0;
				}
			},
		};

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
		double v = 0, w = 0, x = 0, y = 0, group = 0;
        boolean desiredClassFound = false;
        while (!desiredClassFound) {
			v = (this.instanceRandom.nextDouble() < 0.5) ? 0 : 1;
			w = (this.instanceRandom.nextDouble() < 0.5) ? 0 : 1;
			x = this.instanceRandom.nextDouble();
			y = this.instanceRandom.nextDouble();
	        group = classificationFunctions[this.functionOption.getValue() - 1].determineClass(v, w, x, y);
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
		inst.setValue(0, v);
		inst.setValue(1, w);
		inst.setValue(2, x);
		inst.setValue(3, y);
		inst.setDataset(header);
		inst.setClassValue(group);
		return inst;
	}

	@Override
	public boolean isRestartable() {
		return true;
	}

	@Override
	public void restart() {
        this.instanceRandom = new Random(this.instanceRandomSeedOption.getValue());
        this.nextClassShouldBeZero = false;
	}
        
        @Override
        public void changeRandomSeed( int value ) {
            this.instanceRandom = new Random(value);
        }

	@Override
	protected void prepareForUseImpl(TaskMonitor monitor,
			ObjectRepository repository) {
		List booleanLabels = new ArrayList();
		booleanLabels.add("0");
		booleanLabels.add("1");
		
		ArrayList<Attribute> attributes = new ArrayList();		
		Attribute attribute1 = new Attribute("v", booleanLabels);
        Attribute attribute2 = new Attribute("w", booleanLabels);

		Attribute attribute3 = new Attribute("x");
        Attribute attribute4 = new Attribute("y");

		List classLabels = new ArrayList();
		classLabels.add("positive");
		classLabels.add("negative");
		Attribute classAtt = new Attribute("class", classLabels);

		attributes.add(attribute1);
        attributes.add(attribute2);
        attributes.add(attribute3);
        attributes.add(attribute4);
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
