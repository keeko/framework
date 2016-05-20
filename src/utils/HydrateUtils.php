<?php
namespace keeko\framework\utils;

use Propel\Generator\Model\PhpNameGenerator;

class HydrateUtils {

	public static function hydrate($data, $obj, $config) {
		$converter = new PhpNameGenerator();
		
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
				
				$method = 'set' . $converter->generateName([$key, PhpNameGenerator::CONV_METHOD_PHPNAME]);
				
				$obj->$method($val);
			}
		}
		
		return $obj;
	}
}