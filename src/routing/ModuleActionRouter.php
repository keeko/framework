<?php
namespace keeko\framework\routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ModuleActionRouter extends AbstractRouter implements RouterInterface {

	public function __construct(array $options) {
		parent::__construct($options);
		
		// routes
		$routes = new RouteCollection();
		
		$moduleRoute = new Route('/{module}', array(
			'module' => $this->options['module']
		));
		$actionRoute = new Route('/{module}/{action}');
		$paramsRoute = new Route(sprintf('/{module}/{action}%s{params}', $this->options['param-separator']));
		
		$routes->add('module', $moduleRoute);
		$routes->add('action', $actionRoute);
		$routes->add('params', $paramsRoute);
		
		$this->init($routes);
	}
	
	/*
	 * (non-PHPdoc) @see \keeko\core\routing\AbstractRouter::getOptionalOptions()
	 */
	protected function getOptionalOptions() {
		return [
			'application',
			'action'
		];
	}
	
	/*
	 * (non-PHPdoc) @see \keeko\core\routing\RouteMatcherInterface::match()
	 */
	public function match($destination) {
		if ($destination == '') {
			$destination = '/';
		}
		
		$data = $this->matcher->match($destination);
		
		// unserialize params
		if (array_key_exists('params', $data)) {
			$data['params'] = $this->unserializeParams($data['params']);
		}
		
		return $data;
	}
	
	/*
	 * (non-PHPdoc) @see \keeko\core\routing\RouteGeneratorInterface::match()
	 */
	public function generate($data) {
		
		// params route
		if (array_key_exists('params', $data)) {
			$data['params'] = $this->serializeParams($data['params']);
			return $this->generator->generate('params', $data);
		}
		
		// action route
		if (array_key_exists('action', $data)) {
			return $this->generator->generate('action', $data);
		}
		
		// module route
		if (array_key_exists('module', $data)) {
			return $this->generator->generate('module', $data);
		}
	}
}
