<?php
	namespace App\Processors;
	
	use App\Processors\Support\Config;
	
	use \App\Processors\CombinedLatinNameParser as CombinedLatinNameParser;
	use \App\Processors\CyrillicNameDictonaryParser as CyrillicNameDictonaryParser;
	use Exception;

	class ConditionCyrLatinNameParser {
	    
		public function __construct($arParams = null) {
		    
		    require_once 'CyrillicNameDictonaryParser.php';
		    require_once 'CombinedLatinNameParser.php';
		    $this->inputField = 'fullname';
		    
		    if (isset($arParams['INPUT_FIELD'])) {$this->inputField = $arParams['INPUT_FIELD'];} 
		    
		    $this->cyrDParser = new CyrillicNameDictonaryParser();
		    $this->clatYParser = new CombinedLatinNameParser(['INPUT_FIELD'=>$arParams['INPUT_FIELD']]); 
		    
		    echo 'Cond CyrLatinNameParser constructed';
		    
		    ;}
		    
		public function __destruct() {}

		public function process(array $request = null) {
		    
		    //echo 'cond pars inp field:'.$this->inputField.'/'.isset($request[$this->inputField]).'/'.$request['somefield'];
		    
		    if (empty($request)||(!isset($request[$this->inputField]))) throw new \Exception('Conditioned CyrLatinNameParser Error: empty element data in '.$this->inputField);
			
			$request[CONFIG::FULLNAME_INPUT_FIELD] = $request[$this->inputField];
			
			if (CONFIG::DEBUG)	{ echo 'combined cyr latin dict parser input ';var_dump($request);}
			
			$input_cyr_repl = preg_replace("/\p{Cyrillic}/u", '', $request[CONFIG::FULLNAME_INPUT_FIELD]);
			
			if (CONFIG::DEBUG) echo 'Lang ';
			
			if(mb_strlen($request[CONFIG::FULLNAME_INPUT_FIELD])>mb_strlen($input_cyr_repl)*2) 
			     {
			         if (CONFIG::DEBUG) echo 'Cyr';  
		             $response = $this->cyrDParser -> process ($request);
		             
			     } 
		         else
		         {
		             if (CONFIG::DEBUG) echo 'Lat';
		             $response = $this->clatYParser -> process ($request);
		         }
			
			if (!preg_match("/\p{Cyrillic}/u", $request[CONFIG::FULLNAME_INPUT_FIELD])) {;}
			
			//$request;
			//$response = ($this->lDParser -> process($request));
			

			
			if (CONFIG::DEBUG) {
			    print_r($response);
			}
			
			//$response = ($this->lYParser -> process($response));
				
				
			return $response;
		}

	}