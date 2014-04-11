import UnitMetrics.UnitFiltersEnum as sf
import UnitMetrics.UnitMetricsEnum as sm


class Filters:
    def __init__(self, mean_measures, stddev_measure, thresholds):
        self.mean_measure = mean_measures
        self.stddev_measure = stddev_measure
        self.thresholds = thresholds

    @staticmethod
    def pass_all(sentence):
        return False

    @staticmethod
    def below_mean(unit_measure, agg_measure):
        return unit_measure < agg_measure

    @staticmethod
    def below_diff(unit_measure, factor, first_agg_measure, second_agg_measure):
        return unit_measure < factor * (first_agg_measure - second_agg_measure)

    def is_filtered(self, sentence, filter_type):

        if sf.UnitFiltersEnum.pass_all == filter_type:
            return self.pass_all(sentence)
        elif sf.UnitFiltersEnum.mean_mag_below == filter_type:
            metric = sm.UnitMetricsEnum.magnitude
            return self.below_mean(sentence.get_metrics(metric)[metric], self.mean_measure[metric])
        elif sf.UnitFiltersEnum.stddev_mag_below_mean == filter_type:
            metric = sm.UnitMetricsEnum.magnitude
        elif sf.UnitFiltersEnum.stddev_MRC_below_mean == filter_type:
            metric = sm.UnitMetricsEnum.max_relation_Cos
        elif sf.UnitFiltersEnum.stddev_norm_mag_below_mean == filter_type:
            metric = sm.UnitMetricsEnum.norm_magnitude
        elif sf.UnitFiltersEnum.stddev_norm_rel_mag_below_mean == filter_type:
            metric = sm.UnitMetricsEnum.norm_relation_magnitude
        elif sf.UnitFiltersEnum.stddev_norm_rel_mag_all_below_mean == filter_type:
            metric = sm.UnitMetricsEnum.norm_relation_magnitude_all

        return self.below_diff(sentence.get_metrics(metric)[metric], self.thresholds[filter_type],
                               self.mean_measure[metric],
                               self.stddev_measure[metric])