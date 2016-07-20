<?php
namespace keeko\framework\kernel;

use keeko\framework\routing\ApplicationRouter;
use Symfony\Component\HttpFoundation\Request;
use keeko\framework\foundation\AbstractApplication;
use keeko\framework\events\KernelHandleEvent;
use Symfony\Component\HttpFoundation\Response;
use phootwork\collection\Map;
use keeko\framework\service\ServiceContainer;
use keeko\framework\service\PuliService;
use keeko\core\CoreModule;
use keeko\core\model\ApplicationUriQuery;
use Symfony\Component\HttpFoundation\Cookie;

class WebKernel {

	/** @var AbstractApplication */
	protected $app;

	/** @var ServiceContainer */
	protected $service;

	/** @var EventDispatcher */
	protected $dispatcher;

	public function __construct(PuliService $puli = null) {
		$this->service = new ServiceContainer($puli);
		$this->service->setKernel($this);
		$this->dispatcher = $this->service->getDispatcher();

		$this->registerListeners();
	}

	public function setCookie(Response $response, $name, $value, $expire = 0) {
		$uris = ApplicationUriQuery::create()->find();
		foreach ($uris as $uri) {
			$cookie = new Cookie($name, $value, $expire, $uri->getBasepath(), $uri->getHttphost(), $uri->getSecure(), false);
			$response->headers->setCookie($cookie);
		}
	}

	private function registerListeners() {
		$reg = $this->service->getExtensionRegistry();
		$listeners = $reg->getExtensions(CoreModule::EXT_LISTENER);

		$map = new Map();
		$getClass = function ($className) use ($map) {
			if (class_exists($className)) {
				if ($map->has($className)) {
					$class = $map->get($className);
				} else {
					$class = new $className();
					$map->set($className, $class);
				}
				if ($class instanceof KeekoEventListenerInterface) {
					$class->setServiceContainer($this->service);
				}
				return $class;
			}
			return null;
		};

		foreach ($listeners as $listener) {
			// subscriber first
			if (isset($listener['subscriber'])) {
				$className = $listener['subscriber'];
				$subscriber = $getClass($className);
				if ($subscriber !== null && $subscriber instanceof KeekoEventSubscriberInterface) {
					$this->dispatcher->addSubscriber($subscriber);
				}
			}

			// class
			if (isset($listener['class']) && isset($listener['method']) && isset($listener['event'])) {
				$className = $listener['class'];
				$class = $getClass($className);
				if ($class !== null
					&& $class instanceof KeekoEventListenerInterface
					&& method_exists($class, $listener['method'])) {
						$this->dispatcher->addListener($listener['event'], [$class, $listener['method']]);
					}
			}
		}
	}

	/**
	 *
	 * @return Response
	 */
	public function process() {
		try {
			$request = Request::createFromGlobals();

			// no trailing slashes in urls
			// redirect unless it's the root url
			$syspref = $this->service->getPreferenceLoader()->getSystemPreferences();
			if (substr($request->getUri(), -1) == '/'
					&& substr($request->getUri(), 0, -1) != $syspref->getRootUrl()) {
				$this->redirect(rtrim($request->getUri(), '/'));
			}

			$router = new ApplicationRouter();

			$uri = $router->match($request);
			$model = $uri->getApplication();

// 			printf('<p><br><br></p><p>Basepath: %s<br>
// 					Pathinfo: %s<br>
// 					Baseurl: %s<br>
// 					Host: %s<br>
// 					HttpHost: %s<br>
// 					Requsturi: %s<br>
// 					Uri: %s<br>
// 					Port: %s<br>
// 					Secure: %s<br><br>APP<br>
// 					Basepath: %s<br>
// 					Host: %s<br>
// 					Secure: %s
// 					</p>',
// 					$request->getBasePath(),
// 					$request->getPathInfo(),
// 					$request->getBaseUrl(),
// 					$request->getHost(),
// 					$request->getHttpHost(),
// 					$request->getRequestUri(),
// 					$request->getUri(),
// 					$request->getPort(),
// 					$request->isSecure() ? 'yes' : 'no',
// 					$uri->getBasepath(),
// 					$uri->getHttphost(),
// 					$uri->getSecure() ? 'yes' : 'no');

			// set locale
			$localization = $uri->getLocalization();
			$this->service->getLocaleService()->setLocale($localization->getLocale());

			// init app
			$class = $model->getClassName();
			$app = new $class($model, $uri, $this->service);
			$app->setBaseUrl($uri->getUrl());
			$app->setDestination($router->getDestination());
// 			$app->setRootUrl($root);
// 			$app->setAppPath($router->getPrefix());
// 			$app->setDestinationPath($router->getDestination());
			$this->app = $app;

			$response = $this->handle($app, $request);

			if ($response instanceof RedirectResponse) {
				$response->sendHeaders();
				$this->redirect($response->getTargetUrl());
			}

			$response->prepare($request);

			return $response;
		} catch (\Exception $e) {
			printf('<b>[%s] %s</b><pre>%s</pre>', get_class($e), $e->getMessage(), $e->getTraceAsString());
		}
	}

	private function redirect($url) {
		header('Location: ' . $url);
		exit(0);
	}

	/**
	 * Runs a kernel target
	 *
	 * @param KernelHandleWebInterface $target
	 */
	public function handle(KernelHandleInterface $target, Request $request) {
		$event = new KernelHandleEvent($target);

		$this->dispatcher->dispatch(KernelHandleEvent::PRE_RUN, $event);
		$response = $target->run($request);
		$this->dispatcher->dispatch(KernelHandleEvent::POST_RUN, $event);

		return $response;
	}

	/**
	 * Returns the processed application
	 *
	 * @return AbstractApplication
	 */
	public function getApplication() {
		return $this->app;
	}
}
