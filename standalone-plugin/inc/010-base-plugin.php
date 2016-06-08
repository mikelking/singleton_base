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

class Base_Plugin extends Singleton_Base {
    const IN_FOOTER = true;
    const IN_HEADER = false;
    
    protected static $activated = false;

    protected function __construct() {}

    protected function activation_actions() {}

    protected function deactivation_actions() {}

    protected function uninstallation_actions() {}

    public function get_asset_url( $asset_file, $path = null ) {
        return( plugins_url( $asset_file, $path ));
    }

    protected static function init() {
        // This is how to add an activation hook if needed
        register_activation_hook( __FILE__, array( 'Your_Plugin_Controller', '__activator' ) );

        // This is how to add an deactivation hook if needed
        register_activation_hook( __FILE__, array( 'Your_Plugin_Controller', '__deactivator' ) );

        // This is how to add an uninstallation hook if needed
        register_uninstall_hook( __FILE__, array( 'Your_Plugin_Controller', '__uninstallor' ) );
    }

    public function __activator() {
        if (! self::$activated) {
            self::$activated = true;
            $this->activation_actions();
        }
    }

    public function __deactivator() {
        if (self::$activated) {
            $this->deactivation_actions();
        }
    }

    public function __uninstallor() {
        if (self::$activated) {
            $this->uninstallation_actions();
        }
    }

}
