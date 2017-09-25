<?php
require_once 'tools.php';
# This file is designed to be included by another php file
# After setting/adding a product to the Products class

Debug::Output("product", false);
$conn = DB::GetDefaultInstance();
$user = new User($conn);
$loggedin = $user->CheckIfLoggedIn();

// Make global scope
$name = $description = $date = $author = $pid = $tags = '';

dump_var(Products::$ProductArray);
if ((!empty(Products::$ProductArray)) && isset(Products::$CurrentProductName) && isset(Products::$ProductArray[Products::$CurrentProductName]))
// ProductInfo is an array containing information about products
// Will be used by home page showing random products, and by search results
{
  $product      = Products::Get();
  dump_var($product);
  $name         = ($product->name == '')        ? "Nameless" : $product->name;
  $description  = ($product->description == '') ? "No Description"  : $product->description;
  $date         = ($product->date == '')        ? "--/--/----"     : $product->date;
  $author       = ($product->author == '')      ? "Authorless"    : $product->author;
  $pid          = (string)($product->pid == '') ? "---"           : $product->pid;
  $tags         = ($product->tags == '')        ? "Tagless"         : $product->tags;
}
$author = $author == $user->GetName() ? "<i>You</i>" : $author;


$thingy = $loggedin ? 'get' : '';
 ?>

<?php if ($loggedin) { echo "<form method='get'>"; } ?>
  <input style="display:none;" name="action" value="view-product">
  <div class='pdiv' onclick="javascript:this.parentNode.submit();">
    <div class='ptitle' title="<?php echo "$name"; ?>">
      <?php echo "$name"; ?>
    </div>
    <div class='pdate' title="<?php echo "$date"; ?>">
      <?php echo "$date"; ?>
    </div>
    <div class='pdesc' title="<?php echo "$description"; ?>">
      <?php echo "$description"; ?>
    </div>
    <div class='pid' title="<?php echo "$pid"; ?>">
      <?php echo "#$pid"; ?>
    </div>
    <input style='display:none;' name="product-id" value="<?php echo "$pid"; ?>">
    <div class='ptags' title="<?php echo "$tags"; ?>">
      <?php echo "$tags"; ?>
    </div>
    <div class='pauthor' title="<?php echo "$author"; ?>">
      <?php echo "$author"; ?>
    </div>
  </div>
<?php if ($loggedin) { echo "</form>"; } ?>

<link rel='stylesheet' type="text/css" href="bob.css">
