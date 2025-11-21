# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Visit Stay Explore is a WordPress-based business directory platform with WooCommerce subscription functionality. The site allows businesses to subscribe to plans and manage their listings across multiple regions in Queensland, Australia.

## Architecture

### Core Components

**WordPress Core**: Standard WordPress installation in root directory

**Custom Plugins**:
- `wp-content/plugins/vse-custom-plugin/` - Main customization plugin handling business logic, subscriptions, and listings management
- `wp-content/plugins/vse-listings/` - Renders listing displays via shortcodes and templates

**Theme**:
- `wp-content/themes/hello-theme-child-master/` - Child theme of Hello Elementor with custom templates for listings display

**Third-party Plugins**:
- WooCommerce + WooCommerce Subscriptions - Handles subscription payments and management
- Advanced Custom Fields (ACF) / Advanced Forms - Custom field management for listings
- Elementor - Page builder

### Custom Post Types and Taxonomies

**Post Type**: `listings` - Business listings created when subscriptions are purchased

**Taxonomies**:
- `region` - Hierarchical taxonomy with parent regions (e.g., "Toowoomba", "Lockyer") and child areas
- `services` - Business service categories

### Data Flow

1. User purchases subscription → WooCommerce order created
2. On order processing (`woocommerce_order_status_processing` hook at vse-custom-plugin.php:812) → Draft `listings` post created with `subscription_id` meta
3. User edits listing via frontend form (Advanced Forms) → Updates listing data and region/service taxonomies
4. Form submission (`af/form/submission` hook at vse-custom-plugin.php:521) → Validates town limits, updates taxonomy terms, publishes post
5. Listings display via custom templates and shortcodes based on region/service filters

### Key Files

**Subscription & Listing Management**:
- `wp-content/plugins/vse-custom-plugin/vse-custom-plugin.php` - Main plugin file (1730 lines) containing:
  - Subscription-to-listing creation logic
  - Form submission handling and validation
  - Town limit enforcement based on subscription tier
  - ACF taxonomy filtering based on subscription limits
  - Custom permalink structure for listings
  - Star rating system integration

**Frontend Templates**:
- `wp-content/themes/hello-theme-child-master/single-listings.php` - Individual listing display
- `wp-content/themes/hello-theme-child-master/taxonomy-region.php` - Region archive pages
- `wp-content/themes/hello-theme-child-master/inc.service-listings.php` - Service listings template logic
- `wp-content/themes/hello-theme-child-master/inc.local.php` - Local area filtering logic
- `wp-content/themes/hello-theme-child-master/woocommerce/myaccount/my-listings.php` - User's listings dashboard

**Shortcode Templates** (in `wp-content/plugins/vse-listings/template/`):
- `listings.php` - Property listings display
- `home-page-region.php` - Homepage region selector
- `home-page-filter.php` - Homepage search filter
- `dashboard-subscriptions.php` - User subscription management

### Region/Area Selection System

The platform uses a complex region hierarchy:
- **Parent Regions**: Toowoomba (24), Lockyer (26), South Burnett (351), Somerset (355), Western Downs (365), Southern Downs (387), Goondiwindi (390)
- **Child Areas**: Specific towns/localities under each parent region

**Town Limit System**:
- Each subscription product has a `town_limit` meta field defining max areas
- Frontend form (page ID 1316) dynamically shows/hides area selection fields based on selected parent regions
- JavaScript (inline in vse-custom-plugin.php:159-277) manages Select2 multi-selects with scroll position retention
- Form submission validates total selected areas against subscription tier limit (vse-custom-plugin.php:537-550)
- ACF query filters (vse-custom-plugin.php:1047-1267) prevent selection beyond limits

### Custom Rewrite Rules

**SEO-friendly listing permalinks** (vse-custom-plugin.php:1650-1700):
- Format: `/listings/{parent-region}/{child-area}/{listing-name}/` or `/listings/{region}/{listing-name}/`
- Generated based on `listing_information_group_your_local_area` meta field

**Service listings pages** (vse-custom-plugin.php:1702-1726):
- Format: `/service-listings/{region-slug}/{service-slug}/`
- Query vars: `region_slug`, `service_slug`, `type`

### Staging Environment

The codebase contains staging-specific URLs (`visitstayexplore.staging-sites.com.au`) hardcoded in multiple locations. When deploying to production, search and replace these URLs.

## Common Development Tasks

### Testing Business Listing Flow

1. Purchase a subscription product via WooCommerce checkout
2. Check that draft listing is auto-created in wp-admin → Listings
3. Navigate to "My Account" → "Listings" to edit the business listing
4. Verify region/service selection and town limit validation
5. Submit form and confirm listing publishes with correct taxonomies

### Working with Region/Service Taxonomies

- ACF fields use taxonomy selectors filtered by `acf/fields/taxonomy/query` hook
- Update region term IDs in vse-custom-plugin.php if taxonomy structure changes
- Region featured images stored as term meta (`image` key)
- Banner images/links for regions stored as term meta (`banner_image`, `banner_link`)

### Adding New Regions

1. Create parent region term in Regions taxonomy
2. Add child area terms under the parent
3. Update JavaScript region show/hide logic (vse-custom-plugin.php:176-256)
4. Add corresponding ACF field group for area selection
5. Update form submission handler (vse-custom-plugin.php:600-638)
6. Update save_post handler (vse-custom-plugin.php:1530-1583)

### Modifying Subscription Products

Subscription products require custom meta field `town_limit` (integer) defining max selectable areas. This is queried throughout the codebase via `get_post_meta($product->get_id(), 'town_limit', true)`.

### Debugging

Use `dd()` function defined in vse-custom-plugin.php:12-22 for quick debugging (simple print_r wrapper).

Commented out error reporting lines exist in the code - uncomment if needed:
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Important Notes

- Direct database queries exist using `$wpdb` - primarily for fetching subscription/order data
- Multiple hooks modify WooCommerce checkout and subscription behavior
- Star rating system integrates with "Rate My Post" plugin via custom AJAX handlers
- Comment system has custom flagging functionality for inappropriate content
- Flush rewrite rules automatically happens on staging (vse-custom-plugin.php:1678-1682)
- The vse subdirectory contains a separate git repository (appears minimal/unused)

## Page IDs (Hardcoded)

- 1316 - Edit Business page
- 1104 - Add Business page
- 917 - Listings archive
- 14 - My Account
- 13 - Checkout
- 1475 - Elementor template used in service listings
