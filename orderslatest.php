<?php
require 'tools.php';

$connection = DB::GetDefaultInstance();
$user = new User($connection);

if ($user->CheckIfLoggedIn())
{
  $getlatestweekquery = "SELECT `week` FROM `orders` ORDER BY `week` DESC";
  $lastestweeksql = $connection->query($getlatestweekquery);
  if (Exists($lastestweeksql))
  {
    $latestweek = $lastestweeksql->fetch_assoc()['week'];

    $getlatestordersquery = "SELECT * FROM `orders` WHERE `week` = '$latestweek'";
    $orderssql = $connection->query($getlatestordersquery);

    $currentrow = '';
    echo "<table>
            <tr>
              <th>ID:</th>
              <th>Week:</th>
              <th>Ordered By:</th>
              <th>ItemID</th>
              <th>Amount</th>
            </tr>";
    while (!is_null($currentrow = $orderssql->fetch_assoc()))
    {
      $id = $currentrow['id'];
      $week = $currentrow['week'];
      $orderer = $currentrow['orderer'];
      $itemid = $currentrow['itemid'];
      $amount = $currentrow['amount'];
      echo "<tr>
              <td>$id</td>
              <td>$week</td>
              <td>$orderer</td>
              <td>$itemid</td>
              <td>$amount</td>
            </tr>";
    }
    echo "</table>";
  }
  else
  {
    nEcho("No Results to show");
  }
}
else
{
  nEcho("User needs to be logged in to view orderse");
}


 ?>

<link rel="stylesheet" type="text/css" href="bob.css">
