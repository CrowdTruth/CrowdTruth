from enum import Enum


class UnitMetricsEnum(Enum):
    magnitude = "|V|"
    norm_magnitude = "norm |V|"
    norm_relation_magnitude = "norm |R|"
    norm_relation_magnitude_all = "norm-all |R|"
    max_relation_Cos = "Max Rel Cos"
    no_annotators = "|# annotators|"