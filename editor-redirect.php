<?php
/*
@package editor-redirect

Plugin Name: Editor Redirect
Plugin URI: https://github.com/ArneGockeln/editor-redirect
Description: This plugin redirects the user to the overview page after they hit the 'Save/Publish Post' button in the Gutenberg editor.
Version: 0.1.0
Author: Arne Gockeln
Author URI: https://webchef.de
Requires PHP: 8.0
Requires at least: 6.9
Text Domain: editor-redirect
License:  GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0
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
function ag_editor_redirect_is_editor_active(): bool {
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

    if ( ( $post_id = sanitize_key( wp_unslash( $_GET['post'] ?? 0 ) ) ) ) {
        if ( function_exists( 'use_block_editor_for_post' ) && ( $post = get_post( $post_id ) ) !== null ) {
            return use_block_editor_for_post( $post );
        }
    }

    return false;
}

/**
 * Enqueue Editor Redirect JS Script.
 *
 * @return void
 */
function ag_editor_redirect_enqueue_admin_scripts_action(): void {
    global $post;

    if ( is_null( $post ) || ! ag_editor_redirect_is_editor_active() ) {
        return;
    }

    wp_enqueue_script('editor-redirect', plugins_url( 'js/editor-redirect.js', __FILE__ ), [ 'wp-data', 'wp-dom-ready' ], '1.0.0', true);
    wp_localize_script( 'editor-redirect', 'editorRedirect', [
        'overviewUrl' => admin_url( sprintf( 'edit.php?post_type=%s',  $post->post_type ) ),
        'nonce' => wp_create_nonce('ajax-nonce')
    ]);
}
add_action( 'admin_enqueue_scripts', 'ag_editor_redirect_enqueue_admin_scripts_action');