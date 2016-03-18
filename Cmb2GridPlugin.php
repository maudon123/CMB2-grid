<?php

namespace Cmb2Grid;

if (!defined('CMB2GRID_DIR')) {
	define('CMB2GRID_DIR', trailingslashit(dirname(__FILE__)));
}

/*
  Plugin Name: CMB2 Grid
  Plugin URI: https://github.com/origgami/CMB2-grid
  Description: A grid system for Wordpress CMB2 library that allows columns creation
  Version: 1.0.0
  Author: Origgami
  Author URI: http://origgami.com.br
  License: GPLv2
 */

if (!class_exists('\Cmb2Grid\Cmb2GridPlugin')) {

	require_once dirname(__FILE__) . '/DesignPatterns/Singleton.php';

	class Cmb2GridPlugin extends DesignPatterns\Singleton {

		const VERSION = '1.0';

		protected function __construct() {
			parent::__construct();
			$this->loadFiles();
			add_action('admin_head', array($this, 'wpHead'));
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
			//$this->test();
		}

		private function test() {
			require dirname(__FILE__) . '/Test/Test.php';
			new Test\Test();
		}

		private function loadFiles() {
			if (is_admin()) {
				require dirname(__FILE__) . '/Grid/Cmb2Grid.php';
				require dirname(__FILE__) . '/Grid/Column.php';
				require dirname(__FILE__) . '/Grid/Row.php';

				require dirname(__FILE__) . '/Grid/Group/Cmb2GroupGrid.php';
				require dirname(__FILE__) . '/Grid/Group/GroupRow.php';
				require dirname(__FILE__) . '/Grid/Group/GroupColumn.php';


				require dirname(__FILE__) . '/Cmb2/Utils.php';
			}
		}

		public function admin_enqueue_scripts() {
			$suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min' );
			wp_enqueue_style( 'cmb2_grid_bootstrap_light', $this->url('assets/css/bootstrap' . $suffix . '.css'), null, self::VERSION );
		}

		public function wpHead() {
			?>
			<style>
				.cmb2GridRow .cmb-row{border:none !important;padding:0 !important}
				.cmb2GridRow .cmb-th label:after{border:none !important}
				.cmb2GridRow .cmb-th{width:100% !important}
				.cmb2GridRow .cmb-td{width:100% !important}
				.cmb2GridRow input[type="text"], .cmb2GridRow textarea, .cmb2GridRow select{width:100%}

				.cmb2GridRow .cmb-repeat-group-wrap{max-width:100% !important;}
				.cmb2GridRow .cmb-group-title{margin:0 !important;}
				.cmb2GridRow .cmb-repeat-group-wrap .cmb-row .cmbhandle, .cmb2GridRow .postbox-container .cmb-row .cmbhandle{right:0 !important}
			</style>
			<?php

		}

		// Based on CMB2_Utils url() method
		public function url($path = '') {
			if (isset($this->url)) {
				return $this->url . $path;
			}

			if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
				// Windows
				$content_dir = str_replace('/', DIRECTORY_SEPARATOR, WP_CONTENT_DIR);
				$content_url = str_replace($content_dir, WP_CONTENT_URL, CMB2GRID_DIR);
				$cmb2_url	 = str_replace(DIRECTORY_SEPARATOR, '/', $content_url);
			} else {
				$cmb2_url = str_replace(
					array(WP_CONTENT_DIR, WP_PLUGIN_DIR),
					array(WP_CONTENT_URL, WP_PLUGIN_URL),
					CMB2GRID_DIR
				);
			}

			/**
			 * Filter the CMB location url
			 *
			 * @param string $cmb2_url Currently registered url
			 */
			$this->url = trailingslashit(apply_filters('cmb2_meta_box_url', set_url_scheme($cmb2_url), CMB2_VERSION));

			return $this->url . $path;
		}

	}

}


/* Instantiate the class on plugins_loaded. */
// wp_installing() function was introduced in WP 4.4.
if ( ( function_exists( 'wp_installing' ) && wp_installing() === false ) || ( ! function_exists( 'wp_installing' ) && ( ! defined( 'WP_INSTALLING' ) || WP_INSTALLING === false ) ) ) {
	add_action( 'plugins_loaded', '\\' . __NAMESPACE__ . '\init' );
}

if ( ! function_exists( '\Cmb2Grid\init' ) ) {
	/**
	 * Initialize the class only if CMB2 is detected.
	 *
	 * @return void
	 */
	function init() {
		if ( defined( 'CMB2_LOADED' ) ) {
			if (!defined('CMB2GRID_DIR')) {
				define('CMB2GRID_DIR', trailingslashit(dirname(__FILE__)));
			}
			Cmb2GridPlugin::getInstance();
		}
	}
}

