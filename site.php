<!DOCTYPE html>
<link rel='stylesheet' type='text/css' href='bob.css'>
<div id='phpoutput'>
<?php
require 'sign-in.php';
require_once 'tools.php';
$connection = DB::GetDefaultInstance();

 ?>
<?php
if (!isset($loginvisible)) { $loginvisible = 'inline-block'; }
if (!isset($registervisible)) { $registervisible = 'none'; }
if (!isset($toolsvisible)) { $toolsvisible = 'none'; }

$connection = DB::GetDefaultInstance();
$user = new User($connection);

$buttoncontent = $loginvisible ? 'Wanting to Register?' : 'Looking for Login?';

dEcho("Before Login check");
$loggedin = $user->CheckIfLoggedIn();
dEcho("After Login check");
dump_var($loggedin);

if (!isset($searchthing)) { $searchthing = ''; }
if (!isset($sr1display)) { $sr1display = 'none'; }
if (!isset($sid1display)) { $sid1display = 'none'; }
if (!isset($searchr1)) { $searchr1 = ''; }
if (!isset($searchid1)) { $searchid1 = 0; }
if (!isset($sr2display)) { $sr2display = 'none'; }
if (!isset($searchr2)) { $searchr2 = ''; }
if (!isset($searchid2)) { $searchid2 = 0; }
if (!isset($sr3display)) { $sr3display = 'none'; }
if (!isset($searchr3)) { $searchr3 = ''; }
if (!isset($searchid3)) { $searchid3 = 0; }

?>
</div>
<div id='formholder'>
  <div style='display: inline-block;'>
<?php
if (!$loggedin) {
echo "
<form id='loginform' action='site.php' method='post' style='display: $loginvisible;'>
  <fieldset>
    <legend>Login</legend>
    <input type='text'      class='topinput'    maxlength='64'    name='name'     placeholder='Username' required>
    <input type='password'  class='bottominput' maxlength='64'    name='password' placeholder='Password' required>
    <input type='submit'    class='submit'      name='formname'   value='Login'>
  </fieldset>
</form>

<form id='registerform' action='site.php' method='post' style='display: $registervisible;'>
  <fieldset>
    <legend>Register</legend>
    <input type='text' class='topinput' maxlength='64' name='name' placeholder='Username' required>
    <input type='password' class='midinput' maxlength='64' name='password' placeholder='Password' required>
    <input type='text' class='bottominput' maxlength='32' minlength='32' name='rcode' placeholder='Referral Code' required>
    <input type='submit' class='submit' name='formname' value='Register'>
  </fieldset>
</form>

<button type='button' id='swapFormButton' onclick='swapForms()'>$buttoncontent</button>
";
}

if ($loggedin)
{
  echo "
<form action='site.php' method='post'>
  <fieldset>
    <legend>Search</legend>
    <input type='text' class='fullinput' name='search' pattern='[A-Za-z0-9 ]+' title='Please Only Alphanumeric' placeholder='Search' value='$searchthing' required>
    <input type='submit' class='submit inlineinput' name='formname' value='Search'>
    <input type='submit' class='submit inlineinput' name='formname' value='Add Item'>
    <div id='searchresult1' style='display: $sr1display;'>
      <button class='inlineinput' type='button' onclick='searchClicked(1)'>$searchr1</button><input style='display: $sid1display;' id='searchid1' value='$searchid1' disabled>
    </div>
    <div id='searchresult2' style='display: $sr2display;'>
      <button class='inlineinput' type='button' onclick='searchClicked(2)'>$searchr2</button><input id='searchid2' value='$searchid2' disabled>
    </div>
    <div id='searchresult3' style='display: $sr3display;'>
      <button class='inlineinput' type='button' onclick='searchClicked(3)'>$searchr3</button><input id='searchid3' value='$searchid3' disabled>
    </div>
  </fieldset>
</form>
<form action='site.php' method='post'>
  <fieldset>
    <legend>Order</legend>
    <input id='orderid' type='number' class='topinput' name='orderid' placeholder='Item ID' required>
    <input type='number' class='midinput' name='amount' placeholder='Amount' required>
    <input type='number' class='bottominput' name='week' placeholder='Week' required>
    <input type='submit' class='submit' name='formname' value='Order'>
  </fieldset>
</form>
<button type='button' id='latest' class='latest' onclick='latestOrders()'>Latest Orders</button>
<iframe id='theiframe' style='display: none;'>IFrame failed :P</iframe>
<button type='button' id='allorders' class='latest' onclick='allOrders()'>All Orders</button>
<iframe id='theotheriframe' style='display: none;'>IFrame2 failed :P</iframe>
<form action='site.php' method='post'>
<fieldset>
  <legend>Account</legend>
  <input type='submit' class='submit' name='formname' value='Create Referral Code'>
  <input type='submit' class='submit' name='formname' value='Logout'>
</fieldset>
</form>
  ";

}
?>
</div>
</div> <!-- of id='formholder' -->
<script><?php require 'site.js'; ?></script>
