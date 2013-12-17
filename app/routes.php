<?php

Route::get('/', function()
{
    return Redirect::to('home');
});

Route::get('home', 'PagesController@index');

Route::controller('files', 'FilesController');




App::bind('Models\File', function($app)
{
    return new Models\File;
});