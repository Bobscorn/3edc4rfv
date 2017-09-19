<?php
# PHP With forms to create Products and add them to the database
#
# Addition to database done by sign-in.php around line 54
 ?>

<form method="post" action="index.php">
  <input type="text" name="product-name" required></input>
  <input type="text" name="product-description"></input>
  <input type="text" name="product-tags" required></input>
  <input type="submit" class="submit" name="formname" value="Create Product"></input>
</form>
