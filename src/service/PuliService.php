<?php
namespace keeko\framework\service;

use Puli\Repository\Api\ResourceRepository;
use Puli\Discovery\Api\Discovery;
use Puli\UrlGenerator\Api\UrlGenerator;

class PuliService {

	/** @var Puli\GeneratedPuliFactory */
	private $puliFactory;

	/** @var ResourceRepository */
	private $resourceRepository;

	/** @var Discovery */
	private $resourceDiscovery;

	/** @var UrlGenerator */
	private $urlGenerator;

	/**
	 *
	 * @return Puli\GeneratedPuliFactory
	 */
	private function getPuliFactory() {
		if ($this->puliFactory === null) {
			$factoryClass = PULI_FACTORY_CLASS;
			$this->puliFactory = new $factoryClass();
		}
		return $this->puliFactory;
	}

	/**
	 * Returns an instance to the puli repository
	 *
	 * @return ResourceRepository
	 */
	public function getResourceRepository() {
		if ($this->resourceRepository === null) {
			$this->resourceRepository = $this->getPuliFactory()->createRepository();
		}

		return $this->resourceRepository;
	}

	/**
	 * Returns an instance to the puli discovery
	 *
	 * @return Discovery
	 */
	public function getResourceDiscovery() {
		if ($this->resourceDiscovery === null) {
			$repo = $this->getResourceRepository();
			$this->resourceDiscovery = $this->getPuliFactory()->createDiscovery($repo);
		}

		return $this->resourceDiscovery;
	}

	/**
	 * Returns the url generator for puli resources
	 *
	 * @return UrlGenerator
	 */
	public function getUrlGenerator() {
		if ($this->urlGenerator === null) {
			$discovery = $this->getResourceDiscovery();
			$this->urlGenerator = $this->getPuliFactory()->createUrlGenerator($discovery);
		}

		return $this->urlGenerator;
	}
}