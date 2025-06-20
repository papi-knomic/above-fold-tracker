# Explanation for Above Fold Tracker

## Problem Summary

As a website owner, I want to **know which hyperlinks were visible above the fold** when visitors opened my homepage within the past 7 days.  
This insight allows me to **optimize page layouts** based on actual user visibility.

---

## Solution Overview

I built a **WordPress plugin** that:
- Injects a JavaScript tracking script on the homepage (or all pages if configured).
- Detects and sends above-the-fold hyperlinks along with screen size and the current page URL.
- Groups each user session with a persistent `visit_id` stored in cookies.
- Stores tracking data in a custom database table.
- Provides admin pages to:
    - View top visited links.
    - Inspect visit sessions with links displayed via Thickbox popups.
- Periodically cleans up old tracking data via WordPress cron, with a configurable retention period.

---

## Key Technical Decisions

### 1. **Session Grouping**
Each page session is grouped using a `visit_id` stored in a cookie to avoid mixing visits and to enable clear session reporting.

### 2. **Tracking with Rate Limiting**
A configurable rate limit (default: 10 seconds) was implemented to:
- Minimize database writes.
- Reduce the potential for spam or abuse.

### 3. **Database Design**
A custom table stores:
- URL of the tracked link.
- Screen size.
- Visit timestamp.
- Visit ID.
- Page URL.

**Indexes:**
- `visit_id`: For grouping visits efficiently.
- `visit_time`: For quick cleanup and filtering.

### 4. **Data Cleanup**
A scheduled cron job automatically deletes data older than the retention period (default: 7 days), which is configurable from the settings page.

### 5. **Admin Reporting with WP_List_Table**
- Sessions are listed in a paginated, searchable table.
- Each session links are displayed using WordPress Thickbox popups (no AJAX needed).
- Reports display the most frequently seen above-the-fold links.

### 6. **Settings Page**
- Allows enabling tracking on all pages (or restrict to homepage).
- Allows changing the rate limit interval.
- Allows setting the data retention period in days.

---

## How the Solution Meets the User Story

- ✅ Tracks above-the-fold links specifically (not scrolling-based tracking).
- ✅ Stores each session for up to 7 days.
- ✅ Provides admin pages for easy, actionable insights.
- ✅ Prioritizes scalability by grouping sessions and rate limiting submissions.

---

## PHPCS Compliance

- Follows **WordPress Coding Standards**.
- Documented exceptions:
    - Use of `$_SERVER['REMOTE_ADDR']` for hashing visit IDs (checked with `isset()` and considered safe for this use case).
    - Direct database queries for table schema and bulk inserts (acceptable in this plugin’s context).
- GitHub Actions are set up to run PHPCS automatically on pushes and pull requests to `develop` and `master`.

---

## Limitations

- Does not track link visibility changes on scroll (only on initial page load).
- Rate limiting is client-side only (could be further improved with server-side protections).

---

## Future Improvements

- Dynamic filtering and sorting in reports via AJAX.
- Graphical charts to visualize trends over time.
- Enhanced session tracking across multiple pages.
- Add server-side rate limiting for even stronger spam protection.
- Potential heatmap integration.

---

## Summary

The plugin is designed to be lightweight, scalable, and extensible for future improvements.  
The core user need—to know which links were visible above the fold in recent visits—is fully met with session grouping, rate limiting, reporting, and proper data cleanup mechanisms.
