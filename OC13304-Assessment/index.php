<?php
/***
* This file is the only webpage that will/should be accessed.
* Through sign-in.php and internal logic, a header and a base page are selected and included
* into this page, this way, all content common to multiple pages only needs to be included once
*
* The downfall being that users can not bookmark any page besides index.php,
* Which will by default show a search bar, login/register forms and 5 random products
*/
 ?>
<!DOCTYPE html>
<link rel='stylesheet' type='text/css' href='bob.css'>
<title id='title'>MLG Shopping Channel</title>
<div id='phpoutput'>
<?php
# Require 'sign-in.php' at the start because it does page/form logic
require 'sign-in.php';
require_once 'tools.php';
$connection = DB::GetDefaultInstance();
Debug::Output("index", false);

if (!isset($loginvisible)) { $loginvisible = 'inline'; }
if (!isset($registervisible)) { $registervisible = 'none'; }
if (!isset($toolsvisible)) { $toolsvisible = 'none'; }

if (!isset($title)) { $title = 'MLG Shopping Channel Home'; }

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
<div id='titlevalue' style='display:none;'><?php echo $title; ?></div>
<button id='togglePHPButton' type="button" onclick="togglePHPOutput()"></button>
<?php
if (!$loggedin) {
  $header = 'login.php';
  $page = isset($page) ? $page : 'home.php';
}

if ($loggedin)
{
  $header = isset($header) ? $header : 'usertools.php';
  $page =  isset($page) ? $page : 'home.php';
}

# This is where the header and base page are included (required in this case)
if (isset($header)) { require "$header"; }
if (isset($page)) { require "$page"; }

?>

<script><?php require 'bob.js'; ?></script>
