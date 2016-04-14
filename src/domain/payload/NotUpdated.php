<?php
namespace keeko\framework\domain\payload;

use keeko\framework\domain\payload\AbstractPayload;

class NotUpdated extends AbstractPayload {

	public function getModel() {
		return $this->get('model');
	}
}