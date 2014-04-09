from .. import config
import urllib
import json
import sys
from UnitMetricsEnum import *
from ..VectorMetrics import *

class Unit:


    # jobs_dict list or dict : key = jobID, value = list_of_spammers to be excluded
    # filtered = true too remove spammers
    def __init__(self, sentence_id, jobs_dict, filtered):
        self.sentence_id = sentence_id
        self.filtered = filtered
        self.unit_vector = {}
        self.unit_metrics = {}
        #if the spammers ids are already provided, or no filtering is needed
        if isinstance(jobs_dict, dict) or filtered is False:
            self.jobs_dict = jobs_dict
            return
        self.jobs_dict = {}
        #obtain the spam list of the jobs
        for job_id in jobs_dict:
            api_param = urllib.urlencode({'field[_id]': job_id, 'only[]': 'metrics.spammers.list'})
            api_call = urllib.urlopen(config.server + "?" + api_param)
            response = json.JSONDecoder().decode(api_call.read())
            try:
                self.jobs_dict[job_id] = response[0]['metrics']['spammers']['list']
            except KeyError:
                print(sys.exc_info()[0])
                raise Exception("There are no spammers computed for job:" + job_id)

    #it should be property?
    def get_unit_vector(self):
        if self.unit_vector:
            return self.unit_vector
        #compute the value of all responses
        all_job_results = []
        for job_id in self.jobs_dict:
            api_param = urllib.urlencode({'field[_id]': job_id, 'only[]': 'results.withSpam'})
            api_call = urllib.urlopen(config.server + "?" + api_param)
            response = json.JSONDecoder().decode(api_call.read())
            job_result = response[0]['results']['withSpam'][self.sentence_id]
            all_job_results.append(job_result)

        unit_keys = all_job_results[0].keys()
        for key in unit_keys:
            agg_value = sum(result[key] for result in all_job_results)
            self.unit_vector[key] = agg_value

        if self.filtered is False:
            return self.unit_vector

        spam_annotations = []
        for job_id in self.jobs_dict:
            for worker in self.jobs_dict[job_id]:
                api_param = urllib.urlencode({'field[job_id]': job_id,
                                              'field[unit_id]': self.sentence_id,
                                              'field[documentType]': 'annotation',
                                              'field[crowdAgent_id]': worker,
                                              'only[]': 'dictionary'})
                api_call = urllib.urlopen(config.server + "?" + api_param)
                response = json.JSONDecoder().decode(api_call.read())
                if len(response) > 1:
                    raise Exception("A worker should annotate a sentence just once for a job")
                if len(response) > 0 :
                    spam_result = response[0]['dictionary']
                    spam_annotations.append(spam_result)

        for key in self.unit_vector:
            agg_value = sum(spam_result[key] for spam_result in spam_annotations)
            self.unit_vector[key] -= agg_value

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

            metric_value = None

            if UnitMetricsEnum.magnitude == metric_key:
                metric_value = VectorMetrics(self.unit_vector).get_magnitude()
            elif UnitMetricsEnum.norm_magnitude == metric_key:
                metric_value = VectorMetrics(self.unit_vector).get_norm_magnitude()
            elif UnitMetricsEnum.norm_relation_magnitude == metric_key:
                metric_value = VectorMetrics(self.unit_vector).get_norm_relation_magnitude()
            elif UnitMetricsEnum.norm_relation_magnitude_all == metric_key:
                metric_value = VectorMetrics(self.unit_vector).get_norm_relation_magnitude_by_all()
            elif UnitMetricsEnum.max_relation_Cos == metric_key:
                metric_value = max(VectorMetrics(self.unit_vector).get_cosine_vector().values())
            elif UnitMetricsEnum.no_annotators == metric_key:
                metric_value = self.get_no_annotators()

            results[metric_key] = metric_value
            self.unit_metrics[metric_key] = metric_value

        return results

    def get_cosine_vector(self):
        if not self.unit_vector:
            self.get_unit_vector()

        return VectorMetrics(self.unit_vector).get_cosine_vector()

    def get_no_annotators(self):
        no_annotators = 0
        for job_id in self.jobs_dict:
            api_param = urllib.urlencode({'field[job_id]': job_id,
                                          'field[unit_id]': self.sentence_id,
                                          'field[documentType]': 'annotation',
                                          'only[]': '_id',
                                          'only[]':'crowdAgent_id'})
            api_call = urllib.urlopen(config.server + "?" + api_param)
            response = json.JSONDecoder().decode(api_call.read())
            if self.filtered:
                for annotation in response:
                    if annotation['crowdAgent_id'] not in self.jobs_dict[job_id]:
                        no_annotators += 1
                #no_annotators += 3
            else:
                no_annotators += len(response)

        return no_annotators