<?php
namespace keeko\framework\kernel;

use keeko\framework\events\KernelHandleEvent;
use keeko\framework\foundation\AbstractApplication;
use keeko\framework\service\ServiceContainer;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use keeko\core\CoreModule;
use phootwork\collection\Map;
use keeko\framework\events\KeekoEventSubscriberInterface;
use keeko\framework\events\KeekoEventListenerInterface;

abstract class AbstractKernel {
	
	/** @var AbstractApplication */
	protected $app;
	
	/** @var ServiceContainer */
	protected $service;
	
	/** @var EventDispatcher */
	protected $dispatcher;
	
	public function __construct() {
		$this->service = new ServiceContainer($this);
		$this->dispatcher = $this->service->getDispatcher();

		$this->registerListeners();
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
	 * Processes the main kernel action
	 *
	 * @return Response
	 */
	abstract public function process(array $options = []);
	
	/**
	 * Runs a kernel target
	 *
	 * @param KernelTargetInterface $target
	 * @param Request $request
	 * @return Response
	 */
	public function handle(KernelHandleInterface $target, Request $request) {
		$event = new KernelHandleEvent($target);
		
		$this->dispatcher->dispatch(KernelHandleEvent::PRE_RUN, $event);
		$response = $target->run($request);
		$this->dispatcher->dispatch(KernelHandleEvent::POST_RUN, $event);
	
		return $response;
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
	 * Returns the processed application
	 *
	 * @return AbstractApplication
	 */
	public function getApplication() {
		return $this->app;
	}
}