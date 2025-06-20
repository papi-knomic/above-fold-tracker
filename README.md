# Above Fold Tracker

Above Fold Tracker is a WordPress plugin that tracks which hyperlinks users see **above the fold** when visiting your homepage. It helps website owners understand user visibility patterns to optimize page layouts.

---

## Features

- ✅ Track visible links above the fold per visit.
- ✅ Group links per page session using cookies.
- ✅ Rate limiting to prevent spam (configurable).
- ✅ Data cleanup via WordPress cron (configurable).
- ✅ Admin dashboards:
    - Top viewed links report.
    - Paginated visits table with session details and links (via Thickbox popup).
- ✅ Settings page to control:
    - Tracking on homepage only or on all pages.
    - Rate limit interval.
    - Data retention period.
- ✅ Fully documented, PHPCS compliant (with documented exceptions).

---

## Installation

1. Upload the plugin to your WordPress `wp-content/plugins` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Navigate to **Above Fold Tracker** in the WordPress admin menu to view reports, visits, and settings.

---

## Usage

- By default, tracking only occurs on the homepage.
- Above-the-fold hyperlinks are detected and tracked when the page loads.
- Each unique visit is grouped using a persistent `visit_id` cookie.
- Admins can:
    - View the most popular links.
    - Inspect individual visit sessions.
    - Configure tracking behavior and cleanup schedule in settings.

---

## Settings

- **Track on All Pages:**  
  Enable tracking on every page (default: homepage only).

- **Rate Limit:**  
  Minimum interval (in seconds) between tracking submissions (default: 10 seconds).

- **Data Retention:**  
  Number of days to keep tracking data (default: 7 days).

---

## Screenshots

- 📊 **Reports:** Top 10 most viewed links.
- 📋 **Visits:** Detailed session logs with popups showing tracked links.
- ⚙️ **Settings:** Customize tracking behavior and cleanup schedule.

---

## Requirements

- WordPress 6.0 or higher.
- PHP 7.3 or higher.

---

## Development

This plugin:
- Uses WP Media’s package template.
- Follows WordPress Coding Standards with documented exceptions for:
    - Safe use of `$_SERVER` superglobal for session hashing.
    - Direct database queries for schema management (sanitized and justified).

### Continuous Integration

PHPCS checks are automatically run via GitHub Actions on:
- Push to `develop` and `master` branches.
- New pull requests.

---

## Roadmap

- Graphical charts for trends.
- Heatmap or scroll depth tracking.
- AJAX-based reporting (optional).
- Multi-site support.

---

## License

**GPL-2.0-or-later**

---

## Author

**Samson Ogheneakporbo Moses**
