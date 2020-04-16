<?php
$page_title = '- Player Profile';
include ('includes/header.inc');
include ('includes/checklogin.inc');
require_once ('../../db/mysql_connect_mafia.php');

echo "<h1>Player Profile</h1>\n";

if (is_numeric($_GET['p'])) {
 $pid = $_GET['p'];
} else if (is_numeric($_SESSION['player_id'])) {
 $pid = $_SESSION['player_id'];
} else {
 echo '<p>Invalid player id, please try again</p>';
}

$query = "SELECT * FROM mafia_players WHERE player_id=$pid";

$result = mysql_query($query);

if (mysql_num_rows($result) == 1) {
 $player = mysql_fetch_array($result, MYSQL_ASSOC);
 echo "<h2>{$player['player_name']}";
 if ($player['player_type'] != 'Normal') { echo " ({$player['player_type']})"; }
 echo "</h2>\n";
 echo "<p><b>Score:</b> {$player['player_score']} | <b>Wins:</b> {$player['player_wins']} | <b>Losses:</b> {$player['player_losses']}</p>\n";
 echo "<h3>Games</h3>";
 
 mysql_free_result($result);
 
 $query = "SELECT mafia_games.game_id, game_name, game_phase FROM mafia_games, mafia_roles WHERE player_id={$_SESSION['player_id']} AND mafia_games.game_id=mafia_roles.game_id ORDER BY game_id DESC";
 
 include('includes/common.inc');
 
 show_games($query);
 if ($numrows == 0) {
  echo "<p>{$player['player_name']} is not currently playing in any games of mafia</p>";
 }

 echo "<h3>Setups</h3>";
 
 $query = "SELECT setup_id, setup_name, setup_description, setup_players FROM mafia_setups WHERE setup_creator=$pid ORDER BY setup_used DESC";

 show_setups($query,false);
 
 if ($numrows == 0) {
  echo "<p>{$player['player_name']} has not created any setups</p>";
 }
 
} else {
 echo '<p>Database error or invalid player id, please try again</p>';
}
?>
