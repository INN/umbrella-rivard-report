<?php
/**
 * remove Largo's google analytics integration, as they're managing their tags themselves
 *
 * @since Largo 0.5.5.4
 * @since Helpscout ticket https://secure.helpscout.net/conversation/615103723/2214/?folderId=1219602
 * @since 2018-07-16
 */
function rr_remove_largo_google_analytics() {
	remove_action( 'wp_head', 'largo_google_analytics' );
}
add_action( 'wp_head', 'rr_remove_largo_google_analytics', 1 );
