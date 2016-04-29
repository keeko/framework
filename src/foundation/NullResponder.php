<?php
namespace keeko\framework\foundation;

use keeko\framework\foundation\AbstractResponder;
use keeko\framework\domain\payload\PayloadInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NullResponder extends AbstractResponder {
	
	public function run(Request $request, PayloadInterface $payload) {
		return new Response();
	}
}