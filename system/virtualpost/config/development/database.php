<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

// $active_group = 'default';
$domain_name  = isset($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
$active_group = $domain_name;
$active_record = TRUE;

/** Config DB for instance node: supper_admin */
$db['supper_admin']['hostname'] = getenv('MYSQL_DB_HOST');
$db['supper_admin']['username'] = getenv('MYSQL_USER');
$db['supper_admin']['password'] = getenv('MYSQL_PASSWORD');
$db['supper_admin']['database'] = getenv('MYSQL_DB_NAME');
$db['supper_admin']['dbdriver'] = 'mysqli';
$db['supper_admin']['dbprefix'] = '';
$db['supper_admin']['pconnect'] = FALSE;
$db['supper_admin']['db_debug'] = TRUE;
$db['supper_admin']['cache_on'] = FALSE;
$db['supper_admin']['cachedir'] = '';
$db['supper_admin']['char_set'] = 'utf8';
$db['supper_admin']['dbcollat'] = 'utf8_general_ci';
$db['supper_admin']['swap_pre'] = '';
$db['supper_admin']['autoinit'] = TRUE;
$db['supper_admin']['stricton'] = FALSE;

/** Config DB for instance node: clevvermail01 */
$db[$active_group]['hostname'] = getenv('MYSQL_DB_HOST');
$db[$active_group]['username'] = getenv('MYSQL_USER');
$db[$active_group]['password'] = getenv('MYSQL_PASSWORD');
$db[$active_group]['database'] = getenv('MYSQL_DB_NAME');
$db[$active_group]['hostname'] = 'localhost';
$db[$active_group]['username'] = 'root';
$db[$active_group]['password'] = '';
$db[$active_group]['database'] = 'clevvermail';
$db[$active_group]['dbdriver'] = 'mysqli';
$db[$active_group]['dbprefix'] = '';
$db[$active_group]['pconnect'] = FALSE;
$db[$active_group]['db_debug'] = TRUE;
$db[$active_group]['cache_on'] = FALSE;
$db[$active_group]['cachedir'] = '';
$db[$active_group]['char_set'] = 'utf8';
$db[$active_group]['dbcollat'] = 'utf8_general_ci';
$db[$active_group]['swap_pre'] = '';
$db[$active_group]['autoinit'] = TRUE;
$db[$active_group]['stricton'] = FALSE;

// Load database config from database

$CI =& get_instance();
$CI->supperadmin_db = $CI->load->database($db['supper_admin'], TRUE);
$CI->supperadmin_db =& $CI->supperadmin_db;
$query = $CI->supperadmin_db->select('instance_database.*');
$query = $CI->supperadmin_db->join('instance_domain', 'instance_database.instance_id = instance_domain.instance_id', 'inner');
$query = $CI->supperadmin_db->where('instance_domain.domain_name', $domain_name);
$db_config_row = $query->get('instance_database')->row();
if (empty($db_config_row)) {
    echo "System Error with domain '.$domain_name.' Please contact with administrator.";
    die();
}
$db[$active_group]['hostname'] = $db_config_row->host_address;
$db[$active_group]['username'] = $db_config_row->username;
$db[$active_group]['password'] = $db_config_row->password;
$db[$active_group]['database'] = $db_config_row->database_name;


/* End of file database.php */
/* Location: ./application/config/database.php */