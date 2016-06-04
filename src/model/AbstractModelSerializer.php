<?php
namespace keeko\framework\model;

use keeko\framework\model\ModelSerializerInterface;
use Tobscure\JsonApi\Relationship;
use keeko\framework\utils\NameUtils;

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
				if (isset($relationships[$rel]['data']['id'])) {
					$this->hydrateToOneRelationship($rel, $model, $relationships[$rel]['data']);
				} else {
					$this->hydrateToManyRelationship($rel, $model, $relationships[$rel]['data']);
				}
			}
		}
	}

	protected function hydrateToManyRelationship($rel, $model, $data) {
		$inferencer = $this->getTypeInferencer();
		$method = 'add' . NameUtils::toStudlyCase($rel);
		if (method_exists($model, $method)) {
			foreach ($data as $item) {
				if (isset($item['id']) && !empty($item['id'])) {
					$queryClass = $inferencer->getQueryClass($item['type']);
					$obj = $queryClass::create()->findOneById($item['id']);
					if ($obj !== null) {
						$model->$method($obj);
					}
				}
			}
		}
	}

	protected function hydrateToOneRelationship($rel, $model, $data) {
		$method = 'set' . NameUtils::toStudlyCase($rel) . 'Id';
		if (method_exists($model, $method) && isset($data['id']) && !empty($data['id'])) {
			$model->$method($data['id']);
		}
	}

	/**
	 * @return TypeInferencerInterface
	 */
	abstract protected function getTypeInferencer();

	public function getMeta($model) {
		return [];
	}

	public function getRelationships() {
		return [];
	}
}