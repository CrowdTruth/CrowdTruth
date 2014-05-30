from .. import config
import urllib
import json
import sys
from UnitMetricsEnum import *
from ..VectorMetrics import *

class Unit:


    # jobs_dict list or dict : key = jobID, value = list_of_spammers to be excluded
    # filtered = true too remove spammers
    def __init__(self, unit_id, unit_vector, no_annotators):
        self.unit_id = unit_id
        self.unit_vector = unit_vector
        self.no_annotators = no_annotators
        self.cosine_vector = {}
        self.unit_metrics = {}

    def get_unit_vector(self):
        return self.unit_vector


    def get_metrics(self, metrics_to_apply):

        if not isinstance(metrics_to_apply, list):
            metrics_to_apply = [metrics_to_apply]

        results = {}

        for metric_key in metrics_to_apply:
            if metric_key in self.unit_metrics:
                results[metric_key] = self.unit_metrics[metric_key]
                continue

            if not self.unit_vector:
                self.get_unit_vector()

            metric_value = {}

            if UnitMetricsEnum.magnitude == metric_key:
                    metric_value = VectorMetrics(self.unit_vector).get_magnitude()
            elif UnitMetricsEnum.norm_magnitude == metric_key:
                    metric_value = VectorMetrics(self.unit_vector).get_norm_magnitude()
            elif UnitMetricsEnum.norm_relation_magnitude == metric_key:
                    metric_value = VectorMetrics(self.unit_vector).get_norm_relation_magnitude()
            elif UnitMetricsEnum.norm_relation_magnitude_all == metric_key:
                    metric_value = VectorMetrics(self.unit_vector).get_norm_relation_magnitude_by_all()
            elif UnitMetricsEnum.max_relation_Cos == metric_key:
                    self.cosine_vector = VectorMetrics(self.unit_vector).get_cosine_vector()
                    metric_value = max(self.cosine_vector.values())
            elif UnitMetricsEnum.no_annotators == metric_key:
                metric_value = self.get_no_annotators()

            results[metric_key] = metric_value
            self.unit_metrics[metric_key] = metric_value

        return results

    def get_cosine_vector(self):
        if self.cosine_vector:
            return self.cosine_vector

        self.cosine_vector = VectorMetrics(self.unit_vector).get_cosine_vector()

        return self.cosine_vector

    def get_no_annotators(self):
        return self.no_annotators