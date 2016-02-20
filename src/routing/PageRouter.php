<?php
namespace keeko\framework\routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class PageRouter extends AbstractRouter implements RouterInterface {
	
	/*
	 * (non-PHPdoc) @see \keeko\core\routing\AbstractRouter::__construct()
	 */
	public function __construct(array $options) {
		// options
		parent::__construct($options);
		
		// routes
		$routes = new RouteCollection();
		$routes->add('slug', new Route('/{slug}'));
		$routes->add('params', new Route(sprintf('/{slug}%s{params}', $this->options['param-separator'])));
		
		$this->init($routes);
	}
	
	
	/*
	 * (non-PHPdoc) @see \keeko\core\routing\RouteMatcherInterface::match()
	 */
	public function match($destination) {
		if ($destination == '') {
			$destination = '/';
		}
		
		$data = $this->matcher->match($destination);
		
		// find page for matched slug
		if (array_key_exists('slug', $data)) {
// 			$data['page'] = PageQuery::create()
// 				->filterByApplication($this->options['application'])
// 				->useRouteQuery()
// 					->filterBySlug($data['slug'])
// 				->endUse()
// 				->find()
// 			;
		}
		
		// unserialize params
		if (array_key_exists('params', $data)) {
			$data['params'] = $this->unserializeParams($data['params']);
		}
		
		return $data;
	}
	
	/*
	 * @TODO: More data params (e.g. page) (non-PHPdoc) @see \keeko\core\routing\RouteGeneratorInterface::match()
	 */
	public function generate($data) {
		
		// params route
		if (array_key_exists('params', $data)) {
			$data['params'] = $this->serializeParams($data['params']);
			return $this->generator->generate('params', $data);
		}
		
		// slug route
		return $this->generator->generate('slug', $data);
	}
}