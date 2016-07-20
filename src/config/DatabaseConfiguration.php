<?php
namespace keeko\framework\config;

use keeko\framework\config\definition\DatabaseDefinition;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class DatabaseConfiguration extends AbstractConfigurationLoader {

	private $config;

	public function load($resource, $type = null) {
		$path = $this->locator->locate($resource);
		if (file_exists($path)) {
			$this->loaded = true;
			$config = Yaml::parse($path);
			$processor = new Processor();
			$this->config = $processor->processConfiguration(new DatabaseDefinition(), $config);
		}
	}

	public function supports($resource, $type = null) {
		return pathinfo($resource, PATHINFO_EXTENSION) === 'yaml' && pathinfo($resource, PATHINFO_FILENAME) === 'database';
	}

	public function getHost() {
		return $this->config['host'];
	}

	public function getDatabase() {
		return $this->config['database'];
	}

	public function getUser() {
		return $this->config['user'];
	}

	public function getPassword() {
		return $this->config['password'];
	}
}
