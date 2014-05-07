from enum import Enum
import json
import encoder
from Metrics.WorkerMetrics.Worker import *
from Metrics.WorkerMetrics.WorkerMetricsEnum import *

# s = Worker(sentence_id="/crowdagent/cf/19935135",
#                  jobs_dict={"entity/text/medical/job/4": ["/crowdagent/cf/19935135","/crowdagent/amt/A1M46I0H8KNEEX"],
#                           "entity/text/medical/job/5": []}, filtered=False)
s = Worker("/crowdagent/cf/19935135",
                  ["entity/text/medical/job/4"
                           ], True)

print(s.get_unit_clusters())
print(s.get_ann_per_unit())
print(s.get_other_workers())
print(s.get_worker_cosine())
print(s.get_avg_worker_agreement())
encoder.c_make_encoder = None
f = encoder.JSONEncoder().encode({WorkerMetricsEnum.worker_cosine:s.get_unit_clusters()})
print(f)

#res = s.get_metrics(WorkerMetricsEnum.no_of_sent)
#print(s.get_no_annotators())