function swapForms()
{
  var loginvisible = document.getElementById('logindiv').style.display == 'inline';
  var registervisible = document.getElementById('registerdiv').style.display == 'inline';
  if (registervisible)
  {
    document.getElementById('logindiv').style.display = 'inline';
    document.getElementById('registerdiv').style.display = 'none';
    document.getElementById('swapFormButton').innerHTML = 'Register?';
    return;
  }
  if (loginvisible)
  {
    document.getElementById('logindiv').style.display = 'none';
    document.getElementById('registerdiv').style.display = 'inline';
    document.getElementById('swapFormButton').innerHTML = 'Login?';
    return;
  }
  document.getElementById('logindiv').style.display = 'inline';
  document.getElementById('registerdiv').style.display = 'none';
  document.getElementById('swapFormButton').innerHTML = 'Register?';
}

function togglePHPOutput()
{
  var OutputVisible = document.getElementById('phpoutput').style.display != 'none';
  if (OutputVisible)
  {
    document.getElementById('phpoutput').style.display = 'none';
  }
  else
  {
    document.getElementById('phpoutput').style.display = 'block';
    if (document.getElementById('phpoutput').children.length == 0)
    {
      document.getElementById('phpoutput').innerHTML = '<h4>No output</h4>';
    }
  }
}


setTimeout(function() {
  document.getElementById('phpoutput').style.display = 'none';
}, 4000);
