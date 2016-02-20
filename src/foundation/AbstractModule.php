<?php
namespace keeko\framework\foundation;

use keeko\core\model\Action;
use keeko\core\model\ActionQuery;
use keeko\core\model\Module;
use keeko\core\model\User;
use keeko\framework\exceptions\ModuleException;
use keeko\framework\exceptions\PermissionDeniedException;
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
	public function loadAction($nameOrAction, $response) {
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
		if (!$action->hasResponse($response)) {
			throw new ModuleException(sprintf('No Response (%s) given for Action (%s) in Module (%s)', $response, $actionName, $this->model->getName()));
		}
		$responseClass = $action->getResponse($response);
		
		if (!class_exists($responseClass)) {
			throw new ModuleException(sprintf('Response (%s) not found in Module (%s)', $responseClass, $this->model->getName()));
		}
		$response = new $responseClass($this, $response);
		
		// gets the action class
		$className = $model->getClassName();
		
		if (!class_exists($className)) {
			throw new ModuleException(sprintf('Action (%s) not found in Module (%s)', $className, $this->model->getName()));
		}
		
		$class = new $className($model, $this, $response);
		
// 		// l10n
// 		$app = $this->getServiceContainer()->getApplication();
// 		$lang = $app->getLocalization()->getLanguage()->getAlpha2();
// 		$country = $app->getLocalization()->getCountry()->getAlpha2();
// 		$l10n = $this->getPath() . 'l10n/';
// 		$locale = $lang . '_' . $country;
		
// 		// load module l10n
// 		$this->addL10nFile('module', $l10n, $lang, $locale, $class);
		
// 		// load additional l10n files
// 		if (isset($this->actions[$actionName]['l10n'])) {
// 			foreach ($this->actions[$actionName]['l10n'] as $file) {
// 				$this->addL10nFile($file, $l10n, $lang, $locale, $class);
// 			}
// 		}
			
// 		// load action l10n
// 		$this->addL10nFile(sprintf('actions/%s', $actionName), $l10n, $lang, $locale, $class);
		
// 		// assets
// 		$page = $app->getPage();
// 		$moduleUrl = sprintf('%s/_keeko/modules/%s/', $app->getRootUrl(), $this->getName());
// 		if (isset($this->actions[$actionName]['assets']['styles'])) {
// 			foreach ($this->actions[$actionName]['assets']['styles'] as $style) {
// 				$page->addStyle($moduleUrl . $style);
// 			}
// 		}
		
// 		if (isset($this->actions[$actionName]['assets']['scripts'])) {
// 			foreach ($this->actions[$actionName]['assets']['scripts'] as $script) {
// 				$page->addScript($moduleUrl . $script);
// 			}
// 		}

		return $class;
	}
	
	private function addL10nFile($file, $dir, $lang, $locale, $class) {
		$translator = $this->getServiceContainer()->getTranslator();
		$langPath = sprintf('%s%s/%s.json', $dir, $lang, $file);
		$localePath = sprintf('%s%s/%s.json', $dir, $locale, $file);
		
		if (file_exists($langPath)) {
			$translator->addResource('json', $langPath, $lang, $class->getCanonicalName());
		}
		
		if (file_exists($localePath)) {
			$translator->addResource('json', $langPath, $locale, $class->getCanonicalName());
		}
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
