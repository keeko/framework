<?php
namespace keeko\framework\routing;

trait QueryStringTrait {

	protected function getQueryStringOptions() {
		return [
			'separator' => '?',
			'delimiter' => '&',
			'true' => 'yes',
			'false' => 'no'
		];
	}

	/**
	 * Unserializes parameters from a query string
	 *
	 * @param string $params
	 * @return array the unserialized array
	 */
	protected function unserializeQueryString($queryString) {
		if (empty($queryString)) {
			return [];
		}
		
		$options = $this->getQueryStringOptions();
		if ($queryString[0] === $options['separator']) {
			$queryString = substr($queryString, 1);
		}
		$parts = explode($options['delimiter'], $queryString);
		$params = [];
		foreach ($parts as $part) {
			$kv = explode('=', $part);
			if ($kv[0] != '') {
				$params[$kv[0]] = count($kv) > 1
					? $kv[1] == $options['true'] ? true
					: ($kv[1] == $options['false'] ? false : $kv[1]) : true;
			}
		}
	
		return $params;
	}
	
	/**
	 * Serializes parameters for query string
	 *
	 * @param array $params
	 * @return string the serialized params
	 */
	protected function serializeQueryString($params, $prepend = false) {
		$options = $this->getQueryStringOptions();
		$pairs = [];
		foreach ($params as $key => $val) {
			$pair = $key;
			if (is_bool($val) === true) {
				$pair .= '=' . $val ? $options['true'] : $options['false'];
			} else if ($val != '') {
				$pair .= '=' . $val;
			}
			$pairs[] = $pair;
		}
		return ($prepend ? $options['separator'] : '') . implode($options['delimiter'], $pairs);
	}
}
