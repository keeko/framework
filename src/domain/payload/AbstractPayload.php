<?php
namespace keeko\framework\domain\payload;

class AbstractPayload {
	
	protected $payload = array();
	
	public function __construct(array $payload) {
		$this->payload = $payload;
	}
	
	public function get($key = null) {
		if ($key === null) {
			return $this->payload;
		}
		
		if (isset($this->payload[$key])) {
			return $this->payload[$key];
		}
		
		return null;
	}
}