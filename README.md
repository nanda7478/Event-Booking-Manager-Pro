# Event Booking Manager Pro

Production-grade WordPress plugin using modern PHP architecture (OOP, namespaces, PSR-4).

## Install

1. Put this folder into `wp-content/plugins/event-booking-manager-pro/`
2. Run Composer in the plugin folder:

```bash
composer install
```

3. Activate **Event Booking Manager Pro** in WordPress.

## Features

- Custom Post Type: `event` (public, rewrite slug `events`, supports title/editor/thumbnail)
- Event meta fields meta box:
  - `_event_date` (Y-m-d)
  - `_event_time` (HH:MM)
  - `_event_location` (textarea)
  - `_available_seats` (integer, min 0)
  - `_booking_status` (`open|closed|cancelled`)
- REST API:
  - `GET /wp-json/tpots/v1/events`
  - Optional: `?date=YYYY-MM-DD`
  - Optional: `?limit=N` (max 100)

