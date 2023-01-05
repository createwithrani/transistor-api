<?php

namespace CreateWithRani\Transistor\Tests;

use CreateWithRani\Transistor\Transistor;
use PHPUnit\Framework\TestCase;

class TransistorTest extends TestCase {

	/**
	 * @throws \Exception
	 */
	public function test_instance() {
		$transistor_api_key = getenv( 'transistor_api_key' );

		$transistor = new Transistor( $transistor_api_key );
		$this->assertInstanceOf( '\CreateWithRani\Transistor\Transistor', $transistor );

		$this->assertFalse( $transistor->success() );

		$this->assertEmpty( $transistor->get_last_error() );

	}

	public function test_response_state() {
		$transistor_api_key = getenv( 'transistor_api_key' );

		$transistor = new Transistor( $transistor_api_key );

		$transistor->get( 'shows' );

		// Since we're using a fake key, it doesn't work
		$this->assertFalse( $transistor->success() );

		// But now we have an error message
		$this->assertSame(
			'Unknown error, call get_last_response() to find out what happened.',
			$transistor->get_last_error()
		);
	}
}
