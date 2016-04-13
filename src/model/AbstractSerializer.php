<?php
namespace keeko\framework\model;

use Tobscure\JsonApi\SerializerInterface;
use Tobscure\JsonApi\Relationship;

abstract class AbstractSerializer implements SerializerInterface {
	
	public function getSelf($model) {
		return '%apiurl%' . $this->getType($model) . '/' . $this->getId($model);
	}

	public function getLinks($model) {
		return [
			'self' => $this->getSelf($model)
		];
	}
	
	public function getAttributes($model, $fields = null) {
		return [];
	}

	public function getRelationship($model, $name) {
		// strip namespace
		if (strstr($name, '/') !== false) {
			$name = substr($name, strpos($name, '/') + 1);
		}
	
		$method = $name;
	
		// to camel case
		if (strstr($method, '-') !== false) {
			$method = lcfirst(implode('', array_map('ucfirst', explode('-', $method))));
		}
	
		if (method_exists($this, $method)) {
			$relationship = $this->$method($model, $name);
			if ($relationship !== null && !($relationship instanceof Relationship)) {
				throw new \LogicException('Relationship method must return null or an instance of '
					. Relationship::class);
			}
			return $relationship;
		}
	}
	
}