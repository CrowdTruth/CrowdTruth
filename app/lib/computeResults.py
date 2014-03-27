import urllib
import ConfigParser
import json
import config
import encoder

job_id = 'entity/text/medical/job/7'
sentence_clusters ={}
api_param = urllib.urlencode({'field[job_id]': job_id,
                          'field[documentType]': 'annotation'})
api_call = urllib.urlopen(config.server + "?" + api_param)
response = json.JSONDecoder().decode(api_call.read())
for annotation in response:
        sentence_id = annotation['unit_id']
        if sentence_id in sentence_clusters.keys():
            for key in sentence_clusters[sentence_id].keys():
                sentence_clusters[sentence_id][key] += annotation['dictionary'][key]
        else:
            sentence_clusters[sentence_id] = annotation['questionDictionary']

encoder.c_make_encoder = None
f = encoder.JSONEncoder().encode(sentence_clusters)
print(f)