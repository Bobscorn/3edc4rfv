<?php

# Call CheckIfLoggedIn in case it hasn't been already,
# because if it hasn't, the name won't be set
$conn = DB::GetDefaultInstance();
$user = isset($user) ? $user : new User($conn);
if ($user->CheckIfLoggedIn())
{
  $username = $user->GetName();
  $getidsql = "SELECT `id` FROM `accounts` WHERE `name` = '$username'";
  $idsql = $conn->query($getidsql);
  # No check for existance as this is done in $user->CheckIfLoggedIn()
  $uid = $idsql->fetch_assoc()['id'];
  $getproductsquery = "SELECT products.name, products.date, products.description, products.id, tags.tags AS tags FROM (`products` INNER JOIN `tags` ON `products`.`id` = `tags`.`productid`) WHERE `products`.`author` = '$uid'";
  $results = $conn->query($getproductsquery);
  if (Exists($results))
  {
    # Print out products (code was copied from 'search.php')
    $rs = array();
    $i = 0;
    $rs[0] = $results->fetch_assoc();
    $rs[1] = $results->fetch_assoc();
    $rs[2] = $results->fetch_assoc();
    $rs[3] = $results->fetch_assoc();
    $rs[4] = $results->fetch_assoc();
    while ($rs[0] != NULL) # Continue while there's at least 1 result (the first) remaining
    {
      echo "<div class='productdiv'>
              <div>";
      # Go through the results, printing out each one
      foreach($rs as $r)
      {
        $thingy = '';
        if (!is_null($r))
        {
          $dateobj = new DateTime($r['date'], timezone_open("Pacific/Auckland"));
          $datestr = $dateobj->format("d/m");
          $r['author'] = $username; # This will be converted to <i>You</i> in product.php as it checks the username
          $thingy = new Product($r['name'], $r['description'], $datestr, $r['author'], $r['tags'], $r['id']);
          Products::Add($thingy, "thing$i");
          include "product.php";
          dump_var($r);
        }
        else # If a result is null, all results following should be null as well, so break
        {
          break;
        }
      }
      echo "   </div>
            </div>";

      $rs[0] = $results->fetch_assoc();
      $rs[1] = $results->fetch_assoc();
      $rs[2] = $results->fetch_assoc();
      $rs[3] = $results->fetch_assoc();
      $rs[4] = $results->fetch_assoc();
      $i++;
    }
  }
  else
  {
    # No products
    echo "<h4 style='text-align:center;'>No Products! :(</h4>";
  }
}
else
{
  # User wasn't logged in
  nEcho("Can't view <i>your</i> products, not logged in");
}

 ?>
