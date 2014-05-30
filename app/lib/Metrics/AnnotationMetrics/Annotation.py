__author__ = 'Tatiana'
from .. import config
import urllib
import json
import sys
from math import log
from math import e
from AnnotationMetricsEnum import *
from ..UnitMetrics.Unit import *


class Annotation:
    # jobs_dict list or dict : key = unitID, value = list_of_jobs ? with spammers maybe
    # filtered = true too remove spammers
    def __init__(self, units):
        self.rel_similarity_dict = {}
        self.cond_prob_dict = {}
        self.top_ann_cond_prob_dict = {}
        self.cond_prob_minus_rel_prob_dict = {}
        self.mutual_info_dict = {}

        self.units = units
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
            elif AnnotationMetricsEnum.annot_freq == metric_key:
                metric_value = self.get_annotation_prob(metric_key)
            elif AnnotationMetricsEnum.annot_top_prob == metric_key:
                metric_value = self.get_annotation_prob(metric_key)
            elif AnnotationMetricsEnum.annot_prob == metric_key:
                metric_value = self.get_annotation_prob(metric_key)
            elif AnnotationMetricsEnum.cond_prob == metric_key:
                metric_value = self.get_cond_annotation_prob(metric_key)
            elif AnnotationMetricsEnum.top_ann_cond_prob == metric_key:
                metric_value = self.get_cond_annotation_prob(metric_key)
            elif AnnotationMetricsEnum.cond_prob_minus_rel_prob == metric_key:
                metric_value = self.get_cond_prob_minus_rel_prob()
            elif AnnotationMetricsEnum.mutual_info == metric_key:
                metric_value = self.get_mutual_info()

            results[metric_key] = metric_value
            self.annotation_metrics[metric_key] = metric_value

        return results

    def get_cond_prob_minus_rel_prob_dict(self):
        if self.mutual_info_dict:
            return self.mutual_info_dict

        self.get_mutual_info()
        return self.mutual_info_dict

    def get_mutual_info(self):
        mutual_info = {}
        if AnnotationMetricsEnum.annot_prob not in self.annotation_metrics:
            self.get_annotation_prob(AnnotationMetricsEnum.annot_prob)

        ann_keys = self.units[self.units.keys()[0]].get_unit_vector().keys()

        for ann1 in ann_keys:
            self.mutual_info_dict[ann1] = {}
            max_value = -1
            for ann2 in ann_keys:
                #joint probability p(ann1 = 0, ann2 = 0)
                p_0_0 = 0
                p_0_1 = 0
                p_1_0 = 0
                p_1_1 = 0
                for unit_id in self.units:
                    unit_vector = self.units[unit_id].get_unit_vector()
                    if unit_vector[ann1] > 0:
                        if unit_vector[ann2] > 0:
                            p_1_1 += 1
                        else:
                            p_1_0 += 1
                    else:
                        if unit_vector[ann2] > 0:
                            p_0_1 += 1
                        else:
                            p_0_0 += 1

                no_units = len(self.units)
                prob_ann1 = self.annotation_metrics[AnnotationMetricsEnum.annot_prob][ann1]
                prob_ann2 = self.annotation_metrics[AnnotationMetricsEnum.annot_prob][ann2]

                p_0_0 /=  (1.0 * no_units)
                p_0_1 /=  (1.0 * no_units)
                p_1_0 /=  (1.0 * no_units)
                p_1_1 /=  (1.0 * no_units)


                term_0_0 = 0 if p_0_0 == 0.0 or prob_ann1 == 1.0 or prob_ann2 == 1.0 else p_0_0 * log(p_0_0 / ((1 - prob_ann1) * (1 - prob_ann2)))
                term_0_1 = 0 if p_0_1 == 0.0 or prob_ann1 == 1.0 or prob_ann2 == 0.0 else p_0_1 * log(p_0_1 / ((1 - prob_ann1) * prob_ann2))
                term_1_0 = 0 if p_1_0 == 0.0 or prob_ann1 == 0.0 or prob_ann2 == 1.0 else p_1_0 * log(p_1_0 / (prob_ann1 * (1 - prob_ann2)))
                term_1_1 = 0 if p_1_1 == 0.0 or prob_ann1 == 0.0 or prob_ann2 == 0.0 else p_1_1 * log(p_1_1 / (prob_ann1 * prob_ann2))

                value = term_0_0 + term_0_1 + term_1_0 + term_1_1
                if value < 1e-14:
                    value = 0
                self.mutual_info_dict[ann1][ann2] = value

                if ann2 != ann1 and value > max_value:
                    max_value = value
            mutual_info[ann1] = max_value

        return mutual_info


    def get_cond_prob_minus_rel_prob_dict(self):
        if self.cond_prob_minus_rel_prob_dict:
            return self.cond_prob_minus_rel_prob_dict

        self.get_cond_prob_minus_rel_prob()
        return self.cond_prob_minus_rel_prob_dict

    def get_cond_prob_minus_rel_prob(self):
        cond_prob_minus_rel_prob = {}
        if AnnotationMetricsEnum.annot_prob not in self.annotation_metrics:
            self.get_annotation_prob(AnnotationMetricsEnum.annot_prob)
        if AnnotationMetricsEnum.cond_prob not in self.annotation_metrics:
            self.get_cond_annotation_prob(AnnotationMetricsEnum.cond_prob)

        ann_keys = self.units[self.units.keys()[0]].get_unit_vector().keys()
        for ann1 in ann_keys:
            self.cond_prob_minus_rel_prob_dict[ann1] = {}
            max_value = -1
            for ann2 in ann_keys:
                cond_prob = self.cond_prob_dict[ann1][ann2]
                prob = self.annotation_metrics[AnnotationMetricsEnum.annot_prob][ann1]
                self.cond_prob_minus_rel_prob_dict[ann1][ann2] = cond_prob - prob
                if ann2 != ann1 and self.cond_prob_minus_rel_prob_dict[ann1][ann2] > max_value:
                    max_value = self.cond_prob_minus_rel_prob_dict[ann1][ann2];

            cond_prob_minus_rel_prob[ann1] = max_value

        return cond_prob_minus_rel_prob


    def get_cond_annotation_prob(self, metric_key):
        # because both top prob and simple prob are generated in the same function
        # check if at least one was generated
        if AnnotationMetricsEnum.annot_prob not in self.annotation_metrics:
            self.get_annotation_prob(AnnotationMetricsEnum.annot_prob)

        ann_keys = self.units[self.units.keys()[0]].get_unit_vector().keys()

        for ann1 in ann_keys:
            self.cond_prob_dict[ann1] = {}
            self.top_ann_cond_prob_dict[ann1] = {}
            for ann2 in ann_keys:
                self.cond_prob_dict[ann1][ann2] = 0
                self.top_ann_cond_prob_dict[ann1][ann2] = 0
                for unit_id in self.units:
                    unit_vector = self.units[unit_id].get_unit_vector()
                    top_annotation_value = max(unit_vector.values())
                    if unit_vector[ann1] > 0:
                        if unit_vector[ann2] > 0:
                            self.cond_prob_dict[ann1][ann2] += 1
                            if top_annotation_value == unit_vector[ann2]:
                                self.top_ann_cond_prob_dict[ann1][ann2] += 1

                prob_ann2 = self.annotation_metrics[AnnotationMetricsEnum.annot_prob][ann2]
                top_prob_ann2 = self.annotation_metrics[AnnotationMetricsEnum.annot_top_prob][ann2]
                no_units = len(self.units)
                self.cond_prob_dict[ann1][ann2] = 0 if prob_ann2 == 0 else self.cond_prob_dict[ann1][ann2] / (
                    1.0 * no_units * prob_ann2)
                self.top_ann_cond_prob_dict[ann1][ann2] = 0 if top_prob_ann2 == 0 else \
                    self.top_ann_cond_prob_dict[ann1][ann2] / (1.0 * no_units * top_prob_ann2)

        cond_prob = {}
        top_ann_cond_prob = {}
        for ann in self.cond_prob_dict:
            max_value_cond = -1
            max_value_top_cond = -1
            for key in self.cond_prob_dict[ann]:
                if key != ann and self.cond_prob_dict[ann][key] > max_value_cond:
                    max_value_cond = self.cond_prob_dict[ann][key]
                if key != ann and self.top_ann_cond_prob_dict[ann][key] > max_value_top_cond:
                    max_value_top_cond = self.top_ann_cond_prob_dict[ann][key]
            cond_prob[ann] = max_value_cond
            top_ann_cond_prob[ann] = max_value_top_cond

        self.annotation_metrics[AnnotationMetricsEnum.cond_prob] = cond_prob
        self.annotation_metrics[AnnotationMetricsEnum.top_ann_cond_prob] = top_ann_cond_prob

        return self.annotation_metrics[metric_key]

    def get_cond_prob_dict(self):
        if self.cond_prob_dict:
            return self.cond_prob_dict

        self.get_annotation_prob(AnnotationMetricsEnum.cond_prob)
        return self.cond_prob_dict

    def get_top_ann_cond_prob_dict(self):
        if self.top_ann_cond_prob_dict:
            return self.top_ann_cond_prob_dict

        self.get_annotation_prob(AnnotationMetricsEnum.top_ann_cond_prob)
        return self.top_ann_cond_prob_dict

    def get_annotation_prob(self, metric_key):
        annot_prob = {}
        annot_freq = {}
        annot_top_prob = {}

        for annKey in self.units[self.units.keys()[0]].get_unit_vector():
            annot_prob[annKey] = 0
            annot_freq[annKey] = 0
            annot_top_prob[annKey] = 0

        for unit_id in self.units:
            unit_vector = self.units[unit_id].get_unit_vector()
            top_annotation_value = max(unit_vector.values())
            for annKey in unit_vector:
                if unit_vector[annKey] > 0:
                    annot_freq[annKey] += 1
                    annot_prob[annKey] += 1
                if unit_vector[annKey] == top_annotation_value:
                    annot_top_prob[annKey] += 1

        no_units = len(self.units)
        for annKey in annot_prob:
            annot_prob[annKey] = annot_prob[annKey] / (1.0 * no_units)
            annot_top_prob[annKey] = annot_top_prob[annKey] / (1.0 * no_units)

        self.annotation_metrics[AnnotationMetricsEnum.annot_freq] = annot_freq
        self.annotation_metrics[AnnotationMetricsEnum.annot_prob] = annot_prob
        self.annotation_metrics[AnnotationMetricsEnum.annot_top_prob] = annot_top_prob

        return self.annotation_metrics[metric_key]

    def get_annotation_clarity(self):
        all_units_vectors = []
        for unit_id in self.units:
            unit = self.units[unit_id]
            unit_vector = unit.get_cosine_vector()
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
        for unit_id in self.units:
            unit = self.units[unit_id]
            unit_vector = unit.get_unit_vector()
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
                    max_value = similarity_dict[ann][key]
            relation_ambiguity[ann] = max_value

        return relation_ambiguity

    @staticmethod
    def remove_annotations(annotations_to_filter, unit_vector):
        results = {}
        for ann in unit_vector:
            if ann not in annotations_to_filter:
                results[ann] = unit_vector[ann]
        return results

    @staticmethod
    def merge_annotations(annotations_to_filter, unit_vector):
        results = {}
        filtered_annotations = []
        for ann in annotations_to_filter:
            value = 0
            for similar_ann in annotations_to_filter[ann]:
                if similar_ann not in unit_vector:
                    ann.replace(similar_ann, '')
                    continue
                else:
                    filtered_annotations.append(similar_ann)
                    value += unit_vector[similar_ann]
            results[ann] = value

        for key in annotations_to_filter:
            filtered_annotations.extend(annotations_to_filter[key])

        for ann in unit_vector:
            if ann not in filtered_annotations:
                results[ann] = unit_vector[ann]

        return results

    @staticmethod
    def filter_annotation(metric_key, annotations_to_filter, unit_vector):
        results = {}
        if AnnotationMetricsEnum.annot_clarity == metric_key:
            results = Annotation.remove_annotations(annotations_to_filter, unit_vector)
        elif AnnotationMetricsEnum.annot_ambiguity == metric_key:
            results = Annotation.merge_annotations(annotations_to_filter, unit_vector)
        else:
            raise Exception("not yet implemented")

        return results

    def get_filtered_set_basic(self, metric_thresholds, metric_key):
        metric_values = self.annotation_metrics[metric_key]
        filtered_set = []
        for annotation in metric_values:
            if metric_thresholds[0] < metric_values[annotation] < metric_thresholds[1]:
                filtered_set.append(annotation)
        return filtered_set

    def get_filtered_set_ambiguity(self, metric_thresholds):
        metric_values = self.annotation_metrics[AnnotationMetricsEnum.annot_ambiguity]
        filtered_dic = {}
        results = {}
        explored = []
        for annotation in metric_values:
            if metric_thresholds[0] < metric_values[annotation] < metric_thresholds[1]:
                #find the most similar annotation to this one
                similar_to = ""
                for name, value in self.rel_similarity_dict[annotation].iteritems():
                    if value == metric_values[annotation]:
                        similar_to = name
                        break
                if annotation not in filtered_dic:
                    filtered_dic[annotation] = [similar_to]
                else:
                    filtered_dic[annotation].append(similar_to)
                #add the relation in both directions
                if similar_to not in filtered_dic:
                    filtered_dic[similar_to] = [annotation]
                else:
                    filtered_dic[similar_to].append(annotation)

        #print filtered_dic
        #find the connected components
        for annotation in filtered_dic:
            if annotation in explored:
                continue
            similar_annotations_list = []
            similar_ann_name = "" + annotation

            explored.append(annotation)
            similar_annotations_list.append(annotation)

            for similar_ann in filtered_dic[annotation]:
                if similar_ann in explored:
                    continue
                similar_annotations_list.append(similar_ann)
                similar_ann_name += "_" + similar_ann
                if similar_ann in filtered_dic:
                    filtered_dic[annotation].extend(filtered_dic[similar_ann])

                explored.append(similar_ann)

            results[similar_ann_name] = similar_annotations_list

        return results


    def get_filtered_set(self, metric_key, metric_thresholds):
        results = []
        if AnnotationMetricsEnum.annot_clarity == metric_key:
            results = self.get_filtered_set_basic(metric_thresholds, AnnotationMetricsEnum.annot_clarity)
        elif AnnotationMetricsEnum.annot_ambiguity == metric_key:
            results = self.get_filtered_set_ambiguity(metric_thresholds)
        else:
            raise Exception("not yet implemented")

        return results