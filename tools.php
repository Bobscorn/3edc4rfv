<?php


// DATABASE CONNECTION CLASS
// WORKING
class DB
{
  private static $instance;
  private const DefaultUsername = 'root';
  private const DefaultPassword = '42';
  private const DefaultDB = 'FoodOrderSite';
  private const DefaultServer = 'localhost';
  private static $server;
  private static $username;
  private static $password;
  private static $database;

  public function SetServer($name)
  {
    self::$server = $name;
  }

  public function SetUser($name)
  {
    self::$username = $name;
  }

  public function SetPassword($word)
  {
    self::$password = $word;
  }

  public function SetDatabase($name)
  {
    self::$database = $name;
  }

  public function SetAll($sname, $uname, $pword, $db)
  {
    self::$server = $sname;
    self::$username = $uname;
    self::$password = $pword;
    self::$database = $db;
  }

  public function GetDefaultInstance()
  {
    if (is_null(self::$instance))
    {
      self::$instance = new mysqli(self::DefaultServer, self::DefaultUsername, self::DefaultPassword, self::DefaultDB);
    }
    return self::$instance;
  }

  public function GetInstance()
  {
    if (!is_null(self::$instance))
    {
      self::$instance = NULL;
    }

    if (DontExist(self::$server)) { self::$server = self::DefaultServer; }
    if (DontExist(self::$username)) { self::$username = self::DefaultUsername; }
    if (DontExist(self::$password)) { self::$password = self::DefaultPassword; }
    if (DontExist(self::$database)) { self::$database = self::DefaultDatabase; }

    self::$instance = new mysqli(self::$server, self::$username, self::$password, self::$database);
  }

}


// USER CLASS TO STORE SESSION DATA
// NOT WORKING
class User {
  private $username;
  private $connection;

  public function __construct($connection)
  {
    $this->connection = $connection;
  }

  public function CheckIfLoggedIn()
  {
    // Check if session or session variables don't exist
    // Verify Variables
    // Query Userid and expiry from token
    // Check query result
    // Check if expired
    // Query the name from userid
    // Check query result
    // Set member variable name, return true
    nEcho("In echo check");
    if (!SessionExists())
    {
      session_start();
    }
    if (!isset($_SESSION['usertoken']) || $_SESSION['usertoken'] == '')
    {
      dEcho("User token not set");
      return false;
    }
    $sessiontoken = MakeSecure($_SESSION['usertoken']);


    $conn = $this->connection;
    $checktokenquery = "SELECT `userid`,`expiry` FROM `usertokens` WHERE `token` = X'$sessiontoken'";
    $result = $conn->query($checktokenquery);
    dump_var($result);
    dump_var($checktokenquery);

    if (Exists($result))
    {
      dEcho("Check token result:");
      dump_var($result);
      $rightnow = new DateTime(NULL, timezone_open("Pacific/Auckland"));
      $resultarr = $result->fetch_assoc();
      $expiry = $resultarr['expiry'];

      dEcho("Expiry: $expiry");
      $expiryobj = new DateTime($expiry, timezone_open("Pacific/Auckland"));
      if ($rightnow > $expiryobj) { nEcho("Login Token has expired, Please login again"); $this->Logout(); return false;}

      $userid = $resultarr['userid'];
      $getnamequery = "SELECT name FROM accounts WHERE id = '$userid'";
      $name = $conn->query($getnamequery);
      if (Exists($name))
      {
        dEcho("Get Game result:");
        dump_var($name);
        $usertoken = $_SESSION['usertoken'];
        dump_var($name);
        $this->username = $name->fetch_assoc()['name'];
        dEcho("Name: $this->username");
        dEcho("Login check returned true");
        dEcho("User token: $usertoken");
        dump_var($_SESSION);
        return true;
      }
    }
    return false;
  }

  public function Login($name, $password)
  {
    // Check connection, session, and name + password
    // Make and Execute Query (not preparing)
    // Check Query result
    // Check if passwords match
    // Get UserID
    // Create token with expiry and uid, add token to session and database
    if (!SessionExists()) { session_start(); }
    if (isset($_SESSION['usertoken'])) { $this->Logout(); }
    if (!isset($name) || !isset($password))
    {
      nEcho("Login failed, password or name don't exist");
      return;
    }
    if(!isset($this->connection))
    {
      dEcho("Login failed, No DB Connection");
      return;
    }

    $conn = $this->connection;
    $getpasswordquery = "SELECT `password`, `id` FROM `accounts` WHERE `name` = '$name'";
    $thing = $conn->query($getpasswordquery);

    $thingrow = $thing->fetch_assoc();
    $dbpassword = $thingrow['password'];
    if (DontExist($dbpassword))
    {
      nEcho("No User by that name");
      return;
    }

    $uid = $thingrow['id'];
    if (password_verify($password, $dbpassword))
    {
      $token = bin2hex(random_bytes(16));
      dEcho("Token is: $token");
      $rightnow = new DateTime(NULL, timezone_open("Pacific/Auckland"));
      $expiresin = new DateInterval('PT5M'); // 5 Minutes
      $expiry = date_add($rightnow, $expiresin); // Make expiry 5 minutes from now

      dump_var($expiry);
      dump_var($expiresin);
      dump_var($rightnow);

      $expirystring = $expiry->format("Y/m/d H:i:s");

      $addtokenquery = "INSERT INTO `usertokens` (`token`,`userid`,`expiry`) VALUES (X'$token','$uid','$expirystring')";

      $_SESSION['usertoken'] = $token;
      $success = $conn->query($addtokenquery);
      $this->username = $name;
      if (Exists($success)) { nEcho("Logged in, $name"); }
    }
  }

  public function Logout()
  {
    // Reset Member Variable
    // Reset Session Variables
    if (!SessionExists()) { session_start(); }
    dEcho("Logging out");
    $this->username = '';
    if (isset($_SESSION['usertoken'])) {
      $_SESSION['usertoken'] = '';
      unset($_SESSION['usertoken']);
      dEcho("Unsetting utoken");
      $utoken = $_SESSION['usertoken'];
      dEcho("Token: $utoken");
    }
    dump_var($_SESSION);
  }

  public function Register($name, $password, $rcode)
  {
    // Check connection,
    // Secure Parameters,
    // Make & Execute Query (not preparing)
    // Check Expiry + Get Referee
    // Register using Referee
    if (DontExist($this->connection)) { nEcho("DB Connection is bad"); return; }
    $name     = MakeSecure($name);
    $password = MakeSecure($password);
    $rcode    = MakeSecure($rcode);

    dEcho("RCode: $rcode");

    $conn             = $this->connection;
    $checkrcodequery  = "SELECT `expiry`,`refereeid` FROM `rcodes` WHERE `code` = X'$rcode'";
    $expireree        = $conn->query($checkrcodequery);

    if (!Exists($expireree)) { nEcho("No matching referral code found"); return; }

    $expirereedata  = $expireree->fetch_assoc();
    $expirystr      = $expirereedata['expiry'];
    $refereeid      = $expirereedata['refereeid'];

    dump_var($expirystr);

    $expiryobj  = new DateTime($expirystr, timezone_open("Pacific/Auckland"));
    $rightnow   = new DateTime(NULL, timezone_open("Pacific/Auckland"));

    if ($rightnow > $expiryobj)
    {
      nEcho("Code has expired");
      return;
    }

    $getrefereenamequery = "SELECT `name` FROM `accounts` WHERE `id` = '$refereeid'";
    $refereesql = $conn->query($getrefereenamequery);
    $refereename = $refereesql->fetch_assoc()['name'];

    $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
    $registerquery  = "INSERT INTO `accounts` (`name`,`password`,`referee`)
                       VALUES ('$name','$hashedpassword','$refereename')";
    $success = $conn->query($registerquery);

    if ($success)
    {
      nEcho("Successfully created account '$name'");
    }

  }

  public function MakeWorkingReferralCode()
  {
    // Check connection
    // Generate 16 byte long code
    // Get expiry date (which will be 5 minutes from now)
    // Make + Execute Query (not preparing)
    // Return code if it worked
    if (DontExist($this->connection)) { echo "<h4>Failed making referral code, db connection failure</h4>"; return; }
    if (!isset($this->username) || $this->username == '') { nEcho("Cannot create Referral Code: No user logged in"); return; }
    $conn = $this->connection;

    $code = GenerateRandomHex(16);

    $rightnow = new DateTime(NULL, timezone_open("Pacific/Auckland"));
    $extratime = new DateInterval("PT5M");
    $expiry = date_add($rightnow, $extratime);

    $conn = $this->connection;
    $expirystring = $expiry->format("Y/m/d H:i:s");

    $getuidquery = "SELECT `id` FROM `accounts` WHERE name = '$this->username'";
    $useridsql = $conn->query($getuidquery);
    if (!Exists($useridsql))
    {
      dEcho("Failed to get UserID of current account");
      return;
    }
    $userid = $useridsql->fetch_assoc()['id'];

    $addcodequery = "INSERT INTO `rcodes` (`code`,`expiry`,`refereeid`) VALUES (X'$code','$expirystring','$userid')";
    $worked = $conn->query($addcodequery);

    if ($worked)
    {
      return $code;
    }

    return 'Failed to create code';
  }
}


function MakeSecure($var)
{
  $var = trim($var);
  $var = htmlspecialchars($var);
  $var = preg_replace("/[^A-Za-z0-9 ]/i", "", $var);
  return $var;
}

function DontExist($var)
{
  return !isset($var) || is_null($var) || $var == '';
}

function Exists($var)
{
  if (method_exists($var, 'fetch_assoc'))
  {
    return $var->num_rows > 0;
  }
  else
  {
    return !DontExist($var);
  }
}

function SessionExists()
{
  return session_status() == PHP_SESSION_ACTIVE;
}

function GenerateRandomHex($bytes)
{
  return bin2hex(random_bytes($bytes));
}

function dump_var($var)
{
  // Debug checkpoint, this way if I want to disable debug output I just comment the next line
  var_dump($var);
}

function dEcho($var)
{
  // Debug checkpoint, all echoed debug output goes through here, so easier to disable
  echo "<h4>$var</h4>";
}

function nEcho($var)
{
  // Provides a way of formatting output before it gets echoed
  echo "<h4>$var</h4>";
}

 ?>
