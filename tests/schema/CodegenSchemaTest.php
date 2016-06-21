<?php
namespace keeko\framework\tests\schema;

use keeko\framework\schema\CodegenSchema;

class CodegenSchemaTest extends \PHPUnit_Framework_TestCase {

// 	public function testWriteConversion() {
// 		$package = CodegenSchema::fromFile(__DIR__ . '/fixture/codegen.json');

// 		$conversion = $package->getWriteConversion('user');

// 		$this->assertTrue(isset($conversion['password']));
// 		$this->assertEquals('password_hash($v, PASSWORD_BCRYPT)', $conversion['password']);
// 	}

	public function testWriteFilter() {
		$package = CodegenSchema::fromFile(__DIR__ . '/fixture/codegen.json');

		$filter = $package->getWriteFilter('user');

		$this->assertEquals(['password_recover_code', 'password_recover_time'], $filter);
	}

	public function testReadFilter() {
		$package = CodegenSchema::fromFile(__DIR__ . '/fixture/codegen.json');

		$filter = $package->getReadFilter('user');

		$this->assertEquals(['password', 'password_recover_code', 'password_recover_time'], $filter);
	}
}