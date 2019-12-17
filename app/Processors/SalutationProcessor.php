<?php
	namespace App\Processors;

	class SalutationProcessor {
		public function __construct() {}
		public function __destruct() {}

		public static function getSalutation(array $request = null) {
			if (empty($request))
				return '';

			$firstname 		= $request[CONFIG::FIRSTNAME_OUTPUT_FIELD];
			$surname 		= $request[CONFIG::SURNAME_OUTPUT_FIELD];
			$fathername		= $request[CONFIG::FATHERNAME_OUTPUT_FIELD];
			$gender 		= $request[CONFIG::GENDER_OUTPUT_FIELD];

			$firstnameValid 	= $request[CONFIG::FIRSTNAME_VALID_OUTPUT_FIELD];
			$surnameValid 		= $request[CONFIG::SURNAME_VALID_OUTPUT_FIELD];
			$fathernameValid 	= $request[CONFIG::SURNAME_VALID_OUTPUT_FIELD];

			if (!$firstnameValid && !$surnameValid && !$fathernameValid)
				return 'Дорогие друзья!';

			if ($gender == 'М') {
				if ($firstnameValid && $fathernameValid) {
						return 'Дорогой ' . $firstname . ' ' . $fathername;
				} elseif ($firstnameValid) {
						return 'Дорогой ' . $firstname;
				} elseif ($surnameValid) {
						return 'Уважаемый г-н ' . $surname;
				}
			} elseif ($gender == 'Ж') {
				if ($firstnameValid && $fathernameValid) {
						return 'Дорогая ' . $firstname . ' ' . $fathername;
				} elseif ($firstnameValid) {
						return 'Дорогая ' . $firstname;
				} elseif ($surnameValid) {
						return 'Уважаемая г-жа ' . $surname;
				}
			} else {
				if ($firstnameValid && $fathernameValid) {
						return 'Дорогой (-ая) ' . $firstname . ' ' . $fathername;
				} elseif ($firstnameValid) {
						return 'Дорогой (-ая) ' . $firstname;
				} elseif ($surnameValid) {
						return 'Уважаемый(-ая) г-а (г-жа) ' . $surname;
				}
			}

			return '';
		}
	}