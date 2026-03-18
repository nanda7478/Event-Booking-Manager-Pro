<?php

declare(strict_types=1);

namespace TPots\EventBooking\Controllers;

final class CptController {
	public const POST_TYPE = 'event';

	public function registerCpt(): void {
		$labels = [
			'name' => __('Events', 'event-booking-manager-pro'),
			'singular_name' => __('Event', 'event-booking-manager-pro'),
			'menu_name' => __('Events', 'event-booking-manager-pro'),
			'add_new' => __('Add New', 'event-booking-manager-pro'),
			'add_new_item' => __('Add New Event', 'event-booking-manager-pro'),
			'edit_item' => __('Edit Event', 'event-booking-manager-pro'),
			'new_item' => __('New Event', 'event-booking-manager-pro'),
			'view_item' => __('View Event', 'event-booking-manager-pro'),
			'search_items' => __('Search Events', 'event-booking-manager-pro'),
			'not_found' => __('No events found.', 'event-booking-manager-pro'),
			'not_found_in_trash' => __('No events found in Trash.', 'event-booking-manager-pro'),
		];

		$args = [
			'labels' => $labels,
			'public' => true,
			'has_archive' => true,
			'show_in_rest' => true,
			'rewrite' => ['slug' => 'events'],
			'menu_icon' => 'dashicons-calendar-alt',
			'supports' => ['title', 'editor', 'thumbnail'],
		];

		register_post_type(self::POST_TYPE, $args);
	}
}

