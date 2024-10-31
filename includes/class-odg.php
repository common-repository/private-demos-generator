<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       orionorigin.com
 * @since      1.0.0
 *
 * @package    Private_Demos_Generator
 * @subpackage Private_Demos_Generator/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Private_Demos_Generator
 * @subpackage Private_Demos_Generator/includes
 * @author     ORION <support@orionorigin.com>
 */
class Private_Demos_Generator {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Private_Demos_Generator_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->plugin_name = 'odg';
        $this->version = '1.0.0';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Private_Demos_Generator_Loader. Orchestrates the hooks of the plugin.
     * - Private_Demos_Generator_i18n. Defines internationalization functionality.
     * - Private_Demos_Generator_Admin. Defines all hooks for the admin area.
     * - Private_Demos_Generator_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-odg-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-odg-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-odg-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-odg-public.php';

        $this->loader = new Private_Demos_Generator_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Private_Demos_Generator_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Private_Demos_Generator_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Private_Demos_Generator_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'init', $plugin_admin, 'register_cpt_demo' );
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'get_orion_demo_metabox' );
        $this->loader->add_action( 'save_post_o-demos', $plugin_admin, 'save_demo_meta' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'get_plugin_menus' );
        $this->loader->add_filter( 'manage_edit-o-demos_columns', $plugin_admin, 'get_columns');
        $this->loader->add_action( 'manage_o-demos_posts_custom_column', $plugin_admin, 'get_columns_values', 5, 2);
        $this->loader->add_action( 'wp_ajax_check-source_db-connection', $plugin_admin, 'test_source_db_connection');
        
//        $list = new ODG_statistics(FALSE);
//        $this->loader->add_action( 'init', $list, 'register_cpt_stats' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Private_Demos_Generator_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'wp_ajax_pdg_generate_demo_install', $plugin_public, 'generate_demo_install' );
        $this->loader->add_action( 'wp_ajax_nopriv_pdg_generate_demo_install', $plugin_public, 'generate_demo_install' );
        $this->loader->add_action( 'wp_ajax_subscribe_to_prelaunch', $plugin_public, 'subscribe_to_prelaunch' );
        $this->loader->add_action( 'wp_ajax_nopriv_subscribe_to_prelaunch', $plugin_public, 'subscribe_to_prelaunch' );
        //$this->loader->add_action( 'orion_old_demo_removal', $plugin_public, 'remove_old_demos_data' );
        $this->loader->add_action( 'wp_ajax_subscribe_to_prelaunch', $plugin_public, 'subscribe_to_prelaunch' );
        $this->loader->add_action( 'wp_ajax_nopriv_subscribe_to_prelaunch', $plugin_public, 'subscribe_to_prelaunch' );
        $this->loader->add_action( 'wp', $plugin_public, 'o_handle_404_for_public_demo' );
        $this->loader->add_action( 'init', $plugin_public, 'init_globals' );
        
        $this->loader->add_action('odg_hourly_checks',$plugin_public, 'check_hourly');
        $this->loader->add_action('odg_twicedaily_checks',$plugin_public, 'check_twicedaily');
        $this->loader->add_action('odg_daily_checks',$plugin_public, 'check_daily');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Private_Demos_Generator_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
