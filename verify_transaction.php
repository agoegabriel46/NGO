<script src="login/jquery/jquery-2.2.4.js"></script>
<!-- sweetalert -->
<script src="login/sweetalert/sweetalert.js"></script>

<?php
//fetch reference from url using php global get variable
$ref = $_GET['reference'];
if($ref == ""){
header("Location:javascript://history.go(-1)");
}
?>
<?php

  $curl = curl_init();
  
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($ref),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer sk_test_ba90c42fce178a1660b087b86018cb16b6a96ab9",
      "Cache-Control: no-cache",
    ),
  ));
  
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);
  
  if ($err) {
    echo "cURL Error #:" . $err;
  } else {
    //echo $response;
    $result = json_decode($response);

  }
  if($result -> data -> status == 'success'){
      $status = $result->data->status;
      $reference = $result->data->reference;
      $lname = $result->data->customer->last_name;
      $fname = $result->data->customer->first_name;
      $fullname = $lname."". $fname;
      $fullamount= $result->data->amount;
      $cus_email = $result->data->customer->email;
      //get time
      date_default_timezone_set('Africa/Accra');
      $date_time = date('m/d/Y h:i:s a', time());
      
      include_once "setting/config.php";

      $mysqli->query("INSERT INTO donations (status, reference, name,email,amount)
      VALUES('$status', '$reference','$fullname', '$cus_email', '$fullamount' )") or die(mysqli_error($mysqli));  
    echo
    '<script type="text/javascript">
    jQuery(function validation(){
    swal("Transaction Successful", "Thank You", "success", {
    button: "Ok",
    });
    });
    </script>';
    header("Location: index.php");
      exit;
  }
  else{
    echo
    '<script type="text/javascript">
    jQuery(function validation(){
    swal("Transaction Unsuccessful", "Please Try Again", "danger", {
    button: "Ok",
    });
    });
    </script>';
    header("Location: index.php");
      exit;
  }
?>