<?php
$json = file_get_contents('php://input');
$array = json_decode($json, true);
file_put_contents('testpost.json', $json, FILE_APPEND);
print('Ok ');
?>
