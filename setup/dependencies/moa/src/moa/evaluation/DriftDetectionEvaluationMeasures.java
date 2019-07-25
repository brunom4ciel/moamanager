package moa.evaluation;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Locale;

import org.apache.commons.math3.distribution.TDistribution;

import org.apache.commons.math3.stat.descriptive.DescriptiveStatistics;

import moa.core.StringUtils;


public class DriftDetectionEvaluationMeasures extends NamesMetrics{
	
	public List<Integer> assessDriftIssues = new ArrayList<>();
	
	public List<Double> measureMDR = new ArrayList<>();
	public List<Double> measureMTD = new ArrayList<>();
	public List<Double> measureMTFA = new ArrayList<>();
	public List<Double> measureMTR = new ArrayList<>();
    public List<Double> meanEntropy = new ArrayList<>();
	public List<Double> meanAccuracy = new ArrayList<>();
	public List<Double> meanTime = new ArrayList<>();
	public List<Double> meanMemory = new ArrayList<>();
	public List<Double> measureMCC = new ArrayList<>();
	public List<Double> measurePrecision = new ArrayList<>();
	public List<Double> measureRecall = new ArrayList<>();	
	public List<Double> measureF1 = new ArrayList<>();
//	public List<Double> measureKappa = new ArrayList<>();
//	public List<Double> measureYouden = new ArrayList<>();
	public List<Double> measureDetectionAccuracy = new ArrayList<>();
	
    public List<Double> FP = new ArrayList<>();
    public List<Double> FN = new ArrayList<>();
    public List<Double> TP = new ArrayList<>();
    public List<Double> TN = new ArrayList<>();
    public List<Double> CD = new ArrayList<>();
    public List<Integer> positions;
    public List<Integer> widths;
    public double accuracy, ctime, memory, fp, fn, tp, tn;
    public int frequency, curr, sz, wt, middlePos;
    public int tolerance;
    public String lineValues = "";
//    private boolean drift;
    

	private int[] measuresTDD;
	private int[] measuresARLSum;
	private int[] measuresARLCount;
	private int[] conceptDriftFirstInstance;
	private int[] lastDriftIssue;
	private int repetition = 0;
	private double alpha = 0;	
	private DriftDetectionEvaluationMetrics[] ddemetrics;// = new DriftDetectionEvaluationMetrics[0];	

	private boolean isChangeDetectMethod = false;
	
    private long maxInstances;
    
    private final int decimal = 8;//2;
    private final int smallDecimal = 8;//4;
    
    public static final String classPackageTitle = "moa.evaluante.DriftDetectionEvaluationMeasures";
    
    public static final String workbenchTitle = "Drift Detection Evaluation Measures";

    public static final String versionString = "1.1 July 2019, 1.0 Dezember 2018";
    
    public static final String authorString = "Silas Garrido Teixeira de Carvalho Santos <sgtcs@cin.ufpe.br>,"
    		+ "\n\tBruno Iran Ferreira Maciel <bifm@cin.ufpe.br>,"
    		+ "\n\tRohgi Toshio Meneses <rtmc2@cin.ufpe.br>,"
    		+ "\n\tRoberto Souto Maior de Barros <roberto@cin.ufpe.br>";

    public static final String copyrightNotice = "(C) 2015-2019 CIn (Informatic Center) of UFPE (Federal University of Pernambuco), Pernambuco, Brazil";

    public static final String webAddress = "http://cin.ufpe.br,\n\thttps://sites.google.com/view/conceptdrift/,\n\thttps://sites.google.com/site/moamethods/";
    
    public static String getWorkbenchInfoString() {
        StringBuilder result = new StringBuilder();
        
        result.append(classPackageTitle);
        StringUtils.appendNewline(result);
        
        result.append(workbenchTitle);
        StringUtils.appendNewline(result);
        
        result.append("Version: ");
        result.append(versionString);
        StringUtils.appendNewline(result);
        
        result.append("Author: ");
        result.append(authorString);
        StringUtils.appendNewline(result);
        
        result.append("Copyright: ");
        result.append(copyrightNotice);
        StringUtils.appendNewline(result);
        result.append("Web: ");
        result.append(webAddress);
        return result.toString();
    }
    
    
	public long getMaxInstances() {
		return maxInstances;
	}

	public void setMaxInstances(long maxInstances) {
		this.maxInstances = maxInstances;
	}


	
		
	public boolean isChangeDetectMethod() {
		return isChangeDetectMethod;
	}


	public void setChangeDetectMethod(boolean isChangeDetectMethod) {
		this.isChangeDetectMethod = isChangeDetectMethod;
	}


	public DriftDetectionEvaluationMetrics[] getDdemetrics() {
		return ddemetrics;
	}

	public void setDdemetrics(DriftDetectionEvaluationMetrics[] ddemetrics) {
		this.ddemetrics = ddemetrics;
	}

	public double getAlpha() {
		return alpha;
	}

	public void setAlpha(double alpha) {
		this.alpha = alpha;
	}

	public int getRepetition() {
		return repetition;
	}

	public void setRepetition(int repetition) {
		this.repetition = repetition;
	}	
	
	
	public void getPrintData(int chooseFormatData) {
		
		for(int i = 0; i < this.ddemetrics.length; i++) {
					
			if(this.isACCURACY()) {
				this.meanAccuracy.add(this.ddemetrics[i].meanAccuracy);
			}
			
			if(this.isTIME()) {
				this.meanTime.add(this.ddemetrics[i].meanTime);
			}
			
			if(this.isMEMORY()) {
				this.meanMemory.add(this.ddemetrics[i].meanMemory);
			}
		}
		
//		if( this.isACCURACY()
//        		|| this.isTIME()
//        		|| this.isMEMORY()
//        		|| this.isDISSIMILARITY()
//        		|| this.isMDR()
//        		|| this.isMTD()
//        		|| this.isMTFA()
//        		|| this.isMTR()        
//        		|| this.isPRECISION()
//        		|| this.isRECALL()
//        		|| this.isMCC()
//        		|| this.isF1()
//        		|| this.isDRIFT_POINT_DISTANCE()
//            	|| this.isDRIFT_MEANS()
//            	|| this.isDRIFT_GENERAL_MEAN()
//            	|| this.isFN_FP_TN_TP() 
//        		) {
			
			System.out.println(getWorkbenchInfoString());
			System.out.println();
//		}
		
		if(this.isACCURACY()) {
			printReturnData(chooseFormatData, "Accuracy:", meanAccuracy
					, this.alpha, this.getDecimal(), this.getSmallDecimal());
		}
		
		if(this.isTIME()) {
			printReturnData(chooseFormatData, "Time:", meanTime
					, this.alpha, this.getDecimal(), this.getSmallDecimal());
		}
		
		if(this.isMEMORY()) {
			printReturnData(chooseFormatData, "Memory (B/s):", meanMemory
					, this.alpha, this.getDecimal(), this.getSmallDecimal());
		}
		
		
		
		if(this.isChangeDetectMethod) {
				
			for(int i = 0; i < this.ddemetrics.length; i++) {
							
				if(this.isPRECISION() 
	            		||	this.isRECALL()
	            		||	this.isMCC()
	            		||	this.isF1()
	            		||  this.isDRIFT_POINT_DISTANCE()
	                	||  this.isDRIFT_MEANS()
	                	||	this.isDRIFT_GENERAL_MEAN()
	                	||	this.isFN_FP_TN_TP() ) {
					
					this.FP.add(this.ddemetrics[i].fp);
					this.FN.add(this.ddemetrics[i].fn);
					this.TP.add(this.ddemetrics[i].tp);
					this.TN.add(this.ddemetrics[i].tn);
				}
							
				
				this.setMaxInstances(this.ddemetrics[i].getMaxInstances());
				
				this.positions = this.ddemetrics[i].positions;
				
				if(this.isMDR()
	            		||	this.isMTD()
	            		||	this.isMTFA()
	            		||	this.isMTR()) { 
					
					//ROHGI
		            DriftMeasures(this.positions, (int) this.getMaxInstances());
		            
					for(int index = 0; index < this.ddemetrics[i].assessDriftIssues.size(); index++) {
						assessDriftIssues(this.ddemetrics[i].assessDriftIssues.get(index));
					}
					
					double [] measureAux;
					double MDR, MTD, MTFA;
					MDR = MTD = MTFA = 0;
					
//					if(this.isMDR() 
//							|| this.isMTD()){
						measureAux = lazyMeasureMDR();  					
			            
			            MDR = (measureAux[0] / measureAux[1]);
			            
			            measureMDR.add(MDR);
//					}
					
//					if(this.isMTD()
//							|| this.isMDR()){
						measureAux = lazyMeasureMTD();            
			            MTD = ((measureAux[0]) / measureAux[1]);
			            
			            measureMTD.add(MTD);
//					}
		            
//					if(this.isMTFA()
//							|| this.isMDR()){
						measureAux = lazyMeasureMTFA();            
			            MTFA = (measureAux[0] / measureAux[1]); 
			            
			            measureMTFA.add(MTFA);
//					}
					
//					if(this.isMDR()){
						double MTR = 0;
			            MTR = (1 - (MDR))*(MTFA)/(MTD);
			            
			            measureMTR.add(MTR);
//					}					
			            
			            
				}
				
				if(this.isPRECISION()) {
					measurePrecision.add(this.Precision(this.ddemetrics[i].fp, 
							this.ddemetrics[i].tp));
				}
					
				if(this.isRECALL()) {
					measureRecall.add(this.Recall(this.ddemetrics[i].fn,
							this.ddemetrics[i].tp));
				}
				
				if(this.isMCC()) {
					measureMCC.add(this.MCC(this.ddemetrics[i].fp, 
							this.ddemetrics[i].fn,
							this.ddemetrics[i].tp, 
							this.ddemetrics[i].tn));
				}
				
				if(this.isF1()) {
					measureF1.add(this.F1(this.ddemetrics[i].fp, 
							this.ddemetrics[i].fn,
							this.ddemetrics[i].tp, 
							this.ddemetrics[i].tn));
				}
				
				
				if(this.isACCURACY_DETECTION() ) {
					measureDetectionAccuracy.add(this.DetectionAccuracy(this.ddemetrics[i].fp, 
							this.ddemetrics[i].fn,
							this.ddemetrics[i].tp, 
							this.ddemetrics[i].tn));
				}	
				
//				if(this.isKAPPA_DETECTION() ) {
//					measureKappa.add(this.Kappa(this.ddemetrics[i].fp, 
//							this.ddemetrics[i].fn,
//							this.ddemetrics[i].tp, 
//							this.ddemetrics[i].tn));
//				}
//				
//				if(this.isYOUDEN_DETECTION() ) {
//					measureYouden.add(this.Youden(this.ddemetrics[i].fp, 
//							this.ddemetrics[i].fn,
//							this.ddemetrics[i].tp, 
//							this.ddemetrics[i].tn));
//				}
				
				if(this.isDRIFT_POINT_DISTANCE()
	                	||  this.isDRIFT_MEANS()
	                	||	this.isDRIFT_GENERAL_MEAN()
	                	||	this.isFN_FP_TN_TP() ) {
					
					for(int index = 0; index < this.ddemetrics[i].CD.size(); index++) {
						this.CD.add(this.ddemetrics[i].CD.get(index));
					}
					
					this.widths = (this.ddemetrics[i].widths);
					
				}
				
	//			for(int index = 0; index < this.ddemetrics[i].widths.size(); index++) {
	//				this.widths = (this.ddemetrics[i].widths);
	//			}
					
				if(this.isENTROPY()) {
					meanEntropy.add(this.ddemetrics[i].getEntropyValue());
				}				
				
			}
			
			
			if(this.isACCURACY_DETECTION()) {
	        	printReturnData(chooseFormatData, "Detection Accuracy:", measureDetectionAccuracy
        			, this.alpha, this.getDecimal(), this.getSmallDecimal());
	        }
	
			
			if(this.isENTROPY()) {
				printReturnData(chooseFormatData, "Entropy:", meanEntropy
						, this.alpha, 9, 11);
			}
			
			if(this.isMDR()) {
				printReturnData(chooseFormatData, "MDR:", measureMDR
					, this.alpha, this.getDecimal(), this.getSmallDecimal());
			}
			
			if(this.isMTD()) {
				printReturnData(chooseFormatData, "MTD:", measureMTD
					, this.alpha, this.getDecimal(), this.getSmallDecimal());
			}
			
			if(this.isMTFA()) {
				printReturnData(chooseFormatData, "MTFA:", measureMTFA
					, this.alpha, this.getDecimal(), this.getSmallDecimal());
			}
			
			if(this.isMTR()) {
				printReturnData(chooseFormatData, "MTR:", measureMTR
					, this.alpha, this.getDecimal(), this.getSmallDecimal());
			}
			
			if(this.isPRECISION()) {
				printReturnData(chooseFormatData, "Precision:", measurePrecision
					, this.alpha, this.getDecimal(), this.getSmallDecimal());
			}
	        
	        if(this.isRECALL()) {
	        	printReturnData(chooseFormatData, "Recall:", measureRecall
        			, this.alpha, this.getDecimal(), this.getSmallDecimal());
	        }
	        
	        if(this.isMCC()) {
	        	printReturnData(chooseFormatData, "MCC:", measureMCC
        			, this.alpha, this.getDecimal(), this.getSmallDecimal());
	        }
	        
	        if(this.isF1()) {
	        	printReturnData(chooseFormatData, "F1:", measureF1
        			, this.alpha, this.getDecimal(), this.getSmallDecimal());
	        }
	        
	        
	        
	        
//	        
//	        if(this.isKAPPA_DETECTION()) {
//	        	printReturnData(chooseFormatData, "Kappa:", measureKappa
//        			, this.alpha, this.getDecimal(), this.getSmallDecimal());
//	        }
//	        
//	        if(this.isYOUDEN_DETECTION()) {
//	        	printReturnData(chooseFormatData, "Youden:", measureYouden
//        			, this.alpha, this.getDecimal(), this.getSmallDecimal());
//	        }
	        
	        if(this.isFN()) {
	        	printReturnData(chooseFormatData, "FN:", FN
        			, this.alpha, this.getDecimal(), this.getSmallDecimal());
	        }
	        
	        if(this.isFP()) {
	        	printReturnData(chooseFormatData, "FP:", FP
        			, this.alpha, this.getDecimal(), this.getSmallDecimal());
	        }
	        
	        if(this.isTN()) {
	        	printReturnData(chooseFormatData, "TN:", TN
        			, this.alpha, this.getDecimal(), this.getSmallDecimal());
	        }

	        if(this.isTP()) {
	        	printReturnData(chooseFormatData, "TP:", TP
        			, this.alpha, this.getDecimal(), this.getSmallDecimal());
	        }
	        
	        
	        
	  
	        lineValues = "";
	        
	        if(this.isDRIFT_POINT_DISTANCE()
	            	|| this.isDRIFT_MEANS()) {
	        	lineValues += printDistanceDriftTable(CD, positions, getRepetition(), getAlpha());
	        }
	        
//	        if(this.isFN_FP_TN_TP() ) {
//	        	lineValues += printMetrics1(FN,FP,TN,TP, getRepetition(), getAlpha());
//	        }
	        
			
	//        
	//        lineValues += printMetrics2(FN,FP,TN,TP);
	//        lineValues += printMetrics3(measureMDR, measureMTD, measureMTFA, measureMTR);
	                
	//        System.out.println("Mean Distance\t\tFN\tFP\tTN\t\tTP\tPrecision\tRecall\t\tMCC\t\tF1\t\tMDR\tMTFA\tMTD\tMTR");
	        
//	        if(this.isDRIFT_POINT_DISTANCE()
//	            	|| this.isDRIFT_MEANS()
//	            	|| this.isFN_FP_TN_TP() ) {
	        	
	//	        System.out.println("FN\tFP\tTN\t\tTP");
	//	        System.out.println(lineValues);
//	        }
        
		}
		
		
		
	}
	
	
	
	private int getDecimal() {
		return decimal;
	}


	private int getSmallDecimal() {
		return smallDecimal;
	}


	public void printReturnData(int chooseFormatData, String title, List<Double> values, double alpha, int decimal, int smallDecimal) {
		if(chooseFormatData == 1) {
			printDataPlainText(title, values, alpha, decimal, smallDecimal);
		}else if(chooseFormatData == 2) {
			printDataXML(title, values, alpha, decimal, smallDecimal);
		}
	}
	
	public void printDataPlainText(String title, List<Double> values, double alpha, int decimal, int smallDecimal) {
		System.out.println(title);
		System.out.println(printListValues(values, decimal, smallDecimal));
		double mean = this.getAverageOfListValues(values);		
						
		if(values.size()>1) {
			
			double ci = this.getCIValue(values, alpha);	
			System.out.print("Mean (CI) = "+this.getFormatDecimalValue(mean, decimal, smallDecimal));	
			System.out.println(" (+-"+this.getFormatDecimalValue(ci, 2, 2)+")");			
			System.out.println("Mean = "+this.getFormatDecimalValue(mean, decimal, smallDecimal));
			System.out.println("Confidence Interval = "+this.getFormatDecimalValue(ci, decimal, smallDecimal));
			
		}else {
			
			System.out.print("Mean (CI) = "+this.getFormatDecimalValue(mean, decimal, smallDecimal));	
			System.out.println(" (+-N/A)");
			System.out.println("Mean = "+this.getFormatDecimalValue(mean, decimal, smallDecimal));
			System.out.println("Confidence Interval = (+-N/A)");
		}
				
		double gm = this.getGeometricMean(values);
		
		System.out.println("Geometric Mean = "+this.getFormatDecimalValue(gm, decimal, smallDecimal));		
		
		double q2 = this.getQuartile(values, 50);
		System.out.println("Median = "+this.getFormatDecimalValue(q2, decimal, smallDecimal));
		
		double mode = this.getMode(this.getValues(values, decimal, smallDecimal));
		System.out.println("Mode = "+(mode == 0.0? "NaN":this.getFormatDecimalValue(mode, decimal, smallDecimal)));
		
		double sum = this.getSum(values);
		
		System.out.println("Sum = "+this.getFormatDecimalValue(sum, decimal, smallDecimal));
		
		double var = this.getVariance(values);
		System.out.println("Variance = "+this.getFormatDecimalValue(var, decimal, smallDecimal));
		
		double sd = this.getstandardDeviation(values);
		System.out.println("Standard Deviation = "+this.getFormatDecimalValue(sd, decimal, smallDecimal));
		
		double dm = this.getMeanDeviation(values);
		System.out.println("Mean Deviation = "+this.getFormatDecimalValue(dm, decimal, smallDecimal));
		
		
		
		double min = this.getMinValue(values);
		double max = this.getMaxValue(values);
		double amplitude = max - min;
		
		double kurtosis = this.getKurtosis(values);
		double skewness = this.getSkewness(values);
				
		
		double q1 = this.getQuartile(values, 25);		
		double q3 = this.getQuartile(values, 75);
		double p10 = this.getQuartile(values, 10);
		double p90 = this.getQuartile(values, 90);
		double pcKurtosis = this.getPercentageCoefficienteKurtosis(values);
				
		
		System.out.println("Minimum = "+this.getFormatDecimalValue(min, decimal, smallDecimal));
		System.out.println("Maximum = "+this.getFormatDecimalValue(max, decimal, smallDecimal));
		System.out.println("Amplitude = "+this.getFormatDecimalValue(amplitude, decimal, smallDecimal));
		
						
		System.out.println("Lower Quartile 25% (Q1) = "+this.getFormatDecimalValue(q1, decimal, smallDecimal));		
		System.out.println("Upper Quartile 75% (Q3) = "+this.getFormatDecimalValue(q3, decimal, smallDecimal));
		double iqr = q3 - q1;
		System.out.println("Interquartile Range (IQR) = "+this.getFormatDecimalValue(iqr, decimal, smallDecimal));
		
		System.out.println("Percentile 10 = "+this.getFormatDecimalValue(p10, decimal, smallDecimal));	
		System.out.println("Percentile 90 = "+this.getFormatDecimalValue(p90, decimal, smallDecimal));	
		
		System.out.println("kurtosis = "+this.getFormatDecimalValue(kurtosis, decimal, smallDecimal));
		
		
		System.out.println("Percentage Coefficiente Kurtosis = "+this.getFormatDecimalValue(pcKurtosis, decimal, smallDecimal));
		
//		C = 0,263 ==> curva mesocúrtica;
//		C < 0,263  ==> curva leptocúrtica;
//		C > 0,263  ==> curva platicúrtica;
		
		String c = "";
		final double pck_c = 0.263;
		
		String s = this.getFormatDecimalValue(pcKurtosis, 3, 3);
    	pcKurtosis = Double.parseDouble(s);
		
		if(Double.compare(pcKurtosis, pck_c) == 0) {
			c = "Mesokurtic";
		}else if(pcKurtosis < pck_c) {
			c = "Leptokurtic";
		}else {
			c = "Platykurtic";
		}
		
//		System.out.println("Kurtosis curve = "+this.getFormatDecimalValue(pcKurtosis, 3, 3) + " "+c);
		System.out.println("Excess kurtosis = "+c);
			
		
		System.out.println("Skewness = "+this.getFormatDecimalValue(skewness, decimal, smallDecimal));
		
		if(mode > 0) {
						
//	    	mean = Double.parseDouble(this.getFormatDecimalValue(mean, decimal, smallDecimal));
//	    	mode = Double.parseDouble(this.getFormatDecimalValue(mode, decimal, smallDecimal));
//			
//	    	double kd = mean - mode;
//	    	
//	    	if(Double.compare(kd, 0) == 0) {
//				c = "Symmetrical";
//			}else if(kd < 0) {
//				c = "Negative Skew";
//			}else {
//				c = "Positive Skew";
//			}
	    	
			mean = Double.parseDouble(this.getFormatDecimalValue(mean, decimal, smallDecimal));
	    	mode = Double.parseDouble(this.getFormatDecimalValue(mode, decimal, smallDecimal));
	    	q1 = Double.parseDouble(this.getFormatDecimalValue(q1, decimal, smallDecimal));
	    	q2 = Double.parseDouble(this.getFormatDecimalValue(q2, decimal, smallDecimal));
	    	q3 = Double.parseDouble(this.getFormatDecimalValue(q3, decimal, smallDecimal));
	    	sd = Double.parseDouble(this.getFormatDecimalValue(sd, decimal, smallDecimal));
	    	
	    	double kd = (3 * (mean - q2) ) / sd;
	    	
	    	if(q2-q1 < q3 - q2) {
				c = "Positive Skew";
			}else if(q2-q1 > q3 - q2) {
				c = "Negative Skew";
			}else {
				if(kd > 0) {
					c = "Positive Skew";
				}else if(kd < 0){
					c = "Negative Skew";
				}else {
					c = "Symmetrical";				
				}
			}
	    	
	    	System.out.println("Coefficiente Skewness = "+this.getFormatDecimalValue(kd, decimal, smallDecimal));
	    	System.out.println("Skewed Distribution = "+c);
		}else {
			System.out.println("Coefficiente Skewness = NaN");
	    	System.out.println("Skewed Distribution = NaN");
		}
				
				
		System.out.println("Sample Size = "+values.size());		
		
		System.out.print("\n");
	}

	
	public void printDataXML(String title, List<Double> values, double alpha, int decimal, int smallDecimal) {
	
	}
	
	
	private String getFormatDecimalValue(double value, int decimal, int smallDecimal) {
		String result = "";
		
		if(value < 1) {
			result = String.format(Locale.US, "%."+smallDecimal+"f", value);
		}else {
			result = String.format(Locale.US, "%."+decimal+"f", value);
		}	
		return result;
	}
	
	private String printListValues(List<Double> values, int decimal, int smallDecimal) {
		String result = "";
		
		if(values.size() > 0) { 
			for (Double value : values) {
				if(!result.equals("")) {
					result += "\n";
				}
				result += this.getFormatDecimalValue(value, decimal, smallDecimal);
	        }
		}		
		return result;
	}
	
	public double[] getValues(List<Double> values, int decimal, int smallDecimal) {
		double [] result = new double[values.size()];		
		if(values.size() > 0) {			
			for (int index = 0; index < values.size(); index++) {
				String s = this.getFormatDecimalValue(values.get(index), decimal, smallDecimal);
	        	result[index] = Double.parseDouble(s);
	        }
		}		
		return result;
	}
	
	public double getAverageOfListValues(List<Double> values) {
		double result = 0.0;
		
		if(values.size() > 0) { 
			for (Double value : values) {
	        	result += value;
	        }
			if(result > 0) {
				result = result / values.size();			
			}
		}		
		return result;
	}
	
	public double getCIValue(List<Double> values, double alpha) {
		double result = 0.0;
		int size = values.size();
		
		if(size > 1) { 
			TDistribution t = new TDistribution(size-1);
			double s = getstandardDeviation(values);
			
		    result = t.inverseCumulativeProbability(1-((1-alpha)/2.0)) * (s / Math.sqrt(size));
		}
		return result;
	}
	
	private double getVariance(List<Double> values) {
        double result = 0.0;
//        double u = this.getAverageOfListValues(values);
        
        if(values.size() > 0) { 
        	DescriptiveStatistics da = new DescriptiveStatistics();
        	for(Double j: values) {
        		da.addValue(j);
        	}        	
        	result = da.getVariance();			
			//result = result / ((double) (values.size() - 1));
		}        
        return result;
    }
	
	public double getMeanDeviation(List<Double> values) {
		double result = 0.0;		
      
		if(values.size() > 0) { 
			
			double u = this.getAverageOfListValues(values);
			
			for (Double value : values) {
	        	result += Math.sqrt(Math.pow(u - value, 2));
	        }
			result = result / values.size();
		}		
		return result;
	}
	
	private double getstandardDeviation(List<Double> values) {
        double result = 0.0;

        if(values.size() > 0) { 
        	DescriptiveStatistics da = new DescriptiveStatistics();
        	for(Double j: values) {
        		da.addValue(j);
        	}        	
        	result = da.getStandardDeviation();	
//			result = Math.sqrt(getVariance(values));
		}        
        return result;
    }
	
	private double getMode(double[] values) {
        double result = 0.0;
        
        if(values.length > 0) {
            int mfreq = 0;           
            //To sort full array use sort(double[] d) method.
            Arrays.sort(values);
            
    		for (Double i : values) {				
    			int freq = 0;			
                for(Double j : values ) {
                    if( Double.compare(j, i) == 0 ) {
                        freq++;
                    }
                }            
                if(freq > 1) {
                	if( freq > mfreq ) {
                    	result = i;
                        mfreq = freq;
                    }
                }                
            }
		}        
        return result;
    }
	
	
	private double getQuartile(List<Double> values, int quartile) {
        double result = 0.0;
        
        if(values.size() > 0) {    
        	DescriptiveStatistics da = new DescriptiveStatistics();
        	for(Double j: values) {
        		da.addValue(j);
        	}        	
        	result = da.getPercentile(quartile);      			
		}        
        
        return result;
    }
	

	private double getMinValue(List<Double> values) {
        double result = 0.0;
        
        if(values.size() > 0) {    
        	DescriptiveStatistics da = new DescriptiveStatistics();
        	for(Double j: values) {
        		da.addValue(j);
        	} 
        	result = da.getMin();
		}        
        
        return result;
    }
	
	private double getMaxValue(List<Double> values) {
        double result = 0.0;
        
        if(values.size() > 0) {    
        	DescriptiveStatistics da = new DescriptiveStatistics();
        	for(Double j: values) {
        		da.addValue(j);
        	} 
        	result = da.getMax();
		}         
        
        return result;
    }
	
	
	private double getKurtosis(List<Double> values) {
        double result = 0.0;
        
        if(values.size() > 0) {    
        	DescriptiveStatistics da = new DescriptiveStatistics();
        	for(Double j: values) {
        		da.addValue(j);
        	} 
        	result = da.getKurtosis();
		}         
        
        return result;
    }
	
	
	private double getSkewness(List<Double> values) {
        double result = 0.0;
        
        if(values.size() > 0) {    
        	DescriptiveStatistics da = new DescriptiveStatistics();
        	for(Double j: values) {
        		da.addValue(j);
        	} 
        	result = da.getSkewness();
		}         
        
        return result;
    }
	
	
	private double getSum(List<Double> values) {
        double result = 0.0;
        
        if(values.size() > 0) {    
        	DescriptiveStatistics da = new DescriptiveStatistics();
        	for(Double j: values) {
        		da.addValue(j);
        	} 
        	result = da.getSum();
		}         
        
        return result;
    }
	
	
	
	private double getGeometricMean(List<Double> values) {
        double result = 0.0;
        
        if(values.size() > 0) {    
        	DescriptiveStatistics da = new DescriptiveStatistics();
        	for(Double j: values) {
        		da.addValue(j);
        	} 
        	result = da.getGeometricMean();
		}         
        
        return result;
    }
	
	
	
	private double getPercentageCoefficienteKurtosis(List<Double> values) {
        double result = 0.0;
        
        if(values.size() > 0) {    
        	   		
    		double q1 = this.getQuartile(values, 25);		
    		double q3 = this.getQuartile(values, 75);
    		double p10 = this.getQuartile(values, 10);
    		double p90 = this.getQuartile(values, 90);
    		
    		result = (q3-q1) / 2 * (p90 - p10);        	
		}         
        
        return result;
    }
	
	
	
	
	
	
	
	public void DriftMeasures(List<Integer> positions, int instanceLimit) {
		
		//Add elements for the left and right stream bounds
		int conceptsBoundsCount = positions.size() + 2;
		
		this.conceptDriftFirstInstance = new int[conceptsBoundsCount];
		this.measuresTDD = new int[conceptsBoundsCount];
		this.measuresARLSum = new int[conceptsBoundsCount];
		this.measuresARLCount = new int[conceptsBoundsCount];
		this.lastDriftIssue = new int[conceptsBoundsCount];
		
		
		for (int i = 0; i < conceptsBoundsCount; i++) {
			
			this.conceptDriftFirstInstance[i] = (( i > 0 && i < (conceptsBoundsCount - 1) ) ? positions.get(i - 1) : 0);
			this.measuresTDD[i] = -1;
			this.measuresARLSum[i] = 0;
			this.measuresARLCount[i] = 0;
			this.lastDriftIssue[i] = 0;
		}
		
		//by default the first concept has TTD zero
		this.measuresTDD[0] = 0;
		
		//Define the lower bound of the first concept and the upper bound of the last concept.
		this.conceptDriftFirstInstance[0] = 0;
		this.conceptDriftFirstInstance[conceptsBoundsCount - 1] = instanceLimit;
	}
	
	public void assessDriftIssues(int driftIssue) {
		
		int lastDriftPosition = 0;
		
		for (int i = 1; i < this.conceptDriftFirstInstance.length; i++) {
			
			lastDriftPosition = i - 1;
			if(this.conceptDriftFirstInstance[lastDriftPosition] <= driftIssue 
					&&  driftIssue < this.conceptDriftFirstInstance[i]) {
				
				if (this.measuresTDD[lastDriftPosition] < 0) {
					
					//The first drift issue will be assessed as a true detection
					this.measuresTDD[lastDriftPosition] = (driftIssue - this.conceptDriftFirstInstance[lastDriftPosition]);
				}else {
					
					if (this.lastDriftIssue[lastDriftPosition] == 0) {
						
						this.lastDriftIssue[lastDriftPosition] = driftIssue;
						this.measuresARLSum[lastDriftPosition] += (driftIssue - this.conceptDriftFirstInstance[lastDriftPosition]);
						
					}else {
						
						this.measuresARLSum[lastDriftPosition] += (driftIssue - this.lastDriftIssue[lastDriftPosition]);
						this.lastDriftIssue[lastDriftPosition] = driftIssue;
						
					}
					
					this.measuresARLCount[lastDriftPosition]++;
				}
			}
		}
		
		
	}
	
	public int[] getByConceptMeasuresARLSum() {
		return this.measuresARLSum;
	}
	
	public int[] getByConceptMeasuresARLCount() {
		return this.measuresARLCount;
	}
	
	public int[] getByConceptMeasuresTTD() {
		return this.measuresTDD;
	}
	
	public int[] getconceptDriftFirstInstance() {
		return this.conceptDriftFirstInstance;
	}
	
	public String measuresDriftDebug() {
		String out = "";
		
        out = "\n\nConcept\tARLCount\tARLSum\tTTD\tdrift";
        for (int i= 0; i < (this.getByConceptMeasuresARLCount().length - 1); i++) {
        	out += "\n"+ i
        			+ "\t" + this.getByConceptMeasuresARLCount()[i]
        			+ "\t\t " + ((this.getByConceptMeasuresARLSum()[i] == 0)?((this.conceptDriftFirstInstance[i + 1] - this.conceptDriftFirstInstance[i])+"*"):this.getByConceptMeasuresARLSum()[i])
        			+ "\t " + ((this.getByConceptMeasuresTTD()[i] < 0)?((this.conceptDriftFirstInstance[i + 1] - this.conceptDriftFirstInstance[i])+"*"):this.getByConceptMeasuresTTD()[i])
        			+ "\t" +  this.getconceptDriftFirstInstance()[i];
        			
        	
        }
        
        return out;
	}

	
	/**
	 * Mean Time between False Alarms (lazy result)
	 * @return double[]
	 */
	public double[] lazyMeasureMTFA() {
		int count = 0;
		double measure = 0.0;
		double[] fractional = {0.0,0.0};
		
		for ( int i = 0; i < (this.measuresARLSum.length - 1); i++) {
			
			if (this.measuresARLCount[i] > 0) {
				measure += this.measuresARLSum[i]/(double)this.measuresARLCount[i];
			}else {
				measure += (this.conceptDriftFirstInstance[i + 1] - this.conceptDriftFirstInstance[i]);
			}
			count++;
		}
		
		fractional[0] = measure;
		fractional[1] = count;
		
		return fractional;
	}
	

	
	/**
	 * Mean Time to Detection (lazy result)
	 * @return
	 */
	public double[] lazyMeasureMTD() {
		int count = 0;
		double measure = 0.0;
		double[] fractional = {0.0,0.0};
		
		for ( int i = 1; i < (this.measuresTDD.length - 1); i++) {
			if (this.measuresTDD[i] > 0 ) {
				measure += this.measuresTDD[i];
			}else {
				measure += (this.conceptDriftFirstInstance[i + 1] - this.conceptDriftFirstInstance[i]);
			}
			count++;
		}
		
		fractional[0] = measure;
		fractional[1] = count;
		
		return fractional;
	}
	
	/**
	 * Missed Detection Rate  (lazy result)
	 * @return
	 */
	public double[] lazyMeasureMDR() {
		int count = 0;
		double measure = 0.0;
		double[] fractional = {0.0,0.0};
		
		for ( int i = 1; i < (this.measuresTDD.length - 1); i++) {
			if (this.measuresTDD[i] < 0 ) {
				measure++;				
			}
			count++;
		}

		fractional[0] = measure;
		fractional[1] = count;
		
		return fractional;
	}
	
	private double calcStandardDeviation(double u, List<Double> mean) {
        double s = 0.0;
        int size = (int) mean.size();
        for (int i = 0; i < size; i++) {
            if ( !Double.isNaN(mean.get(i)) ) {
                s += ((mean.get(i) - u) * (mean.get(i) - u));
            }
        }

        return Math.sqrt(s / ((double) (size - 1)));
    }
	
	
	public String printMetrics1( List<Double> FN, List<Double> FP, List<Double> TN, List<Double> TP, int repetition, double alpha ) {
        String results="";
        double n, uFN,uFP,uTN,uTP, sFN,sFP,sTN,sTP, sumFN,sumFP,sumTN,sumTP;
        sumFN=sumFP=sumTN=sumTP=0.0;
        n = FN.size();
        
        System.out.println("FN\tFP\tTN\tTP");
        
        for ( int i=0; i<n; i++ ) {
            System.out.printf("%.0f\t",FN.get(i)); System.out.printf("%.0f\t",FP.get(i));
            System.out.printf("%.0f\t",TN.get(i)); System.out.printf("%.0f\n",TP.get(i));
            
            sumFN += FN.get(i); sumFP += FP.get(i);
            sumTN += TN.get(i); sumTP += TP.get(i);
        }
        
        uFN = sumFN / n; uFP = sumFP / n;
        uTN = sumTN / n; uTP = sumTP / n;
        
        sFN = calcStandardDeviation(uFN, FN); sFP = calcStandardDeviation(uFP, FP);
        sTN = calcStandardDeviation(uTN, TN); sTP = calcStandardDeviation(uTP, TP);
        
        
        if (repetition > 1 ) {
        TDistribution t = new TDistribution(repetition-1);
            System.out.printf("Mean (CI) = ");
            System.out.printf("%.2f (+-%.2f)\t", uFN, t.inverseCumulativeProbability(1-((1-alpha)/2.0)) * (sFN / Math.sqrt(n)));
            System.out.printf("%.2f (+-%.2f)\t", uFP, t.inverseCumulativeProbability(1-((1-alpha)/2.0)) * (sFP / Math.sqrt(n)));
            System.out.printf("%.2f (+-%.2f)\t", uTN, t.inverseCumulativeProbability(1-((1-alpha)/2.0)) * (sTN / Math.sqrt(n)));
            System.out.printf("%.2f (+-%.2f)\n\n", uTP, t.inverseCumulativeProbability(1-((1-alpha)/2.0)) * (sTP / Math.sqrt(n)));
        } else {
            System.out.printf("Mean (CI) = ");
            System.out.printf("%.2f (+-N/A)\t", uFN);
            System.out.printf("%.2f (+-N/A)\t", uFP);
            System.out.printf("%.2f (+-N/A)\t", uTN);
            System.out.printf("%.2f (+-N/A)\n\n", uTP);
        }
        
        System.out.println("FN\tFP\tTN\t\tTP");
        
        results += String.format("%.0f\t%.0f\t%.0f\t\t%.0f", sumFN,sumFP,sumTN,sumTP);
        
        System.out.println(""+results);
        
        results = "";
        
        return results;
    }
	
		
	
	public String printDistanceDriftTable2 (List<Double> cd, List<Integer> positions, int repetition, double alpha) {
        System.out.println("Drift point distance:");
        
        double u, s; 
        String results="";
        int sz = positions.size();
        int pos = 0;        
        double sum[] = new double[sz];
        double freq[] = new double[sz];
        
        for ( int i=0; i<cd.size(); i++ ) {
            if ( cd.get(i) != -1.0 ) {
                System.out.printf("%.2f\t", cd.get(i)-positions.get(pos));
                sum[pos] += cd.get(i)-positions.get(pos);
                freq[pos] += 1.0;
            } else {
                System.out.printf("FN\t");
            }
            
            pos = (pos+1)%sz;
            if ( (i+1)%sz == 0 ) {
                System.out.printf("\n");
            }
        }
        
        List<Double> tempValues = new ArrayList<>();
        List<Double> tempAllValues = new ArrayList<>();
        double sumAll = 0.0;
        
        if ( sz > 0 ) {
            System.out.printf("Mean (CI) = ");
        } else {
            System.out.printf("Mean (CI) = N/A (+-N/A)\t");
        }      
        
        for ( int i=0; i<sz; i++ ) {
            tempValues.clear();
            u = sum[i] / freq[i];
            for ( int j=i; j<cd.size(); j+=sz ) {
                if ( cd.get(j) != -1 ) {
                    tempValues.add(cd.get(j)-positions.get(i));
                    tempAllValues.add(cd.get(j)-positions.get(i));
                    sumAll += (cd.get(j)-positions.get(i));
                }
            }
            s = calcStandardDeviation(u, tempValues);
            if ( Double.isNaN(u) ) {
                System.out.printf("N/A (+-N/A)\t");
            } else {
                if ( repetition > 1 ) {
                    TDistribution t = new TDistribution(repetition-1);
                    System.out.printf("%.2f (+-%.2f)\t", u, t.inverseCumulativeProbability(1-((1-alpha)/2.0)) * (s / Math.sqrt(freq[i])));
                } else {
                    System.out.printf("%.2f (+-N/A)\t", u);
                }
            }
        } 
        
        System.out.printf("\n");
        
        
        tempValues = new ArrayList<>();
        tempAllValues = new ArrayList<>();
        u = 0;
        
        if ( sz > 0 ) {
            System.out.printf("Mean = ");
        } else {
            System.out.printf("Mean = N/A\t");
        }      
        
        for ( int i=0; i<sz; i++ ) {
            u = sum[i] / freq[i];
        } 
        
        System.out.printf("%.2f\t", u);
        System.out.printf("\n");
        
        
        
        
        
        u = sumAll / tempAllValues.size();
        if ( Double.isNaN(u) ) {
            System.out.printf("General Mean = N/A\n\n");
//            results += "N/A\t\t";
        } else {
            System.out.printf("General Mean = %.2f\n\n", u);
//            results += String.format("%.2f\t\t", u);
        }
        
        return results;
    }
	
	
	
	public String printDistanceDriftTable (List<Double> cd, List<Integer> positions, int repetition, double alpha) {
        System.out.println("Drift point distance:");
        
        double u, s; 
        String results="";
        int sz = positions.size();
        int pos = 0;        
        double sum[] = new double[sz];
        double freq[] = new double[sz];
        
        for ( int i=0; i<cd.size(); i++ ) {
            if ( cd.get(i) != -1.0 ) {
                System.out.printf("%.2f\t", cd.get(i)-positions.get(pos));
                sum[pos] += cd.get(i)-positions.get(pos);
                freq[pos] += 1.0;
            } else {
                System.out.printf("FN\t");
            }
            
            pos = (pos+1)%sz;
            if ( (i+1)%sz == 0 ) {
                System.out.printf("\n");
            }
        }
        
        List<Double> tempValues = new ArrayList<>();
        List<Double> tempAllValues = new ArrayList<>();
        double sumAll = 0.0;
        double sumAll2 = 0.0;
        
        if ( sz > 0 ) {
            System.out.printf("Drifts Means = ");
        } else {
            System.out.printf("Drifts Means = N/A (+-N/A)\t");
        }
        
//        double v = Double.parseDouble(this.getFormatDecimalValue((cd.get(j)-positions.get(i)), 2, 2)); 
//        sumAll +=v;        
//        System.out.printf("==%.2f\t\t", v);
        
        for ( int i=0; i<sz; i++ ) {
            tempValues.clear();
            u = sum[i] / freq[i];
            for ( int j=i; j<cd.size(); j+=sz ) {
                if ( cd.get(j) != -1 ) {
                    tempValues.add(cd.get(j)-positions.get(i));
                    tempAllValues.add(cd.get(j)-positions.get(i));
                    sumAll += (cd.get(j)-positions.get(i));                   
                }
            }
            
            u = Double.parseDouble(this.getFormatDecimalValue(u, 2, 2)); 
            sumAll2 += u; 
            
            s = calcStandardDeviation(u, tempValues);
            if ( Double.isNaN(u) ) {
                System.out.printf("N/A (+-N/A)\t");
            } else {
                if ( repetition > 1 ) {
                    TDistribution t = new TDistribution(repetition-1);
                    System.out.printf("%.2f (+-%.2f)\t", u, t.inverseCumulativeProbability(1-((1-alpha)/2.0)) * (s / Math.sqrt(freq[i])));
                } else {
                    System.out.printf("%.2f (+-N/A)\t", u);
                }
            }
        } 
        
        System.out.printf("\n");
        u = sumAll / tempAllValues.size();
//        u = sumAll2 / sz;
        
        if ( Double.isNaN(u) ) {
            System.out.printf("General Mean = N/A\n");
//            results += "N/A\t\t";
        } else {
            System.out.printf("General Mean = %.2f\n", u);
//            results += String.format("%.2f\t\t", u);
        }
        
        
        
        
        
        
        
        
        tempValues = new ArrayList<>();
        tempAllValues = new ArrayList<>();
        u = 0;
        
        if ( sz > 0 ) {
            System.out.printf("Mean (CI) = ");
        } else {
            System.out.printf("Mean (CI) = N/A (+-N/A)\t");
        }      
        
        for ( int i=0; i<sz; i++ ) {
            tempValues.clear();
            u = sum[i] / freq[i];
            for ( int j=i; j<cd.size(); j+=sz ) {
                if ( cd.get(j) != -1 ) {
                    tempValues.add(cd.get(j)-positions.get(i));
                    tempAllValues.add(cd.get(j)-positions.get(i));
                    sumAll += (cd.get(j)-positions.get(i));
                }
            }
            s = calcStandardDeviation(u, tempValues);
            if ( Double.isNaN(u) ) {
                System.out.printf("N/A (+-N/A)\t");
            } else {
                if ( repetition > 1 ) {
                    TDistribution t = new TDistribution(repetition-1);
                    System.out.printf("%.2f (+-%.2f)\t", u, t.inverseCumulativeProbability(1-((1-alpha)/2.0)) * (s / Math.sqrt(freq[i])));
                } else {
                    System.out.printf("%.2f (+-N/A)\t", u);
                }
            }
        } 
        
        System.out.printf("\n");
        
        
        
        tempValues = new ArrayList<>();
        tempAllValues = new ArrayList<>();
        u = 0;
        
        if ( sz > 0 ) {
            System.out.printf("Mean = ");
        } else {
            System.out.printf("Mean = N/A (+-N/A)\t");
        }      
        
        for ( int i=0; i<sz; i++ ) {
            tempValues.clear();
            u = sum[i] / freq[i];
            for ( int j=i; j<cd.size(); j+=sz ) {
                if ( cd.get(j) != -1 ) {
                    tempValues.add(cd.get(j)-positions.get(i));
                    tempAllValues.add(cd.get(j)-positions.get(i));
                    sumAll += (cd.get(j)-positions.get(i));
                }
            }
            System.out.printf("%.2f\t", u);
        } 
        
        System.out.printf("\n");
        
        
        
        
        
        
        
        
        
        
        
        
//        System.out.println("");
//        System.out.println("General drift point distance:");
//        
//        
//        tempValues = new ArrayList<>();
//        tempAllValues = new ArrayList<>();
//        u = 0;
//        
//        if ( sz > 0 ) {
//            System.out.printf("Mean = ");
//        } else {
//            System.out.printf("Mean = N/A\t");
//        }      
//        
//        for ( int i=0; i<sz; i++ ) {
//            u = sum[i] / freq[i];
//        } 
//        
//        System.out.printf("%.2f\t", u);
//        System.out.printf("\n");
        
        
        
        return results;
    }

	/**
	 * Returns an double with F1 calculation. 
	 * 
	 * @author Bruno I. F. Maciel <bifm@cin.ufpe.br>
	 * @param  fp an absolute of sum all false positives.
	 * @param  fn an absolute of sum all false negatives.
	 * @param  tp an absolute of sum all true positives.
	 * @param  tn an absolute of sum all true negatives.
	 * @return      the F1 value
	 */
    public double F1(double fp, double fn, double tp, double tn)
    {
    	double result = 0;
    	double precision,recall;
    	
    	precision=recall=0.0;
    	
        if ( tp != 0.0 ) {
        	precision = Precision(fp, tp);
        	recall = Recall(fn, tp);
        }
    	
        if ( precision != 0.0 ) {
        	result = 2*precision*recall/(precision+recall);
        }
        
    	return result;
    }
    
    /**
	 * Returns an double with recall calculation. 
	 * 
	 * @author Bruno I. F. Maciel <bifm@cin.ufpe.br>
	 * @param  fn an absolute of sum all false negatives.
	 * @param  tp an absolute of sum all true positives.
	 * @return      the recall value
	 */
	public double Recall(double fn, double tp)
    {
    	double result = 0;
    	
        if ( tp != 0.0 ) {
        	result = (tp)/(tp+fn);
        }
    	
    	return result;
    }
	
	/**
	 * Returns an double with precision calculation. 
	 * 
	 * @author Bruno I. F. Maciel <bifm@cin.ufpe.br>
	 * @param  fp an absolute of sum all false positives.
	 * @param  tp an absolute of sum all true positives.
	 * @return	the precision value
	 */
	public double Precision(double fp, double tp)
    {
    	double result = 0;
    	
        if ( tp != 0.0 ) {
        	result = (tp)/(tp+fp);
        }
    	
    	return result;
    }
	
	/**
	 * Returns an double with MCC calculation. 
	 * 
	 * @author Bruno I. F. Maciel <bifm@cin.ufpe.br>
	 * @param  fp an absolute of sum all false positives.
	 * @param  fn an absolute of sum all false negatives.
	 * @param  tp an absolute of sum all true positives.
	 * @param  tn an absolute of sum all true negatives.
	 * @return	the MCC value
	 */
	public double MCC(double fp, double fn, double tp, double tn)
    {
    	double result = 0;
    	
    	if ( fp+tp != 0.0 ) {
            result = (tp*tn-fp*fn)/Math.sqrt((tp+fp)*(tp+fn)*(tn+fp)*(tn+fn));
        }
    	
    	return result;
    }    
	
	/**
	 * Returns an double with Detection Accuracy calculation. 
	 * 
	 * @author Bruno I. F. Maciel <bifm@cin.ufpe.br>
	 * @param  fp an absolute of sum all false positives.
	 * @param  fn an absolute of sum all false negatives.
	 * @param  tp an absolute of sum all true positives.
	 * @param  tn an absolute of sum all true negatives.
	 * @return      the Detection Accuracy value
	 */
    public double DetectionAccuracy(double fp, double fn, double tp, double tn)
    {
    	double result = 1;
    	
    	if ( fp+tp != 0.0 ) {
    				
            result = ((tp+tn)) / ((tp+fn+tn+fp));
        }
        
    	return result;
    }
    
    
//	/**
//	 * Returns an double with KAPPA calculation. 
//	 * 
//	 * @author Bruno I. F. Maciel <bifm@cin.ufpe.br>
//	 * @param  fp an absolute of sum all false positives.
//	 * @param  fn an absolute of sum all false negatives.
//	 * @param  tp an absolute of sum all true positives.
//	 * @param  tn an absolute of sum all true negatives.
//	 * @return      the Kappa value
//	 */
//    public double Kappa(double fp, double fn, double tp, double tn)
//    {//Kappa = (totalAccuracy - randomAccuracy) / (1 - randomAccuracy)
//    	
//    	double result = 1;
//    	
//    	if ( fp+tp != 0.0 ) {
//    		//totalAccuracy = (TP + TN) / (TP + TN + FP + FN)
//    		double totalAccuracy = DetectionAccuracy(fp,fn,tp,tn);
//    		
//    		//randomAccuracy = (ActualFalse * PredictedFalse + ActualTrue * PredictedTrue) / Total*Total
//    		//randomAccuracy = (TN + FP) * (TN + FN) + (FN + TP) * (FP + TP) / Total*Total
//    		double randomAccuracy = ((tn+fp)*(tn+fn) + (fn+tp)*(fp+tp))/ (Math.pow((fp+fn+tp+tn),2));
//    		    		
////    		double p2 = (((tp+fn)*(tn+fp)) + ((tp+fp)*(tn+fn)))/ ((tp+fn)+(tn+fp));
////    				
////            result = ((tp+tn)-p2) / ((tp+fn+tn+fp)-p2);
//            
//            result = (totalAccuracy - randomAccuracy) / (1 - randomAccuracy);
//        }
//        
//    	return result;
//    }
    
//    /**
//	 * Returns an double with Youden calculation. 
//	 * 
//	 * @author Bruno I. F. Maciel <bifm@cin.ufpe.br>
//	 * @param  fp an absolute of sum all false positives.
//	 * @param  fn an absolute of sum all false negatives.
//	 * @param  tp an absolute of sum all true positives.
//	 * @param  tn an absolute of sum all true negatives.
//	 * @return      the Youden value
//	 */
//    public double Youden(double fp, double fn, double tp, double tn)
//    {
//    	double result = 0;
//    	double recall,specificity;
//    	
//    	recall=specificity=0.0;
//    	
//        if ( tp != 0.0 ) {
//        	recall = Recall(fn, tp);
//        	specificity = (tn/(tn+fp));
//        }
//    	
//        if ( recall != 0.0 ) {
//        	result = recall - (1 - specificity);
//        }
//        
//    	return result;
//    }
    
}
