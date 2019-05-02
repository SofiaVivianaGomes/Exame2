<?php
namespace App\Tests;
require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php';

use PHPUnit\Framework\TestCase;
use App\UserController;

class UserTest extends TestCase {
	public function setUp() {
		$this->UserController = new UserController();
	}

	public function tearDown() {
		unset($this->UserController);
	}

	public function testValidate($data=NULL, $registered=NULL, $to_alter=NULL, $expected, $msg) {
		$this->assertSame(
			$expected,
			$this->UserController->validate($data, $registered, $to_alter),
			$msg
		);
	}

	public function testLogin($data=NULL, $expected, $msg) {
		$this->assertSame(
			$expected,
			$this->UserController->login($data),
			$msg
		);
	}

	public function testRegister($data=NULL, $expected, $msg) {
		$this->assertSame(
			$expected,
			$this->UserController->register($data),
			$msg
		);
	}

	public function testAlter($data=NULL, $hash=NULL, $expected, $msg) {
		$this->assertSame(
			$expected,
			$this->UserController->alter($data, hash),
			$msg
		);
	}

	public function testLogout($expected, $msg) {
		$this->assertSame(
			$expected,
			$this->UserController->logout(),
			$msg
		);
	}
}