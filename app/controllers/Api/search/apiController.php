<?php
namespace Api\search;

use \BaseController as BaseController;
use \Input as Input;
use \URL as URL;
use \Response as Response;

use \Security\ProjectHandler as ProjectHandler;
use \Security\Permissions as Permissions;
use \Security\Roles as Roles;

use \Auth as Auth;
use \Repository as Repository;
use \Entity as Entity;
use \Activity as Activity;
use \SoftwareAgent as SoftwareAgent;
use \CrowdAgent as CrowdAgent;
use League\Csv\Writer as Writer;

class apiController extends BaseController {
	protected $repository;

	public function __construct(Repository $repository){
		$this->repository = $repository;
	}

	protected $operators = array(
		'=' , '<', '>', '<=', '>=', '<>', 'like'
	);
	
	public function getIndex()
	{
		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

		// Filter data for projects for which the authenticated user has permissions.
		if(Input::has('authkey')) {
			$user = \UserAgent::where('api_key', Input::get('authkey'))->first();
			if(is_null($user)) {
				return [ 'error' => 'Invalid auth key: '.Input::get('authkey') ];
			}
		} elseif(Auth::check()) {
			$user = Auth::user();
		} else {
			return [ 'error' => 'Authentication required. Please supply authkey.' ];
		}
		$projects = ProjectHandler::getUserProjects($user, Permissions::PROJECT_READ);
		$projectNames = array_column($projects, 'name');
		$collection = $collection->whereIn('project', $projectNames);

		if(Input::has('match'))
		{
			$collection = $this->processFields($collection);
		}

		$start = (int) Input::get('start', 0);
		$limit = (int) Input::get('limit', 100);
		$only = Input::get('only', array());

		if($orderBy = Input::get('orderBy')){
			foreach($orderBy as $sortingColumnName => $sortingDirection)
			{
				$collection = $collection->orderBy($sortingColumnName, $sortingDirection);
			}
		}

		$collection = $collection->paginate($limit, $only);
		$pagination = $collection->links()->render();
		$count = $collection->toArray();
		unset($count['data']);
		$documents = $collection->toArray()['data'];

		if(array_key_exists('tocsv', Input::all()))
		{
			set_time_limit(1200);
			$writer = new Writer(new \SplTempFileObject);
			$writer->setNullHandlingMode(Writer::NULL_AS_EMPTY);


			$headerDotted = array();

			foreach($documents as $line_index => $row)
			{
				unset($row['metrics'], $row['platformJobId'], $row['results'], $row['cache']);

				if(isset($row['parents']))
				{
					$row['wasDerivedFrom'] = implode(",", $row['parents']);
					unset($row['parents']);
				}

				foreach(array_dot($row) as $k => $v)
				{
					array_push($headerDotted, $k);
				}

			}

			$headerDotted = array_unique($headerDotted);
			natcasesort($headerDotted);

			$csvHeader = array_change_key_case(str_replace('.', '_', array_values($headerDotted)), CASE_LOWER);
			$writer->insertOne($csvHeader);

			foreach($documents as $line_index => $row)
			{
				if(isset($row['parents']))
				{
					$row['wasDerivedFrom'] = implode(",", $row['parents']);
					unset($row['parents']);
				}

				$row = array_dot($row);

				foreach($headerDotted as $column)
				{
					if(isset($row[$column]))
					{
						$csvRow[str_replace('.', '_', $column)] = $row[$column];
					}
					else
					{
						$csvRow[str_replace('.', '_', $column)] = "";
					}
				}

				$writer->insertOne($csvRow);
			}

			$writer->output(time() . '.csv');

			die;
		}

		return Response::json([
				"count" => $count,
				"pagination" => $pagination,
				"searchQuery" => Input::except('page'),
				"documents" => $documents
			]);
	}

	public function recur_ksort(&$array) {
		foreach ($array as &$value) {
			if (is_array($value)) $this->recur_ksort($value);
		}
		return ksort($array);
	}

	public function anyPost()
	{
		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

		if(isset(Input::get('match')['_id']))
		{
			$collection = $this->processFields($collection);

			if($data = Input::get('data'))
			{
				$data = json_decode($data, true);

				$collection->update($data, array('upsert' => true));
			}

			return $collection->get();
		}
	}

	public function anyPut()
	{
		$c = Input::get('collection', 'Entity');

		$collection = $this->repository->returnCollectionObjectFor($c);

		if(isset(Input::get('match')['_id']))
		{
			$collection = $this->processFields($collection);

			if($data = Input::get('data'))
			{
				$data = json_decode($data, true);

				$original = $collection->first();
				$originalArray = $original->toArray();

				if(array_key_exists(key($data), $originalArray))
				{
					$merged = array_replace_recursive($originalArray, $data);
					$original->update($merged, array('upsert' => true));
				}

				return Response::json($original);
			}
		}
	}

	protected function processFields($collection)
	{
		foreach(Input::get('match') as $field => $value)
		{
			if(is_array($value))
			{
				foreach($value as $operator => $subvalue)
				{
					if(is_int($operator) || $operator == "")
					{
						$collection = $collection->whereIn($field, array($subvalue));
						continue;
					}

					if(in_array($operator, $this->operators))
					{
						if(is_numeric($subvalue))
						{
							$subvalue = (double) $subvalue;
						}

						if($operator == "like")
						{
							$collection = $collection->where($field, $operator, "%" . preg_quote($subvalue, '/') . "%");
						}
						elseif($field == "created_at" || $field == "updated_at")
						{
							$date = new \DateTime($subvalue);

							if($operator == "<=")
							{
								$date->add(new \DateInterval('P1D'));
								$operator = "<";
							}

							$collection = $collection->where($field, $operator, $date);
						}
						else
						{
							$collection = $collection->where($field, $operator, $subvalue);
						}
					}
				}

			}
			else
			{
				if(is_numeric($value))
				{
					$value = (int) $value;
				}

				$collection = $collection->whereIn($field, array($value));
			}
		}

		return $collection;
	}
}
