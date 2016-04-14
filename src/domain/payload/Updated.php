<?php
namespace keeko\framework\domain\payload;

use keeko\framework\domain\payload\AbstractPayload;

class Updated extends AbstractPayload {

	public function getModel() {
		return $this->get('model');
	}
}