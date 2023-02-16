<?php


function sb_referral_admin_menu() {
	add_menu_page(
		__( 'Affiliates', 'my-textdomain' ),
		__( 'Affiliates', 'my-textdomain' ),
		'manage_options',
		'fastgrades-affiliates',
		'sb_affiliates_admin_page_contents',
		'dashicons-groups',
		3
	);
	//add submenu
	add_submenu_page('fastgrades-affiliates', 'Referrals', 'Referrals', 'manage_options', 'fastgrades-referrals', 'sb_referral_admin_page_contents');
}
add_action( 'admin_menu', 'sb_referral_admin_menu' );


function sb_affiliates_admin_page_contents(){
	wp_enqueue_style('datatablecss','https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');
	wp_enqueue_script('datatables','https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js');
	?>

		<h2>Fastgrades Affiliates</h2>

        <div class="sb_affiliate_wrapper">
            <?php _e(do_shortcode('[sb_get_affiliates_admin]')); ?>
        </div>

	<?php
}



function sb_referral_admin_page_contents(){
	wp_enqueue_style('datatablecss','https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');
	wp_enqueue_script('datatables','https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js');

	echo'<h2>Fastgrades Refferals</h2>';
	_e(do_shortcode('[sb_admin_show_referrals]'));
}







function sb_get_affiliates_admin(){
	$args = array(
		'role'    => 'sales-representative',
		'order'   => 'DESC'
	);
	$users = get_users( $args );

    // print('<pre>');
    // print_r($users);
    // print('</pre>');
	?>
	<style>
		.dataTables_wrapper .dataTables_length select {
			padding: 5px 15px !important;
		}
	</style>
		<div class="table-wrapper" style="width:90%;margin:0px auto;">
			<table id="affiliate_id" class="display">
				<thead>
					<tr>
						<th>S.No.</th>
						<th>Affiliate's Name</th>
						<th>Affiliate's Email</th>
						<th>Phone No.</th>
						<th>Referrals</th>
						<th>Affiliate's URL</th>
						<th>Registration Date</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1;
						foreach ($users as $user) { 
							$regis_date = $user->user_registered;
							//$date = $value->date_time;
							$date = new DateTime($regis_date);
							$date = $date->format('Y-m-d');
							$phone = get_field('phone_number_sales','user_'.$user->ID);
							$referrals = sb_get_affiliates_referrals_count($user->ID);	
							$refferal_code = get_field('refferal_code','user_'.$user->ID);		
							$affiliate_link = home_url().'/?affiliate='.$refferal_code;				
							?>
							<tr>
								<td><?php _e($i); ?></td>
								<td><?php _e($user->display_name); ?></td>
								<td><?php _e($user->user_email); ?></td>
								<td><?php _e($phone); ?></td>
								<td><?php _e($referrals); ?></td>
								<td><?php _e($affiliate_link); ?></td>
								<td><?php _e($date); ?></td>
							</tr>
							<?php   
								$i++;
						} ?>
				</tbody>
			</table>
		</div>
	
		<script>
			jQuery(document).ready( function($){
				$('#affiliate_id').DataTable();
			});
		</script>
	<?php
}
add_shortcode( 'sb_get_affiliates_admin', 'sb_get_affiliates_admin' );

function sb_get_affiliates_referrals_count($user_id){
    global $wpdb;
    //$user_id = get_current_user_id();
    $table_name = $wpdb->prefix . "referral_table";
    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE affiliate_id = '$user_id' ");
	$count = count($results);
	return $count;
}

function sb_admin_show_referrals_data(){
	global $wpdb;
    $table_name = $wpdb->prefix . "referral_table";
    $results = $wpdb->get_results("SELECT * FROM $table_name");
	// print('<pre>');
	// print_r($results);
	// print('</pre>');
	?>

	<style>
		.dataTables_wrapper .dataTables_length select {
			padding: 5px 15px !important;
		}
	</style>
		<div class="table-wrapper" style="width:90%;margin:0px auto;">
			<table id="referrals_data" class="display">
				<thead>
					<tr>
						<th>S.No.</th>
						<th>Referral's Name</th>
						<th>Registered As</th>
						<th>Affiliate's Name</th>
						<th>Affiliate's Link</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1;
						foreach ($results as $row) { 
							// $regis_date = $user->user_registered;
							// //$date = $value->date_time;
							// $date = new DateTime($regis_date);
							// $date = $date->format('Y-m-d');
							// $phone = get_field('phone_number_sales','user_'.$user->ID);
							// $referrals = sb_get_affiliates_referrals_count($user->ID);	

							$referal_user_info = get_userdata($row->user_id);
							$referal_user_roles = $referal_user_info->roles;
							$affiliate_user_info = get_userdata($row->affiliate_id);
							$refferal_code = get_field('refferal_code','user_'.$row->affiliate_id);

							// print('<pre>');
							// print_r($referal_user_info);
							// print('</pre>');

							$referral_name = $referal_user_info->first_name.' '.$referal_user_info->last_name;

							// print($row->user_id.'<br>');
							$affiliate_link = home_url().'/?affiliate='.$refferal_code;
							if(!empty($referal_user_info)){
							?>
							<tr>
								<td><?php _e($i); ?></td>
								<td><?php _e($referral_name); ?></td>
								<td><?php _e($referal_user_roles[0]); ?></td>
								<td><?php _e(($affiliate_user_info->display_name == 'Fastgrade Admin') ? 'Direct' : $affiliate_user_info->display_name); ?></td>
								<td><?php _e($affiliate_link); ?></td>
							</tr>
							<?php   }
								$i++;
						} ?>
				</tbody>
			</table>
		</div>
	
		<script>
			jQuery(document).ready( function($){
				$('#referrals_data').DataTable();
			});
		</script>

	<?php
}
add_shortcode( 'sb_admin_show_referrals', 'sb_admin_show_referrals_data' );