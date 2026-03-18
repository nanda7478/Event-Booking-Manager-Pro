<?php

declare(strict_types=1);

namespace TPots\EventBooking\Core;

use TPots\EventBooking\Controllers\CptController;
use TPots\EventBooking\Controllers\MetaBoxController;
use TPots\EventBooking\Services\Rest\EventsEndpoint;

final class Plugin {
	private Loader $loader;

	public function __construct() {
		$this->loader = new Loader();

		$this->registerControllers();
		$this->registerServices();
	}

	private function registerControllers(): void {
		$cpt = new CptController();
		$this->loader->addAction('init', $cpt, 'registerCpt');

		$metaBox = new MetaBoxController();
		$this->loader->addAction('add_meta_boxes', $metaBox, 'registerMetaBox');
		$this->loader->addAction('save_post_event', $metaBox, 'saveMetaBox', 10, 2);
	}

	private function registerServices(): void {
		$rest = new EventsEndpoint();
		$this->loader->addAction('rest_api_init', $rest, 'registerRoutes');
	}

	public function run(): void {
		$this->loader->run();
	}
}

