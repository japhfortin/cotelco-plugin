<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function cotelco_account_found($acc_no) {
	global $wpdb;
	$tbl_accounts = $wpdb->prefix . 'cotelco_accounts';
	$tbl_ledger = $wpdb->prefix . 'cotelco_ledger';
	$query = $wpdb->prepare("SELECT COUNT(*) FROM $tbl_accounts WHERE account_no = %s", $acc_no);
	$count = $wpdb->get_var( $query );
	if ($count == 1) {
		return true;
	}
	return false;
}

function cotelco_is_account_registered($acc_no) {
    $args = array(
    	'meta_key' => 'cotelco_account_no',
    	'meta_value' => $acc_no
    );
    $user = get_users($args);
    if (count($user) > 0) {
    	return true;
    }
    return false;
}

/*function cotelco_check_latest_month($acc_no, $input_date) {
	global $wpdb;
	// $c_acc_no = get_usermeta( get_current_user_id(), $meta_key = 'cotelco_account_no' );
	$tbl_ledger = $wpdb->prefix . 'cotelco_ledger';
	$query = $wpdb->prepare("SELECT account_no, MAX(date) AS date, reference, kwhused, debit, credit, balance FROM $tbl_ledger WHERE account_no = %s", $acc_no);
	$c_latest_account = $wpdb->get_row($query);

	$date_input = strtotime($input_date . '/01 ');
	$latest_date = strtotime($c_latest_account->date);

    if (date('Y-m', $date_input) == date('Y-m', $latest_date)) {
    	var_dump('yes');
    } else {
    	var_dump('no');
    }


}*/