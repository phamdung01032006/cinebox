<?php

ob_start(); //turns on output buffering
session_start();

date_default_timezone_set("Asia/Ho_Chi_Minh");
// kết nối đến database có tên là cinebox ở mysql
try {
    $con = new PDO("mysql:dbname=cinebox;host=localhost", "root","");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
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
 
 
// Start session 
if(!session_id()){ 
    session_start(); 
}  
?>