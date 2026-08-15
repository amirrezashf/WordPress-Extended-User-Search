<?php
/**
 * Plugin Name:       WordPress Extended User Search
 * Plugin URI:        https://github.com/amirrezashf/WordPress-Extended-User-Search
 * Description:       Extend WordPress admin user search to include nickname, display name, nicename, and selected user meta fields such as mobile numbers.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Amirreza Shayesteh Far
 * Author URI:        https://github.com/amirrezashf
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       wp-extended-user-search
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class WP_Extended_User_Search {

	const VERSION = '1.0.0';

	/**
	 * Singleton instance.
	 *
	 * @var WP_Extended_User_Search|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return WP_Extended_User_Search
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register hooks.
	 */
	private function __construct() {
		add_filter( 'user_search_columns', array( $this, 'extend_core_search_columns' ), 10, 3 );
		add_action( 'pre_user_query', array( $this, 'extend_user_query_with_meta' ), 20 );
	}

	/**
	 * Extend native WordPress user-table search columns.
	 *
	 * @param array         $search_columns Searchable columns.
	 * @param string        $search         Search term.
	 * @param WP_User_Query $query          User query instance.
	 * @return array
	 */
	public function extend_core_search_columns( $search_columns, $search, $query ) {
		if ( ! $this->should_extend_query( $query, $search ) ) {
			return $search_columns;
		}

		$search_columns[] = 'user_nicename';
		$search_columns[] = 'display_name';

		return array_values( array_unique( $search_columns ) );
	}

	/**
	 * Add usermeta-based search conditions.
	 *
	 * @param WP_User_Query $query User query instance.
	 */
	public function extend_user_query_with_meta( $query ) {
		global $wpdb;

		if ( ! $query instanceof WP_User_Query ) {
			return;
		}

		$raw_search = isset( $query->query_vars['search'] )
			? (string) $query->query_vars['search']
			: '';

		if ( ! $this->should_extend_query( $query, $raw_search ) ) {
			return;
		}

		$search = $this->normalize_search_term( $raw_search );

		if ( '' === $search ) {
			return;
		}

		$meta_keys = $this->get_searchable_meta_keys();

		if ( empty( $meta_keys ) ) {
			return;
		}

		$alias = 'wpeus_meta';

		if ( false === strpos( $query->query_from, " {$alias} " ) ) {
			$query->query_from .= " LEFT JOIN {$wpdb->usermeta} AS {$alias} ON {$wpdb->users}.ID = {$alias}.user_id";
		}

		$meta_key_placeholders = implode( ', ', array_fill( 0, count( $meta_keys ), '%s' ) );
		$like                  = '%' . $wpdb->esc_like( $search ) . '%';

		$params   = $meta_keys;
		$params[] = $like;

		$meta_condition = $wpdb->prepare(
			"({$alias}.meta_key IN ({$meta_key_placeholders}) AND {$alias}.meta_value LIKE %s)",
			$params
		);

		if ( ! is_string( $meta_condition ) || '' === $meta_condition ) {
			return;
		}

		$query->query_where .= ' OR ' . $meta_condition;

		if ( false === stripos( $query->query_fields, 'DISTINCT' ) ) {
			$query->query_fields = preg_replace(
				'/^\s*SELECT\s+/i',
				'SELECT DISTINCT ',
				$query->query_fields,
				1
			);
		}
	}

	/**
	 * Determine whether this user query should be extended.
	 *
	 * @param WP_User_Query $query  User query.
	 * @param string        $search Search string.
	 * @return bool
	 */
	private function should_extend_query( $query, $search ) {
		if ( ! is_admin() || ! $query instanceof WP_User_Query ) {
			return false;
		}

		if ( '' === trim( (string) $search ) ) {
			return false;
		}

		global $pagenow;

		$allowed = 'users.php' === $pagenow;

		return (bool) apply_filters( 'wpeus_should_extend_query', $allowed, $query, $search );
	}

	/**
	 * Normalize the native WordPress user-search string.
	 *
	 * @param string $search Raw search string.
	 * @return string
	 */
	private function normalize_search_term( $search ) {
		$search = wp_unslash( (string) $search );
		$search = trim( $search );
		$search = trim( $search, "* \t\n\r\0\x0B" );

		return sanitize_text_field( $search );
	}

	/**
	 * Get user meta keys included in extended search.
	 *
	 * @return array
	 */
	private function get_searchable_meta_keys() {
		$keys = array(
			'nickname',
			'mobile_user',
		);

		$keys = apply_filters( 'wpeus_searchable_meta_keys', $keys );

		if ( ! is_array( $keys ) ) {
			return array();
		}

		$keys = array_map( 'sanitize_key', $keys );

		return array_values( array_unique( array_filter( $keys ) ) );
	}
}

WP_Extended_User_Search::instance();
