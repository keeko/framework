<?php
namespace keeko\framework\foundation;

use Symfony\Component\HttpFoundation\Request;
use keeko\framework\domain\payload\PayloadInterface;

abstract class AbstractPayloadResponder extends AbstractResponder {

	abstract protected function getPayloadMethods();
	
	public function run(Request $request, PayloadInterface $payload) {
		$class = get_class($payload);
		$methods = $this->getPayloadMethods();
		if (!isset($methods['keeko\framework\domain\payload\Error'])) {
			$methods['keeko\framework\domain\payload\Error'] = 'error';
		}

		$method = isset($methods[$class]) ? $methods[$class] : 'notRecognized';
		return $this->$method($request, $payload);
	}
	
	protected function notRecognized(Request $request, PayloadInterface $payload) {
		throw new \Exception('Unknown payload ' . get_class($payload) . ' for ' . get_class($this));
	}
	
	protected function error(Request $request, PayloadInterface $payload) {
		throw new \Exception('Unknown error.');
	}
}