<?php
namespace keeko\framework\domain\payload;

use keeko\framework\domain\payload\AbstractPayload;

class NotFound extends AbstractPayload {

	public function getMessage() {
		return $this->get('message');
	}
}