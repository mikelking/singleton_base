<?php

/*
Plugin Name: Cookie Controller
Version: 1.1
Description: A simple framework for working with cookies in PHP & JS.
Author: Mikel King
Text Domain: cookie-controller
License: BSD(3 Clause)
License URI: http://opensource.org/licenses/BSD-3-Clause

	Copyright (C) 2014, Mikel King, olivent.com, (mikel.king AT olivent DOT com)
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

		* Redistributions of source code must retain the above copyright notice, this
		list of conditions and the following disclaimer.

		* Redistributions in binary form must reproduce the above copyright notice,
		this list of conditions and the following disclaimer in the documentation
		and/or other materials provided with the distribution.

		* Neither the name of the {organization} nor the names of its
		contributors may be used to endorse or promote products derived from
		this software without specific prior written permission.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
	AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
	IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
	FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
	DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
	SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
	CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
	OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
	OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * Class Cookie_Controller
 *
 * Establishes a standardized interface for working with cookies.
 *
 * @author Mikel King <mikel.king@olivent.com>
 *
 * @since 1.0
 *
 * @license BSD(3 Clause) http://opensource.org/licenses/BSD-3-Clause
 *
 * @see http://php.net/manual/en/features.cookies.php
 *
 */
class Cookie_Controller extends Singleton_Base  {
	const VERSION            = '1.2';
	const IN_FOOTER          = true;
	const IN_HEADER          = false;
	const FILE_SPEC          = __FILE__;
	const DEFAULT_DOMAIN     = 'www.jafdip.com';
	const DEFAULT_EXPIRATION = '+1 year';
	const DEFAULT_PATH       = '/';
	const DEPENDS            = 'jquery';
	const COOKIE_LIB_NAME    = 'cookie-lib';
	const COOKIE_LIB_FILE    = 'cookie-controller/js/cookie.min.js';

	protected static $negative_expiration;
	protected static $current_cookie = array();
	protected static $current_cookie_data;

	public static $cookie_domain;
	public static $cookie_status = array();

	public $expiration;

	public function __construct() {
		date_default_timezone_set( self::DEFAULT_TZ );
		self::$negative_expiration = time() - 60;

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public static function use_current_domain() {
		self::$cookie_domain = $_SERVER['HTTP_HOST'];

		// equivalent of return( $this )
		return( self::get_instance() );
	}

	/**
	 * @todo refactor unsing late static binding to eliminate the param passing silliness
	 * @param null $cookie_domain
	 * @return null|static
	 */
	public static function set_cookie_domain( $cookie_domain = null ) {
		if ( isset( $cookie_domain ) ) {
			self::$cookie_domain = $cookie_domain;
		}
		self::use_current_domain();
		print('<!-- Cookie Domain: ' . self::$cookie_domain . '. -->' . PHP_EOL );
		return( self::$cookie_domain );
	}

	/**
	 * @return string
	 */
	protected static function get_cookie_domain() {
		if ( isset( self::$cookie_domain ) ) {
			return( self::$cookie_domain );
		}
		return( self::DEFAULT_DOMAIN );
	}

	/**
	 * @param $cookie
	 * @return array|null
	 */
	public static function finder( $cookie_key ) {
		if ( isset( $cookie_key ) && is_array( $cookie_key ) && array_key_exists( 'name', $cookie_key ) && array_key_exists( $cookie_key['name'], $_COOKIE ) ) {
			$cookie_name = $cookie_key['name'];
			self::$current_cookie['name'] = $cookie_name;
			self::$current_cookie['data'] = $_COOKIE[$cookie_name];
		} elseif ( isset( $cookie_key ) && is_string( $cookie_key ) && array_key_exists( $cookie_key, $_COOKIE ) ) {
			self::$current_cookie['name'] = $cookie_key;
			self::$current_cookie['data'] = $_COOKIE[$cookie_key];
		}
		return( self::$current_cookie );
	}

	/**
	 * @param $cookie_key
	 * @return bool|array
	 */
	protected static function cookie_finder( $cookie_key ) {
		if ( isset( $cookie_key ) && array_key_exists( $cookie_key, $_COOKIE ) ) {
			self::$current_cookie['name'] = $_COOKIE[$cookie_key];
			return( self::$current_cookie );
		}
		return( false );
	}

	public static function get_current_cookie() {
		return( self::$current_cookie );
	}

	public static function create_cookie( $cookie ) {
		$cookie_expiration = self::get_expiration();
		$cookie_domain	 = self::get_cookie_domain();
		$cookie_path	   = self::DEFAULT_PATH;

		if ( isset( $cookie ) && ! self::finder( $cookie ) ) {
			if ( is_array( $cookie ) && array_key_exists( 'name', $cookie ) ) {
				$cookie_name = $cookie['name'];
			} elseif ( is_string( $cookie ) ) {
				$cookie_name = $cookie;
			}

			if ( is_array( $cookie ) && array_key_exists( 'data', $cookie ) ) {
				$cookie_data = $cookie['data'];
			} elseif ( is_string( $cookie ) ) {
				$cookie_data = sha1( $cookie );
			}

			if ( is_array( $cookie ) && array_key_exists( 'expiration', $cookie ) ) {
				$cookie_expiration = $cookie['expiration'];
			}

			if ( is_array( $cookie ) && array_key_exists( 'domain', $cookie ) ) {
				$cookie_domain = $cookie['domain'];
			}

			if ( is_array( $cookie ) && array_key_exists( 'path', $cookie ) ) {
				$cookie_path = $cookie['path'];
			}
		}

		if ( isset( $cookie_name ) && isset( $cookie_data ) ) {
			self::$cookie_status = '';
			$status = setcookie(
				$cookie_name,
				$cookie_data,
				$cookie_expiration,
				$cookie_path,
				$cookie_domain
			);
			if ( $status === true ) {
				self::$cookie_status = array(
						'name'   => $cookie_name,
						'data'   => $cookie_data,
						'path'   => $cookie_path,
						'epxr'   => $cookie_expiration,
						'domain' => $cookie_domain,
						'status' => 'created',
				);
			} else {
				self::$cookie_status = array(
						'name'   => $cookie_name,
						'data'   => $cookie_data,
						'status' => 'failed',
				);
			}
		} else {
			self::$cookie_status = array(
				'name'   => $cookie,
				'status' => 'failed',
			);
		}
		return( self::$cookie_status );
	}

	/**
	 * @param $cookie_key
	 * @return null|static
	 */
	public static function delete_cookie( $cookie_key ) {
		self::$negative_expiration = time() - 60;
		if ( self::finder( $cookie_key ) ) {
			setcookie(
				$cookie_key,
				self::finder( $cookie_key ),
				self::$negative_expiration,
				self::DEFAULT_PATH,
				self::get_cookie_domain()
			);
			self::$cookie_status = array(
				'name'   => $cookie_key,
				'status' => 'deleted',
			);
		} else {
			self::$cookie_status = array(
				'name'   => $cookie_key,
				'status' => 'not found',
			);
		}

		// equivalent of return( $this )
		return( self::get_instance() );
	}

	/**
	 * @param null $expiration
	 * @return null|static
	 */
	public function set_expiration( $expiration = null ) {
		if ( isset( $expiration ) ) {
			$this->expiration = strtotime( $expiration );
		}
		// equivalent of return( $this )
		return( self::get_instance() );
	}


	/**
	 * @return int
	 */
	protected static function get_expiration() {
		return( strtotime( self::DEFAULT_EXPIRATION ) );
	}


	/**
	 * @return null
	 */
	public function enqueue_scripts() {
		wp_register_script(
			self::COOKIE_LIB_NAME,
			$this->get_url_to_asset( self::COOKIE_LIB_FILE ),
			array( self::DEPENDS ),
			self::VERSION,
			self::IN_HEADER
		);
		wp_enqueue_script( self::COOKIE_LIB_NAME );
		return;
	}

	/**
	 * @param $asset_path
	 * @return string
	 */
	protected function get_url_to_asset( $asset_path ) {
		return( plugins_url( $asset_path, __FILE__ ) );
	}

	public static function dbg_msg() {
		$msg = '<!-- Cookie Status: ';
		$msg .= print_r( self::$cookie_status, true );
		$msg .= '#======== Cookie Object ========#' . PHP_EOL;
		$msg .= print_r( $_COOKIE, true );
		$msg .= ' -->' . PHP_EOL;
		print( $msg );
	}
}
// The following auto-activates the plugin so that the cooke.min.js is enqueued
$cc = Cookie_Controller::get_instance();
