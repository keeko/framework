<?php
namespace keeko\framework\model;

use keeko\framework\model\ModelSerializerInterface;
use phootwork\lang\Arrayable;
use Tobscure\JsonApi\Relationship;

abstract class AbstractModelSerializer extends AbstractSerializer implements ModelSerializerInterface, Arrayable {
	
	protected function addRelationshipSelfLink(Relationship $relationship, $model, $related) {
		$links = $relationship->getLinks();
		$links = $links + [
			'self' => $this->getSelf($model) . '/relationships/' . $related
		];
		$relationship->setLinks($links);
		return $relationship;
	}
	
	public function toArray($model) {
		$id = ['id' => $this->getId($model)];
		$attributes = $this->getAttributes($model);
		return $id + $attributes;
	}
	
	public function hydrateRelationships($model, $data) {
		$relationships = isset($data['relationships']) ? $data['relationships'] : [];
	
		foreach (array_keys($this->getRelationships()) as $rel) {
			if (isset($relationships[$rel]) && isset($relationships[$rel]['data'])) {
				$method = 'set' . ucFirst($rel);
				if (method_exists($this, $method)) {
					$this->$method($model, $relationships[$rel]['data']);
				}
			}
		}
	}
}