<?php
namespace keeko\framework\model;

use keeko\framework\model\ModelSerializerInterface;
use Tobscure\JsonApi\Relationship;

abstract class AbstractModelSerializer extends AbstractSerializer implements ModelSerializerInterface {
	
	/**
	 * 
	 * @param Relationship $relationship
	 * @param mixed $model
	 * @param string $related
	 * @return Relationship
	 */
	protected function addRelationshipSelfLink(Relationship $relationship, $model, $related) {
		$links = $relationship->getLinks();
		$links = !is_array($links) ? [] : $links;
		$links = array_merge($links, [
			'self' => $this->getSelf($model) . '/relationships/' . $related
		]);
		$relationship->setLinks($links);
		return $relationship;
	}
	
	public function toArray($model) {
		$id = ['id' => $this->getId($model)];
		$attributes = $this->getAttributes($model);
		return $id + $attributes;
	}
	
	protected function hydrateRelationships($model, $data) {
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
	
	public function getMeta($model) {
		return [];
	}
	
	public function getRelationships() {
		return [];
	}
}