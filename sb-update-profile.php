<?php

function sb_update_profile() {
	if (!is_user_logged_in()) 
		return;


    $curr_user = get_current_user_id();

    if (isset($_POST['submit'])) {
        if (!empty($_POST['fname']) && !empty($_POST['lname']) && !empty($_POST['uid']) ){
            //$set_pwd = wp_set_fname( $_POST['fname'], $curr_user );
            $sb_fname = $_POST['fname'];
            $sb_lname = $_POST['lname'];
            $sb_age = $_POST['user_age'];
            $sb_phone = $_POST['sb_phone'];
            //$sb_email = $_POST['sb_email'];
            $update_user = wp_update_user(array(
                'ID' => $curr_user,
                'first_name' => $sb_fname,
                'last_name' =>$sb_lname,
                )
            );
			update_field('above_eighteen',$sb_age,'user_'.$curr_user);
			update_field('phone_number_sales',$sb_phone,'user_'.$curr_user);
            if($update_user){
				
				?>
                    <div class="alert alert-success" role="alert">
                        <strong>Profile Updated Successfully!</strong> 
                    </div>
            <?php }
        } 
            
    }

}

function sb_update_user_profile_fn(){
	if (!is_user_logged_in())
	auth_redirect();

	ob_start();
	$curr_user = get_current_user_id();
	$userinfo = wp_get_current_user();

	$login_name = new WP_User($curr_user);
	// echo'<pre>';
	// print_r($usermeta);
	// echo'</pre>';
	sb_update_profile(); 
	?>
    <style>
        input#loginname:disabled {
            background: #fffcfcc4;
        }
    </style>
	<div class="user_change_pw">
		<form action="" method="POST" >
			<div class="input_wrapper uname form-group" style="margin: 5px auto;">
				<label for="loginname">Username/Email</label>
				<input type="text" class="form-control" id="loginname" name="loginname" value="<?php echo $login_name->user_login; ?>" disabled>
			</div>

			<div class="input_wrapper pw form-group" style="margin: 5px auto;">
				<label for="fname">First Name *</label>
				<input type="text" class="form-control" id="fname" name="fname" value="<?php echo $userinfo->user_firstname?>" required>
			</div>
			<div class="input_wrapper pw form-group" style="margin: 5px auto;">
				<label for="lname">Last Name *</label>
				<input type="text" class="form-control" id="lname" name="lname" value="<?php echo $userinfo->user_lastname?>" required>
			</div>
			<div class="input_wrapper pw form-group" style="margin: 5px auto;">
				<label for="lname">Phone Number *</label>
				<input type="number" class="form-control" id="lname" name="sb_phone" value="<?php echo get_field('phone_number_sales','user_'.$curr_user); ?>" required>
			</div>
			<div class="input_wrapper pw form-group">
				<label for="user_age">Above 18 years?</label>
				<select name="user_age" id="user_age">
					<option value="above" <?php echo get_field('above_eighteen','user_'.$curr_user) == 'above' ? ' selected="selected"' : '';?> >I am 18 or more and responsible for my actions.</option>
					<option value="not_above" <?php echo get_field('above_eighteen','user_'.$curr_user) == 'not_above' ? ' selected="selected"' : '';?>>I am 17 or less and my guardian who is responsible for my actions is signing up for me.</option>
				</select>
			</div>

			<div class="input_wrapper submit form-group" style="margin: 5px auto;">
				<input type="hidden" name="uid" value="<?php echo $login_name->ID ?>">
				<input type="submit" class="btn btn-primary" name="submit" value="Update Account">
			</div>
		</form>
	</div>
	
	<?php
	return ob_get_clean();
}
add_shortcode('sb_update_user_profile_form', 'sb_update_user_profile_fn' );