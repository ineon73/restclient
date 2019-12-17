<?php
	namespace App\Processors;

	class PhoneValidator {
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

			if (isset($request[CONFIG::PHONE_INPUT_FIELD])) {
				$phone = $request[CONFIG::PHONE_INPUT_FIELD];

				/* Parse phone */
				if ($normalizePhone = $this -> normalize($phone))
					$output[CONFIG::PHONE_INPUT_FIELD] = $normalizePhone;
				else {
					$output[CONFIG::PHONE_INPUT_FIELD] = null;
					$output['PARSING_ERROR'] []= 'Некорректный телефон в поле \'' . CONFIG::PHONE_INPUT_FIELD . '\': ' . $phone;
				}
				/* */
			}

			if (isset($request[CONFIG::WORK_PHONE_INPUT_FIELD])) {
				$workPhone = $request[CONFIG::WORK_PHONE_INPUT_FIELD];

				/* Parse work phone */
				if ($normalizeWorkPhone = $this -> normalize($workPhone))
					$output[CONFIG::WORK_PHONE_INPUT_FIELD] = $normalizeWorkPhone;
				else {
					$output[CONFIG::WORK_PHONE_INPUT_FIELD] = null;
					$output['PARSING_ERROR'] []= 'Некорректный телефон в поле \'' . CONFIG::WORK_PHONE_INPUT_FIELD . '\': ' . $workPhone;
				}
				/* */
			}

			if (isset($request[CONFIG::MOBILE_PHONE_INPUT_FIELD])) {
				$mobilePhone = $request[CONFIG::MOBILE_PHONE_INPUT_FIELD];

				/* Parse mobile phone */
				if ($normalizeMobilePhone = $this -> normalize($mobilePhone))
					$output[CONFIG::MOBILE_PHONE_INPUT_FIELD] = $normalizeMobilePhone;
				else {
					$output[CONFIG::WORK_PHONE_INPUT_FIELD] = null;
					$output['PARSING_ERROR'] []= 'Некорректный телефон в поле \'' . CONFIG::MOBILE_PHONE_INPUT_FIELD . '\': ' . $mobilePhone;
				}
				/* */
			}

			return $output;
		}

		private function normalize($phone = null) {
			if (empty($phone))
				return false;

			$phone = trim(preg_replace('/[^\d]+/', '', $phone));
			
			if (strlen($phone) > 11)
				return false;

			if (substr($phone, 0, 1) == '8')
				$phone = '7' . substr($phone, 1);

			if (strlen($phone) == '10')
				$phone = '7' . $phone;

			return $phone;
		}
	}