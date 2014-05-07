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

        raise Exception("Code which shouldn't be reached:"  )
        # self.jobs_dict = {}
        # #obtain the spam list of the jobs
        # for job_id in jobs_dict:
        #     api_param = urllib.urlencode({'field[_id]': job_id, 'only[]': 'metrics.spammers.list'})
        #     api_call = urllib.urlopen(config.server + "?" + api_param)
        #     response = json.JSONDecoder().decode(api_call.read())
        #     try:
        #         self.jobs_dict[job_id] = response[0]['metrics']['spammers']['list']
        #     except KeyError:
        #         print(sys.exc_info()[0])
        #         raise Exception("There are no spammers computed for job:" + job_id)

    #it should be property?
    def get_unit_vector(self):
        if self.unit_vector:
            return self.unit_vector
        #compute the value of all responses
        all_job_results = []
        for job_id in self.jobs_dict:
            api_param = urllib.urlencode({'field[_id]': job_id, 'limit':10000, 'only[]': 'results.withSpam'})
            api_call = urllib.urlopen(config.server + "?" + api_param)
            response = json.JSONDecoder().decode(api_call.read())
            job_result = response[0]['results']['withSpam'][self.sentence_id]
            all_job_results.append(job_result)

        for micro_task in all_job_results:
            for annotation, value in micro_task.items():
                if annotation not in self.unit_vector:
                    self.unit_vector[annotation] = value
                    continue

                for item in value:
                    self.unit_vector[annotation][item] += value[item]

        if self.filtered is False:
            return self.unit_vector

        spam_annotations = {}
        for job_id in self.jobs_dict:
            for worker in self.jobs_dict[job_id]:
                api_param = urllib.urlencode({'field[job_id]': job_id,'limit':10000,
                                              'field[unit_id]': self.sentence_id,
                                              'field[documentType]': 'annotation',
                                              'field[crowdAgent_id]': worker,
                                              'only[]': 'dictionary'})
                api_call = urllib.urlopen(config.server + "?" + api_param)
                response = json.JSONDecoder().decode(api_call.read())
                for res in response:
                    for annotation, value in res['dictionary'].items():
                        if annotation not in spam_annotations:
                            spam_annotations[annotation] = value
                            continue
                        for item in value:
                            spam_annotations[annotation][item] += value[item]

        if len(spam_annotations) == 0:
            return self.unit_vector

        for annotation, value in self.unit_vector.items():
            for key in value:
                self.unit_vector[annotation][key] -= spam_annotations[annotation][key];

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
                for annotation in self.unit_vector:
                    metric_value[annotation] = VectorMetrics(self.unit_vector[annotation]).get_magnitude()
            elif UnitMetricsEnum.norm_magnitude == metric_key:
                for annotation in self.unit_vector:
                    metric_value[annotation] = VectorMetrics(self.unit_vector[annotation]).get_norm_magnitude()
            elif UnitMetricsEnum.norm_relation_magnitude == metric_key:
                for annotation in self.unit_vector:
                    metric_value[annotation] = VectorMetrics(self.unit_vector[annotation]).get_norm_relation_magnitude()
            elif UnitMetricsEnum.norm_relation_magnitude_all == metric_key:
                for annotation in self.unit_vector:
                    metric_value[annotation] = VectorMetrics(self.unit_vector[annotation]).get_norm_relation_magnitude_by_all()
            elif UnitMetricsEnum.max_relation_Cos == metric_key:
                for annotation in self.unit_vector:
                    metric_value[annotation] = max(VectorMetrics(self.unit_vector[annotation]).get_cosine_vector().values())
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
        metric = {}
        for job_id in self.jobs_dict:
            api_param = urllib.urlencode({'field[job_id]': job_id, 'limit':10000,
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

        #change this if ever
        for annotation in self.unit_vector:
            metric[annotation] = no_annotators
        return metric