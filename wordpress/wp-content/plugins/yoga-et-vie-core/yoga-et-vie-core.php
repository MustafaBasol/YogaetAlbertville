<?php
/**
 * Plugin Name:       Yoga et Vie — fonctionnalités
 * Description:       Contenus structurés du site Yoga et Vie : fiches cours (jour, horaire, niveau, lieu…), informations d'événement, formulaire de contact et réglages. Indépendant du thème pour que les contenus survivent à un changement de thème.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      8.2
 * Author:            Yoga et Vie Albertville
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       yoga-et-vie-core
 *
 * @package YogaEtVieCore
 */

defined( 'ABSPATH' ) || exit;

define( 'YEV_CORE_VERSION', '0.1.0' );
define( 'YEV_CORE_FILE', __FILE__ );
define( 'YEV_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'YEV_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once YEV_CORE_DIR . 'includes/fields.php';
require_once YEV_CORE_DIR . 'includes/courses.php';
require_once YEV_CORE_DIR . 'includes/events.php';
require_once YEV_CORE_DIR . 'includes/block-bindings.php';
require_once YEV_CORE_DIR . 'includes/settings.php';
require_once YEV_CORE_DIR . 'includes/contact-form.php';
require_once YEV_CORE_DIR . 'includes/demo-notice.php';

/**
 * Flushes rewrite rules so /cours/… URLs work right after activation.
 */
function yev_core_activate(): void {
	yev_register_course_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'yev_core_activate' );

/**
 * Cleans rewrite rules on deactivation.
 */
function yev_core_deactivate(): void {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'yev_core_deactivate' );
