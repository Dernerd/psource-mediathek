<?php
/**
 * Admin Single gallery edit helper.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single gallery admin edit page helper.
 */
class PSMT_Admin_Edit_Gallery_Panel {
	/**
	 * Tabs on the dit page.
	 *
	 * @var array
	 */
	private $tabs = array();

	/**
	 * Singleton instance.
	 *
	 * @var PSMT_Admin_Edit_Gallery_Panel
	 */
	private static $instance = null;

	/**
	 * Constructor
	 */
	private function __construct() {

	}

	/**
	 * Get singleton instance.
	 *
	 * @return PSMT_Admin_Edit_Gallery_Panel
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Render the tabs(in metabox)
	 */
	public function render() {
		?>
        <div id="psmt-admin-edit-panels">
			<?php $this->render_nav(); ?>
			<?php $this->render_panels(); ?>
			<?php wp_nonce_field( 'psmt-manage-gallery', '_psmt_manage_gallery_nonce' ); ?>
        </div>
		<?php
	}


	/**
	 * Add a panel
	 *
	 * @param array $args array of args for adding edit panel.
	 *
	 * @type string id unique panel id
	 * @type callable $callback used to display pane
	 * @type string $title title to display
	 *
	 * @return null
	 */
	public function add_panel( $args ) {

		if ( empty( $args['id'] ) || empty( $args['title'] ) || empty( $args['callback'] ) ) {
			return;
		}

		$this->tabs[ $args['id'] ] = $args;
	}

	/**
	 * Render nav
	 */
	private function render_nav() {
		$class = 'psmt-admin-edit-panel-tabs';
		?>
        <style type="text/css">

            .psmt-clearfix {
                *zoom: 1;
            }

            .psmt-clearfix:before,
            .psmt-clearfix:after {
                display: table;
                line-height: 0;
                content: "";
            }

            .psmt-clearfix:after {
                clear: both;
            }

            .psmt-admin-edit-panel {
                display: none;
            }

            .psmt-admin-active-panel {
                display: block;
            }

            .psmt-admin-edit-panel:first {
                display: block;
            }

            #psmt-admin-edit-panel-tabs-nav {
                border-bottom: 1px solid #cecece;
                list-style: outside none none;
                margin: 0 0 10px;
                padding: 0;
            }

            #psmt-admin-edit-panel-tabs-nav li {
                float: left;
                margin: 0 10px -1px 0;
            }

            #psmt-admin-edit-panel-tabs-nav li a {
                color: #aaa;
                display: block;
                font-size: 14px;
                font-weight: 300;
                outline: medium none;
                padding: 7px 10px 5px;
                text-decoration: none;
            }

            #psmt-admin-edit-panel-tabs-nav li.psmt-admin-edit-panel-tab-active a {
                -moz-border-bottom-colors: none;
                -moz-border-left-colors: none;
                -moz-border-right-colors: none;
                -moz-border-top-colors: none;
                border-color: #cecece #cecece #fff;
                border-image: none;
                border-radius: 3px 3px 0 0;
                border-style: solid;
                border-width: 1px;
                color: #21759b;
                padding-top: 6px;
            }

        </style>
        <ul id="psmt-admin-edit-panel-tabs-nav" class="psmt-clearfix">
			<?php foreach ( $this->tabs as $tab ) : ?>
                <li class="<?php echo $class; ?>"><a href="#psmt-admin-edit-panel-tab-<?php echo $tab['id']; ?>"
                                                     title="<?php echo $tab['title']; ?>"><?php echo $tab['title']; ?></a>
                </li>
			<?php endforeach; ?>
        </ul>


		<?php
	}

	private function render_panels() {

		?>
		<?php foreach ( $this->tabs as $tab ) : ?>
            <div id="psmt-admin-edit-panel-tab-<?php echo $tab['id']; ?>" class="psmt-admin-edit-panel psmt-clearfix">
				<?php do_action( 'psmt_admin_edit_panel_before_tab_' . $tab['id'] ); ?>

				<?php call_user_func( $tab['callback'] ); ?>

				<?php do_action( 'psmt_admin_edit_panel_after_tab_' . $tab['id'] ); ?>
            </div>

		<?php endforeach; ?>
		<?php
		$this->script();
	}

	/**
	 * Tabbable JavaScript codes
	 *
	 * This code uses localstorage for displaying active tabs
	 */
	public function script() {
		?>
        <script>
            jQuery(document).ready(function ($) {

                $('.psmt-admin-edit-panel:first').addClass('psmt-admin-active-panel');
                // Switches option sections.
                $('.psmt-admin-edit-panel').not('.psmt-admin-active-panel').hide();

                // always show the first tab
                // $('.psmt-admin-edit-panel:first').fadeIn();
                $('#psmt-admin-edit-panel-tabs-nav li:first').addClass('psmt-admin-edit-panel-tab-active');

                // on click of the tab navigation.
                $('#psmt-admin-edit-panel-tabs-nav a').click(function (evt) {
                    var $li = $(this).parent();
                    $('#psmt-admin-edit-panel-tabs-nav li').removeClass('psmt-admin-edit-panel-tab-active');

                    $li.addClass('psmt-admin-edit-panel-tab-active').blur();

                    var clicked_group = $(this).attr('href');
                    $('.psmt-admin-edit-panel').hide();
                    $('.psmt-admin-edit-panel').removeClass('psmt-admin-active-panel');
                    $(clicked_group).fadeIn();
                    $(clicked_group).addClass('psmt-admin-active-panel');

                    evt.preventDefault();
                });
            });
        </script>
		<?php
	}
}

/**
 * Function to easily access the edit panel.
 *
 * @return PSMT_Admin_Edit_Gallery_Panel
 */
function psmt_admin_edit_gallery_panel_helper() {
	return PSMT_Admin_Edit_Gallery_Panel::get_instance();
}
