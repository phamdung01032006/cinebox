<?php 
// Include the configuration file  
require_once("includes/config.php");
require_once("includes/header.php");
// Include the database connection file  
require_once 'dbConnect.php'; 
 
$statusMsg = ''; 
$status = 'error'; 
 
// Check whether the DB reference ID is not empty 
if(!empty($_GET['checkout_ref_id'])){ 
    $paypal_order_id  = base64_decode($_GET['checkout_ref_id']); 
     
    // Fetch subscription data from the database 
    $sqlQ = "SELECT 
                    S.*,
                    P.name AS planName,
                    P.price AS planPrice,
                    P.interval,
                    P.intervalCount
                FROM user_subscriptions AS S
                LEFT JOIN plans AS P ON P.id = S.planId
                WHERE S.paypalOrderId = ?"; 
    $stmt = $db->prepare($sqlQ);  
    $stmt->bind_param("s", $paypal_order_id); 
    $stmt->execute(); 
    $result = $stmt->get_result(); 
 
    if($result->num_rows > 0){ 
        $subscr_data = $result->fetch_assoc(); 
         
        $status = 'success'; 
        $statusMsg = 'Your Subscription Payment has been Successful!'; 
    }else{ 
        $statusMsg = "Subscription has been failed!"; 
    } 
}else{ 
    header("Location: index.php"); 
    exit; 
} 
?>
<link rel="stylesheet" href="assets/style/paypal.css">
<div class="container">
  
<div class="card cart">
    <?php if(!empty($subscr_data)){ ?>
    <h1 class="<?php echo $status; ?>"><?php echo $statusMsg; ?></h1>  
    <label class="title">PayPal Subscription</label>
    <div class="steps">
        <div class="step">
        <div>
            <span>PLANS</span>
                <p><b>Reference Number:</b> #<?php echo $subscr_data['id']; ?></p>
                <p><b>Subscription ID:</b> <?php echo $subscr_data['paypalSubscrId']; ?></p>
                <p><b>TXN ID:</b> <?php echo $subscr_data['paypalOrderId']; ?></p>
                <p><b>Paid Amount:</b> <?php echo $subscr_data['paidAmount'].' '.$subscr_data['currencyCode']; ?></p>
                <p><b>Status:</b> <?php echo $subscr_data['status']; ?></p>
        </div>
        <hr />
        <div>
            <span>Subscription Information</span>
                <p><b>Plan Name:</b> <?php echo $subscr_data['planName']; ?></p>
                <p><b>Amount:</b> <?php echo $subscr_data['planPrice'].' '.CURRENCY; ?></p>
                <p><b>Plan Interval:</b> <?php echo $subscr_data['intervalCount'].$subscr_data['interval']; ?></p>
                <p><b>Period Start:</b> <?php echo $subscr_data['validFrom']; ?></p>
                <p><b>Period End:</b> <?php echo $subscr_data['validTo']; ?></p>
        </div>
        <hr />
        <div class="promo">
            <span>Payer Information</span>
                <p><b>Name:</b> <?php echo $subscr_data['subscriberName']; ?></p>
                <p><b>Email:</b> <?php echo $subscr_data['subscriberEmail']; ?></p>
            <?php }else{ ?>
                <h1 class="error">Your Subscription failed!</h1>
                <p class="error"><?php echo $statusMsg; ?></p>
            <?php } ?>
        </div>
    </div>
    </div>

    <div class="card checkout">
    <div class="footer">
        <label class="price">Watch unlimited films and movies</label>
        <button type="button" class="animatedGoButton" onclick="window.location.href='index.php'">
            <svg xmlns="http://www.w3.org/2000/svg" class="arr-2" viewBox="0 0 24 24">
                <path
                d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                ></path>
            </svg>
            <span class="text">Go</span>
            <span class="circle"></span>
            <svg xmlns="http://www.w3.org/2000/svg" class="arr-1" viewBox="0 0 24 24">
                <path
                d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                ></path>
            </svg>
        </button>
    </div>
    </div>
</div>

<?php require_once("footer.php"); ?>