<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       orionorigin.com
 * @since      1.0.0
 *
 * @package    Private_Demos_Generator
 * @subpackage Private_Demos_Generator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Private_Demos_Generator
 * @subpackage Private_Demos_Generator/admin
 * @author     ORION <support@orionorigin.com>
 */
class Private_Demos_Generator_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Private_Demos_Generator_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Private_Demos_Generator_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/odg-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( "o-ui", plugin_dir_url( __FILE__ ) . 'css/UI.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Private_Demos_Generator_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Private_Demos_Generator_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/odg-admin.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( "o-admin", plugin_dir_url( __FILE__ ) . 'js/o-admin.js', array( 'jquery' ), $this->version, false );
    }

    public function register_cpt_demo() {
        $labels = array(
            'name' => __( 'Demos', 'o-demos' ),
            'singular_name' => __( 'Template', 'o-demos' ),
            'add_new' => __( 'New demo', 'o-demos' ),
            'add_new_item' => __( 'New demo', 'o-demos' ),
            'edit_item' => __( 'Edit demo', 'o-demos' ),
            'new_item' => __( 'New demo', 'o-demos' ),
            'view_item' => __( 'View', 'o-demos' ),
            'search_items' => __( 'Search demos', 'o-demos' ),
            'not_found' => __( 'No demo found', 'o-demos' ),
            'not_found_in_trash' => __( 'No demo in the trash', 'o-demos' ),
            'menu_name' => __( 'Demos', 'o-demos' ),
        );
        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'description' => 'Demos for the products customizer.',
            'supports' => array( 'title', 'thumbnail' ),
            'public' => true,
            'menu_icon' => PDG_URL . 'admin/images/logo128.svg',
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'has_archive' => false,
            'query_var' => false,
            'can_export' => true
        );

        register_post_type( 'o-demos', $args );
    }

    public function get_plugin_menus() {
        global $submenu;
        add_submenu_page( 'edit.php?post_type=o-demos', __( 'Settings', 'odg' ), __( 'Settings', 'odg' ), 'manage_options', 'o-settings', array( $this, 'add_settings_page' ) );
        //add_submenu_page('edit.php?post_type=o-demos', __('User manual', 'odg'), __('User Manual', 'odg'), 'manage_options', 'o-settings', array($this, 'add_settings_page'));

        $url = PDG_URL . 'UsermanualPDG.pdf';
        $submenu[ 'edit.php?post_type=o-demos' ][] = array( __( 'User manual', 'odg' ), 'manage_options', $url );
    }

    public function get_orion_demo_metabox() {
        $screens = array( 'o-demos' );

        foreach ($screens as $screen) {
            add_meta_box(
                    'o-demos-box', __( 'Demo', 'odg' ), array( $this, 'get_demo_metabox_content' ), $screen
            );

            add_meta_box(
                    'o-demos-emails-box', __( 'Emails notifications', 'odg' ), array( $this, 'get_email_metabox_content' ), $screen
            );
        }
    }

    public function get_demo_metabox_content() {
        $demo_id = get_the_ID();
        $demo_meta = get_post_meta( $demo_id, "o-demos", true );
        if (empty( $demo_meta )) {
            global $wpdb;
//            var_dump($wpdb);
            $demo_meta = array(
                "sql_tables_prefix" => $wpdb->prefix,
                "source_path" => get_home_path(),
                "old_website_url" => home_url(),
                "new_website_root_url" => "",
                "old_db_name" => $wpdb->dbname,
                "old_db_user" => $wpdb->dbuser,
                "old_db_pwd" => $wpdb->dbpassword,
                "admin_user" => "",
                "admin_pwd" => ""
            );
        }

        $begin = array(
            'type' => 'sectionbegin',
//                        'table' => 'options',
        );

        $s_name = array(
            'title' => __( 'DB name', 'odg' ),
            'type' => 'text',
            'name' => 'o-demos[old_db_name]',
            'default' => $demo_meta[ "old_db_name" ],
        );

        $s_user = array(
            'title' => __( 'DB user', 'odg' ),
            'type' => 'text',
            'name' => 'o-demos[old_db_user]',
            'default' => $demo_meta[ "old_db_user" ],
        );

        $s_pwd = array(
            'title' => __( 'DB password', 'odg' ),
            'type' => 'text',
            'name' => 'o-demos[old_db_pwd]',
            'default' => $demo_meta[ "old_db_pwd" ],
        );
        
        $s_check = array(
            'title' => __('Test db Connection', 'odg'),
            'type' => 'custom',
            'callback'=> array($this, 'get_test_connection_button')
        );

        $source_db = array(
            'title' => __( 'Source DB', 'odg' ),
            'desc' => __( 'Credentials of the database to clone.', 'odg' ),
            'type' => 'groupedfields',
            'fields' => array( $s_name, $s_user, $s_pwd, $s_check ),
        );


        $s_path = array(
            'title' => __( 'Source Dir', 'odg' ),
            'type' => 'text',
            'name' => 'o-demos[source_path]',
            'default' => $demo_meta[ "source_path" ],
        );


        $s_table_prefix = array(
            'title' => __( 'Source tables prefix', 'odg' ),
            'type' => 'text',
            'name' => 'o-demos[sql_tables_prefix]',
            'default' => $demo_meta[ "sql_tables_prefix" ],
        );
        
        $s_get_config = array(
            'id' => 'pdg_get_config',
            'title' => __( 'Get database configuration', 'odg' ),
            'class' => 'button',
            'type' => 'button',
        );

        $old_url = array(
            'title' => __( 'Source website URL', 'odg' ),
            'type' => 'text',
            'name' => 'o-demos[old_website_url]',
            'default' => $demo_meta[ "old_website_url" ],
            'desc' => 'URL settings of the installation to clone'
        );
        $old_rewrite_path = array(
            'title' => __( 'Rewrite Path', 'odg' ),
            'type' => 'text',
            'name' => 'o-demos[rewrite_path]',
            'desc' => 'Rewrite path of the installation to clone. Example in bold: <br>RewriteRule . <strong>/demos</strong>/index.php [L]',
        );

        $username = array(
            'title' => __( 'Username', 'odg' ),
            'type' => 'text',
            'name' => 'o-demos[admin_user]',
            'default' => $demo_meta[ "admin_user" ],
        );

        $pwd = array(
            'title' => __( 'Password', 'odg' ),
            'type' => 'text',
            'name' => 'o-demos[admin_pwd]',
            'default' => $demo_meta[ "admin_pwd" ],
        );
        $end = array( 'type' => 'sectionend' );

        $source_files = array(
            'title' => __( 'Files', 'odg' ),
            'desc' => __( 'Location of the files to clone.', 'odg' ),
            'type' => 'groupedfields',
            'fields' => array( $s_path, $s_table_prefix ),
        );

        $demo_credentials = array(
            'title' => __( 'Credentials', 'odg' ),
            'desc' => __( 'Cloned installation credentials to be mailed to the requester once the generation is complete.', 'odg' ),
            'type' => 'groupedfields',
            'fields' => array( $username, $pwd ),
        );
        
        $overwrite_demo_dir = array(
            'title' => __( 'Overwrite demo directories', 'odg' ),
            'name' => 'o-demos[overwrite_demo_dir]',
            'type' => 'radio',
            'default' => 'no',
            'class' => 'odg-overwrite-demo-dir',
            'desc' => __( 'Would you like to put the demo in another directory than the one defined in the general settings?', 'odg' ),
            'options' => array(
                "no" => "No",
                "yes" => "Yes",
            )
        );
        
        $d_directory = array(
            'title' => __( 'New demos dir.', 'odg' ),
            'type' => 'text',
            'name' => 'o-demos[demos_install_dir]',
            'desc' => 'Path of the directory which will contain the generated demos.',
            'row_class' => 'show-if-overwrite-dir',
        );
        $d_url = array(
            'title' => __( 'New demos dir URL', 'odg' ),
            'type' => 'text',
            'name' => 'o-demos[new_website_root_url]',
            'desc' => 'URL of the directory which will contain the generated demos.',
            'row_class' => 'show-if-overwrite-dir',
        );
        $rewrite_paths = array(
            'title' => __( 'Rewrite Path', 'odg' ),
            'desc' => 'Rewrite path of the directory which will contain the generated demos. Example in bold: <br>RewriteRule . <strong>/public-demos</strong>/index.php [L]',
            'type' => 'text',
            'name' => 'o-demos[global_settings_rewrite_path]',
            'row_class' => 'show-if-overwrite-dir',
        );

        echo o_admin_fields( array( $begin, $source_db, $source_files, $old_url, $old_rewrite_path, $demo_credentials, $overwrite_demo_dir, $d_directory, $d_url, $rewrite_paths, $end ) );
    }

    public function get_email_metabox_content() {
        $begin = array(
            'type' => 'sectionbegin',
        );

        $subject = array(
            'title' => __( 'Subject', 'odg' ),
            'type' => 'text',
            'desc' => __( 'Email subject', 'odg' ),
            'name' => 'o-demos[email-subject]',
        );

        $message = array(
            'title' => __( 'Message', 'odg' ),
            'type' => 'textarea',
            'desc' => __( 'Email message. <br>Placeholders: <br><strong>{user-email}</strong><br> <strong>{demo-url}</strong><br> <strong>{demo-name}</strong><br> <strong>{demo-username}</strong><br><strong>{demo-password}</strong><br>', 'odg' ),
            'name' => 'o-demos[email-message]',
            'default' => "Your demo has been successfully generated: \n"
            . "Site URL: {demo-url} \n"
            . "Admin URL: {demo-url}/wp-admin \n"
            . "<br><p style='color: red;'>Please note that the generated demo is only valid for one (01) hour.</p> \n\n"
            . "Username: {demo-username} \n"
            . "Password: {demo-password} \n"
        );

        $from = array(
            'title' => __( 'From', 'odg' ),
            'type' => 'text',
            'desc' => __( 'Email header', 'odg' ),
            'name' => 'o-demos[email-from]',
            'default' => 'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>',
        );

        $end = array( 'type' => 'sectionend' );

        echo o_admin_fields( array( $begin, $from, $subject, $message, $end ) );
    }

    public function save_demo_meta( $demo_id ) {
        if (isset( $_POST[ "o-demos" ] ))
            update_post_meta( $demo_id, "o-demos", $_POST[ "o-demos" ] );
    }

    public function add_settings_page() {
        if (isset( $_POST[ "o_settings" ] )) {
            update_option( "o_settings", $_POST[ "o_settings" ] );
            ?>
            <div id="message" class="updated notice notice-success is-dismissible"><p>Data successfully saved.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
            <?php
        }
        $demo_db_meta = get_option( "o_settings", true );
        if (empty( $demo_db_meta ))
            $demo_db_meta = array(
                "root_db_user" => "",
                "root_db_pwd" => "",
                "demos_install_dir" => "",
                "demos_db" => ""
            );
        ?>
        <div class="wrap cf">
            <h1><?php _e( "Private Demos Generator Settings", "odg" ); ?></h1>
            <form method="POST" action="" class="mg-top">
                <div class="postbox" id="o-demos">
                    <?php
                    $begin = array(
                        'type' => 'sectionbegin',
                        'table' => 'options',
                    );

                    $db_root_user = array(
                        'title' => __( 'DB user', 'odg' ),
                        'type' => 'text',
                        'name' => 'o_settings[root_db_user]',
                        'desc' => 'Username of the database that will contain the generated demos.',
                        'default' => $demo_db_meta[ "root_db_user" ]
                    );
                    $db_root_pwd = array(
                        'title' => __( 'DB password', 'odg' ),
                        'type' => 'text',
                        'name' => 'o_settings[root_db_pwd]',
                        'desc' => 'Password of the database that will contain the generated demos.',
                        'default' => $demo_db_meta[ "root_db_pwd" ]
                    );
                    $d_database = array(
                        'title' => __( 'DB Name', 'odg' ),
                        'type' => 'text',
                        'name' => 'o_settings[demos_db]',
                        'desc' => 'Name of the database that will contain the generated demos.',
                        'default' => $demo_db_meta[ "demos_db" ]
                    );
                    $d_directory = array(
                        'title' => __( 'Demos directory', 'odg' ),
                        'type' => 'text',
                        'name' => 'o_settings[demos_install_dir]',
                        'desc' => 'Path of the directory which will contain the generated demos.',
                        'default' => $demo_db_meta[ "demos_install_dir" ]
                    );
                    
                    $rewrite_paths = array(
                        'title' => __( 'Rewrite Path', 'odg' ),
                        'desc' => 'Rewrite path of the directory which will contain the generated demos. Example in bold: <br>RewriteRule . <strong>/public-demos</strong>/index.php [L]',
                        'type' => 'text',
                        'name' => 'o_settings[rewrite_path]',
                    );

                    $end = array(
                        'type' => 'sectionend'
                    );

                    $source_db = array(
                        'title' => __( 'Demos DB', 'odg' ),
                        'desc' => 'Parameters of the database that will contain the generated demos.',
                        'type' => 'groupedfields',
                        'fields' => array( $d_database, $db_root_user, $db_root_pwd ),
                    );

                    $d_url = array(
                        'title' => __( 'Demo websites folder URL', 'odg' ),
                        'type' => 'text',
                        'name' => 'o_settings[new_website_root_url]',
                        'desc' => 'URL of the directory which will contain the generated demos.',
                    );
                    $validity_period = array(
                        'title' => __( 'Demos validity period (in minutes)', 'odg' ),
                        'type' => 'number',
                        'name' => 'o_settings[validity_period]',
                        'desc' => 'How many minutes after the generation should the demo be cleaned?.',
                        'default' => 60
                    );

                    //adding cron setting

                    if (isset( $demo_db_meta[ 'cron-type' ] )) {
                        $action_meta = $demo_db_meta[ 'cron-type' ];
                    } else {
                        $action_meta = "wp-cron";
                    }

                    if ($action_meta == "wp-cron") {
                        $custom_request_css = "display:none;";
                    } else {
                        $custom_request_css = "";
                    }

                    $cron_type = array(
                        'title' => __( 'Cron service', 'odg' ),
                        'name' => 'o_settings[cron-type]',
                        'type' => 'radio',
                        'default' => 'wp-cron',
                        'class' => 'odg-cron-type',
                        'desc' => __( 'Choose the cron service that you want to use', 'odg' ),
                        'options' => array(
                            "wp-cron" => "Use the wp_cron",
                            "server-cron" => "Use a real cron",
                        )
                    );

                    $cron_settings = array(
                        'title' => __( 'Cron settings', 'odg' ),
                        'name' => 'o_settings[cron-settings]',
                        'type' => 'text',
                        'desc' => __( 'a secret hash key that will be used to execute the cron' ),
                        'id' => 'odg-cron-server-key',
                        'row_css' => $custom_request_css,
                        'row_class' => 'show-if-server',
                        'default' => '7744889e548c69557cfa845b3e4e76cf',
                    );

                    $recurrence = array(
                        'title' => __( 'Recurrence', 'odg' ),
                        'name' => 'o_settings[recurrence]',
                        'type' => 'select',
                        //'class'=> 'odg-urls-list-extraction-type',
                        'desc' => __( 'How often do you want the checking made?', 'odg' ),
                        'options' => array(
                            "hourly" => __( "hourly", "odg" ),
                            "daily" => __( "daily", "odg" ),
                            "twice_daily" => __( "Twice daily", "odg" ),
                        ),
                    );
                    
                    $plugins_to_disable = array(
                        'title' => __( 'List of plugins to disable', 'odg' ),
                        'type' => 'textarea',
                        'name' => 'o_settings[plugins-list]',
                        'desc' => __('List of plugins to be deactivated when new demo is generated', 'odg' ),
                    );
                    


                    $settings = array(
                        $begin,
                        $source_db,
                        $d_directory,
                        $rewrite_paths,
                        $d_url,
                        $validity_period,
                        $cron_type,
                        $cron_settings,
                        $recurrence,
                        $plugins_to_disable,
                        $end
                    );
                    echo o_admin_fields( $settings );
                    $url = get_home_url() . '/odg/?odg-key=';
                    ?>

                    <div class='show-if-server'>
                        <p>Call it directly: <strong class="odg-highlight"> curl --silent <span class="odg_url"></span></strong></p>
                        <p>or set up a cron <strong  class="odg-highlight">*/5 * * * * GET <span class="odg_url"></span> /dev/null </strong> </p>
                        <p>You can setup an interval as low as one minute, but should consider a reasonable value of 5-15 minutes as well.</p>
                        <p>                            
                            If you need help setting up a cron job please refer to the documentation that your provider offers.</p>
                        <p>Anyway, chances are high that either the <a href="https://docs.cpanel.net/twiki/bin/view/AllDocumentation/CpanelDocs/CronJobs#Adding a cron job">CPanel</a>, <a href="http://download1.parallels.com/Plesk/PP10/10.3.1/Doc/en-US/online/plesk-administrator-guide/plesk-control-panel-user-guide/index.htm?fileName=65208.htm">Plesk</a> or the <a href="http://www.thegeekstuff.com/2011/07/php-cron-job/">crontab</a> documentation will help you.</p>
                    </div>
                    <br><input type="submit" class="button button-primary button-large" value="<?php _e( "Save", "odg" ); ?>">
                </div>
            </form>
        </div>

        <script>
            var odg_url = "<?php echo ($url); ?>";
        </script>

        <?php
    }

    /**
     * Adds the Custom column to the default products list to help identify which ones are custom
     * @param array $defaults Default columns
     * @return array
     */
    function get_columns( $defaults ) {
        $defaults[ 'pdg_shortcode' ] = __( 'Shortcode', 'pdg' );
        return $defaults;
    }

    /**
     * Sets the Custom column value on the products list to help identify which ones are custom
     * @param type $column_name Column name
     * @param type $id Product ID
     */
    function get_columns_values( $column_name, $id ) {
        if ($column_name === 'pdg_shortcode') {
            echo "[pdg-demo id=$id]";
        }
    }
    
    function get_test_connection_button(){
        ?>
        <button id="pdg_db_connect" class="button">Test connection</button>
        <span id="db_connect_loading" class="loading_active" style="display: none;"></span>
        <?php
    }
    
    function get_db_config_button(){
        ?>
        <button id="pdg_db_config" class="button">Get db config</button>
        <?php
    }
    
    function test_source_db_connection(){
        global $wpdb;
        $sdb = $_POST['sdb'];
        
        if(is_array($sdb)){
            
            $dbuser = $sdb['sdb_user'];
            $dbname = $sdb['sdb_name'];
            $dbpass = $sdb['sdb_pass'];
            
            $open_connection = new wpdb( $dbuser, $dbpass, $dbname, $wpdb->dbhost );
            $connect = $open_connection->check_connection();
            if($connect)
                die('1');
//            else
//                die($open_connection->show_errors());

        }
    }

}
