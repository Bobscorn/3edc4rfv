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
/*  switch (number)
  {
  case '1':
    var id = document.getElementById('searchid1').value;
    document.getElementById('orderid').value = id;
    break;
  case '2':
    var id = document.getElementById('searchid2').value;
    document.getElementById('orderid').value = id;
    break;*/
//  case '3':
    var id = document.getElementById('searchid'+number.toString()).value;
    document.getElementById('orderid').value = id;
//    break;
  }
}
