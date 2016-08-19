<?php

/*
Plugin Name: URL Magick
Version: 1.1
Description: A simple framework for consistently manipulating URLs on TMBI sites
Author: Mikel King & Santhosh Kumar
Text Domain: url-magick
License: BSD(3 Clause)
License URI: http://opensource.org/licenses/BSD-3-Clause

	Copyright (C) 2014, Mikel King, olivent.com, (mikel.king AT rd DOT com) & Santhosh Kumar, rd.com, (santhosh.kumar@tmbi.com)
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
		This software without specific prior written permission.

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

// This class will eventually replace the url fixer plugin.

class URL_Magick {
	const URL_DELIM         = '/';
	const PROTOCOL_DELIM    = '://';
	const BASE_DELIM        = '/wp-content/uploads/';
	const NTWRK_DELIM       = 'sites/2/';
	const DEFAULT_CDN_URL   = 'www.rd.com';
	const DEFAULT_IMAGE_URL = 'http://www.rd.com/wp-content/themes/rdnap/img/desktop-logo.png';

	public static $site_url;
	public static $host_name;
	public static $current_host;

	public function __construct() {}

	public static function get_deliminator() {
		if ( is_multisite() ) {
			return( self::BASE_DELIM . self::NTWRK_DELIM );
		}
		return( self::BASE_DELIM );
	}

	public static function set_current_host() {
		self::$current_host = filter_var( $_SERVER['HTTP_HOST'], FILTER_SANITIZE_URL );
	}

	public static function get_current_host() {
		self::set_current_host();
		return( self::$current_host );
	}

	public static function get_default_image_url() {
		return( self::DEFAULT_IMAGE_URL );
	}

	public static function set_host_name() {
		self::get_site_url();
		$url_parts = explode( self::PROTOCOL_DELIM, self::$site_url );
		self::$host_name = $url_parts[1];

	}

	public static function get_host_name() {
		self::set_host_name();
		return(self::$host_name);
	}

	public static function set_site_url() {
		if ( is_multisite() ) {
			self::$site_url = network_site_url();
		} else {
			self::$site_url = site_url();
		}
	}

	public static function get_site_url() {
		self::set_site_url();
		return(self::$site_url);
	}

	public static function get_asset_path( $uri ) {
		if ( is_multisite() ) {
			self::$site_url = network_site_url();
			$asset = explode( self::NTWRK_DELIM, $uri );
			if ( isset( $asset[1] ) ) {
				return( self::get_deliminator() . $asset[1] );
			} else {
				return( self::get_deliminator() . $uri );
			}
		}
		return( $uri );
	}

	/* Disect a URL into four parts, protocol, host, URI and asset
     * @return ARRAY
	 * */
	public static function get_disected_url( $url ) {
		$cleaned_url = self::get_cleaned_url( $url );

		if ( stripos( $cleaned_url, self::NTWRK_DELIM ) ) {
			$url_parts = explode( self::get_deliminator(), $cleaned_url );
		} else {
			$url_parts = explode( self::BASE_DELIM, $cleaned_url );
		}

		if ( isset( $url_parts[1] ) ) {
			$uri = $url_parts[1];
		} else {
			$uri = $url_parts[0];
		}

		$url_parts2 = explode( self::PROTOCOL_DELIM, $url_parts[0] );

		$url = array(
			'protocol' => $url_parts2[0],
			'host' => $url_parts2[1],
			'URI' => self::get_deliminator() . $uri,
			'asset' => self::get_asset_path( self::get_deliminator() . $uri ),
		);

		return( $url );
	}

	public static function get_cdn_url( $url ) {
		$cleaned_url = self::get_cleaned_url( $url );

		$temp_url = self::get_disected_url( $cleaned_url );
		$temp_url['host'] = self::DEFAULT_CDN_URL;
		$url  = $temp_url['protocol'] . self::PROTOCOL_DELIM;
		$url .= $temp_url['host'];
		$url .= $temp_url['asset'];

		return( $url );
	}

	public static function get_asset_uri( $url ) {
		$cleaned_url = self::get_cleaned_url( $url );
		$url = self::get_disected_url( $cleaned_url );

		return( $url['asset'] );
	}

	public static function get_cleaned_url( $url ) {
		return( filter_var( $url, FILTER_SANITIZE_URL ) );
	}


	public static function print_url_parts( $url ) {
		$cleaned_url = self::get_cleaned_url( $url );
		$url = self::get_disected_url( $cleaned_url );
		var_dump( $url );
	}

	public static function get_cdn_permalink( $url ) {
		$cleaned_url = self::get_cleaned_url( $url );

		$url_parts = explode( self::PROTOCOL_DELIM, $cleaned_url );
		$url_elements = explode( self::URL_DELIM, $url_parts[1] );
		$elements = array_slice( $url_elements, 1 );
		$uri = implode( self::URL_DELIM, $elements );

		$cdn_url  = $url_parts[0] . self::PROTOCOL_DELIM;
		$cdn_url .= self::DEFAULT_CDN_URL . self::URL_DELIM;
		$cdn_url .= $uri;

		return( $cdn_url );
	}

}
