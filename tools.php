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
    if (!SessionExists()) { return false; }
    if (DontExist($_SESSION['usertoken']) || DontExist($_SESSION['tokenexpiry']))
    {
      return false;
    }
    $sessiontoken = MakeSecure($_SESSION['usertoken']);
    $sessiontimeout = MakeSecure($_SESSION['tokenexpiry']);

    $conn = $this->connection;
    $checktokenquery = "SELECT `userid` FROM `usertokens` WHERE `token` = '$sessiontoken'";
    $userid = $conn->query($checktokenquery);

    if (Exists($userid))
    {
      $getnamequery = "SELECT name FROM accounts WHERE id = '$userid'";
      $name = $conn->query($getnamequery);
      if (Exists($name))
      {
        var_dump($name);
        $this->username = $name;
        return true;
      }
    }
    return false;
  }

  public function Login($name, $password)
  {
    if ($this->CheckIfLoggedIn()) { $this->Logout(); }
    if (!SessionExists()) { session_start(); }
    if (DontExist($name) || DontExist($password))
    {
      echo "<h4> Login failed, password or name don't exist</h4>";
      return;
    }
    if(DontExist($this->connection))
    {
      echo "<h4> Login failed, DB connection was bad</h4>";
      return;
    }

    $conn = $this->connection;
    $getpasswordquery = "SELECT password FROM accounts WHERE name = '$name'";
    $dbpassword = $conn->query($getpasswordquery);

    if (DontExist($dbpassword))
    {
      echo "<h4> Login Failed, user doesn't exist</h4>";
      return;
    }

    if (password_verify($password, $dbpassword))
    {
      $token = bin2hex(random_bytes(16));
      $rightnow = new DateTime(NULL, timezone_open("Pacific/Auckland"));
      $expiresin = new DateTime("@360", timezone_open("Pacific/Auckland"));
      $expiry = $rightnow + $expiresin;
      var_dump($expiry);
      var_dump($expiresin);
      var_dump($rightnow);
      $addtokenquery = "INSERT INTO `usertokens` (`token`,`expiry`) VALUES (x`$token`,`$expiry`)";

    }
  }

  public function Register($name, $password, $rcode)
  {
    if (DontExist($this->connection)) { echo "<h4>DB Connection is bad</h4>"; return; }
    $name = MakeSecure($name);
    $password = MakeSecure($password);
    $rcode = MakeSecure($rcode);

    $conn = $this->connection;

    $checkrcodequery = "SELECT expiry FROM `rcodes` WHERE `code` = `$rcode`";
    $expiry = $conn->query($checkrcodequery);

    var_dump($expiry);

  }

  public function MakeReferralCode()
  {
    
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

 ?>
