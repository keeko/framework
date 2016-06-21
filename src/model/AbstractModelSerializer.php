<?php
namespace keeko\framework\model;

use keeko\framework\model\ModelSerializerInterface;
use Tobscure\JsonApi\Relationship;
use keeko\framework\utils\NameUtils;
use Propel\Runtime\Collection\Collection;

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

// 	protected function hydrateRelationships($model, $data) {
// 		$relationships = isset($data['relationships']) ? $data['relationships'] : [];

// 		foreach (array_keys($this->getRelationships()) as $rel) {
// 			if (isset($relationships[$rel]) && isset($relationships[$rel]['data'])) {
// 				if (isset($relationships[$rel]['data']['id'])) {
// 					$this->hydrateToOneRelationship($rel, $model, $relationships[$rel]['data']);
// 				} else {
// 					$this->hydrateToManyRelationship($rel, $model, $relationships[$rel]['data']);
// 				}
// 			}
// 		}
// 	}

// 	protected function hydrateToManyRelationship($rel, $model, $data) {
// 		// set them all by once
// 		$inferencer = $this->getTypeInferencer();
// 		$method = 'set' . $this->getCollectionMethodPluralName($rel);
// 		if (method_exists($model, $method)) {
// 			$collection = new Collection();

// 			foreach ($data as $item) {
// 				if (isset($item['id']) && !empty($item['id'])) {
// 					$queryClass = $inferencer->getQueryClass($item['type']);
// 					$obj = $queryClass::create()->findOneById($item['id']);
// 					$collection->append($obj);
// 				}
// 			}

// 			$model->$method($collection);
// 		}
// 	}

// 	protected function hydrateToOneRelationship($rel, $model, $data) {
// 		$method = 'set' . NameUtils::toStudlyCase($rel) . 'Id';
// 		if (method_exists($model, $method) && isset($data['id']) && !empty($data['id'])) {
// 			$model->$method($data['id']);
// 		}
// 	}

	/**
	 * @return TypeInferencerInterface
	 */
	abstract protected function getTypeInferencer();

	/**
	 * Returns the method name for collections (e.g. one-to-many and many-to-many)
	 *
	 * @param string $relatedName
	 * @return string
	 */
	abstract protected function getCollectionMethodName($relatedName);

	/**
	 * Returns the plural method name for collections (e.g. one-to-many and many-to-many)
	 *
	 * @param string $relatedName
	 * @return string
	 */
	abstract protected function getCollectionMethodPluralName($relatedName);

	public function getMeta($model) {
		return [];
	}

	public function getRelationships() {
		return [];
	}
}