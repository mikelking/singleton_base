<?php
/*
Plugin Name: Admin Message Class
Version: 1.0
Description: Adds a standardized base class for rendering messages in the WordPress Admin console. BY itself the plugin does nothing more
than make the class available to use. You must instantiate the AdminMessage in order to utilize it.
Author: Mikel King
Author URI: http://mikelking.com
Plugin URI: http://olivent.com/wordpress-plugins/konexus-admin_messages
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

// see: http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices

// I did this because I thought it might be useful and eventually phpunit testable

//         self::$setup->admin_error_msg(print_r(self::$setup, self::PRINT_OFF));

// Remember to implement any abstact methods.
class Admin_Message {
	const ERROR_DIV_CLASS_FMT = '<div id="message" class="error"><p><strong>%s</strong></p></div>';
	const NORMAL_DIV_CLASS_FMT = '<div id="message" class="updated"><p><strong>%s</strong></p></div>';
	const PRINT_OFF = true;

	private $admin_msg;

	public $error_level;

	public function __construct($message) {
		if ( isset( $message ) ) {
			$this->admin_msg = $message;
		}
	}

	public function get_admin_error_message() {
		if ( $this->admin_msg ) {
			$the_message = sprintf( self::ERROR_DIV_CLASS_FMT, $this->admin_msg );
			return($the_message);
		}
	}

	public function get_admin_normal_message() {
		if ( $this->admin_msg ) {
			$the_message = sprintf( self::NORMAL_DIV_CLASS_FMT, $this->admin_msg );
			return($the_message);
		}
	}

	public function display_admin_error_message() {
		print($this->get_admin_error_message());
	}

	public function display_admin_normal_message() {
		print($this->get_admin_normal_message());
	}

	public function set_admin_message_level($level) {
		if ( isset( $level ) ) {
			$this->error_level = $level;
		} else {
			$this->error_level = 'normal';
		}
	}

	public function get_the_admin_message() {
		if ( $this->error_level == 'error' ) {
			return($this->get_admin_error_message());
		} else {
			return($this->get_admin_normal_message());
		}
	}

	public function show_the_admin_message() {
		print($this->get_the_admin_message());
	}


	public static function print_eol() {
		print('<br>' . PHP_EOL);
	}
}
