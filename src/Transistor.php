<?php

namespace CreateWithRani\Transistor;

/**
 * A minimally abstracted Transistor API v1 wrapper
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
	private $last_error         = '';
	private $last_response      = array();
	private $last_request       = array();

	/**
	 * Set up a new instance
	 *
	 * @param string $api_key Your Transistor API key
	 *
	 * @return void
	 */
	public function __construct( $api_key ) {
		// make sure cURL is available
		if ( ! function_exists( 'curl_init' ) || ! function_exists( 'curl_setopt' ) ) {
			throw new \Exception( "cURL support is required, but can't be found." );
		}

		$this->api_key = $api_key;

		// since we haven't called any API methods yet, we don't have a last response
		$this->last_response = array(
			'headers' => null,
			'body'    => null,
		);
	}

	public function user() {
		return $this->make_curl_request( 'get' );
	}

	/**
	 * Array of headers and body of your last request
	 *
	 * @return array Array with keys 'headers' and 'body'
	 */
	public function get_last_request() {
		return $this->last_request;
	}

	/**
	 * Get the last response; array of headers and body
	 *
	 * @return array Array with keys 'headers' and 'body'
	 */
	public function get_last_response() {
		return $this->last_response;
	}

	/**
	 * Get the last error, generally by the API
	 *
	 * @return string The last error
	 */
	public function get_last_error() {
		return $this->last_error;
	}

	/**
	 * Was the last request successful?
	 *
	 * @return bool True if the last request was successful, false otherwise
	 */
	public function success() {
		return $this->request_successful;
	}

	/**
	 * Make a DELETE request
	 *
	 * @param string $method The API method to be called
	 * @param array $args Arguments to be passed to the method as an associative array (optional    )
	 * @param int $timeout Timeout for the request (optional)
	 *
	 * @return array|bool The response or false on failure
	 */
	public function delete( $method, $args = array(), $timeout = self::TIMEOUT ) {
		return $this->make_curl_request( 'delete', $method, $args, $timeout );
	}

	/**
	 * Make a GET request
	 *
	 * @param string $method The API method to be called
	 * @param array $args Arguments to be passed to the method as an associative array (optional)
	 * @param int $timeout Timeout for the request (optional)
	 *
	 * @return array|bool The response or false on failure
	 */
	public function get( $method, $args = array(), $timeout = self::TIMEOUT ) {
		return $this->make_curl_request( 'get', $method, $args, $timeout );
	}

	/**
	 * Make a PATCH request
	 *
	 * @param string $method The API method to be called
	 * @param array $args Arguments to be passed to the method as an associative array (optional)
	 * @param int $timeout Timeout for the request (optional)
	 *
	 * @return array|bool The response or false on failure
	 */
	public function patch( $method, $args = array(), $timeout = self::TIMEOUT ) {
		return $this->make_curl_request( 'patch', $method, $args, $timeout );
	}

	/**
	 * Make a POST request
	 *
	 * @param string $method The API method to be called
	 * @param array $args Arguments to be passed to the method as an associative array (optional)
	 * @param int $timeout Timeout for the request (optional)
	 *
	 * @return array|bool The response or false on failure
	 */
	public function post( $method, $args = array(), $timeout = self::TIMEOUT ) {
		return $this->make_curl_request( 'post', $method, $args, $timeout );
	}

	/**
	 * Make the HTTP request
	 *
	 * @param string $verb The HTTP verb to use: GET, POST, PUT, DELETE
	 * @param string $method The API method to be called
	 * @param array $args Arguments to be passed to the method as an associative array
	 * @param int $timeout Timeout for the request
	 *
	 * @return array|bool The response or false on failure
	 */
	private function make_curl_request( $verb, $method = false, $args = array(), $timeout = self::TIMEOUT ) {
		// set up the request URL
		$url = $this->api_url . $method;

		$response = $this->prepare_for_request( $verb, $method, $url, $timeout );

		// set up the request header
		$http_header = array(
			'Accept: application/vnd.api+json',
			'Content-Type: application/vnd.api+json',
			'x-api-key: ' . $this->api_key,
		);

		if ( 'put' === $verb ) {
			$http_header[] = 'Allow: PUT, PATCH, POST';
		}

		// set up the cURL handle
		$handle = curl_init();
		curl_setopt( $handle, CURLOPT_URL, $url );
		curl_setopt( $handle, CURLOPT_HTTPHEADER, $http_header );
		curl_setopt( $handle, CURLOPT_USERAGENT, 'CreateWithRani/Transistor-API/1.0 (github.com/createwithrani/transistor-api)' );
		curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $handle, CURLOPT_VERBOSE, true );
		curl_setopt( $handle, CURLOPT_HEADER, true );
		curl_setopt( $handle, CURLOPT_TIMEOUT, $timeout );
		curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl );
		curl_setopt( $handle, CURLOPT_ENCODING, '' );
		curl_setopt( $handle, CURLINFO_HEADER_OUT, true );

		// set up the HTTP verb-specific options
		switch ( $verb ) {
			case 'get':
				if ( empty( $args ) ) {
					curl_setopt( $handle, CURLOPT_URL, $url );
				} else {
					$query = http_build_query( $args, '', '&' );
					curl_setopt( $handle, CURLOPT_URL, $url . '?' . $query );
				}

				break;

			case 'post':
				curl_setopt( $handle, CURLOPT_POST, true );
				$this->attach_encoded_data( $handle, $args );
				break;

			case 'patch':
				curl_setopt( $handle, CURLOPT_CUSTOMREQUEST, 'PATCH' );
				$this->attach_encoded_data( $handle, $args );
				break;

			case 'delete':
				curl_setopt( $handle, CURLOPT_CUSTOMREQUEST, 'DELETE' );
				break;
		}

		// make the request
		$response_content    = curl_exec( $handle );
		$response['headers'] = curl_getinfo( $handle );
		$response            = $this->set_response_state( $response, $response_content, $handle );
		$formatted_response  = $this->format_response( $response );

		// store the request info
		$this->last_request = array(
			'url'     => $url,
			'verb'    => $verb,
			'args'    => $args,
			'timeout' => $timeout,
		);

		// store the response info
		$this->last_response = array(
			'code'    => curl_getinfo( $handle, CURLINFO_HTTP_CODE ),
			'headers' => curl_getinfo( $handle ),
			'body'    => $response,
		);

		// close the cURL handle
		curl_close( $handle );

		$successful = $this->is_successful( $response, $formatted_response, $timeout );

		return is_array( $formatted_response ) ? $formatted_response : $successful;

	}

	/**
	 * Set up defaults in the response array
	 *
	 * @param string  $verb   The HTTP verb to use: GET, POST, PATCH, DELETE
	 * @param string  $method The API method to be called
	 * @param string  $url   The URL to be called
	 * @param integer $timeout Timeout for the request
	 *
	 * @return array
	 */
	private function prepare_for_request( $verb, $method, $url, $timeout ) {
		$this->last_error = '';

		$this->request_successful = false;

		$this->last_response = array(
			'headers'     => null, // array of details from curl_getinfo()
			'httpHeaders' => null, // array of HTTP headers
			'body'        => null, // content of the response
		);

		$this->last_request = array(
			'method'  => $verb,
			'path'    => $method,
			'url'     => $url,
			'body'    => '',
			'timeout' => $timeout,
		);

		return $this->last_response;
	}

	/**
	 * Encode data and attach it to the request
	 *
	 * @param   resource $handle   cURL session handle, used by reference
	 * @param   array    $data Assoc array of data to attach
	 */
	private function attach_encoded_data( &$handle, $data ) {
		$encoded                    = json_encode( $data );
		$this->last_request['body'] = $encoded;
		curl_setopt( $handle, CURLOPT_POSTFIELDS, $encoded );
	}

	/**
	 * Format response and set the state
	 *
	 * @param array    $response        The response from the curl request
	 * @param string   $response_content Body of the request response
	 * @param resource $handle              Curl session handle
	 *
	 * @return array    Modified response
	 */
	private function set_response_state( $response, $response_content, $handle ) {
		if ( false === $response_content ) {
			$this->last_error = curl_error( $handle );
		} else {

			$header_size = $response['headers']['header_size'];

			$response['httpHeaders'] = $this->get_headers_array( substr( $response_content, 0, $header_size ) );
			$response['body']        = substr( $response_content, $header_size );

			if ( isset( $response['headers']['request_header'] ) ) {
				$this->last_request['headers'] = $response['headers']['request_header'];
			}
		}

		return $response;
	}

	/**
	 * Get the HTTP headers as an array of header-name => header-value pairs.
	 *
	 * @param string $headers_string
	 *
	 * @return array
	 */
	private function get_headers_array( $headers_string ) {
		$headers = array();

		foreach ( explode( "\r\n", $headers_string ) as $i => $line ) {
			if ( preg_match( '/HTTP\/[1-2]/', substr( $line, 0, 7 ) ) === 1 ) {
				continue;
			}

			$line = trim( $line );
			if ( empty( $line ) ) {
				continue;
			}

			list($key, $value) = explode( ':', $line );
			$value             = ltrim( $value );

			$headers[ $key ] = $value;
		}

		return $headers;
	}

	/**
	 * Decode response and format any errors
	 *
	 * @param array $response The response from the request
	 *
	 * @return array|false    The JSON decoded into an array
	 */
	private function format_response( $response ) {
		$this->last_response = $response;

		if ( ! empty( $response['body'] ) ) {
			return json_decode( $response['body'], true );
		}

		return false;
	}

	/**
	 * Check if the response was successful or a failure. If failed, store error.
	 *
	 * @param array       $response          The response from the request
	 * @param array|false $formatted_response The response body payload from the request
	 * @param int         $timeout           The timeout supplied to the request.
	 *
	 * @return bool     If the request was successful
	 */
	private function is_successful( $response, $formatted_response, $timeout ) {
		$status = $this->get_http_status( $response, $formatted_response );

		// if the status is between 200 and 299, it's a success
		if ( $status >= 200 && $status <= 299 ) {
			$this->request_successful = true;
			return true;
		}

		if ( isset( $formatted_response['detail'] ) ) {
			$this->last_error = sprintf( '%d: %s', $formatted_response['status'], $formatted_response['detail'] );
			return false;
		}

		// if the the request total time is greater than the timeout we provided, it's a timeout
		if ( $timeout > 0 && $response['headers'] && $response['headers']['total_time'] >= $timeout ) {
			$this->last_error = sprintf( 'The request timed out after %f seconds.', $response['headers']['total_time'] );
			return false;
		}

		$this->last_error = 'Unknown error, call get_last_response() to find out what happened.';
		return false;
	}

	/**
	 * Find the HTTP status code from the headers or API response body
	 *
	 * @param array       $response          The response from the request
	 * @param array|false $formatted_response The response body
	 *
	 * @return int  HTTP status code
	 */
	private function get_http_status( $response, $formatted_response ) {
		if ( ! empty( $response['headers'] ) && isset( $response['headers']['http_code'] ) ) {
			return (int) $response['headers']['http_code'];
		}

		if ( ! empty( $response['body'] ) && isset( $formatted_response['status'] ) ) {
			return (int) $formatted_response['status'];
		}
		// If we can't find the status code, return 418 (I'm a teapot)
		// Learn More: https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/418
		return 418;
	}
}
