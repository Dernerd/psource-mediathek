<?php
/**
 * PsourceMediathek Actions API.
 *
 * @package psourcemediathek
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * PsourceMediathek core actions
 *
 * This section lists/exposes the actions that an addon plugin should use to build any addon for PsourceMediathek
 *
 * These provide actions for the plugin developers where they can hook without worrying if PsourceMediathek is active or not.
 * These actions are just a layer over WordPress core actions
 */

/**
 * Actions not declared here but existing
 *
 * [plugins_loaded] -> [psmt_loaded]
 * psmt_loaded is fired when all the required files of PsourceMediathek is loaded.
 * It is fired at plugins_loaded action.
 * Plugins should use psmt_loaded action to load the core files of their own plugin
 *
 * [init] -> [psmt_init]
 * psmt_init action is equivalent to WordPress 'init' action. Use it to register post type or do anything initialization
 * This is the first action where current user will be available and properly set
 */

/**
 * The following provides an abstraction/interface for the dependent addons
 * It is a very specific sub set of WordPress( or currently BuddyPress ) actions that we feel are important
 * for the development of PsourceMediathek addons.
 * If you need a new hook, please do let us know.
 *
 * The following actions are modeled after BuddyPress and we believe they have done it in the right way.
 *
 * In future, we will unattach the actions from BuddyPress and use WordPress core actions when we move towards non BP Galleries
 *
 * The best thing is PsourceMediathek addons won't have to worry about that if the hook to various psmt_{action_name}
 */

add_action( 'parse_query', 'psmt_parse_query', 2 );
add_action( 'wp', 'psmt_ready', 10 ); // wp action.

add_action( 'after_setup_theme', 'psmt_after_setup_theme', 10 ); // After WP themes
add_action( 'init', 'psmt_setup', 0 ); // first thing on init.
add_action( 'init', 'psmt_init', 11 ); // after buddypress, BP uses 10 priority.
add_action( 'wp_enqueue_scripts', 'psmt_enqueue_scripts', 10 ); // load front end js/css etc.
add_action( 'admin_enqueue_scripts', 'psmt_admin_enqueue_scripts', 10 ); // load admin js.
add_action( 'admin_bar_menu', 'psmt_setup_admin_bar', 10 ); // admin_bar_menu.
add_action( 'template_redirect', 'psmt_template_redirect', 9 );
add_action( 'widgets_init', 'psmt_widgets_init', 10 );


add_action( 'psmt_template_redirect', 'psmt_actions', 4 );
add_action( 'psmt_template_redirect', 'psmt_screens', 6 );
/**
 * Fires on parse_query
 */
function psmt_parse_query() {
	do_action( 'psmt_parse_query' );
}

/**
 * Fires on 'wp' action
 */
function psmt_ready() {
	do_action( 'psmt_ready' );
}

/**
 * Fires after the after_setup_theme
 */
function psmt_after_setup_theme() {
	do_action( 'psmt_after_setup_theme' );
}

/**
 * Register post types, status etc here
 */
function psmt_setup() {
	do_action( 'psmt_setup' );
}

/**
 * All Initialization code shoud hook to this
 * Register post types, taxonomies or check for users
 */
function psmt_init() {
	do_action( 'psmt_init' );
}

/**
 * Register/enqueue scripts/styles on this action for front end loading
 */
function psmt_enqueue_scripts() {
	do_action( 'psmt_enqueue_scripts' );
}

/**
 * Register/enqueue scripts/styles on this action for loading on admin/dashboard
 */
function psmt_admin_enqueue_scripts() {
	do_action( 'psmt_admin_enqueue_scripts' );
}

/**
 * Fires on admin_bar_menu
 * Are you adding a node to adminbar or removing a node from adminbar?
 * This is best suited for that
 */
function psmt_setup_admin_bar() {
	do_action( 'psmt_setup_admin_bar' );
}

/**
 * Do not directly use it
 * Only use it if you can not work with psmt_actions, psmt_screens those are more meaningful actions
 */
function psmt_template_redirect() {
	do_action( 'psmt_template_redirect' );
}

/**
 * Register your widgets on this action
 */
function psmt_widgets_init() {
	do_action( 'psmt_widgets_init' );
}

/**
 * Fires on template_redirect
 * Best suited for doing any type of form manipulation/redirect
 */
function psmt_actions() {
	do_action( 'psmt_actions' );
}

/**
 * Add your screen handlers that loads templates on this action
 */
function psmt_screens() {
	do_action( 'psmt_screens' );
}
