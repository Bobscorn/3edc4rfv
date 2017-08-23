function swapForms()
{
  var loginvisible = document.getElementById('loginform').style.display == 'inline-block';
  var registervisible = document.getElementById('registerform').style.display == 'inline-block';
  if (registervisible)
  {
    document.getElementById('loginform').style.display = 'inline-block';
    document.getElementById('registerform').style.display = 'none';
    document.getElementById('swapFormButton').innerHTML = 'Wanting to Register?';
    return;
  }
  if (loginvisible)
  {
    document.getElementById('loginform').style.display = 'none';
    document.getElementById('registerform').style.display = 'inline-block';
    document.getElementById('swapFormButton').innerHTML = 'Looking for Login?';
    return;
  }
  document.getElementById('loginform').style.display = 'inline-block';
  document.getElementById('registerform').style.display = 'none';
  document.getElementById('swapFormButton').innerHTML = 'Wanting to Register?';
}

function searchClicked(number)
{
  var id = document.getElementById('searchid'+number).value;
  document.getElementById('orderid').value = id;
}

function lastestOrders()
{
  alert("deez nuts");
  document.getElementById('theiframe').src = 'orderslatest.php';
  document.getElementById('theiframe').style.display = 'block';
}
