<?php
/*
Plugin Name: Fast Grades Refferal System.
Description: refferal-App.
Author: Subhojit Banik
Version: 1.0.0
Author URI: #
 */



define('SB_REFFERAL_VERSION', '1.0.0');
define('SB_REFFERAL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SB_REFFERAL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SB_REFFERAL_PLUGIN_FILE', __FILE__);
/*
required files..
 */
require SB_REFFERAL_PLUGIN_DIR . 'refferal.php';
require SB_REFFERAL_PLUGIN_DIR . 'sb-update-profile.php';
require SB_REFFERAL_PLUGIN_DIR . 'sb-change-password.php';
require SB_REFFERAL_PLUGIN_DIR . 'sb-referal-graph.php';
require SB_REFFERAL_PLUGIN_DIR . 'sb-admin-referal.php';

/**
 * enqueue scripts  
 * 
 */
function sb_referrals_enqueue_scripts()
{
    if(is_page('referrals')){
		wp_enqueue_style('sb-referral-css', SB_REFFERAL_PLUGIN_URL . 'style.css');
    }   
	if(is_page('sales-dashboard')){
		wp_enqueue_script('sb-tensorflowgraph', 'https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-vis');
    } 
}
add_action('wp_enqueue_scripts', 'sb_referrals_enqueue_scripts');




/*
*create reviews table..   
*/

function sb_create_refferal_table_fn(){

    global $wpdb;
    $table_name = $wpdb->prefix . 'referral_table';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    affiliate_id varchar(255) NOT NULL,
    user_id varchar(255) NOT NULL,
    date_time TIMESTAMP NOT NULL,
    PRIMARY KEY  (id)
  ) $charset_collate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
register_activation_hook(SB_REFFERAL_PLUGIN_FILE, 'sb_create_refferal_table_fn');

// function droprtable(){
//   global $wpdb;
//   $table_name = $wpdb->prefix . 'referral_table';
//   $sql = "DROP TABLE IF EXISTS $table_name";
//   $wpdb->query($sql);
// }
// register_deactivation_hook(SB_REFFERAL_PLUGIN_FILE,'droprtable');


function show_table($atts)
{

  global $wpdb;
  $table = $wpdb->prefix . 'referral_table';

  $existing_columns = $wpdb->get_col("DESC {$table}", 0);
  //print_r($existing_columns	);

  $data = [
    'request_id' => 5479,
  ];
  // ----------------------------------------------

  // $sql = "ALTER TABLE `{$table}`
  // 		ADD `canceled_by` INT NULL DEFAULT 0;";

  // $query_result = $wpdb->query( $sql );
  // $sql = "ALTER TABLE `{$table}`
  // 		ADD `cancel_reason` VARCHAR(255) NULL ;";
      

  $sql = $wpdb->get_results("SELECT * FROM $table ");


  // $query_result = $wpdb->query( $sql );

  // ----------------------------------------------
  

  ob_start();
  print_r($existing_columns	);
  echo'<pre>';
  print_r($sql);
  echo'</pre>';
 
  return ob_get_clean();

}
add_shortcode( 'show_table', 'show_table' );