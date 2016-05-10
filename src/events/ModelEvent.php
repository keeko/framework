<?php
namespace keeko\framework\events;

use Symfony\Component\EventDispatcher\Event;

class ModelEvent extends Event {
	
	private $model;
	
	public function __construct($model) {
		$this->model = $model;
	}
	
	public function getModel() {
		return $this->model;
	}
}