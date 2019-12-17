<?php
	namespace App\Processors;
	use App\Processors\Support\Config;
	class CombinedLatinNameParser {
	    
		public function __construct($arParams = null) {
		    
		    require_once 'LatinNameDictionaryParser.php';
		    require_once 'LatinNameYandexParser.php';
		    $this->inputField = 'fullname';
		    
		    if (isset($arParams['INPUT_FIELD'])) {$this->inputField = $arParams['INPUT_FIELD'];} 
		    
		    $this->lDParser = new latinNameDictionaryParser();
		    $this->lYParser = new latinNameYandexParser(); 
		    
		    echo 'CombinedLatinNameParser constructed'.PHP_EOL;
		    
		    ;}
		public function __destruct() {}

		public function process(array $request = null) {
		    
		    if (empty($request)||(!isset($request[$this->inputField]))) die ('CombinedLatinNameParser Error: empty element data in '.$this->inputField);
			
			$request[CONFIG::FULLNAME_INPUT_FIELD] = $request[$this->inputField];
			
			if (CONFIG::DEBUG)	{ echo 'latin dict parser input ';var_dump($request);}
			

			
			//$request;
			$response = ($this->lDParser -> process($request));
			
			
			
			if (CONFIG::DEBUG) {
			    echo 'result of dictonary parser';
			    print_r($response);
			}
			
			$response = ($this->lYParser -> process($response));
				
				
			return $response;
		}

	}