<?php
namespace keeko\framework\foundation;

use Symfony\Component\HttpFoundation\Request;
use keeko\framework\utils\TwigRenderTrait;
use keeko\framework\domain\payload\PayloadInterface;

abstract class AbstractResponder {
	
	use TwigRenderTrait;

	protected $module;

	public function __construct(AbstractModule $module) {
		$this->module = $module;
	}

	/**
	 * Returns the service container
	 *
	 * @return ServiceContainer
	 */
	protected function getServiceContainer() {
		return $this->module->getServiceContainer();
	}

	abstract public function run(Request $request, PayloadInterface $payload);
}