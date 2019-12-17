<?php
	namespace App\Processors;

	use App\Processors\Support\Config;
	
	class TwoNameFieldsParser {
	    
		public function __construct($arParams = null) {

		    $this->inputField = 'fullname';
		    
		    if (isset($arParams['INPUT_FIELD_CYRLATIN'])) {$this->inputCyrLatField = $arParams['INPUT_FIELD_CYRLATIN'];} 
		    if (isset($arParams['INPUT_FIELD_LATIN'])) {$this->inputLatField = $arParams['INPUT_FIELD_LATIN'];} 
		    
		    $this->cclnp = new ConditionCyrLatinNameParser(['INPUT_FIELD'=>$this->inputCyrLatField]);
		    
		    $this->clnp = new CombinedLatinNameParser(['INPUT_FIELD'=>$this->inputLatField]);
		    
		    echo 'CombinedLatinNameParser constructed'.PHP_EOL;
		    
		    ;}
		    
		public function __destruct() {}

		public function process(array $request = null) {		

			print_r('DEBUG:'.CONFIG::DEBUG);
			
			if (CONFIG::DEBUG)		var_dump( $request);
			
			$init_quality = $this->qualify($request);
			
			if (CONFIG::DEBUG) echo 'init qual'.$init_quality;
			

			
			$combined_result = $this->cclnp->process([$this->inputCyrLatField=>$request[$this->inputCyrLatField]]);
			
			$combined_result_quality = ($this->qualify($combined_result));
			
			if (CONFIG::DEBUG) {
					print_r('result1');
					
					echo 'result of first parser inside main scr:<pre>';
				
					echo 'now output:';
					print_r($combined_result	);
					echo '</pre>';
					
					print_r($combined_result_quality);
					print_r('qual');
				}	
			
				$latin_result = $this->clnp->process([$this->inputLatField=>$request[$this->inputLatField]]);
			
				$latin_result_quality = ($this->qualify($latin_result));
				
				if (CONFIG::DEBUG) {
					print_r('result1');
					
					echo 'result of first parser inside main scr:<pre>';
					
					echo 'now output:';
					print_r($latin_result	);
					echo '</pre>';
					
					print_r($latin_result_quality);
					print_r('qual');
				}
			
			//print_r($result2);
			
			return $request;
		}
		
			function qualify($nameElements) 
				{
				    foreach (array(CONFIG::FIRSTNAME_VALID_OUTPUT_FIELD,CONFIG::FATHERNAME_VALID_OUTPUT_FIELD,CONFIG::GENDER_OUTPUT_FIELD,CONFIG::SURNAME_VALID_OUTPUT_FIELD) as $field) 
				    { 
				        if(empty($nameElements[$field])) 
				        {
				            $nameElements[$field]=0;
				        } 
				    }
				    
				    
					$qual = 0;
					if($nameElements[CONFIG::FIRSTNAME_VALID_OUTPUT_FIELD]==1) { 
						if($nameElements[CONFIG::FATHERNAME_VALID_OUTPUT_FIELD]==1) { $qual= 100;} 
						else { $qual = 50;} 
					}
					
					else if($nameElements[CONFIG::SURNAME_VALID_OUTPUT_FIELD]==1) { $qual = 25; }
					else if(strlen($nameElements[CONFIG::SURNAME_VALID_OUTPUT_FIELD].$nameElements[CONFIG::SURNAME_VALID_OUTPUT_FIELD].$nameElements[CONFIG::SURNAME_VALID_OUTPUT_FIELD])>1) {$qual = 1;}
					
					if (!empty($nameElements[CONFIG::GENDER_OUTPUT_FIELD])) {++$qual;}
					
					return $qual;
	;}

	}