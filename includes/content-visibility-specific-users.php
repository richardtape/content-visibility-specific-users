<?php

/**
 * Main loader file for Content Visibility SpecificUsers Add-on.
 *
 * @package ContentVisibilitySpecificUsers
 */

namespace RichardTape\ContentVisibilitySpecificUsers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Use the content_visibility_enqueue_editor_assets action to load our assets so we know we're loading when and where we should be.
add_action( 'content_visibility_enqueue_editor_assets', __NAMESPACE__ . '\\enqueue_editor_assets', 30 );

/**
 * Enqueue script and style assets used in the editor.
 *
 * @since 1.0.0
 */
function enqueue_editor_assets() { // phpcs:ignore

	$prereqs = array(
		'wp-blocks',
		'wp-i18n',
		'wp-element',
		'wp-plugins',
		'wp-dom-ready',
	);

	// The 5.8 widgets screen requires a special editor?! Feelsbadman.
	$CVEditor = new \RichardTape\ContentVisibility\Editor();
	if ( $CVEditor->on_widgets_screen() ) {
		$prereqs[] = 'wp-edit-widgets';
	} else {
		$prereqs[] = 'wp-editor';
	}

	wp_register_script(
		'content-visibility-specificusers',
		plugins_url( '/build/index.js', dirname( __FILE__ ) ),
		$prereqs,
		filemtime( plugin_dir_path( __DIR__ ) . 'build/index.js' ),
		true
	);

	$users = get_users( array( 'fields' => array( 'display_name', 'ID' ) ) );

	$block_visibility_specificusers_args = array(
		'users' => $users,
	);

	wp_localize_script( 'content-visibility-specificusers', 'BlockVisibilitySpecificUsers', $block_visibility_specificusers_args );

	wp_enqueue_script( 'content-visibility-specificusers' );

	wp_enqueue_style( 'content-visibility-specificusers-panel', plugins_url( 'build/index.css', dirname( __FILE__ ) ) );

}//end enqueue_editor_assets()

add_filter( 'content_visibility_rule_types_and_callbacks', __NAMESPACE__ . '\\add_rule_type_and_callback' );

/**
 * Register our rule type to enable us to provide the logic callback.
 *
 * @param array $default_rule_types_and_callbacks Existing rules and callbacks.
 * @return array modified rule types and callbacks with ours added.
 */
function add_rule_type_and_callback( $default_rule_types_and_callbacks ) {

	$default_rule_types_and_callbacks['specificusers'] = __NAMESPACE__ . '\rule_logic_specificusers';

	return $default_rule_types_and_callbacks;

}//end add_rule_type_and_callback()

/**
 * Rule test for our specific users. Is the currently logged in user one of the users in the list
 * of users for this block?
 *
 * @param array  $rule_value Which users are selected for this block.
 * @param string $block_visibility Whether the block should be shown or hidden if the rule is true.
 * @param array  $block The full block.
 * @return bool  false if the block is to be removed. true if the block is to be potentially kept.
 */
function rule_logic_specificusers( $rule_value, $block_visibility, $block ) {

	// Make sure we're not touching this block if no users are set. keep this block to let others decide.
	if ( ! is_array( $rule_value ) || empty( $rule_value ) ) {
		return true;
	}

	// Check we have relevant data to check - i.e. we have some user IDs to check.
	if ( ! isset( $rule_value['specificusers'] ) || empty( $rule_value['specificusers'] ) ) {
		return true;
	}

	$user_ids = array_column( $rule_value['specificusers'], 'value' );

	if ( ! is_array( $user_ids ) || empty( $user_ids ) ) {
		return true;
	}

	// Some users are selected and the user isn't signed in. Need a user to be logged in.
	if ( ! is_user_logged_in() ) {
		return false;
	}

	// OK, this block is set to only be shown/hidden to some user IDs. Compare that to the current
	// user's ID.
	$current_user_id = get_current_user_id();

	$is_current_user_in_list_of_users_for_this_block = false;

	if ( in_array( $current_user_id, array_values( $user_ids ) ) ) {
		$is_current_user_in_list_of_users_for_this_block = true;
	}

	switch ( $block_visibility ) {
		case 'shown':
			return $is_current_user_in_list_of_users_for_this_block;

		case 'hidden':
			return ! $is_current_user_in_list_of_users_for_this_block;
	}

}//end rule_logic_specificusers()

