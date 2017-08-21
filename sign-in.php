<?php
require_once 'tools.php';
// Checks if form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (DontExist($_POST['formname'])) {$_POST['formname'] = 'none';}
  switch ($_POST['formname'])
  {
    case 'Login':
      // LOGGING IN

      break;

    case 'Register':
      // REGISTERING


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
