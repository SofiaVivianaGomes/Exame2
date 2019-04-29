<?php
namespace App\Tests;
require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php';

use PHPUnit\Framework\TestCase;
use App\IndexesController;

class IndexesTest extends TestCase {
	public function setUp() {
		$this->IndexesController = new IndexesController();
	}

	public function tearDown() {
		unset($this->IndexesController);
	}

	public function testIndexes($input=NULL, $expected, $msg) {
		$this->assertSame(
			$expected,
			$this->IndexesController->indexes($input),
			$msg
		);
	}

	public function testGetServiceData($expected, $msg) {
		$this->assertSame(
			$expected,
			$this->IndexesController->getServiceData(),
			$msg
		);
	}

	public function testSaveData($input, $expected, $msg) {
		$this->assertSame(
			$expected,
			$this->IndexesController->saveData($input),
			$msg
		);
	}
}