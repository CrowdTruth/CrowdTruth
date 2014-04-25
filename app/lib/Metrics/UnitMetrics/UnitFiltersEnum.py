<<<<<<< HEAD
from enum import Enum


class UnitFiltersEnum(Enum):
    pass_all = "No Filter"
    mean_mag_below = "|V| < Mean"
    stddev_mag_below_mean = "|V| < STDDEV"
    stddev_MRC_below_mean = "MRC < STDDEV"
    stddev_norm_mag_below_mean = "norm |V| < STDDEV"
    stddev_norm_rel_mag_below_mean = "norm |R| < STDDEV"
    stddev_norm_rel_mag_all_below_mean = "norm-all |R| < STDDEV"
=======
from enum import Enum


class UnitFiltersEnum(Enum):
    pass_all = "No Filter"
    mean_mag_below = "|V| < Mean"
    stddev_mag_below_mean = "|V| < STDDEV"
    stddev_MRC_below_mean = "MRC < STDDEV"
    stddev_norm_mag_below_mean = "norm |V| < STDDEV"
    stddev_norm_rel_mag_below_mean = "norm |R| < STDDEV"
    stddev_norm_rel_mag_all_below_mean = "norm-all |R| < STDDEV"
>>>>>>> 373a996f28c09ac33cb9afd1d59c621e5cca2fd5
