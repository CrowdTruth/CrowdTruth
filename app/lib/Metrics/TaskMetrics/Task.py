import urllib2
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
from ..AnnotationMetrics.Annotation import *
from ..AnnotationMetrics.AnnotationMetricsEnum import *

class Task:
    def update_metrics(self, jobID):
        #update all the metrics - if null create
        #TODOf
        pass

    def __init__(self, jobs, template_id):
        # update to include multiple type of jobs..
        self.template_id = template_id
        self.default_thresholds = self.__get_default_thresholds()
        if isinstance(jobs, dict):
            self.jobs_dict = jobs
        else:
            self.jobs_dict = {}
            for job_id in jobs:
                api_param = urllib.urlencode({'field[_id]': job_id, 'limit':10000})
                api_call = urllib2.urlopen(config.server + "?" + api_param)
                response = json.JSONDecoder().decode(api_call.read())
                self.jobs_dict[job_id] = response[0]['results']['withSpam'].keys()

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
        annotations = all_units_metrics[all_units_metrics.keys()[0]][metrics_keys[0]].keys();
        for key in metrics_keys:
            metric_mean = {}
            mean_measures[key] = {}
            for annotation in annotations:
                metric_mean[annotation] = sum(metric[key][annotation] for (unit,metric) in all_units_metrics.iteritems())
                mean_measures[key][annotation] = metric_mean[annotation]/len(all_units_metrics)
        return mean_measures

    def __compute_stddev_measure(self, all_units_metrics, mean_metrics = None):
        if mean_metrics is None:
            mean_metrics = self.__compute_mean_measure(all_units_metrics)

        stddev_measures = {}
        metrics_keys = all_units_metrics[all_units_metrics.keys()[0]].keys()
        annotations = all_units_metrics[all_units_metrics.keys()[0]][metrics_keys[0]].keys();
        for key in metrics_keys:
            metric_stddev = {}
            stddev_measures[key] = {}
            for annotation in annotations:
                metric_stddev[annotation] = sum(pow((metric[key][annotation] - mean_metrics[key][annotation]),2) for (unit,metric) in all_units_metrics.iteritems())
                stddev_measures[key][annotation] = pow((metric_stddev[annotation]/len(all_units_metrics)), 0.5)

        return stddev_measures

    def __get_sentence_filter_threshold(self):
        return {UnitFiltersEnum.stddev_mag_below_mean:1,
                UnitFiltersEnum.stddev_MRC_below_mean:1,
                UnitFiltersEnum.stddev_norm_mag_below_mean:1,
                UnitFiltersEnum.stddev_norm_rel_mag_below_mean:1,
                UnitFiltersEnum.stddev_norm_rel_mag_all_below_mean:1}

    def __get_default_thresholds(self):
        api_param = urllib.urlencode({'field[_id]': self.template_id,'limit':10000,
                              'only[]': 'content.defaultThresholds'})
        api_call = urllib2.urlopen(config.server + "?" + api_param)
        response = json.JSONDecoder().decode(api_call.read())
        return response[0]['content']['defaultThresholds']

    def __get_all_workers(self,filtered):
        workers_clusters = {}
        for job_id in self.jobs_dict:
            api_param = urllib.urlencode({'field[job_id]': job_id,'limit':10000,
                              'field[documentType]': 'annotation',
                              'only[]': 'crowdAgent_id'})
            api_call = urllib2.urlopen(config.server + "?" + api_param)
            response = json.JSONDecoder().decode(api_call.read())
            print len(response)
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

    def get_FactSpan_spam(self, all_workerFilter_metrics):
        spammers = []
        for worker in all_workerFilter_metrics:
            spam = False
            check_failed  =  all_workerFilter_metrics[worker][WorkerMetricsEnum.factor_selection_check]
            agree  =  all_workerFilter_metrics[worker][WorkerMetricsEnum.avg_worker_agreement]
            cos =  all_workerFilter_metrics[worker][WorkerMetricsEnum.worker_cosine];

            if agree < 0.3:
                spam = True

            if agree < 0.5 and check_failed > 0.3:
                spam = True

            if cos > 0.6:
                spam = True

            if cos > 0.4 and check_failed > 0.3:
                spam = True

            if spam == True and worker not in spammers:
                spammers.append(worker)

        return spammers

    def get_RelDir_spam(self, all_workerFilter_metrics):
        spammers = []

        for metric_name in self.default_thresholds['workerThresholds'].keys():
            metric_thresholds = self.default_thresholds['workerThresholds'][metric_name]
            metric = getattr(WorkerMetricsEnum, metric_name, None)
            for worker in all_workerFilter_metrics:
              #  print(all_workers_metrics[worker])
              #  print(metric_thresholds)
                if metric_thresholds[0] < all_workerFilter_metrics[worker][metric] < metric_thresholds[1]:
                    if worker not in spammers:
                        spammers.append(worker)

        return spammers

    def get_RelExt_spam(self, all_workerFilter_metrics):
        spammers = []

        return spammers

    def create_metrics(self):
        unit_cluster = self.__create_unit_cluster()
        metrics = {}

        all_units_metrics = {}
        all_units = []
        all_units_vec = {}

        for unit_id in unit_cluster:
         #   print(unit_id)
            unit = Unit(unit_id, unit_cluster[unit_id], False)
            all_units_vec[unit_id] = unit.get_unit_vector()
            all_units.append(unit)
            unit_result = unit.get_metrics(UnitMetricsEnum.__members__.values())
            all_units_metrics[unit_id] = unit_result

        metrics['pivotTables'] = {}
        metrics['pivotTables']['units'] = {}
        metrics['pivotTables']['units']['withSpam'] = {}
        for unit in all_units:
            metrics['pivotTables']['units']['withSpam'][unit.sentence_id] = unit.get_cosine_vector()

        print 1
        metrics['pivotTables']['annotations'] = {}
        metrics['pivotTables']['annotations']['withSpam'] = {}
        ann_keys = all_units[0].get_unit_vector().keys()
        annotation_metrics = {}
        for ann_key in ann_keys:
            annotation = Annotation(all_units, ann_key)
            annotation_metrics[ann_key] = annotation.get_metrics(AnnotationMetricsEnum.__members__.values())
            metrics['pivotTables']['annotations']['withSpam'][ann_key] = annotation.get_rel_similarity_dict()

        metrics['annotations'] = {}
        metrics['annotations']['withSpam'] = annotation_metrics

        #(union_filtered_sent)
        #TODO - if sentence provided select just workers which annotated those sentences
        workers = self.__get_all_workers([])
        all_workers_metrics = {}
        for worker in workers:
            worker_result = worker.get_metrics(WorkerMetricsEnum.__members__.values())
            all_workers_metrics[worker.crowd_agent_id] = worker_result




        mean_metrics = self.__compute_mean_measure(all_units_metrics)
        stddev_measure = self.__compute_stddev_measure(all_units_metrics, mean_metrics)
        thresholds = self.__get_sentence_filter_threshold()




        #print(mean_metrics)
        #print(stddev_measure)
        print 2
        #filter based on both sentences or one
        unit_filter = Filters(mean_metrics,stddev_measure,thresholds)
        filtered_sentences = {}
        for filter_type in UnitFiltersEnum.__members__.values():
            filtered_list = []
            for unit in all_units:
                if unit_filter.is_filtered(unit, filter_type):
                    filtered_list.append(unit.sentence_id)
            filtered_sentences[filter_type] = filtered_list

        metrics['units'] = {}
        for unit in all_units_metrics:
            for metric in all_units_metrics[unit]:
                avg = 0
                count  = 0
                for ann in all_units_metrics[unit][metric]:
                    count += 1
                    avg += all_units_metrics[unit][metric][ann]
                all_units_metrics[unit][metric]['avg'] = avg/(1.0 * count)

        metrics['units']['withSpam'] = all_units_metrics
        #print("filtered sentences:")

        union_filtered_sent = []
        for filter_name in self.default_thresholds['unitThresholds']:
            filter_enum_type = getattr(UnitFiltersEnum, filter_name, None)
            union_filtered_sent = list(set(union_filtered_sent)|set(filtered_sentences[filter_enum_type]))

        metrics['filteredUnits'] = {}
        metrics['filteredUnits']['count'] = len(union_filtered_sent)
        metrics['filteredUnits']['list'] = union_filtered_sent

        #print(union_filtered_sent)
        #TODO - if sentence provided select just workers which annotated those sentences
        workersFilter = self.__get_all_workers(union_filtered_sent)
        all_workerFilter_metrics = {}
        for worker in workersFilter:
            worker_result = worker.get_metrics(WorkerMetricsEnum.__members__.values())
            #if the worker doesn't have units which are not unfiltered
            if worker_result == 0:
                worker_result = all_workers_metrics[worker.crowd_agent_id]
            all_workerFilter_metrics[worker.crowd_agent_id] = worker_result

        print 3



        worker_mean_metrics = self.__compute_mean_measure(all_workerFilter_metrics)
        worker_stddev_measure = self.__compute_stddev_measure(all_workerFilter_metrics, worker_mean_metrics)


        spammers = []
        for metric_name in self.default_thresholds['workerThresholds'].keys():

            metric_thresholds = self.default_thresholds['workerThresholds'][metric_name]
            metric = getattr(WorkerMetricsEnum, metric_name, None)
            for worker in all_workerFilter_metrics:
              #  print(all_workers_metrics[worker])
              #  print(metric_thresholds)
                annotations = all_workerFilter_metrics[worker][metric].keys()
                spammer = True

                for annotation in annotations:
                    if (not (metric_thresholds[0] < all_workerFilter_metrics[worker][metric][annotation] < metric_thresholds[1])):
                        spammer = False

                if spammer == True:
                    if worker not in spammers:
                        spammers.append(worker)

        for worker in all_workerFilter_metrics:
            for metric in all_workerFilter_metrics[worker]:
                avg = 0
                count = 0
                for ann in all_workerFilter_metrics[worker][metric]:
                    avg += all_workerFilter_metrics[worker][metric][ann]
                    count += 1
                all_workerFilter_metrics[worker][metric]['avg'] = avg/(1.0 * count)

        metrics['workers'] = {}
        metrics['workers']['withFilter'] = all_workerFilter_metrics
        metrics['aggWorker'] = {}

        for metric in worker_mean_metrics:
            count = 0
            avg = 0
            for ann in worker_mean_metrics[metric]:
                count += 1
                avg += worker_mean_metrics[metric][ann]
            worker_mean_metrics[metric]['avg'] = avg/(1.0 * count)

        metrics['aggWorker']['mean'] = worker_mean_metrics

        for metric in worker_stddev_measure:
            avg = 0
            count = 0;
            for ann in worker_stddev_measure[metric]:
                count += 1;
                avg += worker_stddev_measure[metric][ann]
            worker_stddev_measure[metric]['avg'] = avg/(1.0 * count)

        print 3
        metrics['aggWorker']['stddev'] = worker_stddev_measure

        # if self.template_id == 'entity/text/medical/FactSpan/Factor_Span/0':
        #     spammers = self.get_FactSpan_spam(all_workerFilter_metrics);
        # elif self.template_id == 'entity/text/medical/RelDir/Relation_Direction/0':
        #     spammers = self.get_RelDir_spam(all_workerFilter_metrics);
        # else:
        #     spammers = self.get_RelExt_spam(all_workerFilter_metrics);

        #print(spammers)
        metrics['spammers'] = {}
        metrics['spammers']['count'] = len(spammers)
        metrics['spammers']['list'] = spammers
        #print(all_workers_metrics.keys())

        #get all the annotations of the spammers
        filtered_annotations = []
        for job_id in self.jobs_dict.keys():
            for spammer in spammers:
                api_param = urllib.urlencode({'field[job_id]': job_id,'limit':10000,
                                              'field[crowdAgent_id]': spammer,
                                              'field[documentType]': 'annotation',
                                              'only[]': '_id'})
                api_call = urllib2.urlopen(config.server + "?" + api_param)
                response = json.JSONDecoder().decode(api_call.read())
                for res in response:
                    filtered_annotations.append(res['_id']);

        metrics['filteredAnnotations'] = {}
        metrics['filteredAnnotations']['count'] = len(filtered_annotations)
        metrics['filteredAnnotations']['list'] = filtered_annotations
        #create a new job dic with newly computed spammers
        all_units_metrics_ws = {}
        all_units_ws = []
        all_units_vec_ws = {}
        #compute average and compare
        for unit_id in unit_cluster:
         #   print(unit_id)
            job_dict = {}
            for job_id in unit_cluster[unit_id]:
                job_dict[job_id] = spammers
            unit_cluster[unit_id] = job_dict
            unit = Unit(unit_id, unit_cluster[unit_id], True)
            all_units_vec_ws[unit_id] = unit.get_unit_vector()
            all_units_ws.append(unit)
         #!save here the without spam result for units
            unit_result = unit.get_metrics(UnitMetricsEnum.__members__.values())
            all_units_metrics_ws[unit_id] = unit_result

        metrics['pivotTables']['units']['withoutSpam'] = {}
        for unit in all_units_ws:
            metrics['pivotTables']['units']['withoutSpam'][unit.sentence_id] = unit.get_cosine_vector()

        print 4

        mean_metrics_ws = self.__compute_mean_measure(all_units_metrics_ws)
        stddev_measure_ws = self.__compute_stddev_measure(all_units_metrics_ws, mean_metrics)


        for unit in all_units_metrics_ws:
            for metric in all_units_metrics_ws[unit]:
                avg = 0
                count  = 0
                for ann in all_units_metrics_ws[unit][metric]:
                    count += 1
                    avg += all_units_metrics_ws[unit][metric][ann]
                all_units_metrics_ws[unit][metric]['avg'] = avg/(1.0 * count)

        metrics['units']['withoutSpam'] = all_units_metrics_ws


        metrics['aggUnits'] = {}
        for metric in mean_metrics_ws:
            avg = 0
            count  = 0
            for ann in mean_metrics_ws[metric]:
                count += 1
                avg += mean_metrics_ws[metric][ann]
            mean_metrics_ws[metric]['avg'] = avg/(1.0 * count)

        metrics['aggUnits']['mean'] = mean_metrics_ws

        for metric in stddev_measure_ws:
            count = 0
            avg = 0
            for ann in stddev_measure_ws[metric]:
                count +=1
                avg += stddev_measure_ws[metric][ann]
            stddev_measure_ws[metric]['avg'] = avg/(1.0 * count)

        metrics['aggUnits']['stddev'] = stddev_measure_ws

        metrics['workerThresholds'] = self.default_thresholds['workerThresholds']
        metrics['unitThresholds'] = self.default_thresholds['unitThresholds']

        for worker in all_workers_metrics:
            for metric in all_workers_metrics[worker]:
                avg = 0
                count = 0
                for ann in all_workers_metrics[worker][metric]:
                    avg += all_workers_metrics[worker][metric][ann]
                    count += 1
                all_workers_metrics[worker][metric]['avg'] = avg/(1.0 * count)

        print 5
        metrics['workers']['withoutFilter'] = all_workers_metrics

        metrics['pivotTables']['annotations']['withoutSpam'] = {}
        ann_keys = all_units_ws[0].get_unit_vector().keys()
        annotation_metrics_ws = {}
        for ann_key in ann_keys:
            annotation = Annotation(all_units_ws, ann_key)
            annotation_metrics_ws[ann_key] = annotation.get_metrics(AnnotationMetricsEnum.__members__.values())
            metrics['pivotTables']['annotations']['withoutSpam'][ann_key] = annotation.get_rel_similarity_dict()

        metrics['annotations']['withoutSpam'] = annotation_metrics_ws

        results = {}
        results['metrics'] = metrics
        results['results'] = {}
        results['results']['withoutSpam'] = all_units_vec_ws
        #results['results']['withSpam'] = all_units_vec


        encoder.c_make_encoder = None
        metrics_json = encoder.JSONEncoder().encode(results)

        #get the unfiltered sentences
        #print(std_dev_metrics)
        return metrics_json