<?php 
// Include the configuration file  
require_once("includes/config.php");
 
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

<?php if(!empty($subscr_data)){ ?>
    <h1 class="<?php echo $status; ?>"><?php echo $statusMsg; ?></h1>

    <h4>Payment Information</h4>
    <p><b>Reference Number:</b> #<?php echo $subscr_data['id']; ?></p>
    <p><b>Subscription ID:</b> <?php echo $subscr_data['paypalSubscrId']; ?></p>
    <p><b>TXN ID:</b> <?php echo $subscr_data['paypalOrderId']; ?></p>
    <p><b>Paid Amount:</b> <?php echo $subscr_data['paidAmount'].' '.$subscr_data['currencyCode']; ?></p>
    <p><b>Status:</b> <?php echo $subscr_data['status']; ?></p>
    
    <h4>Subscription Information</h4>
    <p><b>Plan Name:</b> <?php echo $subscr_data['planName']; ?></p>
    <p><b>Amount:</b> <?php echo $subscr_data['planPrice'].' '.CURRENCY; ?></p>
    <p><b>Plan Interval:</b> <?php echo $subscr_data['intervalCount'].$subscr_data['interval']; ?></p>
    <p><b>Period Start:</b> <?php echo $subscr_data['validFrom']; ?></p>
    <p><b>Period End:</b> <?php echo $subscr_data['validTo']; ?></p>
    
    <h4>Payer Information</h4>
    <p><b>Name:</b> <?php echo $subscr_data['subscriberName']; ?></p>
    <p><b>Email:</b> <?php echo $subscr_data['subscriberEmail']; ?></p>
<?php }else{ ?>
    <h1 class="error">Your Subscription failed!</h1>
    <p class="error"><?php echo $statusMsg; ?></p>
<?php } ?>