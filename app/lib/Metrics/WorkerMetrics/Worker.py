from .. import config
import urllib
import json
import sys
import numpy as np
from scipy import spatial
from WorkerMetricsEnum import *
from ..VectorMetrics import *
from ..UnitMetrics.Unit import *

class Worker:

    def __init__(self, crowd_agent_id,  unit_vectors, unit_freq):
        self.crowd_agent_id = crowd_agent_id
        self.unit_vectors = unit_vectors
        self.unit_freq = unit_freq
        self.worker_metrics = {}
        self.worker_agreement = {}

    def get_unit_vectors(self):
        return self.unit_vectors

    def get_metrics(self, workers, units, metrics_to_apply):
        if not isinstance(metrics_to_apply, list):
            metrics_to_apply = [metrics_to_apply]

        results = {}

        for metric_key in metrics_to_apply:
            if metric_key in self.worker_metrics:
                results[metric_key] = self.worker_metrics[metric_key]
                continue

            if len(self.unit_vectors.keys()) == 0:
                results[metric_key] = 0
                self.worker_metrics[metric_key] = 0
                continue

            metric_value = 0

            #update this to be computed per term
            if WorkerMetricsEnum.no_of_units == metric_key:
                    metric_value = len(self.unit_vectors)
            elif WorkerMetricsEnum.ann_per_unit == metric_key:
                    metric_value = self.get_ann_per_unit()
            elif WorkerMetricsEnum.avg_worker_agreement == metric_key:
                    metric_value = self.get_avg_worker_agreement(workers)
            elif WorkerMetricsEnum.worker_cosine == metric_key:
                    metric_value = self.get_worker_cosine(units)
            elif WorkerMetricsEnum.factor_selection_check == metric_key:
                    metric_value = self.get_factor_selection_check()
            elif WorkerMetricsEnum.novelty_irrelevant_sel == metric_key:
                    metric_value = self.get_novelty_irrelevant_sel()
            elif WorkerMetricsEnum.consistency_check == metric_key:
                    metric_value = self.get_consistency_check()
            elif WorkerMetricsEnum.novel_words_per_tweet == metric_key:
                    metric_value = self.get_novel_words_per_tweet()
            elif WorkerMetricsEnum.notnovel_words_per_tweet == metric_key:
                    metric_value = self.get_notnovel_words_per_tweet()
            elif WorkerMetricsEnum.novelty_relevant_sel == metric_key:
                    metric_value = self.get_novelty_relevant_sel()
            elif WorkerMetricsEnum.novelty_t1morenovel_sel == metric_key:
                    metric_value = self.get_novelty_t1morenovel_sel()
            elif WorkerMetricsEnum.novelty_t1equalnovel_sel == metric_key:
                    metric_value = self.get_novelty_t1equalnovel_sel()
            elif WorkerMetricsEnum.novelty_t1lessnovel_sel == metric_key:
                    metric_value = self.get_novelty_t1lessnovel_sel()
            elif WorkerMetricsEnum.novelty_selection_frequency == metric_key:
                    metric_value = self.get_novelty_selection_frequency()
            elif WorkerMetricsEnum.event_ann_per_unit == metric_key:
                    metric_value = self.get_event_ann_per_unit()
            elif WorkerMetricsEnum.missed_instructions == metric_key:
                    metric_value = self.get_missed_instructions()
            elif WorkerMetricsEnum.none_event_type_frequency == metric_key:
                    metric_value = self.get_none_event_type_frequency()


            results[metric_key] = metric_value
            self.worker_metrics[metric_key] = metric_value

        return results

    def get_event_ann_per_unit(self):
        no_annotations = 0
        no_units = 0
        for unit_id in self.unit_vectors:
        	unitString = str(unit_id)
        	if unitString.endswith("eventType"):
        		no_annotations += sum(self.unit_vectors[unit_id].values())
            	#count the number of units, an unit can be annotated twice is some cases
            	no_units += self.unit_freq[unit_id]

        if len(self.unit_vectors) == 0:
            return 0

        return no_annotations/(no_units * 1.0)

 
    def get_consistency_check(self):
        sel_check = 0.0
        count = 0
        for unit_id in self.unit_vectors:
            if "[CHECK_FAILED]" in self.unit_vectors[unit_id]:
                sel_check += self.unit_vectors[unit_id]['[CHECK_FAILED]']
                count += 1

        if count == 0:
            return 0

        return sel_check/(count * 1.0)

    def get_missed_instructions(self):
        sel_check = 0.0
        count = 0
        for unit_id in self.unit_vectors:
        	if "check_failed" in self.unit_vectors[unit_id]:
        		sel_check += self.unit_vectors[unit_id]['check_failed']
        		count += 1

        if count == 0:
            return 0

        return sel_check/(count * 1.0)

    def get_none_event_type_frequency(self):
    	sel_check = 0.0
        count = 0
        for unit_id in self.unit_vectors:
        	unitString = str(unit_id)
        	if unitString.endswith("eventType"):
        		if "[NONE]" in self.unit_vectors[unit_id]:
        			sel_check += self.unit_vectors[unit_id]["[NONE]"]
               		count += 1

        if count == 0:
            return 0

        return sel_check/(count * 1.0)


    def get_novelty_irrelevant_sel(self):
        sel_check = 0.0
        count = 0
        for unit_id in self.unit_vectors:
        	unitString = str(unit_id)
        	if unitString.endswith("t1_relevancy"):
        	   	if "irrelevant" in self.unit_vectors[unit_id]:
        	   		if self.unit_vectors[unit_id]['irrelevant'] == 1:
        	   			sel_check += self.unit_vectors[unit_id]['irrelevant']
        	   		count += 1
        	if unitString.endswith("t2_relevancy"):
        		if "irrelevant" in self.unit_vectors[unit_id]:
        			if self.unit_vectors[unit_id]['irrelevant'] == 1:
        				sel_check += self.unit_vectors[unit_id]['irrelevant']
        			count += 1
        if count == 0:
            return 0

        return sel_check/(count * 1.0)

    def get_novelty_relevant_sel(self):
        sel_check = 0.0
        count = 0
        for unit_id in self.unit_vectors:
        	if "relevant" in self.unit_vectors[unit_id]:
        		sel_check += self.unit_vectors[unit_id]['relevant']
        		count += 1

        if count == 0:
            return 0

        return sel_check/(count * 1.0)

    def get_novelty_t1morenovel_sel(self):
        sel_check = 0.0
        count = 0
        for unit_id in self.unit_vectors:
        	unitString = str(unit_id)
        	if unitString.endswith("t1_novelty"):
	        	if "more_novel" in self.unit_vectors[unit_id]:
	        		sel_check += self.unit_vectors[unit_id]['more_novel']
	               	count += 1

        if count == 0:
            return 0

        return sel_check/(count * 1.0)

    def get_novelty_t1equalnovel_sel(self):
        sel_check = 0.0
        count = 0
        for unit_id in self.unit_vectors:
        	unitString = str(unit_id)
        	if unitString.endswith("t1_novelty"):
	        	if "equal_novel" in self.unit_vectors[unit_id]:
	        		sel_check += self.unit_vectors[unit_id]['equal_novel']
	               	count += 1

        if count == 0:
            return 0

        return sel_check/(count * 1.0)

    def get_novelty_t1lessnovel_sel(self):
        sel_check = 0.0
        count = 0
        for unit_id in self.unit_vectors:
        	unitString = str(unit_id)
        	if unitString.endswith("t1_novelty"):
	        	if "less_novel" in self.unit_vectors[unit_id]:
	        		sel_check += self.unit_vectors[unit_id]['less_novel']
	               	count += 1

        if count == 0:
            return 0

        return sel_check/(count * 1.0)


    def get_novelty_selection_frequency(self):
  		sel_check_less = 0.0
  		count_less = 0
  		sel_check_equal = 0.0
  		count_equal = 0
		sel_check_more = 0.0
		count_more = 0
		sel_check_na = 0.0
		count_na = 0
		score_less = 0.0
		score_equal = 0.0
		score_more = 0.0
		score_na = 0.0
		for unit_id in self.unit_vectors:
			unitString = str(unit_id)
			if unitString.endswith("t1_novelty"):
				if "less_novel" in self.unit_vectors[unit_id]:
					sel_check_less += self.unit_vectors[unit_id]['less_novel']
					count_less += 1
		      	if "equal_novel" in self.unit_vectors[unit_id]:
		      		sel_check_equal += self.unit_vectors[unit_id]['equal_novel']
		      		count_equal += 1
		      	if "more_novel" in self.unit_vectors[unit_id]:
		        	sel_check_more += self.unit_vectors[unit_id]['more_novel']
		      		count_more += 1
		        if "na" in self.unit_vectors[unit_id]:
		        	sel_check_na += self.unit_vectors[unit_id]['na']
		      		count_na += 1

		if count_less != 0:
		    score_less = sel_check_less / (count_less * 1.0)
		if count_equal != 0:
		    score_equal = sel_check_equal / (count_equal * 1.0)
		if count_more != 0:
		    score_more = sel_check_more / (count_more * 1.0)
		if count_na != 0:
		    score_na = sel_check_na / (count_na * 1.0)

		list_values = [score_less, score_equal, score_more, score_na]

		return max(list_values)

    def get_novel_words_per_tweet(self):
        no_annotations = 0
        no_units = 0
        for unit_id in self.unit_vectors:
        	unitString = str(unit_id)
        	if unitString.endswith("t1_novelwords"):
        		if (self.unit_vectors[unit_id]["[NONE]"] != 1) and (self.unit_vectors[unit_id]["[CHECK_FAILED]"] != 1):
        			no_annotations += sum(self.unit_vectors[unit_id].values())
        			no_units += 1
        	if unitString.endswith("t2_novelwords"):
        		if (self.unit_vectors[unit_id]["[NONE]"] != 1) and (self.unit_vectors[unit_id]["[CHECK_FAILED]"] != 1):
        			no_annotations += sum(self.unit_vectors[unit_id].values())
        			no_units += 1

        if len(self.unit_vectors) == 0:
            return 0

        if no_units == 0:
            return 0

        return no_annotations/(no_units * 1.0)

    def get_notnovel_words_per_tweet(self):
        no_annotations = 0
        no_units = 0
        for unit_id in self.unit_vectors:
        	unitString = str(unit_id)
        	if unitString.endswith("t1_notnovelwords"):
        		if self.unit_vectors[unit_id]["[NONE]"] != 1:
        			no_annotations += sum(self.unit_vectors[unit_id].values())
        			no_units += 1
        	if unitString.endswith("t2_notnovelwords"):
        		if self.unit_vectors[unit_id]["[NONE]"] != 1:
        			no_annotations += sum(self.unit_vectors[unit_id].values())
        			no_units += 1

        if len(self.unit_vectors) == 0:
            return 0

        if no_units == 0:
            return 0

        return no_annotations/(no_units * 1.0)

    def get_factor_selection_check(self):
        sel_check = 0.0
        count = 0
        for unit_id in self.unit_vectors:
            if "[CHECK_FAILED]" in self.unit_vectors[unit_id]:
                sel_check += self.unit_vectors[unit_id]['[CHECK_FAILED]']
                count += 1

        if count == 0:
            return 0

        return sel_check/(count * 1.0)

    def get_ann_per_unit(self):
        no_annotations = 0
        no_units = 0
        #is it per unit or per term annotated?
        for unit_id in self.unit_vectors:
            no_annotations += sum(self.unit_vectors[unit_id].values())
            #count the number of units, an unit can be annotated twice is some cases
            no_units += self.unit_freq[unit_id]

        if len(self.unit_vectors) == 0:
            return 0

        return no_annotations/(no_units * 1.0)

    #rewrite this if it is inefficient to just collect workers based on sentences
    def get_avg_worker_agreement(self, workers):
        weighted_sum = 0.0
        weighted_count = 0.0
        for worker_id in workers:
            worker = workers[worker_id]
            if (self.crowd_agent_id in worker.worker_agreement):
                w_w_agreement = worker.worker_agreement[self.crowd_agent_id]
            else:
                w_w_agreement = self.get_w_w_agreement(workers[worker_id])
                self.worker_agreement[worker_id] = w_w_agreement

            no_common_units = len(self.get_common_units(workers[worker_id]))
            weighted_count += no_common_units
            weighted_sum += no_common_units*w_w_agreement

        if weighted_count == 0.0:
            return 0

        return weighted_sum/(weighted_count * 1.0)

    def get_common_units(self, worker):
        worker_unit_vectors = worker.get_unit_vectors()
        worker_units = worker_unit_vectors.keys()
        self_units = self.get_unit_vectors().keys()
        common_units = list(set(worker_units) & set(self_units))
        return common_units

    #check if terms or sentence
    def get_norm_worker_unit_vector(self,unit_id):
        worker_unit_vec = self.unit_vectors[unit_id]
        norm_worker_unit_vec = {}
        for key in worker_unit_vec:
            norm_worker_unit_vec[key] = worker_unit_vec[key]/(self.unit_freq[unit_id] * 1.0)
        return norm_worker_unit_vec

    def get_w_w_agreement(self, worker):
        common_units = self.get_common_units(worker)
        if len(common_units) == 0:
            return 0

        hit_count = 0
        annotation_count = 0
        for unit_id in common_units:
            self_unit_vector = self.get_norm_worker_unit_vector(unit_id)
            worker_unit_vect = worker.get_norm_worker_unit_vector(unit_id)
            for key in self_unit_vector:
                hit_count += min(self_unit_vector[key],worker_unit_vect[key])
            annotation_count += sum(self_unit_vector.values())

        if annotation_count == 0:
            return 0

        return hit_count/(1.0 * annotation_count)

    def get_w_u_similarity(self, self_unit_dict, media_unit_dict):
        media_unit_vec = np.zeros(len(media_unit_dict))
        self_unit_vec = np.zeros(len(self_unit_dict))
        index = 0
        for key in self_unit_dict.keys():
            media_unit_vec[index] = media_unit_dict[key] - self_unit_dict[key]
            self_unit_vec[index] = self_unit_dict[key]
            index += 1

        if (np.count_nonzero(media_unit_vec) == 0) or (np.count_nonzero(self_unit_vec) == 0):
            return 0

        rel_cosine = spatial.distance.cosine(media_unit_vec, self_unit_vec)
        return rel_cosine

    def get_worker_cosine(self, units):
        sum_cos = 0.0
        count = 0
        for unit_id in self.unit_vectors:
            sum_cos += self.get_w_u_similarity(self.unit_vectors[unit_id], units[unit_id].unit_vector)
            count += 1

        if count == 0:
            return 0

        return sum_cos/(1.0 * count)

