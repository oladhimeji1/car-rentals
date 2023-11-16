<?php
    function makePayment($price, $userEmail){
        // echo "<script>console.log($price)</script>";
    $url = "https://api.paystack.co/transaction/initialize";

    $fields = [
        'email' => "$userEmail",
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
    
    // {"status": authorization URL created","data":{"authorization_url":"https://checkout.paystack.com/sq1tu3g4hpugl4n","access_code":"sq1tu3g4hpugl4n","reference":"zxjs85sljk"}} 
    $reference = $responseObject->data->reference;

    // Check if the status is true
    if ($responseObject->status == 1) {
        // Get the authorization URL
        $authorizationUrl = $responseObject->data->authorization_url;
        // Redirect the user to the authorization URL
        // header("Location: $authorizationUrl");
        // exit();
        verify($reference);
    } else {
        // Handle the case where the status is not true
        // echo "Error: Unable to get authorization URL";
        echo "<script>console.log('Error: Unable to get authorization URL');</script>";
    }
}
    function verify($reference){
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

            // Output the result
            echo "<script>console.log('$amount');</script>";
            // Here i want to send the Amount and reference number to the database
            // Update the tblbooking with this

            // $amount 
            // $reference
    
        }
    }

?>
<!-- {"status": authorization URL created","data":{"authorization_url":"https://checkout.paystack.com/sq1tu3g4hpugl4n","access_code":"sq1tu3g4hpugl4n","reference":"zxjs85sljk"}} -->