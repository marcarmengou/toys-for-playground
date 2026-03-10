=== Toys for Playground ===
Contributors: marc4
Tags: playground
Requires at least: 6.3
Tested up to: 6.9
Requires PHP: 7.4
Tested PHP: 8.3
Stable tag: 1.2.5
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Toys for Playground allows you to set up development, training, and testing environments in WordPress Playground easily. No Playground API knowledge needed.

== Description ==

**Toys for Playground allows you to set up development, training, and testing environments in WordPress Playground easily. No Playground API knowledge needed.**

Generate a custom WordPress setup in Playground with just a few clicks, including theme, plugins, and the WordPress and PHP versions that you need.

Can be useful for developers, trainers, and testers. Test configurations risk-free, entirely in your browser.

Available toys:

**Cloner**
Clone site, theme, and plugins to Playground.

**Generator**
Create a Playground from scratch, with the theme and plugins you want.

**Plugin Explorer**
Explore any plugin directly from WordPress repository in Playground.

**Theme Explorer**
Explore any theme directly from WordPress repository in Playground.

**Sharer**
Generate Playground link of your current page for debugging or sharing.

== Installation ==

1. Go to **Plugins > Add New Plugin**.
2. Search for **Toys for Playground**.
3. Install and activate the **Toys for Playground** plugin.

== Changelog ==

= [1.2.5] - 2026-10-03 =

* Security: Escaped Settings link with esc_html__() in plugin action links.
* Fix: Added isset() guards for $_POST['themes'], $_POST['wp_version'], $_POST['php_version'] in cloner.php to prevent PHP warnings.
* Fix: Added isset() guard for $_POST['theme'] in generator.php to prevent PHP warnings.
* Code: Removed unused $plugin_basename variable from cloner.php.
* Code: cloner.php now starts with <?php and closes the wrap div in HTML.
* Performance: get_option('enable_sharer') stored in variable to avoid repeated calls.

= [1.2.4] - 2026-10-03 =

* Fix: Corrected uninstall.php — removed incorrect DROP TABLE block.
* Code: Unified function prefix to toys_for_playground_ across all files.
* Code: Replaced echo esc_html__() with esc_html_e() in generator.php for consistency.
* Compatibility: Tested up to WordPress 6.9.

= [1.2.3] - 2026-10-03 =

* Updated WP version selector: 6.3 to 6.9 (removed older versions below 6.3).
* Updated PHP version selector: 7.4 to 8.5 (removed older versions below 7.4).
* Added WP 6.9 in WP version selector.
* Added PHP 8.5 in PHP version selector.
* Removed changelog.txt file.
* Improvement: wp_enqueue_script() now uses plugin version constant for cache busting.

= [1.2.2] - 2025-10-15 =

* Added WP 6.8 in WP version selector.
* Added PHP 8.4 in PHP version selector.
* Fix: Added text domain to 'Settings' link for i18n compatibility.
* Fix: Updated uninstall script.

= [1.2.1] - 2024-28-10 =

* Added new WordPress versions in Cloner and Generator.
* Compatibility: WordPress 6.0 - WordPress 6.7

= [1.2.0] - 2024-13-01 =

* Added latest and beta params in WP version selector.
* Compatibility: WordPress 6.0 - WordPress 6.4
* Compatibility: PHP 7.0 - PHP 8.3

= [1.1.9] - 2023-10-10 =

* Added Copy button in Sharer toy modal.
* Update new Storage params

= [1.1.8] - 2023-15-09 =

* Added Sharer toy
* Added nightly param
* Added uninstall.php
* Minor corrections

= [1.1.6] - 2023-08-09 =

* Hidden submenus
* Some usability fixes

= [1.1.5] - 2023-08-09 =

* Fixes persistent storage
* Fixes i18n
* Separate PHP files for each tool.
* Corrected and summarized text.
* Added plugin information and images
* More visible directory buttons

= [1.1.1] - 2023-03-09 =

* i18n ready
* Added WP 6.3 WP selector in toys
* Added Plugin Explorer
* Added Theme Explorer
* Rewritted the Generator toy

= [1.0.8] - 2023-02-09 =

* Compatibility: WordPress 5.9 - WordPress 6.3
* Compatibility: PHP 5.9 - PHP 8.2
* Added Storage option
* Added inactive plugins and themes
* Added WordPress & PHP version selectors
* Added some help texts
* Some corrections

= [1.0] - 2023-16-06 =

* First version.