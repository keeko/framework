<?php
namespace keeko\framework\installer;

use Composer\Package\CompletePackageInterface;
use Composer\IO\IOInterface;

class DesignInstaller extends AbstractPackageInstaller {

	public function install(IOInterface $io, CompletePackageInterface $package) {
// 		$extra = $package->getExtra();
		
// 		$io->write('[Keeko] Install Design: ' . $package->getName());
		
// 		if (!array_key_exists('keeko', $extra)) {
// 			return;
// 		}
		
// 		$keeko = $extra['keeko'];
// 		$design = new Design();
		
// 		$this->installLayouts($design, $keeko);
// 		$this->updatePackage($design, $package);
	}

// 	private function installLayouts(Design $design, $keeko) {
// 		if (!array_key_exists('layouts', $keeko['layouts'])) {
// 			return;
// 		}
		
// 		foreach ($keeko['layouts'] as $name => $props) {
// 			$layout = new Layout();
// 			$layout->setName($name);
// 			$layout->setDesign($design);
			
// 			if (array_key_exists('title', $props)) {
// 				$layout->setTitle($props['title']);
// 			}
			
// 			$layout->save();
			
// 			// install blocks
// 			$this->installBlocks($layout, $props);
// 		}
// 	}

// 	private function installBlocks(Layout $layout, $props) {
// 		if (!array_key_exists('blocks', $props)) {
// 			return;
// 		}
		
// 		foreach ($props['blocks'] as $name => $title) {
// 			$block = new Block();
// 			$block->setName($name);
// 			$block->setTitle($title);
// 			$block->setLayout($layout);
// 			$block->save();
// 		}
// 	}

	public function update(IOInterface $io, CompletePackageInterface $initial, CompletePackageInterface $target) {
// 		$io->write('[Keeko] Update Design: ' . $target->getName());
		
// 		$design = DesignQuery::create()->findOneByName($target->getName());
		
// 		if ($design !== null) {
// 			// TODO: Update Layouts
			
// 			$this->updatePackage($design, $target);
// 		}
	}

	public function uninstall(IOInterface $io, CompletePackageInterface $package) {
// 		$io->write('[Keeko] Uninstall Design: ' . $package->getName());
		
// 		$design = DesignQuery::create()->findOneByName($package->getName());
// 		$design->delete();
	}
}