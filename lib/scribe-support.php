<?php
if(!class_exists('Scribe_Support')) {
	class Scribe_Support {
		
		const TEST_URL = 'http://api.scribeseo.com/test.htm';
		const TEST_URL_SECURE = 'https://api.scribeseo.com/test.htm';

		private $_tests = array();

		// INITIALIZE

		public function __construct() {
			$this->register_tests();
		}

		private function register_tests() {
			$this->register_test(__('WordPress Version', 'scribe'), array(__CLASS__, 'get_wordpress_version'));
			$this->register_test(__('PHP Version', 'scribe'), array(__CLASS__, 'get_php_version'));
			$this->register_test(__('Scribe Installed?', 'scribe'), array(__CLASS__, 'is_scribe_installed'));
			$this->register_test(__('Scribe Version', 'scribe'), array(__CLASS__, 'get_scribe_version'));
			$this->register_test(__('Scribe API Key', 'scribe'), array(__CLASS__, 'get_scribe_api_key'));
			$this->register_test(__('Activated Dependency', 'scribe'), array(__CLASS__, 'get_activated_dependency'));
			$this->register_test(__('SSL Connect', 'scribe'), array(__CLASS__, 'get_connect_via_ssl'));
			$this->register_test(__('Non-SSL Connect', 'scribe'), array(__CLASS__, 'get_connect_via_non_ssl'));
			$this->register_test(__('Web Server', 'scribe'), array(__CLASS__, 'get_web_server'));
			$this->register_test(__('Server IP', 'scribe'), array(__CLASS__, 'get_ip_address'));
			$this->register_test(__('Installed Themes', 'scribe'), array(__CLASS__, 'get_installed_themes'));
			$this->register_test(__('Activated Theme', 'scribe'), array(__CLASS__, 'get_activated_theme'));
			$this->register_test(__('Installed Plugins', 'scribe'), array(__CLASS__, 'get_installed_plugins'));
			$this->register_test(__('Activated Plugins', 'scribe'), array(__CLASS__, 'get_activated_plugins'));
		}

		// UTILITY

		public function get_test_results() {
			$results = array();
			foreach($this->_tests as $test) {
				if(is_callable($test['callback'])) {
					$test_result = call_user_func($test['callback']);

					$results[] = array('name' => esc_html( $test['name'] ), 'value' => esc_html( $test_result['value'] ), 'warn' => $test_result['warn']);
				}
			}
			return $results;
		}

		public function register_test($name, $callback) {
			if(is_callable($callback)) {
				$this->_tests[] = array('name' => $name, 'callback' => $callback);
			}
			
			return count($this->_tests);
		}

		/// CALLBACKS

		function get_wordpress_version() {
			global $wp_version;
			return array('value' => $wp_version, 'warn' => version_compare($wp_version, '3.0', '<'));
		}

		public static function get_php_version() {
			return array('value' => phpversion(), 'warn' => false);
		}

		public static function get_activated_dependency() {
			$warn = true;
			
			if(class_exists('Scribe_SEO')) {
				$settings = Scribe_SEO::get_settings();
				$seo_tool_settings = is_object($settings['seo-tool-settings']) ? $settings['seo-tool-settings'] : new stdClass;
				
				if(isset($seo_tool_settings->name)) {
					$warn = false;
					$dependency = $seo_tool_settings->name;
				} else {
					$dependency = __('Could not determine selected dependency.', 'scribe');
				}
			} else {
				$dependency = __('Could not determine selected dependency.', 'scribe');
			}

			return array('value' => $dependency, 'warn' => $warn);
		}

		public static function get_connect_via_ssl() {
			$response = wp_remote_get('https://api.scribeseo.com', array('sslverify' => false));
			
			$warn = is_wp_error($response);
			if(is_wp_error($response)) {
				$connect = __('Could not connect via SSL to api.scribeseo.com.', 'scribe');
			} else {
				$connect = __('Successfully connected via SSL to api.scribeseo.com', 'scribe');
			}
			
			return array('value' => $connect, 'warn' => $warn);
		}

		public static function get_connect_via_non_ssl() {
			$response = wp_remote_get('http://api.scribeseo.com');
			
			$warn = is_wp_error($response);
			if(is_wp_error($response)) {
				$connect = __('Could not connect via non-SSL to api.scribeseo.com.', 'scribe');
			} else {
				$connect = __('Successfully connected via non-SSL to api.scribeseo.com', 'scribe');
			}
			
			return array('value' => $connect, 'warn' => $warn);
		}

		public static function get_web_server() {
			$server = $_SERVER['SERVER_SOFTWARE'];
			if(empty($server)) {
				$server = __('Could not determine server software.', 'scribe');
			}
			
			return array('value' => $server, 'warn' => false);
		}

		public static function get_ip_address() {
			$ip_address = $_SERVER['SERVER_ADDR'];
			if(empty($ip_address)) {
				$ip_address = __('Could not determine server IP.', 'scribe');
			}
			
			return array('value' => $ip_address, 'warn' => false);
		}

		public static function is_scribe_installed() {
			$installed = class_exists('Scribe_SEO');
			
			return array('value' => $installed ? __('Yes', 'scribe') : __('No', 'scribe'), 'warn' => !$installed);
		}

		public static function get_scribe_version() {
			if(class_exists('Scribe_SEO')) {
				$version = Scribe_SEO::VERSION;
			} else {
				$version = __('Could not determine the Scribe SEO version.', 'scribe');
			}

			return array('value' => $version, 'warn' => false);
		}
		
		public static function get_scribe_api_key() {
			if(class_exists('Scribe_SEO')) {
				$settings = Scribe_SEO::get_settings();
				$api_key = $settings['api-key'];
			} else {
				$api_key = __('Could not determine the Scribe API Key', 'scribe');
			}

			return array('value' => $api_key, 'warn' => false);
		}

		public static function get_installed_themes() {
			$themes = wp_get_themes();
			$output = '';
			foreach($themes as $item) {
				$output .= "{$item['Name']}\n";
			}

			return array('value' => trim($output), 'warn' => false);
		}
		
		public static function get_activated_theme() {
			$theme = wp_get_theme();
			
			return array('value' => $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ), 'warn' => false);
		}

		public static function get_installed_plugins() {
			$installed = get_plugins();
			$output = '';
			foreach($installed as $item) {
				$output .= "{$item['Name']}\n";
			}
			
			return array('value' => trim($output), 'warn' => false);
		}

		public static function get_activated_plugins() {
			$active = get_option('active_plugins');
			
			$output = '';
			foreach($active as $item) {
				$path = path_join(WP_PLUGIN_DIR, $item);
				$data = get_plugin_data($path);
				$output .= "{$data['Name']}\n";
			}

			return array('value' => trim($output), 'warn' => false);
		}
	}
}
