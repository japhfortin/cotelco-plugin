<?php 
// registration form
function cotelco_registration_form() {
    // wp core registered scripts
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'jquery-mask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js', array(), '1.0.0', true );
    wp_enqueue_script( 'cp-shortcodes', CP_PLUGIN_URL . 'js/shortcodes.js', array(), '1.0.0', true );

    wp_enqueue_style( 'cp-shortcodes',  CP_PLUGIN_URL . "css/shortcodes.css");
    wp_enqueue_style( 'jquery-ui-datepicker', 'http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

    ob_start();
    global $wpdb, $user_ID;
    
    if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {  
	   
	    $errors = array();  
        // Check username is present and not already in use  
        $username = sanitize_text_field($_REQUEST['username']);  
        if ( strpos($username, ' ') !== false ) {
            $errors['username'] = "Sorry, no spaces allowed in usernames";  
        }  
        if(empty($username))  {   
            $errors['username'] = "Please enter a username";  
        } elseif( username_exists( $username ) ) {  
            $errors['username'] = "Username already exists, please try another";  
        }  
   
        // Check email address is present and valid  
        $email = sanitize_email($_REQUEST['email']); 
        if( !is_email( $email ) ) {   
            $errors['email'] = "Please enter a valid email";  
        } elseif( email_exists( $email ) ) {  
            $errors['email'] = "This email address is already in use";  
        }
   
        // Check password is valid  
        if(0 === preg_match("/.{8,}/", $_POST['password'])) {  
          $errors['password'] = "Password must be at least eight characters";  
        }  
   
        // Check password confirmation_matches  
        if(0 !== strcmp($_POST['password'], $_POST['password_confirmation'])) {  
          $errors['password_confirmation'] = "Passwords do not match";  
        }

        // check if account no valid
        $account_no = sanitize_text_field($_POST['account_no']);
        if(empty($account_no))  {   
            $errors['account_no'] = "Please enter your account number";  
        }

        if (!cotelco_account_found($account_no)) {
        	$errors['account_no'] = "Sorry, we can't find your account on our record";
        }

        if (cotelco_is_account_registered($account_no)) {
        	$errors['account_no'] = "Sorry, your account is already registered";
        }
   		
        // check billing month
        $billing_month = sanitize_text_field($_POST['billing_month']);
        if(empty($billing_month))  {   
            $errors['billing_month'] = "Please enter your billing month";  
        }

        $tbl_ledger = $wpdb->prefix . 'cotelco_ledger';
		$query = $wpdb->prepare("SELECT account_no, date, reference, kwhused, debit, credit, balance FROM $tbl_ledger WHERE account_no = %s ORDER BY date DESC LIMIT 1", $account_no);
		$c_latest_account = $wpdb->get_row($query);

		$date_input = strtotime($billing_month . '/01 ');
		$latest_date = strtotime($c_latest_account->date);

		if (date('Y-m', $date_input) != date('Y-m', $latest_date)) {
			$errors['billing_month'] = "Please enter your latest billing month";  
		}

   		// check billing month
        if(empty(sanitize_text_field($_POST['payment_date'])))  {   
            $errors['payment_date'] = "Please enter your payment date";  
        }

        // check latest payment or
        $payment_or = sanitize_text_field($_POST['payment_or']);
        if(empty($payment_or))  {   
            $errors['payment_or'] = "Please enter your payment official receipt";  
        }

        if ($payment_or != $c_latest_account->reference) {
        	$errors['payment_or'] = "Please enter your latest payment official receipt";  
        }

        // Check terms of service is agreed to  
        if($_POST['terms'] != "Yes") {  
            $errors['terms'] = "You must agree to Terms of Service";  
        } 
        
        if(0 === count($errors)) {  
            $password = $_POST['password'];  
   			$fname = sanitize_text_field($_POST['first_name']);
   			$lname = sanitize_text_field($_POST['last_name']);
            // $new_user_id = wp_create_user( $username, $password, $email );
            $new_user_id = wp_insert_user(
				array(
					'user_login'	=>	$username,
					'user_pass'	=>	$password,
					'first_name'	=>	$fname,
					'last_name'	=>	$lname,
					'user_email'	=>	$email,
					'display_name'	=>	$fname . ' ' . $lname,
				)
			);
            update_user_meta( $new_user_id, 'cotelco_account_no', sanitize_text_field($_POST['account_no']) );
            update_user_meta( $new_user_id, 'cotelco_billing_month', sanitize_text_field($_POST['billing_month']) );
            update_user_meta( $new_user_id, 'cotelco_payment_date', sanitize_text_field($_POST['payment_date']) );
            update_user_meta( $new_user_id, 'cotelco_payment_or', sanitize_text_field($_POST['payment_or']) );
   
            // You could do all manner of other things here like send an email to the user, etc. I leave that to you.  
   
            $success = 1;
            wp_new_user_notification($new_user_id);
   			?>
   			<div class="alert alert-success">
				<strong>Thank you!</strong> You can now log in to our website. You will receive an email regarding your login information shortly.
			</div>
   			<?php
   			// clear post array
            $_POST = array();
        } 

        else {
            ?>
            <div class="alert alert-danger fade in alert-dismissible show">
				<ul>
					<?php foreach ($errors as $key => $value): ?>
						<li><?php echo $value; ?></li>
					<?php endforeach ?>
				</ul>
			</div>
            <?php
        }
   
    }  

  
?>  
	<form id="cotelco_signup_form" class="form-cotelco" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<h3>Personal Information</h3>
		<div class="form-group <?php echo (!empty($errors['username'])) ? 'error' : '' ?>">
			<label for="username">Username</label>  
			<input class="form-control" type="text" name="username" id="username" value="<?php echo @$_POST['username']?>">
		</div>
		<div class="form-group <?php echo (!empty($errors['email'])) ? 'error' : '' ?>">
			<label for="email">Email address</label>  
			<input class="form-control" type="text" name="email" id="email" value="<?php echo @$_POST['email']?>">  
		</div>
		<div class="form-group <?php echo (!empty($errors['first_name'])) ? 'error' : '' ?>">
			<label for="first_name">First Name</label>  
			<input class="form-control" type="text" name="first_name" id="first_name" value="<?php echo @$_POST['first_name']?>">  
		</div>
		<div class="form-group <?php echo (!empty($errors['last_name'])) ? 'error' : '' ?>">
			<label for="last_name">Last Name</label>  
			<input class="form-control" type="text" name="last_name" id="last_name" value="<?php echo @$_POST['last_name']?>">  
		</div>
		<div class="form-group <?php echo (!empty($errors['password'])) ? 'error' : '' ?>">
			<label for="password">Password</label>  
			<input class="form-control" type="password" name="password" id="password">  
		</div>
		<div class="form-group <?php echo (!empty($errors['password_confirmation'])) ? 'error' : '' ?>">
			<label for="password_confirmation">Confirm Password</label>  
			<input class="form-control" type="password" name="password_confirmation" id="password_confirmation">  
		</div>
		<h3>Cotelco Account Information</h3>
		<div class="form-group <?php echo (!empty($errors['account_no'])) ? 'error' : '' ?>">
			<label for="account_no">Account No</label>  
			<input class="form-control" type="text" name="account_no" id="account_no" value="<?php echo @$_POST['account_no']?>">  
		</div>
		<div class="form-group <?php echo (!empty($errors['billing_month'])) ? 'error' : '' ?>">
			<label for="billing_month">Billing Month</label>  
			<input class="form-control" type="text" placeholder="yyyy/mm" name="billing_month" id="billing_month" value="<?php echo @$_POST['billing_month']?>">  
		</div>
		<div class="form-group <?php echo (!empty($errors['payment_date'])) ? 'error' : '' ?>">
			<label for="payment_date">Payment Date</label>  
			<input class="form-control" type="text" placeholder="mm/dd/yyyy" name="payment_date" id="payment_date" value="<?php echo @$_POST['payment_date']?>">  
		</div>
		<div class="form-group <?php echo (!empty($errors['payment_or'])) ? 'error' : '' ?>">
			<label for="payment_or">Payment OR</label>  
			<input class="form-control" type="text" name="payment_or" id="payment_or" value="<?php echo @$_POST['payment_or']?>">  
		</div>
		<div class="form-group <?php echo (!empty($errors['terms'])) ? 'error' : '' ?>">
			<input name="terms" id="terms" type="checkbox" value="Yes">  
			<label for="terms">I agree to the Terms of Service</label>  
		</div>
		<div class="et_pb_button_wrapper et_pb_text_align_right">
			<input class="et_pb_button" type="submit" id="submitbtn" name="submit" value="Sign Up">  
		</div>
	</form>  
  <?php
    return ob_get_clean();
}
add_shortcode('cotelco_register', 'cotelco_registration_form');

function cotelco_login_form() {
	if ( is_user_logged_in() ) {
		return;
	}
	
	ob_start();
	?>
	<form name="loginform" id="loginform" class="form-cotelco" action="/wp-login.php" method="post">
			<div class="form-group">
				<p class="login-username">
					<label for="user_login">Username or Email Address</label>
					<input class="form-control" type="text" name="log" id="user_login" class="input" value="" size="20">
				</p>
			</div>
			<div class="form-group">
				<p class="login-password">
					<label for="user_pass">Password</label>
					<input class="form-control" type="password" name="pwd" id="user_pass" class="input" value="" size="20">
				</p>
			</div>
			
			<p class="login-remember"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember Me</label></p>

			<div class="et_pb_button_wrapper et_pb_text_align_right"> 
				<p class="login-submit">
					<input type="submit" name="wp-submit" id="wp-submit" class="et_pb_button button button-primary" value="Log In">
					<input type="hidden" name="redirect_to" value="<?php echo get_option('siteurl') . '/consumer-bill/' ?>">
				</p>
			</div>
			
		</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'cotelco_login', 'cotelco_login_form' );

function cotelco_consumer_bill_table() {
	global $wpdb;
	$tbl_accounts = $wpdb->prefix . 'cotelco_accounts';
	$tbl_ledger = $wpdb->prefix . 'cotelco_ledger';
	
	// get account info
	$c_acc_no = get_usermeta( get_current_user_id(), $meta_key = 'cotelco_account_no' );
	$query = $wpdb->prepare("SELECT * FROM $tbl_accounts WHERE account_no = %s", $c_acc_no);
	$c_accounts = $wpdb->get_row($query);


	$query = $wpdb->prepare("SELECT * FROM $tbl_ledger WHERE account_no = %s", $c_acc_no);
	$c_account_ledger = $wpdb->get_results($query);
	// get account ledger
	ob_start();
	if (is_user_logged_in() && !empty($c_acc_no)) {
		?>
		<table class="table-cotelco">
			<thead>
				<tr>
					<th colspan="4">Account Information</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>Account No: </th>
					<td><?php echo $c_accounts->account_no; ?></td>
					<th>Status: </th>
					<td><?php echo $c_accounts->status; ?></td>
				</tr>
				<tr>
					<th>Name: </th>
					<td><?php echo $c_accounts->name; ?></td>
					<th>MID: </th>
					<td><?php echo $c_accounts->mid; ?></td>
				</tr>
				<tr>
					<th>Address: </th>
					<td><?php echo$c_accounts->address; ?></td>
					<th>District: </th>
					<td><?php echo $c_accounts->district; ?></td>
				</tr>
				<tr>
					<th>Type: </th>
					<td><?php echo $c_accounts->type; ?></td>
					<th>BAPA: </th>
					<td><?php echo ($c_accounts->bapa) ? 'YES' : 'NO'; ?></td>
				</tr>
			</tbody>
		</table>

		<?php if (count($c_account_ledger) > 0): ?>
			<table class="table-cotelco">
				<thead>
					<tr>
						<th colspan="6">Ledger</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>Date</th>
						<th>Reference</th>
						<th>KWH Used</th>
						<th>Debit</th>
						<th>Credit</th>
						<th>Balance</th>
					</tr>
					<?php foreach ($c_account_ledger as $i => $item): ?>
						<tr>
							<td><?php echo $item->date; ?></td>
							<td><?php echo $item->reference; ?></td> 
							<td><?php echo $item->kwhused; ?></td>
							<td><?php echo $item->debit; ?></td>
							<td><?php echo $item->credit; ?></td>
							<td><?php echo $item->balance; ?></td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		<?php endif ?>
		<?php
	} else {
		echo "Please login or register to view consumer bill.";
	}
	return ob_get_clean();
}
add_shortcode( 'cotelco_bill', 'cotelco_consumer_bill_table' );