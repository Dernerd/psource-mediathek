<?php
/**
 * Media list widget.
 *
 * @package psourcemediathek.
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Universal Media widget
 */
class PSMT_Media_List_Widget extends WP_Widget {
	/**
	 * PSMT_Media_List_Widget constructor.
	 *
	 * @param string $name widget name.
	 * @param array  $widget_options widget options.
	 */
	public function __construct( $name = '', $widget_options = array() ) {

		if ( empty( $name ) ) {
			$name = __( 'PSM Medien Liste', 'psourcemediathek' );
		}

		parent::__construct( false, $name, $widget_options );
	}

	/**
     * Display media list.
	 *
     * @param array $args args.
	 * @param array $instance widget instance.
	 */
	public function widget( $args, $instance ) {

		$defaults = array(
			// gallery type, all,audio,video,photo etc.
			'type'          => '',
			// pass specific gallery id.
			'id'            => '',
			// pass specific gallery ids as array.
			'in'            => array(),
			// pass gallery ids to exclude.
			'exclude'       => array(),
			// pass gallery slug to include.
			'slug'          => '',
			// public,private,friends one or more privacy level.
			'status'        => '',
			// one or more component name user,groups, evenets etc.
			'component'     => '',
			// the associated component id, could be group id, user id, event id.
			'component_id'  => '',
			// how many items per page.
			'per_page'      => false,
			// how many galleries to offset/displace.
			'offset'        => false,
			// which page when paged.
			'page'          => false,
			// to avoid paging.
			'nopaging'      => false,
			// order.
			'order'         => 'DESC',
			// none, id, user, title, slug, date,modified, random, comment_count, meta_value,meta_value_num, ids.
			// user params.
			'orderby'       => 'date',
			'user_id'       => '',
			'include_users' => array(),
			// users to exclude.
			'exclude_users' => array(),
			'user_name'     => '',
			'scope'         => false,
			// time parameter.
			'search_terms'  => '',
			// this year.
			'year'          => '',
			// 1-12 month number.
			'month'         => '',
			// 1-53 week
			'week'          => '',
			// specific day.
			'day'           => '',
			// specific hour.
			'hour'          => '',
			// specific minute.
			'minute'        => '',
			// specific second 0-60.
			'second'        => '',
			// yearMonth, 201307//july 2013
			'yearmonth'     => '',
			'meta_key'      => '',
			'meta_value'    => '',
			// 'meta_query'=>false,
			// which fields to return ids, id=>parent, all fields(default).
			'fields'        => '',
            'for'           => '',
		);

		$instance = (array) $instance;
		$title = $instance['title'];
		unset( $instance['title'] );

		$query_args = array_merge( $defaults, $instance );

		$for = $query_args['for'];
		unset( $query_args['for'] );

		if ( $for ) {
			$query_args['user_id'] = psmt_get_dynamic_user_id_for_context( $for );
			if ( empty( $query_args['user_id'] ) ) {
				return;
			}
		}

		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$playlist = $instance['playlist'];

		unset( $instance['playlist'] );

		$query = new PSMT_Media_Query( $query_args );

		psmt_widget_save_media_data( 'query', $query );

		$type = $instance['type'];
		$slug = '';

		$view = 'grid';

		if ( $playlist ) {
			$view = 'playlist';
		}

		psmt_get_template_part( "widgets/{$view}", $type );// shortcodes/playlist-entry.php.

		psmt_widget_reset_media_data( 'query' );

		echo $args['after_widget'];
	}

	/**
	 * Update widget settings.
	 *
	 * @param array $new_instance new instance.
	 * @param array $old_instance old instance.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		if ( psmt_is_active_component( $new_instance['component'] ) ) {
			$instance['component'] = $new_instance['component'];
		}

		if ( psmt_is_active_type( $new_instance['type'] ) ) {
			$instance['type'] = $new_instance['type'];
		}

		if ( psmt_is_active_status( $new_instance['status'] ) ) {
			$instance['status'] = $new_instance['status'];
		}

		$instance['per_page'] = absint( $new_instance['per_page'] );

		$instance['orderby'] = $new_instance['orderby'];

		$instance['order']    = $new_instance['order'];
		$instance['playlist'] = $new_instance['playlist'];
		$instance['for']      = $new_instance['for'];

		return $instance;
	}

	/**
	 * Display widget settings.
	 *
	 * @param array $instance current instance.
	 *
	 * @return null
	 */
	public function form( $instance ) {

		$defaults = array(
			// gallery type, all,audio,video,photo etc.
			'type'          => false,
			// pass specific gallery id.
			'id'            => false,
			// pass specific gallery ids as array.
			'in'            => false,
			// pass gallery ids to exclude.
			'exclude'       => false,
			// pass gallery slug to include.
			'slug'          => false,
			// public,private,friends one or more privacy level.
			'status'        => false,
			// one or more component name user,groups, evenets etc.
			'component'     => false,
			// the associated component id, could be group id, user id, event id.
			'component_id'  => false,
			// how many items per page.
			'per_page'      => 5,
			// how many galleries to offset/displace.
			'offset'        => false,
			// which page when paged.
			'page'          => false,
			// to avoid paging.
			'nopaging'      => false,
			// order.
			'order'         => 'DESC',
			// none, id, user, title, slug, date,modified, random, comment_count, meta_value,meta_value_num, ids
			// user params.
			'orderby'       => 'date',
			'user_id'       => false,
			'include_users' => false,
			// users to exclude.
			'exclude_users' => false,
			'user_name'     => false,
			'scope'         => false,
			// time parameter.
			'search_terms'  => '',
			// this year.
			'year'          => false,
			// 1-12 month number.
			'month'         => false,
			// 1-53 week
			'week'          => '',
			// specific day.
			'day'           => '',
			// specific hour.
			'hour'          => '',
			// specific minute.
			'minute'        => '',
			// specific second 0-60.
			'second'        => '',
			// yearMonth, 201307//july 2013
			'yearmonth'     => false,
			'meta_key'      => '',
			'meta_value'    => '',
			// 'meta_query'=>false,
			// which fields to return ids, id=>parent, all fields(default)
			'fields'        => false,
			'column'        => 4,
			'title'         => _x( 'Aktuelle Medien', 'media widget title', 'psourcemediathek' ),
			'playlist'      => 0,
			'for'           => '',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
        <p>
            <label for="psmt-gallery-widget-title"><?php _e( 'Titel:', 'psourcemediathek' ); ?>
                <input class="widefat" id="psmt-gallery-widget-title"
                       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                       value="<?php echo esc_attr( $instance['title'] ); ?>" style="width: 100%"/>
            </label>
        </p>
        <table>

            <tr>
                <td>
                    <label for="<?php echo $this->get_field_id( 'component' ); ?>"><?php _e( 'Komponente ausw??hlen:', 'psourcemediathek' ); ?></label>
                </td>
                <td>

					<?php psmt_component_dd( array(
						'name'     => $this->get_field_name( 'component' ),
						'id'       => $this->get_field_id( 'component' ),
						'selected' => $instance['component'],
					) );
					?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Typ ausw??hlen:', 'psourcemediathek' ); ?></label>
                </td>
                <td>

					<?php psmt_type_dd( array(
						'name'     => $this->get_field_name( 'type' ),
						'id'       => $this->get_field_id( 'type' ),
						'selected' => $instance['type'],
					) );
					?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo $this->get_field_id( 'status' ); ?>"><?php _e( 'Status ausw??hlen:', 'psourcemediathek' ); ?></label>
                </td>
                <td>
					<?php psmt_status_dd( array(
						'name'     => $this->get_field_name( 'status' ),
						'id'       => $this->get_field_id( 'status' ),
						'selected' => $instance['status'],
					) );
					?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo $this->get_field_id( 'for' ); ?>"><?php _e( 'Medien von:', 'psourcemediathek' ); ?></label>
                </td>
                <td>
                    <select id="<?php echo $this->get_field_id( 'for' ); ?>"
                            name="<?php echo $this->get_field_name( 'for' ); ?>">
                        <option value="" <?php selected( '', $instance['for'] ); ?>><?php _e( 'Jeder', 'psourcemediathek' ); ?></option>
                        <option value="logged" <?php selected( 'logged', $instance['for'] ); ?>><?php _e( 'Angemeldeter Benutzer', 'psourcemediathek' ); ?></option>
                        <?php if ( psourcemediathek()->is_bp_active() ) : ?>
                            <option value="displayed" <?php selected( 'displayed', $instance['for'] ); ?>><?php _e( 'Angezeigter Benutzer', 'psourcemediathek' ); ?></option>
                        <?php endif;?>
                    </select>

                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo $this->get_field_id( 'per_page' ); ?>"><?php _e( 'Pro Seite:', 'psourcemediathek' ); ?></label>
                </td>
                <td>
                    <input class="" id="<?php echo $this->get_field_id( 'per_page' ); ?>"
                           name="<?php echo $this->get_field_name( 'per_page' ); ?>" type="number"
                           value="<?php echo absint( $instance['per_page'] ); ?>"/>

                </td>
            </tr>

            <tr>
                <td>
                    <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Sortieren nach:', 'psourcemediathek' ); ?></label>
                </td>
                <td>
                    <select id="<?php echo $this->get_field_id( 'orderby' ); ?>"
                            name="<?php echo $this->get_field_name( 'orderby' ); ?>">
                        <option value="title" <?php selected( 'title', $instance['orderby'] ); ?>><?php _e( 'Alphabetisch', 'psourcemediathek' ); ?></option>
                        <option value="date" <?php selected( 'date', $instance['orderby'] ); ?>><?php _e( 'Datum', 'psourcemediathek' ); ?></option>
                        <option value="rand" <?php selected( 'rand', $instance['orderby'] ); ?>><?php _e( 'Zuf??llig', 'psourcemediathek' ); ?></option>
                    </select>

                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Sortierreihenfolge', 'psourcemediathek' ); ?></label>
                </td>
                <td>
                    <select id="<?php echo $this->get_field_id( 'order' ); ?>"
                            name="<?php echo $this->get_field_name( 'order' ); ?>">
                        <option value="ASC" <?php selected( 'ASC', $instance['order'] ); ?>><?php _e( 'Aufsteigend', 'psourcemediathek' ); ?></option>
                        <option value="DESC" <?php selected( 'DESC', $instance['order'] ); ?>><?php _e( 'Absteigend', 'psourcemediathek' ); ?></option>
                    </select>

                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo $this->get_field_id( 'playlist' ); ?>"><?php _e( 'Als Playlist anzeigen?', 'psourcemediathek' ); ?></label>
                </td>
                <td>
                    <input type='checkbox' id="<?php echo $this->get_field_id( 'playlist' ); ?>"
                           name="<?php echo $this->get_field_name( 'playlist' ); ?>"
                           value='1' <?php checked( 1, $instance['playlist'] ); ?> />
                    <p> <?php _e( 'gilt nur f??r den Audio/Video-Typ', 'psourcemediathek' ); ?>
                </td>
            </tr>

        </table>

		<?php
	}
}

/**
 * Register widget.
 */
function psmt_register_list_media_widgets() {
	register_widget( 'PSMT_Media_List_Widget' );
}

add_action( 'psmt_widgets_init', 'psmt_register_list_media_widgets' );