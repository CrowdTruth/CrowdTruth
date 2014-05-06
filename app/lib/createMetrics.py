#!/usr/bin/python

import sys
from enum import Enum
import urllib
import json
import config
from Metrics.TaskMetrics.Task import *

#job_id_list = sys.argv[1].split(',');
# #template = 'entity/text/medical/questiontemplate/1'
# api_param = urllib.urlencode({'field[_id]': job_id,
#                                           'only[]':'jobConf_id'})
# api_call = urllib.urlopen(config.server + "?" + api_param)
# response = json.JSONDecoder().decode(api_call.read())
#
# api_param = urllib.urlencode({'field[_id]': response[0]['jobConf_id'],
#                                           'only[]':'template'})
# api_call = urllib.urlopen(config.server + "?" + api_param)
# response = json.JSONDecoder().decode(api_call.read())
# template = response[0]['template'];
# #s = Task(job, sys.argv[2])
template = "entity/text/medical/RelDir/Relation_Direction/0"
job_id = "entity/text/medical/job/10"
s = Task([job_id],template,0);
metrics = s.create_metrics()


print(metrics)
#res = s.get_metrics(WorkerMetricsEnum.no_of_sent)