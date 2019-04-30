<?php
$url = 'http://192.168.168.199/testpostrec.php';
$ch = curl_init($url);
$data = array(
    'username' => 'user1',
    'password' => '123456'
);
$payload = json_encode(array("user" => $data));
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
print($result);
?>
