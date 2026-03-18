<?php

declare(strict_types=1);

namespace TPots\EventBooking\Controllers;

use WP_Post;

final class MetaBoxController {
	public const NONCE_ACTION = 'tpots_ebm_pro_save_event_meta';
	public const NONCE_NAME = 'tpots_ebm_pro_nonce';

	public const META_EVENT_DATE = '_event_date';
	public const META_EVENT_TIME = '_event_time';
	public const META_LOCATION = '_event_location';
	public const META_AVAILABLE_SEATS = '_available_seats';
	public const META_BOOKING_STATUS = '_booking_status';

	/** @var array<int, string> */
	private array $allowedStatuses = ['open', 'closed', 'cancelled'];

	public function registerMetaBox(): void {
		add_meta_box(
			'tpots_ebm_pro_event_meta',
			__('Event Details', 'event-booking-manager-pro'),
			[$this, 'renderMetaBox'],
			CptController::POST_TYPE,
			'normal',
			'default'
		);
	}

	public function renderMetaBox(WP_Post $post): void {
		wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

		$date = (string) get_post_meta($post->ID, self::META_EVENT_DATE, true);
		$time = (string) get_post_meta($post->ID, self::META_EVENT_TIME, true);
		$location = (string) get_post_meta($post->ID, self::META_LOCATION, true);
		$seats = (string) get_post_meta($post->ID, self::META_AVAILABLE_SEATS, true);
		$status = (string) get_post_meta($post->ID, self::META_BOOKING_STATUS, true);

		if ($status === '') {
			$status = 'open';
		}

		?>
		<table class="form-table" role="presentation">
			<tbody>
			<tr>
				<th scope="row"><label for="tpots_event_date"><?php esc_html_e('Event Date', 'event-booking-manager-pro'); ?></label></th>
				<td>
					<input type="date" id="tpots_event_date" name="tpots_event_date" value="<?php echo esc_attr($date); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e('Format: YYYY-MM-DD', 'event-booking-manager-pro'); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="tpots_event_time"><?php esc_html_e('Event Time', 'event-booking-manager-pro'); ?></label></th>
				<td>
					<input type="time" id="tpots_event_time" name="tpots_event_time" value="<?php echo esc_attr($time); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e('24h format: HH:MM', 'event-booking-manager-pro'); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="tpots_event_location"><?php esc_html_e('Location', 'event-booking-manager-pro'); ?></label></th>
				<td>
					<textarea id="tpots_event_location" name="tpots_event_location" rows="4" class="large-text"><?php echo esc_textarea($location); ?></textarea>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="tpots_available_seats"><?php esc_html_e('Available Seats', 'event-booking-manager-pro'); ?></label></th>
				<td>
					<input type="number" min="0" id="tpots_available_seats" name="tpots_available_seats" value="<?php echo esc_attr($seats); ?>" class="small-text" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="tpots_booking_status"><?php esc_html_e('Booking Status', 'event-booking-manager-pro'); ?></label></th>
				<td>
					<select id="tpots_booking_status" name="tpots_booking_status">
						<?php foreach ($this->allowedStatuses as $option): ?>
							<option value="<?php echo esc_attr($option); ?>" <?php selected($status, $option); ?>>
								<?php echo esc_html(ucfirst($option)); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * @param int     $postId
	 * @param WP_Post $post
	 */
	public function saveMetaBox(int $postId, WP_Post $post): void {
		if (!isset($_POST[self::NONCE_NAME]) || !wp_verify_nonce((string) $_POST[self::NONCE_NAME], self::NONCE_ACTION)) {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		if (wp_is_post_revision($postId) || wp_is_post_autosave($postId)) {
			return;
		}

		if ($post->post_type !== CptController::POST_TYPE) {
			return;
		}

		if (!current_user_can('edit_post', $postId)) {
			return;
		}

		$date = isset($_POST['tpots_event_date']) ? sanitize_text_field((string) $_POST['tpots_event_date']) : '';
		$time = isset($_POST['tpots_event_time']) ? sanitize_text_field((string) $_POST['tpots_event_time']) : '';
		$location = isset($_POST['tpots_event_location']) ? sanitize_textarea_field((string) $_POST['tpots_event_location']) : '';
		$seats = isset($_POST['tpots_available_seats']) ? absint($_POST['tpots_available_seats']) : 0;
		$status = isset($_POST['tpots_booking_status']) ? sanitize_text_field((string) $_POST['tpots_booking_status']) : 'open';

		if ($date !== '' && !$this->isValidDate($date)) {
			$date = '';
		}

		if ($time !== '' && !$this->isValidTime($time)) {
			$time = '';
		}

		if (!in_array($status, $this->allowedStatuses, true)) {
			$status = 'open';
		}

		update_post_meta($postId, self::META_EVENT_DATE, $date);
		update_post_meta($postId, self::META_EVENT_TIME, $time);
		update_post_meta($postId, self::META_LOCATION, $location);
		update_post_meta($postId, self::META_AVAILABLE_SEATS, max(0, $seats));
		update_post_meta($postId, self::META_BOOKING_STATUS, $status);
	}

	private function isValidDate(string $date): bool {
		$dt = \DateTime::createFromFormat('Y-m-d', $date);
		return $dt instanceof \DateTime && $dt->format('Y-m-d') === $date;
	}

	private function isValidTime(string $time): bool {
		return (bool) preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time);
	}
}

