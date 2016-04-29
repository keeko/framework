<?php
namespace keeko\framework\schema;

use phootwork\collection\Map;
use phootwork\collection\Collection;
use phootwork\collection\ArrayList;
use phootwork\collection\Set;

abstract class KeekoPackageSchema extends SubSchema {

	/** @var string */
	protected $title;
	
	/** @var string */
	protected $class;
	
	/** @var Map */
	protected $extensions;
	
	/** @var Map<string, string> */
	protected $extensionPoints;
	
	/**
	 * @param array $contents
	 */
	protected function parse($contents = []) {
		$data = new Map($contents);
	
		$this->title = $data->get('title', '');
		$this->class = $data->get('class', '');
		$this->extensionPoints = $data->get('extension-points', new Map());
		
		$this->extensions = new Map();
		$extensions = $data->get('extensions', []);
		if ($extensions instanceof Collection) {
			$extensions = $extensions->toArray();
		}
		foreach ($extensions as $key => $val) {
			if (!$this->extensions->has($key)) {
				$this->extensions->set($key, new ArrayList());
			}
			if (is_array($val)) {
				foreach ($val as $v) {
					$this->extensions->get($key)->add($v);
				}
			} else {
				$this->extensions->get($key)->add($val);
			}
		}
		
		return $data;
	}
	
	public function toArray() {
		$arr = [
			'title' => $this->title,
			'class' => $this->class
		];
	
		$extensionPoints = $this->extensionPoints->toArray();
		if (count($extensionPoints) > 0) {
			$arr['extension-points'] = $extensionPoints;
		}
	
		if ($this->extensions->size() > 0) {
			$extensions = [];
			foreach ($this->extensions->keys() as $key) {
				$data = $this->extensions->get($key);
				if ($data->size() == 1) {
					$extensions[$key] = $data->get(0);						
				} else {
					$extensions[$key] = $data->toArray();
				}
			}
			$arr['extensions'] = $extensions;
		}

		return $arr;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	
	public function getClass() {
		return $this->class;
	}
	
	public function setClass($class) {
		$this->class = $class;
		return $this;
	}
	
	/**
	 * Checks whether an extension with the given key exists
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function hasExtensions($key) {
		return $this->extensions->has($key);
	}
	
	/**
	 * Returns the extensions for the given key
	 *
	 * @param string $key
	 * @return ArrayList
	 */
	public function getExtensions($key) {
		return $this->extensions->get($key);
	}
	
	/**
	 * Returns all extension keys
	 * 
	 * @return Set
	 */
	public function getExtensionKeys() {
		return $this->extensions->keys();
	}
	
	/**
	 * Returns all extensions
	 *
	 * @return Map
	 */
	public function getAllExtensions() {
		return $this->extensions;
	}
	
	/**
	 * Checks whether an extension point with the given key exists
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function hasExtensionPoint($key) {
		return $this->extensionPoints->has($key);
	}
	
	/**
	 * Returns the path of the schema for the given key
	 *
	 * @param string $key
	 * @return string
	 */
	public function getExtensionPoint($key) {
		return $this->extensionPoints->get($key);
	}
	
	/**
	 * Returns all extension points
	 *
	 * @return Map
	 */
	public function getExtensionPoints() {
		return $this->extensionPoints;
	}
}