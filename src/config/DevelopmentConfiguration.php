<?php
namespace keeko\framework\config;

use keeko\framework\config\definition\DevelopmentDefinition;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class DevelopmentConfiguration extends AbstractConfigurationLoader {

	private $config;

	public function load($resource, $type = null) {
		$path = $this->locator->locate($resource);
		if (file_exists($path)) {
			$this->loaded = true;
			$config = Yaml::parse($path);
			$processor = new Processor();
			$this->config = $processor->processConfiguration(new DevelopmentDefinition(), $config);
		}
	}

	public function supports($resource, $type = null) {
		return pathinfo($resource, PATHINFO_EXTENSION) === 'yaml' && pathinfo($resource, PATHINFO_FILENAME) === 'development';
	}

	public function getPropelLogging() {
		return $this->config['propel']['logging'];
	}
}