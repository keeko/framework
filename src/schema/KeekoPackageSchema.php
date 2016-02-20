<?php
namespace keeko\framework\schema;

use phootwork\collection\Map;

abstract class KeekoPackageSchema extends SubSchema {

	/** @var string */
	protected $title;
	
	/** @var string */
	protected $class;
	
	/** @var Map<string, Map> */
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
		$this->extensions = $data->get('extensions', new Map());
		$this->extensionPoints = $data->get('extension-points', new Map());
		
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
	
		$extensions = $this->extensions->toArray();
		if (count($extensions) > 0) {
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
	public function hasExtension($key) {
		return $this->extensions->has($key);
	}
	
	/**
	 * Returns the extension with the key
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getExtension($key) {
		return $this->extensions->get($key);
	}
	
	/**
	 * Returns all extensions
	 *
	 * @return Map
	 */
	public function getExtensions() {
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