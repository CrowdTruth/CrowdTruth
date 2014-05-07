__author__ = 'Tatiana'
import config
import urllib
import json
import sys
from AnnotationMetricsEnum import *
from ..UnitMetrics.Unit import *


class Annotation:


    # jobs_dict list or dict : key = unitID, value = list_of_jobs ? with spammers maybe
    # filtered = true too remove spammers
    def __init__(self, annotation_name, unit_dict, filtered):
        self.annotation_name = annotation_name
        self.unit_dict = unit_dict
        self.filtered = filtered
        self.annotation_vector = {}
        self.unit_list = []
        self.annotation_metrics = {}
        for unit_id in unit_dict:
            unit = Unit(unit_id, unit_dict[unit_id], filtered)
            self.unit_list.append(unit)

    #it should be property?
    def get_annotation_vector(self):
        if self.annotation_vector:
            return self.annotation_vector

        #compute the value of all responses
        all_units_vectors = []
        for unit in self.unit_list:
            unit_vector = unit.get_unit_vector()
            all_units_vectors.append(unit_vector)

        unit_keys = all_units_vectors[0].keys()
        for key in unit_keys:
            agg_value = sum(vector[key] for vector in all_units_vectors)
            self.annotation_vector[key] = agg_value

    def get_metrics(self, metrics_to_apply):
        if not isinstance(metrics_to_apply, list):
            metrics_to_apply = [metrics_to_apply]

        results = {}

        for metric_key in metrics_to_apply:
            if metric_key in self.annotation_metrics:
                results[metric_key] = self.annotation_metrics[metric_key]
                continue

            metric_value = None

            if AnnotationMetricsEnum.annot_clarity == metric_key:
                metric_value = self.get_annotation_clarity()
            elif AnnotationMetricsEnum.annot_ambiguity == metric_key:
                metric_value = self.get_relation_ambiguity()

            results[metric_key] = metric_value
            self.annotation_metrics[metric_key] = metric_value

        return results

    def get_annotation_clarity(self):
        max_value = -1
        for unit in self.unit_list:
            unit_annotation_value = unit.get_unit_vector()[self.annotation_name]
            if unit_annotation_value > max:
                max_value = unit_annotation_value

        return max_value

    def get_rel_similarity_dict(self):
        if not self.get_annotation_vector():
            self.get_annotation_vector()

        similarity_dict = {}
        for key in self.annotation_vector:
            similarity_dict[key] = (self.annotation_vector[key] + self.annotation_vector[self.annotation_name]) / \
                                   (1.0 * self.annotation_vector[key])
        return similarity_dict

    def get_relation_ambiguity(self):
        similarity_dict = self.get_rel_similarity_dict()
        return max(similarity_dict.values())