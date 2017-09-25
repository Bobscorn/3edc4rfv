<?php
/**
* This page contains only forms
* One is the search bar
* The others are the login/register forms
* One flaw with the register form is how there is no 'confirm password' box
* This means people might make a locked account,
* it has no confirm password box because it would mean rearranging the entire structure of the header
* which is a pain, so no confirm password box
*/
 ?>
<?php if (!isset($searchtooltip)) $searchtooltip = 'anything'; ?>
<form id='registerform' action="#" method="post" style="display:none;">
</form>
<form id='loginform' action="#" method="post" style="display:none;">
</form>
<div id='userstufflogin'><!--
  --><form action='index.php' method='post'><!--
    --><div style='display: inline-block;width:97%;'><!--
      --><div id ='usermenuslogin'><!--
        --><input type='text' class='searchbar' style='display:inline;margin:5px 0;' name='search' pattern='[A-Za-z0-9 ]+' title='Please Only Alphanumeric' placeholder='Search for <?php echo $searchtooltip; ?>' value='<?php echo "$searchthing"; ?>' ><!--
        --><input type='submit' class='submit searchbutton' style='display:inline;' name='formname' value='Search'><!--
      --></div><!--
      --><div id='logindiv' style='display: <?php echo "$loginvisible;"; ?>'><!--
        --><input id='username' type='text'      class='topinput'    maxlength='64'    name='name'     placeholder='Username' form='loginform' required><!--
        --><input id='password' type='password'  class='bottominput' maxlength='64'    name='password' placeholder='Password' form='loginform' required><!--
        --><input style='float:right;' type='submit'    class='submit'      name='formname' form='loginform' value='Login'><!--
      --></div><!--
      --><div id='registerdiv' style='display: <?php echo "$registervisible;";?>'><!--
        --><input id='registername' type='text' class='topinput' maxlength='64' name='name' placeholder='Username' form='registerform' required><!--
        --><input id='registerpassword' type='password' class='bottominput' maxlength='64' name='password' placeholder='Password' form='registerform' required><!--
        --><input style='float:right;' type='submit' class='submit' name='formname' form='registerform' value='Register'><!--
      --></div><!--
    --></div><!--
    --><div style='width: 2.5%; display: block; height:auto; float:right;'><!--
      --><button type='button' id='swapFormButton' onclick='swapForms()'><?php echo "$buttoncontent"; ?></button><!--
    --></div><!--
  --></form><!--
--></div>
