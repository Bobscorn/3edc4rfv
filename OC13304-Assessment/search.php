<?php
require_once "tools.php";

# Search through product name first, then tags, then author, then description
if (is_null($_POST['search']))
{
  die('no search term');
}
$search = $_POST['search'];
$conn = DB::GetDefaultInstance();
Debug::Output("search", false); # Telling the debug class to not output anything
dump_var($conn);

# This sql Searches a product's name, tags, author, and description
# It orders them by score, were the higest score is given when search is
# found in the name, then 2nd highest score when found in tags, 3rd highest in author,
# and last in description
# It removes duplicates by selecting DISTINCT id's from an aliased table
# Without aliasing the table, duplicates snuck in.

$searchquery = "SELECT DISTINCT
    id,
    description,
    NAME,
    DATE,
    tags,
    author
FROM
    (
        (
        SELECT
            products.id,
            products.description,
            products.name AS NAME,
            products.date,
            tags.tags AS tags,
            accounts.name AS author,
            MATCH(`products`.`name`) AGAINST('$search') +3 AS score
        FROM
            (
                (
                    `products`
                INNER JOIN `tags` ON `products`.`id` = `tags`.`productid`
                )
            INNER JOIN `accounts` ON `products`.`author` = `accounts`.`id`
            )
        WHERE
            MATCH(`products`.`name`) AGAINST('$search' IN BOOLEAN MODE)
        UNION
    SELECT
        products.id,
        products.description,
        products.name AS NAME,
        products.date,
        tags.tags AS tags,
        accounts.name AS author,
        MATCH(`tags`.`tags`) AGAINST('$search') +2 AS score
    FROM
        (
            (
                `products`
            INNER JOIN `tags` ON `products`.`id` = `tags`.`productid`
            )
        INNER JOIN `accounts` ON `products`.`author` = `accounts`.`id`
        )
    WHERE
        MATCH(`tags`.`tags`) AGAINST('$search' IN BOOLEAN MODE)
    UNION
SELECT
    products.id,
    products.description,
    products.name AS NAME,
    products.date,
    tags.tags AS tags,
    accounts.name AS author,
    MATCH(`accounts`.`name`) AGAINST('$search') +1 AS score
FROM
    (
        (
            `products`
        INNER JOIN `tags` ON `products`.`id` = `tags`.`productid`
        )
    INNER JOIN `accounts` ON `products`.`author` = `accounts`.`id`
    )
WHERE
    MATCH(`accounts`.`name`) AGAINST('$search' IN BOOLEAN MODE)
UNION
SELECT
    products.id,
    products.description,
    products.name AS NAME,
    products.date,
    tags.tags AS tags,
    accounts.name AS author,
    MATCH(`products`.`description`) AGAINST('$search') AS score
FROM
    (
        (
            `products`
        INNER JOIN `tags` ON `products`.`id` = `tags`.`productid`
        )
    INNER JOIN `accounts` ON `products`.`author` = `accounts`.`id`
    )
WHERE
    MATCH(`products`.`description`) AGAINST('$search' IN BOOLEAN MODE)
ORDER BY
    score
DESC
    ) AS a
    )";

$results = $conn->query($searchquery);
dump_var($results);
dEcho("<br>");
dump_var($conn);
dEcho("<br>");
dump_var($searchquery);

# Go through the results, printing 5 into a product div,
# infinitely producing product divs to until all results are printed

# First make a 5 large array to store search results in groups of 5
$rs = array();
$rs[0] = $results->fetch_assoc();
$rs[1] = $results->fetch_assoc();
$rs[2] = $results->fetch_assoc();
$rs[3] = $results->fetch_assoc();
$rs[4] = $results->fetch_assoc();
$i = 0; # $i is used for naming the products
{
  echo "<div class='productdiv'>
          <div>";
  $thingmahdoohicky = '';

  # Go through the results, printing out each one
  foreach($rs as $r)
  {
    $thingy = '';
    if (!is_null($r))
    {
      $thingmahdoohicky = 'wadup'; # To check for No Results
      $thingy = new Product($r['name'], $r['description'], $r['date'], $r['author'], $r['tags'], $r['id']);
      Products::Add($thingy, "thing$i");
      include "product.php";
      dump_var($r);
    }
    else
    {
      break;
    }
  }
  if ($thingmahdoohicky != 'wadup') # Checks if any results were printed
  {
    nEcho("No Results :/<br><br>");
    echo "<h5>But here's a really long div you can scroll across :)</h5>";
  }
  echo "   </div>
        </div>"; # Close the first product div

  # Get next results
  $rs[0] = $results->fetch_assoc();
  $rs[1] = $results->fetch_assoc();
  $rs[2] = $results->fetch_assoc();
  $rs[3] = $results->fetch_assoc();
  $rs[4] = $results->fetch_assoc();
  $i++;
}

# Go through remaining search results printing out 5 into a product div (if any are null,
# no results after that null result are printed, the product div will be half full)
# A while loop because initialisation is done before, meaning a for loop is unnecesary
while ($rs[0] != NULL) # Continue while there's at least 1 result remaining
{
  echo "<div class='productdiv'>
          <div>";
  # Go through the results, printing out each one
  foreach($rs as $r)
  {
    $thingy = '';
    if (!is_null($r))
    {
      $thingy = new Product($r['name'], $r['description'], $r['date'], $r['author'], $r['tags'], $r['id']);
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

 ?>
