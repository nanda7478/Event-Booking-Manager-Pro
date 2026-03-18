<?php

declare(strict_types=1);

namespace TPots\EventBooking\Core;

use TPots\EventBooking\Controllers\CptController;

final class Activator {
	public static function activate(): void {
		// Ensure CPT/rewrite rules exist before flushing.
		$cpt = new CptController();
		$cpt->registerCpt();

		flush_rewrite_rules();
	}
}

