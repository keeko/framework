<?php
namespace keeko\framework\security;

use keeko\core\model\Action;
use keeko\core\model\GroupQuery;
use keeko\core\model\User;
use keeko\framework\service\ServiceContainer;
use phootwork\collection\Map;
use phootwork\collection\Set;

class Firewall {
	
	/** @var ServiceContainer */
	private $service;
	
	/** @var Map */
	private $permissionTables;

	public function __construct(ServiceContainer $service) {
		$this->service = $service;
		$this->user = $service->getAuthManager()->getUser();
		$this->permissionTables = new Map();
	}
	
	public function hasPermission($module, $action, User $user = null) {
		$module = $this->service->getModuleManager()->load($module);
		$action = $module->getActionModel($action);
		return $this->hasActionPermission($action, $user);
	}

	public function hasActionPermission(Action $action, User $user = null) {
		if ($user === null) {
			$user = $this->user;
		}
		$permissionTable = $this->getPermissionTable($user);

		return $permissionTable->contains($action->getId());
	}

	/**
	 * Returns a set of allowed action ids
	 *
	 * @param User $user
	 * @return Set
	 */
	private function getPermissionTable(User $user) {
		$userId = $user->getId();
		if ($this->permissionTables->has($userId)) {
			return $this->permissionTables->get($userId);
		}
		
		// always allow what guests can do
		$guestGroup = GroupQuery::create()->findOneByIsGuest(true);
		
		// collect groups from user
		$groups = GroupQuery::create()->filterByUser($user)->find();
		$userGroup = GroupQuery::create()->filterByOwnerId(($userId))->findOne();
		if ($userGroup) {
			$groups[] = $userGroup;
		}
		$groups[] = $guestGroup;
		
		// ... structure them
		$permissionTable = new Set();
		foreach ($groups as $group) {
			foreach ($group->getActions() as $action) {
				$permissionTable->add($action->getId());
			}
		}
		
		$this->permissionsTables->set($userId, $permissionTable);
		
		return $this->permissionsTables->get($userId);
	}
	
	private function isGuest(User $user) {
		return $user->getId() === -1;
	}
}