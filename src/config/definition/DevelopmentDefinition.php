<?php
namespace keeko\framework\config\definition;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class DevelopmentDefinition implements ConfigurationInterface {

	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$root = $treeBuilder->root('development');
		$root
			->children()
				->arrayNode('propel')
					->children()
						//->enumNode('logging')->values(['stderr'])->end()
						->scalarNode('logging')->end()
					->end()
				->end()
			->end()
		;
		
		return $treeBuilder;
	}

}