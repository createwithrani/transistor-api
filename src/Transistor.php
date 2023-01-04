<?php

namespace CreateWithRani\Transistor;

/**
 * A minimal abstracted Transistor API wrapper
 * Transistor API: https://developers.transistor.fm/
 * This wrapper: https://github.com/aurooba/transistor-api
 *
 * @author Aurooba Ahmed <aurooba@auroobamakes.com>
 * @version 1.0.0
 */

 class Transistor {

	private $api_key;
	private $api_url = 'https://api.transistor.fm/v1/';

	const TIMEOUT = 10;

	private $verify_ssl = true;

	private $request_successful = false;
	private $last_error = '';
	private $last_response = array();
	private $last_request = array();

	/**
	 * Set up a new instance
	 *
	 * @param string $api_key Your Transistor API key
	 *
	 * @return void
	 */
	public function __construct($api_key) {
		$this->api_key = $api_key;
	}

 }