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

    def __init__(self, jobs, template_id):
        # update to include multiple type of jobs..
        self.template_id = template_id
        self.default_thresholds = self.__get_default_thresholds()
        self.default_query = {}
        self.default_query_v1 = {}
        for jobPosition in range(len(jobs)):
            query_key = "match[job_id][" + str(jobPosition) + "]"
            self.default_query[query_key] = jobs[jobPosition]
            #remove this when APIs are integrated
            self.default_query_v1["field[job_id][" + str(jobPosition) + "]"] = jobs[jobPosition]

        self.default_query["match[documentType]"] = 'workerunit'
        self.default_query_v1["field[documentType]"] = 'workerunit'

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
            mean_measures[key] = {}
            metric_mean = sum(metric[key] for (unit,metric) in all_units_metrics.iteritems())
            mean_measures[key] = metric_mean/len(all_units_metrics)

        return mean_measures

    def __compute_stddev_measure(self, all_units_metrics, mean_metrics = None):
        if mean_metrics is None:
            mean_metrics = self.__compute_mean_measure(all_units_metrics)

        stddev_measures = {}
        metrics_keys = all_units_metrics[all_units_metrics.keys()[0]].keys()
        for key in metrics_keys:
            stddev_measures[key] = {}
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
        api_param = urllib.urlencode({'field[_id]': self.template_id,'limit':10000,
                              'only[]': 'content.defaultThresholds'})

        api_call = urllib2.urlopen(config.server + "v1/?" + api_param)
        response = json.JSONDecoder().decode(api_call.read())
        #print(config.server + "v1/?" + api_param)
        return response[0]['content']['defaultThresholds']

    def __get_units(self, add_query_list, annotations_to_filter):
        media_units = {}
        #get the list of media units
        queryCriteria = dict(self.default_query.items() + add_query_list.items())
        api_param = urllib.urlencode(queryCriteria)
        api_call = urllib2.urlopen(config.server + "analytics/mapreduceunit/?" + api_param)
        response = json.JSONDecoder().decode(api_call.read())
        #print(config.server + "analytics/mapreduceunit/?" + api_param)
        #create media unit objects
        for media_unit in response['results']:
            unit_vector = media_unit['value']['vector']
            for annotation in annotations_to_filter:
                unit_vector = Annotation.filter_annotation(annotation, annotations_to_filter[annotation], unit_vector)
            media_units[media_unit['_id']] = Unit(media_unit['_id'], unit_vector, media_unit['value']['count'])
        #print media_units

        return media_units

    def __get_workers(self, add_query_list, annotations_to_filter):
        workers = {}
        #get the list of workers
        queryCriteria = dict(self.default_query.items() + add_query_list.items())
        api_param = urllib.urlencode(queryCriteria)
        api_call = urllib2.urlopen(config.server + "analytics/mapreduceworker/?" + api_param)
        #print(config.server + "analytics/mapreduceworker/?" + api_param)
        response = json.JSONDecoder().decode(api_call.read())
        #print(response)
        #create worker objects
        for worker_info in response['results']:
            worker_id = worker_info['_id']
            unit_vectors = {}
            unit_freq = {}
            for unit in worker_info['value']['workerunits']:
                unit_id = unit['unit_id']
                unit_vector = unit['vector']
                for annotation in annotations_to_filter:
                    unit_vector = Annotation.filter_annotation(annotation, annotations_to_filter[annotation], unit_vector)
                unit_vectors[unit_id] = unit_vector
                unit_freq[unit_id] = unit['count']

            workers[worker_id] = Worker(worker_id, unit_vectors, unit_freq)
        #print(len(workers))
        return workers

    def process_units(self, units_metrics):
        results = {}

        for unit in units_metrics:
            position = unit.rfind('/')
            unit_id = unit[:position]
            key_id = unit[position+1:]
            if unit_id not in results:
                results[unit_id] = {}
            results[unit_id][key_id] = units_metrics[unit]

        return results

    def print_pivot_tables(self, unfiltered_workers, unfiltered_units, unfiltered_annotation, filtered_workers, filtered_units, filtered_annotation, results):
        results['metrics']['pivotTables']['workers'] = {}
        results['metrics']['pivotTables']['workers']['withFilter'] = {}
        results['metrics']['pivotTables']['workers']['withoutFilter'] = {}
        for worker in filtered_workers:
           results['metrics']['pivotTables']['workers']['withFilter'] [worker] = filtered_workers[worker].worker_agreement
           results['metrics']['pivotTables']['workers']['withoutFilter'] [worker] = unfiltered_workers[worker].worker_agreement

        results['metrics']['pivotTables']['units'] = {}
        results['metrics']['pivotTables']['units']['withSpam'] = {}
        results['metrics']['pivotTables']['units']['withoutSpam'] = {}
        for unit in filtered_units:
            position = unit.rfind('/')
            unit_id = unit[:position]
            key_id = unit[position+1:]
            if unit_id not in results['metrics']['pivotTables']['units']['withSpam']:
                results['metrics']['pivotTables']['units']['withSpam'][unit_id] = {}
                results['metrics']['pivotTables']['units']['withoutSpam'][unit_id] = {}
            results['metrics']['pivotTables']['units']['withSpam'][unit_id][key_id] = unfiltered_units[unit].cosine_vector
            results['metrics']['pivotTables']['units']['withoutSpam'][unit_id][key_id] = filtered_units[unit].cosine_vector

        results['metrics']['pivotTables']['annotations'] = {}
        results['metrics']['pivotTables']['annotations']['withSpam'] = {}
        results['metrics']['pivotTables']['annotations']['withoutSpam'] = {}

        if unfiltered_annotation is not None:
            results['metrics']['pivotTables']['annotations']['withSpam']['rel_similarity'] = unfiltered_annotation.rel_similarity_dict
            results['metrics']['pivotTables']['annotations']['withSpam']['cond_prob'] = unfiltered_annotation.cond_prob_dict
            results['metrics']['pivotTables']['annotations']['withSpam']['cond_prob_minus_rel_prob'] = unfiltered_annotation.cond_prob_minus_rel_prob_dict
            results['metrics']['pivotTables']['annotations']['withSpam']['top_ann_cond_prob'] = unfiltered_annotation.top_ann_cond_prob_dict
            results['metrics']['pivotTables']['annotations']['withSpam']['mutual_info_dict'] = unfiltered_annotation.mutual_info_dict

        if filtered_annotation is not None:
            results['metrics']['pivotTables']['annotations']['withoutSpam']['rel_similarity'] = filtered_annotation.rel_similarity_dict
            results['metrics']['pivotTables']['annotations']['withoutSpam']['cond_prob'] = filtered_annotation.cond_prob_dict
            results['metrics']['pivotTables']['annotations']['withoutSpam']['cond_prob_minus_rel_prob'] = filtered_annotation.cond_prob_minus_rel_prob_dict
            results['metrics']['pivotTables']['annotations']['withoutSpam']['top_ann_cond_prob'] = filtered_annotation.top_ann_cond_prob_dict
            results['metrics']['pivotTables']['annotations']['withoutSpam']['mutual_info_dict'] = filtered_annotation.mutual_info_dict

    def get_worker_units(self, selected_workers_to_filter):
        query = ""
        worker_units = []

        for worker in selected_workers_to_filter:
            query += '&field[crowdAgent_id][0]=' + worker + '&only[]=_id&limit=10000'
            
            api_param = urllib.urlencode(self.default_query_v1.items())
            api_call = urllib2.urlopen(config.server + "v1/?" + api_param + query)
            response = json.JSONDecoder().decode(api_call.read())
            for worker_unit in response:
                worker_units.append(worker_unit['_id']);
        return worker_units

    def create_metrics(self):

        #get the unfiltered units for this jobs
        unfiltered_units = self.__get_units({},{})
        #print(unfiltered_units)
        #compute all the metrics on the units
        unfiltered_units_metrics = {}
        for unit_id in unfiltered_units:
            unit = unfiltered_units[unit_id]
            #compute all the metrics, if it is computationally intense, define in the template the metrics to be computed
            unit_result = unit.get_metrics(UnitMetricsEnum.__members__.values())
            unfiltered_units_metrics[unit_id] = unit_result

        #get the unfiltered workers for this jobs
        unfiltered_workers = self.__get_workers({},{})
        #print(len(unfiltered_workers))
        #compute all the metrics on the workers
        unfiltered_workers_metrics = {}
        for worker_id in unfiltered_workers:
            worker = unfiltered_workers[worker_id]
            #compute all the metrics, if it is computationally intense, define in the template the metrics to be computed
            #print(WorkerMetricsEnum.__members__.values())
            worker_result = worker.get_metrics(unfiltered_workers, unfiltered_units, WorkerMetricsEnum.__members__.values())
            unfiltered_workers_metrics[worker_id] = worker_result
        #print(unfiltered_workers_metrics)
        #get the metrics for unfiltered annotations
        unfiltered_annotation_metrics = {}
        unfiltered_annotation = None
        if len(self.default_thresholds['annotationThresholds']) > 0:
            unfiltered_annotation = Annotation(unfiltered_units)
            unfiltered_annotation_metrics = unfiltered_annotation.get_metrics(AnnotationMetricsEnum.__members__.values())

        #get the mean metrics of units
        unfiltered_unit_mean_metrics = self.__compute_mean_measure(unfiltered_units_metrics)
        unfiltered_unit_stddev_measure = self.__compute_stddev_measure(unfiltered_units_metrics, unfiltered_unit_mean_metrics)
        unit_thresholds = self.__get_sentence_filter_threshold()

        unit_filter = Filters(unfiltered_unit_mean_metrics, unfiltered_unit_stddev_measure, unit_thresholds)

        #compute the filtered sentence according to each filter type
        units_to_filter = {}
        for filter_type in UnitFiltersEnum.__members__.values():
            filtered_list = []
            for unit in unfiltered_units:
                if unit_filter.is_filtered(unfiltered_units[unit], filter_type):
                    filtered_list.append(unit)
            units_to_filter[filter_type] = filtered_list

        #get the list of unclear units to be filtered
        selected_units_to_filter = []
        for filter_name in self.default_thresholds['unitThresholds']:
            filter_enum_type = getattr(UnitFiltersEnum, filter_name, None)
            selected_units_to_filter = list(set(selected_units_to_filter)|set(units_to_filter[filter_enum_type]))
        #print(selected_units_to_filter)

        unclear_units_query_list = {}
        iter = 0
        for unit in selected_units_to_filter:
            unclear_units_query_list['match[unit_id][<>][' + str(iter) + ']'] = unit
            iter += 1
        
        #print(unclear_units_query_list)
        #compute the average values for workers
        unfiltered_worker_mean_metrics = self.__compute_mean_measure(unfiltered_workers_metrics)
        unfiltered_worker_stddev_measure = self.__compute_stddev_measure(unfiltered_workers_metrics, unfiltered_worker_mean_metrics)

        #compute the list of unclear, ambiguous annotations
        selected_annotations_to_filter = {}
        for metric_name in self.default_thresholds['annotationThresholds']:
            metric_thresholds = self.default_thresholds['annotationThresholds'][metric_name]
            metric = getattr(AnnotationMetricsEnum, metric_name, None)
            filtered_set = unfiltered_annotation.get_filtered_set(metric, metric_thresholds)
            selected_annotations_to_filter[metric] = filtered_set

        filtered_workers = self.__get_workers(unclear_units_query_list, selected_annotations_to_filter)
        #print(filtered_workers)
        unfiltered_units_for_worker = self.__get_units({},selected_annotations_to_filter)
        #compute all the metrics on the units
        filtered_workers_metrics = {}
        for worker_id in unfiltered_workers:
            if worker_id not in filtered_workers:
                filtered_workers[worker_id] = Worker(worker_id, {}, {})
            worker = filtered_workers[worker_id]
            #compute all the metrics, if it is computationally intense, define in the template the metrics to be computed
            worker_result = worker.get_metrics(filtered_workers, unfiltered_units_for_worker, WorkerMetricsEnum.__members__.values())
            filtered_workers_metrics[worker_id] = worker_result

        filtered_worker_mean_measure = self.__compute_mean_measure(filtered_workers_metrics)
        filtered_worker_stddev_measure = self.__compute_stddev_measure(filtered_workers_metrics, filtered_worker_mean_measure)
        #print(filtered_workers_metrics)
        #get the list of low quality workers
        selected_workers_to_filter = []
        worker_cosine_array = []
    	worker_agreement_array = []
    	#novelty_irrelevant_sel_array = []
    	consistency_check_array = []
    	#novelty_selection_frequency_array = []
    	missed_instructions_array = []
    	none_event_type_frequency_array = []
    	event_type_frequency_array = []
        for metric_name in self.default_thresholds['workerThresholds'].keys():
        	metric_thresholds = self.default_thresholds['workerThresholds'][metric_name]
        	metric = getattr(WorkerMetricsEnum, metric_name, None)
        	#print(metric)
        	for worker in filtered_workers_metrics:
        		if metric_name == "worker_cosine":
        			if filtered_worker_mean_measure[metric] + filtered_worker_stddev_measure[metric] < filtered_workers_metrics[worker][metric]:
        				if worker not in worker_cosine_array:
        					#print(worker)
        					worker_cosine_array.append(worker)
        		elif metric_name == "avg_worker_agreement":
        			if filtered_workers_metrics[worker][metric] < filtered_worker_mean_measure[metric] - filtered_worker_stddev_measure[metric]:
        				if worker not in worker_agreement_array:
        					worker_agreement_array.append(worker)
        		elif metric_name == "consistency_check":
        			if 0 < filtered_workers_metrics[worker][metric]:
        				if worker not in consistency_check_array:
        					consistency_check_array.append(worker)
        		elif metric_name == "missed_instructions":
        			if 0 <= filtered_workers_metrics[worker][metric]:
        				if worker not in missed_instructions_array:
        					missed_instructions_array.append(worker)
        		elif metric_name == "none_event_type_frequency":
        			if 0.7 <= filtered_workers_metrics[worker][metric]:
        				if worker not in none_event_type_frequency_array:
        					none_event_type_frequency_array.append(worker)
        		elif metric_name == "event_type_frequency":
        			if 0.7 < filtered_workers_metrics[worker][metric]:
        				if worker not in event_type_frequency_array:
        					event_type_frequency_array.append(worker)

        for worker in filtered_workers_metrics:
        	if (worker in worker_cosine_array) and (worker in worker_agreement_array):
        		selected_workers_to_filter.append(worker)
        	elif (worker in worker_cosine_array) and (worker not in worker_agreement_array):
        		if (worker in consistency_check_array) or (worker in missed_instructions_array) or (worker in none_event_type_frequency_array):
        			if worker not in selected_workers_to_filter:
        				selected_workers_to_filter.append(worker)
        	elif (worker not in worker_cosine_array) and (worker in worker_agreement_array):
        		if (worker in consistency_check_array) or (worker in missed_instructions_array) or (worker in none_event_type_frequency_array):
        			if worker not in selected_workers_to_filter:
        				selected_workers_to_filter.append(worker)
        	elif (worker in none_event_type_frequency_array) and (worker in event_type_frequency_array):
        		if worker not in selected_workers_to_filter:
        			selected_workers_to_filter.append(worker)

       	#print(selected_workers_to_filters)
       	spam_worker_query_list = {}
        iter = 0
        for worker in selected_workers_to_filter:
            spam_worker_query_list['match[crowdAgent_id][<>][' + str(iter) + ']'] = worker
            iter += 1

        filtered_units = self.__get_units(spam_worker_query_list, selected_annotations_to_filter)
        #compute all the metrics on the units
        filtered_units_metrics = {}

        for unit_id in unfiltered_units:
            if unit_id not in filtered_units:
                if len(self.default_thresholds['annotationThresholds']) > 0:
                    empty_vector = filtered_units[filtered_units.keys()[0]].get_unit_vector()
                else:
                    empty_vector = unfiltered_units[unit_id].get_unit_vector()

                for key in empty_vector:
                        empty_vector[key] = 0
                filtered_units[unit_id] = Unit(unit_id, empty_vector, 0)

            unit = filtered_units[unit_id]
            #compute all the metrics, if it is computationally intense, define in the template the metrics to be computed
            unit_result = unit.get_metrics(UnitMetricsEnum.__members__.values())
            filtered_units_metrics[unit_id] = unit_result


        filtered_annotation_metrics = {}
        filtered_annotation = None
        if len(self.default_thresholds['annotationThresholds']) > 0:
            #get the unfiltered units for this jobs
            filtered_units_annotations = self.__get_units(spam_worker_query_list, {})
            filtered_units_workers = {}
            for unit_id in filtered_units_annotations:
                if unit_id not in selected_units_to_filter:
                    filtered_units_workers[unit_id] = filtered_units_annotations[unit_id]
            # should be done like this once the params are sent by value
            #unclear_units_workers_query_list = dict(unclear_units_query_list.items() + spam_worker_query_list.items())
            #filtered_units_workers = self.__get_units(unclear_units_workers_query_list,{})
            #get the metrics for unfiltered annotations
            filtered_annotation = Annotation(filtered_units_workers)
            filtered_annotation_metrics = filtered_annotation.get_metrics(AnnotationMetricsEnum.__members__.values())

        #get the mean metrics of units
        filtered_unit_mean_measure = self.__compute_mean_measure(filtered_units_metrics)
        filtered_unit_stddev_measure = self.__compute_stddev_measure(filtered_units_metrics, filtered_unit_mean_measure)

        pr_unfiltered_units_metrics = self.process_units(unfiltered_units_metrics)
        pr_filtered_units_metrics = self.process_units(filtered_units_metrics)

        results = {}
        results['metrics'] = {}

        results['metrics']['spammers'] = {}
        results['metrics']['spammers']['count'] = len(selected_workers_to_filter)
        results['metrics']['spammers']['list'] = selected_workers_to_filter

        selected_worker_units_to_filter = self.get_worker_units(selected_workers_to_filter)
        results['metrics']['filteredWorkerunits'] = {}
        results['metrics']['filteredWorkerunits']['count'] = len(selected_worker_units_to_filter)
        results['metrics']['filteredWorkerunits']['list'] = selected_worker_units_to_filter

        results['metrics']['filteredunits'] = {}
        results['metrics']['filteredunits']['count'] = len(selected_units_to_filter)
        results['metrics']['filteredunits']['list'] = selected_units_to_filter

        results['metrics']['filteredAnnotations'] = selected_annotations_to_filter

        results['metrics']['workerThresholds'] = self.default_thresholds['workerThresholds']
        results['metrics']['unitThresholds'] = self.default_thresholds['unitThresholds']
        results['metrics']['annotationThresholds'] = self.default_thresholds['annotationThresholds']

        results['metrics']['aggWorkers'] = {}
        results['metrics']['aggWorkers']['stddev'] = filtered_worker_stddev_measure
        results['metrics']['aggWorkers']['mean'] = filtered_worker_mean_measure

        results['metrics']['aggUnits'] = {}
        results['metrics']['aggUnits']['stddev'] = filtered_unit_stddev_measure
        results['metrics']['aggUnits']['mean'] = filtered_unit_mean_measure

        results['metrics']['workers'] = {}
        results['metrics']['workers']['withoutFilter'] = unfiltered_workers_metrics
        results['metrics']['workers']['withFilter'] = filtered_workers_metrics

        results['metrics']['annotations'] = {}
        results['metrics']['annotations']['withSpam'] = unfiltered_annotation_metrics
        results['metrics']['annotations']['withoutSpam'] = filtered_annotation_metrics

        results['metrics']['units'] = {}
        metrics_keys = unfiltered_units_metrics[unfiltered_units_metrics.keys()[0]].keys()

        results['metrics']['units']['withSpam'] = pr_unfiltered_units_metrics
        #compute the average value for unit metrics, remove when database schema is changed
        for unit_id in results['metrics']['units']['withSpam']:
            len_tasks = len(results['metrics']['units']['withSpam'][unit_id])
            results_metrics = {}
            for key in metrics_keys:
                metric_mean = sum(metric[key] for (unit_task, metric) in results['metrics']['units']['withSpam'][unit_id].iteritems())
                results_metrics[key] = metric_mean/float(len_tasks)
            results['metrics']['units']['withSpam'][unit_id]['avg'] = {}
            for key in results_metrics:
                results['metrics']['units']['withSpam'][unit_id]['avg'][key] = results_metrics[key]

        results['metrics']['units']['withoutSpam'] = pr_filtered_units_metrics
        #compute the average value for unit metrics, remove when database schema is changed
        for unit_id in results['metrics']['units']['withoutSpam']:
            len_tasks = len(results['metrics']['units']['withoutSpam'][unit_id])
            results_metrics = {}
            for key in metrics_keys:
                metric_mean = sum(metric[key] for (unit_task, metric) in results['metrics']['units']['withoutSpam'][unit_id].iteritems())
                results_metrics[key] = metric_mean/float(len_tasks)
            results['metrics']['units']['withoutSpam'][unit_id]['avg'] = {}
            for key in results_metrics:
                results['metrics']['units']['withoutSpam'][unit_id]['avg'][key] = results_metrics[key]

        results['metrics']['pivotTables'] = {}

        self.print_pivot_tables(unfiltered_workers, unfiltered_units, unfiltered_annotation, filtered_workers, filtered_units, filtered_annotation, results)

        results['results'] = {}
        results['results']['withoutSpam'] = {}
        units_without_spam = self.__get_units(spam_worker_query_list, {})

        for unit in unfiltered_units:
            if unit not in units_without_spam:
                empty_vector = unfiltered_units[unit].get_unit_vector()
                for key in empty_vector:
                    empty_vector[key] = 0;
                units_without_spam[unit] = Unit(unit, empty_vector, 0)
            position = unit.rfind('/')
            unit_id = unit[:position]
            key_id = unit[position+1:]
            if unit_id not in results['results']['withoutSpam']:
               results['results']['withoutSpam'][unit_id] = {}

            results['results']['withoutSpam'][unit_id][key_id] = units_without_spam[unit].get_unit_vector()

        encoder.c_make_encoder = None
        metrics_json = encoder.JSONEncoder().encode(results)
        return metrics_json

