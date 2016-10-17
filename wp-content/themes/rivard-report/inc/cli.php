<?php

/**
 * Manage Rivard Report tasks for migrating posts
 */
class RR_WP_CLI extends WP_CLI_Command {
	private function log($stuff) {
		WP_CLI::line( var_export( $stuff, true ) );
	}

	/**
	 * Get IDs of posts that have a specific meta ID
	 * 
	 * @param String $meta_key The meta_key what we're looking for
	 */
	private function find_post_ids_by_meta_key( $meta_key = '' ) {
		global $wpdb;
		$raw_ids = $wpdb->get_results( "SELECT distinct post_id from $wpdb->postmeta where meta_key = '$meta_key'" );

		// $raw_ids is probably an array of array( post_id => string ints ), so let's flatten that to an array of ints
		$just_ids = array();

		foreach ( $raw_ids as $k => $v ) {
			// because of stdClass::__set_state(array(
			if ( is_object( $v ) ) {
				$v = (array) $v;
			}

			if ( is_array( $v ) ) {
				if ( is_numeric( $v['post_id'] ) ) {
					$just_ids[] = $v['post_id'];
				} else {
					$this->log( "Found a post_id that has metadata but isn't a valid post_id:" );
					$this->log( $v );
				}
			} else if ( is_numeric( $v ) ) {
				if ( is_int( $v ) ) {
					$just_ids[] = $v;
				} else {
					$this->log( "Found a post_id that has metadata but isn't a valid post_id:" );
					$this->log( $v );
				}
			} else {
				$this->log( "Found a post_id that has metadata but isn't a valid post_id:" );
				$this->log( $v );
			}
		}

		return $just_ids;
	}

	/**
	 * For ever post with a '_bucket_main_gallery' meta_value, create Largo-style featured media for them:
	 * array (
	 *     'type' => 'gallery',
	 *     'ids' => array(),
	 *     'attachment_data' => array()
	 * )
	 *
	 * @uses find_gallery_post_ids
	 */
	public function migrate_galleries() {
		$old_meta_key = '_bucket_main_gallery';
		$post_ids = $this->find_post_ids_by_meta_key( $old_meta_key );

		$progress = \WP_CLI\Utils\make_progress_bar( "Migrating video media field keys...", count( $post_ids ) );

		foreach ( $post_ids as $post_id ) {
			$former_gallery = get_post_meta( $post_id, $old_meta_key, true );
			$new_gallery = explode( ',', $former_gallery );
			$attachment_data = wp_prepare_attachment_for_js( $new_gallery[0] );

			$new = array (
				'type' => 'gallery',
				'gallery' => $new_gallery,
				'attachment_data' => $attachment_data,
			);

			// don't overwrite things that exist?
			$existing_featured_media = get_post_meta( $post_id, 'featured_media', true );
			if ( !empty( $existing_featured_media ) ) {
				$this->log( "Post $post_id has existing featured media metadata; we won't overwrite it with this data:" );
				$this->log( $new );
				$progress->tick();
				continue;
			}

			// Destructive!
			update_post_meta( $post_id, 'featured_media', $new );
			delete_post_meta( $post_id, $old_meta_key );
			$progress->tick();
		}

		$progress->finish();
	}

	/**
	 * For ever post with a '_bucket_video_embed' meta_value, create Largo-style featured media for them:
	 * array (
	 *     'type' => 'video',
	 *     'url' => $embed['src'], //linkable canon link for the video 
	 *     'thumbmail_url' => '', // URL for the thumbnail image
	 *     'thumbnail_type' => '', // This is usually 'oembed'
	 *     'title' => '', // Defaults to title of the video. 
	 *     'caption' => '', // Can be left empty.
	 *     'embed' => '', // HTML <iframe> tag with src="" attribute
	 *     'credit' => '', // Author of the video.
	 *     'attachment_data' => array() // of the post meta for the attachment id that is the thumbnail of this post
	 * )
	 *
	 * @uses find_gallery_post_ids
	 * @uses largo_media_sideload_image
	 *
	 * These are inessential; the migration works without them.
	 * @todo find a way to get the oembed data that isn't js
	 * @todo find a way to set a proper thumbnail on these posts
	 */
	public function migrate_videos() {
		$old_meta_key = '_bucket_video_embed';

		$post_ids = $this->find_post_ids_by_meta_key( $old_meta_key );

		$progress = \WP_CLI\Utils\make_progress_bar( "Migrating gallery media field keys...", count( $post_ids ) );

		foreach ( $post_ids as $post_id ) {
			// The format of _bucket_video_embed was an html_ecape()'d string, so we need to turn it back to HTML
			$embed_html = html_entity_decode( get_post_meta( $post_id, $old_meta_key, true ), ENT_COMPAT, get_option('blog_charset') );

			// get the embed URL from the iframe
			preg_match( '/src="([^"]+)"/', $embed_html, $match );
			$src = $match[1];

			// Get the current thumbnail image, use that as the featured emdia thumbnail image
			$current_thumbnail = get_post_meta( $post_id, '_thumbnail_id', true );
			$attachment_data = wp_prepare_attachment_for_js( $current_thumbnail ); // save the image as an attachment

			$new = array(
				// Specify the type
				'type' => 'video',

				// linkable canon link for the video, which in this case is just the embed url
				'url' => $src,

				// Largo sets these in js/featured-media.js, by doing an AJAX call in the browser
				// Not setting them here does not harm anything
				'thumbnail_type' => '',

				// These can be left empty without consequence
				'title' => '', // Defaults to title of the video. Largo sets this in js/featured-media.js, by doing an AJAX call in the browser
				'caption' => '', // Can be left empty. Largo sets this in js/featured-media.js, by doing an AJAX call in the browser
				'credit' => '', // Author of the video. Largo sets this in js/featured-media.js, by doing an AJAX call in the browser

				// This is the HTML for the embed
				'embed' => html_entity_decode($embed_html, ENT_COMPAT, get_option('blog_charset') ),

				// This is the thumbnail
				'thumbmail_url' => wp_get_attachment_url( $current_thumbnail ),
				'attachment_data' => $attachment_data // of the attachment that is the thumbnail for the post, which isn't set
			);

			// don't overwrite things that exist?
			$existing_featured_media = get_post_meta( $post_id, 'featured_media', true );
			if ( !empty( $existing_featured_media ) ) {
				$this->log( "Post $post_id has existing featured media metadata; we won't overwrite it with this data:" );
				$this->log( $new );
				$progress->tick();
				continue;
			}

			// Destructive!
			update_post_meta( $post_id, 'featured_media', $new );
			delete_post_meta( $post_id, $old_meta_key );
			$progress->tick();
		}

		$progress->finish();
	}

	/**
	 * Run all migrations
	 *
	 * @uses migrate_videos
	 * @uses migrate_galleries
	 */
	public function migrate_all_post_types() {
		$this->migrate_videos();
		$this->migrate_galleries();
	}
}
