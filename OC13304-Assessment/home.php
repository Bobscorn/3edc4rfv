<?php
require_once "tools.php";
Debug::Output("home", false);
?>
<div class="productdiv">
<div>
<?php
# Get 5 random products
# First get highest id of products
$gethighestidquery = "SELECT `id` FROM `products` ORDER BY `id` DESC LIMIT 1;";
$conn = DB::GetDefaultInstance();
$highestidsql = $conn->query($gethighestidquery);
if (Exists($highestidsql))
{
  $highestid = $highestidsql->fetch_assoc()['id'];
}
else
{
  die("yomama");
}
if ($highestid > 1)
{
  $i = 0;
  $j = 0;
  $nums = array();
  while ($i < 5 && $j < 5)
  {
    $randnum = GetRand(0, $highestid, $nums);
    $trygetrandomproductquery = "SELECT * FROM `products` INNER JOIN `tags` ON `products`.`id` = `tags`.`productid` WHERE `products`.`id` = $randnum;";
    dump_var($trygetrandomproductquery);
    $randomproduct = $conn->query($trygetrandomproductquery);
    if (Exists($randomproduct))
    {
      $i++;
      $nums["$randnum"] = $randnum;
      # Print product
      $row = $randomproduct->fetch_assoc();
      $thing = '';
      $thing = new Product($row['name'], $row['description'], $row['date'], $row['author'], $row['tags'], $row['id']);
      Products::Add($thing, "thing$i");
      include "product.php";
    }
    else
    {
      $j++;
      # Max of 5 failures (stored in $j)
    }
  }
  dump_var($nums);
}
else
{
  echo "<h4> No Products :/</h4>";
}

function GetRand($bottom, $top, $nums)
{
  $thing = mt_rand($bottom, $top);
  foreach ($nums as $i)
  {
    if ($thing == $i)
    {
      return GetRand($bottom, $top, $nums);
      break;
    }
  }
  return $thing;
}

?>
</div>
</div>

<!-- Beginning of purely HTML Content (content before is from php) -->
