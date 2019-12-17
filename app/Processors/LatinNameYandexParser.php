<?php
	namespace App\Processors;
	use \App\Processors\Support\Yandex as Yandex;
	use \App\Processors\Support\Dictionary as Dictionary;
	use App\Processors\Support\Config;

	class latinNameYandexParser {
		public function __construct() {}
		public function __destruct() {}

		public function process(array $request) {
		    
		    
//			require_once 'yandex.class.php';
//			require_once 'dictionary.class.php';
			$yandex		= new Yandex();
			$dictionary = new Dictionary();

			$output = $request;
			
			
			//if (isset($output['PARSING_ERROR']))
			//	$output['PARSING_ERROR'] = $output['PARSING_ERROR'];
			//else
			//	$output['PARSING_ERROR'] = [];

			$firstname 	='';      if(isset($request[CONFIG::FIRSTNAME_OUTPUT_FIELD])) $firstname 	=$request[CONFIG::FIRSTNAME_OUTPUT_FIELD];
			$surname 	='';      if(isset($request[CONFIG::SURNAME_OUTPUT_FIELD]))  $surname 	=$request[CONFIG::SURNAME_OUTPUT_FIELD];
			$gender 		='';  if(isset($request[CONFIG::GENDER_OUTPUT_FIELD]))  $gender = $request[CONFIG::GENDER_OUTPUT_FIELD];
			$firstnameValid ='' ; if(isset($request[CONFIG::FIRSTNAME_VALID_OUTPUT_FIELD]))  $firstnameValid = $request[CONFIG::FIRSTNAME_VALID_OUTPUT_FIELD];
			$surnameValid 	='';  if(isset($request[CONFIG::SURNAME_VALID_OUTPUT_FIELD]))  $surnameValid = $request[CONFIG::SURNAME_VALID_OUTPUT_FIELD];
			
			$parsing_error 	='';  if(isset($request[CONFIG::PARSING_ERROR_FIELD])) $parsing_error = $request[CONFIG::PARSING_ERROR_FIELD];
			
			echo '$firstname field'.CONFIG::FIRSTNAME_OUTPUT_FIELD; var_dump($firstname);var_dump($request);


			if (!$firstnameValid) {
				$firstname = $yandex -> translate($firstname)['result']['text'][0];

				$isFirstname = $dictionary -> isFirstname($firstname);
				if (CONFIG::DEBUG) {
					echo 'yandex firtsname translation res валидировано по словарю имя:';
					print_r($firstname);
					var_dump($isFirstname); 
				}
				/* Если в русском словаре что-то нашлось */
				if ($isFirstname !== false) {
					/* Несоответствие по гендеру */
					if ($isFirstname['gender'])
						$gender = $isFirstname['gender'];
					/* */
					$firstnameValid = 1;
				}
				
				
				/* */
			}
			/* */

			if (CONFIG::DEBUG) {
					echo 'yandex статус0:';
					var_dump($firstnameValid); 
				}
			
			/* Если фамилия не валидна - используем yandex и проверяем по русским словарям */
			if (!$surnameValid) {
				$surname = $yandex -> translate($surname)['result']['text'][0];

				
				$isSurname = $dictionary -> isSurname($surname);
				var_dump($isSurname);
				
				if (CONFIG::DEBUG) {
				    echo 'yandex translator surname res:';
				    print_r($surname);
				}
				
				/* Если в русском словаре что-то нашлось */
				if ($isSurname !== false) {
					/* Несоответствие по гендеру */
					if (is_string($gender) && $gender !== $isSurname['gender'])
						$output['PARSING_ERROR'] []= 'GENDER_ERROR';
					else
						$gender = $isSurname['gender'];
					/* */
					$surnameValid = 1;
				}
				/* */
			}
			/* */

			/* Если имя есть - первая буква в верхнем регистре */
			if ($firstname) {
				$output[CONFIG::FIRSTNAME_OUTPUT_FIELD] = ucfirst($firstname);

				if (($position = mb_strpos($firstname, '-')) !== false) {
					$output[CONFIG::FIRSTNAME_OUTPUT_FIELD] = mb_strtoupper(mb_substr($firstname, 0, 1)) . mb_strtolower(mb_substr($firstname, 1, $position)) . mb_strtoupper(mb_substr($firstname, $position + 1, 1)) . mb_strtolower(mb_substr($firstname, $position + 2));
				}
			}
			/* */

			/* Если фамилия есть - первая буква в верхнем регистре */
			if ($surname) {
				$output[CONFIG::SURNAME_OUTPUT_FIELD] = ucfirst($surname);

				if (($position = mb_strpos($surname, '-')) !== false) {
					//echo 'hi';
					$output[CONFIG::SURNAME_OUTPUT_FIELD] = mb_strtoupper(mb_substr($surname, 0, 1)) . mb_strtolower(mb_substr($surname, 1, $position)) . mb_strtoupper(mb_substr($surname, $position + 1, 1)) . mb_strtolower(mb_substr($surname, $position + 2));
					//echo mb_substr($surname, $position + 1, 1);
					//var_dump(ucfirst(substr($surname, $position + 1, mb_strlen($surname))));
				}
			}
			/* */

			// var_dump($isFirstname);
			// echo '<br>';
			// var_dump($isSurname);

			// var_dump($isTransFirstname);
			// echo '<br>';
			// var_dump($isTransSurname);

			if (CONFIG::DEBUG) {
					echo 'yandex inside script result:<pre>';
					var_dump($gender);
					var_dump($firstnameValid);
					echo 'now output:';
					print_r($output	);
					echo '</pre>';
				
				}
			
			$output = array_merge( $output , [
				CONFIG::FIRSTNAME_VALID_OUTPUT_FIELD 	=> $firstnameValid,
				CONFIG::SURNAME_VALID_OUTPUT_FIELD		=> $surnameValid,
				CONFIG::GENDER_OUTPUT_FIELD				=> $gender
			]);

			
		if (CONFIG::DEBUG) {
					echo 'yandex статус2: result after output mod';
					var_dump($firstnameValid);
					print_r($output	);
				
				}
				
			/* Удалить невалидные поля */
			if (!$firstnameValid)
				unset($output[CONFIG::FIRSTNAME_VALID_OUTPUT_FIELD]);

			if (!$surnameValid)
				unset($output[CONFIG::SURNAME_VALID_OUTPUT_FIELD]);
			/* */

		if (CONFIG::DEBUG) {
					echo 'yandex after validity and output3:';
					var_dump($firstnameValid);
					var_dump($output	);
				
				}
			
			
			/* Если GENDER_ERROR - удалить GENDER */
				if (isset($output['PARSING_ERROR'])&&in_array('GENDER_ERROR', $output['PARSING_ERROR']))
				unset($output[CONFIG::GENDER_OUTPUT_FIELD]);
			/* */

			return $output;
		}
	}