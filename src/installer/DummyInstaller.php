<?php
namespace keeko\framework\installer;

use Composer\IO\IOInterface;
use Composer\Package\CompletePackageInterface;

class DummyInstaller extends AbstractPackageInstaller {

	public function install(IOInterface $io, CompletePackageInterface $package) {
	}

	public function update(IOInterface $io, CompletePackageInterface $initial, CompletePackageInterface $target) {
	}

	public function uninstall(IOInterface $io, CompletePackageInterface $package) {
	}
}