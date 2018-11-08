<?php
define("DEFAULT_LANGUAGE", "en");

// Array of available languages
$arr_active_languages = array(
    "en" => "English",
    "bg" => "Български",
    "es" => "Spanish",
    "de" => "German"
);

// Force database creation
define("DATABASE_CREATE", false);

// Define database type
// To check installed drivers use: print_r(PDO::getAvailableDrivers());
//     mysql          - MySql,
//     pgsql          - PostgreSQL
//     sqlite/sqlite2 - SQLite 
//     oci            - Oracle
//     cubrid         - Cubrid
//     firebird       - Firebird/Interbase 6
//     dblib          - FreeTDS / Microsoft SQL Server / Sybase
//     ibm            - IBM DB2
//     informix       - IBM Informix Dynamic Server
//     odbc           - ODBC v3 (IBM DB2, unixODBC and win32 ODBC)
define("DATABASE_TYPE", "mysql");

// Config file directory - Directory, where config file must be
define("CONFIG_FILE_DIRECTORY", "../");

// Config file name - Output file with config parameters (database, username etc.)
define("CONFIG_FILE_NAME", "config.php");

// According to directory hierarchy (you may add/remove "../" before CONFIG_FILE_DIRECTORY)
define("CONFIG_FILE_PATH", CONFIG_FILE_DIRECTORY . CONFIG_FILE_NAME);

// Config file name - config template file name
define("CONFIG_FILE_TEMPLATE", "config.tpl");

// SQL dump file - file that includes SQL statements for installation
if (!isset($_SESSION['client']) || $_SESSION['client'] == 'No') {
    define("SQL_DUMP_FILE_CREATE", "sql/database-main.sql");
} else {
    define("SQL_DUMP_FILE_CREATE", "sql/database-client.sql");
}

// Defines using of utf-8 encoding and collation for SQL dump file
define("USE_ENCODING", true);
define("DUMP_FILE_ENCODING", "utf8");
define("DUMP_FILE_COLLATION", "utf8_unicode_ci");
?>