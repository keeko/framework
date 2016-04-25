<?php
namespace keeko\framework\schema;

use phootwork\collection\CollectionUtils;
use phootwork\collection\Map;
use phootwork\collection\ArrayList;

class CodegenSchema extends RootSchema {
	
	/** @var Map */
	private $data;
	
	/** @var Map */
	private $models;
	
	/** @var ArrayList */
	private $excluded;
	
	public function __construct($contents = []) {
		$this->parse($contents);
	}
	
	private function parse($contents) {
		$this->data = CollectionUtils::toMap($contents);
		
		$this->models = $this->data->get('models', new Map());
		$this->excluded = $this->data->get('excluded', new ArrayList());
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
	 * Return the excluded models
	 * 
	 * @return ArrayList
	 */
	public function getExcludedModels() {
		return $this->excluded;
	}
}
