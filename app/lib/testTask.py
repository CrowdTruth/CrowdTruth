from enum import Enum
import json
from Metrics.TaskMetrics.Task import *

s = Task(["entity/text/medical/job/7","entity/text/medical/job/6"],"entity/text/medical/questiontemplate/1")

metrics = s.create_metrics()

print(metrics)
#res = s.get_metrics(WorkerMetricsEnum.no_of_sent)