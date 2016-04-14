<?php
namespace keeko\framework\domain\payload;

use keeko\framework\domain\payload\AbstractPayload;

class NotDeleted extends AbstractPayload {

	public function getMessage() {
		return $this->get('message');
	}
}