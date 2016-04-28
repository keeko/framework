<?php
namespace keeko\framework\foundation;

use keeko\core\model\Application;
use keeko\core\model\Localization;
use keeko\framework\kernel\KernelTargetInterface;
use keeko\framework\kernel\Page;
use keeko\framework\service\ServiceContainer;
use keeko\framework\utils\TwigRenderTrait;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractApplication implements KernelTargetInterface {
	
	use TwigRenderTrait;

	/** @var Application */
	protected $model;

	/** @var Localization */
	protected $localization;

	protected $rootUrl;
	
	protected $appPath;

	protected $destinationPath;
	
	protected $appUrl;
	
	/** @var ServiceContainer */
	protected $service;
	
	/** @var Page */
	protected $page;

	/**
	 * Creates a new Keeko Application
	 */
	public function __construct(Application $model, ServiceContainer $service) {
		$this->model = $model;
		$this->service = $service;
		$this->page = new Page();
	}
	
	/**
	 * Returns the applications name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->model->getName();
	}
	
	/**
	 * Returns the applications canonical name
	 *
	 * @return string
	 */
	public function getCanonicalName() {
		return str_replace('/', '.', $this->model->getName());
	}
	
	/**
	 * Returns the applications title
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->model->getTitle();
	}
	
	/**
	 * Returns the service container
	 *
	 * @return ServiceContainer
	 */
	public function getServiceContainer() {
		return $this->service;
	}
	
	/**
	 * @return Page
	 */
	public function getPage() {
		return $this->page;
	}
	
	/**
	 * Returns the associated application model
	 *
	 * @return Application
	 */
	public function getModel() {
		return $this->model;
	}

	public function setAppPath($prefix) {
		$this->appPath = $prefix;
		$this->updateAppUrl();
	}

	public function getAppPath() {
		return $this->appPath;
	}

	public function setRootUrl($root) {
		$this->rootUrl = $root;
		$this->updateAppUrl();
	}
	
	public function getRootUrl() {
		return $this->rootUrl;
	}
	
	private function updateAppUrl() {
		$this->appUrl = $this->rootUrl . $this->appPath;
	}
	
	public function getAppUrl() {
		return $this->appUrl;
	}
	
	public function setDestinationPath($destination) {
		$this->destinationPath = $destination;
	}

	public function getDestinationPath() {
		return $this->destinationPath;
	}
	
// 	public function getTargetPath() {
// 		return $this->destinationPath;
// 	}
	
// 	public function getTailPath() {
// 		return $this->destinationPath;
// 	}
	
	/**
	 * Returns the full url for the current page
	 * 
	 * @return string
	 */
	public function getFullUrl() {
		return $this->appUrl . $this->destinationPath;
	}

	/**
	 * Set the localization of this app
	 * 
	 * @param Localization $localization
	 */
	public function setLocalization(Localization $localization) {
		$this->localization = $localization;
	}

	/**
	 * Returns the localization of this app
	 * 
	 * @return Localization
	 */
	public function getLocalization() {
		return $this->localization;
	}
	
// 	protected function runAction(AbstractAction $action, Request $request) {
// 		$runner = $this->getServiceContainer()->getRunner();
// 		return $runner->run($action, $request);
// 	}
	
	abstract public function run(Request $request);
	
}