<?php
/*
Plugin Name: Debug
Version: 3.1
Description: Provides a simple object framework insert debugging data into the content stream with easy to locate markers. BY itself the plugin does nothing more
than make the class available to use. You must instantiate the Debugger in order to utilize it.
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
 * Class Debug
 *
 * @author Mikel King <mikel.king@olivent.com>
 * @license BSD
 * @copyright 2011
 */
class Debug {
	const VERSION           = '3.1';
	const LOG_404_ERRORS    = false;
	const MKEAPI            = false;	// True to activate the psuedo api
	const LOG_MKEAPI        = true;		// True to activate request & response logging
	const LOG_MKEAPI_HEADER = true;		// True to activate header logging
	const DBG_MKEAPI_LOG    = false;
	const DEBUG_SOURCEPOINT_API = true;

	public $debug_label;
	public $debug_value;
	public $debug_environment;
	public $debug_this = false;
	public $target_addr;
	public $whitelist;
	public $whitelisted;
	public $debug_output_enabled = false;
	public static $debug_data;

	public function __construct() {
		$this->whitelist = array(
						ip2long( '10.252.8.0' ) => ip2long( '10.252.11.255' ),
						ip2long( '172.16.0.0' ) => ip2long( '172.31.255.255' ),
					);

		$this->get_debug_this_flag();
		/*         $this->get_debug_environment(); */
		$this->get_target_addr();
		/*         $this->whitelist_check(); */
		$this->whitelisted = true;
		$this->set_debug_label();
		$this->set_debug_output();
	}

	public function start_debug_comment_block() {
		printf( "<!-- Begin debug comment thread %s\n", $this->debug_label );
	}

	public function end_debug_comment_block() {
		printf( "End debug comment thread %s --> \n", $this->debug_label );
	}

	public function short_debug_comment() {
		printf( "<!-- debug comment %s -->\n", $this->debug_label );
	}

	public function render_debug_data() {
		if ( isset( self::$debug_data ) ) {
			var_dump( self::$debug_data );
		}
	}

	public function debug_store($value) {
		if ( $this->debug_output_enabled === true ) {
			if ( isset( $value ) ) {
				if ( is_array( $value ) || is_object( $value ) ) {
					self::$debug_data .= sprintf( '<!-- %s ', $this->debug_label );
					self::$debug_data .= print_r( $value, true );
					self::$debug_data .= sprintf( " --> \n" );
				} else {
					self::$debug_data .= sprintf( "<!-- %s ==== %s -->\n", $this->debug_label, $value );
				}
			} else {
				self::$debug_data .= sprintf( "<!-- %s -- MARK POINT assumed because &#36;value was not set. -->\n", $this->debug_label );
			}
		}
	}

	public function debug_print($value) {
		if ( $this->debug_output_enabled === true ) {
			if ( isset( $value ) ) {
				if ( is_array( $value ) || is_object( $value ) ) {
					printf( '<!-- %s ', $this->debug_label );
					var_dump( $value );
					print(" --> \n");
				} else {
					printf( "<!-- %s ==== %s -->\n", $this->debug_label, $value );
				}
			} else {
				printf( "<!-- %s -- MARK POINT assumed because &#36;value was not set. -->\n", $this->debug_label );
			}
		}
	}

	public function debug_print_r($value) {
		if ( $this->debug_output_enabled === true ) {
			if ( isset( $value ) ) {
					printf( '<!-- %s ', $this->debug_label );
					print_r( $value );
					print(" --> \n");
			}
		} else {
			printf( "<!-- %s -- MARK POINT assumed because &#36;value was not set. -->\n", $this->debug_label );
		}
	}

	public function debug_dump($value) {
		if ( $this->debug_output_enabled === true ) {
			if ( isset( $value ) ) {
					printf( '<!-- %s ', $this->debug_label );
					var_dump( $value );
					print(" --> \n");
			}
		} else {
			printf( "<!-- %s -- MARK POINT assumed because &#36;value was not set. -->\n", $this->debug_label );
		}
	}

	public function set_debug_label( $label = null ) {
		if ( isset( $label ) ) {
			$this->debug_label = $label;
		} else {
			$this->debug_label = 'Default Label';
		}
	}

	public function get_debug_this_flag() {
		if ( isset( $_REQUEST['debug'] ) ) {
			if ( $_REQUEST['debug'] === '1' || $_REQUEST['debug'] === 'true' ) {
				$this->debug_this = true;
			}
		}
	}

	public function set_debug_output() {
		if ( $this->debug_this && $this->whitelisted ) {
			$this->debug_output_enabled = true;
		} else {
			$this->debug_output_enabled = false;
		}
	}

	/*
        This section requires some improvement; a switch statement could clean this up.
    */
	public function get_debug_environment() {
		if ( isset( $_SERVER['ENVIRONMENT'] ) ) {
			$this->debug_environment = 'dev';
		} else {
			/*
                Although I am no fond of ternary conditionals as they force you to
                think backwards there are some rare cases that I do find them useful
            */
			$this->debug_environment = (isset( $_SERVER['ETSY_ENVIRONMENT'] )) ? $_SERVER['ETSY_ENVIRONMENT'] : 'production';
		}
	}

	public function get_target_addr() {
		if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$this->target_addr = ip2long( $_SERVER['HTTP_X_FORWARDED_FOR'] );
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$this->target_addr = ip2long( $_SERVER['REMOTE_ADDR'] );
		}
	}

	public function whitelist_check() {
		foreach ( $this->whitelist as $network => $broadcast ) {
			if ( ($this->target_addr > $network) && ($this->target_addr < $broadcast) ) {
				$this->whitelisted = true;
			} else {
				$this->whitelisted = false;
			}
		}
	}

	public static function print_file_name( $file = null ) {
		$filename = __FILE__;
		if ( $file && file_exists( $file ) ) {
			$filename = $file;
		}


		if ( isset( $_GET['debug'] ) ) {
			print('<!-- FileName: ' . $filename . ' -->' . PHP_EOL);
		}
	}

	/**
	 * @return bool
	 */
	public static function log_mke_api() {
		if ( self::LOG_MKEAPI ||
			( isset( $_GET['debug'] ) &&
				$_GET['debug'] === 'logmkeapi' ) ) {
			return( true );
		}
		return( false );
	}

	/**
	 * @return bool
	 */
	public static function log_mke_api_header() {
		if ( self::LOG_MKEAPI_HEADER ||
			( isset( $_GET['debug'] ) &&
				$_GET['debug'] === 'logmkeapihdr' ) ) {
			return( true );
		}
		return( false );
	}

	/**
	 * @return bool
	 */
	public static function debug_the_mkeapi_log() {
		return(self::DBG_MKEAPI_LOG);
	}

	/**
	 * @return bool
	 */
	public static function debug_mke_api() {
		if ( self::MKEAPI ||
			( isset( $_GET['debug'] ) &&
			$_GET['debug'] === 'mkeapi' ) ) {
			return( true );
		}
		return( false );
	}

	public static function print_cookie() {
		if ( isset( $_GET['debug'] ) && $_GET['debug'] === 'cookie' ) {
			print('<h2>Cookie obj</h2>' . PHP_EOL);
			var_dump( $_COOKIE );
			print('<h3>Cookie obj</h3>' . PHP_EOL);
			exit;
		}
	}

	public static function print_wp_rewrite() {
		if ( isset( $_GET['debug'] ) && $_GET['debug'] === 'wprewrite' ) {
			global $wp_rewrite;
			print('<h2>WP Rewrite obj</h2>' . PHP_EOL);
			var_dump( $wp_rewrite );
			print('<h3>WP Rewrite obj</h3>' . PHP_EOL);
			exit;
		}
	}

	public static function print_serialized_wp_rewrite() {
		if ( isset( $_GET['debug'] ) && $_GET['debug'] === 'swprewrite' ) {
			global $wp_rewrite;
			print('<h2>WP Rewrite obj</h2>' . PHP_EOL);
			print(serialize( $wp_rewrite ));
			print('<h3>WP Rewrite obj</h3>' . PHP_EOL);
			exit;
		}
	}

	public static function print_wp_query() {
		if ( isset( $_GET['debug'] ) && $_GET['debug'] === 'wpquery' ) {
			global $wp_query;
			print('<h2>WP Query obj</h2>' . PHP_EOL);
			var_dump( $wp_query );
			print('<h3>WP Query obj</h3>' . PHP_EOL);
			exit;
		}
	}

	public static function print_wp_page() {
		if ( isset( $_GET['debug'] ) && $_GET['debug'] === 'wppage' ) {
			global $pages;
			print('<h2>WP Page obj</h2>' . PHP_EOL);
			var_dump( $pages );
			print('<h3>WP Page obj</h3>' . PHP_EOL);
			exit;
		}
	}

	public static function print_wp_post() {
		if ( isset( $_GET['debug'] ) && $_GET['debug'] === 'wppost' ) {
			global $post;
			print('<h2>WP Post obj</h2>' . PHP_EOL);
			var_dump( $post );
			print('<h3>WP Post obj</h3>' . PHP_EOL);
			exit;
		}
	}

	public static function print_canonical() {
		if ( isset( $_GET['debug'] ) && $_GET['debug'] === 'canonical' ) {
			printf( '<h2>The permalink should be: %s </h2>', str_ireplace( 'https', 'http', get_permalink() ) );
			exit;
		}
	}

	public static function print_noindex( $msg ) {
		if ( isset( $_GET['debug'] ) && $_GET['debug'] === 'noindex' ) {
			print( $msg );
		}
	}

	public static function print_wp_script( $script_slug, $format = null ) {
		$msg = $script_slug . 'was not found.';
		$fmt = '<!-- % -->';
		if ( isset( $format ) ) {
			$fmt = $format;
		}

		if ( isset( $_GET['debug'] ) && $_GET['debug'] === 'wpscript' ) {
			//            $options = array(
			//                'flags' => FILTER_FLAG_STRIP_LOW
			//            );
			//            $script_slug = filter_var($slug, FILTER_SANITIZE_STRING, $options);
			$msg = $script_slug . ' is just a dream!!!';

			if ( wp_script_is( $script_slug, $list = 'enqueued' ) ) {
				$msg = $script_slug . ' is indeed enqueued!!!';
			} elseif ( wp_script_is( $script_slug, $list = 'registered' ) ) {
				$msg = $script_slug . ' is indeed registered!!!';
			} elseif ( wp_script_is( $script_slug, $list = 'to-do' ) ) {
				$msg = $script_slug . ' is not ready!!!';
			}
		}
		printf( $fmt, $msg . PHP_EOL );
	}

	public static function print_wp_order_listing($msg) {
		if ( isset( $_GET['debug'] ) && $_GET['debug'] === 'articlesorder' ) {
			print('<div style="color:red">Article Pubhlished on : ' .$msg .'</div>'. PHP_EOL);
		}
	}

	public static function print_test_ads_unit($msg) {
		if ( isset( $_GET['debug'] ) && $_GET['debug'] === 'adsunit' ) {
			print('<p style="color:red">Debug ads Unit : ' .$msg .'</p>'. PHP_EOL);
		}
	}

	/**
	 * @return bool
	 */
	public static function log_404_errors() {
		if ( self::LOG_404_ERRORS ||
			( isset( $_GET['debug'] ) &&
				$_GET['debug'] === 'log404' ) ) {
			return( true );
		}
		return( false );
	}

	public static function enable_error_reporting() {
		ini_set( 'display_errors', 'On' );
		error_reporting( E_ALL & ~E_STRICT );
	}

	public static function debug_sourcpoint_api() {
		if ( self::DEBUG_SOURCEPOINT_API ||
			( isset( $_GET['debug'] ) &&
				$_GET['debug'] === 'SourcpointApi' ) ) {
			return( true );
		}
		return( false );
	}
}
