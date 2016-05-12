<?php
namespace keeko\framework\service;

use keeko\core\model\Extension;
use keeko\core\model\ExtensionQuery;
use phootwork\collection\Map;
use phootwork\json\Json;
use phootwork\collection\ArrayList;

class ExtensionRegistry {
	
	/** @var Map */
	private $extensions;
	
	/** @var Map */
	private $packages;
	
	public function __construct() {
		$this->extensions = new Map();
		$this->packages = new Map();
		
		// load up all extensions
		$exts = ExtensionQuery::create()->joinPackage()->find();
		foreach ($exts as $ext) {
			/* @var $ext Extension */
			$key = $ext->getKey();
			$packageName = $ext->getPackage()->getName();
			$data = Json::decode($ext->getData());
			
			// add to global extensions
			if (!$this->extensions->has($key)) {
				$this->extensions->set($key, new ArrayList());
			}
			$this->extensions->get($key)->add($data);
			
			// add to package extensions
			if (!$this->packages->has($packageName)) {
				$this->packages->set($packageName, new Map());
			}
			
			$pkg = $this->packages->get($packageName);
			if (!$pkg->has($key)) {
				$pkg->set($key, new ArrayList());
			}
			$pkg->get($key)->add($data);
		}
	}

	/**
	 * Returns the extension for the given key
	 *
	 * @param string $key
	 * @return ArrayList
	 */
	public function getExtensions($key) {
		if ($this->extensions->has($key)) {
			return $this->extensions->get($key);
		}
		
		return [];
	}
	
	/**
	 * Returns the extension for the given key by a given packageName
	 *
	 * @param string $key
	 * @param string $packageName
	 * @return ArrayList
	 */
	public function getExtensionsByPackage($key, $packageName) {
		if ($this->packages->has($packageName)) {
			$pkg = $this->packages->get($packageName);
			if ($pkg->has($key)) {
				return $pkg->get($key);
			}
		}
		
		return [];
	}
}