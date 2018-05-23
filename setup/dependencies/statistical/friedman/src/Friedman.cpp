/*
 * @author Silas Garrido (silas@silasgarrido.com)
 */

#include <iostream>
using std::make_pair;

#include <limits>
using std::numeric_limits;

#include <tuple>
using std::get;


#include <algorithm>
using std::sort;

#include <cmath>
using std::sqrt;
using std::min;
using std::max;

#include <boost/math/distributions/normal.hpp>
using boost::math::normal;

#include <boost/math/distributions/fisher_f.hpp>
using boost::math::fisher_f_distribution;

#include "Friedman.h"

bool decrease (double i,double j) { return i>j; }
bool increase (stats i,stats j) { return i.p_value<j.p_value; }

// Private methods

vector<stats> Friedman::calcStatistics() {
    vector<stats> ans; stats tmp;
    double se; normal n;

    tmp.alpha = alpha; tmp.reject = false;
    for ( int i=0; i<this->k; i++ ) {
        for ( int j=i+1; j<this->k; j++ ) {
            se = sqrt((this->k*(this->k+1.0))/(6.0*this->n));
            tmp.z = fabs(this->mean_positions_rank[i]-this->mean_positions_rank[j])/se;
            tmp.p_value = 2.0*(1.0-cdf(n,tmp.z));
            tmp.hypothesis.first = i;
            tmp.hypothesis.second = j;
            ans.push_back(tmp);
        }
    }

    return ans;
}

vector<stats> Friedman::Bonferroni() {
    vector<stats> ans = calcStatistics();

    sort( ans.begin(), ans.end(), increase );

    for ( int i=0; i<(int)ans.size(); i++ ) {
        ans[i].p_value = min( 1.0, (this->k-1)*ans[i].p_value );
        if ( ans[i].p_value <= ans[i].alpha ) {
            ans[i].reject = true;
        }
    }

    return ans;
}

vector<stats> Friedman::Nemenyi() {
    vector<stats> ans = calcStatistics();

    sort( ans.begin(), ans.end(), increase );

    auto m = (int)ans.size(); double maxP = 0.0;
    for ( int i=0; i<m; i++ ) {
        ans[i].p_value = max(maxP, min(1.0,ans[i].p_value*m));
        maxP = ans[i].p_value;
        if ( ans[i].p_value <= ans[i].alpha )  {
            ans[i].reject = true;
        }
    }

    return ans;
}

vector<stats> Friedman::Holm() {
    vector<stats> ans = calcStatistics();

    sort( ans.begin(), ans.end(), increase );

    auto m = (int)ans.size(); double maxP = 0.0;
    for ( int i=0; i<m; i++ ) {
        ans[i].p_value = max(maxP, min(1.0,ans[i].p_value*(m-i)));
        maxP = ans[i].p_value;
        if ( ans[i].p_value <= ans[i].alpha )  {
            ans[i].reject = true;
        }
    }

    return ans;
}

vector<bool> Friedman::ShafferMaxHypotheses(int k) {
    vector<bool> ans ( ((k*(k-1))/2)+1 ), tmp;
    int c = 0;

    if ( k == 0 || k == 1 ) {
        ans[0] = true;
        return ans;
    }

    for ( int i=1; i<=k; i++ ) {
        if ( i > 1 ) {
            c = (i*(i-1))/2;
        }

        tmp = ShafferMaxHypotheses(k-i);
        for ( int j=0; j<(int)tmp.size(); j++ ) {
            if ( tmp[j] ) {
                ans[j] = true;
                ans[j+c] = true;
            }
        }
    }

    return ans;
}

vector<stats> Friedman::Shaffer() {
    vector<stats> ans = calcStatistics();

    sort( ans.begin(), ans.end(), increase );

    int m = (int)ans.size(); double maxP = 0.0;
    vector<bool> maxHyp = ShafferMaxHypotheses(this->k);
    for ( int i=0; i<m; i++ ) {
        int t=1;
        for ( int j=2; j<=m-i; j++ ) {
            if ( maxHyp[j] && j>t ) {
                t = j;
            }
        }
        ans[i].p_value = max(maxP,min(1.0,(ans[i].p_value*(double)t)));
        maxP = ans[i].p_value;
        if ( ans[i].p_value <= ans[i].alpha )  {
            ans[i].reject = true;
        }
    }

    return ans;
}

vector<pair<vector<int>,vector<int>>> Friedman::DepthSearch(vector<int> c, vector<bool> mark) {
    vector<pair<vector<int>,vector<int>>> subsets, tmp;
    pair<vector<int>,vector<int>> local; int last=0;

    for ( int i=0; i<(int)mark.size(); i++ ) {
        if ( mark[i] ) {
            local.first.push_back(c[i]);
            last = i+1;
        }
    }

    for ( int i=last; i<(int)mark.size()-1; i++ ) {
        mark[i] = true;
        local.first.push_back(c[i]);
        for ( int w=0; w<(int)mark.size(); w++ ) {
            if ( !mark[w] ) {
                local.second.push_back(c[w]);
            }
        }
        tmp = DepthSearch( c, mark );
        subsets.push_back(local);
        subsets.insert(subsets.end(), tmp.begin(), tmp.end());
        mark[i] = false; local.first.pop_back(); local.second.clear();
    }

    return subsets;
}

vector<vector<pair<int,int>>> Friedman::ExhaustiveSearch(vector<int> c) {
    vector<pair<vector<int>,vector<int>>> subsets;
    vector<vector<pair<int,int>>> e, e1, e2;
    vector<pair<int,int>> e_tmp; bool isValid;

    for ( int i=0; i<(int)c.size(); i++ ) {
        for ( int j=i+1; j<(int)c.size(); j++ ) {
            e_tmp.emplace_back(make_pair(c[i],c[j]));
        }
    }

    if ( (int)e_tmp.size() == 0 ) {
        return e;
    }

    e.push_back(e_tmp);

    subsets = DepthSearch(c, std::vector<bool>((int)c.size(),false));
    for ( int i=0; i<(int)subsets.size(); i++ ) {
        e1 = ExhaustiveSearch( subsets[i].first );
        e2 = ExhaustiveSearch( subsets[i].second );

        e.insert(e.end(), e1.begin(), e1.end());
        e.insert(e.end(), e2.begin(), e2.end());

        for ( int j=0; j<(int)e1.size(); j++ ) {
            e_tmp.clear();
            for ( int w=0; w<(int)e2.size(); w++ ) {
                e_tmp.insert(e_tmp.end(), e1[j].begin(), e1[j].end());
                e_tmp.insert(e_tmp.end(), e2[w].begin(), e2[w].end());

                // Check if is a valid combination
                isValid = true;
                for ( int x=0; x<(int)e_tmp.size(); x++ ) {
                    for ( int y=x+1; y<(int)e_tmp.size(); y++ ) {
                        if (( e_tmp[x].first == e_tmp[y].first ) && ( e_tmp[x].second == e_tmp[y].second )) {
                            isValid = false;
                            break;
                        }
                    }
                }

                if ( isValid ) {
                    e.push_back(e_tmp);
                }
            }
        }

        // Remove repeated elements
        for ( int j=0; j<(int)e.size(); j++ ) {
            for ( int w=j+1; w<(int)e.size(); w++ ) {
                if ( (int)e[j].size() == (int)e[w].size() ) {
                    isValid = false;
                    for ( int z=0; z<(int)e[j].size(); z++ ) {
                        if (( e[j][z].first != e[w][z].first ) || ( e[j][z].second != e[w][z].second )) {
                            isValid = true;
                            break;
                        }
                    }
                    if ( !isValid ) {
                        e.erase(e.begin()+w);
                    }
                }
            }
        }
    }

    return e;
}

vector<stats> Friedman::BergmannHommel() {
    vector<stats> ans = calcStatistics();
    vector<vector<pair<int,int>>> e, a;
    vector<int> c(this->k); bool in;
    double I, minP; vector<double> p_value2 ((int)ans.size()) ;

    sort( ans.begin(), ans.end(), increase );

    for ( int i=0; i<(int)c.size(); i++ ) {
        c[i] = i;
    }

    for ( int i=0; i<(int)ans.size(); i++ ) {
        p_value2[i] = ans[i].p_value;
    }

    e = ExhaustiveSearch(c);
    for ( int i=0; i<(int)ans.size(); i++ ) {
        for ( int j=0; j<(int)e.size(); j++ ) {
            I = (double)e[j].size(); minP = numeric_limits<double>::max(); in = false;
            for ( int w=0; w<(int)e[j].size(); w++ ) {
                for ( int z=0; z<(int)ans.size(); z++ ) {
                    if ((( e[j][w].first == ans[z].hypothesis.first ) && ( e[j][w].second == ans[z].hypothesis.second )) ||
                        (( e[j][w].first == ans[z].hypothesis.second ) && ( e[j][w].second == ans[z].hypothesis.first ))) {
                        minP = min( minP, ans[z].p_value );
                        if ( z == i ) {
                            in = true;
                        }
                    }
                }
            }
            if (( (minP*I) > p_value2[i] ) && (in)) {
                p_value2[i] = minP*I;
            }
        }
    }

    double maxP = 0.0;
    for ( int i=0; i<(int)ans.size(); i++ ) {
        ans[i].p_value = max(maxP, min(1.0,p_value2[i]));
        maxP = ans[i].p_value;
        if ( ans[i].p_value <= ans[i].alpha )  {
            ans[i].reject = true;
        }
    }

    return ans;
}

tuple<vector<double>,vector<vector<double>>> Friedman::friedman() {
    vector<vector<double>> f2, f3;
    vector<double> aux( this->k, 0 ), totF( this->k, 0 ), f_aux( this->k, 0 );
    double cnt1, cnt2;

    for ( int i=0; i<this->n; i++ ) {
        for ( int j=0; j<this->k; j++ ) {
            aux[j] = this->values[i][j];
        }

        sort( aux.begin(), aux.end(), decrease );

        f2.push_back(f_aux);
        for ( int j=0; j<this->k; j++ ) {
            for ( int z=0; z<this->k; z++ ) {
                if ( this->values[i][j] == aux[z] ) {
                    f2[i][j] = z+1;
                    break;
                }
            }
        }
    }

    for ( int i=0; i<this->n; i++ ) {
        f3.push_back(f_aux);
        for ( int j=0; j<this->k; j++ ) {
            cnt1 = cnt2 = f2[i][j];
            for ( int z=0; z<this->k; z++ ) {
                if (( j != z ) && ( f2[i][j] == f2[i][z] )) {
                    cnt2++;
                    cnt1+=cnt2;
                }
            }

            if ( cnt2 != f2[i][j] ) {
                f3[i][j] = cnt1/((cnt2-f2[i][j])+1);
            } else {
                f3[i][j] = f2[i][j];
            }

            totF[j] += f3[i][j];
        }
    }

    double Rj = 0.0;
    for ( int i=0; i<k; i++ ) {
        Rj += (totF[i]*totF[i]);
        totF[i] /= this->n;
    }

    this->Xf = (double)((12.0/(this->n*this->k*(this->k+1))) * Rj) - (double)(3.0*this->n*((this->k+1)));
    this->Ff = ((double)(this->n-1)*this->Xf) / ((double)(this->n*(this->k-1)) - this->Xf);

    return make_tuple(totF,f3);
}

// Public methods

Friedman::Friedman(vector<vector<double>> values) {
    this->n = (int)values.size();
    this->k = (int)values[this->n-1].size();
    this->values = values;

    tuple<vector<double>,vector<vector<double>>> f = friedman();
    this->mean_positions_rank = get<0>(f);
    this->positions_rank = get<1>(f);

    set_confidence(Confidence::confidence95);
}

void Friedman::set_confidence(Confidence c) {
    switch ( c ) {
        case Confidence::confidence99:
            this->alpha = 0.01;
            break;
        case Confidence::confidence95:
            this->alpha = 0.05;
            break;
        case Confidence::confidence90:
            this->alpha = 0.10;
            break;
    }
}

vector<stats> Friedman::post_test(PostTest pt) {
    vector<stats> ans;

    switch ( pt ) {
        case PostTest::BonferroniDunn:
            ans = Bonferroni();
            break;
        case PostTest::Nemenyi:
            ans = Nemenyi();
            break;
        case PostTest::Holm:
            ans = Holm();
            break;
        case PostTest::Shaffer:
            ans = Shaffer();
            break;
        case PostTest::BergmannHommel:
            ans = BergmannHommel();
            break;
    }

    return ans;
}

bool Friedman::post_test_required() {
    fisher_f_distribution<> F (this->k-1,(this->k-1)*(this->n-1));

    return (this->Ff > quantile(F,1-this->alpha));
}

vector<vector<double>> Friedman::get_positions_rank() {
    return this->positions_rank;
}

vector<double> Friedman::get_mean_positions_rank() {
    return this->mean_positions_rank;
}

double Friedman::get_Ff() {
    return this->Ff;
}

double Friedman::get_Xf() {
    return this->Xf;
}
