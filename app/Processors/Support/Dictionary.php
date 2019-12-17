<?php
    namespace App\Processors\Support;
    
	class Dictionary {
		public function __construct() {
	//		require_once 'base/db.class.php';
		    $this -> pdo = (new DictionaryDB)->connect();
		}

		public function __destruct() {
			unset($this -> pdo);
		}

        public function isTransFirstname($word = null) {
            try {
                if (!$word)
                    return false;

                $sql = $this -> pdo -> prepare('SELECT * FROM dict_trans_firstname WHERE word = :word LIMIT 1');
                $sql -> execute([
                    'word' => $word
                ]);

                $response = $sql -> fetch(\PDO::FETCH_ASSOC);

                if (empty($response))
                    return false;

                return [
                    'ru'        => $response['translation'],
                    'en'        => $response['word'],
                    'gender'    => $response['gender']
                ];
            } catch (Exception $e) {
                die('errorGetTranslatationFirstnameFunction');
            }

            return false;
        }

        public function isTransSurname($word = null) {
            try {
                if (!$word)
                    return false;

                $sql = $this -> pdo -> prepare('SELECT * FROM dict_trans_surname WHERE word = :word LIMIT 1');
                $sql -> execute([
                    'word' => $word
                ]);

                $response = $sql -> fetch(\PDO::FETCH_ASSOC);

                if (empty($response))
                    return false;

                return [
                    'ru'        => $response['translation'],
                    'en'        => $response['word'],
                    'gender'    => $response['gender']
                ];
            } catch (Exception $e) {
                die('errorGetTranslatationSurnameFunction');
            }

            return false;
        }
		
		public function isMinusWord($word = null) {
            try {
                if (!$word)
                    return false;

                $sql = $this -> pdo -> prepare('SELECT * FROM dict_stopwords WHERE word = :word LIMIT 1');
                $sql -> execute([
                    'word' => $word
                ]);

                $response = $sql -> fetch(\PDO::FETCH_ASSOC);

                if (!empty($response))
	                return true;

	            return false;
            } catch (Exception $e) {
                die('errorIsMinusWordFunction');
            }

            return false;
		}

		public function isFirstname($name = null) {
            try {
            	$name = trim($name);

                if (!$name)
                    return false;

                $sql = $this -> pdo -> prepare('SELECT * FROM dict_firstname WHERE UF_DICT_NAME = :name LIMIT 1');
                $sql -> execute([
                    'name' => $name
                ]);

                $response = $sql -> fetch(\PDO::FETCH_ASSOC);

                if (!empty($response))
	                return [
	                	'firstname'	=> $response['UF_DICT_NAME'],
	                    'replacement' => $response['replacement'],
	                	'gender' 	=> $response['UF_DICT_GENDER']
	                ];

	            return false;
            } catch (Exception $e) {
                die('errorSearchName');
            }

            return false;
		}

		public function isSurname($surname = null) {
            try {
            	$surname = trim($surname);

                if (!$surname)
                    return false;

                $sql = $this -> pdo -> prepare('SELECT * FROM dict_surname WHERE UF_DICT_SURNAME = :surname LIMIT 1');
                $sql -> execute([
                    'surname' => $surname
                ]);

                $response = $sql -> fetch(\PDO::FETCH_ASSOC);

                if (!empty($response))
	                return [
	                	'surname'	=> $response['UF_DICT_SURNAME'],
	                	'gender' 	=> $response['UF_DICT_GENDER']
	                ];

	            return false;
            } catch (Exception $e) {
                die('errorSearchSurname');
            }

            return false;
		}

		public function isFathername($fathername = null) {
            try {
            	$fathername = trim($fathername);

                if (!$fathername)
                    return false;

                $sql = $this -> pdo -> prepare('SELECT * FROM dict_fathername WHERE UF_DICT_FATHERNAME = :fathername LIMIT 1');
                $sql -> execute([
                    'fathername' => $fathername
                ]);

                $response = $sql -> fetch(\PDO::FETCH_ASSOC);

                if (!empty($response))
	                return [
	                	'fathername'	=> $response['UF_DICT_FATHERNAME'],
	                	'gender' 		=> $response['UF_DICT_GENDER']
	                ];

	            return false;
            } catch (Exception $e) {
                die('errorSearchSurname');
            }

            return false;
		}

        public function isReplacement($name) {
            try {
                $name = trim($name);

                if (!$name)
                    return false;

                $sql = $this -> pdo -> prepare('SELECT replacement FROM dict_firstname WHERE UF_DICT_NAME = :name LIMIT 1');
                $sql -> execute([
                    'name' => $name
                ]);

                $response = $sql -> fetch(\PDO::FETCH_ASSOC);

                if (!empty($response))
                    return $response;

                return false;
            } catch (Exception $e) {
                die('errorIsReplacementForFirstname');
            }

            return false;
        }

        public function safeString($string) {
            $string = trim(str_replace(['\'', '"', '\\', '/', '%', '(', ')', '*', '\0', '!%'], '', $string));

            $string = trim(str_replace(['.', ',', ';', "\r", "\t", "\n",], ' ', $string));
            
            return $string;
        }

        public function replaceCirillicSymbols($string) {
            return str_replace(['е', 'а', 'о', 'к', 'п', 'в', 'т', 'у'], ['e', 'a', 'o', 'k', 'n', 'b', 't', 'y'], $string);
        }


        public function replaceLatinSymbols($string) {
            return str_replace(['e', 'a', 'o', 'k', 'n', 'b', 't', 'y'], ['е', 'а', 'о', 'к', 'п', 'в', 'т', 'у'], $string);
        }

        public function translit($string, $encode) {
            $string = (string) $string;
            $string = trim($string);
            $string = function_exists('mb_strtolower') ? mb_strtolower($string) : strtolower($string);

            if ($encode == 'latin')
                $string = strtr($string, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo','ж'=>'zh','з'=>'z','и'=>'i','й'=>'j','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'eh','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
            elseif ($encode == 'cirillic')
                $string = strtr($string, array('a'=>'а','b'=>'б','v'=>'в','w'=>'в','g'=>'г','d'=>'д','e'=>'е','yo'=>'ё','zh'=>'ж','z'=>'з','i'=>'и','j'=>'й','k'=>'к','l'=>'л','m'=>'м','n'=>'н','o'=>'о','p'=>'п','r'=>'р','s'=>'с','t'=>'т','u'=>'у','f'=>'ф','h'=>'х','c'=>'ц','ch'=>'ч','sh'=>'ш','shch'=>'щ','y'=>'ы','eh'=>'э','yu'=>'ю','ya'=>'я'));

            return $string;
        }
	}