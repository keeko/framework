<?php
namespace keeko\framework\utils;

class HydrateUtils {

	public static function hydrate($data, $obj, $config) {
		foreach ($config as $key => $cb) {
			$callback = false;
			if (is_string($key)) {
				$callback = true;
			} else {
				$key = $cb;
			}
			if (isset($data[$key])) {
				$val = $data[$key];
				if (is_callable($cb) && $callback) {
					$val = $cb($val);
				}

				$method = 'set' . NameUtils::toStudlyCase($key);

				$obj->$method($val);
			}
		}

		return $obj;
	}
}