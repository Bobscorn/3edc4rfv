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

function latestOrders()
{
  if (document.getElementById('latest').innerHTML == 'Latest Orders') {
    document.getElementById('latest').innerHTML = 'Hide';
    document.getElementById('theiframe').src = 'orderslatest.php';
    document.getElementById('theiframe').style.display = 'block';
  }
  else
  {
    document.getElementById('latest').innerHTML = 'Latest Orders';
    document.getElementById('theiframe').src = '';
    document.getElementById('theiframe').style.display = 'none';
  }
}

function allOrders()
{
  if (document.getElementById('allorders').innerHTML == 'All Orders')
  {
    document.getElementById('allorders').innerHTML = 'Hide';
    document.getElementById('theotheriframe').src = 'ordersall.php';
    document.getElementById('theotheriframe').style.display = 'block';
  }
  else
  {
    document.getElementById('allorders').innerHTML = 'All Orders';
    document.getElementById('theotheriframe').src = '';
    document.getElementById('theotheriframe').style.display = 'none';
  }
}
