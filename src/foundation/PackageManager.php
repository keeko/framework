<?php
namespace keeko\framework\foundation;

use Composer\Package\CompletePackage;
use Composer\Package\Loader\ArrayLoader;
use keeko\framework\exceptions\PackageException;
use keeko\framework\schema\PackageSchema;
use keeko\framework\service\ServiceContainer;
use phootwork\collection\Map;
use phootwork\json\Json;
use Puli\Repository\Api\Resource\BodyResource;
use Puli\Repository\Api\ResourceNotFoundException;

class PackageManager {

	/** @var Map */
	private $packageCache;
	
	/** @var Map */
	private $composerCache;
	
	/** @var ServiceContainer */
	private $service;
	
	/** @var ArrayLoader */
	private $loader;

	public function __construct(ServiceContainer $service) {
		$this->service = $service;
		$this->packageCache = new Map();
		$this->composerCache = new Map();
		$this->loader = new ArrayLoader();
	}

	/**
	 *
	 * @param BodyResource $file
	 * @return array
	 */
	private function getJson(BodyResource $file) {
		$config = Json::decode($file->getBody());

		// fix version
		if (!isset($config['version'])) {
			$config['version'] = 'dev-master';
		}
		
		return $config;
	}
	
	private function getFile($packageName) {
		$repo = $this->service->getResourceRepository();
		
		try {
			return $repo->get('/packages/' . $packageName . '/composer.json');
		} catch (ResourceNotFoundException $e) {
			throw new PackageException(sprintf('Package (%s) not found', $packageName));
		}
	}

	/**
	 * @param string $packageName
	 * @return PackageSchema
	 */
	public function getPackage($packageName) {
		if ($this->packageCache->has($packageName)) {
			return $this->packageCache->get($packageName);
		}
		
		$package = new PackageSchema($this->getJson($this->getFile($packageName)));
		$this->packageCache->set($packageName, $package);
		return $package;
	}
	
	
	/**
	 *
	 * @param string $packageName
	 * @return CompletePackage
	 */
	public function getComposerPackage($packageName) {
		if ($this->composerCache->has($packageName)) {
			return $this->composerCache->get($packageName);
		}
		
		$package = $this->loader->load($this->getJson($this->getFile($packageName)));
		$this->composerCache->set($packageName, $package);
		return $package;
	}
}
