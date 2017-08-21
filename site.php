<?php
require 'sign-in.php';
require_once 'tools.php';
$connection = DB::GetDefaultInstance();

 ?>
<!DOCTYPE html>
<?php
$token = bin2hex(random_bytes(16));
echo "<h4>Token: $token";
 ?>
