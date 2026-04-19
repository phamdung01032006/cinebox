<?php

ob_start(); //turns on output buffering
session_start();

require_once(__DIR__ . "/language.php");

if(empty($_SESSION["siteLanguage"])) {
    setCurrentLanguage("en");
}

date_default_timezone_set("Asia/Ho_Chi_Minh");
// kết nối đến database có tên là cinebox ở mysql
try {
    $con = new PDO("mysql:dbname=cinebox;host=localhost;charset=utf8mb4", "root","");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $con->exec("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");
} catch(PDOException $e) {
    exit("Connection failed: ". $e->getMessage());
}

/* PayPal REST API configuration 
 * You can generate API credentials from the PayPal developer panel. 
 * See your keys here: https://developer.paypal.com/dashboard/ 
 */ 
define('PAYPAL_SANDBOX', TRUE); //TRUE=Sandbox | FALSE=Production 
define('PAYPAL_SANDBOX_CLIENT_ID', 'AXxzTKBZhfxhfwk4En9da8m-dzJjMKoE6W_91_pilRVq0j_k_Ptp4HGuy_Ni9fue7Mw2G3MKUG6yWxBn'); 
define('PAYPAL_SANDBOX_CLIENT_SECRET', 'ELINpi8xY2V-9V1HW8FCA9iyWJ_HA1Fv6mBbUL_ItYvHajyson6cJIg6UdvMgkOn5BW6WNeX8XYSwn8C'); 
define('PAYPAL_PROD_CLIENT_ID', 'Insert_Live_PayPal_Client_ID_Here'); 
define('PAYPAL_PROD_CLIENT_SECRET', 'Insert_Live_PayPal_Secret_Key_Here'); 
 
define('CURRENCY', 'USD');  
 
// Database configuration  
define('DB_HOST', 'localhost'); 
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', '');  
define('DB_NAME', 'cinebox'); 

$recommendationApiBaseUrl = getenv('RECOMMENDATION_API_BASE_URL');
if(!$recommendationApiBaseUrl) {
    $recommendationApiBaseUrl = 'http://127.0.0.1:8000';
}
define('RECOMMENDATION_API_BASE_URL', rtrim($recommendationApiBaseUrl, '/'));

// Listing performance tuning
if(!defined("HOME_CATEGORY_ROW_LIMIT")) {
    define("HOME_CATEGORY_ROW_LIMIT", 8);
}
if(!defined("BROWSE_CATEGORY_ROW_LIMIT")) {
    define("BROWSE_CATEGORY_ROW_LIMIT", 12);
}
if(!defined("CATEGORY_PAGE_ROW_LIMIT")) {
    define("CATEGORY_PAGE_ROW_LIMIT", 30);
}
if(!defined("CONTINUE_WATCHING_LIMIT")) {
    define("CONTINUE_WATCHING_LIMIT", 12);
}
if(!defined("RECOMMENDATION_ROW_LIMIT")) {
    define("RECOMMENDATION_ROW_LIMIT", 12);
}
if(!defined("HOMEPAGE_MAX_CATEGORIES")) {
    define("HOMEPAGE_MAX_CATEGORIES", 8);
}
 
 
// Start session 
if(!session_id()){ 
    session_start(); 
}  
?>
