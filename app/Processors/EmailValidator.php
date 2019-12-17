<?php
	namespace App\Processors;

	class EmailValidator {
		public function __construct() {}
		public function __destruct() {}

		public function process(array $request = null) {
			if (empty($request))
				die ('Error: empty data!');

			$output = $request;
			if (isset($output['PARSING_ERROR']))
				$output['PARSING_ERROR'] = $output['PARSING_ERROR'];
			else
				$output['PARSING_ERROR'] = [];

			if (isset($request[CONFIG::EMAIL_INPUT_FIELD])) {
				$email = $request[CONFIG::EMAIL_INPUT_FIELD];

				/* Parse email */
				if ($normalizeEmail = $this -> normalize($email))
					$output[CONFIG::EMAIL_INPUT_FIELD] = $normalizeEmail;
				else {
					$output[CONFIG::EMAIL_INPUT_FIELD] = null;
					$output['PARSING_ERROR'] []= 'Некорректный e-mail в поле \'' . CONFIG::EMAIL_INPUT_FIELD . '\': ' . $email;
				}
				/* */
			}

			if (isset($request[CONFIG::WORK_EMAIL_INPUT_FIELD])) {
				$workEmail = $request[CONFIG::WORK_EMAIL_INPUT_FIELD];

				/* Parse work email */
				if ($normalizeWorkEmail = $this -> normalize($workEmail))
					$output[CONFIG::WORK_EMAIL_INPUT_FIELD] = $normalizeWorkEmail;
				else {
					$output[CONFIG::WORK_EMAIL_INPUT_FIELD] = null;
					$output['PARSING_ERROR'] []= 'Некорректный e-mail в поле \'' . CONFIG::WORK_EMAIL_INPUT_FIELD . '\': ' . $workEmail;
				}
				/* */
			}

			if (isset($request[CONFIG::PRIVATE_EMAIL_INPUT_FIELD])) {
				$privateEmail = $request[CONFIG::PRIVATE_EMAIL_INPUT_FIELD];

				/* Parse private email */
				if ($normalizePrivateEmail = $this -> normalize($privateEmail))
					$output[CONFIG::PRIVATE_EMAIL_INPUT_FIELD] = $normalizePrivateEmail;
				else {
					$output[CONFIG::PRIVATE_EMAIL_INPUT_FIELD] = null;
					$output['PARSING_ERROR'] []= 'Некорректный e-mail в поле \'' . CONFIG::PRIVATE_EMAIL_INPUT_FIELD . '\': ' . $privateEmail;
				}
				/* */
			}

			return $output;
		}

		private function normalize($email = null) {
			if (empty($email))
				return false;

			$email = strtolower(trim(str_replace(['е', 'а', 'о', 'к', 'п', 'в', 'т', 'у'], ['e', 'a', 'o', 'k', 'n', 'b', 't', 'y'], $email)));

			if (substr($email, -1) == '.')
				$email = substr($email, 0, strlen($email) - 1);

			if (!filter_var($email, FILTER_VALIDATE_EMAIL))
				return false;

			return $this -> autoReplaceDomain($email);
		}

		private function autoReplaceDomain($email = null) {
			if (empty($email))
				return false;

			$address 	= trim(substr($email, 0, strpos($email, '@')));
			$domain 	= trim(substr($email, strpos($email, '@') + 1, strlen($email)));
			 
			$domain = strtr($domain, [
			  	'ya.ru' => 'yandex.ru'
			]);

			return $address . '@' . $domain;
		}
	}