<?php
namespace keeko\framework\tests\schema;

use keeko\framework\schema\PackageSchema;
use keeko\framework\schema\ActionSchema;

class KeekoSchemaTest extends \PHPUnit_Framework_TestCase {
	
	public function testAppPackage() {
		$package = PackageSchema::fromFile(__DIR__ . '/fixture/app.json');
		
		$this->assertEquals('keeko-app', $package->getType());
		
		$keeko = $package->getKeeko();
		
		$this->assertTrue($keeko->isApp());
		$this->assertFalse($keeko->isModule());
		
		$app = $keeko->getApp();
		
		$this->assertEquals('Dummy App', $app->getTitle());
		$this->assertEquals('keeko\\app\\DummyApp', $app->getClass());
	}
	
	public function testModulePackage() {
		$package = PackageSchema::fromFile(__DIR__ . '/fixture/module.json');
		
		$this->assertEquals('keeko-module', $package->getType());
		
		$keeko = $package->getKeeko();
		$this->assertFalse($keeko->isApp());
		$this->assertTrue($keeko->isModule());
		
		$module = $keeko->getModule();
		$this->assertEquals('Dummy Module', $module->getTitle());
		$this->assertEquals('keeko\\module\\DummyModule', $module->getClass());
		$this->assertEquals('module', $module->getSlug());
		
		$this->assertTrue($module->hasAction('dashboard'));
		$dashboard = $module->getAction('dashboard');
		$this->assertEquals('Admin overview', $dashboard->getTitle());
		$this->assertEquals('keeko\\module\\actions\\DashboardAction', $dashboard->getClass());
		$this->assertEquals(1, $dashboard->getAcl()->size());
		$this->assertTrue($dashboard->hasAcl('admin'));
		
		$this->assertTrue($dashboard->hasResponse('json'));
		$this->assertEquals('keeko\\module\\responses\\DashboardJsonResponse', $dashboard->getResponse('json'));
	}
	
	public function testActions() {
		$package = new PackageSchema();
		$module = $package->getKeeko()->getKeekoPackage('module');
		
		$this->assertEquals(0, $module->getActionNames()->size());
		
		$action = new ActionSchema('create-sth');
		$action->setClass('keeko\\user\\actions\\CreateSthAction');
		$action->setTitle('Create something');
		
		$this->assertEquals('create-sth', $action->getName());
		$this->assertEquals('keeko\\user\\actions\\CreateSthAction', $action->getClass());
		$this->assertEquals('Create something', $action->getTitle());
		
		$module->addAction($action);

		$this->assertEquals(1, $module->getActionNames()->size());
	}

}
