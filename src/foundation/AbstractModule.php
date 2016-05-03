<?php
namespace keeko\framework\foundation;

use keeko\core\model\Action;
use keeko\core\model\ActionQuery;
use keeko\core\model\Module;
use keeko\core\model\User;
use keeko\framework\exceptions\ModuleException;
use keeko\framework\exceptions\PermissionDeniedException;
use keeko\framework\schema\ActionSchema;
use keeko\framework\schema\PackageSchema;
use keeko\framework\service\ServiceContainer;

abstract class AbstractModule {
	
	/** @var Module */
	protected $model;

	protected $actions;

	/** @var User */
	protected $user;
	
	/** @var ServiceContainer */
	protected $service;
	
	/** @var Preferences */
	private $preferences;
	
	/** @var PackageSchema */
	protected $package;

	public function __construct(Module $module, ServiceContainer $service) {
		$this->model = $module;
		$this->service = $service;
		
		$packageManager = $service->getPackageManager();
		$this->package = $packageManager->getPackage($module->getName());
		
		$this->loadActions();
	}
	
	/**
	 * Returns the modules name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->model->getName();
	}
	
	/**
	 * Returns the modules canonical name
	 *
	 * @return string
	 */
	public function getCanonicalName() {
		return str_replace('/', '.', $this->getName());
	}
	
	/**
	 * Returns the modules title
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
	 * Returns the associated module model
	 *
	 * @return Module
	 */
	public function getModel() {
		return $this->model;
	}
	
	
	
	public function getPath() {
		return sprintf('%s/%s/', KEEKO_PATH_MODULES, $this->model->getName());
	}
	
	public function getManagedFilesPath() {
		return sprintf('%s/managed/%s', KEEKO_PATH_FILES, $this->model->getName());
	}
	
	public function getManagedFilesUrl() {
		return sprintf('%s/files/managed/%s', $this->getServiceContainer()->getApplication()->getRootUrl(), $this->model->getName());
	}
	
	/**
	 * Returns the module's preferences
	 *
	 * @return Preferences
	 */
	public function getPreferences() {
		if ($this->preferences === null) {
			$this->preferences = $this->service->getPreferenceLoader()->getModulePreferences($this->model->getId());
		}
		
		return $this->preferences;
	}

	private function loadActions() {
		$models = [];
		$actions = ActionQuery::create()->filterByModule($this->model)->find();
		foreach ($actions as $action) {
			$models[$action->getName()] = $action;
		}
		$keeko = $this->package->getKeeko();
		$module = $keeko->getModule();
	
		
		$this->actions = [];
		foreach ($module->getActionNames() as $actionName) {
			if (isset($models[$actionName])) {
				$this->actions[$actionName] = [
					'model' => $models[$actionName],
					'action' => $module->getAction($actionName)
				];
			}
		}
	}

	/**
	 * Loads the given action
	 *
	 * @param Action|string $actionName
	 * @param string $response the response type (e.g. html, json, ...)
	 * @return AbstractAction
	 */
	public function loadAction($nameOrAction, $response = null) {
		$model = null;
		if ($nameOrAction instanceof Action) {
			$model = $nameOrAction;
			$actionName = $nameOrAction->getName();
		} else {
			$actionName = $nameOrAction;
		}
		
		if (!isset($this->actions[$actionName])) {
			throw new ModuleException(sprintf('Action (%s) not found in Module (%s)', $actionName, $this->model->getName()));
		}
		
		if ($model === null) {
			$model = $this->actions[$actionName]['model'];
		}
		
		/* @var $action ActionSchema */
		$action = $this->actions[$actionName]['action'];
		
		
		// check permission
		if (!$this->service->getFirewall()->hasActionPermission($model)) {
			throw new PermissionDeniedException(sprintf('Can\'t access Action (%s) in Module (%s)', $actionName, $this->model->getName()));
		}
		
		// check if a response is given
		if ($response !== null) {
			if (!$action->hasResponse($response)) {
				throw new ModuleException(sprintf('No Response (%s) given for Action (%s) in Module (%s)', $response, $actionName, $this->model->getName()));
			}
			$responseClass = $action->getResponse($response);
			
			if (!class_exists($responseClass)) {
				throw new ModuleException(sprintf('Response (%s) not found in Module (%s)', $responseClass, $this->model->getName()));
			}
			$response = new $responseClass($this);
		} else {
			$response = new NullResponder($this);
		}
		
		// gets the action class
		$className = $model->getClassName();
		
		if (!class_exists($className)) {
			throw new ModuleException(sprintf('Action (%s) not found in Module (%s)', $className, $this->model->getName()));
		}
		
		$class = new $className($model, $this, $response);
		
		
		// l10n
		// ------------
		
		$localeService = $this->getServiceContainer()->getLocaleService();
		
		// load module l10n
		$file = sprintf('/%s/locales/{locale}/module.json', $this->package->getFullName());
		$localeService->loadLocaleFile($file, $class->getCanonicalName());
		
		// load additional l10n files
		foreach ($action->getL10n() as $file) {
			$file = sprintf('/%s/locales/{locale}/%s', $this->package->getFullName(), $file);
			$localeService->loadLocaleFile($file, $class->getCanonicalName());
		}

		// load action l10n
		$file = sprintf('/%s/locales/{locale}/actions/%s', $this->package->getFullName(), $actionName);
		$localeService->loadLocaleFile($file, $class->getCanonicalName());
		
		
		// assets
		// ------------
		$app = $this->getServiceContainer()->getKernel()->getApplication();
		$page = $app->getPage();
		
		// scripts
		foreach ($action->getScripts() as $script) {
			$page->addScript($script);
		}
		
		// styles
		foreach ($action->getStyles() as $style) {
			$page->addStyle($style);
		}

		return $class;
	}
	
	/**
	 * Shortcut for getting permission on the given action in this module
	 *
	 * @param string $action
	 * @param User $user
	 */
	public function hasPermission($action, User $user = null) {
		return $this->getServiceContainer()->getFirewall()->hasPermission($this->getName(), $action, $user);
	}

	abstract public function install();

	abstract public function uninstall();

	abstract public function update($from, $to);
}
