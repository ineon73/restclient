<?php
	ini_set('error_reporting', E_ALL & ~E_NOTICE);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);

	require_once 'core/models/base/config.class.php';

	require_once 'core/models/CyrillicNameDictonaryParser.php';

	require_once 'core/models/PhoneValidator.php';
	require_once 'core/models/EmailValidator.php';

	require_once 'core/models/SalutationProcessor.php';
	require_once 'core/models/EmailValidator.php';
	

	
	require_once 'core/models/CombinedLatinNameParser.php';
	require_once 'core/models/ConditionCyrLatinNameParser.php';
	
	use \App\Processors\Config as Config,
	\App\Processors\cyrillicNameDictonaryParser as cyrillicNameDictonaryParser,
	\App\Processors\LatinNameDictionaryParser as latinNameDictionaryParser, 
	\App\Processors\PhoneValidator as PhoneValidator, 
	\App\Processors\EmailValidator as EmailValidator, 
	\App\Processors\SalutationProcessor as SalutationProcessor, 
	\App\Processors\LatinNameYandexParser as LatinNameYandexParser;
	
	use \App\Processors\CombinedLatinNameParser as CombinedLatinNameParser;
	use \App\Processors\ConditionCyrLatinNameParser as ConditionCyrLatinNameParser;
	
	$fullname 	= new cyrillicNameDictonaryParser();

	$phone 		= new PhoneValidator();
	$email 		= new EmailValidator();	

	
	print_r('DEBUG:'.CONFIG::DEBUG);
	

	if (CONFIG::DEBUG)		var_dump($_REQUEST);
	
	$request = $_REQUEST;
	
	$init_quality = qualify($request);
	
	echo 'iq'.$init_quality;
	
	$cclnp = new ConditionCyrLatinNameParser(['INPUT_FIELD'=>'fullname']);
	
	$clnp = new CombinedLatinNameParser(['INPUT_FIELD'=>'cardholder']);
	
	$combined_result = $cclnp->process(['fullname'=>$request['fullname']]);
	
	$combined_result_quality = (qualify($combined_result));
	
	
	if (CONFIG::DEBUG) {
        	print_r('result1');
        	
        	echo 'result of first parser inside main scr:<pre>';
        
        	echo 'now output:';
        	print_r($combined_result	);
        	echo '</pre>';
        	
        	print_r($combined_result_quality);
        	print_r('qual');
    	}	
	
    	$latin_result = $clnp->process(['cardholder'=>$request['cardholder']]);
	
    	$latin_result_quality = (qualify($latin_result));
    	
    	if (CONFIG::DEBUG) {
    	    print_r('result1');
    	    
    	    echo 'result of first parser inside main scr:<pre>';
    	    
    	    echo 'now output:';
    	    print_r($latin_result	);
    	    echo '</pre>';
    	    
    	    print_r($latin_result_quality);
    	    print_r('qual');
    	}
    	
    	
    	
    	
	
	function qualify($nameElements) 
	{
	    
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
	
	print_r($result2);
