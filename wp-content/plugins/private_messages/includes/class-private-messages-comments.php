<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Comments
 *
 * Handle comments (messages between parties).
 *
 * @since 1.0.0
 * @category Class
 * @author Astoundify
 */
class Private_Messages_Comments {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Exclude PM comment by modifying comment query clause.
		add_filter( 'comments_clauses', array( $this, 'exclude_pm_comments' ) );

		// Exclude PM comment in feed.
		add_action( 'comment_feed_join', array( $this, 'exclude_pm_comments_from_feed_join' ) );
		add_action( 'comment_feed_where', array( $this, 'exclude_pm_comments_from_feed_where' ) );
	}

	/**
	 * Exclude PM Comments everywhere by filtering the comment query clauses.
	 * By using this filter we need to get all comment using direct wpdb method.
	 *
	 * @since 1.0.0
	 * @link https://developer.wordpress.org/reference/hooks/comments_clauses/
	 *
	 * @param array $clauses Query clauses.
	 * @return array
	 */
	public function exclude_pm_comments( $clauses ) {
		global $wpdb, $typenow;

		// Do not exclude in PM admin screen.
		if ( $typenow === 'private-messages' ) {
			return $clauses;
		}

		// Set "join" clause if not set.
		if ( ! $clauses['join'] ) {
			$clauses['join'] = '';
		}

		// Use "LEFT JOIN" to get all comment from all posts type.
		if ( ! strstr( $clauses['join'], "JOIN $wpdb->posts" ) ) {
			$clauses['join'] .= " LEFT JOIN $wpdb->posts ON comment_post_ID = $wpdb->posts.ID ";
		}

		// Set "where" clause.
		if ( $clauses['where'] ) {
			$clauses['where'] .= ' AND ';
		}

		// Exclude PM comment post type.
		$clauses['where'] .= " $wpdb->posts.post_type NOT IN ('private-messages') ";

		return $clauses;
	}

	/**
	 * Comment Feed "JOIN" clause.
	 *
	 * @since 1.0.0
	 *
	 * @param string $join SQL Join clause.
	 */
	public function exclude_pm_comments_from_feed_join( $join ) {
		global $wpdb;

		if ( ! strstr( $join, $wpdb->posts ) ) {
			$join = " LEFT JOIN $wpdb->posts ON $wpdb->comments.comment_post_ID = $wpdb->posts.ID ";
		}
		return $join;
	}

	/**
	 * Comment Feed "WHERE" clause.
	 *
	 * @since 1.0.0
	 *
	 * @param string $where SQL Where clause.
	 */
	public function exclude_pm_comments_from_feed_where( $where ) {
		global $wpdb;

		if ( $where ) {
			$where .= ' AND ';
		}
		$where .= " $wpdb->posts.post_type NOT IN ('private-messages') ";
		return $where;
	}

}

// Load class.
new Private_Messages_Comments();
