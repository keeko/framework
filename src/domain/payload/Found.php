<?php
namespace keeko\framework\domain\payload;

use keeko\framework\domain\payload\AbstractPayload;

class Found extends AbstractPayload {

	public function getModel() {
		return $this->get('model');
	}
}