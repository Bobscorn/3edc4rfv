<?php
require_once "tools.php";
$conn = DB::GetDefaultInstance();
$user = isset($user) ? $user : new User($conn);

if (isset($_GET['product-id']) && $user->CheckIfLoggedIn())
{
  $conn = DB::GetDefaultInstance();
  $user = new User($conn);
  $user->CheckIfLoggedIn();
  $productid = $_GET['product-id'];
  $productname = $productdate = $productdesc = $productauthor = $producttags = '';

  # Get Product data from id, only id was supplied, this was the url isn't crowded
  $getproductinfoquery = "SELECT
    products.name,
    products.date,
    products.description AS `desc`,
    accounts.name AS author,
    tags.tags AS tags
FROM
    (
        (
            `products`
        INNER JOIN `tags` ON `products`.`id` = `tags`.`productid`
        )
    INNER JOIN `accounts` ON `products`.`author` = `accounts`.`id`
    )
WHERE
    `products`.`id` = '$productid'";

    $sqldata      = $conn->query($getproductinfoquery);
    if (Exists($sqldata))
    {
      $productinfo      = $sqldata->fetch_assoc();
      $productname      = $productinfo['name'];
      $productdate      = $productinfo['date'];
      #$productobj       = new DateTime($productdatetemp, timezone_open("Pacific/Auckland"));
      #$productdate      = $productobj->format("Y/m/d");
      $productdesc      = $productinfo['desc'];
      $productauthor    = $user->GetName() == $productinfo['author'] ? "<i>You</i>" : $productinfo['author'];
      $producttags      = $productinfo['tags'];
      # Replace line breaks with <br> tags
      $productdesc      = preg_replace("/[\n]/", "<br>", $productdesc);
    echo "
    <div class='pvdiv'>
      <div class='pvtitle'>$productname</div><!--
      --><div class='pvdate'>Created:<br> $productdate</div>
      <div class='pvdesc'>".$productdesc."</div>
      <div class='pvauthor'>Made by: $productauthor</div><!--
      --><div class='pvtags'>Tags: $producttags</div>
      <div class='pvid'>Product ID: $productid</div>
    </div>
    ";
  }
}
else
{
  # just go to home page, ?action=home ensures it will the home page
  header('Location: index.php?action=home');
}


 ?>
