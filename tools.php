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
    if (!SessionExists()) { return false; }
    if (DontExist($_SESSION['usertoken']))
    {
      return false;
    }
    $sessiontoken = MakeSecure($_SESSION['usertoken']);
    $sessiontimeout = MakeSecure($_SESSION['tokenexpiry']);

    $conn = $this->connection;
    $checktokenquery = "SELECT `userid`,`expiry` FROM `usertokens` WHERE `token` = '$sessiontoken'";
    $result = $conn->query($checktokenquery);

    if (Exists($result))
    {
      $rightnow = new DateTime(NULL, timezone_open("Pacific/Auckland"));
      if ($rightnow > $result['expiry']) { nEcho("Token has expired"); return false;}

      $userid = $result['userid'];
      $getnamequery = "SELECT name FROM accounts WHERE id = '$userid'";
      $name = $conn->query($getnamequery);
      if (Exists($name))
      {
        dump_var($name);
        $this->username = $name;
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
    // Create token and expiry, add token to session and database
    if ($this->CheckIfLoggedIn()) { $this->Logout(); }
    if (!SessionExists()) { session_start(); }
    if (DontExist($name) || DontExist($password))
    {
      nEcho("Login failed, password or name don't exist");
      return;
    }
    if(DontExist($this->connection))
    {
      dEcho("Login failed, DB connection was bad");
      return;
    }

    $conn = $this->connection;
    $getpasswordquery = "SELECT password FROM accounts WHERE name = '$name'";
    $dbpassword = $conn->query($getpasswordquery);

    if (DontExist($dbpassword))
    {
      nEcho("No User by that name");
      return;
    }

    if (password_verify($password, $dbpassword))
    {
      $token = bin2hex(random_bytes(16));
      $rightnow = new DateTime(NULL, timezone_open("Pacific/Auckland"));
      $expiresin = new DateTime("@360", timezone_open("Pacific/Auckland"));
      $expiry = $rightnow + $expiresin;
      dump_var($expiry);
      dump_var($expiresin);
      dump_var($rightnow);
      $addtokenquery = "INSERT INTO `usertokens` (`token`,`expiry`) VALUES (x`$token`,`$expiry`)";

    }
  }

  public function Logout()
  {
    // Reset Member Variable
    // Reset Session Variables
    $this->username = '';
    $_SESSION['usertoken'] = '';
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

    $conn = $this->connection;
    $checkrcodequery = "SELECT expiry, referee FROM `rcodes` WHERE `code` = `$rcode`";
    $expireree = $conn->query($checkrcodequery);
    $expiry = $expireree['expiry'];
    $referee = $expireree['referee'];

    dump_var($expiry);

    $rightnow = new DateTime(NULL, timezone_open("Pacific/Auckland"));
    if ($rightnow > $expiry)
    {
      nEcho("Code has expired");
      return;
    }
    $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
    $registerquery = "INSERT INTO `accounts` (`name`,`password`,`referee`)
                      VALUES (`$name`,`$hashedpassword`,`$referee`)";
    $success = $conn->query($registerquery);

    if ($success)
    {
      nEcho("Successfully created account '$name'");
    }

  }

  public function MakeWorkingReferralCode()
  {
    // Check connection
    // Generate 20 byte long code
    // Get expiry date (360 seconds from now)
    // Make + Execute Query (not preparing)
    // Return code if it worked
    if (DontExist($this->connection)) { echo "<h4>Failed making referral code, db connection failure</h4>"; return; }

    $code = GenerateRandomHex(20);

    $rightnow = new DateTime(NULL, timezone_open("Pacific/Auckland"));
    $extratime = new DateTime("@360", timezone_open("Pacific/Auckland"));
    $expiry = $rightnow + $extratime;

    $conn = $this->connection;
    $addcodequery = "INSERT INTO `rcodes` (`code`,`expiry`) VALUES (`$code`,`$expiry`)";
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
  //$var = ;
}

function DontExist($var)
{
  return !isset($var) || is_null($var) || $var == '';
}

function Exists($var)
{
  return !DontExist($var);
}

function SessionExists()
{
  return session_status() == PHP_SESSION_ACTIVE;
}

function GenerateRandomHex($bytes)
{
  return hex2bin(random_bytes($bytes));
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
