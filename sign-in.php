<?php
require_once 'tools.php';
// Checks if form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (DontExist($_POST['formname'])) {$_POST['formname'] = 'none';}
  $connection = DB::GetDefaultInstance();
  $user = new User($connection);
  switch ($_POST['formname'])
  {
    case 'Login':
      // LOGGING IN
      $name = MakeSecure($_POST['name']);
      $password = MakeSecure($_POST['password']);
      if (DontExist($name) || DontExist($password))
      {
        echo "<h4> Username or password invalid </h4>";
        break;
      }
      $user->Login($name, $password);

      break;

    case 'Register':
      // REGISTERING
      $name     = MakeSecure($_POST['name']);
      $password = MakeSecure($_POST['password']);
      $rcode    = MakeSecure($_POST['rcode']);
      if (DontExist($name) || DontExist($password) || DontExist($rcode))
      {
        echo "<h4> One supplied register parameter was invalid</h4>";
        break;
      }
      $user->Register($name, $password, $rcode);

      break;

    case 'Create Referral Code':


      break;

    case 'none':
      break;
    default:
    $formname = $_POST['formname'];
      echo "<h4> A form was submitted with value '$formname,' this value was not recognised </h4><h6> You can safely ignore this if you're not a moderator";
      break;
  }
}

 ?>
