<<<<<<< HEAD
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
=======
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
>>>>>>> 373a996f28c09ac33cb9afd1d59c621e5cca2fd5
#res = s.get_metrics(WorkerMetricsEnum.no_of_sent)