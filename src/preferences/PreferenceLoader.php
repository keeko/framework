<?php
namespace keeko\framework\preferences;

use keeko\core\model\PreferenceQuery;

class PreferenceLoader {

	/** @var SystemPreferences */
	private $system;
	private $raw = [];
	private $preferences = [];
	
	public function __construct($preferences = null) {
		$this->raw['system'] = [];
		$preferences = PreferenceQuery::create()->find();
		foreach ($preferences as $preference) {
			$module = $preference->getModuleId() ?: 'system';
			if (!isset($this->raw[$module])) {
				$this->raw[$module] = [];
			}
			
			$this->raw[$module][$preference->getKey()] = $preference->getValue();
		}
	}
	
	/**
	 * @return SystemPreferences
	 */
	public function getSystemPreferences() {
		if ($this->system === null) {
			$this->system = new SystemPreferences($this->raw['system']);
		}
		return $this->system;
	}
	
	public function getModulePreferences($moduleId) {
		if (isset($this->preferences[$moduleId])) {
			return $this->preferences[$moduleId];
		}
		
		if (!isset($this->raw[$moduleId])) {
			$this->preferences[$moduleId] = new Preferences($this->raw[$moduleId]);
			return $this->preferences[$moduleId];
		}
	}
}
