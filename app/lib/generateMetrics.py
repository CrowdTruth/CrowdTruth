#!/usr/bin/python

import sys
from enum import Enum
import json
from Metrics.TaskMetrics.Task import *

job_list = sys.argv[1].split(', ')
#template = 'entity/text/medical/questiontemplate/1'
s = Task(job_list, sys.argv[2])
#s=Task(job_list,template);
metrics = s.create_metrics()

print(metrics)
#res = s.get_metrics(WorkerMetricsEnum.no_of_sent)