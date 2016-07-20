<?php
namespace keeko\framework\utils;

use Tobscure\JsonApi\Parameters as BaseParameters;

class Parameters extends BaseParameters {

	public function getPage($key, $default = null) {
		$page = $this->getInput('page', []);

		return isset($page[$key]) ? $page[$key] : $default;
	}

	public function toQueryString(array $overwrites = []) {
		$parts = [];

		if ($include = $this->getInput('include')) {
			$parts['include'] = $include;
		}

		if ($sort = $this->getInput('sort')) {
			$parts['sort'] = $sort;
		}

		$parts['fields'] = $this->getFields();

		if ($filter = $this->getInput('filter')) {
			$parts['filter'] = $filter;
		}

		$parts['page'] = $this->getInput('page', []);

		// apply overwrites
		$parts = array_merge_recursive($parts, $overwrites);

		// to string
		$qs = [];
		foreach ($parts as $key => $value) {
			if (is_array($value)) {
				$this->dataToQueryString($qs, $value, $key);
			} else {
				$qs[] = $key . '=' .$value;
			}
		}

		return implode('&', $qs);
	}

	private function dataToQueryString(&$qs, array $data, $parentKey = '') {
		foreach ($data as $key => $value) {
			$var = sprintf('%s[%s]', $parentKey, $key);
			if (is_array($value)) {
				$this->dataToQueryString($qs, $value, $var);
			} else {
				$qs[] = $var . '=' . $value;
			}
		}
	}
}