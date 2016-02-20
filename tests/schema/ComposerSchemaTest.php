<?php
namespace keeko\framework\tests\schema;

use phootwork\collection\Map;
use phootwork\json\Json;
use keeko\framework\schema\PackageSchema;

class ComposerSchemaTest extends \PHPUnit_Framework_TestCase {
	
	public function testEmptyPackage() {
		$this->assertEmptyPackage(new PackageSchema());
	}
	
	public function testReadEmptyPackage() {
		$package = PackageSchema::fromFile(__DIR__ . '/fixture/empty.json');
		
		$this->assertEmptyPackage($package);
	}
	
	private function assertEmptyPackage(PackageSchema $package) {
		$this->assertEquals('', $package->getFullName());
		$this->assertEquals('', $package->getName());
		$this->assertEquals('', $package->getVendor());
		$this->assertEquals('', $package->getDescription());
		$this->assertEquals('', $package->getType());
		$this->assertEquals('', $package->getLicense());
	
		// authors
		$this->assertEquals(0, $package->getAuthors()->size());
	
		// autoload
		$autoload = $package->getAutoload();
		$this->assertTrue($autoload->getPsr4()->isEmpty());
		$this->assertTrue($autoload->getPsr0()->isEmpty());
		$this->assertTrue($autoload->getClassmap()->isEmpty());
		$this->assertTrue($autoload->getFiles()->isEmpty());
	
		// require
		$require = $package->getRequire();
		$this->assertTrue($require->isEmpty());
	
		// require-dev
		$requireDev = $package->getRequireDev();
		$this->assertTrue($requireDev->isEmpty());
	
		// extra
		$extra = $package->getExtra();
		$this->assertTrue($extra->isEmpty());
	}
	
	public function testReadBasicPackage() {
		$package = PackageSchema::fromFile(__DIR__ . '/fixture/basic.json');
		
		$this->assertBasicPackage($package);
	}

	private function assertBasicPackage(PackageSchema $package) {
		$this->assertEquals('basic/package', $package->getFullName());
		$this->assertEquals('package', $package->getName());
		$this->assertEquals('basic', $package->getVendor());
		$this->assertEquals('I am just a dummy', $package->getDescription());
		$this->assertEquals('package', $package->getType());
		$this->assertEquals('MIT', $package->getLicense());
		
		// authors
		$authors = $package->getAuthors();
		$gossi = $authors->get(0);
		$this->assertEquals(1, $authors->size());
		$this->assertEquals('gossi', $gossi->getName());
		
		// autoload
		$autoload = $package->getAutoload();
		$psr4 = $autoload->getPsr4();
		$this->assertTrue($autoload->getClassmap()->isEmpty());
		$this->assertTrue($autoload->getFiles()->isEmpty());
		$this->assertTrue($autoload->getPsr0()->isEmpty());
		$this->assertEquals('src/', $psr4->getPath('basic\\package\\'));
		$this->assertTrue($psr4->hasNamespace('basic\\package\\'));
		
		// require
		$require = $package->getRequire();
		$this->assertFalse($require->isEmpty());
		$this->assertTrue($require->has('phootwork/collection'));
		
		// require-dev
		$requireDev = $package->getRequireDev();
		$this->assertFalse($requireDev->isEmpty());
		$this->assertTrue($requireDev->has('phpunit/phpunit'));
		
		// extra
		$extra = $package->getExtra();
		$this->assertTrue($extra->has('moop'));
		$this->assertEquals('value', $extra->get('moop'));
		
		$this->assertTrue($extra->get('doop') instanceof Map);
		$this->assertEquals('other-value', $extra->get('doop')->get('some'));
	}
	
	public function testWriteBasicPackage() {
		$package = PackageSchema::fromFile(__DIR__ . '/fixture/basic.json');
		$json = Json::encode($package->toArray(), Json::PRETTY_PRINT | Json::UNESCAPED_SLASHES);
		$expected = file_get_contents(__DIR__ . '/fixture/basic.json');
		
		$this->assertEquals($expected, $json);
	}
	
	public function testExtendedPackage() {
		$package = PackageSchema::fromFile(__DIR__ . '/fixture/extended.json');
		$json = Json::encode($package->toArray(), Json::PRETTY_PRINT | Json::UNESCAPED_SLASHES);
		$expected = file_get_contents(__DIR__ . '/fixture/extended.json');
		
		$this->assertEquals($expected, $json);
	}

}
