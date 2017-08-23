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
      nEcho("Logging in");
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

    case 'Logout':

      $user->Logout();

      break;

    case 'Create Referral Code':

      $user->CheckIfLoggedIn();
      nEcho("Your referral code is: ".$user->MakeWorkingReferralCode());

      break;


    case 'Search':

      $searchthing = MakeSecure($_POST['search']);
      $searchquery = "SELECT * FROM `items` WHERE `itemname` LIKE '%$searchthing%'";
      $searchresultssql = $connection->query($searchquery);
      var_dump($searchresultssql);
      if (!Exists($searchresultssql))
      {
        $sr1display = 'block';
        $searchr1 = 'No results';
        $searchid1 = 0;
        $sid1display = 'none';
      }
      else
      {
        $searchresult1 = $searchresultssql->fetch_assoc();
        $sr1display = 'block';
        $searchr1 = $searchresult1['itemname'];
        $searchid1 = $searchresult1['id'];
        if (!is_null($searchresult2 = $searchresultssql->fetch_assoc()))
        {
          $sr2display = 'block';
          $searchr2 = $searchresult2['itemname'];
          $searchid2 = $searchresult2['id'];
          if (!is_null($searchresult3 = $searchresultssql->fetch_assoc()))
          {
            $sr3display = 'block';
            $searchr3 = $searchresult3['itemname'];
            $searchid3 = $searchresult3['id'];
          }
        }
      }

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
