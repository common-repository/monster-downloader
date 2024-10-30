<?php

use Pluginbazar\Utils;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class MONSTER_DOWNLOADER_Reports_table extends WP_List_Table {

	/**
	 * @var array
	 */
	private $downloaded_data = array();

	/**
	 * Return downloaded data
	 *
	 * @param string $search
	 *
	 * @return array|object|stdClass[]|null
	 */
	private function get_downloaded_data( $search_string = "" ) {

		global $wpdb;

		$filter_type = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';

		if ( ! empty( $search_string ) ) {
			$search_string = str_replace( array( ' ', '_' ), '-', strtolower( $search_string ) );

			return $wpdb->get_results( "SELECT * FROM " . MONSTER_DOWNLOADER_TABLE_REPORTS . " WHERE object_name Like '%{$search_string}%'", ARRAY_A );
		}

		if ( ! empty( $filter_type ) ) {
			return $wpdb->get_results( "SELECT * FROM " . MONSTER_DOWNLOADER_TABLE_REPORTS . " WHERE  object_type = '{$filter_type}' ", ARRAY_A );
		}

		return $wpdb->get_results( "SELECT * FROM " . MONSTER_DOWNLOADER_TABLE_REPORTS, ARRAY_A );
	}


	/**
	 * @return array
	 */
	function get_columns() {
		return apply_filters( 'WPDP/Filters/get_report_columns',
			array(
				'id'            => esc_html__( 'ID', 'monster-downloader' ),
				'object_name'   => esc_html__( 'Name', 'monster-downloader' ),
				'object_type'   => esc_html__( 'Type', 'monster-downloader' ),
				'downloaded_by' => esc_html__( 'Downloaded By', 'monster-downloader' ),
				'datetime'      => esc_html__( 'Downloaded At', 'monster-downloader' ),
			)
		);
	}


	/**
	 * @return void
	 */
	function prepare_items() {

		$search_string         = isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '';
		$downloaded_data       = $this->get_downloaded_data( $search_string );
		$columns               = $this->get_columns();
		$per_page              = 20;
		$current_page          = $this->get_pagenum();
		$count                 = count( $downloaded_data );
		$this->downloaded_data = array_slice( $downloaded_data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->set_pagination_args( array(
			'total_items' => $count,
			'per_page'    => $per_page,
		) );

		$this->_column_headers = array( $columns );
		$this->items           = $this->downloaded_data;
	}

	/**
	 * @param $item
	 * @param $column_name
	 *
	 * @return bool|mixed|string|void
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
			case 'object_name':
			case 'object_type':
			case 'downloaded_by':
			case 'datetime':
			default:
				return $item[ $column_name ];
		}
	}

	/**
	 * Column object_name
	 *
	 * @param $item
	 *
	 * @return void
	 */
	function column_object_name( $item ) {

		$object_name   = Utils::get_args_option( 'object_name', $item );
		$object_name   = ucwords( str_replace( array( '-', '_' ), ' ', $object_name ) );
		$row_actions[] = sprintf( '<span class="monster-downloader - download"><a href=" % s">%s</a></span>', '', esc_html__( 'Download', 'monster-downloader' ) );

		printf( '<a href="#"><strong>%s</strong></a>', $object_name );
		printf( '<div class="row-actions visible">%s</div>', implode( ' | ', $row_actions ) );
	}


	/**
	 * Column object type
	 *
	 * @param $item
	 */
	function column_object_type( $item ) {

		$object      = Utils::get_args_option( 'object_type', $item );
		$object_type = ucfirst( $object );

		printf( '<div><strong>%s</strong></div>', $object_type );
	}

	/**
	 * @param $item
	 *
	 * @return void
	 */
	function column_downloaded_by( $item ) {

		$user_id       = Utils::get_args_option( 'downloaded_by', $item );
		$downloaded_by = get_user_by( 'id', $user_id );

		printf( '<div><a href="#"><strong>%s</strong></a></div>', $downloaded_by->display_name );
		printf( '<div><a href="#"><strong>%s</strong></a></div>', $downloaded_by->user_email );
	}

	/**
	 * @param $item
	 *
	 * @return void
	 */
	function column_datetime( $item ) {

		$time     = Utils::get_args_option( 'datetime', $item );
		$datetime = strtotime( $time );
		$time     = date( 'jS M, y - h:i a', $datetime );
		$datetime = human_time_diff( $datetime, time() ) . esc_html__( ' ago', 'monster-downloader' );
		printf( '<div class="monster-downloader_time_diff">%s</div>', $datetime );
		printf( '<div class="monster-downloader_download_time">%s</div>', $time );
	}


	/**
	 * Add filter form
	 *
	 * @param string $which
	 */
	function extra_tablenav( $which ) {
		if ( $which == "top" ) {

			$filter_type  = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';
			$current_page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';

			?>
            <div class="alignleft ">
                <form action="" method="get">
                    <select name="type">
                        <option value=""><?php esc_html_e( 'All', 'monster-downloader' ); ?></option>
                        <option <?php selected( $filter_type, 'plugin' ); ?> value="plugin"><?php esc_html_e( 'Plugin', 'monster-downloader' ); ?></option>
                        <option <?php selected( $filter_type, 'theme' ); ?> value="theme"><?php esc_html_e( 'Theme', 'monster-downloader' ); ?></option>
                    </select>
                    <input type="hidden" name="page" value="<?php echo esc_attr( $current_page ); ?>">
                    <button class="button" type="submit"><?php echo esc_html__( 'Filter', 'monster-downloader' ); ?></button>
                </form>
            </div>
			<?php
		}
	}
}
