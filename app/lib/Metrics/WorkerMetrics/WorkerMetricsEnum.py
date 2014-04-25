<<<<<<< HEAD
from enum import Enum


class WorkerMetricsEnum(Enum):
    no_of_units = "# Sents"
    worker_cosine = "Cos"
    #worker_agreement = "Agreement between workers"
    avg_worker_agreement = "Avg. Agreement"
    ann_per_unit = "annots/Sent"
=======
from enum import Enum


class WorkerMetricsEnum(Enum):
    no_of_units = "# Sents"
    worker_cosine = "Cos"
    #worker_agreement = "Agreement between workers"
    avg_worker_agreement = "Avg. Agreement"
    ann_per_unit = "annots/Sent"
>>>>>>> 373a996f28c09ac33cb9afd1d59c621e5cca2fd5
    #factor_selection_check = "# wrong selections"