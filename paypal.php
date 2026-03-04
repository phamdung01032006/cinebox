<?php 
// Include configuration file  
require_once("includes/config.php");
 
// Include the database connection file 
include_once 'dbConnect.php'; 
 
// Fetch plans from the database 
$sqlQ = "SELECT * FROM plans"; 
$stmt = $db->prepare($sqlQ); 
$stmt->execute(); 
$result = $stmt->get_result(); 
$hasPlans = ($result && $result->num_rows > 0);
 
// Get logged-in user ID from sesion 
// Session name need to be changed as per your system 
$loggedInUserID = !empty($_SESSION['userID'])?$_SESSION['userID']:0; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_SANDBOX?PAYPAL_SANDBOX_CLIENT_ID:PAYPAL_PROD_CLIENT_ID; ?>&vault=true&intent=subscription"></script>
</head>
<body>
<div class="container">
    <h1>PayPal subscriptions payment gateway integration</h1>

    <div class="panel">
    <div class="overlay hidden">
        <div class="overlay-content">
            <img src="css/loading.gif" alt="Processing..."/>
        </div>
    </div>

    <div class="panel-heading">
        <h3 class="panel-title">Subscription with PayPal</h3>
        
        <!-- Subscription Plan Info -->
        <div class="form-group">
            <label>Select Subscription Plan:</label>
            <select id="subscr_plan" class="form-control">
                <?php 
                if($hasPlans){ 
                    while($row = $result->fetch_assoc()){ 
                        $interval = $row['interval']; 
                        $interval_count = $row['intervalCount']; 
                        $interval_str = ($interval_count > 1)?$interval_count.' '.$interval.'s':$interval; 
                ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name'].' [$'.$row['price'].'/'.$interval_str.']'; ?></option>
                <?php 
                    } 
                }else{
                ?>
                    <option value="">No plans available</option>
                <?php
                }
                ?>
            </select>
        </div>
    </div>
    <div class="panel-body">
        <!-- Display status message -->
        <div id="paymentResponse" class="hidden"></div>
        
        <!-- Set up a container element for the button -->
        <div id="paypal-button-container"></div>
    </div>
</div>
</div>
<script>
paypal.Buttons({
    createSubscription: async (data, actions) => {
        setProcessing(true);

        // Get the selected plan ID
        let subscr_plan_id = document.getElementById("subscr_plan").value;
        
        if(!subscr_plan_id){
            setProcessing(false);
            resultMessage("No subscription plan found. Please add a plan in the database.");
            return false;
        }

        // Send request to the backend server to create subscription plan via PayPal API
        let postData = {request_type: 'create_plan', plan_id: subscr_plan_id};
        const PLAN_ID = await fetch("paypal_checkout_init.php", {
            method: "POST",
            headers: {'Accept': 'application/json'},
            body: encodeFormData(postData)
        })
        .then((res) => {
            return res.json();
        })
        .then((result) => {
            setProcessing(false);
            if(result.status == 1){
                return result.data.id;
            } else{
                resultMessage(result.msg);
                return false;
            }
        });

        if(!PLAN_ID){
            return false;
        }

        // Creates the subscription
        return actions.subscription.create({
            'plan_id': PLAN_ID,
            'custom_id': '<?php echo $loggedInUserID; ?>'
        });
    },
    onApprove: (data, actions) => {
        setProcessing(true);

        // Send request to the backend server to validate subscription via PayPal API
        var postData = {request_type:'capture_subscr', order_id:data.orderID, subscription_id:data.subscriptionID, plan_id: document.getElementById("subscr_plan").value};
        fetch('paypal_checkout_init.php', {
            method: 'POST',
            headers: {'Accept': 'application/json'},
            body: encodeFormData(postData)
        })
        .then((response) => response.json())
        .then((result) => {
            if(result.status == 1){
                // Redirect the user to the status page
                window.location.href = "payment-status.php?checkout_ref_id="+result.ref_id;
            }else{
                resultMessage(result.msg);
            }
            setProcessing(false);
        })
        .catch(error => console.log(error));
    }
}).render('#paypal-button-container');

// Helper function to encode payload parameters
const encodeFormData = (data) => {
  var form_data = new FormData();

  for ( var key in data ) {
    form_data.append(key, data[key]);
  }
  return form_data;   
}

// Show a loader on payment form processing
const setProcessing = (isProcessing) => {
    if (isProcessing) {
        document.querySelector(".overlay").classList.remove("hidden");
    } else {
        document.querySelector(".overlay").classList.add("hidden");
    }
}

// Display status message
const resultMessage = (msg_txt) => {
    const messageContainer = document.querySelector("#paymentResponse");

    messageContainer.classList.remove("hidden");
    messageContainer.textContent = msg_txt;
    
    setTimeout(function () {
        messageContainer.classList.add("hidden");
        messageContainer.textContent = "";
    }, 5000);
}    
</script>
</body>
</html>
