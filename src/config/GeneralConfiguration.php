<?php
namespace keeko\framework\config;

use keeko\framework\config\definition\GeneralDefinition;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class GeneralConfiguration extends AbstractConfigurationLoader {

	private $config;
	
	public function load($resource, $type = null) {
		if (file_exists($resource)) {
			$this->loaded = true;
			$config = Yaml::parse($resource);
		} else {
			$config = [];
		}
		$processor = new Processor();
		$this->config = $processor->processConfiguration(new GeneralDefinition(), $config);
	}
	
	public function supports($resource, $type = null) {
		return pathinfo($resource, PATHINFO_EXTENSION) === 'yaml' && pathinfo($resource, PATHINFO_FILENAME) === 'general';
	}
	
	public function getPathsFiles() {
		return $this->config['paths']['files'];
	}
}