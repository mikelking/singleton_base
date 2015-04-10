<?php
namespace YOUR_PLUGIN_NAMESPACE
/*
Plugin Name: YOUR_PLUGIN_NAME
Version: 1.0
Description: YOUR_PLUGIN_DESCRIPTION
Author: Mikel King
Text Domain: YOUR_PLUGIN_TEXT_DOMAIN
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

require(__DIR__ . '/inc/singleton-base.php');

class Your_Plugin_Controller extends Base_Plugin {
    const VERSION = '1.0';

    protected function __construct() {
        register_activation_hook( __FILE__, array( 'Your_Plugin_Controller', '__activator' ) );
        register_activation_hook( __FILE__, array( 'Your_Plugin_Controller', '__deactivator' ) );
        register_uninstall_hook( __FILE__, array( 'Your_Plugin_Controller', '__uninstallor' ) );
    }



    protected function activation_actions() {}

    protected function deactivation_actions() {}

    protected function uninstallation_actions() {}
}

$ypc = Your_Plugin_Controller::get_instance();