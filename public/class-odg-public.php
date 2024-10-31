<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       orionorigin.com
 * @since      1.0.0
 *
 * @package    Private_Demos_Generator
 * @subpackage Private_Demos_Generator/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Private_Demos_Generator
 * @subpackage Private_Demos_Generator/public
 * @author     ORION <support@orionorigin.com>
 */
class Private_Demos_Generator_Public {

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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_shortcode( 'pdg-demo', array( $this, 'get_demo_generation_form' ) );
        add_shortcode( 'pdg-remove-expired-demos', array( $this, 'remove_expired_demos' ) );
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/odg-public.css', array(), $this->version, 'all' );
        //wp_enqueue_style( 'validationEngine', plugin_dir_url( __FILE__ ) . 'css/validationEngine.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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
        $dir = plugin_dir_url( __FILE__ );
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/odg-public.js', array( 'jquery' ), $this->version, false );
        wp_localize_script( $this->plugin_name, 'odg_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        //wp_enqueue_script( 'validationEngine', plugin_dir_url( __FILE__ ) . 'js/validationEngine.js', array('jquery', $this->plugin_name), $this->version, false );
        wp_register_script( 'validationEngine-js', $dir . '/js/validationEngine/jquery.validationEngine.min.js' );

        wp_enqueue_script( 'validationEngine-js', array( 'jquery' ) );

        wp_register_script( 'validationEngine-en-js', $dir . '/js/validationEngine/jquery.validationEngine-en.min.js' );
        wp_enqueue_script( 'validationEngine-en-js', array( 'jquery' ) );

        wp_register_style( 'validationEngine-css', $dir . '/js/validationEngine/validationEngine.jquery.min.css' );
        wp_enqueue_style( 'validationEngine-css' );
    }

    function remove_expired_demos() {
        global $wpdb;
        $options = get_option( 'o_settings' );
        $root_demo_dir = $options[ "demos_install_dir" ];
        $root_user = $options[ "root_db_user" ];
        $root_pwd = $options[ "root_db_pwd" ];
        $validity_period = get_proper_value($options, 'validity_period', 60);

        $demo_directories = scandir( $root_demo_dir );

        $output = "";
        foreach ($demo_directories as $dir_name) {
            $dir_path = $root_demo_dir . "/$dir_name";
            if ($dir_name == "." || $dir_name == ".." || !is_dir( $dir_path ))
                continue;

            $raw_dir_creation_date = filemtime( $dir_path );
            $format = 'Y-m-d H:i';
            $dir_creation_date = date( $format, $raw_dir_creation_date );
            $dir_creation_datetime = new DateTime( $dir_creation_date );
            $now = date( $format );
            $now_datetime = new DateTime( $now );
            $interval = $now_datetime->diff( $dir_creation_datetime );
            $diff_in_minutes = odg_interval_to_minutes( $interval );

            if ($diff_in_minutes > $validity_period) {
                $new_db = new wpdb( $root_user, $root_pwd, $options[ "demos_db" ], $wpdb->dbhost );
                $new_db->show_errors();
                $sql = "show tables like '" . $dir_name . "_%'";
                $wp_tables = $new_db->get_col( $sql );

                foreach ($wp_tables as $table_name) {
                    $sql = "DROP TABLE IF EXISTS $table_name";
                    $result = $new_db->query( $sql );
                }
                $del_result = odg_remove_dir( $dir_path );
                if (!file_exists( $dir_path )) {
                    $msg = "Demo $dir_name successfully removed.";
                } else {
                    $msg = "Failed to remove $dir_name.";
                }
                echo $msg . "<br>";
                $output.=$msg . "\n";
            }
        }
    }

    public function get_demo_generation_form( $atts ) {
        extract( shortcode_atts( array(
            'id' => '',
            'show_title' => 1,
            'show_1h_warning' => 1,
                        ), $atts, 'pdg-demo' ) );
        ob_start();
        ?>
        <div id="demo-modal" class="reveal-modal" data-reveal>
            <div class="modal-body">
                <input type="hidden" value="<?php echo $id; ?>" id="demo-id">
                <?php
                if($show_title)
                {
                ?>
                <div class="modal-title">
                    GENERATE A DEMO
                    <div class="modal-subtitle">Enter your email address below to receive your demo URL</div>
                </div>
                <?php
                }
                ?>
                <div id="demo-form-failure-messages" style="margin: 10px;"></div> 
                <?php
                if($show_title)
                {
                ?>
                <p id="demo-duration-notice" class="orange">Please note that the generated demo is only valid for one (01) hour</p>
                <?php
                }
                ?>
                <form id="demo-request-frm" data-form-failure-messages="#demo-form-failure-messages">
                    <input type="text" id="demo-request-email" placeholder="Email address" class="w-100 validate[required,custom[email]]">
                    <label style="color: red; display: block; font-size: 13px; cursor: pointer;">Hotmail, Outlook and Live email adresses may not work due to their spams policy.</label>
                    <input type="submit" value="GENERATE AND SEND URL">
                    <img src="<?php echo plugin_dir_url( __FILE__ ); ?>/images/ajax-loader.gif" class="frm-loader" alt="ajax loader">
                </form>
                <div id="debug"></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function generate_demo_install() {
        $id = $_POST[ "demo_id" ];
        $name = get_the_title( $id );
        $email = $_POST[ "email" ];

        ob_start();
        $demo_meta = get_post_meta( $id, "o-demos", true );
        $overwrite_demo_dir=  get_proper_value($demo_meta, 'overwrite_demo_dir', 'no');

        $options = get_option( 'o_settings' );
        $root_dir = $options[ "demos_install_dir" ];
        if($overwrite_demo_dir=="yes")
            $root_dir=$demo_meta['demos_install_dir'];
        $demo_user = $demo_meta[ "admin_user" ];
        $demo_pwd = $demo_meta[ "admin_pwd" ];
        

        $workdir_name = date('His');
        $workdir_path = $root_dir . "/$workdir_name";
        $source_dir = $demo_meta[ "source_path" ];
        $source_rewrite_path = $demo_meta[ "rewrite_path" ];
        $dest_rewrite_path= $options[ "rewrite_path" ];
        
        if($overwrite_demo_dir=='no')
        {
            if (o_endsWith( $options[ "new_website_root_url" ], "/" ))
                $new_siteurl = $options[ "new_website_root_url" ] . "$workdir_name";
            else
                $new_siteurl = $options[ "new_website_root_url" ] . "/$workdir_name";
        }
        else
        {
            if (o_endsWith( $demo_meta['demos_install_dir'], "/" ))
                $new_siteurl = $demo_meta[ "new_website_root_url" ] . "$workdir_name";
            else
                $new_siteurl = $demo_meta[ "new_website_root_url" ] . "/$workdir_name";  
            
            $dest_rewrite_path=$demo_meta[ "global_settings_rewrite_path" ];
        }

        $result = $this->duplicate_db( $workdir_name, $demo_meta, $email );

        if ($result !== false) {

            $old_rewrite_rule = "RewriteRule . $source_rewrite_path/index.php [L]";
            $new_rewrite_rule = "RewriteRule . $dest_rewrite_path/$workdir_name/index.php [L]";

            $old_db = array('DB_NAME', $demo_meta[ "old_db_name" ]);//"define('DB_NAME', '" . $demo_meta[ "old_db_name" ] . "');";
            $new_db = "define('DB_NAME', '" . $options[ "demos_db" ] . "');";

            $old_db_user = array('DB_USER', $demo_meta[ "old_db_user" ]);//"define('DB_USER', '" . $demo_meta[ "old_db_user" ] . "');";
            $new_db_user = "define('DB_USER', '" . $options[ "root_db_user" ] . "');";

            $old_db_pwd = array('DB_PASSWORD', $demo_meta[ "old_db_pwd" ]);//"define('DB_PASSWORD', '" . $demo_meta[ "old_db_pwd" ] . "');";
            $new_db_pwd = "define('DB_PASSWORD', '" . $options[ "root_db_pwd" ] . "');";

            $old_prefix = '$table_prefix = \'' . $demo_meta[ "sql_tables_prefix" ] . '\';';
            $new_prefix = '$table_prefix = \'' . $workdir_name . '_\';';

            //Sometimes there are 2 spaces before the equals symbol
            $old_prefix_2 = '$table_prefix  = \'' . $demo_meta[ "sql_tables_prefix" ] . '\';';
            $new_prefix_2 = '$table_prefix  = \'' . $workdir_name . '_\';';

            if (wp_mkdir_p( $workdir_path )) {
                $res = $this->copyr( $source_dir, $workdir_path );

                if ($res === true) {
                    $old_rewrite_base = "RewriteBase $source_rewrite_path/";
                    $new_rewrite_base = "RewriteBase $dest_rewrite_path/$workdir_name/";
                    $this->replace_line( "$workdir_path/.htaccess", $old_rewrite_base, $new_rewrite_base );
                    $this->replace_line( "$workdir_path/.htaccess", $old_rewrite_rule, $new_rewrite_rule );
                    $this->replace_line( "$workdir_path/wp-config.php", $old_db, $new_db );
                    $this->replace_line( "$workdir_path/wp-config.php", $old_db_user, $new_db_user );
                    $this->replace_line( "$workdir_path/wp-config.php", $old_db_pwd, $new_db_pwd );
                    $this->replace_line( "$workdir_path/wp-config.php", $old_prefix, $new_prefix );
                    $this->replace_line( "$workdir_path/wp-config.php", $old_prefix_2, $new_prefix_2 );
                } else {
                    echo "Doh! Can't replace data in .htaccess and wp-config.php directory $workdir_path";
                    $result = false;
                }
            } else {
                echo "Can't create directory $workdir_path";
                $result = false;
            }
        } else {
            echo "Database duplication $workdir_name failed";
            $result = false;
        }

        if ($result !== false)
            $to_encode = array( "success" => true, "message" => "Your demo has successfully been generated and the URL has been sent to $email" );
        echo json_encode( $to_encode );
        $output = ob_get_contents();
        ob_end_clean();
        
        $subject = $demo_meta[ "email-subject" ];
        $message = $demo_meta[ "email-message" ];
        $from = $demo_meta[ "email-from" ];
        $searches = array( '{user-email}', '{demo-url}', '{demo-name}', '{demo-username}', '{demo-password}' );
        $replaces = array( $email, $new_siteurl, $name, $demo_user, $demo_pwd );

        $formatted_subject = str_replace( $searches, $replaces, $subject );
        $formatted_message = str_replace( $searches, $replaces, $message );
        
        if ($result !== false)
            odg_mail( $email, $formatted_subject, nl2br( $formatted_message ), $from );
        else {
            $admin_email = get_option( "admin_email" );
            odg_mail( $admin_email, "Failed demo request for $email", $output, $from );
        }
        echo $output;
        die();
    }

    function str_common_prefix( $s1, $s2, $i = 0 ) {
        return (!empty( $s1{$i} ) && !empty( $s2{$i} ) && $s1{$i} == $s2{$i} ) ? $this->str_common_prefix( $s1, $s2, ++$i ) : $i;
    }

    public function copyr( $source, $dest ) {
        // Check for symlinks
        if (is_link( $source )) {
            return symlink( readlink( $source ), $dest );
        }

        // Simple copy for a file
        if (is_file( $source )) {
            return copy( $source, $dest );
        }

        // Make destination directory
        if (!is_dir( $dest )) {
            wp_mkdir_p( $dest );
        }

        // Loop through the folder
        $dir = dir( $source );
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            $this->copyr( "$source/$entry", "$dest/$entry" );
        }

        // Clean up
        $dir->close();
        return true;
    }

    public function save_demo_request_data( $demo_id, $email ) {
        $demo_name = get_the_title( $demo_id );
        $date = date( 'Y-m-d' );
        $time = date( 'H:i:s' );
        
        $post_id = wp_insert_post(array (
           'post_type' => 'odg-stats',
           'post_title' => $demo_name,
           'post_status' => 'publish',
           'comment_status' => 'closed',   
           'ping_status' => 'closed',      
        ));
        if ($post_id) {
           add_post_meta($post_id, 'demo_request', $demo_name);
           add_post_meta($post_id, 'date_request', $date);
           add_post_meta($post_id, 'time_request', $time);
           add_post_meta($post_id, 'email', $email);
        }

        
    }

    public function replace_line( $input, $to_replace, $replace_by ) {
        if (!file_exists( $input ))
            return;
        $files = file( $input );
        if (empty( $files ))
            return;
        $new_file = array();
        foreach ($files as $line) {
            if(is_array( $to_replace))
            {
                if(strpos( $line, $to_replace[0] )!==FALSE && strpos( $line, $to_replace[1] )!==FALSE )
                    $new_file[] = str_replace( $line, $replace_by, $line );
                else
                    $new_file[] = $line;
            }
            else
                $new_file[] = str_replace( $to_replace, $replace_by, $line );
        }

        file_put_contents( $input, $new_file );
    }

    public function o_handle_404_for_public_demo( $wp ) {
        if (!is_admin() && is_404() && preg_match( '/^public-demos/', $wp->request )) {
            wp_redirect( home_url( user_trailingslashit( 'demo-not-available' ) ) );
            exit;
        }
    }

    public function duplicate_db( $target_db, $demo_meta, $requestor_email ) {
        global $wpdb;
        $options = get_option( 'o_settings' );
        $old_siteurl = $demo_meta[ "old_website_url" ];
        $overwrite_demo_dir=  get_proper_value($demo_meta, 'overwrite_demo_dir', 'no');
        if($overwrite_demo_dir=='no')
        {
            if (o_endsWith( $options[ "new_website_root_url" ], "/" ))
                $new_siteurl = $options[ "new_website_root_url" ] . "$target_db";
            else
                $new_siteurl = $options[ "new_website_root_url" ] . "/$target_db";
        }
        else
        {
            if (o_endsWith( $demo_meta['demos_install_dir'], "/" ))
                $new_siteurl = $demo_meta[ "new_website_root_url" ] . "$target_db";
            else
                $new_siteurl = $demo_meta[ "new_website_root_url" ] . "/$target_db";            
        }
        
        $wpdb->show_errors();

        $root_user = $options[ "root_db_user" ];
        $root_pwd = $options[ "root_db_pwd" ];
        
        $ref_db = new wpdb( $demo_meta[ "old_db_user" ], $demo_meta[ "old_db_pwd" ], $demo_meta[ "old_db_name" ], $wpdb->dbhost );
        $new_db = new wpdb( $root_user, $root_pwd, $options[ "demos_db" ], $wpdb->dbhost );
        $new_db->show_errors();
        $sql = "show tables like '" . $demo_meta[ "sql_tables_prefix" ] . "%'";
        $table_names = $ref_db->get_col( $sql );
        foreach ($table_names as $table_name) {
            $new_name = str_replace( $demo_meta[ "sql_tables_prefix" ], $target_db . "_", $table_name );
            $duplication_sql = "CREATE TABLE `$new_name` LIKE `" . $demo_meta[ "old_db_name" ] . "`.$table_name;";
            $new_db->query( $duplication_sql );

            $data_copy_sql = "insert into `$new_name` SELECT * FROM `" . $demo_meta[ "old_db_name" ] . "`.$table_name;";
            $new_db->query( $data_copy_sql );
        }

        $sql1 = "UPDATE $target_db" . "_options SET option_value = replace(option_value, '$old_siteurl', '$new_siteurl') WHERE option_name = 'home' OR option_name = 'siteurl';";
        $result = $new_db->query( $sql1 );
        $sql2 = "UPDATE $target_db" . "_posts SET guid = replace(guid, '$old_siteurl','$new_siteurl');";
        $result = $new_db->query( $sql2 );
        $sql3 = "UPDATE $target_db" . "_posts SET post_content = replace(post_content, '$old_siteurl', '$new_siteurl');";
        $result = $new_db->query( $sql3 );
        $sql4 = "UPDATE $target_db" . "_postmeta SET meta_value = replace(meta_value,'$old_siteurl','$new_siteurl');";
        $result = $new_db->query( $sql4 );
        $sql5 = "UPDATE $target_db" . "_users SET user_email='$requestor_email' where user_login='shop';";
        $result = $new_db->query( $sql5 );
        $sql6 = "UPDATE $target_db" . "_options SET option_value='$requestor_email' where option_name='admin_email';";
        $result = $new_db->query( $sql6 );

        //Prefix replacement
        $sql7 = "UPDATE $target_db" . "_options SET option_name='$target_db" . "_user_roles' where option_name='" . $demo_meta[ "sql_tables_prefix" ] . "user_roles';";
        $result = $new_db->query( $sql7 );

        $sql8 = "UPDATE $target_db" . "_usermeta SET meta_key= REPLACE(meta_key, '" . $demo_meta[ "sql_tables_prefix" ] . "', '$target_db" . "_');";
        $result = $new_db->query( $sql8 );

        //Password modification
        $new_pass = wp_hash_password( $demo_meta[ "admin_pwd" ] );
        $sql9 = "update $target_db" . "_users set user_pass='$new_pass' where user_login='" . $demo_meta[ "admin_user" ] . "'";
        $result = $new_db->query( $sql9 );

        //get active plugins list and remove the selected plugins from it
        $sqlresult = $new_db->get_results( "SELECT option_value FROM $target_db" . "_options WHERE option_name='active_plugins' " );
        $active_plugins = unserialize($sqlresult[0]->option_value);
        
        $options_list = $options['plugins-list'];
        $options_array = explode("\n", $options_list);
         if(is_array($options_array) && !empty($options_array) ){
             foreach($options_array as $option){
                 if($option != ""){
                     $position = array_search( trim($option), $active_plugins);
                     unset($active_plugins[$position]);
                 }
                
            }
         } 
        
        $sql10 = "UPDATE $target_db" . "_options SET option_value='".serialize($active_plugins)."' where option_name='active_plugins';";
        $result = $new_db->query( $sql10 );

        return $result;
    }

    function init_globals() {
        global $wad_settings;
        $wad_settings = get_option( "o-demos" );
    }

    public function check_hourly() {
        $options = get_option('o_settings');
        if ( isset($options['cron-settings']) && $options['cron-settings'] == 'wp-cron' ) {
            if($options['recurrence'] == 'hourly'){
               $this->remove_expired_demos(); 
}
        }
    }

    public function check_twicedaily() {
        $options = get_option('o_settings');
        if ( isset($options['cron-settings']) && $options['cron-settings'] == 'wp-cron' ) {
            if($options['recurrence'] == 'twice_daily'){
               $this->remove_expired_demos(); 
            }
        }
    }

    public function check_daily() {
        $options = get_option('o_settings');
        if ( isset($options['cron-settings']) && $options['cron-settings'] == 'wp-cron' ) {
            if($options['recurrence'] == 'daily'){
               $this->remove_expired_demos(); 
            }
        }
    }

}
