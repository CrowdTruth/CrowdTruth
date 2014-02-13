<?php

class SelectionController extends BaseController {

	public function getIndex()
	{
        return View::make('selection.index',  compact('documents'));
	}

	public function postAdd()
	{
		$repository = App::make('\MongoDB\Repository');

		if($document = $repository->find(Input::get('URI')))
		{
			Cart::add($document->_id, $document->title, 1, 1);
			return View::make('selection.inline_menu');
		}

		return false;
	}

	public function getAdd()
	{
		$repository = App::make('\MongoDB\Repository');

		if($document = $repository->find(Input::get('URI')))
		{
			Cart::add($document->_id, $document->title, 1, 1);
		}

		return Redirect::back();
	}

	public function postRemove()
	{
		try {
			Cart::remove(Input::get('selectionID'));
			return View::make('selection.inline_menu');
		} Catch(Exception $e)
		{
			return "No document at selectionID";
		}
	}

	public function getRemove()
	{
		try {
			Cart::remove(Input::get('selectionID'));
			return Redirect::back();
		} Catch(Exception $e)
		{
			return "No document at selectionID";
		}
	}	

	public function getView()
	{
		echo "<pre>";
		print_r(Cart::content());
		echo "</pre>";
	}

	public function postDestroy()
	{
		try{
			Cart::destroy();
			return View::make('selection.inline_menu');
		} Catch(Exception $e)
		{
			return "Error emptying cart";
		}		
	}

	public function getDestroy()
	{
		try{
			Cart::destroy();
			Session::flash('flashSuccess', 'Successfully emptied selection');
		} Catch(Exception $e){
			Session::flash('flashError', 'There was a problem emptying your selection');
		}		
		return Redirect::to('files/browse');
	}

	public function removeByURI($URI)
	{
		foreach(Cart::content() as $item){
			if($item['id'] == $URI){
				try{
					Cart::remove($item['rowid']);
					return true;
				} catch (Exception $e) {
					dd('Error removing rowID: ' . $item['rowID']);
				}
			}
		}
	}

	public function getInline(){
		return View::make('selection.inline_menu');
	}
}