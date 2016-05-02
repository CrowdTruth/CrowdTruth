<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */

	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

	public function testHome()
	{
		$crawler = $this->client->request('GET', '/home');
		$this->assertTrue($this->client->getResponse()->isOk());
	}

	public function testMedia()
	{
		$this->be(UserAgent::where('_id','admin')->first());
		$this->client->request('GET', '/media/search');
		$this->assertTrue($this->client->getResponse()->isOk());
	}

	// Temp function to add template for metrics
	public function testTemplate()
	{
		$entity = new Entity;
		$json = '{
		    "_id" : "entity/text/medical/FactSpan/Factor_Span/0",
		    "format" : "text",
		    "domain" : "medical",
		    "documentType" : "template",
		    "0" : "type",
		    "1" : "todo",
		    "content" : {
		        "defaultThresholds" : {
		            "unitThresholds" : [],
		            "workerThresholds" : {
		                "avg_worker_agreement" : [ 
		                    -0.1, 
		                    0.01
		                ],
		                "worker_cosine" : [ 
		                    0.99, 
		                    1.1
		                ],
		                "duplicate_units" : [ 
		                    2.5, 
		                    100000
		                ],
		                "duplicate_keywords" : [ 
		                    5,
		                    100000
		                ]
		            },
		            "annotationThresholds" : {}
		        },
		        "replaceValues" : {
		            "relation_noPrefix" : {
		                "causedebugging" : "causes",
		                "contraindicatedebugging" : "contraindicates"
		            }
		        },
		        "question" : [ 
		            {
		                "component" : "label",
		                "value" : "{{description}}"
		            },
		            {
		                "component" : "label",
		                "class" : "well",
		                "value" : "{{sentence_text}}"
		            },
		            {
		                "id" : "direction",
		                "component" : "radio",
		                "editable" : true,
		                "index" : 0,
		                "label" : "Which of the following statements is EXPRESSED in the SENTENCE above?",
		                "options" : {
		                    "{{terms_first_text}} {{relation_noPrefix}} {{terms_second_text}}" : "{{terms_first_text}} {{relation_noPrefix}} {{terms_second_text}}",
		                    "{{terms_second_text}} {{relation_noPrefix}} {{terms_first_text}}" : "{{terms_second_text}} {{relation_noPrefix}} {{terms_first_text}}",
		                    "no_relation" : "-{{relation_noPrefix}}- is NOT EXPRESSED between the two PHRASES"
		                },
		                "required" : false,
		                "gold" : true
		            }
		        ]
		    },
		    "hash" : "f1320b0a410e9567b3de5dfabb6f15bb",
		    "activity_id" : "activity/templatebuilder/0",
		    "user_id" : "admin"
		}';
		$entity['attributes'] = json_decode($json,true);
		$entity->save();
	}



	protected $activity;

	public function testActivity()
	{
		$this->activity = new Activity;
		$this->activity->softwareAgent_id = "testActivity";
		$this->activity->save();
	}

	public function testResultImporter()
	{
		$this->be(UserAgent::where('_id','admin')->first());
		$file = new Symfony\Component\HttpFoundation\File\UploadedFile(storage_path('../tests/soundTest.csv'), 'soundTest.csv');
		$input = [
        'inputClass' => 'test',
        'outputClass' => 'test',
        'input-project' => 'Sounds',
        'input-type' => 'sound',
        'output-type' => 'sound'
		];

		$this->action('POST', 'MediaController@postImportresults', null, $input, ['file' => $file]);

		$this->assertTrue($this->client->getResponse()->isOk());
	}

}