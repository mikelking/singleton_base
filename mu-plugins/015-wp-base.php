<?php
/**
 * Created by PhpStorm.
 * User: miking
 * Date: 6/16/15
 * Time: 11:49 AM
 * Version: 1.1
 */

class WP_Base extends Base_Plugin {

	/**
	 * Example of WordPress specific custom post type check
	 * @return bool
	 */
	public static function is_slideshow() {
		if ( get_post_type( get_the_ID() ) === 'slideshows' ) {
			return( true );
		}
		return( false );
	}

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

	public function get_url_to_dir( $asset_path ) {
		return( plugins_url( $asset_path, __DIR__ ));
	}

	public function get_url_to_file( $asset_path ) {
		return( plugins_url( $asset_path, __FILE__ ));
	}
}

