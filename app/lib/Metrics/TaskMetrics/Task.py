import urllib
import ConfigParser
import json
from .. import config
from .. import encoder
from ..UnitMetrics.Unit import *
from ..UnitMetrics.UnitMetricsEnum import *
from ..UnitMetrics.UnitFiltersEnum import *
from ..Filters import *
from ..WorkerMetrics.Worker import *

class Task:
    def update_metrics(self, jobID, annotation_id):
        #update all the metrics - if null create
        #TODOf
        pass

    def __init__(self, jobs, template_id):
        # update to include multiple type of jobs..
        self.template_id = template_id
        self.default_thresholds = self.__get_default_thresholds()
        if isinstance(jobs,dict):
            self.jobs_dict = jobs
        else:
            self.jobs_dict = {}
            for job_id in jobs:
                api_param = urllib.urlencode({'field[_id]': job_id})
                api_call = urllib.urlopen(config.server + "?" + api_param)
                response = json.JSONDecoder().decode(api_call.read())
                self.jobs_dict[job_id] = response[0]['results'].keys()

    def __create_unit_cluster(self):
        unit_cluster = {}
        for job in self.jobs_dict:
            for unit in self.jobs_dict[job]:
                if unit in unit_cluster:
                    unit_cluster[unit].append(job)
                else:
                    unit_cluster[unit] = [job]
        return unit_cluster

    def __compute_mean_measure(self, all_units_metrics):
        mean_measures = {}
        metrics_keys = all_units_metrics[all_units_metrics.keys()[0]].keys()
        for key in metrics_keys:
            metric_mean = sum(metric[key] for (unit,metric) in all_units_metrics.iteritems())
            mean_measures[key] = metric_mean/len(all_units_metrics)
        return mean_measures

    def __compute_stddev_measure(self, all_units_metrics, mean_metrics = None):
        if mean_metrics is None:
            mean_metrics = self.__compute_mean_measure(all_units_metrics)

        stddev_measures = {}
        metrics_keys = all_units_metrics[all_units_metrics.keys()[0]].keys()
        for key in metrics_keys:
            metric_stddev = sum(pow((metric[key] - mean_metrics[key]),2) for (unit,metric) in all_units_metrics.iteritems())
            stddev_measures[key] = pow((metric_stddev/len(all_units_metrics)), 0.5)

        return stddev_measures

    def __get_sentence_filter_threshold(self):
        return {UnitFiltersEnum.stddev_mag_below_mean:1,
                UnitFiltersEnum.stddev_MRC_below_mean:1,
                UnitFiltersEnum.stddev_norm_mag_below_mean:1,
                UnitFiltersEnum.stddev_norm_rel_mag_below_mean:1,
                UnitFiltersEnum.stddev_norm_rel_mag_all_below_mean:1}

    def __get_default_thresholds(self):
        api_param = urllib.urlencode({'field[_id]': self.template_id,
                              'only[]': 'content.defaultThresholds'})
        api_call = urllib.urlopen(config.server + "?" + api_param)
        response = json.JSONDecoder().decode(api_call.read())
        return response[0]['content']['defaultThresholds']

    def __get_all_workers(self,filtered):
        workers_clusters = {}
        for job_id in self.jobs_dict:
            api_param = urllib.urlencode({'field[job_id]': job_id,
                              'field[documentType]': 'annotation',
                              'only[]': 'crowdAgent_id'})
            api_call = urllib.urlopen(config.server + "?" + api_param)
            response = json.JSONDecoder().decode(api_call.read())
            for annotation in response:
                crowd_agent = annotation['crowdAgent_id']
                if crowd_agent in workers_clusters:
                    if job_id not in workers_clusters[crowd_agent]:
                        workers_clusters[crowd_agent][job_id] = filtered
                else:
                    workers_clusters[crowd_agent] = {job_id:filtered}
        workers = []
        for worker_id in workers_clusters:
            #if the worker performance will be ever compared, it should be computed on the same filtered set of
            #sentences
            worker = Worker(worker_id, workers_clusters[worker_id], True, workers_clusters)
            workers.append(worker)
        return workers

    def create_metrics(self):
        unit_cluster = self.__create_unit_cluster()
        results = {}

        all_units_metrics = {}
        all_units = []

        for unit_id in unit_cluster:
         #   print(unit_id)
            unit = Unit(unit_id,unit_cluster[unit_id], False)
            all_units.append(unit)
            unit_result = unit.get_metrics(UnitMetricsEnum.__members__.values())
            all_units_metrics[unit_id] = unit_result

        results['all_unit_metrics'] = {}
        results['all_unit_metrics']['with_spam'] = all_units_metrics


        mean_metrics = self.__compute_mean_measure(all_units_metrics)
        stddev_measure = self.__compute_stddev_measure(all_units_metrics, mean_metrics)
        thresholds = self.__get_sentence_filter_threshold()

        #print(mean_metrics)
        #print(stddev_measure)

        unit_filter = Filters(mean_metrics,stddev_measure,thresholds)
        filtered_sentences = {}
        for filter_type in UnitFiltersEnum.__members__.values():
            filtered_list = []
            for unit in all_units:
                if unit_filter.is_filtered(unit, filter_type):
                    filtered_list.append(unit.sentence_id)
            filtered_sentences[filter_type] = filtered_list
        results['filtered_units'] = filtered_sentences
        #print("filtered sentences:")

        union_filtered_sent = []
        for filter_name in self.default_thresholds['unit_filters']:
            filter_enum_type = getattr(UnitFiltersEnum, filter_name, None)
            union_filtered_sent = list(set(union_filtered_sent)|set(filtered_sentences[filter_enum_type]))


        #print(union_filtered_sent)
        #TODO - if sentence provided select just workers which annotated those sentences
        workers = self.__get_all_workers(union_filtered_sent)
        all_workers_metrics = {}
        for worker in workers:
            worker_result = worker.get_metrics(WorkerMetricsEnum.__members__.values())
            all_workers_metrics[worker.crowd_agent_id] = worker_result

        results['workers'] = all_workers_metrics
        spammers = []
        for metric_name in self.default_thresholds['worker_thresholds'].keys():

            metric_thresholds = self.default_thresholds['worker_thresholds'][metric_name]
            metric = getattr(WorkerMetricsEnum, metric_name, None)
            for worker in all_workers_metrics:
              #  print(all_workers_metrics[worker])
              #  print(metric_thresholds)
                if metric_thresholds[0] < all_workers_metrics[worker][metric] < metric_thresholds[1]:
                    if worker not in spammers:
                        spammers.append(worker)

        #print(spammers)
        results['spammers_list'] = spammers
        #print(all_workers_metrics.keys())

        #create a new job dic with newly computed spammers
        all_units_metrics_ws = {}
        #compute average and compare
        for unit_id in unit_cluster:
         #   print(unit_id)
            job_dict = {}
            for job_id in unit_cluster[unit_id]:
                job_dict[job_id] = spammers
            unit_cluster[unit_id] = job_dict
            unit = Unit(unit_id,unit_cluster[unit_id], True)
         #!save here the without spam result for units
            unit_result = unit.get_metrics(UnitMetricsEnum.__members__.values())
            all_units_metrics_ws[unit_id] = unit_result

        results['all_unit_metrics']['without_spam'] = all_units_metrics_ws
        results['aggregate_metrics'] = {"mean_unit_metrics":mean_metrics,"stddev_unit_metrics":stddev_measure}


        encoder.c_make_encoder = None
        metrics_json = encoder.JSONEncoder().encode(results)

        #get the unfiltered sentences
        #print(std_dev_metrics)
        return metrics_json