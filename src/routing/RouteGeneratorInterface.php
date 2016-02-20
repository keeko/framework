<?php
namespace keeko\framework\routing;

interface RouteGeneratorInterface {

	/**
	 * Generates a route for the given data.
	 *
	 * @param mixed $data        	
	 * @return String the route
	 */
	public function generate($data);
}