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
      if (DontExist($name) || DontExist($password))
      {
        echo "<h4> One supplied register parameter was invalid</h4>";
        break;
      }
      $user->Register($name, $password);

      break;

    case 'Logout':

      $user->Logout();

      break;

    case 'Search':

      $_DEBUGOUTPUT['search'] = true;
      $currentdebug = 'search';
      $searchthing = strtolower(MakeSecure($_POST['search']));
      $searchquery = "SELECT * FROM `items` WHERE `itemname` LIKE '%$searchthing%'";

      $searchthing = MakeSecure($_POST['search']); // Used to keep the search parameter in the search bar

      break;

    case 'Create Product':

      if (isset($_POST['product-name']))
      {
        # Post variables
        $pname = $_POST['product-name'];
        $pdesc = isset($_POST['product-desc']) ? $_POST['product-desc'] : 'No description';
        $ptags = isset($_POST['product-tags']) ? $_POST['product-tags'] : 'Tagless';
        # Self Obtained Variables
        $pdate = new DateTime(NULL, timezone_open("Pacific/Auckland"));
        $pauthor = $user->GetName();
      }
      else
      {
        $header = 'usertools.php';
        $page = 'product.php';
      }

      // Check if item exists already
      /*$checkforitemquery = "SELECT `id` FROM `items` WHERE `itemname` = '$itemname'";
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
      */
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
