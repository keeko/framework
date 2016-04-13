<?php
namespace keeko\framework\foundation;

use keeko\core\model\Action;
use keeko\framework\kernel\KernelTargetInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractAction implements KernelTargetInterface {
	
	/** @var Action */
	protected $model;

	/** @var AbstractModule */
	protected $module;

	/** @var array */
	protected $params = [];

	/** @var AbstractResponder */
	protected $responder;
	
	private $domainBackup;

	public function __construct(Action $model, AbstractModule $module, AbstractResponder $responder) {
		$this->model = $model;
		$this->module = $module;
		$this->responder = $responder;
	}
	
	/**
	 * Returns the actions name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->model->getName();
	}
	
	/**
	 * Returns the canonical actions name
	 *
	 * @return string
	 */
	public function getCanonicalName() {
		return $this->module->getCanonicalName() . '.' . $this->model->getName();
	}
	
	/**
	 * Returns the actions title
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
	protected function getServiceContainer() {
		return $this->module->getServiceContainer();
	}
	
	/**
	 * Returns the module's preferences
	 *
	 * @return Preferences
	 */
	protected function getPreferences() {
		return $this->module->getPreferences();
	}

	public function setParams($params) {
		$resolver = new OptionsResolver();
		$this->configureParams($resolver);
		$this->params = $resolver->resolve($params);
	}

	protected function configureParams(OptionsResolver $resolver) {
		// does nothing, extend this method and provide functionality for your action
	}

	/**
	 * Returns the param
	 *
	 * @param string $name the param name
	 * @return mixed
	 */
	protected function getParam($name) {
		return $this->params[$name];
	}
	
	/**
	 * Returns the associated action model
	 *
	 * @return Action
	 */
	public function getModel() {
		return $this->model;
	}

	/**
	 * Returns the associated module
	 *
	 * @return AbstractModule
	 */
	protected function getModule() {
		return $this->module;
	}
	
	abstract public function run(Request $request);

}