<?php
namespace keeko\framework\kernel;

use keeko\core\CoreModule;
use keeko\framework\events\KeekoEventListenerInterface;
use keeko\framework\events\KeekoEventSubscriberInterface;
use keeko\framework\service\PuliService;
use keeko\framework\service\ServiceContainer;
use phootwork\collection\Map;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class AbstractKernel {

	/** @var ServiceContainer */
	protected $service;

	/** @var EventDispatcher */
	protected $dispatcher;

	public function __construct(PuliService $puli = null) {
		$this->service = new ServiceContainer($this, $puli);
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
	 */
	abstract public function process(array $options = []);

	/**
	 * Runs a kernel target
	 *
	 * @param KernelHandleInterface $target
	 */
	abstract public function handle(KernelHandleInterface $target);

	/**
	 * Returns the service container
	 *
	 * @return ServiceContainer
	 */
	public function getServiceContainer() {
		return $this->service;
	}

}