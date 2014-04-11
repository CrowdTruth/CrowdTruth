import numpy as np
from scipy import spatial


class VectorMetrics:
    other_option = "OTHER"
    none_option = "NONE"

    def __init__(self, annotation_dict):
        #TODO
        #add comments
        """

        :type annotationDict: dict
        contains the names of annotations types and how many times they where selected
        """

        #assert isinstance(annotationDict, dict)
        self.filtered_ann_dict = annotation_dict.copy()
        self.ann_dict = annotation_dict.copy()
        self.filtered_ann_dict.pop(self.other_option, None)
        self.filtered_ann_dict.pop(self.none_option, None)

    def get_magnitude(self):
        return np.linalg.norm(self.ann_dict.values())

    def get_norm_magnitude(self):
        ann_values = self.ann_dict.values()
        sum_ann_values = sum(ann_values)
        if sum_ann_values == 0:
            return 0
        return np.linalg.norm(ann_values) / sum_ann_values

    def get_norm_relation_magnitude(self):
        filtered_ann_values = self.filtered_ann_dict.values()
        sum_filtered_ann_values = sum(filtered_ann_values)
        if sum_filtered_ann_values == 0:
            return 0
        return np.linalg.norm(filtered_ann_values) / sum_filtered_ann_values

    def get_norm_relation_magnitude_by_all(self):
        filtered_ann_values = self.filtered_ann_dict.values()
        ann_values = self.ann_dict.values()
        sum_ann_values = sum(ann_values)
        if sum_ann_values == 0:
            return 0
        return np.linalg.norm(filtered_ann_values) / sum(ann_values)

    def get_cosine_vector(self):
        cosine_vector = {}

        # don't change ann_values vector before extracting both
        # the keys and the values for preserving the order
        ann_keys = self.ann_dict.keys()
        ann_values = self.ann_dict.values()
        ann_length = len(ann_keys)

        #if the ann is the zero vector cosine returns error
        if sum(ann_values) == 0:
            return self.ann_dict

        for iter in range(0, ann_length):
            #create the ann_keys[iter] relation vector
            unit_vec = np.zeros(ann_length)
            unit_vec[iter] = 1.0
            #compute the cosine measure
            #dot_product = np.dot(unit_vec, ann_values)
            #rel_cosine = dot_product / (np.sqrt(dot_product) * np.sqrt(dot_product))
            rel_cosine = 1 - spatial.distance.cosine(unit_vec, ann_values)
            cosine_vector[ann_keys[iter]] = rel_cosine

        return cosine_vector