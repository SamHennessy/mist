<?php

namespace MistTest\Log;

abstract class PsrLoggerInterfaceTest extends \Psr\Log\Test\LoggerInterfaceTest
{
	public function testContextCanContainAnything()
	{
		parent::testContextCanContainAnything();
		$this->assertTrue(true);
	}

	public function testContextExceptionKeyCanBeExceptionOrOtherValues()
	{
		parent::testContextExceptionKeyCanBeExceptionOrOtherValues();
		$this->assertTrue(true);
	}
}
