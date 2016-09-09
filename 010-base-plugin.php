<?php
/*
Plugin Name: Base Plugin Class
Version: 1.0
Description: Sets a standard class to build new plugin from.
Author: Mikel King
Text Domain: base-plugin
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

//Debug::enable_error_reporting();

class Base_Plugin extends Singleton_Base {
	const IN_FOOTER = true;
	const IN_HEADER = false;
	const FILE_SPEC = __FILE__;

	protected static $activated = false;

	protected function __construct() {}

	protected function activation_actions() {}

	protected function deactivation_actions() {}

	protected function uninstallation_actions() {}

	public function get_asset_url( $asset_file ) {
		return( plugins_url( $asset_file, static::FILE_SPEC ));
	}

	/**
	* @return bool
	*/
	public static function is_cms_user() {
		if ( is_array( $_COOKIE ) && ! empty( $_COOKIE ) ) {
			foreach ( array_keys( $_COOKIE ) as $cookie ) {
				if ( $cookie != 'wordpress_test_cookie' &&
					( substr( $cookie, 0, 2 ) == 'wp' ||
						substr( $cookie, 0, 9 ) == 'wordpress' ||
						substr( $cookie, 0, 14 ) == 'comment_author' ) ) {

					return(true);
				}
			}
		}
	}

	/**
	* Yet another example of high order abstraction.
	* The goal is to make as much of this plugin building;
	* as well plugable as possible
	*/
	public function init() {
		// This is how to add an activation hook if needed
		register_activation_hook( static::FILE_SPEC, array( $this, 'activator' ) );

		// This is how to add an deactivation hook if needed
		register_activation_hook( static::FILE_SPEC, array( $this, 'deactivator' ) );

		// This is how to add an uninstallation hook if needed
		register_uninstall_hook( static::FILE_SPEC, array( __CLASS__, 'uninstallor' ) );
	}

	public function activator() {
		if (! self::$activated) {
			self::$activated = true;
			$this->activation_actions();
		}
	}

	public function deactivator() {
		if (self::$activated) {
			$this->deactivation_actions();
		}
	}

	/**
	 * @todo need to review this and improve the handling
	 */
	public static function uninstallor() {
		if (self::$activated) {
			$this->uninstallation_actions();
		}
	}

}
