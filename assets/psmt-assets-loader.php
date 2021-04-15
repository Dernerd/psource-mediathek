<?php
/**
 * Asset Loader.
 *  Loads various scripts/styles for PsourceMediathek
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Script Loader for PsourceMediathek, loads appropriate scripts as enqueued by various components of gallery
 */
class PSMT_Assets_Loader {

	/**
	 * Absolute url to the psourcemediathek plugin dir
	 *
	 * @var string
	 */
	private $url = '';

	/**
	 * Singleton instance of PSMT_Assets_Loader
	 *
	 * @var PSMT_Assets_Loader
	 */
	private static $instance;

	/**
	 * PSMT_Assets_Loader constructor.
	 */
	private function __construct() {

		$this->url = psourcemediathek()->get_url();

		// load js on front end.
		add_action( 'psmt_enqueue_scripts', array( $this, 'load_js' ) );
		add_action( 'psmt_enqueue_scripts', array( $this, 'add_js_data' ) );

		// load admin js.
		add_action( 'psmt_admin_enqueue_scripts', array( $this, 'load_js' ) );
		add_action( 'psmt_admin_enqueue_scripts', array( $this, 'add_js_data' ) );

		add_action( 'psmt_enqueue_scripts', array( $this, 'load_css' ) );

		add_action( 'wp_footer', array( $this, 'footer' ) );
		add_action( 'in_admin_footer', array( $this, 'footer' ) );
	}

	/**
	 * Factory Method, Get singleton instance.
	 *
	 * @return PSMT_Assets_Loader singleton instance
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {

			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Load the PsourceMediathek js files/codes
	 */
	public function load_js() {

		// use it to avoid loading psourcemediathek js where not required.
		if ( ! apply_filters( 'psmt_load_js', true ) ) {
			// is this  a good idea? should we allow this?
			return;
		}

		// we can further refine it in future to only load a part of it on the pages, depending on current context and user state
		// for now, let us keep it all together
		// Uploader class.
		wp_register_script( 'psmt_uploader', $this->url . 'assets/js/uploader.js', array(
			'plupload',
			'plupload-all',
			'jquery',
			'underscore',
			'json2',
			'media-models',
		) );
		// 'plupload-all'
		// magnific popup for lightbox.
		wp_register_script( 'magnific-js', $this->url . 'assets/vendors/magnific/jquery.magnific-popup.min.js', array( 'jquery' ) );

		// comment+posting activity on single gallery/media page.
		wp_register_script( 'psmt_activity', $this->url . 'assets/js/activity.js', array( 'jquery' ) ); //'plupload-all'
		// everything starts here.
		wp_register_script( 'psmt_core', $this->url . 'assets/js/psmt.js', array(
			'jquery',
			'jquery-ui-sortable',
			'jquery-touch-punch', // for mobile jquery ui drag/drop support.
		) );

		wp_register_script( 'psmt_remote', $this->url .'assets/js/psmt-remote.js', array('jquery') );


		wp_register_script( 'psmt_settings_uploader', $this->url . 'admin/psmt-settings-manager/core/_inc/uploader.js', array( 'jquery' ) );

		// we have to be selective about admin only? we always load it on front end
		// do not load on any admin page except the edit gallery?
		if ( is_admin() && function_exists( 'get_current_screen' ) && get_current_screen()->post_type != psmt_get_gallery_post_type() ) {
			return;
		}

		wp_enqueue_script( 'psmt_uploader' );

		// load lightbox only on edit gallery page or not admin.
		if ( ! is_admin() ) {
			// only load the lightbox if it is enabled in the admin settings.
			if ( psmt_get_option( 'load_lightbox' ) ) {
				wp_enqueue_script( 'magnific-js' );
			}

			wp_enqueue_script( 'psmt_activity' );
		}

		wp_enqueue_script( 'psmt_core' );
		wp_enqueue_script( 'psmt_remote' );

		// we only need these to be loaded for activity page, should we put a condition here?
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
		// force wp to load _js template for the playlist and the code to.
		do_action( 'wp_playlist_scripts' ); // may not be a good idea.

		$this->default_settings();
		$this->plupload_localize();
		$this->localize_strings();
	}

	/**
	 * Default settings.
	 */
	public function default_settings() {
		global $wp_scripts;

		$data = $wp_scripts->get_data( 'psmt_uploader', 'data' );

		if ( $data && false !== strpos( $data, '_psmtUploadSettings' ) ) {
			return;
		}

		$max_upload_size = wp_max_upload_size();


		$defaults = array(
			'runtimes'            => 'html5,silverlight,flash,html4',
			'file_data_name'      => '_psmt_file', // key passed to $_FILE.
			'multiple_queues'     => true,
			'max_file_size'       => $max_upload_size . 'b',
			'url'                 => admin_url( 'admin-ajax.php' ),
			'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
			'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
			'filters'             => array(
				array(
					'title'      => __( 'Zulässige Dateien' ),
					'extensions' => '*',
				),
			),
			'multipart'           => true,
			'urlstream_upload'    => true,
		);

		// Multi-file uploading doesn't currently work in iOS Safari,
		// single-file allows the built-in camera to be used as source for images.
		if ( wp_is_mobile() ) {
			$defaults['multi_selection'] = false;
		}

		$defaults = apply_filters( 'psmt_upload_default_settings', $defaults );

		$params = array(
			'action'       => 'psmt_add_media',
			'_wpnonce'     => wp_create_nonce( 'psmt_add_media' ),
			'component'    => psmt_get_current_component(),
			'component_id' => psmt_get_current_component_id(),
			'context'      => 'gallery', // default context.
		);

		$params = apply_filters( 'psmt_plupload_default_params', $params );
		// $params['_wpnonce'] = wp_create_nonce( 'media-form' );
		$defaults['multipart_params'] = $params;

		$settings = array(
			'defaults'      => $defaults,
			'browser'       => array(
				'mobile'    => wp_is_mobile(),
				'supported' => _device_can_upload(),
			),
			'limitExceeded' => false, // always false, we have other ways to check this.
		);

		$script = 'var _psmtUploadSettings = ' . json_encode( $settings ) . ';';

		if ( $data ) {
			$script = "$data\n$script";
		}

		$wp_scripts->add_data( 'psmt_uploader', 'data', $script );
	}

	/**
	 * A copy from wp pluload localize.
	 */
	public function plupload_localize() {

		// error message for both plupload and swfupload.
		$uploader_l10n = array(
			'queue_limit_exceeded'      => __( 'Du hast versucht, zu viele Dateien in die Warteschlange zu stellen.' ),
			'file_exceeds_size_limit'   => __( '%s überschreitet die maximale Upload-Größe.' ),
			'zero_byte_file'            => __( 'Diese Datei ist leer. Bitte versuche es mit einer anderen.' ),
			'invalid_filetype'          => __( 'Dieser Dateityp ist nicht zulässig. Bitte versuche es mit einem anderen.' ),
			'not_an_image'              => __( 'Diese Datei ist kein Bild. Bitte versuche es mit einem anderen.' ),
			'image_memory_exceeded'     => __( 'Speicher überschritten. Bitte versuche es mit einer anderen kleineren Datei.' ),
			'image_dimensions_exceeded' => __( 'Dies ist größer als die maximale Größe. Bitte versuche es mit einem anderen.' ),
			'default_error'             => __( 'Beim Upload ist ein Fehler aufgetreten. Bitte versuche es später noch einmal.' ),
			'missing_upload_url'        => __( 'Es ist ein Konfigurationsfehler aufgetreten. Bitte wende Dich an den Serveradministrator.' ),
			'upload_limit_exceeded'     => __( 'Du darfst nur 1 Datei hochladen.' ),
			'http_error'                => __( 'HTTP Fehler.' ),
			'upload_failed'             => __( 'Hochladen fehlgeschlagen.' ),
			'big_upload_failed'         => __( 'Bitte versuche, diese Datei mit dem %1$sBrowser-Uploader%2$s hochzuladen.' ),
			'big_upload_queued'         => __( '%s überschreitet die maximale Upload-Größe für den Multi-File-Uploader, wenn er in Deinem Browser verwendet wird.' ),
			'io_error'                  => __( 'E/A-Fehler.' ),
			'security_error'            => __( 'Sicherheitsfehler.' ),
			'file_cancelled'            => __( 'Datei abgebrochen.' ),
			'upload_stopped'            => __( 'Hochladen gestoppt.' ),
			'dismiss'                   => __( 'Verwerfen' ),
			'crunching'                 => __( 'Knirschen&hellip;' ),
			'deleted'                   => __( 'in den Müll.' ),
			'error_uploading'           => __( '&#8220;%s&#8221; konnte nicht hochgeladen werden.' ),
		);

		wp_localize_script( 'psmt_uploader', 'pluploadL10n', $uploader_l10n );
	}

	/**
	 * Add extra js data.
	 */
	public function add_js_data() {

		$settings = array(
			'enable_activity_lightbox'              => psmt_get_option( 'enable_activity_lightbox' ) ? true : false,
			'enable_gallery_lightbox'               => psmt_get_option( 'enable_gallery_lightbox' ) ? true : false,
			'enable_lightbox_in_gallery_media_list' => psmt_get_option( 'enable_lightbox_in_gallery_media_list' ) ? true : false,
		);

		$active_types = psmt_get_active_types();

		$extensions            = $type_errors = array();
		$allowed_type_messages = array();
		foreach ( $active_types as $type => $object ) {
			$type_extensions = psmt_get_allowed_file_extensions_as_string( $type, ',' );

			$extensions[ $type ]            = array(
				'title'      => sprintf( 'Select %s', psmt_get_type_singular_name( $type ) ),
				'extensions' => $type_extensions,
			);
			$readable_extensions            = psmt_get_allowed_file_extensions_as_string( $type, ', ' );
			$type_errors[ $type ]            = sprintf( _x( 'Dieser Dateityp ist nicht zulässig. Zulässige Dateitypen sind: %s', 'type error message', 'psourcemediathek' ), $readable_extensions );
			$allowed_type_messages[ $type ] = sprintf( _x( ' Bitte wähle nur: %s', 'type error message', 'psourcemediathek' ), $readable_extensions );
		}

		$settings['types']                 = $extensions;
		$settings['type_errors']           = $type_errors;
		$settings['allowed_type_messages'] = $allowed_type_messages;
		$settings['max_allowed_file_size'] = sprintf( _x( 'Maximal zulässige Dateigröße: %s', 'maximum allowed file size info', 'psourcemediathek' ), size_format( wp_max_upload_size() ) );

		if ( psmt_is_single_gallery() ) {
			$settings['current_type'] = psmt_get_current_gallery()->type;
		}

		$settings['activity_disable_auto_file_browser'] = psmt_get_option( 'activity_disable_auto_file_browser', 0 );
		$settings['empty_url_message'] = __( 'Bitte gib eine URL an.', 'psourcemediathek' );

		$settings['loader_src'] = psmt_get_asset_url( 'assets/images/loader.gif', 'psmt-loader' );

		$disabled_types_as_keys = array();

		$disabled_types = psmt_get_option( 'lightbox_disabled_types', array() );

		if ( empty( $disabled_types ) ) {
			$disabled_types = array();
		}

		foreach ( $disabled_types as $type ) {
			$disabled_types_as_keys[ $type ] = 1;
		}

		$settings['lightboxDisabledTypes'] = $disabled_types_as_keys;

		$settings = apply_filters( 'psmt_localizable_data', $settings );

		wp_localize_script( 'psmt_core', '_psmtData', $settings );
		// _psmtData.
	}

	/**
	 * Localize strings for use at various places
	 */
	public function localize_strings() {

		$params = apply_filters( 'psmt_js_strings', array(
			'show_all'            => __( 'Zeige alles', 'psourcemediathek' ),
			'show_all_comments'   => __( 'Zeige alle Kommentare zu diesem Thread', 'psourcemediathek' ),
			'show_x_comments'     => __( 'Alle %d Kommentare anzeigen', 'psourcemediathek' ),
			'mark_as_fav'         => __( 'Favorit', 'psourcemediathek' ),
			'my_favs'             => __( 'Meine Favoriten', 'psourcemediathek' ),
			'remove_fav'          => __( 'Favorit entfernen', 'psourcemediathek' ),
			'view'                => __( 'Ansehen', 'psourcemediathek' ),
			'bulk_delete_warning' => _x( 'Durch das Löschen werden alle ausgewählten Medien und Dateien dauerhaft entfernt. Möchtest Du fortfahren?', 'bulk deleting warning message', 'psourcemediathek' ),
		) );
		wp_localize_script( 'psmt_core', '_psmtStrings', $params );
	}

	/**
	 * Load CSS on front end
	 */
	public function load_css() {

		wp_register_style( 'psmt-core-css', $this->url . 'assets/css/psmt-core.css' );
		wp_register_style( 'psmt-extra-css', $this->url . 'assets/css/psmt-pure/psmt-pure.css' );
		wp_register_style( 'magnific-css', $this->url . 'assets/vendors/magnific/magnific-popup.css' ); //
		// should we load the css everywhere or just on the gallery page
		// i am leaving it like this for now to avoid design issues on shortcode pages/widget
		// only load magnific css if the lightbox is enabled.
		if ( psmt_get_option( 'load_lightbox' ) ) {
			wp_enqueue_style( 'magnific-css' );
		}

		wp_enqueue_style( 'psmt-extra-css' );
		wp_enqueue_style( 'psmt-core-css' );
	}

	/**
	 * Simply injects the html which we later use for showing loaders
	 * The benefit of loading it into dom is that the images are preloaded and have better user experience
	 */
	public function footer() {
		?>
        <ul style="display: none;">
            <li id="psmt-loader-wrapper" style="display:none;" class="psmt-loader">
                <div id="psmt-loader"><img
                            src="<?php echo psmt_get_asset_url( 'assets/images/loader.gif', 'psmt-loader' ); ?>"/></div>
            </li>
        </ul>

        <div id="psmt-cover-uploading" style="display:none;" class="psmt-cover-uploading">
            <img src="<?php echo psmt_get_asset_url( 'assets/images/loader.gif', 'psmt-cover-loader' ); ?>"/>
        </div>


		<?php
	}

}

// initialize.
PSMT_Assets_Loader::get_instance(); //initialize

