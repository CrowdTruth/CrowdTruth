from enum import Enum


class AnnotationMetricsEnum(Enum):
    annot_freq = "|S:R|"
    annot_clarity = "RClar"
    annot_prob = "P(R)"
    annot_top_prob = "P(R-Top)"
    # causal power
    annot_ambiguity = "max Rr->Rc"
    cond_prob = "max P(Rc|Rr)"
    top_ann_cond_prob = "max P(Rc|Rr-Top)"
    cond_prob_minus_rel_prob = "max P(Rc|Rr)-P(Rc)"
    mutual_info = "max I(Rc,Rd)"