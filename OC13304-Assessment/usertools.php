<?php if (!isset($searchtooltip)) $searchtooltip = 'anything'; ?>
<div id='userstuff'><!--
--><form action='index.php' method='post'><!--
  --><div id ='usermenus'><!--
  --><input type='text' class='searchbar' style='display:inline;' name='search' pattern='[A-Za-z0-9 ]+' title='Please Only Alphanumeric' placeholder='Search for <?php echo $searchtooltip; ?>' value='<?php echo "$searchthing"; ?>' ><!--
    --><input type='submit' class='submit searchbutton' style='display:inline;' name='formname' value='Search'><!--
    --><div id='search'><!--
    --></div><!--
  --></div><!--
  --><div id='otherusermenus' class="menu-parent"><!--
  --><h3 style='text-decoration: none;'>Menu</h3><!--
    --><ul class="menu"><!--
      --><li><input type="submit" class="submit" style='border:none;border-radius:0;background-color:rgba(0,0,0,0);' name="formname" value="Create Product"></li><!--
      --><li><input type='submit' class='submit' style='border:none;border-radius:0;background-color:rgba(0,0,0,0);' name='formname' value='Logout'></li><!--
    --></ul><!--
  --></div><!--
--></form><!--
--></div>
