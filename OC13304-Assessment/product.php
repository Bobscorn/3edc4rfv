<?php
require_once 'tools.php';
// This file is designed to be included by another file after setting
// $CurrentProductName

Debug::Output("product", false);

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
  if (!strstr($pid, "#"))
  {
    $pid = "#" . "$pid";
  }
  $tags         = ($product->tags == '')        ? "Tagless"         : $product->tags;
}



 ?>

<div class='pdiv'>
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
    <?php echo "$pid"; ?>
  </div>
  <div class='ptags' title="<?php echo "$tags"; ?>">
    <?php echo "$tags"; ?>
  </div>
  <div class='pauthor' title="<?php echo "$author"; ?>">
    <?php echo "$author"; ?>
  </div>
</div>

<link rel='stylesheet' type="text/css" href="bob.css">
