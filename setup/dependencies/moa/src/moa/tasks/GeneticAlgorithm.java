/*
 *    EvaluateInterleavedTestThenTrain.java
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
package moa.tasks;

import java.io.InputStream;
import java.text.DecimalFormat;
import moa.classifiers.Classifier;
import moa.core.ObjectRepository;
import moa.evaluation.ClassificationPerformanceEvaluator;
import moa.evaluation.LearningCurve;
import moa.options.ClassOption;
import moa.options.IntOption;
import moa.options.FloatOption;
import moa.options.MultiChoiceOption;
import moa.streams.InstanceStream;
import static moa.tasks.MainTask.INSTANCES_BETWEEN_MONITOR_UPDATES;
import weka.core.Instance;

import java.util.*;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.xml.parsers.*;
import org.w3c.dom.*;

/**
 * Algorithm based on the description presented in "Genetic Algorithm in Search,
 * Optimization, and Machine Learning" - David E. Goldberg.
 *
 * @author Silas Garrido (sgtcs@cin.ufpe.br)
 */

class XMLReader {
    protected String methodCommand;
    protected String[] methods, methodsDescriptions, parameterType;
    protected double[] lowerLimit, upperLimit, defaultParameter;
    protected Element mainElement;
    protected int parameterSize;
    
    XMLReader() {
        try {
            this.mainElement = readXML("GeneticAlgorithmMethods.xml"); // Path
            this.methods = readElement(this.mainElement, "method", "name");
            this.methodsDescriptions = readElement(this.mainElement, "method", "description");
        } catch (Exception ex) {
            Logger.getLogger(XMLReader.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
    
    private Element readXML(String pathXML) throws Exception {
        InputStream path = getClass().getResourceAsStream(pathXML);
        DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
        DocumentBuilder db = dbf.newDocumentBuilder();
        Document doc = db.parse(path);
        
        Element element = doc.getDocumentElement();
        
        return element;
    }
    
    private String[] readElement( Element e, String tagName, String child ) throws Exception {
        NodeList nl = e.getElementsByTagName(tagName);
        String[] names = new String[nl.getLength()];
        
        for ( int i=0; i<nl.getLength(); i++ ) {
            Element tagElement = (Element)nl.item(i);
            
            NodeList nl2 = tagElement.getElementsByTagName(child);
            tagElement = (Element)nl2.item(0);
            names[i] = tagElement.getFirstChild().getNodeValue();
        }
        
        return names;
    }
    
    private double[] stringToDouble( String[] s ) {
        double[] d = new double[s.length];
        for ( int i=0; i<s.length; i++ ) {
            d[i] = Double.parseDouble(s[i]);
        }
        
        return d;
    }
    
    public void setValuesByIndex( int index ) throws Exception {
        String[] tmp = readElement( this.mainElement, "method", "methodCommand" );
        this.methodCommand = tmp[index];
        
        NodeList nl = this.mainElement.getElementsByTagName("method");
        Element tagElement = (Element)nl.item(index);
        tmp = readElement(tagElement, "parameter", "lowerLimit");
        this.lowerLimit = stringToDouble(tmp);
        tmp = readElement(tagElement, "parameter", "upperLimit");
        this.upperLimit = stringToDouble(tmp);
        if ( tagElement.getElementsByTagName("defaultParameter").getLength() > 0 ) {
            tmp = readElement(tagElement, "parameter", "defaultParameter");
            this.defaultParameter = stringToDouble(tmp);
        } else {
            this.defaultParameter = null;
        }
        tmp = readElement(tagElement, "parameter", "parameterType");
        this.parameterType = tmp;
        this.parameterSize = tmp.length;
    }
}

public class GeneticAlgorithm extends MainTask {

    @Override
    public String getPurposeString() {
        return "Evaluates a classifier on a stream by testing then training with each example in sequence.";
    }

    private static final long serialVersionUID = 1L;
    
    private static final XMLReader xmlr = new XMLReader();

    public ClassOption learnerOption = new ClassOption("learner", 'l',
            "Classifier to train.", Classifier.class, "bayes.NaiveBayes");

    public ClassOption streamOption = new ClassOption("stream", 's',
            "Stream to learn from.", InstanceStream.class,
            "ConceptDriftStream -s (ConceptDriftStream -s generators.AgrawalGenerator -d (generators.AgrawalGenerator -f 2) -p 3000 -w 1) -d (generators.AgrawalGenerator -f 3) -p 6000 -w 1");

    public ClassOption evaluatorOption = new ClassOption("evaluator", 'e',
            "Classification performance evaluation method.",
            ClassificationPerformanceEvaluator.class,
            "BasicClassificationPerformanceEvaluator");

    public IntOption instanceLimitOption = new IntOption("instanceLimit", 'i',
            "Maximum number of instances to test/train on  (-1 = no limit).",
            10000, -1, Integer.MAX_VALUE);
    
    public MultiChoiceOption conceptDriftOption = new MultiChoiceOption(
            "conceptDriftMethod", 'c', "Method that will have the optimized parameters", xmlr.methods, xmlr.methodsDescriptions, 0);

    public IntOption populationSizeOption = new IntOption(
            "populationSize", 'p', "Population Size.",
            20, 0, Integer.MAX_VALUE);

    public IntOption elitismNumberOption = new IntOption(
            "elitismNumber", 'n', "Elitism Number.",
            2, 0, Integer.MAX_VALUE);

    public FloatOption rankMethodRateOption = new FloatOption("rankMethodRate",
            'r', "Rank Method Rate.", 0.2, 0.0, 1.0);

    public IntOption generationsNumberOption = new IntOption(
            "generationsNumber", 'g', "Generations Number.",
            5, 0, Integer.MAX_VALUE);

    public IntOption generationsMutationOption = new IntOption(
            "generationsMutation", 'm', "Generations with Mutation.",
            5, 0, Integer.MAX_VALUE);

    public FloatOption populationMutationOption = new FloatOption(
            "populationMutation", 'x', "population of each generation that will suffer mutation.",
            0.2, 0.0, 1.0);

    protected double[][] population, tempPopulation;
    protected int populationSize, parameterSize, elitismNumber;
    protected int[] rankMethodIndex, generationChosenMutation;
    protected double[] lowerLimit, upperLimit, defaultParameter, fitness;
    protected double rankMethodRate;
    protected Random generator;
    protected String methodCommand;
    protected String[] parameterType;

    @Override
    public Class<?> getTaskResultType() {       
        return LearningCurve.class;
    }

    public void initializeAttributes(int pSize) {
        this.generator = new Random();
        this.parameterSize = pSize;

        this.populationSize = this.populationSizeOption.getValue();
        this.elitismNumber = this.elitismNumberOption.getValue();
        this.rankMethodRate = this.rankMethodRateOption.getValue();

        this.fitness = new double[this.populationSize];

        this.population = new double[this.populationSize][this.parameterSize];
        this.tempPopulation = new double[this.populationSize][this.parameterSize];
    }

    public void initializaMethodParameters() {
        try {
            xmlr.setValuesByIndex(this.conceptDriftOption.getChosenIndex());
            initializeAttributes(xmlr.parameterSize);
            this.lowerLimit = xmlr.lowerLimit;
            this.upperLimit = xmlr.upperLimit;
            this.defaultParameter = xmlr.defaultParameter;
            this.parameterType = xmlr.parameterType;
            this.methodCommand = xmlr.methodCommand;
        } catch (Exception ex) {
            Logger.getLogger(GeneticAlgorithm.class.getName()).log(Level.SEVERE, null, ex);
        }
    }

    public double calcFitness(TaskMonitor monitor, ObjectRepository repository, String CLIString) {
        this.learnerOption.setValueViaCLIString(CLIString);
        prepareClassOptions(monitor, repository);

        Classifier learner = (Classifier) getPreparedClassOption(this.learnerOption);
        InstanceStream stream = (InstanceStream) getPreparedClassOption(this.streamOption);
        ClassificationPerformanceEvaluator evaluator = (ClassificationPerformanceEvaluator) getPreparedClassOption(this.evaluatorOption);
        evaluator.reset();
        learner.setModelContext(stream.getHeader());
        int maxInstances = this.instanceLimitOption.getValue(), posAccuracy = 1;
        long instancesProcessed = 0;
        monitor.setCurrentActivity("Evaluating learner...", -1.0);

        while (stream.hasMoreInstances()
                && ((maxInstances < 0) || (instancesProcessed < maxInstances))) {
            Instance trainInst = stream.nextInstance();
            Instance testInst = (Instance) trainInst.copy();
            double[] prediction = learner.getVotesForInstance(testInst);
            evaluator.addResult(testInst, prediction);
            learner.trainOnInstance(trainInst);
            instancesProcessed++;

            if (instancesProcessed % INSTANCES_BETWEEN_MONITOR_UPDATES == 0) {
                if (monitor.taskShouldAbort()) {
                    return -1;
                }
                long estimatedRemainingInstances = stream.estimatedRemainingInstances();
                if (maxInstances > 0) {
                    long maxRemaining = maxInstances - instancesProcessed;
                    if ((estimatedRemainingInstances < 0)
                            || (maxRemaining < estimatedRemainingInstances)) {
                        estimatedRemainingInstances = maxRemaining;
                    }
                }
                monitor.setCurrentActivityFractionComplete(estimatedRemainingInstances < 0 ? -1.0
                        : (double) instancesProcessed
                        / (double) (instancesProcessed + estimatedRemainingInstances));
            }
        }

        return evaluator.getPerformanceMeasurements()[posAccuracy].getValue();
    }

    public void startPopulation() {
        int defaultSize = (this.defaultParameter != null) ? 1 : 0;
        double[] range = new double[this.parameterSize];
        double[] currentValue = new double[this.parameterSize];
        double randomNumber;
        
        if ( defaultSize == 1 ) {
            this.population[0] = this.defaultParameter;
        }

        for (int i = 0; i < this.parameterSize; i++) {
            range[i] = (this.upperLimit[i] - this.lowerLimit[i]) / (this.populationSize - defaultSize);
            currentValue[i] = this.lowerLimit[i];
        }

        for (int i = defaultSize; i < this.populationSize; i++) {
            for (int j = 0; j < this.parameterSize; j++) {
                randomNumber = range[j] * this.generator.nextDouble();
                this.population[i][j] = currentValue[j] + randomNumber;
                currentValue[j] += range[j];
            }
        }
    }

    public void populationFitnessGeneration(TaskMonitor monitor, ObjectRepository repository) {
        while (this.methodCommand.contains("LEARNER")) {
            this.methodCommand = this.methodCommand.replaceFirst("LEARNER", this.learnerOption.getValueAsCLIString());
        }

        String tmpCommand;
        for (int i = 0; i < this.populationSize; i++) {
            tmpCommand = this.methodCommand;
            for (int j = 0; j < this.parameterSize; j++) {
                switch (this.parameterType[j]) {
                    case "double":
                        tmpCommand = tmpCommand.replaceFirst("varDouble", String.valueOf(this.population[i][j]));
                        break;
                    case "int":
                        tmpCommand = tmpCommand.replaceFirst("varInt", String.valueOf(((int) this.population[i][j])));
                        break;
                }
            }
            this.fitness[i] = calcFitness(monitor, repository, tmpCommand);
        }
 
        double keyAcc;
        double[] keyPopulation;
        int j;
        for (int i = 1; i < this.populationSize; i++) {
            keyAcc = this.fitness[i];
            keyPopulation = this.population[i];
            j = i - 1;
            while (j >= 0 && this.fitness[j] < keyAcc) {
                this.fitness[j + 1] = this.fitness[j];
                this.population[j + 1] = this.population[j];
                j--;
            }
            this.fitness[j + 1] = keyAcc;
            this.population[j + 1] = keyPopulation;
        }
    }

    public void elitism() {
        if ( (this.elitismNumber % 2) != 0 ) {
            this.elitismNumber += 1;
        }

        this.tempPopulation = this.population;
    }

    public void rankMethod() {
        int indexLength = this.populationSize, pos = 0, used = 0;
        this.rankMethodIndex = new int[indexLength];

        int limit = (int) (this.populationSize * this.rankMethodRate), current = 0;
        for (int i = 0; i < indexLength; i++) {
            this.rankMethodIndex[i] = pos;
            current++;

            if (current == limit || limit <= 1) {
                used += limit;
                limit = (int) ((this.populationSize - used) * this.rankMethodRate);
                current = 0;
                pos = ((pos + 1) == this.populationSize) ? 0 : pos + 1;
            }
        }
    }

    public void crossover() {
        boolean sign = false;
        int pair1, pair2, k, indexLength = this.populationSize, tmp;
        for (int i = this.elitismNumber; i < this.populationSize; i += 2) {
            pair1 = rankMethodIndex[generator.nextInt(indexLength)];
            do {
                pair2 = rankMethodIndex[generator.nextInt(indexLength)];
            } while (pair2 == pair1);

            do {
                k = generator.nextInt(this.parameterSize);
                if (this.parameterSize == 1) {
                    break;
                }
            } while (k == 0);

            for (int j = 0; j < this.parameterSize; j++) {
                if (j >= k && !sign) {
                    tmp = pair1;
                    pair1 = pair2;
                    pair2 = tmp;
                    sign = true;
                }
                this.tempPopulation[i][j] = this.population[pair1][j];
                this.tempPopulation[i + 1][j] = this.population[pair2][j];
            }
            sign = false;
        }

        this.population = this.tempPopulation;
    }

    public int[] randomPositionsWithoutRepetitions(int size, int lowerLimit, int upperLimit) {
        int[] ans = new int[size];
        boolean finish;

        for (int i = 0; i < size; i++) {
            finish = false;
            while (!finish) {
                ans[i] = generator.nextInt(upperLimit);
                if (ans[i] < lowerLimit) {
                    ans[i] += lowerLimit;
                }
                finish = true;
                for (int j = i - 1; j >= 0; j--) {
                    if (ans[i] == ans[j]) {
                        finish = false;
                        break;
                    }
                }
            }
        }

        Arrays.sort(ans);

        return ans;
    }

    public void mutation() {
        int size = ((int) ((double) this.populationSize * this.populationMutationOption.getValue()));
        int[] chosenPopulation = randomPositionsWithoutRepetitions(size, this.elitismNumber, this.populationSize);

        double randomDouble;
        int populationPos, chosenParameter;

        for (int i = 0; i < size; i++) {
            populationPos = chosenPopulation[i];
            chosenParameter = generator.nextInt(this.parameterSize);
            randomDouble = generator.nextDouble();

            this.population[populationPos][chosenParameter] = randomDouble * this.upperLimit[chosenParameter];
            if (this.population[populationPos][chosenParameter] < this.lowerLimit[chosenParameter]) {
                this.population[populationPos][chosenParameter] += this.lowerLimit[chosenParameter];
            }
        }
    }
    
    public void printGeneration( int gen ) {
        System.out.println("\nGeneration " + (gen + 1));
        for (int j = 0; j < this.populationSize; j++) {
            for (int w = 0; w < this.parameterSize; w++) {
                System.out.print(this.population[j][w] + " - ");
            }
            System.out.println("[" + this.fitness[j] + "]");
        }
    }

    @Override
    protected Object doMainTask(TaskMonitor monitor, ObjectRepository repository) {
        List<String> m = new ArrayList<>();
        int count = 0;
        int gMutation = this.generationsMutationOption.getValue();
        int gNumber = this.generationsNumberOption.getValue();

        initializaMethodParameters();
        startPopulation();

        if (gMutation > 0) {
            this.generationChosenMutation = randomPositionsWithoutRepetitions(gMutation, 0, gNumber);
        }

        for (int i = 0; i < gNumber; i++) {
            populationFitnessGeneration(monitor, repository);

            printGeneration(i);

            if ( i != gNumber-1 ) {
                elitism();
                rankMethod();
                crossover();

                if ((gMutation > 0) && (this.generationChosenMutation[count] == i)) {
                    mutation();
                    count++;
                }
            }
        }

        DecimalFormat df = new DecimalFormat("#0.00000");
        for (int j = 0; j < this.parameterSize; j++) {
            switch (this.parameterType[j]) {
                case "double":
                    m.add("Parameter" + (j + 1) + ": " + df.format(this.population[0][j]));
                    break;
                case "int":
                    m.add("Parameter" + (j + 1) + ": " + (int) this.population[0][j]);
                    break;
            }
        }
        m.add("Accuracy: " + df.format(this.fitness[0]));

        return m;
    }
}
