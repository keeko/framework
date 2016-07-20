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