<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Anything admin related here
 **/
class PSMT_Admin {

	/**
	 * Parent Menu slug.
	 *
	 * @var string
	 */
	private $menu_slug = '';

	/**
	 * Einstellungsseite instance.
	 *
	 * @var PSMT_Admin_Settings_Page
	 */
	private $page;

	/**
	 * Singleton instance.
	 *
	 * @var PSMT_Admin
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->menu_slug = 'edit.php?post_type=' . psmt_get_gallery_post_type();
	}

	/**
	 * Get the singleton instance.
	 *
	 * @return PSMT_Admin
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the parent slug for adding new admin menu items
	 *
	 * @return string
	 */
	public function get_menu_slug() {
		return $this->menu_slug;
	}

	/**
	 * Set the page object. It saves the reference to page.
	 *
	 * @param PSMT_Admin_Settings_Page $page page object.
	 */
	public function set_page( $page ) {
		$this->page = $page;
	}

	/**
	 * Get the page object.
	 *
	 * @return PSMT_Admin_Settings_Page
	 */
	public function get_page() {
		return $this->page;
	}
}

/**
 * Shortcut to access APP_Admin class.
 *
 * @return PSMT_Admin
 */
function psmt_admin() {
	return PSMT_Admin::get_instance();
}

/**
 * Handle admin Settings Screen
 */
class PSMT_Admin_Settings_Helper {

	/**
	 * Singleton instance.
	 *
	 * @var PSMT_Admin_Settings_Helper
	 */
	private static $instance = null;

	/**
	 * Page object.
	 *
	 * @var PSMT_Admin_Settings_Page
	 */
	private $page;

	/**
	 * Array of active media types.
	 *
	 * @var array
	 */
	private $active_types = array();

	/**
	 * Array of media type options.
	 *
	 * @var array
	 */
	private $type_options = array();

	/**
	 * Constructor.
	 */
	private function __construct() {

		add_action( 'admin_init', array( $this, 'update_notice_visibility' ) );
		add_action( 'admin_init', array( $this, 'reset_settings' ) );

		add_action( 'admin_init', array( $this, 'init' ) );

		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		// add_action( 'admin_enqueue_scripts', array( $this, 'load_js' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'load_css' ) );

	}

	/**
	 * Get the singleton instance.
	 *
	 * @return PSMT_Admin_Settings_Helper
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Reset PsourceMediathek settings.
	 */
	public function reset_settings() {
		// is it our action?
		if ( ! isset( $_POST['psmt-action-reset-settings'] ) ) {
			return;
		}

		// nonce verify?
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'psmt-action-reset-settings' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// store default settings if not already exists.
		update_option( 'psmt-settings', psmt_get_default_options() );
		delete_option( 'psmt_settings_saved' );
	}

	/**
	 * Add notice in admin bar.
	 */
	public function admin_notice() {
		// only if user can manage_option.
		if ( ! current_user_can( 'manage_options' ) || get_option( 'psmt_settings_saved' ) ) {
			return;
		}
		$link = add_query_arg( 'page', 'psmt-settings', psmt_admin()->get_menu_slug() );
		?>
        <div class="notice notice-success">
            <p><?php _ex( 'PsourceMediathek ist fast fertig. Bitte überprüfe und aktualisiere die Einstellungen (mindestens einmal speichern).', 'admin notice message', 'psourcemediathek' ); ?>
                <a href="<?php echo $link; ?>"
                   title="<?php _ex( 'Jetzt aktualisieren', 'admin notice action link title', 'psourcemediathek' ); ?>"><?php _ex( 'Tu es.', 'admin notice message' ); ?></a>
            </p>
        </div>
		<?php
	}

	/**
	 * Save if the admin notice was dismissed.
	 */
	public function update_notice_visibility() {
		if ( $this->is_settings_page() && isset( $_GET['settings-updated'] ) && current_user_can( 'manage_options' ) ) {
			update_option( 'psmt_settings_saved', 1, true );
		}
	}

	/**
	 * Build options for page rendering.
	 */
	private function build_options() {

		$this->active_types = psmt_get_active_types();

		foreach ( $this->active_types as $type => $object ) {
			$this->type_options[ $type ] = $object->label;
		}

	}

	/**
	 * Gte the types array for the component.
	 *
	 * @param string $component component name(groups|members etc).
	 *
	 * @return array
	 */
	private function get_type_options( $component = '' ) {
		return $this->type_options;
	}

	/**
	 * Check if it is settings page.
	 *
	 * @return bool
	 */
	private function is_settings_page() {

		global $pagenow;

		// we need to load on options.php otherwise settings won't be registered.
		if ( 'options.php' === $pagenow ) {
			return true;
		}

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'psmt-settings' && isset( $_GET['post_type'] ) && $_GET['post_type'] == psmt_get_gallery_post_type() ) {
			return true;
		}

		return false;
	}

	/**
	 * Initialize the admin settings panel and fields
	 */
	public function init() {

		if ( ! $this->is_settings_page() ) {
			return;
		}


		$this->build_options();

		if ( ! class_exists( 'PSMT_Admin_Settings_Page' ) ) {
			require_once psourcemediathek()->get_path() . 'admin/psmt-settings-manager/psmt-admin-settings-loader.php';
		}


		// 'psmt-settings' is used as page slug as well as option to store in the database.
		$page = new PSMT_Admin_Settings_Page( 'psmt-settings' );

		// Add a panel to to the admin.
		// A panel is a Tab and what comes under that tab.
		$panel = $page->add_panel( 'general', _x( 'Allgemein', 'Admin Einstellungen panel title', 'psourcemediathek' ) );

		// A panel can contain one or more sections. each sections can contain fields.
		$section = $panel->add_section( 'component-settings', _x( 'Komponenteneinstellungen', 'Abschnittstitel Admin-Einstellungen', 'psourcemediathek' ) );

		$components_details = array();
		$components         = psmt_get_registered_components();

		foreach ( $components as $key => $component ) {
			$components_details[ $key ] = $component->label;
		}

		$component_keys     = array_keys( $components_details );
		$default_components = array_combine( $component_keys, $component_keys );
		$active_components  = array_keys( psmt_get_active_components() );

		if ( ! empty( $active_components ) ) {
			$default_components = array_combine( $active_components, $active_components );
		}

		$section->add_field( array(
			'name'    => 'active_components',
			'label'   => _x( 'Galerien aktivieren für?', 'Admin Einstellungen', 'psourcemediathek' ),
			'type'    => 'multicheck',
			'options' => $components_details,
			'default' => $default_components,
		) );

		/**
		 * Status section.
		 */
		$registered_statuses = $available_media_stati = psmt_get_registered_statuses();

		$options = array();

		foreach ( $available_media_stati as $key => $available_media_status ) {
			$options[ $key ] = $available_media_status->get_label();
		}

		$panel->add_section( 'status-settings', _x( 'Privatsphäre Einstellungen', 'Abschnittstitel Admin-Einstellungen', 'psourcemediathek' ) )
		      ->add_field( array(
			      'name'    => 'default_status',
			      'label'   => _x( 'Standardstatus für Galerie/Medien', 'Admin Einstellungen', 'psourcemediathek' ),
			      'desc'    => _x( 'Wird verwendet, wenn wir den Status nicht vom Benutzer erhalten dürfen', 'Admin Einstellungen', 'psourcemediathek' ),
			      'default' => psmt_get_default_status(),
			      'options' => $options,
			      'type'    => 'select',
		      ) );

		$section = $panel->get_section( 'status-settings' );

		// $registered_statuses = psmt_get_registered_statuses();
		$status_info = array();

		foreach ( $registered_statuses as $key => $status ) {
			$status_info[ $key ] = $status->label;
		}

		$active_statuses  = array_keys( psmt_get_active_statuses() );
		$status_keys      = array_keys( $status_info );
		$default_statuses = array_combine( $status_keys, $status_keys );

		if ( ! empty( $active_statuses ) ) {
			$default_statuses = array_combine( $active_statuses, $active_statuses );
		}

		$section->add_field( array(
			'name'    => 'active_statuses',
			'label'   => _x( 'Aktivierte Medien-/Galeriestatus', 'Admin Einstellungen', 'psourcemediathek' ),
			'type'    => 'multicheck',
			'options' => $status_info,
			'default' => $default_statuses,
		) );

		// enabled type ?
		$section     = $panel->add_section( 'types-settings', _x( 'Medientypeinstellungen', 'Abschnittstitel Admin-Einstellungen', 'psourcemediathek' ) );
		$valid_types = psmt_get_registered_types();

		$options          = array();
		$types_info       = array();
		$extension_fields = array();

		foreach ( $valid_types as $type => $type_object ) {

			$types_info[ $type ] = $type_object->label;

			$extension_fields [] = array(
				'id'      => 'extensions-' . $type,
				'name'    => 'extensions',
				'label'   => sprintf( _x( 'Zulässige Erweiterungen für %s', 'Einstellungsseite', 'psourcemediathek' ), $type ),
				'desc'    => _x( 'Separate Dateierweiterungen durch Komma', 'Einstellungsseite', 'psourcemediathek ' ),
				'default' => join( ',', (array) $type_object->get_registered_extensions() ),
				'type'    => 'extensions',
				'extra'   => array( 'key' => $type, 'name' => 'extensions' ),
			);
		}

		$type_keys     = array_keys( $types_info );
		$default_types = array_combine( $type_keys, $type_keys );
		$active_types  = array_keys( $this->active_types );

		if ( ! empty( $active_types ) ) {
			$default_types = array_combine( $active_types, $active_types );
		}

		$section->add_field( array(
			'name'    => 'active_types',
			'label'   => _x( 'Aktivierte Medien-/Galerietypen', 'Einstellungsseite', 'psourcemediathek' ),
			'type'    => 'multicheck',
			'options' => $types_info,
			'default' => $default_types,
		) );

		$section->add_fields( $extension_fields );

		$section = $panel->add_section( 'sizes-settings', _x( 'Einstellungen Mediengröße', 'Abschnittstitel Admin-Einstellungen', 'psourcemediathek' ) );

		$defaults       = psmt_get_default_options();
		$size_thumbnail = $defaults['size_thumbnail'];

		$section->add_field( array(
			'name'    => 'size_thumbnail',
			'label'   => _x( 'Vorschaubild', 'Einstellungsseite', 'psourcemediathek' ),
			'type'    => 'media_size',
			'options' => array(
				'width'  => $size_thumbnail['width'],
				'height' => $size_thumbnail['height'],
				'crop'   => $size_thumbnail['crop'],
			),
			'desc'    => _x( 'Medien-Miniaturbildgröße. Wenn Zuschneiden aktiviert ist, wird das Foto auf die Größe zugeschnitten.', 'Hinweis Administratoreinstellungen', 'psourcemediathek' ),
		) );

		$size_mid = $defaults['size_mid'];
		$section->add_field( array(
			'name'    => 'size_mid',
			'label'   => _x( 'Mittel', 'Einstellungsseite', 'psourcemediathek' ),
			'type'    => 'media_size',
			'options' => array(
				'width'  => $size_mid['width'],
				'height' => $size_mid['height'],
				'crop'   => $size_mid['crop'],
			),
			'desc'    => _x( 'Medien mittlerer Größe. Wenn Zuschneiden aktiviert ist, wird das Foto auf die Größe zugeschnitten.', 'Hinweis Administratoreinstellungen', 'psourcemediathek' ),

		) );

		$size_large = $defaults['size_large'];
		$section->add_field( array(
			'name'    => 'size_large',
			'label'   => _x( 'Groß', 'Einstellungsseite', 'psourcemediathek' ),
			'type'    => 'media_size',
			'options' => array(
				'width'  => $size_large['width'],
				'height' => $size_large['height'],
				'crop'   => $size_large['crop'],
			),
			'desc'    => _x( 'Medien groß. Wenn Zuschneiden aktiviert ist, wird das Foto auf die Größe zugeschnitten.', 'Hinweis Administratoreinstellungen', 'psourcemediathek' ),

		) );

		$size_labels = array();
		$media_sizes = psmt_get_media_sizes();

		foreach ( $media_sizes as $name => $size ) {
			$size_labels[ $name ] = $size['label'];
		}

		$size_labels['original'] = _x( 'Original', 'Media size name', 'psourcemediathek' );

		$section->add_field( array(
			'name'    => 'single_media_size',
			'label'   => _x( 'Bildgröße für einzelne Medienseite.', 'Einstellungsseite', 'psourcemediathek' ),
			'type'    => 'select',
			'options' => $size_labels,
			'default' => $defaults['single_media_size'],
			'desc'    => _x( 'Wird zum Anzeigen der einzelnen Medien verwendet.', 'Hinweis Administratoreinstellungen', 'psourcemediathek' ),
		) );

		$section->add_field( array(
			'name'    => 'lightbox_media_size',
			'label'   => _x( 'Bildgröße für Lightbox.', 'Einstellungsseite', 'psourcemediathek' ),
			'type'    => 'select',
			'options' => $size_labels,
			'default' => $defaults['lightbox_media_size'],
			'desc'    => _x( 'Wird zum Anzeigen der Medien in der Lightbox verwendet.', 'Hinweis Administratoreinstellungen', 'psourcemediathek' ),
		) );

		// 4th section
		// enabled storage
		// Storage section
		$panel->add_section( 'storage-settings', _x( 'Speichereinstellungen', 'Einstellungsseite Abschnittsüberschrift', 'psourcemediathek' ) )
		      ->add_field( array(
			      'name'    => 'psmt_upload_space',
			      'label'   => _x( 'maximaler Upload-Speicherplatz pro Benutzer (MB)?', 'Admin Speichereinstellungen', 'psourcemediathek' ),
			      'type'    => 'text',
			      'default' => $defaults['psmt_upload_space'],

		      ) )
		      ->add_field( array(
			      'name'    => 'psmt_upload_space_groups',
			      'label'   => _x( 'maximaler Upload-Speicherplatz pro Gruppe (MB)?', 'Admin Speichereinstellungen', 'psourcemediathek' ),
			      'type'    => 'text',
			      'default' => $defaults['psmt_upload_space_groups'],

		      ) )
		      ->add_field( array(
			      'name'    => 'show_upload_quota',
			      'label'   => _x( 'Upload-Kontingent anzeigen?', 'Admin Speichereinstellungen', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'default' => $defaults['show_upload_quota'],
			      'options' => array(
				      1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
		      ) )
		      ->add_field( array(
			      'name'    => 'show_max_upload_file_size',
			      'label'   => _x( 'Maximale Upload-Dateigröße anzeigen?', 'Admin Speichereinstellungen', 'psourcemediathek' ),
			      'desc'    => _x( 'Wenn aktiviert, werden die Informationen zur maximalen Upload-Größe in der Upload-Dropzone angezeigt.', 'Admin Speichereinstellungen', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'default' => $defaults['show_max_upload_file_size'],
			      'options' => array(
				      1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
		      ) );

		$storage_methods        = psmt_get_registered_storage_managers();
		$storage_methods        = array_keys( $storage_methods );
		$storage_method_options = array();

		foreach ( $storage_methods as $storage_method ) {
			$storage_method_options[ $storage_method ] = ucfirst( $storage_method );
		}

		$panel->get_section( 'storage-settings' )->add_field( array(
			'name'    => 'default_storage',
			'label'   => _x( 'Welcher sollte als Standardspeicher markiert werden?', 'Admin Speichereinstellungen', 'psourcemediathek' ),
			'default' => psmt_get_default_storage_method(),
			'options' => $storage_method_options,
			'type'    => 'radio',
		) );

		// 5th section
		// remote Settings.
		// Storage section
		$panel->add_section( 'remote-settings', _x( 'Einstellungen zum Hinzufügen/Hochladen von Medien', 'Einstellungsseite Abschnittsüberschrift', 'psourcemediathek' ), _x( 'Steuere das Verhalten der Remote-Medien.', 'Beschreibung  Administratoreinstellungen', 'psourcemediathek' ) )
		      ->add_field( array(
			      'name'    => 'enable_file_upload',
			      'label'   => _x( 'Hochladen von Dateien aktivieren?', 'Admin Remote Einstellungen', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'options' => array(
				      1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
			      'default' => $defaults['enable_file_upload'],
			      'desc'    => _x( 'Wenn Nein, wird der lokale Datei-Upload deaktiviert.', 'Admin Remote Einstellungen', 'psourcemediathek' ),
		      ) )->add_field( array(
			      'name'    => 'enable_remote',
			      'label'   => _x( 'Hinzufügen von Medien über Link aktivieren?', 'Admin Remote Einstellungen', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'options' => array(
				      1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
			      'default' => $defaults['enable_remote'],
			      'desc'    => _x( 'Wenn Nein, werden die Funktionen für Remote-Dateien und Verknüpfungen vollständig deaktiviert.', 'Admin Remote Einstellungen', 'psourcemediathek' ),
		      ) )
		      ->add_field( array(
			      'name'    => 'enable_remote_file',
			      'label'   => _x( 'Aktiviere das Hinzufügen eines direkten Links zu Dateien von anderen Webseiten?', 'Admin Remote Einstellungen', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'options' => array(
				      1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
			      'default' => $defaults['enable_remote_file'],
			      'desc'    => _x( 'Der Benutzer wird über eine direkte URL zu Remote-Dateien hinzugefügt, z.B: http://example.com/hello.jpg', 'Admin Remote Einstellungen', 'psourcemediathek' ),
		      ) )
		      ->add_field( array(
			      'name'    => 'download_remote_file',
			      'label'   => _x( 'Datei auf Deinen Server herunterladen?', 'Admin Remote Einstellungen', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'options' => array(
				      1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
			      'default' => $defaults['download_remote_file'],
			      'desc'    => _x( 'Wenn ein Benutzer eine Remote-Datei hinzufügt, sollte diese automatisch auf Deinen Server heruntergeladen werden? Wir empfehlen dringend, es für Bilder zu aktivieren.', 'Admin Remote Einstellungen', 'psourcemediathek' ),
		      ) )
		      ->add_field( array(
			      'name'    => 'enable_oembed',
			      'label'   => _x( 'OEMBED-Unterstützung aktivieren?', 'Admin Speichereinstellungen', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'default' => $defaults['enable_oembed'],
			      'options' => array(
				      1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
			      'desc'    => _x( 'Ermögliche Benutzern das einfache Hinzufügen von Links (Videos, Fotos) von YouTube, Vimeo, Facebook usw..', 'Admin Remote Einstellungen', 'psourcemediathek' ),
		      ) );


		$panel->add_section( 'general-misc-settings', _x( 'Entwickler-Tools', 'Einstellungsseite Abschnittsüberschrift', 'psourcemediathek' ) )
		      ->add_field( array(
			      'name'    => 'enable_debug',
			      'label'   => _x( 'Debug-Info aktivieren?', 'Admin Speichereinstellungen', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'default' => $defaults['enable_debug'],
			      'options' => array(
				      1 => __( 'Ja', 'psourcemediathek' ),
				      0 => __( 'Nein', 'psourcemediathek' ),
			      ),
		      ) );
		// 5th section
		$this->add_sitewide_panel( $page );
		$this->add_buddypress_panel( $page );
		$this->add_members_panel( $page );
		$this->add_groups_panel( $page );

		$theme_panel = $page->add_panel( 'theming', _x( 'Darstellung', 'Titel der Registerkarte Themenbereich', 'psourcemediathek' ) );
		$theme_panel->add_section( 'display-settings', _x( 'Bildschirmeinstellungen ', 'Admin Design Einstellungen Abschnittsüberschrift', 'psourcemediathek' ) )
		            ->add_field( array(
			            'name'    => 'galleries_per_page',
			            'label'   => _x( 'Wie viele Galerien pro Seite?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'type'    => 'text',
			            'default' => $defaults['galleries_per_page'],
		            ) )
		            ->add_field( array(
			            'name'    => 'media_per_page',
			            'label'   => _x( 'Wie viele Medien pro Seite?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'type'    => 'text',
			            'default' => $defaults['media_per_page'],
		            ) )
		            ->add_field( array(
			            'name'    => 'media_columns',
			            'label'   => _x( 'Wie viele Medien pro Zeile?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'type'    => 'text',
			            'default' => $defaults['media_columns'],
		            ) )
		            ->add_field( array(
			            'name'    => 'gallery_columns',
			            'label'   => _x( 'Wie viele Galerien pro Reihe?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'type'    => 'text',
			            'default' => $defaults['gallery_columns'],
		            ) )
		            ->add_field( array(
			            'name'    => 'show_gallery_description',
			            'label'   => _x( 'Galeriebeschreibung auf einzelnen Galerieseiten anzeigen?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'desc'    => _x( 'Soll die Beschreibung für die Galerie über der Medienliste angezeigt werden?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'default' => $defaults['show_gallery_description'],
			            'type'    => 'radio',
			            'options' => array(
				            1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				            0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			            ),
		            ) )
		            ->add_field( array(
			            'name'    => 'show_media_description',
			            'label'   => _x( 'Medienbeschreibung auf einzelnen Medienseiten anzeigen?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'desc'    => _x( 'Soll die Beschreibung für Medien unter den Medien angezeigt werden?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'default' => $defaults['show_media_description'],
			            'type'    => 'radio',
			            'options' => array(
				            1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				            0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			            ),
		            ) );

		$theme_panel->add_section( 'audio-video', _x( 'Audio/Video-spezifische Einstellungen', ' Admin Design Abschnittsüberschrift', 'psourcemediathek' ) )
		            ->add_field( array(
			            'name'    => 'enable_audio_playlist',
			            'label'   => _x( 'Audio-Wiedergabeliste aktivieren?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'desc'    => _x( 'Sollte eine Audiogalerie als Wiedergabeliste aufgeführt werden?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'default' => $defaults['enable_audio_playlist'],
			            'type'    => 'radio',
			            'options' => array(
				            1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				            0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			            ),
		            ) )
		            ->add_field( array(
			            'name'    => 'enable_video_playlist',
			            'label'   => _x( 'Video-Wiedergabeliste aktivieren?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'desc'    => _x( 'Sollte eine Videogalerie als Wiedergabeliste aufgeführt werden?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'default' => $defaults['enable_video_playlist'],
			            'type'    => 'radio',
			            'options' => array(
				            1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				            0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			            ),
		            ) )
		            ->add_field( array(
			            'name'    => 'gdoc_viewer_enabled',
			            'label'   => _x( 'Google Doc Viewer verwenden?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'desc'    => _x( 'Möchtest Du Google Doc Viewer zum Anzeigen von Dokumenten verwenden?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'default' => $defaults['gdoc_viewer_enabled'],
			            'type'    => 'radio',
			            'options' => array(
				            1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				            0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			            ),
		            ) );


		$theme_panel->add_section( 'comments', _x( 'Kommentareinstellungen', 'Admin Design Abschnittsüberschrift', 'psourcemediathek' ) )
		            ->add_field( array(
			            'name'    => 'enable_media_comment',
			            'label'   => _x( 'Kommentieren einzelner Medien aktivieren?', 'Admin Design Kommentareinstellungen', 'psourcemediathek' ),
			            'default' => $defaults['enable_media_comment'],
			            'type'    => 'radio',
			            'options' => array(
				            1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				            0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			            ),
		            ) )
		            ->add_field( array(
			            'name'    => 'enable_gallery_comment',
			            'label'   => _x( 'Kommentieren einzelner Galerie aktivieren?', 'Admin Design Kommentareinstellungen', 'psourcemediathek' ),
			            'default' => $defaults['enable_gallery_comment'],
			            'type'    => 'radio',
			            'options' => array(
				            1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				            0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			            ),
		            ) );

		$theme_panel->add_section( 'lightbox', _x( 'Lightbox Einstellungen', 'Admin Design Abschnittsüberschrift', 'psourcemediathek' ) )
		            ->add_field( array(
			            'name'    => 'load_lightbox',
			            'label'   => _x( 'Lightbox Javascript & CSS laden?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'desc'    => _x( 'Sollen wir das mitgelieferte Lightbox-Skript laden? Stelle Nein ein, wenn Du keine LLightbox verwenden oder Deine eigene verwenden möchtest', 'Admin Einstellungen', 'psourcemediathek' ),
			            'default' => $defaults['load_lightbox'],
			            'type'    => 'radio',
			            'options' => array(
				            1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				            0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			            ),
		            ) )
		            ->add_field( array(
			            'name'    => 'lightbox_media_only',
			            'label'   => _x( 'Kommentare in Lightbox nicht anzeigen', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'desc'    => _x( 'Kommentare werden standardmäßig angezeigt.', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'default' => $defaults['enable_gallery_lightbox'],
			            'type'    => 'radio',
			            'options' => array(
				            1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				            0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			            ),
		            ) )
		            ->add_field( array(
			            'name'    => 'enable_activity_lightbox',
			            'label'   => _x( 'Aktivitätsmedien in Lightbox öffnen?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'desc'    => _x( 'Wenn Du Ja festlegst, werden die Fotos usw. in der Lightbox auf dem Aktivitätsbildschirm geöffnet.', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'default' => $defaults['enable_activity_lightbox'],
			            'type'    => 'radio',
			            'options' => array(
				            1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				            0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			            ),
		            ) )
		            ->add_field( array(
			            'name'    => 'enable_gallery_lightbox',
			            'label'   => _x( 'Fotos in Lightbox öffnen, wenn auf Galerie geklickt wird?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'desc'    => _x( 'If you set yes, the photos will be opened in lightbox when a gallery cover is clicked.', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'default' => $defaults['enable_gallery_lightbox'],
			            'type'    => 'radio',
			            'options' => array(
				            1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				            0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			            ),
		            ) )
		            ->add_field( array(
			            'name'    => 'enable_lightbox_in_gallery_media_list',
			            'label'   => _x( 'Fotos in Lightbox öffnen, wenn auf ein Foto in der Galerie geklickt wird?', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'desc'    => _x( 'Wenn Du "Ja" festlegst, werden die Fotos in der Lightbox geöffnet, wenn auf ein Foto in der Galerie geklickt wird.', 'Admin Design Einstellungen', 'psourcemediathek' ),
			            'default' => $defaults['enable_lightbox_in_gallery_media_list'],
			            'type'    => 'radio',
			            'options' => array(
				            1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				            0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			            ),
		            ) )
                    ->add_field( array(
                        'name'    => 'lightbox_disabled_types',
                        'label'   => _x( 'Lightbox für diese Typen deaktivieren?', 'Admin Design Einstellungen', 'psourcemediathek' ),
                        'desc'    => _x( 'Wenn Lightbox aktiviert ist, kannst Du sie für bestimmte Typen speziell deaktivieren.', 'Admin Design Einstellungen', 'psourcemediathek' ),
                        'default' => $defaults['lightbox_disabled_types'],
                        'type'    => 'multicheck',
                        'options' => $types_info,
                    ) );

		// add an empty addons panel to allow plugins to register any setting here
		// though a plugin can add a new panel, smaller plugins should use this panel instead.
		$page->add_panel( 'addons', _x( 'Addons', 'Admin Einstellungen Addons panel tab title', 'psourcemediathek' ), _x( 'PsourceMediathek Addon Einstellungen', 'Addons panel description', 'psourcemediathek' ) );

		// auto posting to activity on gallery upload?
		// should post after the whole gallery is uploaded or just after each media?
		$this->page = $page;

		psmt_admin()->set_page( $this->page );

		do_action( 'psmt_admin_register_settings', $page );
		// initialize settings.
		$page->init();

	}

	/**
	 * Add settings panel for site gallery.
	 *
	 * @param PSMT_Admin_Settings_Page $page page object.
	 */
	private function add_sitewide_panel( $page ) {

		if ( ! psmt_is_active_component( 'sitewide' ) ) {
			return;
		}

		$defaults = psmt_get_default_options();

		$sitewide_panel = $page->add_panel( 'sitewide', _x( 'Webseitenweite Galerie', 'Admin Seitenweite Galerieeinstellungen Titel Registerkarte', 'psourcemediathek' ) );

		$sitewide_panel->add_section( 'sitewide-general', _x( 'Allgemeine Einstellungen ', 'Admin Seitenweite Galerieeinstellungen Abschnittsüberschrift', 'psourcemediathek' ) )
		               ->add_field( array(
			               'name'    => 'enable_gallery_archive',
			               'label'   => _x( 'Galerie-Archiv aktivieren?', 'Admin Seitenweite Galerieeinstellungen', 'psourcemediathek' ),
			               'desc'    => _x( 'Wenn Du diese Option aktivierst, kannst Du alle Galerien auf einer einzigen Seite anzeigen (Archivseite).', 'Admin Seitenweite Galerieeinstellungen', 'psourcemediathek' ),
			               'default' => $defaults['enable_gallery_archive'],
			               'type'    => 'radio',
			               'options' => array(
				               1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				               0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			               ),
		               ) )
		               ->add_field( array(
			               'name'    => 'gallery_archive_slug',
			               'label'   => _x( 'Galerie-Archiv Slug', 'Admin Seitenweite Galerieeinstellungen', 'psourcemediathek' ),
			               'desc'    => _x( 'Bitte wähle einen Slug, die Teil des Permalinks des Galeriearchivs wird, z.B: http://yoursite.com/{slug}. Keine Leerzeichen, nur Kleinbuchstaben.', 'Admin Seitenweite Galerieeinstellungen', 'psourcemediathek' ),
			               'default' => $defaults['gallery_archive_slug'],
			               'type'    => 'text',
		               ) )
		               ->add_field( array(
			               'name'    => 'gallery_permalink_slug',
			               'label'   => _x( 'Galerie Permalink Slug', 'Admin Seitenweite Galerieeinstellungen', 'psourcemediathek' ),
			               'desc'    => _x( 'Bitte wähle einen Slug, die Teil des Galerie-Permalinks wird, z.B: http://yoursite.com/{slug}/gallery-name. Keine Leerzeichen, nur Kleinbuchstaben.', 'Admin Seitenweite Galerieeinstellungen', 'psourcemediathek' ),
			               'default' => $defaults['gallery_permalink_slug'],
			               'type'    => 'text',
		               ) );

		$this->add_type_settings( $sitewide_panel, 'sitewide' );
		$this->add_gallery_views_panel( $sitewide_panel, 'sitewide' );
	}

	/**
	 * Add type settings to the panel depending on the component.
	 *
	 * @param PSMT_Admin_Settings_Panel $panel panel object.
	 * @param string                   $component component.
	 */
	private function add_type_settings( $panel, $component ) {

		// Get active types and allow admins to support types for components.
		$options      = array();
		$active_types = $this->active_types;

		foreach ( $active_types as $type => $type_object ) {
			$options[ $type ] = $type_object->label;
		}

		$type_keys     = array_keys( $active_types );
		$default_types = array_combine( $type_keys, $type_keys );

		$key                    = $component . '_active_types';
		$active_component_types = psmt_get_option( $key );

		if ( ! empty( $active_component_types ) ) {
			$default_types = array_combine( $active_component_types, $active_component_types );
		}

		$panel->add_section( $component . '-types', _x( 'Typeneinstellungen ', 'Admin Einstellungen Seitenweite Galerie Abschnittsüberschrift', 'psourcemediathek' ) )
		      ->add_field( array(
			      'name'    => $key,
			      'label'   => _x( 'Aktivierte Medien-/Galerietypen', 'Einstellungsseite', 'psourcemediathek' ),
			      'type'    => 'multicheck',
			      'options' => $options,
			      'default' => $default_types,
		      ) );
	}

	/**
	 * Add settings panel for BuddyPress section.
	 *
	 * @param PSMT_Admin_Settings_Page $page page object.
	 */
	private function add_buddypress_panel( $page ) {

		if ( ! psourcemediathek()->is_bp_active() || ! ( psmt_is_active_component( 'members' ) || psmt_is_active_component( 'groups' ) ) ) {
			return;
		}

		$defaults = psmt_get_default_options();
		$panel    = $page->add_panel( 'buddypress', _x( 'BuddyPress', 'Admin Einstellungen BuddyPress panel tab title', 'psourcemediathek' ) );

		// directory settings.
		$panel->add_section( 'directory-settings', _x( 'Verzeichniseinstellungen', 'Abschnittstitel Admin-Einstellungen', 'psourcemediathek' ) )
		      ->add_field( array(
			      'name'    => 'has_gallery_directory',
			      'label'   => _x( 'Galerieverzeichnis aktivieren?', 'Admin Einstellungen', 'psourcemediathek' ),
			      'desc'    => _x( 'Eine Seite erstellen, um alle Galerien aufzulisten?', 'Admin Einstellungen', 'psourcemediathek' ),
			      'default' => $defaults['has_gallery_directory'],
			      'type'    => 'radio',
			      'options' => array(
				      1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
		      ) );

		// activity settings.
		$activity_section = $panel->add_section( 'activity-settings', _x( 'Aktivitätseinstellungen', 'Abschnittstitel Admin-Einstellungen', 'psourcemediathek' ) );

		$activity_section->add_field( array(
			'name'    => 'activity_upload',
			'label'   => _x( 'Aktivitäts-Upload zulassen?', 'Admin Einstellungen', 'psourcemediathek' ),
			'desc'    => _x( 'Benutzern das Hochladen vom Aktivitätsbildschirm erlauben?', 'Admin Einstellungen', 'psourcemediathek' ),
			'default' => $defaults['activity_upload'],
			'type'    => 'radio',
			'options' => array(
				1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			),
		) )->add_field( array(
			'name'    => 'activity_disable_auto_file_browser',
			'label'   => _x( 'Automatisches Öffnen der Dateiauswahl deaktivieren?', 'Admin Einstellungen', 'psourcemediathek' ),
			'desc'    => _x( 'Deaktiviert das automatische Öffnen der Dateiauswahl auf dem Mediensymbol. Klicke auf das Aktivitätsbeitragsformular.', 'Admin Einstellungen', 'psourcemediathek' ),
			'default' => $defaults['activity_disable_auto_file_browser'],
			'type'    => 'radio',
			'options' => array(
				1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			),
		) );

		$activity_options = array(
			'create_gallery' => _x( 'Neue Galerie wurde erstellt.', 'Admin Einstellungen', 'psourcemediathek' ),
			'add_media'      => _x( 'Neue Medien hinzugefügt/hochgeladen.', 'Admin Einstellungen', 'psourcemediathek' ),
		);

		$default_activities = $defaults['autopublish_activities'];

		if ( ! empty( $default_activities ) ) {
			$default_activities = array_combine( $default_activities, $default_activities );
		}

		$activity_section->add_field( array(
			'name'    => 'autopublish_activities',
			'label'   => _x( 'Automatisch in Aktivität veröffentlichen Wann?', 'Admin Einstellungen', 'psourcemediathek' ),
			'type'    => 'multicheck',
			'options' => $activity_options,
			'default' => $default_activities,
		) );

		$this->add_activity_views_panel( $panel );

		$panel->add_section( 'misc-settings', _x( 'Verschiedene Einstellungen', 'Abschnittstitel Admin-Einstellungen', 'psourcemediathek' ) )
		      ->add_field( array(
			      'name'    => 'show_orphaned_media',
			      'label'   => _x( 'Verwaiste Medien dem Benutzer anzeigen?', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      'desc'    => _x( 'Möchtest Du die Medien auflisten, wenn sie von einer Aktivität hochgeladen wurden, die Aktivität jedoch nicht veröffentlicht wurde?', 'Admin Einstellungen', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'default' => $defaults['show_orphaned_media'],
			      'options' => array(
				      1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
		      ) )
		      ->add_field( array(
			      'name'    => 'delete_orphaned_media',
			      'label'   => _x( 'Verwaiste Medien automatisch löschen?', 'Admin Einstellungen', 'psourcemediathek' ),
			      'desc'    => _x( 'Möchtest Du die verwaisten Medien löschen, die in der Aktivität hochgeladen wurden?', 'Admin Einstellungen', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'default' => $defaults['delete_orphaned_media'],
			      'options' => array(
				      1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
		      ) )
		      ->add_field( array(
			      'name'    => 'activity_media_display_limit',
			      'label'   => _x( 'Maximale Anzahl von Medien, die in der Aktivität angezeigt werden sollen?', 'Admin Einstellungen', 'psourcemediathek' ),
			      'desc'    => _x( 'Begrenze die Anzahl von Medien, die als an Aktivität gebunden angezeigt werden.', 'Admin Einstellungen', 'psourcemediathek' ),
			      'type'    => 'text',
			      'default' => $defaults['activity_media_display_limit'],
		      ) );
	}

	/**
	 * Add settings for Members Gallery.
	 *
	 * @param PSMT_Admin_Settings_Page $page page object.
	 */
	private function add_members_panel( $page ) {

		if ( ! psourcemediathek()->is_bp_active() || ! psmt_is_active_component( 'members' ) ) {
			return;
		}

		$defaults = psmt_get_default_options();

		$panel = $page->add_panel( 'members', _x( 'Mitgliedergalerie', 'Admin Einstellungen BuddyPress panel tab title', 'psourcemediathek' ) );
		$this->add_type_settings( $panel, 'members' );

		$section = $panel->get_section( 'members-types' );

		if ( $section ) {

			$section->add_field( array(
				'name'    => 'members_enable_type_filters',
				'label'   => _x( 'Galerie-Typ-Filter im Profil aktivieren?', 'Admin Einstellungen group section', 'psourcemediathek' ),
				'type'    => 'radio',
				'default' => $defaults['members_enable_type_filters'],
				'options' => array(
					1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
					0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				),
			) );
		}

		$this->add_gallery_views_panel( $panel, 'members' );
	}

	/**
	 * Add settings panel for BuddyPress Groups.
	 *
	 * @param PSMT_Admin_Settings_Page $page page object.
	 */
	private function add_groups_panel( $page ) {

		if ( ! psourcemediathek()->is_bp_active() || ! psmt_is_active_component( 'groups' ) ) {
			return;
		}

		$defaults = psmt_get_default_options();

		$panel = $page->add_panel( 'groups', _x( 'Gruppen Galerie', 'Admin Einstellungen BuddyPress panel tab title', 'psourcemediathek' ) );

		$this->add_type_settings( $panel, 'groups' );
		$this->add_gallery_views_panel( $panel, 'groups' );

		$panel->add_section( 'group-settings', _x( 'Gruppeneinstellungen', 'Abschnittstitel Admin-Einstellungen', 'psourcemediathek' ) )
		      ->add_field( array(
			      'name'    => 'enable_group_galleries_default',
			      'label'   => _x( 'Gruppengalerien standardmäßig aktivieren?', 'Admin Einstellungen group section', 'psourcemediathek' ),
			      'desc'    => _x( 'Wenn Du Ja festlegst, sind Gruppengalerien standardmäßig für alle Gruppen aktiviert. Ein Gruppenadministrator kann sie jedoch durch Aufrufen der Einstellungen ausschalten.', 'Admin Einstellungen group section', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'default' => $defaults['enable_group_galleries_default'],
			      'options' => array(
				      'yes' => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      'no'  => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
		      ) )
		      ->add_field( array(
			      'name'    => 'contributors_can_edit',
			      'label'   => _x( 'Teilnehmer können ihre eigenen Medien bearbeiten?', 'Admin Einstellungen group section', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'default' => $defaults['contributors_can_edit'],
			      'options' => array(
				      1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
		      ) )
		      ->add_field( array(
			      'name'    => 'contributors_can_delete',
			      'label'   => _x( 'Teilnehmer können ihre eigenen Medien löschen?', 'Admin Einstellungen group section', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'default' => $defaults['contributors_can_delete'],
			      'options' => array(
				      1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
		      ) )
		      ->add_field( array(
			      'name'    => 'groups_enable_my_galleries',
			      'label'   => _x( 'Meine Galerien Gruppenmitgliedern zeigen?', 'Admin Einstellungen group section', 'psourcemediathek' ),
			      'desc'    => _x( 'Auf Gruppenseiten wird eine Registerkarte mit dem Namen "Meine Galerie" hinzugefügt, auf der der angemeldete Benutzer die in dieser Gruppe erstellten Galerien sehen kann.', 'Admin Einstellungen group section', 'psourcemediathek' ),
			      'type'    => 'radio',
			      'default' => $defaults['groups_enable_my_galleries'],
			      'options' => array(
				      1 => _x( 'Ja', 'Admin-Einstellungsoption', 'psourcemediathek' ),
				      0 => _x( 'Nein', 'Admin-Einstellungsoption', 'psourcemediathek' ),
			      ),
		      ) );
	}


	/**
	 * Add Themes setting panel.
	 *
	 * @param PSMT_Admin_Settings_Panel $panel panel object.
	 * @param string                   $component component name.
	 */
	private function add_gallery_views_panel( $panel, $component ) {

		$active_types = $this->active_types;

		$section = $panel->add_section( $component . '-gallery-views', sprintf( _x( ' %s Standardansichten der Galerie', 'Gallery view Abschnittsüberschrift', 'psourcemediathek' ), ucwords( $component ) ) );

		$supported_types = psmt_component_get_supported_types( $component );

		foreach ( $active_types as $key => $type_object ) {
			// if the component does not support type, do not add the settings.
			if ( ! empty( $supported_types ) && ! psmt_component_supports_type( $component, $key ) ) {
				continue;
				// if none of the types are enabled, it means, it is the first time and we need not break here.
			}

			$registered_views = psmt_get_registered_gallery_views( $key );
			$options          = array();

			foreach ( $registered_views as $view ) {

				if ( ! $view->supports_component( $component ) || ! $view->supports( 'gallery' ) ) {
					continue;
				}

				$options[ $view->get_id() ] = $view->get_name();
			}

			$section->add_field( array(
				'name'    => $component . '_' . $key . '_gallery_default_view',
				'label'   => sprintf( _x( '%s Galerie', 'admin gallery  settings', 'psourcemediathek' ), psmt_get_type_singular_name( $key ) ),
				'desc'    => _x( 'Wird als Standardansicht verwendet. Es kann pro Galerie überschrieben werden', 'admin gallery settings', 'psourcemediathek' ),
				'default' => 'default',
				'type'    => 'radio',
				'options' => $options,
			) );
		}
	}

	/**
	 * Add activity view settings.
	 *
	 * @param PSMT_Admin_Settings_Panel $panel panel object.
	 */
	private function add_activity_views_panel( $panel ) {

		$active_types = $this->active_types;

		$section = $panel->add_section( 'activity-gallery-views', _x( 'Ansicht Aktivitätsmedienliste', 'Activity view Abschnittsüberschrift', 'psourcemediathek' ) );

		foreach ( $active_types as $key => $type_object ) {

			$registered_views = psmt_get_registered_gallery_views( $key );
			$options          = array();

			foreach ( $registered_views as $view ) {

				if ( ! $view->supports( 'activity' ) ) {
					continue;
				}

				$options[ $view->get_id() ] = $view->get_name();
			}

			$section->add_field( array(
				'name'    => 'activity_' . $key . '_default_view',
				'label'   => sprintf( _x( '%s Liste', 'admin gallery settings', 'psourcemediathek' ), psmt_get_type_singular_name( $key ) ),
				'desc'    => _x( 'Wird verwendet, um angehängte Aktivitätsmedien anzuzeigen.', 'admin gallery settings', 'psourcemediathek' ),
				'default' => 'default',
				'type'    => 'radio',
				'options' => $options,
			) );
		}
	}

	/**
	 * Add Menu
	 */
	public function add_menu() {

		add_submenu_page( psmt_admin()->get_menu_slug(), _x( 'Einstellungen', 'Admin Einstellungen page title', 'psourcemediathek' ), _x( 'Einstellungen', 'Admin Einstellungen menu label', 'psourcemediathek' ), 'manage_options', 'psmt-settings', array(
			$this,
			'render',
		) );

	}

	/**
	 * Show/render the setting page
	 */
	public function render() {
		$this->page->render();
	}
}

// instantiate.
PSMT_Admin_Settings_Helper::get_instance();
