// Everything javascript in this website

// This function swaps between login and register form
function swapForms()
{
  var loginvisible = document.getElementById('logindiv').style.display == 'inline';
  var registervisible = document.getElementById('registerdiv').style.display == 'inline';
  if (registervisible) // Switch to login form, change switch button to say 'Register?'
  {
    document.getElementById('logindiv').style.display = 'inline';
    document.getElementById('registerdiv').style.display = 'none';
    document.getElementById('swapFormButton').innerHTML = 'Register?';
    return;
  }
  if (loginvisible) // Switch to register form, change switch button to say 'Login?'
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

// This function toggles visibility of the php output at the top of the page
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
    if (document.getElementById('phpoutput').children.length == 0) // If phpoutput is empty
    {
      document.getElementById('phpoutput').innerHTML = '<h4>Nothing to see here</h4>';
    }
  }
}

// Change title of page, done here because php can't edit already printed content
// And php determines title after content might have been printed
document.getElementById('title').innerHTML = document.getElementById('titlevalue').innerHTML;

// Automatically hide phpoutput after 4 seconds (4000ms)
setTimeout(
  function() {
    document.getElementById('phpoutput').style.display = 'none';
  },
4000);
