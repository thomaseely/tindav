<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://tindav.com/api/v1.0/registration.php",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\n    \"user_type\":\"parent\",\n\t\"username\":\"parent17@test.com\",\n\t\"password\":\"thomas\",\n\t\"first_name\":\"parent7\",\n\t\"last_name\":\"tom4\",\n\t\"address\":\"street1\",\n\t\"city\":\"fremont\",\n\t\"state\":\"ca\",\n\t\"country\":\"usa\",\n\t\"zipcode\":\"12345\",\n\t\"phone\":\"9902548200\"\n}",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "content-type: application/json",
    "postman-token: b892deb2-fed6-fe3c-7e37-9fe9baf0ed1b"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}