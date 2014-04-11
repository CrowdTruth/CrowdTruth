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

    #to be changed when the question template is created
    def __get_job_filters(self,job_id):
        # TODO make a call to the question template/job configuration and get the active filters
        return ['stddev_MRC_below_mean']

    # jobs_dict list or dict : key = jobID, value = list_of_sentences to be excluded
    # filtered = true too remove spammers
    def __init__(self, crowd_agent_id, jobs_dict, filtered, workers_clusters=None):
        self.workers_clusters = workers_clusters
        self.crowd_agent_id = crowd_agent_id
        self.filtered = filtered
        self.worker_vector = {}
        self.workers = None
        self.worker_metrics = {}
        self.unit_clusters = {}

        #if the filtered sentence ids are already provided, or no filtering is needed
        if isinstance(jobs_dict, dict) or filtered is False:
            self.jobs_dict = jobs_dict
            self.get_unit_clusters()
            return

        self.jobs_dict = {}
        #obtain the filtered list of sentences
        for job_id in jobs_dict:
            # filters_names = self.__get_job_filters(job_id)
            # filtered_units = set([])
            # for filter_item in filters_names:
            #     api_param = urllib.urlencode({'field[_id]': job_id, 'only[]': 'metrics.filtered_units.' + filter_item})
            #     api_call = urllib.urlopen(config.server + "?" + api_param)
            #     response = json.JSONDecoder().decode(api_call.read())
            #     try:
            #          filtered_units |= set(response[0]['metrics']['filtered_units'][filter_item])
            #     except KeyError:
            #         print(sys.exc_info()[0])
            #         raise Exception("There are no filtered sentences computed for job:" + job_id + " filter:" + filter_item)
            api_param = urllib.urlencode({'field[_id]': job_id, 'only[]': 'metrics.filteredUnits.list'})
            api_call = urllib.urlopen(config.server + "?" + api_param)
            response = json.JSONDecoder().decode(api_call.read())
            try:
                 filtered_units = response[0]['metrics']['filteredUnits'][list]
            except KeyError:
                print(sys.exc_info()[0])
                raise Exception("There are no filtered sentences computed for job:" + job_id )
            self.jobs_dict[job_id] = filtered_units
        self.get_unit_clusters()

    def get_unit_clusters(self):
        #print("get unit clusters")
        if self.unit_clusters:
            return self.unit_clusters
        #print("job list")
        #print(self.jobs_dict)
        for job_id in self.jobs_dict:
            api_param = urllib.urlencode({'field[job_id]': job_id,
                              'field[crowdAgent_id]': self.crowd_agent_id,
                              'field[documentType]': 'annotation',
                              'only[0]': 'unit_id',
                              'only[1]': 'dictionary'})
            api_call = urllib.urlopen(config.server + "?" + api_param)
            response = json.JSONDecoder().decode(api_call.read())
            for annotation in response:
                unit_id = annotation['unit_id']
                #this unit needs to be filtered
                if unit_id in self.jobs_dict[job_id]:
                    continue
                job_annotation_results = {job_id:annotation['dictionary']}
                if unit_id in self.unit_clusters:
                    self.unit_clusters[unit_id][job_id] = annotation['dictionary']
                else:
                    self.unit_clusters[unit_id] = job_annotation_results

        return self.unit_clusters

    #it should be property?
    def get_worker_vector(self):
        #TODO create if necessary?, extend the existing metrics
        return self.worker_vector

    def get_metrics(self, metrics_to_apply):
        if not isinstance(metrics_to_apply, list):
            metrics_to_apply = [metrics_to_apply]

        results = {}

        for metric_key in metrics_to_apply:
            if metric_key in self.worker_metrics:
                results[metric_key] = self.worker_metrics[metric_key]
                continue

            metric_value = None

            if WorkerMetricsEnum.no_of_units == metric_key:
                metric_value = len(self.unit_clusters)
            elif WorkerMetricsEnum.ann_per_unit == metric_key:
                metric_value = self.get_ann_per_unit()
            elif WorkerMetricsEnum.avg_worker_agreement == metric_key:
                metric_value = self.get_avg_worker_agreement()
            elif WorkerMetricsEnum.worker_cosine == metric_key:
                metric_value = self.get_worker_cosine()

            results[metric_key] = metric_value
            self.worker_metrics[metric_key] = metric_value

        return results

    def get_ann_per_unit(self):
        no_annotations = 0
        for unit_id in self.unit_clusters:
            for job_id in self.unit_clusters[unit_id]:
                annotation_dict = self.unit_clusters[unit_id][job_id]
                no_annotations += sum(annotation_dict.values())

        if len(self.unit_clusters) == 0:
            return 0

        return no_annotations/(len(self.unit_clusters) * 1.0)

    def get_other_workers(self):
        workers_clusters = {}
        for job_id in self.jobs_dict:
            api_param = urllib.urlencode({'field[job_id]': job_id,
                              'field[documentType]': 'annotation',
                              'only[]': 'crowdAgent_id'})
            api_call = urllib.urlopen(config.server + "?" + api_param)
            response = json.JSONDecoder().decode(api_call.read())
            for annotation in response:
                crowd_agent = annotation['crowdAgent_id']
                #this unit needs to be filtered
                #if crowd_agent == self.crowd_agent_id:
                 #   continue
                #if this agent
                if crowd_agent in workers_clusters:
                    if job_id not in workers_clusters[crowd_agent]:
                        workers_clusters[crowd_agent].append(job_id)
                else:
                    workers_clusters[crowd_agent] = [job_id]
        return workers_clusters

    #rewrite this if it is inefficient to just collect workers based on sentences
    def get_avg_worker_agreement(self):
        if self.workers_clusters is None:
            self.workers_clusters = self.get_other_workers()

        if self.workers is None:
            self.workers = []
            for worker_id in self.workers_clusters:
                #if the worker performance will be ever compared, it should be computed on the same filtered set of
                #sentences
                if worker_id == self.crowd_agent_id:
                    continue
                worker = Worker(worker_id, self.workers_clusters[worker_id], False)
                self.workers.append(worker)


        weighted_sum = 0
        weighted_count = 0
        for worker in self.workers:
            w_w_agreement = self.get_w_w_agreement(worker)
            no_common_units = len(self.get_common_units(worker))
            weighted_count += no_common_units
            weighted_sum += no_common_units*w_w_agreement

        if weighted_sum == 0:
            return 0

        return weighted_sum/(weighted_count * 1.0)

    def get_common_units(self, worker):
        worker_unit_clusters = worker.get_unit_clusters()
        worker_units = worker_unit_clusters.keys()
        self_units = self.get_unit_clusters().keys()
        common_units = list(set(worker_units) & set(self_units))
        return common_units

    def get_norm_worker_unit_vector(self,unit_id):
        worker_unit_vec = self.get_worker_unit_vector(unit_id)
        norm_worker_unit_vec = {}
        no_units_jobs = len(self.unit_clusters[unit_id])
        for key in worker_unit_vec:
            norm_worker_unit_vec[key] = worker_unit_vec[key]/(no_units_jobs * 1.0)
        return norm_worker_unit_vec

    def get_worker_unit_vector(self, unit_id):
        job_dict = self.unit_clusters[unit_id]
        worker_unit_vector = {}
        annotations_keys = job_dict[job_dict.keys()[0]].keys()
        for key in annotations_keys:
            value = sum(annotation_dict[key] for (job_id,annotation_dict) in job_dict.iteritems())
            worker_unit_vector[key] = value
        return worker_unit_vector

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
        return hit_count/(1.0 * annotation_count)

    def get_w_u_similarity(self, unit_id):
        unit = Unit(unit_id, self.unit_clusters[unit_id].keys(), False)
        unit_sum_dict = unit.get_unit_vector()
        self_unit_dict = self.get_worker_unit_vector(unit_id)
        unit_sum_vec = np.zeros(len(unit_sum_dict))
        self_unit_vec = np.zeros(len(unit_sum_dict))
        index = 0
        for key in self_unit_dict.keys():
            unit_sum_vec[index] = unit_sum_dict[key] - self_unit_dict[key]
            self_unit_vec[index] = self_unit_dict[key]
            index += 1
        rel_cosine = spatial.distance.cosine(unit_sum_vec, self_unit_vec)
        return rel_cosine

    def get_worker_cosine(self):
        sum_cos = 0.0
        count = 0.0
        for unit_id in self.unit_clusters:
            sum_cos += self.get_w_u_similarity(unit_id)
            count += 1
        if count == 0:
            return 0

        return sum_cos/(1.0 * count)
