<?php
/**
 * Plugin Name:       APB Sun & Moon Scroll Sky
 * AI Plugin Built: true
 * Plugin URI:        https://example.com/plugins/apb-sun-moon-scroll-sky
 * Description:       Adds a scroll-driven day-to-night sky animation with a sun, moon, gradient backgrounds, and dark mode text transition using CSS scroll-driven animations.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            APB
 * Author URI:        https://example.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       apb-sun-moon-scroll-sky
 * Domain Path:       /languages
 *
 * @package APB\SunMoonScrollSky
 */

/*
 * =============================================================================
 * SYSTEM INSTRUCTIONS (for reference / provenance)
 * =============================================================================
 *
 * You are an expert autonomous WordPress developer. You have been given a plan
 * to build a plugin. Your goal is to write all files necessary according to the
 * plan, following WordPress coding standards and best practices throughout.
 *
 * SETUP:
 *   - Discover abilities; verify slug availability via ai/get-installed-plugins.
 *
 * FILE WRITING:
 *   - Use write_file for every file.
 *   - Main plugin file must include the full system instructions and the
 *     provided plan inside a multi-line PHP comment.
 *
 * PLUGIN HEADER:
 *   - Complete header block: Plugin Name, Plugin URI, Description, Version,
 *     Requires at least, Requires PHP, Author, Author URI, License, License URI,
 *     Text Domain, Domain Path; Requires Plugins if applicable.
 *
 * NAMESPACING & PREFIXING:
 *   - Namespace derived from slug (APB vendor). Here: APB\SunMoonScrollSky.
 *   - All globals prefixed with apb_sun_moon_scroll_sky_ / APB_SUN_MOON_SCROLL_SKY_.
 *   - CSS classes prefixed: .apb-sms-*.
 *
 * SECURITY:
 *   - ABSPATH guard at top of every PHP file.
 *   - Nonces for forms/AJAX; capability checks; escape output; sanitize input.
 *   - $wpdb->prepare() for custom queries.
 *
 * WORDPRESS FUNCTIONS & APIs:
 *   - Prefer native WP APIs (Settings, Options, Transients, HTTP).
 *   - wp_enqueue_style/script with versioned handles and correct deps.
 *
 * HOOKS & ARCHITECTURE:
 *   - Bootstrap through hooks; never execute at include time.
 *   - Use activation/deactivation/uninstall hooks where applicable.
 *   - Expose own actions/filters for extensibility; document with PHPDoc.
 *
 * PERFORMANCE:
 *   - Enqueue only where needed; transient caching; efficient queries.
 *
 * INTERNATIONALISATION:
 *   - Wrap user-facing strings in i18n functions; Domain Path: /languages.
 *
 * CODE QUALITY:
 *   - PHPDoc on classes/methods/hooks; WP PHP Coding Standards; Yoda conditions.
 *
 * FINISHING:
 *   - Call finish() alone when done.
 *
 * =============================================================================
 * PLUGIN PLAN
 * =============================================================================
 *
 * {
 *   "plugin_name": "APB Sun & Moon Scroll Sky",
 *   "plugin_slug": "apb-sun-moon-scroll-sky",
 *   "description": "Adds a scroll-driven day-to-night sky animation with a sun,
 *                   moon, gradient backgrounds, and dark mode text transition
 *                   using CSS scroll-driven animations.",
 *   "complexity": "simple",
 *   "is_modification": false,
 *   "files": [
 *     {
 *       "path": "apb-sun-moon-scroll-sky.php",
 *       "type": "php",
 *       "description": "Main plugin file with plugin header. Registers a
 *         function apb_sun_moon_scroll_sky_enqueue_assets() hooked to
 *         wp_enqueue_scripts that enqueues the CSS file. Also hooks
 *         apb_sun_moon_scroll_sky_render_sky() to wp_body_open (with a
 *         wp_footer fallback) to inject the fixed-position sky container
 *         markup containing the sun element, moon element, star field layer,
 *         and gradient layers. All markup uses aria-hidden='true' and
 *         pointer-events:none so it sits purely as a decorative background.
 *         Uses a @supports check in CSS so the plugin is a no-op when
 *         scroll-driven animations aren't supported.",
 *       "is_main": true
 *     },
 *     {
 *       "path": "assets/sky.css",
 *       "type": "css",
 *       "description": "Contains all visual and animation logic. Wraps
 *         everything in @supports (animation-timeline: scroll()) so
 *         unsupported browsers get nothing. Defines the sky container,
 *         stacked gradient layers, star field, sun and moon animations, and
 *         scroll-timeline-driven text color transitions."
 *     }
 *   ],
 *   "hooks_used": ["wp_enqueue_scripts", "wp_body_open", "wp_footer"],
 *   "wp_apis_used": ["wp_enqueue_style", "plugins_url", "plugin_dir_path"],
 *   "security_notes": [
 *     "No user input accepted; no nonce or sanitization needed.",
 *     "Injected HTML is fully static and hardcoded.",
 *     "aria-hidden and pointer-events:none keep layer non-interactive.",
 *     "Assets enqueued with versioned handle to respect caching."
 *   ],
 *   "architecture": "Minimal two-file plugin. PHP registers a stylesheet on
 *     the frontend and injects a small, static decorative DOM structure into
 *     the page early via wp_body_open. CSS does all the work with
 *     scroll-driven animations, gated behind @supports."
 * }
 */

namespace APB\SunMoonScrollSky;

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version.
 *
 * @since 1.0.0
 */
const APB_SUN_MOON_SCROLL_SKY_VERSION = '1.0.0';

/**
 * Plugin file path.
 *
 * @since 1.0.0
 */
define( __NAMESPACE__ . '\APB_SUN_MOON_SCROLL_SKY_FILE', __FILE__ );

/**
 * Enqueues the frontend stylesheet that drives the scroll-based sky animation.
 *
 * Only enqueues on the frontend (not in the admin or feeds) to avoid
 * needlessly loading the stylesheet where it would have no effect.
 *
 * @since 1.0.0
 *
 * @return void
 */
function apb_sun_moon_scroll_sky_enqueue_assets() {
	// Only load on the frontend.
	if ( is_admin() || is_feed() ) {
		return;
	}

	/**
	 * Filters whether the APB Sun & Moon Scroll Sky assets should load on the current request.
	 *
	 * Return false to prevent the sky from rendering on a given page.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $should_load Whether to load the sky assets. Default true.
	 */
	$should_load = apply_filters( 'apb_sun_moon_scroll_sky_should_load', true );

	if ( ! $should_load ) {
		return;
	}

	wp_enqueue_style(
		'apb-sun-moon-scroll-sky',
		plugins_url( 'assets/sky.css', APB_SUN_MOON_SCROLL_SKY_FILE ),
		array(),
		APB_SUN_MOON_SCROLL_SKY_VERSION,
		'all'
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\apb_sun_moon_scroll_sky_enqueue_assets' );

/**
 * Renders the decorative sky container early in the <body>.
 *
 * Emits a fixed-position, aria-hidden, pointer-events:none container holding
 * the gradient layers, star field, sun, and moon. All animation is driven by
 * CSS scroll-driven animations, gated by an @supports rule in the stylesheet
 * so that browsers without support render nothing of consequence.
 *
 * This function is idempotent for a single request: if already rendered (for
 * example via wp_body_open), the wp_footer fallback will not re-emit it.
 *
 * @since 1.0.0
 *
 * @return void
 */
function apb_sun_moon_scroll_sky_render_sky() {
	static $rendered = false;

	if ( $rendered ) {
		return;
	}

	/**
	 * Filters whether the sky markup should be rendered on the current request.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $should_render Whether to render the sky markup. Default true.
	 */
	$should_render = apply_filters( 'apb_sun_moon_scroll_sky_should_render', true );

	if ( ! $should_render ) {
		return;
	}

	$rendered = true;

	// The markup is fully static; safe to output directly.
	// Using esc_html__ for the visually-hidden sun/moon labels.
	$sun_label  = esc_html__( 'Sun', 'apb-sun-moon-scroll-sky' );
	$moon_label = esc_html__( 'Moon', 'apb-sun-moon-scroll-sky' );
	?>
	<div class="apb-sms-sky" aria-hidden="true" role="presentation">
		<div class="apb-sms-layer apb-sms-layer--sunrise"></div>
		<div class="apb-sms-layer apb-sms-layer--day"></div>
		<div class="apb-sms-layer apb-sms-layer--sunset"></div>
		<div class="apb-sms-layer apb-sms-layer--night">
			<div class="apb-sms-stars"></div>
		</div>
		<div class="apb-sms-sun" data-label="<?php echo esc_attr( $sun_label ); ?>"></div>
		<div class="apb-sms-moon" data-label="<?php echo esc_attr( $moon_label ); ?>"></div>
	</div>
	<?php

	/**
	 * Fires immediately after the sky markup is rendered.
	 *
	 * @since 1.0.0
	 */
	do_action( 'apb_sun_moon_scroll_sky_after_render' );
}
add_action( 'wp_body_open', __NAMESPACE__ . '\apb_sun_moon_scroll_sky_render_sky', 0 );
// Fallback for themes that do not call wp_body_open().
add_action( 'wp_footer', __NAMESPACE__ . '\apb_sun_moon_scroll_sky_render_sky', 0 );

/**
 * Loads the plugin text domain for translations.
 *
 * @since 1.0.0
 *
 * @return void
 */
function apb_sun_moon_scroll_sky_load_textdomain() {
	load_plugin_textdomain(
		'apb-sun-moon-scroll-sky',
		false,
		dirname( plugin_basename( APB_SUN_MOON_SCROLL_SKY_FILE ) ) . '/languages'
	);
}
add_action( 'init', __NAMESPACE__ . '\apb_sun_moon_scroll_sky_load_textdomain' );
