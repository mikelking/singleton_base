<?php

/*
    Plugin Name: Variation Base Class
    Version: 1.7
    Description: Adds a standardized base class for creating variants by extension
    Author: Mikel King
    Author URI: http://mikelking.com
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
 * Class Variant_Base
 *
 * Establishes a standardized system for implementing variations which can be introduced via a query string on the URL
 * as well a storing these into a cookie if desired so that the variant may be persisted through a user's session for
 * a period of time.
 *
 * @author Mikel King <mikel.king@olivent.com>
 *
 * @since 1.0
 *
 * @license BSD(3 Clause) http://opensource.org/licenses/BSD-3-Clause
 *
 */
class Variant_Base extends Base_Plugin {
	const VERSION      = '1.7';
	const AD_BLOCKER   = 'noads';
	const ASYNC_FLAG   = 'async-test';
	const ASYNC_FLAG_2 = 'async';

	public static $variants       = array();
	public static $noads_variants = array();

	public $found_variant;
	public $new_registration;

	public function __construct( $new_variant = null ) {
		$this->set_ad_blocking();
		$this->set_async_testing();

		if ( isset( $new_variant ) ) {
			$this->register( $new_variant );
		}
	}

	/**
	 * @return Variant_Base
	 */
	protected function set_async_testing() {
		$this->register( self::ASYNC_FLAG );
		return( $this );
	}

	/**
	 * @return bool
	 */
	public function is_async_testing_active() {
		return( $this->validate_query_string() === self::ASYNC_FLAG );
	}

	/**
	 * @param null $key
	 * @return Variant_Base
	 */
	protected function set_ad_blocking( $key = null ) {
		if ( isset( $key ) && $key != true ) {
			self::$noads_variants[] = $key;
		}  elseif ( isset( $this->new_registration ) && $key === true ) {
			self::$noads_variants[] = $this->new_registration;
		}

		$this->register( self::AD_BLOCKER );
		return( $this );
	}

	protected static function is_noads_cookie() {
		foreach ( self::$noads_variants as $noads_variant ) {
			if ( Cookie_Controller::finder( $noads_variant ) ) {
				return( true );
			}
		}
		return( false );
	}

	public function __call($method_name, $arguments) {
		parent::__call( $method_name, $arguments ); // TODO: Change the autogenerated stub
	}

	/**
	 * @return bool
	 */
	public function is_ad_blocked() {
		if ( $this->validate_query_string() === self::AD_BLOCKER ||
			$this->is_async_testing_active() ||
			self::is_noads_cookie() ||
			$this->validate_query_string() === self::ASYNC_FLAG_2
		) {
			return( true );
		}
		return( false );
	}

	/**
	 * @param null $variant
	 * @return bool
	 */
	public function is_active( $variant = null ) {
		if ( isset( $variant ) ) {

			$this->find( $variant );
		}

		if ( $this->validate_query_string() === $this->found_variant ) {
			return( true );
		}
		return( false );
	}

	protected function validate_query_string() {
		if ( isset( $_GET['variant'] ) ) {
			return ($this->sanitize_data( $_GET['variant'] ));
		}
		return( null );
	}

	/**
	 * @param $variant
	 * @return Variant_Base
	 */
	public function register( $variant ) {
		$this->new_registration = $variant;

		if ( ! in_array( $this->new_registration, self::$variants ) ) {
			self::$variants[] = $variant;
		}
		return( $this );
	}

	/**
	 * @param null $variant_needle
	 * @return Variant_Base
	 */
	public function find( $variant_needle = null ) {
		if ( isset( $variant_needle ) ) {
			$needle = $variant_needle;
		} elseif ( isset( $this->new_registration ) ) {
			$needle = $this->new_registration;
		} else {
			$needle = self::AD_BLOCKER;
		}

		if ( isset( $needle ) && in_array( $needle, self::$variants ) ) {
			$this->found_variant = $needle;
		}
		return( $this );
	}

	/*
        PHP Warning:  filter_var() expects at most 3 parameters, 7 given
    */
	/**
	 *
	 * PHP Warning:  filter_var() expects at most 3 parameters, 7 given
	 *
	 * @param $data
	 * @return mixed
	 */
	protected function sanitize_data($data) {
		$flags = array(
			FILTER_FLAG_NO_ENCODE_QUOTES,
			FILTER_FLAG_STRIP_LOW,
			FILTER_FLAG_STRIP_HIGH,
			FILTER_FLAG_ENCODE_LOW,
			FILTER_FLAG_ENCODE_HIGH,
			FILTER_FLAG_ENCODE_AMP,
		);
		return( filter_var( $data, FILTER_SANITIZE_STRING ) );
	}

	/**
	 * PHP Warning:  filter_var() expects at most 3 parameters, 7 given
	 */
	public function debug_noads() {
		print( '<!-- The ads have been blocked -->' . PHP_EOL );
		print( '<!-- The noads query string "' . $this->validate_query_string() . '" was found. -->' . PHP_EOL);
		print( '<!-- The is_active = "' . var_export( $this->is_active( 'noads' ), true ) . '" was found. -->' . PHP_EOL);
		print( '<!-- The current variants: ' . PHP_EOL );
		print_r( self::$variants, true );
		print( ' -->' . PHP_EOL );

		if ( isset( $this->found_variant ) ) {
			print('<!-- The variant found was: ' . $this->found_variant . ' -->' . PHP_EOL);
		} else {
			print('<!-- The variant was not found. -->' . PHP_EOL);
		}

	}

	/**
	 * @param null $variant
	 */
	public static function debug_variant($variant = null) {
		if ( $variant ) {
			print('<!-- The variant target: ' . $variant . ' is active -->' . PHP_EOL);
		}
		print('<!-- The variant url param is set to: ' . $this->validate_variant_url() . ' -->' . PHP_EOL);

		print('<!-- The variant array contains: ' . PHP_EOL . print_r( self::$variant, true ) . ' -->' . PHP_EOL);
	}
}
// the following examples hope to demonstrate the maximum of flexibility in usage

/* Example 1 a basic approach:
$variant = 'bacon';
$vb = new Variant_Base( $variant );

if ( $vb->find( $variant )->is_active() ) {
    $vb->debug_variant( $variant );
}
*/

/* Example 2 using the builtin ad blocking variant:
$vb = new Variant_Base();
if ( $vb->is_ad_blocked() ) {
    // Do something special
}
*/

/* Example 3 an extreme orthoginal approach:
$vb = new Variant_Base();
$variant = 'bacon';
if ( $vb->register( $variant )->find()->is_active() ) {
    $vb->debug_variant( $variant );
}
*/