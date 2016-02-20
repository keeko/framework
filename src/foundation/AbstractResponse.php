<?php
namespace keeko\framework\foundation;

use Symfony\Component\HttpFoundation\Request;
use keeko\framework\utils\TwigRenderTrait;

abstract class AbstractResponse {
	
	use TwigRenderTrait;

	protected $data = [];

	protected $twig;

	protected $module;

	public function __construct(AbstractModule $module, $format) {
		$this->module = $module;
		$templatePath = sprintf('%s/%s/templates/%s', KEEKO_PATH_MODULES, $module->getModel()->getName(), $format);
		
		if (file_exists($templatePath)) {
			$loader = new \Twig_Loader_Filesystem($templatePath);
			$this->twig = new \Twig_Environment($loader);
		}
	}

	/**
	 * Returns the service container
	 *
	 * @return ServiceContainer
	 */
	protected function getServiceContainer() {
		return $this->module->getServiceContainer();
	}

	public function setData($data) {
		$this->data = $data;
	}
	
	protected function getTwig() {
		return $this->module->getTwig();
	}

	abstract public function run(Request $request, $data = null);
}