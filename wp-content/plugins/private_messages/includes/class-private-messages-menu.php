<?php
/**
 * Menu Item
 *
 * @class Private_Messages_Menu
 * @version 1.0.0
 * @author Astoundify
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Menu Navigation Modification.
 *
 * @since 1.0.0
 */
class Private_Messages_Menu {

	/**
	 * Hook in to WordPress
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'walker_nav_menu_start_el', array( $this, 'unread_count_item' ), 10, 4 );
	}

	/**
	 * Replace {{private_message_count}} with a badge of unread messages.
	 *
	 * @since  1.0.0
	 *
	 * @param string $item_output Item output.
	 * @param object $item        Item.
	 * @param int    $depth       Depth.
	 * @param array  $args        Args.
	 * @return string $item_output
	 */
	public function unread_count_item( $item_output, $item, $depth, $args ) {
		if ( false === strpos( $item->title, '{{private_message_count}}' ) ) {
			return $item_output;
		}

		$count = pm_get_unread_count( get_current_user_id() );

		$item_output = str_replace( '{{private_message_count}}', '<span class="private-message-count">' . $count . '</span>', $item_output );

		return $item_output;
	}

}

new Private_Messages_Menu();
