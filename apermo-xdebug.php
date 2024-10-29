<?php
/**
 * Apermo Xdebug
 *
 * @author      Christoph Daum
 * @copyright   2018 Christoph Daum
 * @license     GPL-2.0+
 * @package     apermo-xdebug
 *
 * @wordpress-plugin
 * Plugin Name: Apermo Xdebug
 * Plugin URI:  https://wordpress.org/plugins/apermo-xdebug/
 * Version:     1.2.2
 * Description: Indents xDebug messages inside the backend, so that these are no longer partly hidden underneath the admin menu. And it will also give you links to directly search for the error message on Google or Stackoverflow.
 * Author:      Christoph Daum
 * Author URI:  https://christoph-daum.de
 * Text Domain: apermo-xdebug
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Apermo Xdebug
 * Copyright (C) 2018, Christoph Daum - c.daum@apermo.de
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You shall not pass' );
}

if ( ! is_admin() ) {
	return;
}

if ( ! ini_get( 'display_errors' ) ) {
	add_action( 'admin_notices', function () {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><strong><?php esc_html_e( 'Apermo Xdebug:', 'apermo-xdebug' ); ?></strong> <?php echo esc_html_x( '"display_errors" is disabled.', 'Will be shown as admin notice if the php.ini setting display_errors is false.', 'apermo-xdebug' ); ?></p>
		</div>
		<?php
	} );
	return;
}

if ( ! function_exists( 'xdebug_get_code_coverage' ) ) {
	add_action( 'admin_notices', function () {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><strong><?php esc_html_e( 'Apermo Xdebug:', 'apermo-xdebug' ); ?></strong> <?php echo esc_html_x( 'Xdebug is not active.', 'Will be shown as admin notice in case Xdebug wasn\'t automatically detected.', 'apermo-xdebug' ); ?></p>
		</div>
		<?php
	} );
	return;
}
/**
 * Class ApermoXdebug
 *
 * Formats Xdebug output inside the WordPress Backend to not interfeer with the Menu.
 */
class ApermoXdebug {
	/**
	 * Store the search engines.
	 *
	 * @var $search_urls
	 */
	private $search_urls;

	/**
	 * ApermoXdebug constructor.
	 */
	public function __construct() {
		add_action( 'admin_head', array( $this, 'print_css' ) );
		add_action( 'admin_head', array( $this, 'print_javascript' ) );

		$this->search_urls = apply_filters(
			'apermo_xdebug_search_urls',
			[
				'google' => [
					'url' => 'https://www.google.com/search?q=\'+search_term+\'',
					'label' => __( 'Search on Google', 'apermo-xdebug' ),
				],
				'stackoverflow' => [
					'url' => 'https://www.stackoverflow.com/search?q=\'+search_term+\'',
					'label' => __( 'Search on Stackoverflow', 'apermo-xdebug' ),
				],
			]
		);
	}

	/**
	 * Outputs <style> for the WordPress Backend.
	 * Called by hook: admin_head
	 */
	public function print_css() {
		?>
		<style id="apermo-xdebug">
			.xdebug-error, .xdebug-var-dump {
				width: calc( 100vw - 200px );
				margin-right: 20px;
				margin-bottom: 20px;
				position: relative;
				z-index: 9991;
				margin-left: 180px;
			}
			.xdebug-error:after, .xdebug-var-dump:after  {
				display:block;
				content: '';
				clear:both;
			}
			/* Had to move this a bit, otherwise links wouldn't be clickable in the Xdebug notice */
			#adminmenuwrap {
				z-index: 9992;
				top: 32px;
			}

			.folded .xdebug-error,
			.folded .xdebug-var-dump {
				width: calc( 100vw - 80px );
				margin-left: 60px;
			}
			.xdebug-error .apermo-xdebug-link {
				display: inline-block;
				color: #333;
				background: #eee;
				border: 2px solid #333;
				padding: 5px;
				text-decoration: none;
				margin: 10px 0 10px 10px;
			}
			.xdebug-error .apermo-xdebug-link:first-of-type {
				margin-left: 0;
			}
			@media screen and (max-width: 960px) {
				.auto-fold .xdebug-error,
				.auto-fold .xdebug-var-dump {
					width: calc( 100vw - 80px );
					margin-left: 60px;
				}
			}
			@media screen and (max-width: 782px) {
				.auto-fold .xdebug-error,
				.auto-fold .xdebug-var-dump {
					margin-right: 10px;
					margin-left: 10px;
					width: calc( 100vw - 20px );
				}
			}
		</style>
		<?php
	}

	/**
	 * Outputs <javascript> for the WordPress Backend.
	 * Called by hook: admin_head
	 */
	public function print_javascript() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				setTimeout(function () {
					if ( ! $('.xdebug-error, .xdebug-var-dump').length ) {
						$( '#apermo-xdebug').remove();
					}
				},500);
				const wp_path = '<?php echo esc_url( get_home_path() ); ?>';
				$('.xdebug-error').each(function () {
					var regex = new RegExp( wp_path, 'g' ),
						search_term = encodeURI (
							$(this).find('tr:first-of-type th').html()
								.replace( regex, '/' )
								.replace(/<\/?[^>]+(>|$)/g, '')
								.replace( '( ! )', '' )
								.trim()
						);
					var $th = $(this).find('tr:first-of-type th');
					$th.append('<br>');
					<?php
					foreach ( $this->search_urls as $search_url ) {
					?>
					$th.append('<a href="<?php echo $search_url['url']; ?>" class="apermo-xdebug-link" target="_blank"><?php echo esc_html( $search_url['label'] ); ?></a>');
					<?php
					}
					?>
				});
			});
		</script>
		<?php
	}
}

// Run boy, run!
add_action( 'plugins_loaded', function () {
	new ApermoXdebug();
} );
