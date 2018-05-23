/*
 *    FadingTargetMean.java
 *    Copyright (C) 2014 University of Porto, Portugal
 *    @author A. Bifet, J. Duarte, J. Gama
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
 *    
 *    
 */
package moa.classifiers.rules.functions;

import moa.classifiers.AbstractClassifier;
import moa.classifiers.Regressor;
import moa.core.DoubleVector;
import moa.core.Measurement;
import moa.options.FlagOption;
import moa.options.FloatOption;
import weka.core.Instance;

public class Perceptron extends AbstractClassifier implements Regressor{

	private final double SD_THRESHOLD = 0.0000001; //THRESHOLD for normalizing attribute and target values

	private static final long serialVersionUID = 1L;

	public FlagOption constantLearningRatioDecayOption = new FlagOption(
			"learningRatio_Decay_set_constant", 'd',
			"Learning Ratio Decay in Perceptron set to be constant. (The next parameter).");

	public FloatOption learningRatioOption = new FloatOption(
			"learningRatio", 'l', 
			"Constante Learning Ratio to use for training the Perceptrons in the leaves.", 0.01);
	
	public FloatOption learningRateDecayOption = new FloatOption(
			"learningRateDecay", 'm', 
			" Learning Rate decay to use for training the Perceptron.", 0.001);
	
	public FloatOption fadingFactorOption = new FloatOption(
			"fadingFactor", 'e', 
			"Fading factor for the Perceptron accumulated error", 0.99, 0, 1);

	private double nError;
	protected double fadingFactor;
	
	protected double learningRatio;

	protected double learningRateDecay;

	// The Perception weights 
	protected double[] weightAttribute; 

	// Statistics used for error calculations
	public DoubleVector perceptronattributeStatistics = new DoubleVector();
	public DoubleVector squaredperceptronattributeStatistics = new DoubleVector();

	// The number of instances contributing to this model
	protected int perceptronInstancesSeen;
	protected int perceptronYSeen;

	protected double accumulatedError;

	// If the model  (weights) should be reset or not
	protected boolean initialisePerceptron; 

	protected double perceptronsumY;

	protected double squaredperceptronsumY;


	public Perceptron()
	{
		this.initialisePerceptron = true;
	}
	
/*
 * Perceptron
*/
	public Perceptron(Perceptron p) {
		super();
		this.constantLearningRatioDecayOption = p.constantLearningRatioDecayOption;
		this.learningRatioOption = p.learningRatioOption;
		this.learningRateDecayOption=p.learningRateDecayOption;
		this.fadingFactorOption = p.fadingFactorOption;
		this.nError = p.nError;
		this.fadingFactor = p.fadingFactor;
		this.learningRatio = p.learningRatio;
		this.learningRateDecay = p.learningRateDecay;
		if (p.weightAttribute!=null)
			this.weightAttribute = p.weightAttribute.clone();
		
		this.perceptronattributeStatistics = new DoubleVector(p.perceptronattributeStatistics);
		this.squaredperceptronattributeStatistics = new DoubleVector(p.squaredperceptronattributeStatistics);
		this.perceptronInstancesSeen = p.perceptronInstancesSeen;
		
		this.initialisePerceptron = p.initialisePerceptron;
		this.perceptronsumY = p.perceptronsumY;
		this.squaredperceptronsumY = p.squaredperceptronsumY;
		this.perceptronYSeen=p.perceptronYSeen;
	}

	public void setWeights(double[] w)
	{
		this.weightAttribute = w;	    
	}

	public double[] getWeights()
	{
		return this.weightAttribute;	    
	}
	


	public int getInstancesSeen() {
		return perceptronInstancesSeen;
	}

	public void setInstancesSeen(int pInstancesSeen) {
		this.perceptronInstancesSeen = pInstancesSeen;
	}


	/**
	 * A method to reset the model
	 */
	public void resetLearningImpl() {
		this.initialisePerceptron = true; 
		this.reset(); 
	}

	public void reset(){
		this.nError=0.0;
		this.accumulatedError = 0.0;
		this.perceptronInstancesSeen = 0;	
		this.perceptronattributeStatistics = new DoubleVector();
		this.squaredperceptronattributeStatistics = new DoubleVector();
		this.perceptronsumY = 0.0;
		this.squaredperceptronsumY = 0.0;
		this.perceptronYSeen=0;
	}

	public void resetError(){
		this.nError=0.0;
		this.accumulatedError = 0.0;
	}

	/**
	 * Update the model using the provided instance
	 */
	public void trainOnInstanceImpl(Instance inst) {
		accumulatedError= Math.abs(this.prediction(inst)-inst.classValue()) + fadingFactor*accumulatedError;
		nError=1+fadingFactor*nError;
		// Initialise Perceptron if necessary   
		if (this.initialisePerceptron == true) {
			this.fadingFactor=this.fadingFactorOption.getValue();
			this.classifierRandom.setSeed(randomSeedOption.getValue()); 
			this.initialisePerceptron = false; // not in resetLearningImpl() because it needs Instance!
			this.weightAttribute = new double[inst.numAttributes()];
			for (int j = 0; j < inst.numAttributes(); j++) {
				weightAttribute[j] = 2 * this.classifierRandom.nextDouble() - 1;
			}
			// Update Learning Rate
			learningRatio = learningRatioOption.getValue();
			this.learningRateDecay = learningRateDecayOption.getValue();
			
		}

		// Update attribute statistics
		this.perceptronInstancesSeen++;
		this.perceptronYSeen++;
		
		
		for(int j = 0; j < inst.numAttributes() -1; j++)
		{
			perceptronattributeStatistics.addToValue(j, inst.value(j));	
			squaredperceptronattributeStatistics.addToValue(j, inst.value(j)*inst.value(j));
		}
		this.perceptronsumY += inst.classValue();
		this.squaredperceptronsumY += inst.classValue() * inst.classValue();

		if(constantLearningRatioDecayOption.isSet()==false){
			learningRatio = learningRatioOption.getValue() / (1+ perceptronInstancesSeen*learningRateDecay); 
		}

		//double prediction = this.updateWeights(inst,learningRatio);
		//accumulatedError= Math.abs(prediction-inst.classValue()) + fadingFactor*accumulatedError;

		
		this.updateWeights(inst,learningRatio);

	}

	/**
	 * Output the prediction made by this perceptron on the given instance
	 */
	private double prediction(Instance inst)
	{
		double[] normalizedInstance = normalizedInstance(inst); 
		double normalizedPrediction = prediction(normalizedInstance);
		return denormalizedPrediction(normalizedPrediction);
	}

	public double normalizedPrediction(Instance inst)
	{
		double[] normalizedInstance = normalizedInstance(inst); 
		double normalizedPrediction = prediction(normalizedInstance);
		return normalizedPrediction;
	}

	private double denormalizedPrediction(double normalizedPrediction) {
		if (this.initialisePerceptron==false){
			double meanY = perceptronsumY / perceptronYSeen;
			double sdY = computeSD(squaredperceptronsumY, perceptronsumY, perceptronYSeen);
			if (sdY > SD_THRESHOLD)	
				return normalizedPrediction * sdY + meanY;
			else
				return normalizedPrediction + meanY;
		}
		else
			return normalizedPrediction; //Perceptron may have been "reseted". Use old weights to predict

	}

	public double prediction(double[] instanceValues)
	{
		double prediction = 0.0;
		if(this.initialisePerceptron == false)
		{
			for (int j = 0; j < instanceValues.length - 1; j++) {
				prediction += this.weightAttribute[j] * instanceValues[j];
			} 
			prediction += this.weightAttribute[instanceValues.length - 1];
		}	
		return prediction;
	}

	public double[] normalizedInstance(Instance inst){
		// Normalize Instance
		double[] normalizedInstance = new double[inst.numAttributes()];
		for(int j = 0; j < inst.numAttributes() -1; j++) {
			int instAttIndex = modelAttIndexToInstanceAttIndex(j, inst);
			double mean = perceptronattributeStatistics.getValue(j) / perceptronYSeen;
			double sd = computeSD(squaredperceptronattributeStatistics.getValue(j), perceptronattributeStatistics.getValue(j), perceptronYSeen);
			if (sd > SD_THRESHOLD) 
				normalizedInstance[j] = (inst.value(instAttIndex) - mean)/ sd; 
			else
				normalizedInstance[j] = inst.value(instAttIndex) - mean;
		}
		return normalizedInstance;
	}

	public  double computeSD(double squaredVal, double val, int size) {
		if (size > 1) {
			return  Math.sqrt((squaredVal - ((val * val) / size)) / (size - 1.0));
		}
		return 0.0;
	}

	public double updateWeights(Instance inst, double learningRatio ){
		// Normalize Instance
		double[] normalizedInstance = normalizedInstance(inst); 
		// Compute the Normalized Prediction of Perceptron
		double normalizedPredict= prediction(normalizedInstance);
		double normalizedY = normalizeActualClassValue(inst);
		double sumWeights = 0.0;		
		double delta = normalizedY - normalizedPredict;

		for (int j = 0; j < inst.numAttributes() - 1; j++) {
			int instAttIndex = modelAttIndexToInstanceAttIndex(j, inst);
			if(inst.attribute(instAttIndex).isNumeric()) {
				this.weightAttribute[j] += learningRatio * delta * normalizedInstance[j];
				sumWeights += Math.abs(this.weightAttribute[j]);
			}
		}
		this.weightAttribute[inst.numAttributes() - 1] += learningRatio * delta;
		sumWeights += Math.abs(this.weightAttribute[inst.numAttributes() - 1]);
		if (sumWeights > inst.numAttributes()) { // Lasso regression
			for (int j = 0; j < inst.numAttributes() - 1; j++) {
				int instAttIndex = modelAttIndexToInstanceAttIndex(j, inst);
				if(inst.attribute(instAttIndex).isNumeric()) {
					this.weightAttribute[j] = this.weightAttribute[j] / sumWeights;
				}
			}
			this.weightAttribute[inst.numAttributes() - 1]  = this.weightAttribute[inst.numAttributes() - 1] / sumWeights;
		}

		return denormalizedPrediction(normalizedPredict);
	}

	public void normalizeWeights(){
		double sumWeights = 0.0;		

		for (int j = 0; j < this.weightAttribute.length ; j++) {
			sumWeights += Math.abs(this.weightAttribute[j]);
		}
		for (int j = 0; j < this.weightAttribute.length; j++) {
			this.weightAttribute[j] = this.weightAttribute[j] / sumWeights;
		}	
	}

	private double normalizeActualClassValue(Instance inst) {
		double meanY = perceptronsumY / perceptronYSeen;
		double sdY = computeSD(squaredperceptronsumY, perceptronsumY, perceptronYSeen);

		double normalizedY = 0.0;
		if (sdY > SD_THRESHOLD){
			normalizedY = (inst.classValue() - meanY) / sdY;
		}else{
			normalizedY = inst.classValue() - meanY;
		}
		return normalizedY;
	}

	@Override
	public boolean isRandomizable() {
		return true;
	}

	@Override
	public double[] getVotesForInstance(Instance inst) {
		return new double[]{this.prediction(inst)};
	}

	@Override
	protected Measurement[] getModelMeasurementsImpl() {
		return null;
	}

	@Override
	public void getModelDescription(StringBuilder out, int indent) {
		if(this.weightAttribute!=null){
			for(int i=0; i< this.weightAttribute.length-1; ++i)
			{
				if(this.weightAttribute[i]>=0 && i>0)
					out.append(" +" + Math.round(this.weightAttribute[i]*1000)/1000.0 + " X" + i );
				else
					out.append(" " + Math.round(this.weightAttribute[i]*1000)/1000.0 + " X" + i );
			}
			if(this.weightAttribute[this.weightAttribute.length-1]>=0 )
				out.append(" +" + Math.round(this.weightAttribute[this.weightAttribute.length-1]*1000)/1000.0);
			else
				out.append(" " + Math.round(this.weightAttribute[this.weightAttribute.length-1]*1000)/1000.0);
		}	
	}

	public void setLearningRatio(double learningRatio) {
		this.learningRatio=learningRatio;

	}
	
	public double getCurrentError()
	{
		if (nError>0)
			return accumulatedError/nError;
		else 
			return Double.MAX_VALUE;
	}

}


