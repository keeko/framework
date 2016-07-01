<?php
namespace keeko\framework\tests\schema;

use keeko\framework\schema\GeneratorDefinitionSchema;

class GeneratorDefinitionSchemaTest extends \PHPUnit_Framework_TestCase {

	public function testWriteFilter() {
		$package = GeneratorDefinitionSchema::fromFile(__DIR__ . '/fixture/generator.json');

		$filter = $package->getWriteFilter('user');

		$this->assertEquals(['password_recover_code', 'password_recover_time'], $filter);
	}

	public function testReadFilter() {
		$package = GeneratorDefinitionSchema::fromFile(__DIR__ . '/fixture/generator.json');

		$filter = $package->getReadFilter('user');

		$this->assertEquals(['password', 'password_recover_code', 'password_recover_time'], $filter);
	}
}