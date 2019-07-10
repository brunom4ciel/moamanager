#include <cstdio>
#include <cstdlib>
#include <iostream>
#include <vector>
#include <cmath>
#include <algorithm>

using namespace std;

struct numbers {
    double diff;
    double abs_diff;
    double rank;
};

struct gen_info {
    string method1, method2;
    double r_plus, r_minus;
    bool reject;
};

bool sort_function (numbers i,numbers j) {
    return (i.abs_diff<j.abs_diff);
}

gen_info calc_wilcoxon ( string method1, string method2, vector<numbers> num ) {
    vector<int> confidence95 = {-1,-1,-1,-1,-1,-1,0,2,3,5,8,10,13,17,21,25,29,34,40,46,52,58,65,73,81,89,98,107,116,126,137,147,159,170,182,195,208,221,235,249,264,279,294,310,327,343,361,378,396,415,434,453,473,494,514,536,557,579,602,625,648,672,697,721,747,772,798,825,852,879,907,936,964,994,1023,1053,1084,1115,1147,1179,1211,1244,1277,1311,1345,1380,1415,1451,1487,1523,1560,1597,1635,1674,1712,1752,1791,1832,1872,1913,1955};
    int cnt, cnt2, cnt_rep, n;
    double pos_sum, neg_sum;
    bool rep; gen_info ans;

    n = (int)num.size();

    sort( num.begin(), num.end(), sort_function );

    cnt=0;
    while ( cnt < n ) {
        cnt_rep=1; cnt2=cnt; rep=false;
        while ( (cnt2 < n-2) && (num[cnt2].abs_diff == num[cnt2+1].abs_diff) ) {
            rep = true;
            cnt_rep++; cnt2++;
        }

        if ( !rep ) {
            num[cnt].rank = (double)(cnt+1.0);
            cnt++;
        } else {
            double draw=0.0;
            for ( int i=cnt+1; i<cnt+cnt_rep+1; i++ ){
                draw += ((double)i/(double)cnt_rep);
            }
            for ( int i=cnt; i<cnt+cnt_rep; i++ ) {
                num[i].rank = draw;
            }
            cnt += cnt_rep;
        }
    }

    pos_sum=neg_sum=0.0;
    for ( int i=0; i<n; i++ ) {
        //cout << num[i].rank << "\n";
        if ( num[i].diff > 0.0 ) {
            pos_sum += num[i].rank;
        } else if ( num[i].diff < 0.0 ) {
            neg_sum += num[i].rank;
        } else {
            pos_sum += num[i].rank;
            neg_sum += num[i].rank;
            while ( i<n-1 && num[i].rank == num[i+1].rank ) {
                i++;
            }
        }
    }

    cout << method1 << " VS " << method2 << "\n";
    cout << "R+ = " << pos_sum << "\n";
    cout << "R- = " << neg_sum << "\n";
    cout << "Para n=" << n << ", R+ ou R- deve ser menor ou igual à " << confidence95[n] << " (95% de confiança) para que ocorra diferença estatística.\n";

    ans.r_plus = pos_sum; ans.r_minus = neg_sum;
    if (( pos_sum <= confidence95[n] ) || ( neg_sum <= confidence95[n] )) {
        cout << "Existe diferença estatística entre os métodos.\n";
        ans.reject = true;
    } else {
        cout << "Não existe diferença estatística entre os métodos.\n";
        ans.reject = false;
    }

    cout << "\n\n";
    return ans;
}

int main() {
    int n, columns;
    vector<vector<double>> values;
    vector<string> methods;

    cin >> n >> columns; string tmp;
    for ( int i=0; i<columns; i++ ) {
        cin >> tmp;
        methods.push_back(tmp);
    }

    vector<numbers> num (n);
    vector<double> aux_v; double aux_d;
    for ( int i=0; i<n; i++ ) {
        aux_v.clear();
        for ( int j=0; j<columns; j++ ) {
            cin >> aux_d;
            aux_v.push_back(aux_d);
        }
        values.push_back(aux_v);
    }


    gen_info temp; vector<gen_info> m_resume;
    for ( int i=0; i<columns; i++ ) {
        for ( int j=i+1; j<columns; j++ ) {
            //num.clear();
            for ( int w=0; w<n; w++ ) {
                num[w].diff = values[w][j]-values[w][i];
                num[w].abs_diff = abs(values[w][j]-values[w][i]);
            }
            temp = calc_wilcoxon(methods[i], methods[j], num);
            temp.method1 = methods[i];
            temp.method2 = methods[j];
            m_resume.push_back(temp);
        }
    }

    cout << "Ways to reject the hypothesis:\n";
    for ( int i=0; i<methods.size(); i++ ) {
        cout << "Method " << methods[i] << " is statistically superior to: ";
        for ( int j=0; j<m_resume.size(); j++ ) {
            if ( methods[i] == m_resume[j].method1 && m_resume[j].r_minus > m_resume[j].r_plus && m_resume[j].reject ) {
                cout << m_resume[j].method2 << "\t";
            } else if ( methods[i] == m_resume[j].method2 && m_resume[j].r_minus < m_resume[j].r_plus && m_resume[j].reject ) {
                cout << m_resume[j].method1 << "\t";
            }
        }
        cout << "\n";
    }

    return 0;
}
