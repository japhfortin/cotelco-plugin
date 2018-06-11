# Cotelco Plugin

*	Name: Cotelco Plugin
*	Description: Additional codes for cotelco website
*	Plugin URI: https://github.com/japhfortin/cotelco-plugin

We created a plugin that stores the code for additional WordPress shortcodes, and codes to create additional tables for us to store the cotelco user accounts and ledgers.

### The Shortcodes:
* [cotelco_register] - Creates a form in which user can register. In order for the user to be registered as user of the website, the following filters must be complied:
    * email address: Must be unique and valid
    * password: Minimum of 8 characters
    * confirm password: should be equal to passwordt
    * account no: Must be unique and present on the existing cotelco user accounts table
    * billing month: Should correspond to latest billing month in the database
    * payment date: Should correspond to latest payment date in the database
    * payment OR: Should correspond to latest payment OR in the database
* [cotelco_login] – Creates a login form where user can login.
* [cotelco_bill] – Creates a table in which user can view his account and ledger.

### The Tables:
* Accounts - this table will have the following fields:
	* 	account_no
	*	name
	*	address
	*	meter
	*	type
	*	status
	*	mid
	*	district
	*	bapa
* Ledger - this table will have the following fields:
	*	account_no
	*	date
	*	reference
	*	kwhused
	*	debit
	*	credit
	*	balance








