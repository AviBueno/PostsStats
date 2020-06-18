<?php

class Utils {
	public static function arrayValueOrException($arr, $key) {
		if (!isset($arr[$key])) {
			throw new \Exception("Array key not found: $key");
		}

		return $arr[$key];
	}

	public static function getWeekKey($dateTime) {
		return $dateTime->format('o-W'); // WEEKYEAR-WEEKNUM
	}

	public static function getMonthKey($dateTime) {
		return $dateTime->format('Y-m'); // YEAR-MONTH
	}
}
