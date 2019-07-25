package moa.evaluation;

public class NamesMetrics {

	public boolean ACCURACY = true;
	public boolean TIME = true;
	public boolean MEMORY = true;
	public boolean ENTROPY = true;
	public boolean MDR = true;
	public boolean MTD = true;
	public boolean MTFA = true;
	public boolean MTR = true;
	
	public boolean DRIFT_POINT_DISTANCE = true;
	public boolean DRIFT_MEANS = true;
	public boolean DRIFT_GENERAL_MEAN = true;
	public boolean FN_FP_TN_TP = true;	
	public boolean RESUME_METRICS = true;
	
	public boolean PRECISION = true;
	public boolean RECALL = true;
	public boolean MCC = true;
	public boolean F1 = true;
	
	
	public boolean FN = true;
	public boolean FP = true;
	public boolean TN = true;
	public boolean TP = true;
	
	public boolean ACCURACY_DETECTION = true;
//	public boolean KAPPA_DETECTION = true;
//	public boolean YOUDEN_DETECTION = true;
	
	public boolean isACCURACY_DETECTION() {
		return ACCURACY_DETECTION;
	}

	public void setACCURACY_DETECTION(boolean aCCURACY_DETECTION) {
		ACCURACY_DETECTION = aCCURACY_DETECTION;
	}

//	public boolean isKAPPA_DETECTION() {
//		return KAPPA_DETECTION;
//	}
//
//	public void setKAPPA_DETECTION(boolean kAPPA_DETECTION) {
//		KAPPA_DETECTION = kAPPA_DETECTION;
//	}
//
//	public boolean isYOUDEN_DETECTION() {
//		return YOUDEN_DETECTION;
//	}
//
//	public void setYOUDEN_DETECTION(boolean yOUDEN_DETECTION) {
//		YOUDEN_DETECTION = yOUDEN_DETECTION;
//	}
	
	public boolean isFN() {
		return FN;
	}

	public void setFN(boolean fN) {
		FN = fN;
	}

	public boolean isFP() {
		return FP;
	}

	public void setFP(boolean fP) {
		FP = fP;
	}

	public boolean isTN() {
		return TN;
	}

	public void setTN(boolean tN) {
		TN = tN;
	}

	public boolean isTP() {
		return TP;
	}

	public void setTP(boolean tP) {
		TP = tP;
	}

	public boolean isDRIFT_POINT_DISTANCE() {
		return DRIFT_POINT_DISTANCE;
	}

	public void setDRIFT_POINT_DISTANCE(boolean dRIFT_POINT_DISTANCE) {
		DRIFT_POINT_DISTANCE = dRIFT_POINT_DISTANCE;
	}

	public boolean isDRIFT_MEANS() {
		return DRIFT_MEANS;
	}

	public void setDRIFT_MEANS(boolean dRIFT_MEANS) {
		DRIFT_MEANS = dRIFT_MEANS;
	}

	public boolean isDRIFT_GENERAL_MEAN() {
		return DRIFT_GENERAL_MEAN;
	}

	public void setDRIFT_GENERAL_MEAN(boolean dRIFT_GENERAL_MEAN) {
		DRIFT_GENERAL_MEAN = dRIFT_GENERAL_MEAN;
	}

	public boolean isFN_FP_TN_TP() {
		return FN_FP_TN_TP;
	}

	public void setFN_FP_TN_TP(boolean fN_FP_TN_TP) {
		FN_FP_TN_TP = fN_FP_TN_TP;
	}

	public boolean isRESUME_METRICS() {
		return RESUME_METRICS;
	}

	public void setRESUME_METRICS(boolean rESUME_METRICS) {
		RESUME_METRICS = rESUME_METRICS;
	}

	public boolean isACCURACY() {
		return ACCURACY;
	}

	public void setACCURACY(boolean aCCURACY) {
		ACCURACY = aCCURACY;
	}

	public boolean isTIME() {
		return TIME;
	}

	public void setTIME(boolean tIME) {
		TIME = tIME;
	}

	public boolean isMEMORY() {
		return MEMORY;
	}

	public void setMEMORY(boolean mEMORY) {
		MEMORY = mEMORY;
	}

	public boolean isENTROPY() {
		return ENTROPY;
	}

	public void setENTROPY(boolean eNTROPY) {
		ENTROPY = eNTROPY;
	}

	public boolean isMDR() {
		return MDR;
	}

	public void setMDR(boolean mDR) {
		MDR = mDR;
	}

	public boolean isMTD() {
		return MTD;
	}

	public void setMTD(boolean mTD) {
		MTD = mTD;
	}

	public boolean isMTFA() {
		return MTFA;
	}

	public void setMTFA(boolean mTFA) {
		MTFA = mTFA;
	}

	public boolean isMTR() {
		return MTR;
	}

	public void setMTR(boolean mTR) {
		MTR = mTR;
	}

	public boolean isPRECISION() {
		return PRECISION;
	}

	public void setPRECISION(boolean pRECISION) {
		PRECISION = pRECISION;
	}

	public boolean isRECALL() {
		return RECALL;
	}

	public void setRECALL(boolean rECALL) {
		RECALL = rECALL;
	}

	public boolean isMCC() {
		return MCC;
	}

	public void setMCC(boolean mCC) {
		MCC = mCC;
	}

	public boolean isF1() {
		return F1;
	}

	public void setF1(boolean f1) {
		F1 = f1;
	}
	
}
