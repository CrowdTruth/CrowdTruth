__author__ = 'Tatiana'
from .. import config
import urllib
import json
import sys
from AnnotationMetricsEnum import *
from ..UnitMetrics.Unit import *


class Annotation:
    # jobs_dict list or dict : key = unitID, value = list_of_jobs ? with spammers maybe
    # filtered = true too remove spammers
    def __init__(self, unit_list, annotation):
        self.annotation = annotation
        self.rel_similarity_dict = {}
        self.unit_list = unit_list
        self.annotation_metrics = {}

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
        all_units_vectors = []
        for unit in self.unit_list:
            unit_vector = unit.get_cosine_vector()[self.annotation]
            all_units_vectors.append(unit_vector)
        annotation_clarity = {}
        unit_keys = all_units_vectors[0].keys()
        for key in unit_keys:
            annotation_clarity[key] = max(vector[key] for vector in all_units_vectors)
        return annotation_clarity

    def get_rel_similarity_dict(self):
        if self.rel_similarity_dict:
            return self.rel_similarity_dict

        all_units_vectors = []
        for unit in self.unit_list:
            unit_vector = unit.get_unit_vector()[self.annotation]
            all_units_vectors.append(unit_vector)

        self.rel_similarity_dict = {}
        unit_keys = all_units_vectors[0].keys()

        for ann1 in unit_keys:
            self.rel_similarity_dict[ann1] = {}
            for ann2 in unit_keys:
                ann1_count = 0
                non_ann1_count = 0
                cond_ann2_ann1 = 0
                cond_ann2_non_ann1 = 0
                for unit in all_units_vectors:
                    if unit[ann2] > 0:
                        if unit[ann1] > 0:
                            cond_ann2_ann1 += 1
                            ann1_count += 1
                        else:
                            cond_ann2_non_ann1 += 1
                            non_ann1_count += 1
                    else:
                        if unit[ann1] > 0:
                            ann1_count += 1
                        else:
                            non_ann1_count += 1

                term1 = 0 if ann1_count == 0 else cond_ann2_ann1 / (1.0 * ann1_count)
                term2 = 0 if non_ann1_count == 0 else cond_ann2_non_ann1 / (1.0 * non_ann1_count)

                if term2 == 1:
                    self.rel_similarity_dict[ann1][ann2] = 0
                else:
                    self.rel_similarity_dict[ann1][ann2] = (term1 - term2) / (1 - term2)

        return self.rel_similarity_dict

    def get_relation_ambiguity(self):
        relation_ambiguity = {}
        similarity_dict = self.get_rel_similarity_dict()
        for ann in similarity_dict:
            max_value = -1
            for key in similarity_dict[ann]:
                if key != ann and similarity_dict[ann][key] > max_value:
                    max_value = similarity_dict[ann][key];
            relation_ambiguity[ann] = max_value

        return relation_ambiguity