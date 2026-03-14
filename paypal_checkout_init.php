<?php  
// Include the configuration file  
require_once("includes/config.php");
// Include the database connection file  
include_once 'dbConnect.php';  
  
// Include the PayPal API library  
require_once 'PaypalCheckout.class.php';  
$paypal = new PaypalCheckout;  
  
$response = array('status' => 0, 'msg' => 'Request Failed!');  
$api_error = '';  
if(!empty($_POST['request_type']) && $_POST['request_type'] == 'create_plan'){  
    $plan_id = !empty($_POST['plan_id']) ? (int)$_POST['plan_id'] : 0;  
    if($plan_id <= 0){ 
        $response['msg'] = 'Invalid subscription plan selected.'; 
        echo json_encode($response); 
        exit; 
    } 
 
    // Fetch plan details from the database  
    $sqlQ = "SELECT `name`,`price`,`interval`,`intervalCount` FROM plans WHERE id=?";  
    $stmt = $db->prepare($sqlQ);  
    $stmt->bind_param("i", $plan_id);  
    $stmt->execute();  
    $stmt->bind_result($planName, $planPrice, $planInterval, $intervalCount);  
    if(!$stmt->fetch()){ 
        $response['msg'] = 'Selected plan not found. Please add a plan in the database.'; 
        echo json_encode($response); 
        exit; 
    } 
    $stmt->close();  
 
    $planInterval = strtoupper(trim((string)$planInterval)); 
    $interval_map = array( 
        'DAY' => 'DAY', 
        'DAILY' => 'DAY', 
        'WEEK' => 'WEEK', 
        'WEEKLY' => 'WEEK', 
        'MONTH' => 'MONTH', 
        'MONTHLY' => 'MONTH', 
        'YEAR' => 'YEAR', 
        'YEARLY' => 'YEAR' 
    ); 
    $planInterval = !empty($interval_map[$planInterval]) ? $interval_map[$planInterval] : ''; 

    if(empty($planName) || !is_numeric($planPrice) || empty($planInterval) || (int)$intervalCount <= 0){ 
        $response['msg'] = 'Plan data is incomplete. Please check name, price, interval, and interval_count.'; 
        echo json_encode($response); 
        exit; 
    } 

    $plan_data = array( 
        'name' => $planName,  
        'price' => $planPrice,  
        'interval' => $planInterval,  
        'interval_count' => $intervalCount,  
    );  

    // Create plan with PayPal API  
    try {  
        $subscr_plan = $paypal->createPlan($plan_data);  
    } catch(Exception $e) {   
        $api_error = $e->getMessage();   
    }  

    if(!empty($subscr_plan)){  
        $response = array(  
            'status' => 1,   
            'data' => $subscr_plan  
        );  
    }else{  
        $response['msg'] = $api_error;  
    }  
}elseif(!empty($_POST['request_type']) && $_POST['request_type'] == 'capture_subscr'){  
    $order_id = $_POST['order_id'];  
    $subscription_id = $_POST['subscription_id']; 
    $db_plan_id = $_POST['plan_id'];  

    // Fetch & validate subscription with PayPal API  
    try {  
        $subscr_data = $paypal->getSubscription($subscription_id);  
    } catch(Exception $e) {   
        $api_error = $e->getMessage();   
    }  

    if(!empty($subscr_data)){  
        $status = $subscr_data['status'];  
        $subscr_id = $subscr_data['id'];  
        $plan_id = $subscr_data['plan_id'];  
        $custom_user_id = $subscr_data['custom_id']; 

        $create_time = $subscr_data['create_time'];  
        $dt = new DateTime($create_time);  
        $created = $dt->format("Y-m-d H:i:s"); 

        $start_time = $subscr_data['start_time'];  
        $dt = new DateTime($start_time);  
        $valid_from = $dt->format("Y-m-d H:i:s"); 

        if(!empty($subscr_data['subscriber'])){ 
            $subscriber = $subscr_data['subscriber']; 
            $subscriber_email = $subscriber['email_address']; 
            $subscriber_id = $subscriber['payer_id']; 
            $given_name = trim($subscriber['name']['given_name']); 
            $surname = trim($subscriber['name']['surname']); 
            $subscriber_name = trim($given_name.' '.$surname); 
        } 

        // custom_id must be users.id (sent from paypal.php)
        if (empty($custom_user_id)) {
            $response['msg'] = 'Missing custom_id (users.id).';
            echo json_encode($response);
            exit;
        }

        // Validate that custom_id exists in users table
        $sqlQ = "SELECT id FROM users WHERE id=? LIMIT 1";
        $stmt = $db->prepare($sqlQ);
        $stmt->bind_param("i", $custom_user_id);
        $stmt->execute();
        $stmt->bind_result($uid);
        if (!$stmt->fetch()) {
            $stmt->close();
            $response['msg'] = 'User not found in users table.';
            echo json_encode($response);
            exit;
        }
        $stmt->close();

        if(!empty($subscr_data['billing_info'])){ 
            $billing_info = $subscr_data['billing_info']; 

            if(!empty($billing_info['outstanding_balance'])){ 
                $outstanding_balance_value = $billing_info['outstanding_balance']['value']; 
                $outstanding_balance_curreny = $billing_info['outstanding_balance']['currency_code']; 
            } 

            if(!empty($billing_info['last_payment'])){ 
                $last_payment_amount = $billing_info['last_payment']['amount']['value']; 
                $last_payment_curreny = $billing_info['last_payment']['amount']['currency_code']; 
            } 

            $next_billing_time = $billing_info['next_billing_time']; 
            $dt = new DateTime($next_billing_time);  
            $valid_to = $dt->format("Y-m-d H:i:s"); 
        } 

        if(!empty($subscr_id) && $status == 'ACTIVE'){  
            // Check if any subscription data exists with the same ID  
            $sqlQ = "SELECT id FROM user_subscriptions WHERE paypalOrderId = ?";  
            $stmt = $db->prepare($sqlQ);   
            $stmt->bind_param("s", $order_id);  
            $stmt->execute();  
            $stmt->bind_result($row_id);  
            $stmt->fetch();  

            $payment_id = 0;  
            if(!empty($row_id)){  
                $payment_id = $row_id; 
            }else{  
                // Insert subscription data into the database  
                $sqlQ = "INSERT INTO user_subscriptions (userId,planId,paypalOrderId,paypalPlanId,paypalSubscrId,validFrom,validTo,paidAmount,currencyCode,subscriberId,subscriberName,subscriberEmail,status,created,modified) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())";  
                $stmt = $db->prepare($sqlQ);  
                $stmt->bind_param("iisssssdssssss", $custom_user_id, $db_plan_id, $order_id, $plan_id, $subscr_id, $valid_from, $valid_to, $last_payment_amount, $last_payment_curreny, $subscriber_id, $subscriber_name, $subscriber_email, $status, $created);  
                $insert = $stmt->execute();

                if($insert){  
                    $user_subscription_id = $stmt->insert_id;
                }

            }

            if(!empty($user_subscription_id)){  
                $ref_id_enc = base64_encode($order_id);  
                $response = array('status' => 1, 'msg' => 'Subscription created!', 'ref_id' => $ref_id_enc); 
            }  
        }
        // Cập nhật dòng isSubscribed ở bảng users thành 1 để biết người đó đã đăng ký
        // 1. Kiểm tra bảng user_subscriptions trước
        $checkSql = "SELECT status FROM user_subscriptions WHERE userId = ? ORDER BY id DESC LIMIT 1";
        $checkStmt = $db->prepare($checkSql);
        $checkStmt->bind_param("s", $custom_user_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $payment = $result->fetch_assoc();

        // 2. Nếu trạng thái là 'ACTIVE' thì mới cho phép UPDATE users
        if ($payment && $payment['status'] === 'ACTIVE') {
            $updateSql = "UPDATE users SET isSubscribed = 1 WHERE id = ?";
            $updateStmt = $db->prepare($updateSql);
            $updateStmt->bind_param("s", $custom_user_id);
            $updateStmt->execute();
            $response['msg'] = "Đã nâng cấp tài khoản.";
        }
        else {
            $response['msg'] = "Thanh toán chưa hoàn tất hoặc không tìm thấy hóa đơn.";
        } 
    }else{  
        $response['msg'] = $api_error;  
    }  
}  
echo json_encode($response);  
?> 
