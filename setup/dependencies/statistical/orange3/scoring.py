"""
Methods for scoring prediction results (CA, AUC, ...).

Examples
--------
>>> import Orange
>>> data = Orange.data.Table('iris')
>>> learner = Orange.classification.LogisticRegressionLearner()
>>> results = Orange.evaluation.TestOnTrainingData(data, [learner])


CRITICAL_VALUES = [
# p   0.01   0.05   0.10  Models
    [2.576, 1.960, 1.645], # 2
    [2.913, 2.344, 2.052], # 3
    [3.113, 2.569, 2.291], # 4
    [3.255, 2.728, 2.460], # 5
    [3.364, 2.850, 2.589], # 6
    [3.452, 2.948, 2.693], # 7
    [3.526, 3.031, 2.780], # 8
    [3.590, 3.102, 2.855], # 9
    [3.646, 3.164, 2.920], # 10
    [3.696, 3.219, 2.978], # 11
    [3.741, 3.268, 3.030], # 12
    [3.781, 3.313, 3.077], # 13
    [3.818, 3.354, 3.120], # 14
    [3.853, 3.391, 3.159], # 15
    [3.884, 3.426, 3.196], # 16
    [3.914, 3.458, 3.230], # 17
    [3.941, 3.489, 3.261], # 18
    [3.967, 3.517, 3.291], # 19
    [3.992, 3.544, 3.319], # 20
    [4.015, 3.569, 3.346], # 21
    [4.037, 3.593, 3.371], # 22
    [4.057, 3.616, 3.394], # 23
    [4.077, 3.637, 3.417], # 24
    [4.096, 3.658, 3.439], # 25
    [4.114, 3.678, 3.459], # 26
    [4.132, 3.696, 3.479], # 27
    [4.148, 3.714, 3.498], # 28
    [4.164, 3.732, 3.516], # 29
    [4.179, 3.749, 3.533], # 30
    [4.194, 3.765, 3.550], # 31
    [4.208, 3.780, 3.567], # 32
    [4.222, 3.795, 3.582], # 33
    [4.236, 3.810, 3.597], # 34
    [4.249, 3.824, 3.612], # 35
    [4.261, 3.837, 3.626], # 36
    [4.273, 3.850, 3.640], # 37
    [4.285, 3.863, 3.653], # 38
    [4.296, 3.876, 3.666], # 39
    [4.307, 3.888, 3.679], # 40
    [4.318, 3.899, 3.691], # 41
    [4.329, 3.911, 3.703], # 42
    [4.339, 3.922, 3.714], # 43
    [4.349, 3.933, 3.726], # 44
    [4.359, 3.943, 3.737], # 45
    [4.368, 3.954, 3.747], # 46
    [4.378, 3.964, 3.758], # 47
    [4.387, 3.973, 3.768], # 48
    [4.395, 3.983, 3.778], # 49
    [4.404, 3.992, 3.788], # 50
]


"""



import math

import numpy as np
import sklearn.metrics as skl_metrics

from Orange.data import DiscreteVariable, ContinuousVariable
from Orange.misc.wrapper_meta import WrapperMeta

__all__ = ["CA", "Precision", "Recall", "F1", "PrecisionRecallFSupport", "AUC",
           "MSE", "RMSE", "MAE", "R2", "compute_CD", "graph_ranks", "LogLoss"]


CRITICAL_VALUES = [
# p   0.01   0.05   0.10  Models
    [2.576, 1.960, 1.645], # 2
    [2.913, 2.344, 2.052], # 3
    [3.113, 2.569, 2.291], # 4
    [3.255, 2.728, 2.460], # 5
    [3.364, 2.850, 2.589], # 6
    [3.452, 2.948, 2.693], # 7
    [3.526, 3.031, 2.780], # 8
    [3.590, 3.102, 2.855], # 9
    [3.646, 3.164, 2.920], # 10
    [3.696, 3.219, 2.978], # 11
    [3.741, 3.268, 3.030], # 12
    [3.781, 3.313, 3.077], # 13
    [3.818, 3.354, 3.120], # 14
    [3.853, 3.391, 3.159], # 15
    [3.884, 3.426, 3.196], # 16
    [3.914, 3.458, 3.230], # 17
    [3.941, 3.489, 3.261], # 18
    [3.967, 3.517, 3.291], # 19
    [3.992, 3.544, 3.319], # 20
    [4.015, 3.569, 3.346], # 21
    [4.037, 3.593, 3.371], # 22
    [4.057, 3.616, 3.394], # 23
    [4.077, 3.637, 3.417], # 24
    [4.096, 3.658, 3.439], # 25
    [4.114, 3.678, 3.459], # 26
    [4.132, 3.696, 3.479], # 27
    [4.148, 3.714, 3.498], # 28
    [4.164, 3.732, 3.516], # 29
    [4.179, 3.749, 3.533], # 30
    [4.194, 3.765, 3.550], # 31
    [4.208, 3.780, 3.567], # 32
    [4.222, 3.795, 3.582], # 33
    [4.236, 3.810, 3.597], # 34
    [4.249, 3.824, 3.612], # 35
    [4.261, 3.837, 3.626], # 36
    [4.273, 3.850, 3.640], # 37
    [4.285, 3.863, 3.653], # 38
    [4.296, 3.876, 3.666], # 39
    [4.307, 3.888, 3.679], # 40
    [4.318, 3.899, 3.691], # 41
    [4.329, 3.911, 3.703], # 42
    [4.339, 3.922, 3.714], # 43
    [4.349, 3.933, 3.726], # 44
    [4.359, 3.943, 3.737], # 45
    [4.368, 3.954, 3.747], # 46
    [4.378, 3.964, 3.758], # 47
    [4.387, 3.973, 3.768], # 48
    [4.395, 3.983, 3.778], # 49
    [4.404, 3.992, 3.788], # 50
]


class ScoreMetaType(WrapperMeta):
    """
    Maintain a registry of non-abstract subclasses and assign the default
    value of `name`.

    The existing meta class Registry cannot be used since a meta class cannot
    have multiple inherited __new__ methods."""
    def __new__(mcs, name, bases, dict_, **kwargs):
        cls = WrapperMeta.__new__(mcs, name, bases, dict_)
        # Essentially `if cls is not Score`, except that Score may not exist yet
        if hasattr(cls, "registry"):
            if not kwargs.get("abstract"):
                # Don't use inherited names, look into dict_
                cls.name = dict_.get("name", name)
                cls.registry[name] = cls
        else:
            cls.registry = {}
        return cls

    def __init__(cls, *args, **_):
        WrapperMeta.__init__(cls, *args)


class Score(metaclass=ScoreMetaType):
    """
    ${sklpar}
    Parameters
    ----------
    results : Orange.evaluation.Results
        Stored predictions and actual data in model testing.
    """
    __wraps__ = None

    separate_folds = False
    is_scalar = True
    is_binary = False  #: If true, compute_score accepts `target` and `average`
    #: If the class doesn't explicitly contain `abstract=True`, it is not
    #: abstract; essentially, this attribute is not inherited
    abstract = True
    class_types = ()
    name = None
    long_name = None  #: A short user-readable name (e.g. a few words)

    def __new__(cls, results=None, **kwargs):
        self = super().__new__(cls)
        if results is not None:
            self.__init__()
            return self(results, **kwargs)
        else:
            return self

    def __call__(self, results, **kwargs):
        if self.separate_folds and results.score_by_folds and results.folds:
            scores = self.scores_by_folds(results, **kwargs)
            return self.average(scores)

        return self.compute_score(results, **kwargs)

    def average(self, scores):
        if self.is_scalar:
            return np.mean(scores, axis=0)
        return NotImplementedError

    def scores_by_folds(self, results, **kwargs):
        nfolds = len(results.folds)
        nmodels = len(results.predicted)
        if self.is_scalar:
            scores = np.empty((nfolds, nmodels), dtype=np.float64)
        else:
            scores = [None] * nfolds
        for fold in range(nfolds):
            fold_results = results.get_fold(fold)
            scores[fold] = self.compute_score(fold_results, **kwargs)
        return scores

    def compute_score(self, results):
        wraps = type(self).__wraps__  # self.__wraps__ is invisible
        if wraps:
            return self.from_predicted(results, wraps)
        else:
            return NotImplementedError

    @staticmethod
    def from_predicted(results, score_function, **kwargs):
        return np.fromiter(
            (score_function(results.actual, predicted, **kwargs)
             for predicted in results.predicted),
            dtype=np.float64, count=len(results.predicted))


class ClassificationScore(Score, abstract=True):
    class_types = (DiscreteVariable, )


class RegressionScore(Score, abstract=True):
    class_types = (ContinuousVariable, )


# pylint: disable=invalid-name
class CA(ClassificationScore):
    __wraps__ = skl_metrics.accuracy_score
    long_name = "Classification accuracy"


class PrecisionRecallFSupport(ClassificationScore):
    __wraps__ = skl_metrics.precision_recall_fscore_support
    is_scalar = False


class TargetScore(ClassificationScore):
    """
    Base class for scorers that need a target value (a "positive" class).

    Parameters
    ----------
    results : Orange.evaluation.Results
        Stored predictions and actual data in model testing.

    target : int, optional (default=None)
        Target class value.
        When None:
          - if averaging is specified, use all classes and average results
          - if average is 'binary' and class variable has exactly 2 values,
            use the value '1' as the positive class

    average: str, method for averaging (default='binary')
        Default requires a binary class or target to be set.
        Options: 'weighted', 'macro', 'micro', None

    """
    is_binary = True
    abstract = True
    __wraps__ = None  # Subclasses should set the scoring function

    def compute_score(self, results, target=None, average='binary'):
        if average == 'binary':
            if target is None:
                if len(results.domain.class_var.values) > 2:
                    raise ValueError(
                        "Multiclass data: specify target class or select "
                        "averaging ('weighted', 'macro', 'micro')")
                target = 1  # Default: use 1 as "positive" class
            average = None
        labels = None if target is None else [target]
        return self.from_predicted(
            results, type(self).__wraps__, labels=labels, average=average)


class Precision(TargetScore):
    __wraps__ = skl_metrics.precision_score


class Recall(TargetScore):
    __wraps__ = skl_metrics.recall_score


class F1(TargetScore):
    __wraps__ = skl_metrics.f1_score


class AUC(ClassificationScore):
    """
    ${sklpar}

    Parameters
    ----------
    results : Orange.evaluation.Results
        Stored predictions and actual data in model testing.

    target : int, optional (default=None)
        Value of class to report.
    """
    __wraps__ = skl_metrics.roc_auc_score
    separate_folds = True
    is_binary = True
    long_name = "Area under ROC curve"

    def calculate_weights(self, results):
        classes = np.unique(results.actual)
        class_cases = [sum(results.actual == class_)
                       for class_ in classes]
        N = results.actual.shape[0]
        weights = np.array([c * (N - c) for c in class_cases])
        wsum = np.sum(weights)
        if wsum == 0:
            raise ValueError("Class variable has less than two values")
        else:
            return weights / wsum

    def single_class_auc(self, results, target):
        y = np.array(results.actual == target, dtype=int)
        return np.fromiter(
            (skl_metrics.roc_auc_score(y, probabilities[:, int(target)])
             for probabilities in results.probabilities),
            dtype=np.float64, count=len(results.predicted))


    def multi_class_auc(self, results):
        classes = np.unique(results.actual)
        weights = self.calculate_weights(results)
        auc_array = np.array([self.single_class_auc(results, class_)
                              for class_ in classes])
        return np.sum(auc_array.T * weights, axis=1)

    def compute_score(self, results, target=None, average=None):
        domain = results.domain
        n_classes = len(domain.class_var.values)

        if n_classes < 2:
            raise ValueError("Class variable has less than two values")
        elif n_classes == 2:
            return self.single_class_auc(results, 1)
        else:
            if target is None:
                return self.multi_class_auc(results)
            else:
                return self.single_class_auc(results, target)


class LogLoss(ClassificationScore):
    """
    ${sklpar}

    Parameters
    ----------
    results : Orange.evaluation.Results
        Stored predictions and actual data in model testing.

    eps : float
        Log loss is undefined for p=0 or p=1, so probabilities are
        clipped to max(eps, min(1 - eps, p)).

    normalize : bool, optional (default=True)
        If true, return the mean loss per sample.
        Otherwise, return the sum of the per-sample losses.

    sample_weight : array-like of shape = [n_samples], optional
        Sample weights.

    Examples
    --------
    >>> Orange.evaluation.LogLoss(results)
    array([ 0.3...])

    """
    __wraps__ = skl_metrics.log_loss

    def compute_score(self, results, eps=1e-15, normalize=True,
                      sample_weight=None):
        return np.fromiter(
            (skl_metrics.log_loss(results.actual,
                                  probabilities,
                                  eps=eps,
                                  normalize=normalize,
                                  sample_weight=sample_weight)
             for probabilities in results.probabilities),
            dtype=np.float64, count=len(results.probabilities))


# Regression scores

class MSE(RegressionScore):
    __wraps__ = skl_metrics.mean_squared_error
    long_name = "Mean square error"


class RMSE(RegressionScore):
    long_name = "Root mean square error"

    def compute_score(self, results):
        return np.sqrt(MSE(results))


class MAE(RegressionScore):
    __wraps__ = skl_metrics.mean_absolute_error
    long_name = "Mean absolute error"


# pylint: disable=invalid-name
class R2(RegressionScore):
    __wraps__ = skl_metrics.r2_score
    long_name = "Coefficient of determination"


class CVRMSE(RegressionScore):
    long_name = "Coefficient of variation of the RMSE"

    def compute_score(self, results):
        mean = np.nanmean(results.actual)
        if mean < 1e-10:
            raise ValueError("Mean value is too small")
        return RMSE(results) / mean * 100





def critical_value(pvalue, models):
    """
    Returns the critical value for the two-tailed Nemenyi test for a given
    p-value and number of models being compared.
    """
    if pvalue == 0.01:
        col_idx = 0
    elif pvalue == 0.05:
        col_idx = 1
    elif pvalue == 0.10:
        col_idx = 2
    else:
        raise ValueError('p-value must be one of 0.01, 0.05, or 0.10')

    if not (2 <= models and models <= 50):
        raise ValueError('number of models must be in range [2, 50]')
    else:
        row_idx = models - 2

    return CRITICAL_VALUES[row_idx][col_idx]

def critical_difference(pvalue, models, datasets):
    """
    Returns the critical difference for the two-tailed Nemenyi test for a
    given p-value, number of models being compared, and number of datasets over
    which model ranks are averaged.
    """
    cv = critical_value(pvalue, models)
    return cv*math.sqrt((models*(models + 1))/(6.0*datasets))


#def critical_differenceNxN(pvalue, k, n):
#    """
#    Returns the critical difference for the two-tailed Nemenyi test for a
#    given p-value, number of models being compared, and number of datasets over
#    which model ranks are averaged.
#    """
#    cv = critical_value(pvalue, k)
#    
#    return ((0.5*cv)/(k*(k-1)/2)* (math.sqrt(n*k*(k + 1)/6.0)) )


# CD scores and plot

def compute_CD(avranks, n, alpha="0.05", test="nemenyi"):
    """
    Returns critical difference for Nemenyi or Bonferroni-Dunn test
    according to given alpha (either alpha="0.05" or alpha="0.1") for average
    ranks and number of tested datasets N. Test can be either "nemenyi" for
    for Nemenyi two tailed test or "bonferroni-dunn" for Bonferroni-Dunn test.
    """
    k = len(avranks)
    
    d = {("nemenyi", "0.05"): [0,0, 1.960,2.344,2.569,2.728,2.850,2.948,3.031,3.102,3.164,3.219,3.268,3.313,3.354,3.391,3.426,3.458,3.489,3.517,3.544,3.569,3.593,3.616,3.637,3.658,3.678,3.696,3.714,3.732,3.749,3.765,3.780,3.795,3.810,3.824,3.837,3.850,3.863,3.876,3.888,3.899,3.911,3.922,3.933,3.943,3.954,3.964,3.973,3.983,3.992],
         ("nemenyi", "0.1"): [0, 0, 1.644854, 2.052293, 2.291341, 2.459516,
                              2.588521, 2.692732, 2.779884, 2.854606, 2.919889,
                              2.977768, 3.029694, 3.076733, 3.119693, 3.159199,
                              3.195743, 3.229723, 3.261461, 3.291224, 3.319233],
         ("bonferroni-dunn", "0.05"): [0, 0, 1.960, 2.241, 2.394, 2.498, 2.576,
                                       2.638, 2.690, 2.724, 2.773, 2.807, 2.838, 2.865, 2.891, 2.914, 2.935, 2.955, 2.974, 2.991, 3.008, 3.023, 3.038, 3.052, 3.065, 3.078, 3.090, 3.102, 3.113, 3.124, 3.134, 3.144],
         ("bonferroni-dunn", "0.1"): [0, 0, 1.645, 1.960, 2.128, 2.241, 2.326,
                                      2.394, 2.450, 2.498, 2.539]}
    
    #cv = critical_value(float(alpha), k) 
    #cd = critical_differenceNxN(float(alpha), k, n)
                                     
    q = d[(test, alpha)]
    cd = q[k] * (k * (k + 1) / (6.0 * n)) ** 0.5
    """
    https://gist.github.com/garydoranjr/5016455
    """
	
    return cd


def graph_ranks(avranks, names, cd=None, cdmethod=None, lowv=None, highv=None,
                width=6, textspace=1, reverse=False, filename=None, alpha=0.05, nameref='', friedmanrank=0, n=None, **kwargs):
    """
    Draws a CD graph, which is used to display  the differences in methods'
    performance. See Janez Demsar, Statistical Comparisons of Classifiers over
    Multiple Data Sets, 7(Jan):1--30, 2006.

    Needs matplotlib to work.

    The image is ploted on `plt` imported using
    `import matplotlib.pyplot as plt`.

    Args:
        avranks (list of float): average ranks of methods.
        names (list of str): names of methods.
        cd (float): Critical difference used for statistically significance of
            difference between methods.
        cdmethod (int, optional): the method that is compared with other methods
            If omitted, show pairwise comparison of methods
        lowv (int, optional): the lowest shown rank
        highv (int, optional): the highest shown rank
        width (int, optional): default width in inches (default: 6)
        textspace (int, optional): space on figure sides (in inches) for the
            method names (default: 1)
        reverse (bool, optional):  if set to `True`, the lowest rank is on the
            right (default: `False`)
        filename (str, optional): output file name (with extension). If not
            given, the function does not write a file.
    """
    try:
        import matplotlib
        import matplotlib.pyplot as plt
        from matplotlib.backends.backend_agg import FigureCanvasAgg
    except ImportError:
        raise ImportError("Function graph_ranks requires matplotlib.")

    width = 4
    width = float(width)
    #textspace = 0.00001
    textspace = float(textspace)

    
    #if cdmethod is None:
    #    methodName = None
    #else:
    #    methodName = names[cdmethod]

    def nth(l, n):
        """
        Returns only nth elemnt in a list.
        """
        n = lloc(l, n)
        return [a[n] for a in l]

    def lloc(l, n):
        """
        List location in list of list structure.
        Enable the use of negative locations:
        -1 is the last element, -2 second last...
        """
        if n < 0:
            return len(l[0]) + n
        else:
            return n

    def mxrange(lr):
        """
        Multiple xranges. Can be used to traverse matrices.
        This function is very slow due to unknown number of
        parameters.

        >>> mxrange([3,5])
        [(0, 0), (0, 1), (0, 2), (1, 0), (1, 1), (1, 2)]

        >>> mxrange([[3,5,1],[9,0,-3]])
        [(3, 9), (3, 6), (3, 3), (4, 9), (4, 6), (4, 3)]

        """
        if not len(lr):
            yield ()
        else:
            # it can work with single numbers
            index = lr[0]
            if isinstance(index, int):
                index = [index]
            for a in range(*index):
                for b in mxrange(lr[1:]):
                    yield tuple([a] + list(b))

    def print_figure(fig, *args, **kwargs):
        canvas = FigureCanvasAgg(fig)
        canvas.print_figure(*args, **kwargs)

    sums = avranks

    tempsort = sorted([(a, i) for i, a in enumerate(sums)], reverse=reverse)
    ssums = nth(tempsort, 0)
    sortidx = nth(tempsort, 1)
    nnames = [names[x] for x in sortidx]

    if lowv is None:
        lowv = min(1, int(math.floor(min(ssums))))
    if highv is None:
        highv = max(len(avranks), int(math.ceil(max(ssums))))

    cline = 0.4

    k = len(sums)

    lines = None

    linesblank = 0
    scalewidth = width - 2 * textspace

    def rankpos(rank):
        if not reverse:
            a = rank - lowv
        else:
            a = highv - rank
        return textspace + scalewidth / (highv - lowv) * a

    distanceh = 0#0.25

    if cd and cdmethod is None:
        # get pairs of non significant methods

        def get_lines(sums, hsd):
            # get all pairs
            lsums = len(sums)
            allpairs = [(i, j) for i, j in mxrange([[lsums], [lsums]]) if j > i]
            # remove not significant
            notSig = [(i, j) for i, j in allpairs
                      if abs(sums[i] - sums[j]) <= hsd]
            # keep only longest

            def no_longer(ij_tuple, notSig):
                i, j = ij_tuple
                for i1, j1 in notSig:
                    if (i1 <= i and j1 > j) or (i1 < i and j1 >= j):
                        return False
                return True

            longest = [(i, j) for i, j in notSig if no_longer((i, j), notSig)]

            return longest

        lines = get_lines(ssums, cd)
        #linesblank = 0.2 + 0.2 + (len(lines) - 1) * 0.1
        linesblank = 0.1 + (len(lines) - 1) * 0.05
        # add scale
        #distanceh = 0.25
        cline += distanceh


    cline = 0.4
    # calculate height needed height of an image
    #minnotsignificant = max(2 * 0.2, linesblank) # original 17/01/2019
    minnotsignificant = max(0.08, linesblank) # old max(0.05, linesblank)
    
    numeropar_vspace_ajust = 0
    if(k%2==0):
        numeropar_vspace_ajust = 0.18
    else:
        numeropar_vspace_ajust = 0.2
  
    if friedmanrank == 1:
        height = cline + ((k + 1) / 2) * numeropar_vspace_ajust #+ (minnotsignificant*0.4) + (cline*0.4)
    else:
        height = cline + ((k + 1) / 2) * 0.15 #+ (minnotsignificant*0.4) + (cline*0.4)
	
    fig = plt.figure(figsize=(width, height))
    
    #fig.suptitle("Nemenyi post-hoc test with α="+str(alpha), fontsize=10)		
    fig.set_facecolor('white')
    ax = fig.add_axes([0, 0, 1, 1])  # reverse y axis
    ax.set_axis_off()

    hf = 1. / height  # height factor
    wf = 1. / width

    def hfl(l):
        return [a * hf for a in l]

    def wfl(l):
        return [a * wf for a in l]


    # Upper left corner is (0,0).
    ax.plot([0, 1], [0, 1], c="w")
    ax.set_xlim(0, 1)
    ax.set_ylim(1, 0)

    def line(l, color='k', alpha2=1, **kwargs):
        """
        Input is a list of pairs of points.
        """
        ax.plot(wfl(nth(l, 0)), hfl(nth(l, 1)), color=color, alpha=alpha2, **kwargs)

    def text(x, y, s, fontsize2='small', color2='k', *args, **kwargs):
        ax.text(wf * x, hf * y, s, fontsize=fontsize2, color=color2, *args, **kwargs)

    def text2(x, y, s, fontsize2='small', color2='k', *args, **kwargs):
        ax.text(wf * x, hf * y, s, fontsize=fontsize2, color=color2, style='italic',bbox=dict(facecolor='gray',alpha=0.2, edgecolor='none',pad=0,zorder=0), *args, **kwargs)

    #line([(0, cline), (width - textspace, cline)], linewidth=1.7)
    line([(textspace, cline), (width - textspace, cline)], linewidth=1.7)

    
    bigtick = 0.1
    smalltick = 0.05

    if cdmethod is None:
        barline = 0.35
        testee=textspace+cline+(textspace)
    else:
        barline = 0.35
        testee=textspace+cline+(textspace)

    tick = None
    for a in list(np.arange(lowv, highv, 0.5)) + [highv]:
        tick = smalltick
        if a == int(a):
            tick = bigtick
        line([(rankpos(a), barline - tick / 2),
              (rankpos(a), cline)],
             linewidth=0.7)
	
    for a in range(lowv, highv + 1):
        text(rankpos(a), cline - tick / 2 - 0.05, str(a),
             ha="center", va="bottom")

    k = len(ssums)
 
	#rankpos(a), 0.6 - tick / 2

    """	
    escreve o rank de friedman - inicio
    """
	 
	#rankpos(math.ceil(k/2)-1) - textspace #
	#(width - textspace) - textspace # 
    #scalewidth2 = width - 2 * textspace
    #textspace - rankpos(math.ceil(k/2)-1) #
    
    minnotsignificant = 0.14

    #friedmanrank=1
    if friedmanrank == 1:
        tick2=cline*(len(avranks)) + (cline*0.5)
        #line([(textspace, cline), (width - textspace, cline)], linewidth=3.7)
        index=0
        avranks2 = sorted(avranks)
        #textspaceh = (textspace/2) + rankpos(math.ceil(k/2)-1) # textspace #+ scalewidth2 / (highv - lowv)  #textspace - (textspace*0.53) #textspace - (scalewidth) #+ rankpos(ssums[1]) #- 0.05#rankpos(a)
        textspaceh = testee#textspace+cline
	#chei = cline + minnotsignificant + ((k - len(avranks) ))
        #chei = cline + minnotsignificant + (math.ceil(k / 2)) * (1 / math.ceil(k / 2)) #(k*0.1)
        chei = cline + minnotsignificant + math.ceil(k/2) * 0.135
        #for i in range(math.ceil(k / 2), k):        
            #chei = cline + minnotsignificant + ((k - 1) * 0.13) #+ (cline/2)
        captionRank = ''    
        for a in range(lowv, highv + 1):        
            #text(textspaceh, chei, str('{0:.2f}'.format(avranks2[index])), ha="center", va="bottom", fontsize2='xx-small')
            #text(textspaceh, tick2 * 0.51, str(nnames[index]), ha="center", va="bottom", fontsize2='xx-small')
            captionRank = captionRank + str('{0:.2f}'.format(avranks2[index])) + "  "
            #textspaceh=textspaceh+(0.27) #(1/len(avranks)))
            index = index + 1
        text(textspaceh, chei, captionRank, ha="center", va="bottom", fontsize2='xx-small')
             
    #line([(textspace, cline), (width - textspace, cline)], linewidth=3.7)
    #index=0
    #avranks2 = sorted(avranks)
    #textspaceh=textspace - (textspace*0.5)
    #for a in range(lowv, highv + 1):
    #    text(rankpos(a), tick2 * 0.675, str(nnames[index]), ha="center", va="bottom", fontsize2='xx-small')
    #    index = index + 1
    
    
    """	
    escreve o rank de friedman - fim
    """	
	
	
	            
    """  
    chei = cline + minnotsignificant + 1 * 0.13
    text(textspace - 0.05, chei, nnames[1], ha="right", va="center", color2='k', fontsize2='small') 
    """		
    
    for i in range(math.ceil(k / 2)):
        chei = cline + minnotsignificant + i * 0.13 # cline + minnotsignificant + i * 0.13
        line([(rankpos(ssums[i]), cline),
              (rankpos(ssums[i]), chei),
              (textspace - 0.0, chei)],color='k',
             linewidth=0.7)
        color3='k'
        if cdmethod is None:
            color3='k'
        else:
            if cdmethod == i:
                color3='r'

        if color3 == 'r':
            color3 = 'k'
            text(textspace - 0.05, chei, '*'+nnames[i], 
			ha="right", va="center", color2=color3, fontsize2='small') #fontsize2='x-small'
        else:
            text(textspace - 0.05, chei, nnames[i], 
			ha="right", va="center", color2=color3, fontsize2='small') #fontsize2='x-small'
                    
 
        
    #"""	
    tick2=0
    for i in range(math.ceil(k / 2), k):        
        chei = cline + minnotsignificant + ((k - i - 1) * 0.13) #cline + minnotsignificant + (k - i - 1) * 0.13
        tick2 = tick2 + cline
        line([(rankpos(ssums[i]), cline),
              (rankpos(ssums[i]), chei),
              (textspace + scalewidth + 0.0, chei)], color='k',
             linewidth=0.7)
        color3='k'
        if cdmethod is None:
            color3='k'
        else:
            if cdmethod == i:
                color3='r'

        if color3 == 'r':
            color3 = 'k'
            text(textspace + scalewidth + 0.05, chei, nnames[i]+'*', 
			ha="left", va="center", color2=color3, fontsize2='small') #fontsize2='x-small'
        else:
            text(textspace + scalewidth + 0.05, chei, nnames[i], 
			ha="left", va="center", color2=color3, fontsize2='small') #fontsize2='x-small'

        #text(textspace + scalewidth + 0.05, chei, nnames[i],# +" "+str(cdmethod)+"="+str(i),
        #     ha="left", va="center", color2=color3, fontsize2='small')

    #"""	
	                 
    if cd and cdmethod is None:
        # upper scale
        if not reverse:
            begin, end = rankpos(lowv), rankpos(lowv + cd)
        else:
            begin, end = rankpos(highv), rankpos(highv - cd)

	#linha do CD que aparece no grafico
        #line([(begin, distanceh + 0.07), (end, distanceh + 0.07)], color='r', linewidth=1.0)
        #line([(begin, (distanceh + bigtick / 2) + 0.07),
        #      (begin, (distanceh - bigtick / 2) + 0.07)],color='r',
        #     linewidth=1.0)
        #line([(end, (distanceh + bigtick / 2) + 0.07),
        #      (end, (distanceh - bigtick / 2) + 0.07)],color='r',
        #     linewidth=1.0)
             
       # scd = float("{0:.4f}".format(cd))
             
        #if cd>4:
        #    text((begin + end) / 2, distanceh, "CD="+str(float("{0:.4f}".format(cd))),
        #     ha="center", va="bottom")
        #else:
        #text( (textspace/2) + (begin + end) / 2 , distanceh, "CD="+str(float("{0:.4f}".format(cd))) + ", α="+str(alpha)+ ", n="+str(n)+ ", g="+str(k), ha="center", va="bottom")
        text( testee , distanceh+0.15, "CD="+str(float("{0:.4f}".format(cd))) + ", α="+str(alpha)+ ", N="+str(n)+ ", k="+str(k),
             ha="center", va="bottom")
        
                
        #if (nameref is None) or (str(nameref).strip()==""):	
        #text(((begin+end) + (scalewidth)*0.2), distanceh, " Nemenyi, α="+str(alpha),ha="right", va="bottom")  	
        #else:	
            #text(((begin+end))+2.4, distanceh - 0.07, "Nemenyi, α="+str(alpha)+str(" "+nameref),ha="center", va="bottom")     
				
        #cline2=0.3
        # no-significance lines
        def draw_lines(lines, side=0.05, height=0.03):
            start = cline + 0.05
            for l, r in lines:
                line([(rankpos(ssums[l]) - side, start),
                      (rankpos(ssums[r]) + side, start)],color='r',
                     linewidth=1.0, alpha2=0.9)
                start += height

        draw_lines(lines)

        #text(textspace + scalewidth, 2.5, ' ', ha="right", va="center", color2='b', fontsize2='small')             
    elif cd:
        begin = rankpos(avranks[cdmethod] )
        #end = rankpos(avranks[cdmethod])  + (cd/2)
        end = rankpos(avranks[cdmethod] + cd) #textspace + scalewidth / (highv - lowv) * (avranks[cdmethod] - lowv) + cd
#[(textspace, cline), (width - textspace, cline)]

        text( testee , distanceh+0.15, "CD="+str(float("{0:.4f}".format(cd))) + ", α="+str(alpha)+ ", N="+str(n)+ ", k="+str(k), ha="center", va="bottom")
        #text( (textspace + cline)+(textspace*0.55) , distanceh-0.1, "CD="+str(float("{0:.4f}".format(cd))) + ", α="+str(alpha)+ ", n="+str(n)+ ", g="+str(k), ha="center", va="bottom")

        line([(begin, cline), (end, cline)],color='r',
             linewidth=2.5)
        line([(begin, cline + bigtick / 2),
              (begin, cline - bigtick / 2)],color='r',
             linewidth=2.5)
        line([(end, cline + bigtick / 2),
              (end, cline - bigtick / 2)],color='r',
             linewidth=2.5)

        
             
    if filename:
        print_figure(fig, filename, **kwargs)
