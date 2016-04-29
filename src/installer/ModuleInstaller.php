<?php
namespace keeko\framework\installer;

use Composer\IO\IOInterface;
use gossi\swagger\Parameter;
use gossi\swagger\Path;
use gossi\swagger\Swagger;
use keeko\core\model\Action;
use keeko\core\model\ActionQuery;
use keeko\core\model\Api;
use keeko\core\model\ApiQuery;
use keeko\core\model\Group;
use keeko\core\model\GroupQuery;
use keeko\core\model\Module;
use keeko\core\model\ModuleQuery;
use keeko\framework\events\ModuleEvent;
use keeko\framework\schema\ModuleSchema;
use keeko\framework\service\ServiceContainer;
use phootwork\json\Json;

class ModuleInstaller extends AbstractPackageInstaller {
	
	/** @var ModuleManager */
	private $manager;
	
	/** @var Group */
	private $guestGroup;
	
	/** @var Group */
	private $userGroup;
	
	/** @var Group */
	private $adminGroup;
	
	public function __construct(ServiceContainer $service) {
		parent::__construct($service);
		$this->manager = $this->service->getModuleManager();
	}

	public function install(IOInterface $io, $packageName) {
		$io->write('[Keeko] Install Module: ' . $packageName);
		
		$package = $this->getPackageSchema($packageName);
		$keeko = $package->getKeeko();
		
		if ($keeko->isModule()) {
			$pkg = $keeko->getModule();

			// create module
			$model = new Module();
			$model->setClassName($pkg->getClass());
			$model->setSlug($pkg->getSlug());
			$this->updatePackage($model, $pkg);
			
			// run module -> install
			$className = $pkg->getClass();
			$class = new $className($model, $this->service);
			$class->install();
			
			$this->dispatcher->dispatch(ModuleEvent::INSTALLED, new ModuleEvent($model));
		}
	}

	public function update(IOInterface $io, $packageName, $from, $to) {
		$io->write(sprintf('[Keeko] Update Module: %s from %s to %s', $packageName, $from, $to));
		
		// retrieve module
		$model = ModuleQuery::create()->findOneByName($packageName);
		$this->updatePackage($model, $packageName);
		
		// run module -> update
		$className = $model->getClass();
		$class = new $className($model, $this->service);
		$class->update($from, $to);
		
		// update api and actions
		if ($this->manager->isActivated($packageName)) {
			$this->updateModule($model);
		}
		
		$this->dispatcher->dispatch(ModuleEvent::UPDATED, new ModuleEvent($model));
	}

	public function uninstall(IOInterface $io, $packageName) {
		$io->write('[Keeko] Uninstall Module: ' . $packageName);

		// retrieve module
		$model = ModuleQuery::create()->findOneByName($packageName);

		// delete if found
		if ($model !== null) {
			$model->delete();
			
			// TODO: Check if api and actions are also deleted (by the call above)
		}

		$this->dispatcher->dispatch(ModuleEvent::UNINSTALLED, new ModuleEvent($model));
	}
	
	public function activate(IOInterface $io, $packageName) {
		$io->write('[Keeko] Activate Module: ' . $packageName);
		
		$package = $this->service->getPackageManager()->getComposerPackage($packageName);
		
		$model = ModuleQuery::create()->findOneByName($packageName);
		$model->setActivatedVersion($package->getPrettyVersion());
		$model->save();
		
		$this->updateModule($model);
	}
	
	private function updateModule(Module $model) {
		$package = $this->service->getPackageManager()->getPackage($model->getName());
		$keeko = $package->getKeeko();

		if ($keeko->isModule()) {
			$module = $keeko->getModule();
			$actions = $this->updateActions($model, $module);
			$this->updateApi($model, $module, $actions);
		}
	}
	
	private function updateActions(Module $model, ModuleSchema $module) {
		$actions = [];
	
		foreach ($module->getActionNames() as $name) {
			$action = $module->getAction($name);
			$a = new Action();
			$a->setName($name);
			$a->setModule($model);
			$a->setTitle($action->getTitle());
			$a->setDescription($action->getDescription());
			$a->setClassName($action->getClass());
				
			// add acl
			foreach ($action->getAcl() as $group) {
				$a->addGroup($this->getGroup($group));
			}
				
			$a->save();
			$actions[$name] = $a->getId();
		}
		
		// remove obsolete actions
		ActionQuery::create()
			->filterByModule($model)
			->where('Action.Name NOT IN ?', $module->getActionNames()->toArray())
			->delete();

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
	
	private function updateApi(Module $model, ModuleSchema $module, $actions) {
		$repo = $this->service->getResourceRepository();
		$filename = sprintf('/packages/%s/api.json', $model->getName());
		if (!$repo->contains($filename)) {
			return;
		}

		// delete every api existent for the given module prior to create the new ones
		ApiQuery::create()
			->filterByActionId(array_values($actions))
			->delete()
		;
	
// 		$extensions = $this->service->getExtensionRegistry()->getExtensionsByPackage('keeko.api', $model->getName());
		
		$json = Json::decode($repo->get($filename)->getBody());
		$swagger = new Swagger($json);
		foreach ($swagger->getPaths() as $path) {
			/* @var $path Path */
			foreach (Swagger::$METHODS as $method) {
				if ($path->hasOperation($method)) {
					$op = $path->getOperation($method);
					$actionName = $op->getOperationId();

					if (!isset($actions[$actionName])) {
						continue;
					}
					
					// find required parameters
					$required = [];
					
					foreach ($op->getParameters() as $param) {
						/* @var $param Parameter */
						if ($param->getIn() == 'path' && $param->getRequired()) {
							$required[] = $param->getName();
						}
					}
					
// 					$prefix = isset($extensions[$actionName])
// 						? $extensions[$actionName]
// 						: $module->getSlug();

					$prefix = $module->getSlug();
					
					$fullPath = str_replace('//', '/', $prefix . '/' . $path->getPath());
					$api = new Api();
					$api->setMethod($method);
					$api->setRoute($fullPath);
					$api->setActionId($actions[$actionName]);
					$api->setRequiredParams(implode(',', $required));
					$api->save();
				}
			}
		}
	
		$model->setApi(true);
		$model->save();
	}
	
	public function deactivate(IOInterface $io, $packageName) {
		$io->write('[Keeko] Deactivate Module: ' . $packageName);
		
		$mod = ModuleQuery::create()->filterByName($packageName)->findOne();
		$mod->setActivatedVersion(null);
		$mod->save();
	}
}