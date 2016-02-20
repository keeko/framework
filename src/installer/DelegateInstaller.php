<?php
namespace keeko\framework\installer;

use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\Package\PackageInterface;
use Composer\Script\PackageEvent;

class DelegateInstaller {

	protected function installPackage(PackageEvent $event) {
// 		if (!self::bootstrap()) {
// 			return;
// 		}
		
// 		$operation = $event->getOperation();
// 		if ($operation instanceof InstallOperation) {
// 			/* @var $operation InstallOperation */
// 			$package = $operation->getPackage();
// 			$installer = self::getInstaller($package);
// 			$installer->install($event->getIO(), $package);
// 		}
	}

	protected function updatePackage(PackageEvent $event) {
// 		if (!self::bootstrap()) {
// 			return;
// 		}
		
// 		$operation = $event->getOperation();
// 		if ($operation instanceof UpdateOperation) {
// 			/* @var $operation UpdateOperation */
// 			$initial = $operation->getInitialPackage();
// 			$target = $operation->getTargetPackage();
// 			$installer = self::getInstaller($target);
// 			$installer->update($event->getIO(), $initial, $target);
// 		}
	}

	protected function uninstallPackage(PackageEvent $event) {
// 		if (!self::bootstrap()) {
// 			return;
// 		}
		
// 		$operation = $event->getOperation();
// 		if ($operation instanceof UninstallOperation) {
// 			$package = $operation->getPackage();
// 			$installer = self::getInstaller($package);
// 			$installer->uninstall($event->getIO(), $package);
// 		}
	}

	/**
	 * Returns the installer for a given type
	 *
	 * @param PackageInterface
	 * @return AbstractPackageInstaller
	 */
	private function getInstaller(PackageInterface $package) {
		switch ($package->getType()) {
			case 'keeko-app':
				return new AppInstaller();
				
			case 'keeko-module':
				return new ModuleInstaller();
				
			default:
				return new DummyInstaller();
		}
	}
	
	/**
	 * Batch processing of install, update and uninstall operations
	 *
	 * @param array<PackageEvent> $install
	 * @param array<PackageEvent> $update
	 * @param array<PackageEvent> $uninstall
	 */
	public function process(array $install, array $update, array $uninstall) {
		// return if database isn't loaded
		if (!KEEKO_DATABASE_LOADED) {
			return;
		}
		
		// do something
	}
}