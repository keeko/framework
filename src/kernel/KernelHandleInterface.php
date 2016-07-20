<?php
namespace keeko\framework\kernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface KernelHandleInterface {

	/**
	 * Runs the particular target
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function run(Request $request);
}
