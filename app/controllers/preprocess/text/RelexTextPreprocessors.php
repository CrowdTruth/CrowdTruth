<?php
namespace Preprocess\Relex;

use Preprocess\AbstractTextPreprocessor as AbstractTextPreprocessor;

// TODO: implement properly and document !
class RelationInSentencePreprocessor extends AbstractTextPreprocessor {
	function getName() {
		return 'Relation in sentence';
	}

	function getParameterJSFunctionName() {
		return 'relationInSentence';
	}

	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	function processItem($params, $data, $entities) {
		return 'IMPLEMENT!!!';
	}
}

class RelationOutsideTermsPreprocessor extends AbstractTextPreprocessor {
	function getName() {
		return 'Relation outside terms';
	}

	function getParameterJSFunctionName() {
		return 'relationOutsideTerms';
	}

	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	function processItem($params, $data, $entities) {
		return 'IMPLEMENT!!!';
	}
}

class RelationBetweenTermsPreprocessor extends AbstractTextPreprocessor {
	function getName() {
		return 'Relation between terms';
	}

	function getParameterJSFunctionName() {
		return 'relationBetweenTerms';
	}

	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	function processItem($params, $data, $entities) {
		return 'IMPLEMENT!!!';
	}
}

class SemicolonBetweenTermsPreprocessor extends AbstractTextPreprocessor {
	function getName() {
		return 'Semicolon between terms';
	}

	function getParameterJSFunctionName() {
		return 'hasSemicolon';
	}

	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	function processItem($params, $data, $entities) {
		return 'IMPLEMENT!!!';
	}
}

class CommaSeparatedTermsPreprocessor extends AbstractTextPreprocessor {
	function getName() {
		return 'Comma separated terms';
	}

	function getParameterJSFunctionName() {
		return 'hasComma';
	}

	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	function processItem($params, $data, $entities) {
		return 'IMPLEMENT!!!';
	}
}

class ParenthesisAroundTermsPreprocessor extends AbstractTextPreprocessor {
	function getName() {
		return 'Parenthesis around terms';
	}

	function getParameterJSFunctionName() {
		return 'hasParenthesis';
	}

	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	function processItem($params, $data, $entities) {
		return 'IMPLEMENT!!!';
	}
}

class OverlapingTermsPreprocessor extends AbstractTextPreprocessor {
	function getName() {
		return 'Overlapping terms';
	}

	function getParameterJSFunctionName() {
		return 'hasOverlappingTerms';
	}

	function getParameterJSFunction() {
		return '<script src="/js/preprocessors/text/relextextpreprocessors.js"></script>';
	}

	function processItem($params, $data, $entities) {
		return 'IMPLEMENT!!!';
	}
}
