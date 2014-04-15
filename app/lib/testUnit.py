<<<<<<< HEAD
from enum import Enum
import json
import encoder
from Metrics.UnitMetrics.Unit import *
from Metrics.UnitMetrics.UnitMetricsEnum import *

s = Unit(sentence_id="entity/text/medical/twrex-structured-sentence/132",
                 jobs_dict={"entity/text/medical/job/4": ["/crowdagent/cf/19935135","/crowdagent/amt/A1M46I0H8KNEEX"],
                          "entity/text/medical/job/5": []}, filtered=False)
# s = unit.Unit(sentence_id="entity/text/medical/twrex-structured-sentence/132",
#                   jobs_dict={"entity/text/medical/job/4"
#                            }, filtered=True)

print(s.get_unit_vector())
res = s.get_metrics([UnitMetricsEnum.magnitude, UnitMetricsEnum.norm_magnitude])
=======
from enum import Enum
import json
import encoder
from Metrics.UnitMetrics.Unit import *
from Metrics.UnitMetrics.UnitMetricsEnum import *

s = Unit(sentence_id="entity/text/medical/twrex-structured-sentence/132",
                 jobs_dict={"entity/text/medical/job/4": ["/crowdagent/cf/19935135","/crowdagent/amt/A1M46I0H8KNEEX"],
                          "entity/text/medical/job/5": []}, filtered=False)
# s = unit.Unit(sentence_id="entity/text/medical/twrex-structured-sentence/132",
#                   jobs_dict={"entity/text/medical/job/4"
#                            }, filtered=True)

print(s.get_unit_vector())
res = s.get_metrics([UnitMetricsEnum.magnitude, UnitMetricsEnum.norm_magnitude])
>>>>>>> 373a996f28c09ac33cb9afd1d59c621e5cca2fd5
print(s.get_no_annotators())