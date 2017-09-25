<?php
require_once 'tools.php';
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
  if (isset($_GET['action']))
  {
    switch ($_GET['action'])
    {
    case 'view-product':

      $page = 'view-product.php';

      break;
      case 'home':

        $page = 'home.php';
        break;
    }
  }
}
// Checks if form has been submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (DontExist($_POST['formname'])) {$_POST['formname'] = 'none';}
  $connection = DB::GetDefaultInstance();
  $user = new User($connection);

  # The formname variable is submitted by 'submit' buttons in forms, (or buttons like logout)
  # This tells PHP which action it should take
  switch ($_POST['formname'])
  {
    case 'none':
      break;
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

      $page = 'search.php';
      $searchtemp = $_POST['search'];
      $title = "Search for $searchtemp";

      break;

    case 'Create Product':

      Debug::Output("create-product", true);
      if (isset($_POST['product-name']) && $user->CheckIfLoggedIn())
      {
        # Product-name is set, this means a form with product values called this page
        # Post variables, Post because product creation should not be 'bookmarked'
        $pname = $_POST['product-name'];
        $pdesc = isset($_POST['product-desc']) ? MakeSecureSymbols($_POST['product-desc']) : 'No description';
        $ptags = isset($_POST['product-tags']) ? MakeSecureCommas($_POST['product-tags']) : 'Tagless';

        # Non-Post Variables
        $pdateobj = new DateTime(NULL, timezone_open("Pacific/Auckland"));
        $pdate = $pdateobj->format("Y/m/d H:i:s");
        $pauthorid = $user->GetID();

        # Insert data into the products table
        $makeproductquery = "INSERT INTO `products` (`name`,`description`,`date`,`author`) VALUES ('$pname','$pdesc','$pdate','$pauthorid');";
        $madeproduct = $connection->query($makeproductquery);
        dump_var($madeproduct);

        # Get highest id in database, this should naturally be the product just added
        $getidquery = "SELECT `id` FROM `products` ORDER BY `id` DESC LIMIT 1;";
        $productidthing = $connection->query($getidquery);
        $id = $productidthing->fetch_assoc()['id'];

        # Add tags into database, as tags are in a separate table this requires a separate query
        $addtagsquery = "INSERT INTO `tags` (`productid`, `tags`) VALUES ('$id','$ptags');";
        $connection->query($addtagsquery);

        if (Exists($madeproduct))
        {
          unset($page); # unset $page to get default behaviour;
        }
        else
        {
          nEcho("Product Creation Failed");
          $page = 'create-product.php';
        }
      }
      else
      {
        # Product-name not set, this means user should be directed to create product page
        # Because same 'formname' value is used for directing to product Creation
        # and submitting new product information
        $title = 'Create a Product';
        $page = $user->CheckIfLoggedIn() ? 'create-product.php' : 0;
        if (!$page) { unset($page); }
      }
      break;

    case 'My Products':

      $page = 'my-products.php';
      $title = 'Your Products';

      break;

    default:
      $formname = $_POST['formname'];
      echo "<h4> A form was submitted with value '$formname,' this value was not recognised </h4><h6> You can safely ignore this if you're not a moderator</h6>";
      break;
  }
}

 ?>
