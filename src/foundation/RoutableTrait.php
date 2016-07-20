<?php
namespace keeko\framework\foundation;

trait RoutableTrait {

	protected $basePath;
	protected $baseUrl;
	protected $destination;

	public function setBaseUrl($url) {
		$this->baseUrl = $url;
	}

	public function getBaseUrl() {
		return $this->baseUrl;
	}

	public function setBasePath($path) {
		$this->basePath = $path;
	}

	public function getBasePath() {
		return $this->basePath;
	}

	public function setDestination($path) {
		$this->destination = $path ?: '/';
	}

	public function getDestination() {
		return $this->destination;
	}
}
