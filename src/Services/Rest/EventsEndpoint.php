<?php

declare(strict_types=1);

namespace TPots\EventBooking\Services\Rest;

use TPots\EventBooking\Controllers\CptController;
use TPots\EventBooking\Controllers\MetaBoxController;
use TPots\EventBooking\Models\Event;
use WP_REST_Request;
use WP_REST_Response;

final class EventsEndpoint {
	private const NAMESPACE = 'tpots/v1';
	private const ROUTE = '/events';

	public function registerRoutes(): void {
		register_rest_route(
			self::NAMESPACE,
			self::ROUTE,
			[
				'methods' => \WP_REST_Server::READABLE,
				'callback' => [$this, 'handleGet'],
				'permission_callback' => '__return_true',
				'args' => [
					'date' => [
						'description' => 'Filter by exact event date (YYYY-MM-DD).',
						'type' => 'string',
						'required' => false,
					],
					'limit' => [
						'description' => 'Maximum number of events to return.',
						'type' => 'integer',
						'required' => false,
						'default' => 10,
					],
				],
			]
		);
	}

	public function handleGet(WP_REST_Request $request): WP_REST_Response {
		$today = (new \DateTime('today', wp_timezone()))->format('Y-m-d');

		$dateFilter = (string) $request->get_param('date');
		$dateFilter = $dateFilter !== '' ? sanitize_text_field($dateFilter) : '';
		if ($dateFilter !== '' && !$this->isValidDate($dateFilter)) {
			$dateFilter = '';
		}

		$limit = (int) $request->get_param('limit');
		if ($limit <= 0) {
			$limit = 10;
		}
		$limit = min(100, $limit);

		$metaQuery = [
			[
				'key' => MetaBoxController::META_EVENT_DATE,
				'value' => $today,
				'compare' => '>=',
				'type' => 'DATE',
			],
		];

		if ($dateFilter !== '') {
			if ($dateFilter < $today) {
				return new WP_REST_Response([], 200);
			}

			$metaQuery = [
				[
					'key' => MetaBoxController::META_EVENT_DATE,
					'value' => $dateFilter,
					'compare' => '=',
					'type' => 'DATE',
				],
			];
		}

		$query = new \WP_Query([
			'post_type' => CptController::POST_TYPE,
			'post_status' => 'publish',
			'posts_per_page' => $limit,
			'orderby' => 'meta_value',
			'meta_key' => MetaBoxController::META_EVENT_DATE,
			'order' => 'ASC',
			'meta_query' => $metaQuery,
			'no_found_rows' => true,
			'ignore_sticky_posts' => true,
		]);

		$events = [];
		foreach ($query->posts as $post) {
			$events[] = (new Event((int) $post->ID))->toArray();
		}

		return new WP_REST_Response($events, 200);
	}

	private function isValidDate(string $date): bool {
		$dt = \DateTime::createFromFormat('Y-m-d', $date);
		return $dt instanceof \DateTime && $dt->format('Y-m-d') === $date;
	}
}

