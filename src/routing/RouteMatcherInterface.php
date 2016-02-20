<?php
namespace keeko\framework\routing;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

interface RouteMatcherInterface {

	/**
	 * Matches a route for the given request
	 *
	 * @throws MethodNotAllowedException
	 * @throws ResourceNotFoundException
	 * @param mixed $request        	
	 * @return mixed data for the route
	 */
	public function match($request);
}