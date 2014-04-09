from enum import Enum


class WorkerMetricsEnum(Enum):
    no_of_units = "# Sents"
    worker_cosine = "Cos"
    #worker_agreement = "Agreement between workers"
    avg_worker_agreement = "Avg. Agreement"
    ann_per_unit = "annots/Sent"
    #factor_selection_check = "# wrong selections"