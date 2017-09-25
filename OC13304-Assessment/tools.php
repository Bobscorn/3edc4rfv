<?php

// This will be used to turn debug output of specific functions/segments on or off,
// As my debug output can be messy and irrelevant at times,
// This cleans it up slightly
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

# Just a nice 'easter egg' for the search bar
$searchtooltips = array("(╯°□°）╯︵ ┻━┻", "٩(⁎❛ᴗ❛⁎)۶", "(͡° ͜ʖ ͡°)", "¯\(°_o)/¯", "( ▀ ͜͞ʖ▀)", "( ͠° ͟ʖ ͡°)", "( ͡°╭͜ʖ╮͡° )", "ᕦ(ò_óˇ)ᕤ", "(^‿^)", "anything", "anything", "anything");
$searchtooltip = $searchtooltips[array_rand($searchtooltips)];

// Class to handle connections to a database, this prevents duplicate connections
// That may arise from including multiple other php files
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

  public function SetAll($sname, $uname = self::DefaultUsername, $pword = self::DefaultPassword, $db = self::DefaultDB)
  {
    self::$server = $sname;
    self::$username = $uname;
    self::$password = $pword;
    self::$database = $db;
  }

  # Get an instance with default parameters
  public function GetDefaultInstance()
  {
    if (is_null(self::$instance))
    {
      self::$instance = new mysqli(self::DefaultServer, self::DefaultUsername, self::DefaultPassword, self::DefaultDB);
    }
    return self::$instance;
  }

  # Get instance with specific parameters
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


# Class to contain User based functions
class User {
  private $username;
  private $connection;

  public function __construct($connection)
  {
    $this->connection = $connection;
  }

  public function CheckIfLoggedIn()
  {
    # This function checks the timeout of tokens in the database,
    # and if token matches stored username & password
    # It doesn't store the state of the User to prevent exploits
    Debug::Output("logincheck", false);
    callStack();
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

    # Debug checkpoint displays latest login tokens
    TableResults('margin: 0 auto; width: 542px; overflow-x: scroll; height: 200px; overflow-y: scroll;', $results, 'id', 'token', 'userid', 'expiry');

    # Get token that matches the one stored in session
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
      dEcho("balls"); # Unique echo statement to know what the output is :P
      dump_var($resultarr);
      $thingy = bin2hex($resultarr['token']);
      dEcho("$thingy");
      $expiry = $resultarr['expiry'];

      dEcho("Expiry: $expiry");
      # Database Expiry is currently a string, can't compare time value of strings
      $expiryobj = new DateTime($expiry, timezone_open("Pacific/Auckland"));
      $rightnow = new DateTime(NULL, timezone_open("Pacific/Auckland"));
      if ($rightnow > $expiryobj)
      {
        nEcho("Login Token has expired, Please login again");
        dump_vars($expiryobj, $rightnow);
        dEcho("Thing");
        $hoobidy = $expiryobj->format("Y/m/d H:i:s");
        $doodah = $rightnow->format("Y/m/d H:i:s");
        dump_vars($hoobidy, $doodah);
        $this->Logout();
        return false;
      }

      # If token hasn't expired
      $userid = $resultarr['userid'];
      $getnamequery = "SELECT `name` FROM `accounts` WHERE `id` = '$userid'";
      $name = $conn->query($getnamequery);
      dEcho("Name checkpoint");
      dump_var($name);
      if (Exists($name))
      {
        dEcho("Get Game result:");
        $usertoken = $_SESSION['usertoken'];
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
    # Check variables this function relies on
    # (Session, usertoken, username, password and Database connection)
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

    # Get password and userid to use in later queries
    $conn = $this->connection;
    $getpasswordquery = "SELECT `password`, `id` FROM `accounts` WHERE `name` = '$name'";
    $thing = $conn->query($getpasswordquery);

    $userdata = $thing->fetch_assoc();
    $dbpassword = $userdata['password'];
    if (DontExist($dbpassword))
    {
      nEcho("No User by that name");
      return;
    }

    # Check passwords, then create random 128bit login token with 5 minute expiry
    if (password_verify($password, $dbpassword))
    {
      $token = bin2hex(random_bytes(16));
      dEcho("Token is: $token");
      $rightnow = new DateTime(NULL, timezone_open("Pacific/Auckland"));
      $expiresin = new DateInterval('PT5M'); // 5 Minutes
      $expiry = date_add($rightnow, $expiresin); // Make expiry 5 minutes from now

      dump_vars($expiry, $expiresin, $rightnow);

      $expirystring = $expiry->format("Y/m/d H:i:s");
      $uid = $userdata['id'];
      $addtokenquery = "INSERT INTO `usertokens` (`token`,`userid`,`expiry`) VALUES (X'$token','$uid','$expirystring')";
      dump_var($addtokenquery);

      $_SESSION['usertoken'] = $token;
      $success = $conn->query($addtokenquery);
      $this->username = $name; # Store name for later use
      if (Exists($success)) { nEcho("Logged in, $name"); }
    }
    else
    {
      nEcho("Bad password");
    }
  }

  # Logs the stored user out
  # Removes token from session, removing token from database should be unnecesary
  public function Logout()
  {
    # Start session in order to reset the stored token
    if (!SessionExists()) { session_start(); }
    dEcho("Logging out");
    $this->username = '';
    if (isset($_SESSION['usertoken']))
    {
      $_SESSION['usertoken'] = '';
      unset($_SESSION['usertoken']);

      dEcho("Unsetting utoken");
      callStack();
    }
    dump_var($_SESSION);
  }

  # Checks for existing account by supplied name,
  # Creates entry in accounts database if not so, ONLY that
  public function Register($name, $password)
  {
    if (DontExist($this->connection)) { nEcho("DB Connection is bad"); return; }
    $name     = MakeSecure($name);
    $password = MakeSecure($password);

    $conn     = $this->connection;

    $checkforexistingaccountquery = "SELECT `id` FROM `accounts` WHERE `name` = '$name'";
    $anotheraccount = $conn->query($checkforexistingaccountquery);

    dump_var($anotheraccount);
    if (Exists($anotheraccount))
    {
      nEcho("Account by that name already exists");
      return;
    }

    $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
    $registerquery  = "INSERT INTO `accounts` (`name`,`password`) VALUES ('$name','$hashedpassword')";
    $success        = $conn->query($registerquery);

    if (Exists($success))
    {
      nEcho("Successfully created account '$name'");
    }
    else
    {
      nEcho("Failed to create account, name might be taken");
    }

  }

  # This function is a failure, search is now done in search.php
/*public function Search($searchfor, $database = 'products')
  {

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
  }*/

  public function GetName()
  {
    return $this->username;
  }

  # User class doesn't store the id,
  # Query database for id matching the stored username
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

# Used to encapsulate Product information into a single datatype
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

# Used to pass product information between php files
# Product information could also be stored in the $_SESSION variable
# But I prefer to not store things in $_SESSION
class Products
{
  public static $ProductArray         = array();
  public static $CurrentProductName   = '';

  # Add a product to the array, and set the current product to the one supplied
  public function Add($product, $name)
  {
    self::$ProductArray["$name"]  = $product;
    self::$CurrentProductName     = $name;
  }

  # Get last product added
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
  // Replaces all characters except A-Z a-z 0-9 (space) and symbols !"#$%&'()*+,-. with nothing (remove all non Alphanumeric and non symbols
  return preg_replace("/[^A-Za-z0-9 !-.\n]/i", "", $var);
}

function MakeSecureCommas($var)
{
  return preg_replace("/[^A-Za-z0-9 ,]/i", "", $var);
}

function DontExist($var)
{
  return !Exists($var);
}

# Shortens checking if a variable is set, and not ''
# Also checks if its a mysqli_result and returns whether its empty
function Exists($var)
{
  # Check if its a mysqli_result
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
    return isset($var) && $var != '';
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

# A formatting thing, prints a table with column names supplied after $mysqliobject (arguments 3 onwards)
# Gives the table css of $formatstring
function TableResults($formatstring, $mysqliobject/*, column names*/)
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
  # Start the table
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

  # While the next row isn't null, print it
  # Assuming the row has columns equivalent to the column names supplied
  while (!is_null($thing = $mysqliobject->fetch_assoc()))
  {
    echo "<tr>";
    foreach ($thing as $i)
    {
      # token database contains binary datatypes, which output incorrectly unless converted to hex
      if (isBinary($i)) { $i = bin2hex($i); }
      echo "<td>$i</td>";
    }
    echo "</tr>";
  }
  # Close table
  echo "</table>";
  echo "</div>";
}

function isBinary($str) {
    return preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
}

# Algorithm showed on https://stackoverflow.com/questions/2394246/algorithm-to-select-a-single-random-combination-of-values/2394292#2394292
# Not entirely sure how it works, it isn't used in this website anyway
function UniqueRandomArray($amount, $cap)
{
  $thing = array();
  $p = 0;

  # A for loop that iterates $amount times
  for ($j = $cap - $amount; $j < $cap; $j++, $p++)
  {
    $t = mt_rand(1, $j);
    # if $t is already in the array, put $j in instead
    if (in_array($t, $thing))
    {
      $thing["$p"] = $j;
    }
    else
    {
      $thing["$p"] = $t;
    }
  }
}

 ?>
