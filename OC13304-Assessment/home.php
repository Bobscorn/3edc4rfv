<?php
require_once "tools.php";
Debug::Output("home", false);
?>
<h3 style='text-align:center;'>Here's some random products</h3>
<div class="productdiv">
<div>
<?php
$conn = DB::GetDefaultInstance();
# Get 5 random products

$randomproductsquery = "SELECT DISTINCT products.id, products.description, products.name AS name, products.date, tags.tags AS tags, accounts.name AS author FROM ((`products` INNER JOIN `tags` ON `products`.`id` = `tags`.`productid`) INNER JOIN `accounts` ON `products`.`author` = `accounts`.`id`) ORDER BY RAND() LIMIT 5;";
$randomproducts = $conn->query($randomproductsquery);
dump_var($randomproductsquery);
dump_var($conn);

for ($i = 0; $i < $randomproducts->num_rows; $i++)
{
  $row = $randomproducts->fetch_assoc();
  $thing = '';
  $thing = new Product($row['name'], $row['description'], $row['date'], $row['author'], $row['tags'], $row['id']);
  Products::Add($thing, "thing$i");
  dump_var($row);
  include "product.php";
}

?>
</div>
</div>

<!-- Beginning of purely HTML Content (content before is from php) -->
