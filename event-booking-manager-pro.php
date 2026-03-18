<?php
/**
 * Plugin Name: Event Booking Manager Pro
 * Description: Manage events with meta fields and a public REST API endpoint.
 * Version: 1.0.0
 * Author: TPots
 * Text Domain: event-booking-manager-pro
 * Requires PHP: 7.4
 *
 * @package TPotsEventBooking
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

define('TPOTS_EBM_PRO_VERSION', '1.0.0');
define('TPOTS_EBM_PRO_PATH', plugin_dir_path(__FILE__));
define('TPOTS_EBM_PRO_URL', plugin_dir_url(__FILE__));

$autoload = TPOTS_EBM_PRO_PATH . 'vendor/autoload.php';
if (file_exists($autoload)) {
	require_once $autoload;
}

register_activation_hook(__FILE__, static function (): void {
	if (!class_exists(\TPots\EventBooking\Core\Activator::class)) {
		return;
	}
	\TPots\EventBooking\Core\Activator::activate();
});

register_deactivation_hook(__FILE__, static function (): void {
	if (!class_exists(\TPots\EventBooking\Core\Deactivator::class)) {
		return;
	}
	\TPots\EventBooking\Core\Deactivator::deactivate();
});

if (class_exists(\TPots\EventBooking\Core\Plugin::class)) {
	$plugin = new \TPots\EventBooking\Core\Plugin();
	$plugin->run();
}

