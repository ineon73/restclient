<?php
	namespace App\Processors;
	use \App\Processors\Support\Dictionary as Dictionary;
	use App\Processors\Support\Config;
	class latinNameDictionaryParser {
		public function __construct() {
		    
//		    require_once 'dictionary.class.php';
		    $this -> dictionary = new Dictionary();
		    
		}
		public function __destruct() {}

		public function process(array $request = null) {
			if (empty($request))	die ('Error: empty data!');
            
			if (CONFIG::DEBUG)	{	echo 'latin dict parser inside '; var_dump($request); echo CONFIG::FULLNAME_INPUT_FIELD.'/'.$request[CONFIG::FULLNAME_INPUT_FIELD]; }
			


			$output = $request;

			$cardholder = explode(' ', $this -> dictionary -> replaceCirillicSymbols($this -> dictionary -> safeString($request[CONFIG::FULLNAME_INPUT_FIELD])));
			# sergey isakov

			if (CONFIG::DEBUG)	{ echo 'after lDparser after split:';	var_dump($cardholder);}
			
			
			/* Автоопределение введённых данных */
			$firstname 	= null;
			$surname 	= null;

			$isTransFirstname 	= null;
			$isTransSurname 	= null;

			$cardholder[0] = $this -> dictionary -> replaceCirillicSymbols($this -> dictionary -> safeString($cardholder[0]));
			$cardholder[1] = $this -> dictionary -> replaceCirillicSymbols($this -> dictionary -> safeString($cardholder[1]));

			$isTransFirstname = $this -> dictionary -> isTransFirstname($cardholder[0]);
			if (($firstname = $isTransFirstname['ru']) === false) {
				$isTransFirstname = $this -> dictionary -> isTransFirstname($cardholder[1]);
				$firstname = $isTransFirstname['ru'];
			}

			$isTransSurname = $this -> dictionary -> isTransSurname($cardholder[0]);
			if (($surname = $isTransSurname['ru']) == false) {
				$isTransSurname = $this -> dictionary -> isTransSurname($cardholder[1]);
				$surname = $isTransSurname['ru'];
			}
			/* */

			/* Parse */
			$gender 			= null;
			$firstnameValid 	= 0;
			$surnameValid 		= 0;
			$fathernameValid 	= 0;

			echo '<br>';
			var_dump($isTransFirstname, $isTransSurname);
			echo '<br>';

				/* Проверка на имя */
			if ($firstname !== false) {
				if ($isTransFirstname !== false) {
					if ($isTransFirstname['gender'] !== '') {
						/* Проверка на согласованность гендера */
						if (is_string($gender) && $gender != $isTransFirstname['gender']) {
							//if (!in_array('GENDER_ERROR', $output['PARSING_ERROR']))
								$output['PARSING_ERROR'] []= 'GENDER_ERROR';
								//$output[CONFIG::CARD_HOLDER_GENDER_OUTPUT_FIELD] = null;
						} else
							$gender = $isTransFirstname['gender'];
						/* */
					}

					$firstnameValid = 1;
				}
			}
				/* */

				/* Проверка на фамилию */
			if ($surname !== false) {
				if ($isTransSurname !== false) {
					if ($isTransSurname['gender'] !== '') {
						/* Проверка на согласованность гендера */
						if (is_string($gender) && $gender != $isTransSurname['gender']) {
							//if (!in_array('GENDER_ERROR', $output['PARSING_ERROR']))
								$output['PARSING_ERROR'] []= 'GENDER_ERROR';
								//$output[CONFIG::CARD_HOLDER_GENDER_OUTPUT_FIELD] = null;
						} else
							$gender = $isTransSurname['gender'];
						/* */
					}

					$surnameValid = 1;
				}
			}
				/* */
			/* */


			/* Вставка неправильного слова в массив */
			$maskResponse = $this -> getMaskResponse([$firstname, $surname], [$cardholder[0], $cardholder[1]]);

			if (CONFIG::DEBUG)	{ echo 'lat dict parser mask:';	var_dump(	[$firstname, 
			$surname, $maskResponse]);}
			
			
			$mask 		= $maskResponse['mask'];
			$firstname 	= $maskResponse['firstname'];
			$surname 	= $maskResponse['surname'];
			/* */

			/* Если слово неправильное - вывести ошибку */
			if (!$firstname && !$surname)
				$output['PARSING_ERROR'] []= 'NAME_CHECK_ERROR';
			/* */

			echo '<br> latin dict vailidt';
			var_dump($firstnameValid, $surnameValid);
			echo '<br>';
			var_dump($cardholder[0], $cardholder[1]);
			echo '<br>';

			$ldparser_output = [
				
				CONFIG::SURNAME_OUTPUT_FIELD			=> $surname,
				CONFIG::FIRSTNAME_OUTPUT_FIELD			=> $firstname,

				CONFIG::GENDER_OUTPUT_FIELD				=> $gender,

				CONFIG::SURNAME_VALID_OUTPUT_FIELD		=> $surnameValid,
				CONFIG::FIRSTNAME_VALID_OUTPUT_FIELD	=> $firstnameValid
			
			];

			$output = array_merge($output,$ldparser_output);
			
			/* Если в словаре не нашлось слова - удалить VALID */
			if (!$surnameValid)
			    unset($output[CONFIG::SURNAME_VALID_OUTPUT_FIELD]);

			if (!$firstnameValid)
			    unset($output[CONFIG::FIRSTNAME_VALID_OUTPUT_FIELD]);
			/* */
			

			return $output;
		}

		private function getMaskResponse(array $dataVariables, array $dataRequest) {
			$mask = null;

			$firstname 	= $dataVariables[0];
			$surname 	= $dataVariables[1];

			$cardholder[0] = $dataRequest[0];
			$cardholder[1] = $dataRequest[1];
            
			if ($surname&&$firstname) {
			    $mask = 0 ;
			}
			elseif ((!$firstname)&&(!$surname)) {$firstname = $cardholder[0];$surname 	= $cardholder[1];$mask = 5;;} /* X X */
			elseif (empty($dataRequest)) {;}

			else if ($firstname&&(!$surname)) {
			    
			    if (empty ($cardholder[1])) {;}

			    else if ($cardholder[1]==$firstname) {$surname = $cardholder[0];$mask = 1;}			/* X NAME */

			    else {$surname = $cardholder[1];$mask = 2;} /* NAME X */
			
			} else { //$surname='smth'
			    
			    if (empty ($cardholder[1])) {;}
			    else if ($cardholder[1]==$surname) { $firstname = $cardholder[0];$mask = 3;}			/* X SURNAME */
			    else {   $firstname = $cardholder[1];$mask = 4;} /* SURNAME X */
			}

			return [
				'mask'			=> $mask,

				'firstname' 	=> $firstname,
				'surname'		=> $surname
			];
		}
	}