<?php
/**
 * @package   ldd_directory_lite
 * @author    LDD Web Design <info@lddwebdesign.com>
 * @license   GPL-2.0+
 * @link      http://lddwebdesign.com
 * @copyright 2014 LDD Consulting, Inc
 *
 * @wordpress-plugin
 * Plugin Name:       LDD Directory Lite
 * Plugin URI:        http://wordpress.org/plugins/ldd-directory-lite
 * Description:       Powerful yet simple to use, easily add a business directory to your WordPress site.
 * Version:           2.0.0-working
 * Author:            LDD Web Design
 * Author URI:        http://www.lddwebdesign.com
 * Text Domain:       ldd-lite
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'WPINC' ) ) die;


define( 'LDDLITE_VERSION',      '2.0.0-working' );

define( 'LDDLITE_PATH',         WP_PLUGIN_DIR.'/'.basename( dirname( __FILE__ ) ) );
define( 'LDDLITE_URL',          plugins_url().'/'.basename( dirname( __FILE__ ) ) );

define( 'LDDLITE_POST_TYPE',    'directory_listings' );
define( 'LDDLITE_TAX_CAT',      'listing_category' );
define( 'LDDLITE_TAX_TAG',      'listing_tag' );

define( 'LDDLITE_PFX',          '_lddlite_' );


register_activation_hook( __FILE__, array( 'LDD_Directory_Lite', 'flush_rewrite' ) );
register_deactivation_hook( __FILE__, array( 'LDD_Directory_Lite', 'flush_rewrite' ) );

ini_set( 'display_errors', 1 );
error_reporting(-1);

class LDD_Directory_Lite {

    /**
     * @var $_instance An instance of ones own instance
     */
    private static $_instance;

    /**
     * @var string Slug for our text domain and other similar uses
     */
    private $_slug = 'ldd-lite';

    /**
     * @var array Options, everybody has them.
     */
    public $options = array();

    /**
     * @var
     */
    public $listing;


    /**
     * Singleton pattern, returns an instance of the class responsible for setting up the plugin
     * and lording over it's configuration options.
     *
     * @since 2.0.0
     * @return LDD_Directory_Lite An instance of the LDD_Directory_Lite class
     */
    public static function get_in() {

        if ( !isset( self::$_instance ) && !( self::$_instance instanceof LDD_Directory_Lite ) ) {
            self::$_instance = new self;
            self::$_instance->include_files();
            self::$_instance->populate_options();
            self::$_instance->setup_plugin();
        }

        return self::$_instance;

    }


    /**
     * Include all the files we'll need to function
     *
     * @since 2.0.0
     */
    public function include_files() {
        require_once( LDDLITE_PATH . '/includes/post-types.php' );
        require_once( LDDLITE_PATH . '/includes/setup.php' );
        require_once( LDDLITE_PATH . '/includes/functions.php' );
        require_once( LDDLITE_PATH . '/includes/email.php' );
        if ( is_admin() ) {
            require_once( LDDLITE_PATH . '/includes/admin/metaboxes.php' );
            require_once( LDDLITE_PATH . '/includes/admin/pointers.php' );
            require_once( LDDLITE_PATH . '/includes/admin/settings.php' );
            require_once( LDDLITE_PATH . '/includes/admin/help.php' );
        }
    }


    /**
     * Populate the options property based on a set of defaults and information pulled from
     * the database. This will also check for and fire an upgrade if necessary.
     *
     * @since 2.0.0
     */
    public function populate_options() {

        $settings = wp_parse_args(
            get_option( 'lddlite_settings' ),
            ldl_get_default_settings() );

        $version = get_option( 'lddlite_version' );

//        require_once( LDDLITE_PATH . '/uninstall.php' );

        if ( !$version ) {
            $dir = dirname( __FILE__ );
            $old_plugin = substr( $dir, 0, strrpos( $dir, '/' ) ) . '/ldd-business-directory/lddbd_core.php';
            if ( file_exists( $old_plugin ) ) {
                require_once( LDDLITE_PATH . '/upgrade.php' );
                add_action( 'init', 'ldl_upgrade', 20 ); // This has to fire later, so we know our CPT's are registered
                add_action( 'admin_init', 'ldl_disable_old' );
            }
        }

        $this->settings = $settings;
        $this->version = $version;

    }


    /**
     * Minor setup. Major setup of internal funtionality is handled in setup.php
     *
     * @since 2.0.0
     */
    public function setup_plugin() {
        add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

        $basename = plugin_basename( __FILE__ );
        add_filter( 'plugin_action_links_' . $basename, array( $this, 'add_action_links' ) );
    }


    /**
     * Load the related i18n files into the appropriate domain.
     *
     * @since 2.0.0
     */
    public function load_plugin_textdomain() {

        $lang_dir = LDDLITE_PATH . '/languages/';
        $lang_dir = apply_filters( 'lddlite_languages_directory', $lang_dir );

        $locale = apply_filters( 'plugin_locale', get_locale(), $this->_slug );
        $mofile = $lang_dir . $this->_slug . $locale . '.mo';

        if ( file_exists( $mofile ) )
            load_textdomain( $this->_slug, $mofile );
        else
            load_plugin_textdomain( $this->_slug, false, $lang_dir );

    }


    /**
     * Add a 'Settings' link on the Plugins page for easier access.
     *
     * @since 2.0.0
     * @param $links array Passed by the filter
     * @return array The modified $links array
     */
    public function add_action_links( $links ) {

        return array_merge(
            array(
                'settings' => '<a href="' . admin_url( 'options-writing.php' ) . '">' . __( 'Settings', 'wp-bitly' ) . '</a>'
            ),
            $links
        );

    }


    /**
     * Flush rewrite rules on activation or deactivation of the plugin.
     *
     * @since 2.0.0
     */
    public static function flush_rewrite() {
        flush_rewrite_rules( false );
    }


    public function get_setting( $key ) {
        return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : '';
    }


}

class ldl {
    public static $slug = 'ldd-lite';

    public static $modal = array();


    public static function load() {
        return LDD_Directory_Lite::get_in();
    }

    public static function tpl() {
        require_once( LDDLITE_PATH . '/includes/class.raintpl.php' );

        raintpl::configure( 'tpl_ext',      'tpl' );
        raintpl::configure( 'tpl_dir',      LDDLITE_PATH . '/templates/' );
        raintpl::configure( 'cache_dir',    LDDLITE_PATH . '/cache/' );
        raintpl::configure( 'path_replace', false );

        return new raintpl;
    }

    public static function setting( $key, $esc = false ) {
        $l = self::load();
        $option = $l->get_setting( $key );
        if ( $esc ) $option = esc_attr( $option );
        return $option;
    }

    public static function attach( $listing ) {
        $l = self::load();
        $l->listing = $listing;
    }

    public static function pull() {
        $l = self::load();
        return $l->listing;
    }

}

/**
 * Start everything.
 */
ldl::load();


