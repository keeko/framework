<?php
namespace keeko\framework\installer;

use Composer\IO\IOInterface;
use keeko\core\model\Extension;
use keeko\core\model\ExtensionQuery;
use keeko\core\model\Package;
use keeko\core\model\PackageQuery;
use keeko\framework\schema\KeekoPackageSchema;
use keeko\framework\service\ServiceContainer;
use phootwork\json\Json;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class AbstractPackageInstaller {
	
	/** @var ServiceContainer */
	protected $service;
	
	/** @var EventDispatcher */
	protected $dispatcher;
	
	public function __construct(ServiceContainer $service) {
		$this->service = $service;
		$this->dispatcher = $this->service->getDispatcher();
	}

	/**
	 * Installs a package
	 *
	 * @param IOInterface $io the IOInterface
	 * @param string $packageName
	 * @throws MissingOptionsException when required parameters for the package are missing
	 */
	abstract public function install(IOInterface $io, $packageName);

	abstract public function update(IOInterface $io, $packageName, $from, $to);

	abstract public function uninstall(IOInterface $io, $packageName);

	protected function updatePackage(Package &$model, KeekoPackageSchema $pkg) {
		$packageName = $pkg->getPackage()->getFullName();
		$result = PackageQuery::create()->filterByName($packageName)->count();
		$info = $this->service->getPackageManager()->getComposerPackage($packageName);
		
		if ($result == 0) {
			$model->setTitle($pkg->getTitle());
			$model->setDescription($info->getDescription());
			$model->setName($packageName);
			$model->setInstalledVersion($info->getPrettyVersion());
			$model->save();
		}
		
		$this->updateExtensions($model, $pkg);
	}
	
	protected function getPackageSchema($packageName) {
		return $this->service->getPackageManager()->getPackage($packageName);
	}
	
	protected function updateExtensions(Package &$model, KeekoPackageSchema $pkg) {
		// remove all existing extensions from this package first
		ExtensionQuery::create()->filterByPackage($model)->delete();
		
		// add them one by one
		foreach ($pkg->getAllExtensions() as $key => $exts) {
			foreach ($exts as $data) {
				$ext = new Extension();
				$ext->setKey($key);
				$ext->setData(Json::encode($data, Json::UNESCAPED_SLASHES));
				$ext->setPackage($model);
				$ext->save();
			}
		}
	}
}