<?php

class ResourceController extends BaseController {

	protected $fileRepository;

	public function __construct(FileRepository $fileRepository){
		$this->fileRepository = $fileRepository;
	}

	public function index()
	{
		return Redirect::to('resource/text');
	}

	public function getCollection($collection){
		$this->fileRepository->getCollection($collection);
	}

	public function getCategory($collection, $category){
		$this->fileRepository->getCategory($collection, $category);
	}

	public function getDocument($collection, $category, $document){
		$this->fileRepository->getDocument($collection, $category, $document);		
	}
}