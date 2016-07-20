<?php
namespace keeko\framework\foundation;

use keeko\core\model\Application;
use keeko\core\model\ApplicationUri;
use keeko\core\model\Localization;
use keeko\framework\kernel\KernelHandleInterface;
use keeko\framework\page\Page;
use keeko\framework\service\ServiceContainer;
use keeko\framework\utils\TwigRenderTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

abstract class AbstractApplication implements KernelHandleInterface, PackageInterface {

	use TwigRenderTrait;
	use RoutableTrait;

	/** @var ServiceContainer */
	protected $service;

	/** @var Application */
	protected $model;

	/** @var ApplicationUri */
	protected $uri;

	/** @var Page */
	protected $page;

	/** @var Localization */
	protected $localization;

// 	protected $rootUrl;

// 	protected $appPath;

// 	protected $destinationPath;

// 	protected $appUrl;

	/**
	 * Creates a new Keeko Application
	 */
	public function __construct(Application $model, ApplicationUri $uri, ServiceContainer $service) {
		$this->service = $service;
		$this->model = $model;
		$this->page = new Page();
		$this->uri = $uri;
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
	 *
	 * @return ApplicationUri
	 */
	public function getUri() {
		return $this->uri;
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

	public function setCookie(Response $response, $name, $value, $expire = 0) {
		foreach ($this->model->getApplicationUris() as $uri) {
			$cookie = new Cookie($name, $value, $expire, $uri->getBasepath(), $uri->getHttphost(), $uri->getSecure(), false);
			$response->headers->setCookie($cookie);
		}
	}

// 	/**
// 	 * Sets the path of the app url
// 	 *
// 	 * @param string $prefix
// 	 */
// 	public function setAppPath($prefix) {
// 		$this->appPath = $prefix;
// 		$this->updateAppUrl();
// 	}

// 	/**
// 	 * Returns the path of the app url
// 	 *
// 	 * @return string
// 	 */
// 	public function getAppPath() {
// 		return $this->appPath;
// 	}

// 	/**
// 	 * Sets the url of the app url
// 	 *
// 	 * @param string $root
// 	 */
// 	public function setRootUrl($root) {
// 		$this->rootUrl = $root;
// 		$this->updateAppUrl();
// 	}

// 	/**
// 	 * Returns the url of the app url
// 	 *
// 	 * @return string
// 	 */
// 	public function getRootUrl() {
// 		return $this->rootUrl;
// 	}

// 	private function updateAppUrl() {
// 		$this->appUrl = $this->rootUrl . $this->appPath;
// 	}

// 	public function getAppUrl() {
// 		return $this->appUrl;
// 	}

// 	public function setDestinationPath($destination) {
// 		$this->destinationPath = $destination;
// 	}

// 	public function getDestinationPath() {
// 		return $this->destinationPath;
// 	}

// 	/**
// 	 * Returns the full url for the current page
// 	 *
// 	 * @return string
// 	 */
// 	public function getFullUrl() {
// 		return $this->appUrl . $this->destinationPath;
// 	}

// 	/**
// 	 * Set the localization of this app
// 	 *
// 	 * @param Localization $localization
// 	 */
// 	public function setLocalization(Localization $localization) {
// 		$this->localization = $localization;
// 	}

	/**
	 * Returns the localization of this app
	 *
	 * @return Localization
	 */
	public function getLocalization() {
		return $this->uri->getLocalization();
	}

}
