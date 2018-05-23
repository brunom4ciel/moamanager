package moa.streams;

import static java.nio.file.StandardOpenOption.APPEND;
import static java.nio.file.StandardOpenOption.CREATE;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.OutputStream;
import java.math.BigDecimal;
import java.math.RoundingMode;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;

import moa.core.InstancesHeader;
import moa.core.ObjectRepository;
import moa.options.AbstractOptionHandler;
import moa.tasks.TaskMonitor;
import weka.core.Instance;

public class DataStream extends AbstractOptionHandler implements
InstanceStream {
	
	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;
	private double []data = new double[0];
	private double p;
	private double n;
	private OutputStream out;
	
	public DataStream(){
		
	}
	
	public DataStream(double p, double n){
		
		data = new double[0];
		
		if(Double.isNaN(p)){
			this.p = 0.0;
		}else{
			this.p = p;
		}
		
		if(Double.isNaN(n)){
			this.n = 1.0;
		}else{
			this.n = n;
		}
	}
	
	public int length(){
		return this.data.length;
	}
	
	public double getErrorOfAccuracy(int newScale){
		double errorAccuracy = 0;
				
		double n = this.getCountOfData(this.n);
		double p = this.getCountOfData(this.p);
		
		errorAccuracy = (double) (n) / (p + n);
		
		return new BigDecimal(errorAccuracy).setScale(newScale, RoundingMode.HALF_UP).doubleValue();
	}
	
	public double getAccuracy(int newScale){
		double accuracy = 0;
				
		double n = this.getCountOfData(this.n);
		double p = this.getCountOfData(this.p);
		
		accuracy = (double) (p) / (p + n);

		return new BigDecimal(accuracy).setScale(newScale, RoundingMode.HALF_UP).doubleValue();
	}
	
//	public double[] getAccuracy(int startMoment, int endMoment, int newScale){
//		double[] accuracy = new double[endMoment-startMoment];
//				
//		double n = 0;//this.getCountOfData(this.n);
//		double p = 0;//this.getCountOfData(this.p);
//		int y = 0;
//		startMoment -= 1;
//		endMoment -= 1;
//		
//		for(int i = startMoment; i < endMoment; i++){
//			
//			System.out.print(""+this.data.length);
//			System.exit(0);
//			
//			if(this.getData(i) == 0.0){
//				p++;
//			}else{
//				n++;
//			}
//
//			accuracy[y] = new BigDecimal((double) (p) / (p + n)).setScale(newScale, RoundingMode.HALF_UP).doubleValue();
//			y++;
//		}	
//
//		return accuracy;
//	}
	
	
	private int getDataLenght(){
		return this.data.length;
	}
	
	
	public double getSumData(){
		
		double sum = 0;
		
		for(int index = 0; index < this.getDataLenght() ; index++)
		{		
			sum +=	this.getData(index);
		}
		
		return sum;
	}

	
	private double getCountOfData(double data){
		
		double sum = 0;
		
		for(int index = 0; index < this.getDataLenght() ; index++){
			if(this.getData(index) == data)				
				sum +=	1;
		}
		
		return sum;
	}
	
	public void printData(){
			
		for(int index = 0; index < this.getDataLenght() ; index++){
			System.out.println(this.getData(index));
		}
		
	}
	
	public void clearData(){
		this.data = new double[0];
	}
	
	public double[] getAllData(){
		return this.data;
	}
	
	public double getData(int index){
		return this.data[index];
	}
	
	public int getCountData(){
		return this.data.length;
	}
	
	private double log2(double d){
	 	   
 	   return Math.log(d)/Math.log(2.0);
    }
    
    private double entropy(double p){
 	   
 	   if (p == 0 || p == 1) return 1;
 	   
 	   double q = 1 - p;
 	   
 	   return -p * log2(p) - q * log2(q);

    }
    
    public double getEntropy()
    {
    	double wr = this.getSumData();
		int windowSize = this.getCountData();
		
    	double err = (double) (wr/windowSize);
    	
    	return entropy(err);
    }
    
    //Kullback Leibler
    
    public double klDivergence(double[] p1, double[] p2) 
    {
    	  double klDiv = 0.0;
    	  
    	  for (int i = 0; i < p1.length; ++i) 
    	  {
    		  if (p1[i] == 0) { continue; }
    		  if (p2[i] == 0.0) { continue; } 

    		  klDiv += p1[i] * Math.log( p1[i] / p2[i] );
    	  }

    	  return klDiv; 
	}

	    
	    
//	public void insertData(double data){//throws Exception{
//		
//		this.data = push(this.data, data);
//		
//	}
	
//	public void shiftPrediction(int index){
//		this.prediction[index] = -1 ;//= this.shift(this.historicPrediction);
//	}

	public void shift() {
		
        double[] longer = new double[this.data.length -1];
        
        for(int i=1;i < this.data.length; i++){
        	longer[i-1]=this.data[i];
        }
        this.data = longer;
    }
    
    public void push(double push) {
        double[] longer = new double[this.data.length + 1];
        System.arraycopy(this.data, 0, longer, 0, this.data.length);
        longer[this.data.length] = push;        
        this.data = longer;
    }
    
    public double getMean(){
    	double sum=0;
    	int n = this.data.length;
    	
    	for(int i=0;i < this.data.length; i++)
    		sum += this.data[i];    		
    	
    	return sum/n;
    }
    
    public double getMean(int newScale){
    	double sum=0;
    	int n = this.data.length;
    	
    	for(int i=0;i < this.data.length; i++)
    		sum += this.data[i];    		
    	
    	return new BigDecimal(sum/n).setScale(newScale, RoundingMode.HALF_UP).doubleValue();
    }
    
    public double getVariance() {
        double s = 0.0;
        int size = this.data.length;
        double mi = this.getMean();
        
        for (int i = 0; i < size; i++) {
            s += Math.pow((this.data[i] - mi), 2);
        }

        return (s / (size - 1));
    }
    
    public double getVariance(int newScale) {
        double s = 0.0;
        int size = this.data.length;
        double mi = this.getMean();
        
        for (int i = 0; i < size; i++) {
            s += Math.pow((this.data[i] - mi), 2);
        }

        return new BigDecimal(s / (size - 1)).setScale(newScale, RoundingMode.HALF_UP).doubleValue();
    }
    
    public double GetStandardError() {

        return Math.sqrt(this.getVariance());
    }
    
    public double GetStandardError(int newScale) {

        return new BigDecimal(Math.sqrt(this.getVariance())).setScale(newScale, RoundingMode.HALF_UP).doubleValue();
    }
    
    
    public boolean save(String fileName, boolean override) throws Exception{
    	
    	boolean result = false;
    	
    	//String filename = "/opt/PMDD-50k-1000W-4CD-AgrawalGenerator.txt";
    			
    	try{
    		
            File f = new File (fileName);              		         
            
            if(f.exists() && !f.isDirectory()) { 
                // do something            	
            	
            	if(override == true){
            		f.delete();
            	}else{
            		
            	}
            	
            }
            
            fileName = f.getAbsolutePath();    		
    		
    		Path p = Paths.get(fileName);
            
    		
    		this.out = new BufferedOutputStream(Files.newOutputStream(p, CREATE, APPEND));
    		
    		String dataoutput = "";
    		
    		for(int index = 0; index < this.getDataLenght() ; index++){
    			
    			//if(dataoutput.equals("")){
    			//	dataoutput = "" + this.getData(index);
    			//}else{
    			//	dataoutput = "\n" + dataoutput + this.getData(index);
    			//}
    			
    			dataoutput = "" + this.getData(index)+"\n";
    			
    			byte data[] = dataoutput.getBytes();			
  		      
    			out.write(data, 0, data.length);
    			
    		}
    		
    		
			    		
    	}catch(Exception e){
    		
    		throw new Exception(e);
    	}
    	
    	return result;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    

	@Override
	public void getDescription(StringBuilder sb, int indent) {
		// TODO Auto-generated method stub
		
	}

	@Override
	public InstancesHeader getHeader() {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public long estimatedRemainingInstances() {
		// TODO Auto-generated method stub
		return 0;
	}

	@Override
	public boolean hasMoreInstances() {
		// TODO Auto-generated method stub
		return false;
	}

	@Override
	public Instance nextInstance() {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public boolean isRestartable() {
		// TODO Auto-generated method stub
		return false;
	}

	@Override
	public void restart() {
		// TODO Auto-generated method stub
		
	}

//	@Override
//	public void changeRandomSeed(int value) {
//		// TODO Auto-generated method stub
//		
//	}

	@Override
	protected void prepareForUseImpl(TaskMonitor monitor, ObjectRepository repository) {
		// TODO Auto-generated method stub
		
	}
    
    
}
