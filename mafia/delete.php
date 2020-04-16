<?php
$page_title = '- Delete Setup';

include ('includes/header.inc');
include ('includes/checklogin.inc');
require_once ('../../db/mysql_connect_mafia.php');

if ((isset($_GET['setup'])) && (is_numeric($_GET['setup']))) {
 $query = "SELECT setup_name, setup_description, setup_players, setup_creator, setup_allknown FROM mafia_setups WHERE setup_id={$_GET['setup']}";
 $result = @mysql_query($query);
 if (mysql_num_rows($result) != 1) {
  echo "<h2>Setup {$_GET['setup']} not found. Please try again. If the problem persists please <a href=\"contact.php\">contact the administrator.</a></h2>";
  include ('./includes/footer.inc');
  exit();
 }
 $setup = mysql_fetch_array($result, MYSQL_NUM);
 mysql_free_result($result);

 if ($_SESSION['player_id'] != $setup[3]) {
  if(is_numeric($_SESSION['player_id'])) {
   $query = "SELECT player_type FROM mafia_players WHERE player_id={$_SESSION['player_id']}";
   $result = @mysql_query($query);
   $player = mysql_fetch_array($result, MYSQL_NUM);
   if ($player[0] != 'Admin') {
    echo '<p>You do not have permission to delete this setup</p>';
    echo '<p><a href="'.$_GET['redirect'].'">Back</a></p>';
    include ('./includes/footer.inc');
    exit();
   }
  } else {
    echo '<p>Bad cookies. Try logging out and back in again</p>';
    include ('./includes/footer.inc');
    exit();
  }
 }

 if (isset($_POST['submitted'])) {
  $query = "DELETE FROM mafia_setups WHERE setup_id={$_GET['setup']}";
  $result = mysql_query($query);
  if (mysql_affected_rows() == 1) {
   echo '<p>Setup deleted</p>';
  } else {
   echo '<p>Database error. Please try again</p>';
  }
  echo '<p><a href="'.$_GET['redirect'].'">Back</a></p>';
 } else {

 $query = "SELECT player_name FROM mafia_players WHERE player_id={$setup[3]}";
 $result = @mysql_query($query);
 if (mysql_num_rows($result) == 1) {
  $setup[] = mysql_fetch_array($result, MYSQL_NUM);
 }
 
 echo "<h2>Are you sure you want to delete the following setup?</h2>\n";
 echo "<p><b>{$setup[0]}</b> by <a href=\"profile.php?p={$setup[3]}\">{$setup[5][0]}</a></p>";
 echo "<p>This setup is for <b>{$setup[2]}</b> players. The following roles are included:<br />".nl2br($setup[1])."</p>";

 if ($setup[4] == 'no') {
  echo '<p><b>Note:</b> The roles of this setup are not revealed in full by the above list.</p>'; 
 }
?>

<form action="delete.php?setup=<?php echo $_GET['setup'].'&redirect='.$_GET['redirect'] ?>" method="post">
<fieldset><legend>Confirm deletion</legend>
 <input type="hidden" name="submitted" value="true" />
 <input type="submit" class="sepia" name="redirect" value="Delete" />  
 
<?php
 }
} else {
    echo '<p>Invalid setup ID. Please try again.</p>';
    echo '<p><a href="'.$_GET['redirect'].'">Back</a></p>';
include ('includes/footer.inc');
}
?>
