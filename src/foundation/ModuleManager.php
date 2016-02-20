<?php
namespace keeko\framework\foundation;

use keeko\core\model\Action;
use keeko\core\model\Api;
use keeko\core\model\Group;
use keeko\core\model\GroupQuery;
use keeko\core\model\Module;
use keeko\core\model\ModuleQuery;
use keeko\framework\events\ModuleEvent;
use keeko\framework\exceptions\ModuleException;
use keeko\framework\service\ServiceContainer;
use phootwork\collection\Map;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ModuleManager implements EventSubscriberInterface {
	
	/** @var Map */
	private $loadedModules;

	/** @var Map */
	private $activatedModules;

	/** @var Map */
	private $installedModules;
	
	/** @var ServiceContainer */
	private $service;
	
	public function __construct(ServiceContainer $service) {
		$this->service = $service;
		$this->loadedModules = new Map();
		$this->activatedModules = new Map();
		$this->installedModules = new Map();
		
		$dispatcher = $service->getDispatcher();
		$dispatcher->addSubscriber($this);
		
		// load modules
		$modules = ModuleQuery::create()->find();
		
		foreach ($modules as $module) {
			$this->installedModules->set($module->getName(), $module);
			if ($module->getActivatedVersion() !== null) {
				$this->activatedModules->set($module->getName(), $module);
			}
		}
	}
	
	/**
	 * {@inheritDoc}
	 */
	public static function getSubscribedEvents() {
		return [
			ModuleEvent::INSTALLED => 'moduleUpdated',
			ModuleEvent::UNINSTALLED => 'moduleUninstalled',
			ModuleEvent::UPDATED => 'moduleUpdated',
			ModuleEvent::ACTIVATED => 'moduleActivated',
			ModuleEvent::DEACTIVATED => 'moduleDeactivated'
		];
	}
	
	public function moduleUninstalled(ModuleEvent $e) {
		$module = $e->getModule();
		$this->installedModules->remove($module->getName());
	}
	
	public function moduleUpdated(ModuleEvent $e) {
		$module = $e->getModule();
		$this->installedModules->set($module->getName(), $module);
		if ($this->activatedModules->has($module->getName())) {
			$this->activatedModules->set($module->getName(), $module);
		}
	}
	
	public function moduleActivated(ModuleEvent $e) {
		$module = $e->getModule();
		$this->activatedModules->set($module->getName(), $module);
	}
	
	public function moduleDeactivated(ModuleEvent $e) {
		$module = $e->getModule();
		$this->activatedModules->remove($module->getName());
	}
	
	/**
	 * Returns whether the given package name is an installed module
	 *
	 * @param string $packageName
	 * @return boolean
	 */
	public function isInstalled($packageName) {
		return $this->installedModules->has($packageName);
	}
	
	/**
	 * Returns whether the given package name is an activated module
	 *
	 * @param string $packageName
	 * @return boolean
	 */
	public function isActivated($packageName) {
		return $this->activatedModules->has($packageName);
	}

	
	// public function getInstalledModules() {
	// return $this->installedModules;
	// }
	
	// public function getActivatedModules() {
	// return $this->activatedModules;
	// }
	
	/**
	 * Loads a module and returns the associated class or returns if already loaded
	 *
	 * @param String $packageName
	 * @throws ModuleException
	 * @return AbstractModule
	 */
	public function load($packageName) {
		if ($this->loadedModules->has($packageName)) {
			return $this->loadedModules->get($packageName);
		}
		
		// check existence
		if (!$this->installedModules->has($packageName)) {
			throw new ModuleException(sprintf('Module (%s) does not exist.', $packageName), 500);
		}
		
		// check activation
		if (!$this->activatedModules->has($packageName)) {
			throw new ModuleException(sprintf('Module (%s) not activated', $packageName), 501);
		}
		
		$model = $this->activatedModules->get($packageName);
		
		if ($model->getInstalledVersion() > $model->getActivatedVersion()) {
			throw new ModuleException(sprintf('Module Version Mismatch (%s). Module needs to be updated by the Administrator', $packageName), 500);
		}
		
		// load
		$className = $model->getClassName();
		
		/* @var $module AbstractModule */
		$module = new $className($model, $this->service);
		$this->loadedModules->set($packageName, $module);
		
		return $module;
	}
	
	/**
	 * @TODO still old api
	 * @param string $packageName
	 */
	public function loadTranslations($packageName) {
		// load l10n
// 		$app = $this->service->getKernel()->getApplication();
// 		$translator = $this->service->getTranslator();
// 		$lang = $app->getLocalization()->getLanguage()->getAlpha2();
// 		$country = $app->getLocalization()->getCountry()->getAlpha2();
// 		$l10n = $mod->getPath() . 'l10n/';
// 		$locale = $lang . '_' . $country;
		
		
		
// 		$langPath = sprintf('%s%s/module.json', $l10n, $lang);
// 		$localePath = sprintf('%s%s/module.json', $l10n, $locale);
		
// 		if (file_exists($langPath)) {
// 			$translator->addResource('json', $langPath, $lang, $mod->getCanonicalName());
// 		}
		
// 		if (file_exists($localePath)) {
// 			$translator->addResource('json', $langPath, $locale, $mod->getCanonicalName());
// 		}
	}

// 	public function update($packageName) {
// 		if (!$this->installedModules->has($packageName)) {
// 			throw new ModuleException(sprintf('Module (%s) not installed for activation', $packageName));
// 		}
// 		$model = $this->installedModules->get($packageName);
// 		$model->setActivatedVersion($model->getInstalledVersion());
// 		$model->save();
// 		$module = $this->service->getPackageManager()->getModuleSchema($packageName);
		
// 		// install actions
// 		$extra = $package->getExtra();
// 		if (isset($extra['keeko']) && isset($extra['keeko']['module'])) {
// 			$actions = $this->installActions($model, $extra['keeko']['module']);
// 			$this->installApi($model, $extra['keeko']['module'], $actions);
// 		}
// 	}

	private function installActions(Module $module, $data) {
		if (!isset($data['actions'])) {
			return;
		}
		
		$actions = [];
		
		foreach ($data['actions'] as $name => $options) {
			$a = new Action();
			$a->setName($name);
			$a->setModule($module);
			
			if (isset($options['title'])) {
				$a->setTitle($options['title']);
			}
			
			if (isset($options['description'])) {
				$a->setDescription($options['description']);
			}
			
			if (isset($options['class'])) {
				$a->setClassName($options['class']);
			}
			
			// add acl
			if (isset($options['acl'])) {
				foreach ($options['acl'] as $group) {
					$a->addGroup($this->getGroup($group));
				}
			}
			
			$a->save();
			$actions[$name] = $a->getId();
		}
		
		return $actions;
	}
	
	/**
	 * @param string $name
	 * @return Group
	 */
	private function getGroup($name) {
		switch ($name) {
			case 'guest':
				if ($this->guestGroup === null) {
					$this->guestGroup = GroupQuery::create()->filterByIsGuest(true)->findOne();
				}
				return $this->guestGroup;

			case 'user':
				if ($this->userGroup === null) {
					$this->userGroup = GroupQuery::create()->filterByIsDefault(true)->findOne();
				}
				return $this->userGroup;
				
			case 'admin':
				if ($this->adminGroup === null) {
					$this->adminGroup = GroupQuery::create()->findOneById(3);
				}
				return $this->adminGroup;
		}
	}

	private function installApi(Module $module, $data, $actionMap) {
		if (!isset($data['api'])) {
			return;
		}
		
		if (!isset($data['api']['apis'])) {
			return;
		}
		
		$base = '/';
		if (isset($data['api']['resourcePath'])) {
			$base = $data['api']['resourcePath'];
		}
		
		foreach ($data['api']['apis'] as $apis) {
			$path = $apis['path'];
			foreach ($apis['operations'] as $op) {
				// fetch required params
				$required = [];
				if (isset($op['parameters'])) {
					foreach ($op['parameters'] as $param) {
						if (isset($param['paramType']) && $param['paramType'] == 'path') {
							$required[] = $param['name'];
						}
					}
				}
				
				// create record
				$fullPath = str_replace('//', '/', $base . '/' . $path);
				$api = new Api();
				$api->setMethod($op['method']);
				$api->setRoute($fullPath);
				$api->setActionId($actionMap[$op['nickname']]);
				$api->setRequiredParams(implode(',', $required));
				$api->save();
			}
		}
		
		$module->setApi(true);
		$module->save();
	}

	public function deactivate($packageName) {
		if (array_key_exists($packageName, $this->activatedModules) && !array_key_exists($packageName, $this->installedModules)) {
			
			$mod = ModuleQuery::create()->filterByName($packageName)->findOne();
			$mod->setActivatedVersion(null);
			$mod->save();
			
			unset($this->activatedModules[$packageName]);
		}
	}
	
	// /**
	// * Returns wether a module was loaded
	// *
	// * @param String $packageName
	// * @return boolean true if loaded, false if not
	// */
	// public function isLoaded($packageName) {
	// return array_key_exists($packageName, $this->loadedModules);
	// }
}
