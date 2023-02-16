<?php
function sb_update_user_pw() {
	if (!is_user_logged_in()) 
		return;

	if (is_page('change-password')) {
		$curr_user = get_current_user_id();

		if (isset($_POST['submit'])) {
			if (!empty($_POST['password']) && !empty($_POST['uid'])){
                //$set_pwd = wp_set_password( $_POST['password'], $curr_user );
				$sb_pwd = $_POST['password'];
				$update_user = wp_update_user(array(
					'ID' => $curr_user,
					'user_pass' => $sb_pwd
						)
				);

                if($update_user){?>
                        <div class="alert alert-success" role="alert">
                            <strong>Password Updated Successfully!</strong> 
                        </div>
                <?php }
            } 
				
		}
	}
}

function account_change_pw_shortcode() {
	if (!is_user_logged_in())
		auth_redirect();

	ob_start();
	$curr_user = get_current_user_id();
	$login_name = new WP_User($curr_user); 
    sb_update_user_pw(); ?>
	<style>
		.input_wrapper.pw .toggle-password {
			color: #000;
			top: 60% !important;
		}
	</style>
	<div class="user_change_pw">
		<form id="change_pw" method="POST">
			<div class="input_wrapper uname">
				<label for="loginname">Username:</label>
				<input type="text" id="loginname" name="loginname" value="<?php echo $login_name->user_login ?>" disabled>
			</div>
			<div class="input_wrapper pw">
				<label for="pass">New Password:</label>
				<input type="password" id="pass" name="password" minlength="8" required>
				<span class="more_info">(8 characters minimum)</span>
			</div>
			
			<div class="input_wrapper submit">
				<input type="hidden" name="uid" value="<?php echo $login_name->ID ?>">
				<input type="submit" name="submit" value="Update">
			</div>

		</form>
		<!-- <div class="process_success" style="display:none;"></div> -->
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('sb_account_change_pw', 'account_change_pw_shortcode');