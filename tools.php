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
  private $password; // Hashed version
  private $connection;

  public function __construct($connection)
  {
    $this->connection = $connection;
  }

  private function CheckIfLoggedIn()
  {
    if (DontExist($_SESSION['username']) || DontExist($_SESSION['password']))
    {
      return false;
    }
    $storedusername = MakeSecure($_SESSION['username']);
    $storedpassword = MakeSecure($_SESSION['password']);

    $conn = $this->connection;
    $getpasswordquery = "SELECT password FROM accounts WHERE username = '$storedusername'";
    $password = $conn->query($getpasswordquery);

    if (Exists($password))
    {
      if (/*password match*/)
    }
    // FINISH CHECKING IF PASSWORD MATCHES

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

 ?>
