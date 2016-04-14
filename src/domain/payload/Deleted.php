<?php
namespace keeko\framework\domain\payload;

use keeko\framework\domain\payload\AbstractPayload;

class Deleted extends AbstractPayload {

	public function getModel() {
		return $this->get('model');
	}
}