from enum import Enum


class WorkerMetricsEnum(Enum):
    no_of_units = "# Sents"
    worker_cosine = "Cos"
    avg_worker_agreement = "Avg. Agreement"
    ann_per_unit = "annots/Sent"
    event_ann_per_unit = "event annotations / tweet"
    missed_instructions = "worker missed instructions"
    none_event_type_frequency = "frequency of none event"
    no_annotated_words = "annotWords/Unit"
    factor_selection_check = "# wrong selections"
    novelty_irrelevant_sel = "# irrelevant selections"
    event_type_frequency = "max worker event type choice"
    consistency_check = "# inconsistant checks"
    novel_words_per_tweet = "highlighted novel words/sent"
    notnovel_words_per_tweet = "highlighted not novel words/sent"
    novelty_relevant_sel = "# relevant selections"
    novelty_t1morenovel_sel = "# t1 more novel selections"
    novelty_t1equalnovel_sel = "# t1 and t2 equal novel selections"
    novelty_t1lessnovel_sel = "# t1 less novel selections"
    novelty_selection_frequency = "max worker novelty choice"
