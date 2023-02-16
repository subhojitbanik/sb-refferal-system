<?php

function sb_sales_rep_login_redirect(){
    if(is_page(array('sales-representative',36)) && (is_user_logged_in())){
        $user_id = get_current_user_id();
        $current_user = new WP_User($user_id);
        $user_roles = $current_user->roles;
        $sales_rep_dashboard = home_url('/sales-dashboard/');
        if (!empty($_GET['redirect_to']))
        $redirect_to = $_GET['redirect_to'];
        foreach ($user_roles as $role) {
            if ($role == 'sales-representative') {
                if (empty($redirect_to)) {
                    wp_safe_redirect($sales_rep_dashboard);
                } else {
                    // return $redirect_to;
                    wp_safe_redirect($redirect_to);
                }
            }
        }
    }
}
add_action('template_redirect', 'sb_sales_rep_login_redirect', 10);

function sb_sales_dashboard_redirect(){
    if(is_page(array( 'sales-dashboard', 'my-profile','sales-representative-payments','referrals','sales-representative-change-password')) && (!is_user_logged_in())){
        $sales_representative = home_url('/signin/');
        wp_safe_redirect($sales_representative);
    }
}
add_action('template_redirect', 'sb_sales_dashboard_redirect', 15);




function sb_get_user_by_refferal_code(){

    if(!isset($_COOKIE['referral'])) { 
        //cookie for visitor
        setcookie("referral", $_GET['affiliate'], time() + (86400 * 365), "/"); // 86400 = 1 day // 86400 * 365 for 365 Days
    }

    if(!empty($_GET['affiliate'])){
        $code = $_GET['affiliate'];
    }else{
        $code = $_COOKIE['referral'];
    }

    $args = array(
        'meta_query' => array(
            array(
                'key' => 'refferal_code',
                'value' => $code,
                'compare' => '='
            )
        )
    );
    $users = get_users($args);

    // echo'<pre>'; print_r($user); echo'</pre>';

    ob_start();            
        // print_r($users[0]->ID);
        foreach ($users as $user) {
            echo 'user_ID : ' .$user->ID .'<br>'; 
        }
    return ob_get_clean();
}
add_shortcode('sb_get_user_by_refferal_code','sb_get_user_by_refferal_code');

function sb_set_user_refferal_code_cookie(){

    if(is_page(array('sales-representative',42))){
        if(!empty($_GET['affiliate'])){
            if(!isset($_COOKIE['referral'])) { 
                //cookie for visitor
                setcookie("referral", $_GET['affiliate'], time() + (86400 * 365), "/"); // 86400 = 1 day // 86400 * 365 for 365 Days
            }
        }
    }
}
add_action('add_after_pdclogreg_registration_form','sb_set_user_refferal_code_cookie');

add_action('init','sb_set_refferal_code_cookie');
function sb_set_refferal_code_cookie(){

    if(!empty($_GET['affiliate'])){
        //echo $_GET['affiliate'];
        if(!isset($_COOKIE['referral'])) { 
            //cookie for visitor
            setcookie("referral", $_GET['affiliate'], time() + (86400 * 365), "/"); // 86400 = 1 day // 86400 * 365 for 365 Days
        }
    }
}



function sb_generate_refferal_code_to_usermeta($user_id){
    $value = $user_id.mt_rand(1111,9999);
    $code = base64_encode($value);
    update_field( 'refferal_code', $code, 'user_'.$user_id );
}
add_action( 'pdclogreg_after_register_user', 'sb_generate_refferal_code_to_usermeta', 15, 1 );


function sb_update_user_role($user_id){
    wp_update_user( array ('ID' => $user_id, 'role' => 'sales-representative') ) ;
}
add_action( 'pdclogreg_after_register_user', 'sb_update_user_role', 10, 1 );


function sb_show_user_dashboard_user_affiliate_link(){
    $user_id = get_current_user_id();
    $referal_code = get_field( 'refferal_code','user_'.$user_id );
    $affiliate_url = get_home_url().'/?affiliate='.$referal_code;
    ob_start();
    ?>
    <h3>Your Affiliate Link (Send this link to your clients)</h3>
    <?php 
        // _e(base64_decode($referal_code));
        // _e(base64_encode(base64_encode($referal_code)));
    ?>
    <input type="text" id="affiliate_link" name="affiliate_link" value="<?php _e($affiliate_url);?>" readonly>
    <button onclick="sbcopyToClipboard()" style="margin:10px auto;">Copy To Clipboard</button>
    <script>
        function sbcopyToClipboard() {
            // Get the text field
            var copyText = document.getElementById("affiliate_link");

            // Select the text field
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            // Copy the text inside the text field
            navigator.clipboard.writeText(copyText.value);
            
            // Alert the copied text
            alert("Affiliate Link Copied: " + copyText.value);
        }
    </script>
    <?php    
    return ob_get_clean();
}
add_shortcode( 'sb_show_user_affiliate_link', 'sb_show_user_dashboard_user_affiliate_link' );




function sb_insert_affiliate($user_id){
    print_r($_COOKIE['affiliate']);

    global $wpdb;
    //$affiliate_id = $_COOKIE['referral'];
    $affiliate_id = sb_get_user_id_by_affiliate_id($_COOKIE['referral']);
    
    $table_name = $wpdb->prefix . "referral_table";

    $sql = $wpdb->insert($table_name, array(
        "affiliate_id" => $affiliate_id,
        "user_id" => $user_id,
    ));
}
add_action( 'pdclogreg_after_register_user', 'sb_insert_affiliate', 20,1 );
add_action( 'fg_after_create_profile', 'sb_insert_affiliate', 20,1 );


function sb_get_refferal_count(){
    global $wpdb;
   //echo $user_id = get_current_user_id();
    //$affiliate_id = get_field( 'refferal_code','user_'.$user_id );
    $affiliate_id = get_current_user_id();
    $table_name = $wpdb->prefix . "referral_table";
    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE affiliate_id = '$affiliate_id' ");
    //$referal_count = count($results);

    $users = [];
    foreach ($results as $value) { 
        $user_info = get_userdata($value->user_id);
        //$date = $value->date_time;
        $date = new DateTime($value->date_time);
        $date = $date->format('Y-m-d');
        $roles = $user_info->roles;
        //print_r($roles);
        foreach($roles as $role){ $i=1;
            if($role != 'sales-representative'){
                $users[] = array(
                    'first_name'=> $user_info->first_name,
                    'last_name'=> $user_info->last_name,
                    'user_email'=> $user_info->user_email,
                    'role'=> $role,
                    'date'=> $date,
                );
            }
        }

    }
    $referal_count = count($users);
    
    ob_start();
    //echo $affiliate_id;
    //echo'<pre>'; print_r($results); echo'</pre>';
        if($referal_count > 0){
        ?>
        <h3>Referral Count : <?php _e($referal_count); ?></h3>
        <?php
        }
    return ob_get_clean();
}
add_shortcode( 'sb_get_refferal_count', 'sb_get_refferal_count' );


function sb_get_user_id_by_affiliate_id($affiliate_id){
    $args = array(
        'meta_query' => array(
            array(
                'key' => 'refferal_code',
                'value' => $affiliate_id,
                'compare' => '='
            )
        )
    );
    $users = get_users($args);

    return $users[0]->ID;
}


function sb_get_referral_user(){
    global $wpdb;
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . "referral_table";
    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE affiliate_id = '$user_id' ");


    $users = [];
    foreach ($results as $value) { 
        $user_info = get_userdata($value->user_id);
        $profile_id = get_field('profile_id','user_'.$value->user_id);
        
        $regis_date = $user_info->user_registered;
        //$date = $value->date_time;
        $date = new DateTime($regis_date);
        $date = $date->format('Y-m-d');
        $roles = $user_info->roles;
        //print_r($roles);
        foreach($roles as $role){ 

            if($role == 'tutor'){
                $phone = get_field('phone',$profile_id);
            }elseif($role == 'student'){
                $phone = get_field('phone_number',$profile_id);
            }
            if($role != 'sales-representative'){
                $users[] = array(
                    'first_name'=> $user_info->first_name,
                    'last_name'=> $user_info->last_name,
                    'user_email'=> $user_info->user_email,
                    'role'=> $role,
                    'phone'=> $phone,
                    'date'=> $date,
                );
            }
        }

    }
    $referal_count = count($users);

    ob_start();
    //echo $affiliate_id;
    // echo'<pre>'; print_r($results); echo'</pre>';

    //echo'<pre>'; print_r($user_info); echo'</pre>';

        if($referal_count > 0){
        ?>  
            <div class="referal-wrapper" style="padding:20px;">
                <h3>Total Referrals : <?php _e($referal_count); ?></h3>
                <table id="table_id" class="display">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Registered As</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                            foreach ($users as $user) { 
                                ?>
                                <tr>
                                    <td><?php _e($i); ?></td>
                                    <td><?php _e($user['first_name']); ?></td>
                                    <td><?php _e($user['last_name']); ?></td>
                                    <td><?php _e($user['role']); ?></td>
                                    <td><?php _e($user['date']); ?></td>  
                                </tr>
                                <?php   
                                    $i++;
                            } ?>
                    </tbody>
                </table>
            </div>

            <script>
                jQuery(document).ready( function($){
                    //$('#table_id').DataTable();
                    var table = $('#table_id').DataTable( {
                        scrollX:        true,
                        scrollCollapse: true,
                        paging:         false,
                        columnDefs: [
                            { width: '150', targets: 1 },{ width: '150', targets: 2 }
                        ],
                        fixedColumns: true
                    } );
                });
            </script>
        <?php
        }else{
            echo '<h3 style="text-align:center;margin: 50px auto;"> No referrals yet! </h3>';
        }
    return ob_get_clean();
}
add_shortcode( 'sb_get_referral_user', 'sb_get_referral_user' );



//add_action( 'wp_head', 'sb_get_user_id_by_affiliate_id', 15 );