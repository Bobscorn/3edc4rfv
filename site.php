<?php
require 'sign-in.php';
require_once 'tools.php';
$connection = DB::GetDefaultInstance();

 ?>
<!DOCTYPE html>
<?php
if (!isset($loginvisible)) { $loginvisible = 'inline-block'; }
if (!isset($registervisible)) { $registervisible = 'none'; }
if (!isset($toolsvisible)) { $toolsvisible = 'none'; }

$connection = DB::GetDefaultInstance();
$user = new User($connection);

nEcho("Before Login check");
$loggedin = $user->CheckIfLoggedIn();
nEcho("After Login check");
dump_var($loggedin);
if (!$loggedin) {
echo "
<form action='site.php' method='post' style='display: $loginvisible;'>
  <fieldset>
    <legend>Login</legend>
    <input type='text' maxlength='64' name='name' required>
    <input type='password' maxlength='64' name='password' required>
    <input type='submit' name='formname' value='Login'>
  </fieldset>
</form>

<form action='site.php' method='post' style='display: $registervisible;'>
  <fieldset>
    <legend>Register</legend>
    <input type='text' maxlength='64' name='name' required>
    <input type='password' maxlength='64' name='password' required>
    <input type='text' maxlength='' name='rcode' required>
  </fieldset>
</form>

";
}

if ($loggedin)
{
  echo "
<form action='site.php' method='post'>
  <fieldset>
    <legend>Formname</legend>
    <input type='' name=''>
  </fieldset>
</form>

  ";

}
?>
