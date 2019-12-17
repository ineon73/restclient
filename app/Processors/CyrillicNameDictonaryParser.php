<?php
	namespace App\Processors;
	use \App\Processors\Support\Dictionary as Dictionary;
	use \App\Processors\Support\DictionaryDB as DictionaryDB;
	
	use App\Processors\Support\Config;
	
	
	class CyrillicNameDictonaryParser {
		public function __construct($arParams = null) {
		    
		    $this -> errorField = 'PARSING_ERROR';
		    
		    if (isset($arParams['ERROR_FIELD'])) {
		        $this -> errorField = $arParams['ERROR_FIELD'];
		        ;}
		    
//			require_once 'base/config.class.php';
//			require_once 'base/db.class.php';

		    $value = config('database.mysql');
		    
		    print_r($value);
		        
			$this -> pdo = (new DictionaryDB)->connect();
		}

		public function __destruct() {
			unset($this -> pdo);
		}

		public function process(array $request = null) {
			if (empty($request))
				die ('Error: empty data!');

	//		require_once 'dictionary.class.php';
			$dictionary = new Dictionary();

			echo 'this is cyrilic dit parser';
	
			
			$output = $request;

			$fullname = explode(' ', $dictionary -> replaceLatinSymbols($dictionary -> safeString($request[CONFIG::FULLNAME_INPUT_FIELD])));

			/* Содержат ли данные минус-слова */
			if ($dictionary -> isMinusWord($fullname[0]) || $dictionary -> isMinusWord($fullname[1]) || $dictionary -> isMinusWord($fullname[2]))
			    $this->modifyElementError('MINUS_WORD',$output); 
			/* */

			/* Автоопределение введённых данных */
			$firstname 	= null;
			$surname 	= null;
			$fathername = null;

			if (!$firstname = $dictionary -> isFirstname($fullname[0])['firstname'])
				if (!$surname = $dictionary -> isSurname($fullname[0])['surname'])
					if (!$fathername = $dictionary -> isFathername($fullname[0])['fathername']);

			if (!$firstname)
				$firstname = $dictionary -> isFirstname($fullname[1])['firstname'];
			if (!$surname)
				$surname = $dictionary -> isSurname($fullname[1])['surname'];
			if (!$fathername)
				$fathername = $dictionary -> isFathername($fullname[1])['fathername'];

			if (!$firstname)
				$firstname = $dictionary -> isFirstname($fullname[2])['firstname'];
			if (!$surname)
				$surname = $dictionary -> isSurname($fullname[2])['surname'];
			if (!$fathername)
				$fathername = $dictionary -> isFathername($fullname[2])['fathername'];		
			/* */

			/* Вставка неправильного слова в массив */
			$maskResponse = $this -> getMaskResponse([$firstname, $surname, $fathername], [$fullname[0], $fullname[1], $fullname[2]]);
            
			if (CONFIG::DEBUG)  echo 'cyr parser mask resp:';print_r($maskResponse);
			
			
			$mask 		= $maskResponse['mask'];
			$firstname 	= $maskResponse['firstname'];
			$surname 	= $maskResponse['surname'];
			$fathername = $maskResponse['fathername'];
			/* */

			/* Parse */
			$gender 			= null;
			$firstNameValid 	= 0;
			$surnameValid 		= 0;
			$fathernameValid 	= 0;

				/* Проверка на имя */
			if ($firstname) {
				$isFirstname = $dictionary -> isFirstname($firstname);
				if (CONFIG::DEBUG)  echo 'cyr parser isfirstname resp:';print_r($isFirstname);
				if ($isFirstname !== false) {
				    
				    if(isset($isFirstname['replacement'])) { $firstname = $isFirstname['replacement'];}
				        
					if ($isFirstname['gender'] !== '') {
						/* Проверка на согласованность гендера */
						if (is_string($gender) && $gender != $isFirstname['gender']) {
							$this->modifyElementError('GENDER_ERROR',$output);
						} else
							$gender = $isFirstname['gender'];
						/* */
					}

					$firstNameValid = 1;
				}
			}
				/* */

				/* Проверка на фамилию */
			if ($surname) {
				$isSurname = $dictionary -> isSurname($surname);
				if ($isSurname !== false) {
				    
				    if(isset($isSurname['replacement'])) { $surname = $isSurname['replacement'];}
					
					if ($isSurname['gender'] !== '') {
						/* Проверка на согласованность гендера */
						if (is_string($gender) && $gender != $isSurname['gender']) {
						    $this->modifyElementError('GENDER_ERROR',$output);
						} else
							$gender = $isSurname['gender'];
						/* */
					}

					$surnameValid = 1;
				}
			}
				/* */

				/* Проверка на отчество */
			if ($fathername) {
				$isFathername = $dictionary -> isFathername($fathername);
				
				if ($isFathername !== false) {
				    
				    if(isset($isFathername['replacement'])) { $fathername = $isFathername['replacement'];}
				    
					if ($isFathername['gender'] !== '') {
						/* Проверка на согласованность гендера */
						if (is_string($gender) && $gender != $isFathername['gender']) {
						    $this->modifyElementError('GENDER_ERROR',$output);
						} else
							$gender = $isFathername['gender'];
						/* */
					}

					$fathernameValid = 1;
				}
			}
				/* */
			/* */

			/* Заменить сокращённые имена на полные */
//			$isReplacement = $dictionary -> isReplacement($firstname);
			
			/* */

			$cdparser_output = [
				CONFIG::SURNAME_OUTPUT_FIELD 			=> $surname,
				CONFIG::FIRSTNAME_OUTPUT_FIELD			=> $firstname,
				CONFIG::FATHERNAME_OUTPUT_FIELD			=> $fathername,

				CONFIG::GENDER_OUTPUT_FIELD				=> $gender,

				CONFIG::SURNAME_VALID_OUTPUT_FIELD		=> $surnameValid,
				CONFIG::FIRSTNAME_VALID_OUTPUT_FIELD	=> $firstNameValid,
				CONFIG::FATHERNAME_VALID_OUTPUT_FIELD	=> $fathernameValid
			];

			$output = array_merge($output,$cdparser_output);
			
			
			/* Unset for column of names */
			if (is_null($output['SURNAME']))
				unset($output['SURNAME']);

			if (is_null($output['FIRSTNAME']))
				unset($output['FIRSTNAME']);

			if (is_null($output['FATHERNAME']))
				unset($output['FATHERNAME']);
			/* */

			unset($dictionary);

			return $output;
		}

		private function getMaskResponse(array $dataVariables, array $dataRequest) {
			$mask = null;

			$firstname 	= $dataVariables[0];
			$surname 	= $dataVariables[1];
			$fathername = $dataVariables[2];

			$fullname[0] = $dataRequest[0];
			$fullname[1] = $dataRequest[1];
			$fullname[2] = $dataRequest[2];

				/* X NAME FATHERNAME */
			if (((!$surname && isset($fullname[0])) && ($firstname && isset($fullname[1])) && ($fathername && isset($fullname[2])))) {
				if ((mb_strtolower($firstname) == mb_strtolower(trim($fullname[1]))) && (mb_strtolower($fathername) == mb_strtolower(trim($fullname[2])))) {
					$surname = $fullname[0];

					$mask = 1;
				}
			}
				/* */

				/* NAME X FATHERNAME */
			if (($firstname && isset($fullname[0])) && (!$surname && isset($fullname[1])) && ($fathername && isset($fullname[2]))) {
				if (mb_strtolower($surname) == mb_strtolower(trim($fullname[1]))) {
					$surname = $fullname[1];

					$mask = 2;
				}
			}
				/* */

				/* NAME FATHERNAME X */
			if (($firstname && isset($fullname[0])) && ($fathername && isset($fullname[1])) && (!$surname && isset($fullname[2]))) {
				if ((mb_strtolower($firstname) == mb_strtolower(trim($fullname[0]))) && (mb_strtolower($fathername) == mb_strtolower(trim($fullname[1])))) {
					$surname = $fullname[2];

					$mask = 3;
				}
			}
				/* */

				/* X X FATHERNAME */
			if ((!$surname && isset($fullname[0])) && (!$firstname && isset($fullname[1])) && $fathername) {
				if (mb_strtolower($fathername) != mb_strtolower(trim($fullname[0])) && mb_strtolower($fathername) != mb_strtolower(trim($fullname[1]))) {
					$surname 	= $fullname[0];
					$firstname 	= $fullname[1];

					$mask = 4;
				}
			}
				/* */

				/* X FATHERNAME X */
			if ((!$firstname && isset($fullname[0])) && $fathername && (!$surname && isset($fullname[2]))) {
				$firstname 	= $fullname[0];
				$surname 	= $fullname[2];

				$mask = 5;
			}
				/* */

				/* X NAME X */
			if ((!$surname && isset($fullname[0])) && $firstname && (!$fathername && isset($fullname[2]))) {
				$surname 	= $fullname[0];
				$fathername = $fullname[2];

				$mask = 6;
			}
				/* */

				/* NAME X X */
			if ($firstname && (!$surname && isset($fullname[1])) && (!$fathername && isset($fullname[2]))) {
				$surname 	= $fullname[1];
				$fathername = $fullname[2];

				$mask = 7;
			}
				/* */

				/* X X X */
			if ((!$firstname && isset($fullname[0])) && (!$surname && isset($fullname[1])) && (!$fathername && isset($fullname[2]))) {
				$firstname 	= $fullname[0];
				$surname 	= $fullname[1];
				$fathername = $fullname[2];

				$mask = 8;
			}
				/* */

				/* NAME X */
			if (($firstname && isset($fullname[0]) && (!$surname && isset($fullname[1])) && !isset($fullname[2]))) {
				if (mb_strtolower($surname) == mb_strtolower(trim($fullname[1]))) {
					$surname = $fullname[1];

					$mask = 9;
				}
			}
				/* */

				/* X NAME */
			if ((!$surname && isset($fullname[0])) && ($firstname && isset($fullname[1])) && !isset($fullname[2])) {
				if (mb_strtolower($surname) == mb_strtolower(trim($fullname[0])) || mb_strtolower($firstname) == mb_strtolower(trim($fullname[1]))) {
					$surname = $fullname[0];

					$mask = 10;
				}
			}
				/* */

				/* X SURNAME */
			if ((!$firstname && isset($fullname[0])) && $surname && !isset($fullname[2])) {
				if (mb_strtolower($surname) != mb_strtolower(trim($fullname[0]))) {
					$firstname = $fullname[0];

					$mask = 11;
				}
			}
				/* */

				/* SURNAME X */
			if ($surname && (!$firstname && isset($fullname[1])) && !isset($fullname[2])) {
				$firstname = $fullname[1];

				$mask = 12;
			}
				/* */

				/* X */
			if ((!$firstname && !$surname && !$fathername) && (isset($fullname[0]) && !isset($fullname[1]) && !isset($fullname[2]))) {
				$surname = $fullname[0];

				$mask = 13;
			}
				/* */

				/* SURNAME NAME X */
			if (($surname && isset($fullname[0])) && ($firstname && isset($fullname[1])) && (!$fathername && isset($fullname[2]))) {
				if (mb_strtolower($firstname) == mb_strtolower(trim($fullname[1])) && mb_strtolower($fathername) == mb_strtolower(trim($fullname[2]))) {
					$fathername = $fullname[2];
					
					$mask = 14;
				}
			}
				/* */

				/* NAME X SURNAME */
			if (($firstname && isset($fullname[0])) && (!$fathername && isset($fullname[1])) && ($surname && isset($fullname[2]))) {
				if (mb_strtolower($fathername) == (trim($fullname[1]))) {
					$fathername = $fullname[1];

					$mask = 15;
				}
			}
				/* */

				/* SURNAME X FATHERNAME */
			if (($surname && isset($fullname[0])) && (!$firstname && isset($fullname[1])) && ($fathername && isset($fullname[2]))) {
				if (mb_strtolower($firstname) == mb_strtolower(trim($fullname[1]))) {
					$firstname = $fullname[1];

					$mask = 16;
				}
			}
				/* */

				/* SURNAME X X */
			if (($surname && isset($fullname[0])) && (!$firstname && isset($fullname[1])) && (!$fathername && isset($fullname[2]))) {
				$firstname 	= $fullname[1];
				$fathername = $fullname[2];

				$mask = 17;
			}
				/* */

				/* X X */
			if ((!$firstname && isset($fullname[0])) && (!$surname && isset($fullname[1])) && (!$fathername && !isset($fullname[2]))) {
				$surname 	= $fullname[0];
				$firstname 	= $fullname[1];

				$mask = 18;
			}
				/* */

				/* X FATHERNAME SURNAME */
			if ((!$firstname && isset($fullname[0])) && ($fathername && isset($fullname[1])) && ($surname && isset($fullname[2]))) {
				$firstname = $fullname[0];

				$mask = 19;
			}
				/* */

				/* X X SURNAME */
			if ((!$firstname && isset($fullname[0])) && (!$fathername && isset($fullname[1])) && ($surname && isset($fullname[2]))) {
				if (mb_strtolower($surname) != mb_strtolower(trim($fullname[0])) && mb_strtolower($surname) != mb_strtolower(trim($fullname[1]))) {
					$firstname 	= $fullname[0];
					$fathername = $fullname[1];

					$mask = 20;
				}
			}
				/* */

				/* NAME X */
			if (($firstname && isset($fullname[0])) && (!$surname && isset($fullname[1])) && (!$fathername && !isset($fullname[2]))) {
				if (mb_strtolower($firstname) == mb_strtolower(trim($fullname[0]))) {
					$firstname 	= $fullname[0];
					$surname 	= $fullname[1];

					$mask = 21;
				}
			}
				/* */

				/* NAME SURNAME X */
			if (($firstname && isset($fullname[0])) && ($surname && isset($fullname[1])) && (!$fathername && isset($fullname[2]))) {
				if ((mb_strtolower($firstname) == mb_strtolower(trim($fullname[0]))) && (mb_strtolower($surname) == mb_strtolower(trim($fullname[1])))) {
					$fathername = $fullname[2];

					$mask = 22;
				}
			}
				/* */

				/* X FATHERNAME */
			if ((!$firstname && isset($fullname[0])) && ($fathername && isset($fullname[1])) && (!$surname && !isset($fullname[2]))) {
				$firstname = $fullname[0];

				$mask = 23;
			}
				/* */

			return [
				'mask'			=> $mask,

				'firstname' 	=> $firstname,
				'surname'		=> $surname,
				'fathername'	=> $fathername
			];
		}
		
		function modifyElementError($newErrName,&$elementArray,$newErrVal = null) {
		    if (empty($elementArray[$this->errorField])) 
		    {
		          $elementArray[$this->errorField] = array($newErrName => $newErrVal);
		      }
		      else 
		      {

		          $elementArray[$this->errorField][$newErrName] = $newErrVal;
		      }
		      // return $elementArray; 
		}
		
	}