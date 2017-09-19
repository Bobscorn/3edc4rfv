<?php

// This will be used to turn debug output of specific functions/segments on or off,
// As my debug output can be messy and irrelevant at times
class Debug
{
  public static $Output = array();
  public static $Current = '';

  public function Output($checkpoint, $enabled)
  {
    if (isset($checkpoint) && !is_null($checkpoint))
    {
      self::$Output["$checkpoint"] = $enabled;
      self::$Current = $checkpoint;
    }
  }
}

$searchtooltips = array("(╯°□°）╯︵ ┻━┻", "٩(⁎❛ᴗ❛⁎)۶", "(͡° ͜ʖ ͡°)", "¯\(°_o)/¯", "( ▀ ͜͞ʖ▀)", "( ͠° ͟ʖ ͡°)", "( ͡°╭͜ʖ╮͡° )", "ᕦ(ò_óˇ)ᕤ", "(^‿^)", "anything", "anything", "anything");
$searchtooltip = $searchtooltips[array_rand($searchtooltips)];

// DATABASE CONNECTION CLASS
// WORKING
class DB
{
  private static $instance;
  private const DefaultUsername = 'root';
  private const DefaultPassword = '42';
  private const DefaultDB = 'OC13304';
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

    if (DontExist(self::$server))   { self::$server = self::DefaultServer; }
    if (DontExist(self::$username)) { self::$username = self::DefaultUsername; }
    if (DontExist(self::$password)) { self::$password = self::DefaultPassword; }
    if (DontExist(self::$database)) { self::$database = self::DefaultDatabase; }

    self::$instance = new mysqli(self::$server, self::$username, self::$password, self::$database);
  }

}


// USER CLASS TO STORE SESSION DATA
// NOT WORKING - ACTUALLY FULLY WORKING
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
    Debug::$Current = 'logincheck';
    $_DEBUGOUT['logincheck'] = false; // No debug output
    callStack();
    dEcho("In echo check");
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

    $result3 = '';

    $conn = $this->connection;

    $getalltokensquery = "SELECT * FROM `usertokens` ORDER BY `id` DESC";
    $results = $conn->query($getalltokensquery);

    TableResults('margin: 0 auto; width: 542px; overflow-x: scroll; height: 200px; overflow-y: scroll;', $results, 'id', 'token', 'userid', 'expiry');

    $checktokenquery = "SELECT * FROM `usertokens` WHERE `token` = X'$sessiontoken'";
    $result3 = $conn->query($checktokenquery);
    dump_var($conn);
    dEcho("Unique Checkpoint 42");
    dump_vars($sessiontoken, $result3, $checktokenquery);
    dEcho("Unique Checkpoint 42 End");

    TableResults('margin: 0 auto; width: 542px; overflow-x: scroll; height: 200px; overflow-y: scroll;', $result3, 'id', 'token', 'userid', 'expiry');

    $result3->data_seek(0);

    if (Exists($result3))
    {
      $resultarr = $result3->fetch_assoc();
      dEcho("balls");
      dump_var($resultarr);
      $thingy = bin2hex($resultarr['token']);
      dEcho("$thingy");
      $expiry = $resultarr['expiry'];

      dEcho("Expiry: $expiry");
      $expiryobj = new DateTime($expiry, timezone_open("Pacific/Auckland"));
      $rightnow = new DateTime(NULL, timezone_open("Pacific/Auckland"));
      if ($rightnow > $expiryobj)
      {
        nEcho("Login Token has expired, Please login again");
        dump_var($expiryobj);
        dump_var($rightnow);
        dEcho("Thing");
        $hoobidy = $expiryobj->format("Y/m/d H:i:s");
        $doodah = $rightnow->format("Y/m/d H:i:s");
        dEcho($hoobidy);
        dEcho($doodah);
        $this->Logout();
        return false;
      }

      $userid = $resultarr['userid'];
      $getnamequery = "SELECT `name` FROM `accounts` WHERE `id` = '$userid'";
      $name = $conn->query($getnamequery);
      dEcho("Name checkpoint");
      dump_var($name);
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

        // Renew token
        $rightnow = new DateTime(NULL, timezone_open("Pacific/Auckland"));
        $in5minutes = new DateInterval("PT5M");
        $newexpiry = date_add($rightnow, $in5minutes);
        $newexpirystring = $newexpiry->format("Y/m/d H:i:s");
        $renewtokenquery = "UPDATE `usertokens` SET `expiry` = '$newexpirystring' WHERE `userid` = '$userid'";
        dEcho("Renew query: $renewtokenquery");
        dEcho("Login Token Renewed");
        $success = $conn->query($renewtokenquery);
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
      dump_var($addtokenquery);

      $_SESSION['usertoken'] = $token;
      $success = $conn->query($addtokenquery);
      $this->username = $name;
      if (Exists($success)) { nEcho("Logged in, $name"); }
    }
    else
    {
      nEcho("Bad password");
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
      callStack();
    }
    dump_var($_SESSION);
  }

  public function Register($name, $password)
  {
    // Check connection,
    // Secure Parameters,
    // Check for existing account
    // Register
    if (DontExist($this->connection)) { nEcho("DB Connection is bad"); return; }
    $name     = MakeSecure($name);
    $password = MakeSecure($password);

    $conn       = $this->connection;

    $checkforexistingaccountquery = "SELECT `id` FROM `accounts` WHERE `name` = '$name'";
    $anotheraccount = $conn->query($checkforexistingaccountquery);

    dump_var($anotheraccount);
    if (Exists($anotheraccount))
    {
      nEcho("Account by that name already exists");
      return;
    }

    $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
    $registerquery  = "INSERT INTO `accounts` (`name`,`password`)
                       VALUES ('$name','$hashedpassword')";
    $success = $conn->query($registerquery);

    if (Exists($success))
    {
      nEcho("Successfully created account '$name'");
    }
    else
    {
      nEcho("Failed to create account, name might be taken");
    }

  }

  public function Search($database, $searchfor)
  {
    if (is_null($database))
    {
      $database = 'items';
    }

    $searchfor = MakeSecureSymbols($searchfor);
    if (empty($searchfor))
    {
      return 0;
    }

    $results = array();

    $searchfor = str_ireplace(", ", ",", $searchfor);
    if (stristr($searchfor, ","))
    {
      // Split the search string into a maximum of 3 search queries
      $searchqueries = explode(",", $searchfor, 3);
      foreach ($searchqueries as $searchpar)
      {
        // Merge the current array
        $results = array_merge(Search($database, $searchpar), $results);
      }
    }

    $singlewords = explode(" ", $searchfor);
    $NoOfSearch = sizeof($singlewords);
    if ($NoOfSearch == 0)
    // Return nothing

    // Search full string, then individual words
    $fullstringsearch = "SELECT products.id, name, description, date, user, tags
        MATCH (`name`) AGAINST ('$searchfor' IN BOOLEAN MODE) AS rank FROM
          ( `products` INNER JOIN
            `tags` ON `products`.`id` = `tags`.`productid` )
            ORDER BY rank DESC;";

    $fullstringresults = $database->query($fullstringsearch);

    $allresults = array();
    $thing = '';
    $i = 0;
    for (; ($thing = $fullstringresults->fetch_assoc()) != null; $i++)
    {
      $allresults["$i"] = $thing;
    }

    for ($j = 0; $j < $NoOfSearch; $j++)
    {
      $individualstringsearch =
          "SELECT products.id, name, description, date, user, tags
          MATCH (`name`) AGAINST ('$searchfor' IN BOOLEAN MODE) AS rank FROM
          ( `products` INNER JOIN
          `tags` ON `products`.`id` = `tags`.`productid` )
          ORDER BY rank DESC;";

      $individualresult = $database->query($individualstringsearch);
      if (Exists($individualresult))
      {
        $e = $i + $j;
        $allresults["$e"] = $individualresult->fetch_assoc();
      }
    }

    return $allresults;
  }

  public function GetName()
  {
    return $this->username;
  }

  public function GetID()
  {
    if (isset($this->username))
    {
      $getidquery = "SELECT `id` FROM `accounts` WHERE `name` = '$this->username'";
      $result = $this->connection->query($getidquery);

      return $result->fetch_assoc()['id'];
    }
  }
}


class Product
{
  public $name;
  public $description;
  public $date;
  public $author;
  public $tags;
  public $pid;

  public function __construct($namein, $descin = '', $datein = '42/69/2042', $authorin = 'Trump', $tagsin = "trumpage, chinese", $pidin = 6969)
  {
    dEcho("New Product made");
    dump_var(func_get_args());
    $this->name         = $namein;
    $this->description  = $descin;
    $this->date         = $datein;
    $this->author       = $authorin;
    $this->tags         = $tagsin;
    $this->pid          = $pidin;
  }
}

class Products
{
  public static $ProductArray         = array();
  public static $CurrentProductName   = '';

  public function Add($product, $name)
  {
    self::$ProductArray["$name"]  = $product;
    self::$CurrentProductName     = $name;
  }

  public function Get()
  {
    return self::$ProductArray[self::$CurrentProductName];
  }
}


function MakeSecure($var)
{
  $var = trim($var);
  $var = htmlspecialchars($var);

  // Makes the resulting string only Alphanumeric (with spaces)
  $var = preg_replace("/[^A-Za-z0-9 ]/i", "", $var);
  return $var;
}

function MakeSecureSymbols($var)
{
  // Replaces all characters except A-Z a-z 0-9 (space) and symbols !"#$%&'()*+,-. with nothing (remove all non Alphanumeric and non symbols)
  $var = preg_replace("/[^A-Za-z0-9 !-.]/i", "", $var);
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
    if (is_null($var->lengths) && $var->num_rows <= 0)
    {
      dEcho("Exists called on variable with no lengths, but with rows");
      callStack();
    }
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
  // Uncomment next line to get debug output
  if (DebugOutputEnabled())
  {
    callStack();
    var_dump($var);
    echo "\n";
  }
}

function DebugOutputEnabled()
{
  return isset(Debug::$Current) && isset(Debug::$Output[Debug::$Current]) && Debug::$Output[Debug::$Current] == true;
}

function callStack()
{
  // Debug checkpoint, Uncomment next line to enable debug output
  if (DebugOutputEnabled()) {
    debug_print_backtrace();
    echo "\n";
  }
}

function dump_vars()
{
  $args = func_get_args();
  $arr = array_values($args);
  foreach ($arr as $i)
  {
    // No need for debug check as dEcho and dump_var does that
    dump_var($i);
    echo "\n";
  }
}

function dEcho($var)
{
  // Debug checkpoint, all echoed debug output goes through here, so easier to disable
  // Uncomment next line to get debug output
  if (DebugOutputEnabled()) {
    echo "<h4>$var\n</h4>";
  }
}

function nEcho($var)
{
  // Provides a way of formatting output before it gets echoed
  echo "<h4>$var\n</h4>";
}

function TableResults($formatstring, $mysqliobject)
{
  if (!method_exists($mysqliobject, 'fetch_assoc'))
  {
    return;
  }
  if (!DebugOutputEnabled())
  {
    return;
  }
  $args = func_get_args();
  $arr = array_values($args);
  $thing = '';
  echo "<div style='$formatstring'>";
  echo "<table>
          <tr>";
  // Skip first two elements as they are format string and mysqliobject
  for ($i = 2; $i < sizeof($arr); $i++)
  {
    $title = strtoupper($arr[$i]);
    echo "<th>$title .</th>";
  }
  echo "</tr>";

  while (!is_null($thing = $mysqliobject->fetch_assoc()))
  {
    echo "<tr>";
    foreach ($thing as $i)
    {
      if (isBinary($i))
      {
        $i = bin2hex($i);
      }
      echo "<td>$i</td>";
    }
    echo "</tr>";
  }

  echo "</table>";
  echo "</div>";
}

function MakeRow()
{
  $args = func_get_args();
  echo "<tr>";
  for ($i = 0; $i < sizeof($args); $i++)
  {
    $thing = $args[$i];
    echo "<td>$thing</td>";
  }
  echo "</tr>";
}

function MakeTitleRow()
{
  $args = func_get_args();
  echo "<tr>";
  for ($i = 0; $i < sizeof($args); $i++)
  {
    $thing = $args[$i];
    echo "<th>$thing</th>";
  }
  echo "</tr>";
}

function isBinary($str) {
    return preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
}

 ?>
