<?php
/*
Plugin Name: Singleton Base Class
Version: 1.4
Description: Sets a standard class to build new plugin from.
Author: Mikel King
Text Domain: singleton-base-plugin
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

abstract class Singleton_Base {
	const ENABLED       = true;
	const DISABLED      = false;
	const EXCEPTION_HDR = 'PHP Exception:  ';
	const DEFAULT_TZ    = 'America/New_York';

	private static $instance;
	private static $instatiator;

	public static $class_name;
	public static $exception_msg_hdr;
	public static $exception_msg_dvdr;

	protected function __construct() {}

	private function __clone() {}

	//	private function __wakeup() {}

	public static function init() {}

	public function __call( $method_name, $arguments ) {
		static::$exception_msg_hdr = 'Unknown method: ';
		static::$exception_msg_dvdr = '->';
		static::call_exception_handler( $method_name, $arguments );
	}

	public function set_class_name( $class = null ) {
		if ( $class ) {
			static::$class_name = $class;
		} else {
			static::$class_name = get_class( static::$instance );
		}
	}

	public static function __callStatic( $method_name, $arguments ) {
		static::$exception_msg_hdr = 'Unknown static method: ';
		static::$exception_msg_dvdr = '::';
		static::call_exception_handler( $method_name, $arguments );
	}

	public static function get_class_name() {
		if ( static::$class_name ) {
			return( static::$class_name );
		}

		return( get_class( static::$instance ) );
	}

	public static function get_arguments( $arguments = null ) {
		$exception_msg = ' with no arguments.';
		if ( isset( $arguments ) && is_array( $arguments ) ) {
			$exception_msg = ' with these arguments: ';
			$exception_msg .= implode( ', ', $arguments );
		} elseif ( isset( $arguments ) && is_string( $arguments ) ) {
			$exception_msg = ' with this argument: ' .  $arguments;
		}
		return( $exception_msg );
	}

	public static function get_exception_msg( $method_name, $arguments ) {
		$exception_msg  = static::$exception_msg_hdr;
		$exception_msg .= static::get_class_name() . static::$exception_msg_dvdr . $method_name;
		$exception_msg .= self::get_arguments( $arguments );
		$exception_msg .= self::get_instantiator_msg();

		return( $exception_msg );
	}

	public static function call_exception_handler( $method_name, $arguments ) {
		self::throw_exception_exception( self::get_exception_msg( $method_name, $arguments ) );
	}

	/**
	* This is where all of the magick happens
	* @param $exception_msg
	*/
	public static function throw_exception_exception( $exception_msg ) {
		error_log( static::EXCEPTION_HDR . $exception_msg, 0 );
	}

	/**
	* @param null $instatiator
	* @todo recommended for deprecation
	*/
	public static function set_instantiator( $instatiator = null ) {
		if ( isset( $instatiator ) ) {
			self::$instatiator = $instatiator;
		}
	}

	/**
	* @param null $instatiator
	* @todo recommended for deprecation
	*/
	public static function get_instantiator_msg() {
		$msg = ' by an undetermined instantiation';
		if ( self::$instatiator ) {
			$msg = ' instantiated by this file: ' . self::$instatiator;
		}
		return( $msg );
	}

	public static function get_instance() {
		self::set_tz();

		if ( static::$instance === null ) {
			static::$instance = new static();
		}

		return( static::$instance );
	}

	public static function set_tz() {
		date_default_timezone_set( static::DEFAULT_TZ );
	}
}
