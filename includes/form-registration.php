<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Back end registration
 */

// adding new user form
add_action( 'user_new_form', 'cotelco_admin_registration_form' );
function cotelco_admin_registration_form( $operation ) {
	if ( 'add-new-user' !== $operation ) {
		// $operation may also be 'add-existing-user'
		return;
	}

	$account_no = ! empty( $_POST['account_no'] ) ? addslashes($_POST['account_no']) : '';

	?>
	<h3><?php esc_html_e( 'Cotelco Account Information', 'cotelco' ); ?></h3>

	<table class="form-table">
		<tr>
			<th><label for="account_no"><?php esc_html_e( 'Account No', 'cotelco' ); ?></label> <span class="description"><?php esc_html_e( '(required)', 'cotelco' ); ?></span></th>
			<td>
				<input type="text"
			       id="account_no"
			       name="account_no"
			       value="<?php echo esc_attr( $account_no ); ?>"
			       class="regular-text"
				/>
			</td>
		</tr>
	</table>
	<?php
}

// validating the field
add_action( 'user_profile_update_errors', 'cotelco_user_profile_update_errors', 10, 3 );
function cotelco_user_profile_update_errors( $errors, $update, $user ) {
	if ( $update ) {
		return;
	}

	if ( empty( $_POST['account_no'] ) ) {
		$errors->add( 'account_no_error', __( '<strong>ERROR</strong>: Please enter your account number.', 'cotelco' ) );
	}
}


// sanitizing and saving the field
add_action( 'user_register', 'cotelco_user_register' );
function cotelco_user_register( $user_id ) {
	if ( ! empty( $_POST['account_no'] ) ) {
		update_user_meta( $user_id, 'cotelco_account_no', addslashes($_POST['account_no']) );
	}
}

add_action( 'edit_user_created_user', 'cotelco_user_register' );


// show or edit user display
add_action( 'show_user_profile', 'cotelco_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'cotelco_show_extra_profile_fields' );

function cotelco_show_extra_profile_fields( $user ) {
	?>
	<h3><?php esc_html_e( 'Cotelco Account Information', 'cotelco' ); ?></h3>

	<table class="form-table">
		<tr>
			<th><label for="cotelco_account_no"><?php esc_html_e( 'Account No', 'cotelco' ); ?></label></th>
			<td><?php echo esc_html( get_the_author_meta( 'cotelco_account_no', $user->ID ) ); ?></td>
		</tr>
		<tr>
			<th><label for="cotelco_billing_month"><?php esc_html_e( 'Billing Month', 'cotelco' ); ?></label></th>
			<td><?php echo esc_html( get_the_author_meta( 'cotelco_billing_month', $user->ID ) ); ?></td>
		</tr>
		<tr>
			<th><label for="cotelco_payment_date"><?php esc_html_e( 'Payment Date', 'cotelco' ); ?></label></th>
			<td><?php echo esc_html( get_the_author_meta( 'cotelco_payment_date', $user->ID ) ); ?></td>
		</tr>
		<tr>
			<th><label for="cotelco_payment_or"><?php esc_html_e( 'Payment OR', 'cotelco' ); ?></label></th>
			<td><?php echo esc_html( get_the_author_meta( 'cotelco_payment_or', $user->ID ) ); ?></td>
		</tr>
	</table>
	<?php
}