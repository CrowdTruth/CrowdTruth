<?php

//use mongo\text\sentence;
use MongoDB\Entity;

class QuestionTemplate extends Entity {

	
	protected $fillable = array('question', 'replace', 'format', 'documentType', 'domain');
	protected $documentType = 'questiontemplate';
	protected $format = 'text';
	protected $domain = 'medical';

    public static function boot ()
    {
        parent::boot();

        static::saving(function ( $content )
        {
            dd('works');
        });
    }


}
















?>