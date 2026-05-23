<?php
/*
@package gutenberg-redirect

Plugin Name: Gutenberg Redirect
Plugin URI: https://github.com/ArneGockeln/gutenberg-redirect
Description: Redirect Gutenberg to post overview after save action.
Version: 0.1
Author: Arne Gockeln
Author URI: https://webchef.de
Requires PHP: 8.0
Requires at least: 6.9
Text Domain: gutenberg-redirect
*/

// Exit if not called inside WP flow
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check if the Gutenberg editor is active right now.
 *
 * @return bool
 */
function gutenberg_redirect_is_editor_active(): bool {
    if ( ! is_admin() ) {
        return false;
    }

    global $pagenow;

    if ( ! in_array( $pagenow, [ 'post.php', 'post-new.php' ], true ) ) {
        return false;
    }

    if ( function_exists( 'get_current_screen' ) ) {
        $screen = get_current_screen();
        if ( $screen && method_exists( $screen, 'is_block_editor' ) ) {
            return $screen->is_block_editor();
        }
    }


    if ( ( $post_id = $_GET['post'] ?? 0 ) ) {
        if ( function_exists( 'use_block_editor_for_post' ) && ( $post = get_post( $post_id ) ) !== null ) {
            return use_block_editor_for_post( $post );
        }
    }

    return false;
}

/**
 * Enqueue Gutenberg Redirect JS Script.
 *
 * @return void
 */
function gutenberg_redirect_enqueue_admin_scripts_action(): void {
    global $post;

    if ( is_null( $post ) || ! gutenberg_redirect_is_editor_active() ) {
        return;
    }

    wp_enqueue_script('gutenberg-redirect', trailingslashit( plugin_dir_url(__FILE__ ) ) . 'js/gutenberg-redirect.js', [ 'wp-data', 'wp-dom-ready' ], '1.0.0', true);
//    wp_localize_script( 'gutenberg-redirect', 'gutenberg_redirect_params', [
//        'redirect_url' => admin_url( 'post.php?post_type=' . $post->post_type )
//    ]);
}
add_action( 'admin_enqueue_scripts', 'gutenberg_redirect_enqueue_admin_scripts_action' );