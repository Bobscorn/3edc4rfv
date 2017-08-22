function swapForms()
{
  var loginvisible = document.getElementById('loginform').style.display == 'inline-block';
  var registervisible = document.getElementById('registerform').style.display == 'inline-block';
  if (registervisible)
  {
    document.getElementById('loginform').style.display = 'inline-block';
    document.getElementById('registerform').style.display = 'none';
    document.getElementById('swapFormButton').textContent = 'Wanting to Register?';
    return;
  }
  if (loginvisible)
  {
    document.getElementById('loginform').style.display = 'none';
    document.getElementById('registerform').style.display = 'inline-block';
    document.getElementById('swapFormButton').textContent = 'Looking for Login?';
    return;
  }
  document.getElementById('loginform').style.display = 'inline-block';
  document.getElementById('registerform').style.display = 'none';
  document.getElementById('swapFormButton').textContent = 'Wanting to Register?';
}
