<?php
namespace keeko\framework\preferences;

use keeko\core\model\Preference;
use Propel\Runtime\Collection\ObjectCollection;
use keeko\core\model\Map\PreferenceTableMap;

class Preferences {

	private $preferences;
	private $moduleId;
	
	public function __construct(array $preferences, $moduleId = null) {
		$this->preferences = $preferences;
		$this->moduleId;
	}
	
	public function has($key) {
		return isset($this->preferences[$key]);
	}
	
	public function get($key, $default = null) {
		if ($this->has($key)) {
			return $this->preferences[$key];
		}

		return $default;
	}
	
	public function set($key, $value) {
		$this->preferences[$key] = $value;
	}
	
	public function save($key = null) {
		if ($key === null) {
			$data = [];
			foreach (array_keys($this->preferences) as $key) {
				$data[] = $this->getPreference($key);
			}
			
			$collection = new ObjectCollection($data);
			$collection->setModel(PreferenceTableMap::CLASS_NAME);
			$collection->save();
		} else if ($this->has($key)) {
			$p = $this->getPreference($key);
			$p->save();
		}
	}
	
	/**
	 *
	 * @param String $key
	 * @return Preference
	 */
	private function getPreference($key) {
		$p = new Preference();
		$p->setKey($key);
		$p->setValue($this->get($key));
		if ($this->moduleId) {
			$p->setModuleId($this->moduleId);
		}
		
		return $p;
	}
}