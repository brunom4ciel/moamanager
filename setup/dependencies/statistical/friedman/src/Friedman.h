/*
 * @author Silas Garrido (silas@silasgarrido.com)
 */

#ifndef FRIEDMAN_H
#define FRIEDMAN_H

#include <iostream>
using std::pair;

#include <tuple>
using std::tuple;

#include <vector>
using std::vector;

enum struct PostTest {BonferroniDunn, Nemenyi, Holm, Shaffer, BergmannHommel};
enum struct Confidence {confidence99, confidence95, confidence90};

struct stats {
    pair<int,int> hypothesis;
    double z, p_value, alpha;
    bool reject;
};

class Friedman {
private:
    vector<vector<double>> values;
    vector<vector<double>> positions_rank;
    vector<double> mean_positions_rank;
    int n; // rows
    int k; // columns

    double alpha; // 0.01 = 99% of confidence, 0.05 = 95% and 0.10 = 90%

    double Xf; // Chi-Squared value
    double Ff; // Fisher value

    vector<stats> calcStatistics();
    vector<stats> Bonferroni();
    vector<stats> Nemenyi();
    vector<stats> Holm();
    vector<bool> ShafferMaxHypotheses( int k );
    vector<stats> Shaffer();
    vector<pair<vector<int>,vector<int>>> DepthSearch( vector<int> c, vector<bool> mark );
    vector<vector<pair<int,int>>> ExhaustiveSearch( vector<int> c );
    vector<stats> BergmannHommel();
    tuple<vector<double>,vector<vector<double>>> friedman();

public:
    explicit Friedman( vector<vector<double>> values );
    void set_confidence( Confidence c );
    vector<stats> post_test( PostTest pt );
    bool post_test_required();
    vector<vector<double>> get_positions_rank();
    vector<double> get_mean_positions_rank();
    double get_Ff();
    double get_Xf();
};

#endif