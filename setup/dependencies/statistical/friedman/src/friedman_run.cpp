#include <iostream>
using std::cin;
using std::cout;
using std::string;

#include <algorithm>
using std::sort;

#include <cmath>
using std::sqrt;
using std::min;
using std::max;


#include <boost/math/distributions/normal.hpp>
using boost::math::normal;

#include "Friedman.h"

int main() {
	string s, test, conf;
	vector<string> methods;
	int row, column, cv_pos;
	bool error=false;
	double CD;

	cin >> row >> column;

	for ( int i=0; i<column; i++ ) {
		cin >> s;
		methods.push_back(s);
	}

	cin >> test >> conf;
	time_t rawtime; time (&rawtime);

	cout << "Input Data - Executed on " << ctime (&rawtime) << "\n";
	cout << "Statistic: " << test << "\n";
	cout << "Confidence: " << conf << "\n\n";
	for ( int i=0; i<(int)methods.size(); i++ ) {
		cout << methods[i] << "\t";
	} cout << "\n";
	vector<vector<double>> v; vector<double> aux(column);
	for ( int i=0; i<row; i++ ) {
		for ( int j=0; j<column; j++ ) {
			cin >> aux[j];
			//aux[j] *= -1; // Reverse
			cout << aux[j] << "\t";
		} cout << "\n";
		v.push_back(aux);
	}

	Friedman f(v);

	cout << "\n\n"; cout << "Ranks\n\n";
	for ( int i=0; i<(int)methods.size(); i++ ) {
		cout << methods[i] << "\t";
	} cout << "\n";
	v.clear(); v = f.get_positions_rank();
	for ( int i=0; i<row; i++ ) {
		for ( int j=0; j<column; j++ ) {
			cout << v[i][j] << "\t";
		} cout << "\n";
	}

	cout << "\n\n";
	for ( int i=0; i<(int)methods.size(); i++ ) {
		cout << methods[i] << "\t";
	} cout << "\n";
	vector<double> a = f.get_mean_positions_rank();
	for ( int i=0; i<column; i++ ) {
		cout << a[i] << "\t";
	}

	cout << "\n\n";

	if (conf == "0.90") {
		f.set_confidence(Confidence::confidence90);
		cv_pos=0;
	} else if (conf == "0.95") {
		f.set_confidence(Confidence::confidence95);
		cv_pos=1;
	} else if (conf == "0.99") {
		f.set_confidence(Confidence::confidence99);
		cv_pos=2;
	} else {
		cout << "Error: Invalid confidence.\n";
		error = true;
	}

	vector<stats> vs;
	if ( test == "Bonferroni-Dunn" ) {
		vs = f.post_test(PostTest::BonferroniDunn);
	} else if ( test == "Nemenyi" ) {
		vs = f.post_test(PostTest::Nemenyi);
	} else if ( test == "Holm" ) {
		vs = f.post_test(PostTest::Holm);
	} else if ( test == "Shaffer" ) {
		vs = f.post_test(PostTest::Shaffer);
	} else if ( test == "Bergmann-Hommel" ) {
		vs = f.post_test(PostTest::BergmannHommel);
	} else {
		cout << "Error: Invalid statistical method name.\n";
		error = true;
	}

	if ( !error ) {
		if ( test == "Bonferroni-Dunn" || test == "Nemenyi" ) {
			// max: 50 methods; min: 2 methods (99, 95 and 90 of confidence avaliable)
			std::vector<std::vector<double>> nemenyi_cv = {{2.576,2.913,3.113,3.255,3.364,3.452,3.526,3.590,3.646,3.696,3.741,3.781,3.818,3.853,3.884,3.914,3.941,3.967,3.992,4.015,4.037,4.057,4.077,4.096,4.114,4.132,4.148,4.164,4.179,4.194,4.208,4.222,4.236,4.249,4.261,4.273,4.285,4.296,4.307,4.318,4.329,4.339,4.349,4.359,4.368,4.378,4.387,4.395,4.404},
					{1.960,2.344,2.569,2.728,2.850,2.948,3.031,3.102,3.164,3.219,3.268,3.313,3.354,3.391,3.426,3.458,3.489,3.517,3.544,3.569,3.593,3.616,3.637,3.658,3.678,3.696,3.714,3.732,3.749,3.765,3.780,3.795,3.810,3.824,3.837,3.850,3.863,3.876,3.888,3.899,3.911,3.922,3.933,3.943,3.954,3.964,3.973,3.983,3.992},
					{1.645,2.052,2.291,2.460,2.589,2.693,2.780,2.855,2.920,2.978,3.030,3.077,3.120,3.159,3.196,3.230,3.261,3.291,3.319,3.346,3.371,3.394,3.417,3.439,3.459,3.479,3.498,3.516,3.533,3.550,3.567,3.582,3.597,3.612,3.626,3.640,3.653,3.666,3.679,3.691,3.703,3.714,3.726,3.737,3.747,3.758,3.768,3.778,3.788}};

			// max: 10 methods; min: 2 methods (95 and 90 of confidence avaliable)
			std::vector<std::vector<double>> bonferroni_cv = {{},
					{1.960, 2.241, 2.394, 2.498, 2.576, 2.638, 2.690, 2.724, 2.773, 2.807, 2.838, 2.865, 2.891, 2.914, 2.935, 2.955, 2.974, 2.991, 3.008, 3.023, 3.038, 3.052, 3.065, 3.078, 3.090, 3.102, 3.113, 3.124, 3.134, 3.144},
					{1.645,1.960,2.128,2.241,2.326,2.394,2.450,2.498,2.539}};

			if ( test == "Bonferroni-Dunn" ) {
				CD = bonferroni_cv[cv_pos][column-2] * sqrt( (double)((column*(column+1)) / (double)(6*row)) );
			} else {
				CD = nemenyi_cv[cv_pos][column-2] * sqrt( (double)((column*(column+1)) / (double)(6*row)) );
			}

			cout << "CD = " << CD << "\n\n";
		}

		normal n;
		cout << "Ways to reject the hypothesis:\n";
		if ( test == "Bonferroni-Dunn" || test == "Nemenyi" ) {
			cout << "-> |R1 - R2| >= " << CD << " (CD Confidence: " << conf << ")\n";
		}
		cout << "-> P-value < " << vs[0].alpha << " (Confidence: " << conf << ")\n\n";

		for ( int i=0; i<(int)methods.size(); i++ ) {
			cout << "Method " << methods[i] <<"\t is statistically superior to: ";
			for ( int j=0; j<(int)vs.size(); j++ ) {
				if ( vs[j].hypothesis.first == i ) {
					if ( a[vs[j].hypothesis.first]<a[vs[j].hypothesis.second] && vs[j].reject ) {
						cout << methods[vs[j].hypothesis.second] << " ";
					}
				} else if ( vs[j].hypothesis.second == i ) {
					if ( a[vs[j].hypothesis.second]<a[vs[j].hypothesis.first] && vs[j].reject ) {
						cout << methods[vs[j].hypothesis.first] << " ";
					}
				}
			}
			cout << "\n";
		}

		cout << "\n";
		for ( int i=0; i<(int)vs.size(); i++ ) {
			cout << "[" << methods[vs[i].hypothesis.first] << ((a[vs[i].hypothesis.first]<a[vs[i].hypothesis.second])?"*":"") << "," << methods[vs[i].hypothesis.second] << ((a[vs[i].hypothesis.first]>a[vs[i].hypothesis.second])?"*":"") << "]\n";
			cout << "z = " << vs[i].z << "\n";
			cout << "p-value = " << vs[i].p_value << "\n";
			cout << "alpha = " << vs[i].alpha << "\n";
			cout << "reject = " << ((vs[i].reject)?"Yes":"No") << "\n\n";
		}
	}

	return 0;
}
