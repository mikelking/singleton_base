<?php

/*
Plugin Name: Custom Post Type Controller
Version: 1.0
Description: A abstracted framework for working with Custom Post Types.
Author: Mikel King
Text Domain: cpt-controller
License: BSD(3 Clause)
License URI: http://opensource.org/licenses/BSD-3-Clause

Copyright (C) 2014, Mikel King, rd.com, (mikel.king AT rd DOT com)
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
 * Class Custom_Post_Type_Controller is an attempt to build a simple class
 * for fast cpt creation. The constants have that are defined are set to the
 * sensible defaults and even given names to hopefully make their function more
 * understandable. A comment is provide where the constant name differs from
 * the codex label name.
 *
 * @link https://codex.wordpress.org/Function_Reference/register_post_type
 * @link https://developer.wordpress.org/resource/dashicons/
 */
class Custom_Post_Type_Controller {
	const NAME = '';
	const SINGULAR_NAME = '';
	const DESCRIPTION = '';
	const CAPABILITY_TYPE = 'post';
	const PUBLICLY_ACCESSIBLE = true;	// label => public
	const PUBLICLY_QUERYABLE = true;
	const CUSTOM_ADMIN_UI = true;	// label => show_ui
	const EXCLUDE_SEARCH = false;	// label => exclude_from_search
	const ADD_TO_MENUS   = true;	// label => show_in_nav_menus
	const ADD_TO_ADMIN_MENU = true;	// label => show_in_menu (bool or string)
	const ADD_TO_ADMIN_BAR = false; // label => show_in_admin_bar
	const MENU_POSITION = 20;	// label => menu_position (default below pages)
	const MENU_ICON = null;	// defaults to post icon
	const META_CAP = true; // map_meta_cap
	const ARCHIVE_PAGE = true;
	const EXPORTABLE = true;
	const TEXT_DOMAIN = '';
	const DEBUG = false;

	public $labels;
	public $capabilities;
	public $cpt_args;
	public $rewrite_args;
	public $support_args;
	public $taxonomies;
	public $lc_name;
	public $lc_singular_name;

	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * unsure about the validity & requirement for this init()
	 */
	public function init() {
		$this->set_lc_name();
		$this->set_lc_singular_name();
		$this->set_labels();
		$this->set_support_args();
//		$this->set_capabilities();
		$this->set_taxonomies();
		$this->set_rewrite_args();
		$this->set_cpt_args();
		$this->register_cpt();
	}

	public function register_cpt() {
		$msg = static::SINGULAR_NAME . ' CPT: ';
		try {
			register_post_type( $this->lc_singular_name, $this->cpt_args );
			if ( static::DEBUG && is_admin() ) {
				$dump_msg = var_export( $this->cpt_args, true );
				$am = new Admin_Message( $msg . $dump_msg );
				$am->display_admin_normal_message();
			} elseif ( static::DEBUG && is_page( 'debug' ) ) {
				// http://dev.rdnap/debug/ example
				var_dump( $this->cpt_args );
			}
		} catch ( WP_Exception $e ) {
			var_dump( $this->cpt_args );
			return( true );
		}
	}

	public function set_lc_name() {
		return( $this->lc_name = strtolower( static::NAME ) );
	}

	public function set_lc_singular_name() {
		return( $this->lc_singular_name = strtolower( static::SINGULAR_NAME ) );
	}

	public function set_labels() {
		$this->labels = array(
			'name' => _x( static::NAME, 'post type general name', static::TEXT_DOMAIN ),
			'singular_name' => _x( static::SINGULAR_NAME, 'post type singular name', static::TEXT_DOMAIN ),
			'menu_name'          => _x( static::NAME, 'admin menu', static::TEXT_DOMAIN ),
			'name_admin_bar'     => _x( static::SINGULAR_NAME, 'add new on admin bar', static::TEXT_DOMAIN ),
			'add_new' => _x( 'Add New', $this->lc_singular_name, static::TEXT_DOMAIN ),

			'add_new_item'       => __( 'Add New ' . static::SINGULAR_NAME, static::TEXT_DOMAIN ),
			'new_item'           => __( 'New ' . static::SINGULAR_NAME, static::TEXT_DOMAIN ),
			'edit_item'          => __( 'Edit ' . static::SINGULAR_NAME, static::TEXT_DOMAIN ),
			'view_item'          => __( 'View ' . static::SINGULAR_NAME, static::TEXT_DOMAIN ),
			'all_items'          => __( 'All ' . static::NAME, static::TEXT_DOMAIN ),
			'search_items'       => __( 'Search ' . static::NAME, static::TEXT_DOMAIN ),
			'parent_item_colon'  => __( 'Parent ' . static::NAME . ':', static::TEXT_DOMAIN ),
			'not_found'          => __( 'No ' . $this->lc_name . ' found.', static::TEXT_DOMAIN ),
			'not_found_in_trash' => __( 'No ' . $this->lc_name . ' found in Trash.', static::TEXT_DOMAIN )
		);
	}

	/**
	 * Will implement this in next iteration
	 */
	public function set_capabilities() {
		$this->capabilities = array(
			'edit_post'          => 'edit_' . $this->lc_singular_name,
			'read_post'          => 'read_' . $this->lc_singular_name,
			'delete_post'        => 'delete_' . $this->lc_singular_name,
			'edit_posts'         => 'edit_' . $this->lc_name,
			'edit_others_posts'  => 'edit_others_' . $this->lc_name,
			'publish_posts'      => 'publish_' . $this->lc_name,
			'read_private_posts' => 'read_private_' . $this->lc_name,
			'create_posts'       => 'edit_' . $this->lc_name
		);
	}

	public function set_cpt_args() {
		$this->cpt_args = array(
			'labels' => $this->labels,
			'description' => __( static::DESCRIPTION, static::TEXT_DOMAIN ),
			'public' => static::PUBLICLY_ACCESSIBLE,
			'exclude_from_search' => static::EXCLUDE_SEARCH,
			'publicly_queryable' => static::PUBLICLY_QUERYABLE,
			'show_ui' => static::CUSTOM_ADMIN_UI,
			'show_in_nav_menus' => static::ADD_TO_MENUS,
			'show_in_menu' => static::ADD_TO_ADMIN_MENU,
			'show_in_admin_bar' => static::ADD_TO_ADMIN_BAR,
			'menu_position' => static::MENU_POSITION,
			'menu_icon' => static::MENU_ICON,
			'capability_type' => static::CAPABILITY_TYPE,
			'map_meta_cap' => static::META_CAP,
			'has_archive' => static::ARCHIVE_PAGE
		);

		if ( isset( $this->capabilities ) ) {
			$this->cpt_args['capabilities'] = $this->capabilities;
		}

		if ( isset( $this->support_args ) ) {
			$this->cpt_args['supports'] = $this->support_args;
		}

		if ( isset( $this->rewrite_args ) ) {
			$this->cpt_args['rewrite'] = $this->rewrite_args;
		}

		if ( isset( $this->taxonomies ) ) {
			$this->cpt_args['taxonomies'] = $this->taxonomies;
		}

	}

	public function set_rewrite_args() {
		$this->rewrite_args = array(
			'slug' => $this->lc_name,
			'with_front' => false,
			'feeds' => true
		);
	}

	/**
	 * Default taxonomy setting override in your child for alternatives
	 */
	public function set_taxonomies() {
		$this->taxonomies = array( 'category', 'post_tag', 'brand' );
	}

	/**
	 *
	 */
	public function set_support_args() {
		$this->support_args = array(
			'title',
			'editor',
			'author',
			'custom-fields',
			'thubmnail',
			'excerpt',
			'revisions'
		);
	}

}