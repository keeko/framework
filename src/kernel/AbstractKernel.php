<?php
namespace keeko\framework\kernel;

use keeko\framework\events\KernelTargetEvent;
use keeko\framework\service\ServiceContainer;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
	 * @param RunnableInterface $target
	 * @param Request $request
	 * @return Response
	 */
	public function handle(KernelTargetInterface $target, Request $request) {
		$event = new KernelTargetEvent($target);
		
		$this->dispatcher->dispatch(KernelTargetEvent::BEFORE_RUN, $event);
		$response = $target->run($request);
		$this->dispatcher->dispatch(KernelTargetEvent::AFTER_RUN, $event);
	
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