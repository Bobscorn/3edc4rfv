<?php
/**
* Just a file containing a form to create a product
*
* Actual PHP code done on line 72 in sign-in.php
*/
 ?>

<form id="create-product" method="post" action="index.php">
  <input type="text" placeholder="Product Name" name="product-name" maxlength="254" pattern="[A-Za-z0-9 ]+" title="Alphanumeric Names Only" required></input>
  <textarea placeholder="Product Description" name="product-desc" maxlength="2147483647" style="height:50px;overflow-x:hidden;overflow-y:auto;word-wrap:break-all;"></textarea>
  <input type="text" placeholder="Product Tags (separate with commas)" name="product-tags" required></input>
  <input type="submit" class="submit" name="formname" value="Create Product"></input>
</form>
