<?php
namespace keeko\framework\installer;

use Composer\IO\IOInterface;
use keeko\core\model\Application;
use keeko\core\model\ApplicationQuery;

class AppInstaller extends AbstractPackageInstaller {

	public function install(IOInterface $io, $packageName) {
		$model = ApplicationQuery::create()->findOneByName($packageName);
		
		if ($model === null) {
			$io->write('[Keeko] Install Application: ' . $packageName);
			
			$package = $this->service->getPackageManager()->getPackage($packageName);
			$keeko = $package->getKeeko();
			
			if ($keeko->isApp()) {
				$pkg = $keeko->getApp();
				
				$model = new Application();
				$model->setClassName($pkg->getClass());
				$this->updatePackage($model, $pkg);
			}
		}
		
		return $model;
	}

	public function update(IOInterface $io, $packageName, $from, $to) {
		// nothing to do here
	}

	public function uninstall(IOInterface $io, $packageName) {
		// nothing to do here
	}
}