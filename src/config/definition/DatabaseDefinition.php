<?php
namespace keeko\framework\config\definition;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class DatabaseDefinition implements ConfigurationInterface {

	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$root = $treeBuilder->root('database');
		$root
			->children()
				->scalarNode('host')->defaultValue('localhost')->end()
				->scalarNode('user')->end()
				->scalarNode('password')->end()
				->scalarNode('database')->isRequired()->end()
			->end()
		;
		
		return $treeBuilder;
	}

}