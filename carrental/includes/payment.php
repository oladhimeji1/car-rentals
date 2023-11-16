<?php
    session_start();
    include('includes/config.php');
    error_reporting(1);
    
    // PHP code to handle the function call
    $vid = 0;
    if (isset($_POST['functionName'])) {
        $functionName = $_POST['functionName'];
        // echo "<script>console.log('normal:come');</script>";
        
        // Check which function to call
        if ($functionName === 'makePayment') {
            $useremail=strval($_SESSION['login']);
            $price = intval($_SESSION['Price']);
            $price = $price . '00';
            $vid = intval($_SESSION['vid']);
            
    echo "<script>console.log('$price');</script>";
            makePayment($price,$useremail);
            // nametest();
            // makePayment(70000, 'ola1@gmail.com');
        }
    }
    
   
    
    function makePayment($price,$useremail){
        // echo "<script>console.log($price)</script>";
    $url = "https://api.paystack.co/transaction/initialize";

    $fields = [
        'email' => "$useremail",
        'amount' => "$price",
    ];


    $fields_string = http_build_query($fields);

    //open connection
    $ch = curl_init();
    
    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer sk_test_9a2bc987d1afa64bcda7497d615ad15d05ba87e5",
        "Cache-Control: no-cache",
    ));
    
    //So that curl_exec returns the contents of the cURL; rather than echoing it
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
    
    //execute post
    $result = curl_exec($ch);

    $responseObject = json_decode($result, false);
    
    $reference = $responseObject->data->reference;

    // Check if the status is true
    if ($responseObject->status == 1) {
        // Get the authorization URL
        $authorizationUrl = $responseObject->data->authorization_url;
        // Redirect the user to the authorization URL
        
        $_SESSION['authorizationUrl'] = $authorizationUrl;
    $_SESSION['reference'] = $reference;
    $_SESSION['vid'] = $vid;

    // Redirect the user to the authorization URL
    header("Location: $authorizationUrl");
    exit();
        // header("Location: $authorizationUrl");
        // verify($reference, $vid);
        // echo "<script>console.log('E reach here');</script>";
        // exit();
    } else {
        echo "<script>console.log('Error: Unable to get authorization URL');</script>";
    }
}

// Check if the required data is present in the session
if (isset($_SESSION['authorizationUrl'], $_SESSION['reference'], $_SESSION['vid'])) {
    // Retrieve data from the session
    $authorizationUrl = $_SESSION['authorizationUrl'];
    $reference = $_SESSION['reference'];
    $vid = $_SESSION['vid'];

    // Perform verification
    verify($reference, $vid);

    echo "<script>console.log('Verification complete');</script>";
} else {
    echo "<script>console.log('Error: Missing session data for verification');</script>";
}

    function verify($reference, $vid){
    
        $curl = curl_init();
        $url = "https://api.paystack.co/transaction/verify/".$reference;
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer sk_test_9a2bc987d1afa64bcda7497d615ad15d05ba87e5",
            "Cache-Control: no-cache",
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $responseObject = json_decode($response, false);
            $amount = $responseObject->data->amount;

            // Convert the number to a string
            $numberAsString = (string)$amount;

            // Check if the number ends with two zeros
            if (substr($numberAsString, -2) === '00') {
                // Remove the two trailing zeros
                $amount = substr($numberAsString, 0, -2);
            } else {
                // If there are no two trailing zeros, keep the original number
                $amount = $numberAsString;
            }

            $vid = $_SESSION['vid'];
            $amount = "$amount"; // Replace with the actual value
            $reference =$reference; // Replace with the actual value
            global $dbh; // Use the global keyword to access $dbh within the function
    
            // $vid = $_SESSION['login'];
            // $amount = "700"; // Replace with the actual value
            // $reference = "reference"; // Replace with the actual value
        
            $updamrf = $dbh->prepare("UPDATE tblbooking SET amount = :amount, ref = :reference WHERE VehicleId = :vid");
            // $updamrf = $dbh->prepare($con);
            $updamrf->bindParam(':amount', $amount, PDO::PARAM_STR);
            $updamrf->bindParam(':reference', $reference, PDO::PARAM_STR);
            $updamrf->bindParam(':vid', $vid, PDO::PARAM_STR);
            $updamrf->execute();
            echo "<script>alert('$amount payed succefully!!');</script>";
        }
    }
    // makePayment(70000, 'ola1@gmail.com');
?>
<!-- {"status": authorization URL created","data":{"authorization_url":"https://checkout.paystack.com/sq1tu3g4hpugl4n","access_code":"sq1tu3g4hpugl4n","reference":"zxjs85sljk"}} -->