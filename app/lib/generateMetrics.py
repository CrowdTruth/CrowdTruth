#!/usr/bin/python

import sys
from enum import Enum
import json
from Metrics.TaskMetrics.Task import *

#sys.argv[1] - job id
#sys.argv[2] - template id
s = Task([sys.argv[1]], sys.argv[2])

metrics = s.create_metrics()

print(metrics)