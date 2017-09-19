<!DOCTYPE html>
<link rel='stylesheet' type='text/css' href='bob.css'>
<div id='phpoutput'>
<?php
require 'sign-in.php';
require_once 'tools.php';
$connection = DB::GetDefaultInstance();

 ?>
<?php
if (!isset($loginvisible)) { $loginvisible = 'inline'; }
if (!isset($registervisible)) { $registervisible = 'none'; }
if (!isset($toolsvisible)) { $toolsvisible = 'none'; }

$connection = DB::GetDefaultInstance();
$user = new User($connection);

$buttoncontent = $loginvisible == 'inline' ? 'Register?' : 'Login?';

dEcho("Before Login check");
$loggedin = $user->CheckIfLoggedIn();;
if ($loggedin)
{
  dEcho("rogged in");
}
else
{
  dEcho("Not logged in");
}
dEcho("After Login check");
dump_var($loggedin);

if (!isset($searchthing)) { $searchthing = ''; }

?>
</div>
<button id='togglePHPButton' type="button" onclick="togglePHPOutput()"></button>
<?php
if (!$loggedin) {
  $header = 'login.php';
  $page = 'home.php';
}

if ($loggedin)
{
  $header = isset($header) ? $header : 'usertools.php';
  $page =  isset($page) ? $page : 'home.php';
}

if (isset($header)) { require "$header"; }
if (isset($page)) { require "$page"; }

?>
</div>
</div> <!-- of id='formholder' -->



<script><?php require 'bob.js'; ?></script>
