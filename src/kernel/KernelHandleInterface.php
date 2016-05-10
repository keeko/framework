<?php
namespace keeko\framework\kernel;

use keeko\framework\foundation\PackageEntityInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface KernelHandleInterface extends PackageEntityInterface {

	/**
	 * Runs the particular target
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function run(Request $request);
}
