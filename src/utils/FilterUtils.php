<?php
namespace keeko\framework\utils;

class FilterUtils {

	public static function whitelistFilter($array, $whitelist = null) {
		$ret = [];
		
		foreach ($whitelist as $key) {
			if (isset($array[$key])) {
				$ret[$key] = $array[$key];
			}
		}
		
		return $ret;
	}

	public static function blacklistFilter($array, $blacklist = []) {
		$ret = [];
		
		foreach (array_keys($array) as $key) {
			if (!in_array($key, $blacklist)) {
				$ret[$key] = $array[$key];
			}
		}
		
		return $ret;
	}
}