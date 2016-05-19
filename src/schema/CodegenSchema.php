<?php
namespace keeko\framework\schema;

use phootwork\collection\CollectionUtils;
use phootwork\collection\Map;
use phootwork\collection\ArrayList;

class CodegenSchema extends RootSchema {
	
	const EXCLUDED_ACTION = 'action';
	const EXCLUDED_SERIALIZER = 'serializer';
	const EXCLUDED_DOMAIN = 'domain';
	const EXCLUDED_API = 'api';
	const EXCLUDED_EMBER = 'ember';
	
	/** @var Map */
	private $data;
	
	/** @var Map */
	private $models;
	
	/** @var Map */
	private $excluded;
	
	public function __construct($contents = []) {
		$this->parse($contents);
	}
	
	private function parse($contents) {
		$this->data = CollectionUtils::toMap($contents);
		
		$this->models = $this->data->get('models', new Map());
		$this->excluded = $this->data->get('excluded', new Map());
	}
	
	/**
	 *
	 * @param string $modelName
	 * @param string $io `read` or `write`
	 * @param string $section `filter` or `conversion`
	 * @return array
	 */
	private function getArray($modelName, $io, $section) {
		if ($this->models->has($modelName)
				&& $this->models->get($modelName)->has($io)
				&& $this->models->get($modelName)->get($io)->has($section)) {
			return $this->models->get($modelName)->get($io)->get($section)->toArray();
		}

		return [];
	}
	
	/**
	 * Returns the relationships for a model
	 * 
	 * @param string $modelName
	 */
	public function getRelationships($modelName) {
		if ($this->models->has($modelName)
				&& $this->models->get($modelName)->has('relationships')) {
			return $this->models->get($modelName)->get('relationships');
		}
		return new Map();
	}
	
	/**
	 * Returns all write conversions
	 *
	 * @param string $modelName
	 * @return array
	 */
	public function getWriteConversion($modelName) {
		return $this->getArray($modelName, 'write', 'conversion');
	}
	
	/**
	 * Returns all write filters
	 *
	 * @param string $modelName
	 * @return array
	 */
	public function getWriteFilter($modelName) {
		return $this->getArray($modelName, 'write', 'filter');
	}
	
	/**
	 * Returns all read filters
	 *
	 * @param string $modelName
	 * @return array
	 */
	public function getReadFilter($modelName) {
		return $this->getArray($modelName, 'read', 'filter');
	}
	
	/**
	 * Return the excluded models for a given section
	 * 
	 * @param string $section
	 * @return ArrayList
	 */
	public function getExcluded($section) {
		return $this->excluded->get($section, new ArrayList());
	}
	
	/**
	 * Return the excluded models for action generation
	 * 
	 * @return ArrayList
	 */
	public function getExcludedAction() {
		return $this->getExcluded(self::EXCLUDED_ACTION);
	}
	
	/**
	 * Return the excluded models for api generation
	 * 
	 * @return ArrayList
	 */
	public function getExcludedApi() {
		return $this->getExcluded(self::EXCLUDED_API);
	}
	
	/**
	 * Return the excluded models for domain generation
	 * 
	 * @return ArrayList
	 */
	public function getExcludedDomain() {
		return $this->getExcluded(self::EXCLUDED_DOMAIN);
	}
	
	/**
	 * Return the excluded models for ember model generation
	 * 
	 * @return ArrayList
	 */
	public function getExcludedEmber() {
		return $this->getExcluded(self::EXCLUDED_EMBER);
	}
	
	/**
	 * Return the excluded models for serializer generation
	 * 
	 * @return ArrayList
	 */
	public function getExcludedSerializer() {
		return $this->getExcluded(self::EXCLUDED_SERIALIZER);
	}
}
