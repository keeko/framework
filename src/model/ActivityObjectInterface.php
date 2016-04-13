<?php
namespace keeko\framework\model;

use keeko\core\model\ActivityObject;

interface ActivityObjectInterface {

	/**
	 * Turns this object into an activity object
	 *
	 * @return ActivityObject
	 */
	public function toActivityObject();
}