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

    case 'Logout':

      $user->Logout();

      break;

    case 'Create Referral Code':

      $user->CheckIfLoggedIn();
      nEcho("Your referral code is: ".$user->MakeWorkingReferralCode());

      break;


    case 'Search':

      $searchthing = strtolower(MakeSecure($_POST['search']));
      $searchquery = "SELECT * FROM `items` WHERE `itemname` LIKE '%$searchthing%'";

      $searchthing = MakeSecure($_POST['search']); // Used to keep the search parameter in the search bar

      $searchresultssql = $connection->query($searchquery);
      dump_var($searchresultssql);
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
        $sid1display = 'inline';
        $searchr1 = ucwords($searchresult1['itemname']);
        $searchid1 = $searchresult1['id'];
        if (!is_null($searchresult2 = $searchresultssql->fetch_assoc()))
        {
          $sr2display = 'block';
          $searchr2 = ucwords($searchresult2['itemname']);
          $searchid2 = $searchresult2['id'];
          if (!is_null($searchresult3 = $searchresultssql->fetch_assoc()))
          {
            $sr3display = 'block';
            $searchr3 = ucwords($searchresult3['itemname']);
            $searchid3 = $searchresult3['id'];
          }
        }
      }

      break;

    case 'Add Item':

      $itemname = strtolower(MakeSecure($_POST['search'])); // Search parameter used to add items as well

      // Check if item exists already
      $checkforitemquery = "SELECT `id` FROM `items` WHERE `itemname` = '$itemname'";
      $itemidsql = $connection->query($checkforitemquery);

      if (Exists($itemidsql))
      {
        // Show the user it exists
        $sr1display = 'block';
        $searchr3 = $itemname;
        $searchid1 = $itemidsql->fetch_assoc()['id'];
        nEcho("Item already exists");
        break;
      }

      $additemquery = "INSERT INTO `items` (`itemname`) VALUES ('$itemname')";
      $success = $connection->query($additemquery);

      if ($success)
      {
        nEcho("Successfully added item '$itemname' into database");
      }

      break;

    case 'Order':

      $itemid   = MakeSecure($_POST['orderid']);
      $amount   = MakeSecure($_POST['amount']);
      $week     = MakeSecure($_POST['week']);

      if (DontExist($itemid) || DontExist($amount) || DontExist($week))
      {
        nEcho("One Order parameter was invalid or empty");
        break;
      }

      $user->CheckIfLoggedIn();
      $username = $user->GetName();

      if ($username == '')
      {
        nEcho("You were logged out so nothing was ordered");
      }

      $orderquery = "INSERT INTO `orders` (`itemid`,`amount`,`week`,`orderer`) VALUES ('$itemid','$amount','$week','$username')";
      $success = $connection->query($orderquery);


      if ($success)
      {
        // Get item query will work if previous INSERT worked because the `itemid` column is a foreign key
        // Referencing the items table, so if it succeeded this will work
        $getitemnamequery = "SELECT `itemname` FROM `items` WHERE `id` = '$itemid'";
        $namesql = $connection->query($getitemnamequery);
        $itemname = $namesql->fetch_assoc()['itemname'];
        nEcho("$username you ordered $amount x '$itemname' ($itemid) for week $week");
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
