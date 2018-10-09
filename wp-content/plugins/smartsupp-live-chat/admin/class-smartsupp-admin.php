<?php

use Smartsupp\Auth\Api;


/**
 * Smartsupp Live Chat.
 *
 * @package   Smartsupp_Admin
 * @author    Smartsupp <vladimir@smartsupp.com>
 * @license   GPL-2.0+
 * @link      http://www.smartsupp.com
 * @copyright 2014 smartsupp.com
 */
class Smartsupp_Admin
{

	const OPTION_NAME = 'smartsupp';

	/**
	 * @var $this
	 */
	protected static $instance = NULL;


	private function __construct()
	{
		$plugin = Smartsupp::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		add_action('admin_menu', array($this, 'addMenuItems'));
		add_action('admin_init', array($this, 'performAction'));

		$plugin_basename = plugin_basename(plugin_dir_path(realpath(dirname(__FILE__))) . $this->plugin_slug . '.php');
		add_filter('plugin_action_links_' . $plugin_basename, function ($links) use ($plugin) {
			array_unshift($links, '<a href="options-general.php?page=' . ($plugin->get_plugin_slug()) . '">Settings</a>');
			return $links;
		});
	}

	public static function get_instance()
	{
		if (NULL == self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}


	public static function install()
	{
		global $wp_version;

		$checks = array(
			'Your Wordpress version is not compatible with Smartsupp plugin which requires at least version 3.1. Please update your Wordpress or insert Smartsupp chat code into your website manually (you will find the chat code in the email we have sent you upon registration)' => version_compare($wp_version, '3.1', '<'),
			'This plugin requires at least PHP version 5.3.0, your version: ' . PHP_VERSION . '. Please ask your hosting company to bring your PHP version up to date.' => version_compare(PHP_VERSION, '5.3.0', '<'),
			'This plugin requires PHP extension \'curl\' installed and enbaled.' => !function_exists('curl_init'),
			'This plugin requires PHP extension \'json\' installed and enbaled.' => !function_exists('json_decode'),
		);

		foreach ($checks as $message => $disable) {
			if ($disable) {
				deactivate_plugins(basename(__FILE__));
				wp_die($message);
			}
		}

		update_option(self::OPTION_NAME, array(
			'active' => FALSE,
			'chat-id' => NULL,
			'email' => NULL,
			'optional-code' => NULL,
		));
	}


	public function addMenuItems()
	{
		add_options_page(
			__('Smartsupp Live Chat - Settings', $this->plugin_slug),
			__('Smartsupp Live Chat', $this->plugin_slug),
			'manage_options',
			$this->plugin_slug,
			array($this, 'renderAdminPage')
		);

		add_menu_page(
			__('Smartsupp Live Chat - Settings', $this->plugin_slug),
			__('Smartsupp Chat', $this->plugin_slug),
			'manage_options',
			$this->plugin_slug,
			array($this, 'renderAdminPage'),
			plugins_url('images/icon-20x20.png', dirname(__FILE__))
		);

	}


	public function performAction()
	{
		if (!isset($_GET['ssaction'])) {
			return;
		}

		$formAction = $message = $email = NULL;
		$action = (string) $_GET['ssaction'];
		switch ($action) {
			case 'login':
			case 'register':
				$formAction = $action;
				$api = new Api;
				$data = array(
					'email' => $_POST['email'],
					'password' => $_POST['password'],
				);
				try {
					$response = $formAction === 'login' ? $api->login($data) : $api->create($data + array('partnerKey' => 'k717wrqdi5', 'lang' => $this->convertLocale(get_locale())));

					if (isset($response['error'])) {
						$message = $response['message'];
						$email = $_POST['email'];
					} else {
						$this->activate($response['account']['key'], $_POST['email']);
					}
				} catch (Exception $e) {
					$message = $e->getMessage();
					$email = $_POST['email'];
				}
				break;
			case 'update':
				$message = 'Custom code was updated.';
				$this->updateOptions(array(
					'optional-code' => strip_tags($_POST['code']),
				));
				break;
			case 'disable':
				$this->deactivate();
				break;
			default:
				$message = 'Invalid action';
				break;
		}
		$this->renderAdminPage($message, $formAction, $email);
		exit;
	}


	public function renderAdminPage($message = NULL, $formAction = NULL, $email = NULL)
	{
		$this->render('views/admin.php', array(
			'domain' => $this->plugin_slug,
			'customers' => $this->getCustomers(),
			'options' => $this->getOptions(),
			'message' => (string) $message,
			'formAction' => $formAction,
			'email' => $email,
		));
	}


	private function convertLocale($locale)
	{
		$available = array('en', 'cs', 'da', 'nl', 'fr', 'de', 'hu', 'it', 'ja', 'pl', 'br', 'pt', 'es', 'tr', 'eu', 'cn', 'tw', 'ro', 'ru', 'sk');
		$part = strtolower(substr($locale, 0, 2));
		$locale = strtolower(substr($locale, 0, 5));
		if (!in_array($part, $available)) {
			return 'en';
		} else {
			if ($part === 'pt') {
				if ($locale === 'pt_br') {
					$part = 'br';
				}
			} elseif ($part === 'zh') {
				if ($locale === 'zh_cn') {
					$part = 'cn';
				} elseif ($locale === 'zh_tw') {
					$part = 'tw';
				} else {
					$part = NULL;
				}
			} elseif ($part === 'eu') {
				$part = NULL;
			}
			return $part ?: 'en';
		}
	}


	private function render($template, $vars = array())
	{
		call_user_func_array(function () use ($template, $vars) {
			extract($vars);
			include $template;
		}, array());
	}


	private function getCustomers()
	{
		$pluginUrl = $this->getPluginUrl();
		return
		'<div class="section--header">' .
			'<h2>' . __('Trusted by more than 55 000 companies', $this->plugin_slug) . '</h2>' .
		'</div>' .
		'<div class="section--body">' .
			'<div class="customers">' .
				'<img class="partner" src="' . $pluginUrl . '/images/skoda.png" alt="ŠKODA AUTO a.s." />' .
				'<img class="partner" src="' . $pluginUrl . '/images/gekko.png" alt="GEKKO Computer" />' .
				'<img class="partner" src="' . $pluginUrl . '/images/lememo.png" alt="Lememo" />' .
				'<img class="partner" src="' . $pluginUrl . '/images/conrad.png" alt="Conrad" />' .
			'</div>' .
		'</div>';
	}


	private function activate($chatId, $email)
	{
		$this->updateOptions(array(
			'active' => TRUE,
			'chat-id' => (string) $chatId,
			'email' => (string) $email,
		));
	}


	private function deactivate()
	{
		$this->updateOptions(array(
			'active' => FALSE,
			'chat-id' => NULL,
			'email' => NULL
		));
	}


	private function updateOptions(array $options)
	{
		$current = $this->getOptions();
		foreach ($options as $key => $option) {
			$current[$key] = $option;
		}
		update_option(self::OPTION_NAME, $current);
	}


	/**
	 * @return array
	 */
	private function getOptions()
	{
		return get_option(self::OPTION_NAME);
	}


	/**
	 * @return string
	 */
	private function getPluginUrl()
	{
		return plugins_url('', __DIR__);
	}


	/**
	 * @internal This method is intended just for static analysis of translations
	 */
	private function getMessages()
	{
		return array(
			__('Email does not exist', $this->plugin_slug),
			__('Invalid password', $this->plugin_slug),
			__('Email is required', $this->plugin_slug),
			__('Email already exists', $this->plugin_slug),
			__('Password is too short. Minimal length is 6 characters.', $this->plugin_slug),
			__('Custom code was updated.', $this->plugin_slug),
			__('Invalid action', $this->plugin_slug),
		);
	}

}
