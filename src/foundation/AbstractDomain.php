<?php
namespace keeko\framework\foundation;

use keeko\framework\service\ServiceContainer;
use keeko\framework\validator\ValidatorInterface;
use keeko\framework\utils\NameUtils;

abstract class AbstractDomain {

	/** @var ServiceContainer */
	protected $service;

	public function __construct(ServiceContainer $service) {
		$this->service = $service;
	}

	/**
	 * Returns the service container
	 *
	 * @return ServiceContainer
	 */
	protected function getServiceContainer() {
		return $this->service;
	}

	/**
	 * Returns a validator
	 *
	 * @return ValidatorInterface
	 */
	protected function getValidator() {
		return null;
	}

	protected function hydrateRelationships($model, $data) {
		$relationships = isset($data['relationships']) ? $data['relationships'] : [];

		foreach (array_keys($relationships) as $rel) {
			if (isset($relationships[$rel]['data'])) {
				$data = $relationships[$rel]['data'];
				if (empty($data)) {
					continue;
				} else if (isset($data['id'])) {
					$this->hydrateToOneRelationship($rel, $model, $data);
				} else {
					$this->hydrateToManyRelationship($rel, $model, $data);
				}
			}
		}
	}

	protected function hydrateToManyRelationship($rel, $model, $data) {
		$method = 'doUpdate' . NameUtils::toStudlyCase($rel);
		if (method_exists($this, $method)) {
			$this->$method($model, $data);
		}
	}

	protected function hydrateToOneRelationship($rel, $model, $data) {
		$method = 'doSet' . NameUtils::toStudlyCase($rel) . 'Id';
		if (method_exists($this, $method) && isset($data['id']) && !empty($data['id'])) {
			$this->$method($model, $data['id']);
		}
	}
}
