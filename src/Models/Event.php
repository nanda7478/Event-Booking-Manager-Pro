<?php

declare(strict_types=1);

namespace TPots\EventBooking\Models;

use TPots\EventBooking\Controllers\MetaBoxController;

final class Event {
	public int $id;
	public string $title;
	public string $date;
	public string $time;
	public string $location;
	public int $availableSeats;
	public string $bookingStatus;
	public string $permalink;

	public function __construct(int $postId) {
		$this->id = $postId;
		$this->title = get_the_title($postId) ?: '';
		$this->date = (string) get_post_meta($postId, MetaBoxController::META_EVENT_DATE, true);
		$this->time = (string) get_post_meta($postId, MetaBoxController::META_EVENT_TIME, true);
		$this->location = (string) get_post_meta($postId, MetaBoxController::META_LOCATION, true);
		$this->availableSeats = absint(get_post_meta($postId, MetaBoxController::META_AVAILABLE_SEATS, true));
		$this->bookingStatus = (string) get_post_meta($postId, MetaBoxController::META_BOOKING_STATUS, true);
		$this->permalink = get_permalink($postId) ?: '';
	}

	/** @return array<string, mixed> */
	public function toArray(): array {
		return [
			'ID' => $this->id,
			'title' => $this->title,
			'date' => $this->date,
			'time' => $this->time,
			'location' => $this->location,
			'available_seats' => $this->availableSeats,
			'booking_status' => $this->bookingStatus,
			'permalink' => $this->permalink,
		];
	}
}

