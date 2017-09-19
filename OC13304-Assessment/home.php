<?php
require_once "tools.php";
// Get random products
Debug::Output("home", false);
?>
<div class="productdiv">
<div>
<?php
$bobproduct = new Product("Water", "Just pure H2O", "21/42/2042", "Bob", "water, h2o", "42" );
Products::Add($bobproduct, "bob");
include "product.php";

$bobproduct = '';
$bobproduct = new Product("Fertiliser", "Hand made Sodium something or rather", "21/69/2420", "Steve Johnson", "fertiliser, sodium", "69");
Products::Add($bobproduct, "steve");
include "product.php";

$bobproduct = '';
$bobproduct = new Product("Leather Belt", "Hand crafted tanned leather belt with stainless steel buckle", "09/08/1999", "Billy", "belt, leather, steel", "01");
Products::Add($bobproduct, "thing");
include "product.php";

$bobproduct = '';
$bobproduct = new Product("China");
Products::Add($bobproduct, "thing1");
include "product.php";

$bobproduct = '';
$bobproduct = new Product("Korea");
Products::Add($bobproduct, "thing2");
include "product.php";

$bobproduct = '';
$bobproduct = new Product("Chinatown");
Products::Add($bobproduct, "thing3");
include "product.php";
?>
</div>
</div>

<!-- Beginning of purely HTML Content (content before is from php) -->
