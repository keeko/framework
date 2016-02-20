<?php
namespace keeko\framework\config\definition;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class GeneralDefinition implements ConfigurationInterface {

	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$root = $treeBuilder->root('general');
		$root
			->children()
				->arrayNode('paths')
					->addDefaultsIfNotSet()
					->children()
						->scalarNode('files')->defaultValue('public/files')->end()
					->end()
				->end()
			->end()
		;
		
		return $treeBuilder;
	}

}