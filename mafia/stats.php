<?php
$page_title = '- Statistics';
include ('includes/header.inc');
require_once ('../../db/mysql_connect_mafia.php');

echo "<h1>Statistics</h1>\n";

echo "<p>The statistics are not fully featured yet. There will be more to see when there is more data in the system.";

echo "<h2>Game Statistics</h2>\n";

$query = "SELECT COUNT(*) FROM mafia_games";
$result = mysql_query($query);
$games[] = mysql_fetch_array($result, MYSQL_NUM);

$query = "SELECT COUNT(*) FROM mafia_games WHERE game_phase=0";
$result = mysql_query($query);
$games[] = mysql_fetch_array($result, MYSQL_NUM);

$query = "SELECT COUNT(*) FROM mafia_games WHERE game_phase!=0 AND game_phase!=-128";
$result = mysql_query($query);
$games[] = mysql_fetch_array($result, MYSQL_NUM);

$query = "SELECT COUNT(*) FROM mafia_games WHERE game_phase=-128";
$result = mysql_query($query);
$games[] = mysql_fetch_array($result, MYSQL_NUM);

echo "<h3>Total games: {$games[0][0]}</h3>\n<p><b>Gathering players:</b> {$games[1][0]}<br><b>In progress:</b> {$games[2][0]}<br><b>Finished:</b> {$games[3][0]}</p>";

echo "<h2>Player Statistics</h2>\n";

$query = "SELECT player_id, player_name, player_wins, player_losses, player_score FROM mafia_players ORDER BY player_score DESC LIMIT 0, 12";
$result = mysql_query($query);

echo '<table class="bordered"><tr><th>Player</th><th>Wins</th><th>Losses</th><th>Percentage</th><th>Score</th></tr>';

while ($player = mysql_fetch_row($result)) {

 if (($player[2]+$player[3]) == 0) {
  $percent = 0;
 } else {
  $percent = round(($player[2]/($player[2]+$player[3])),2);
 }
 
 echo "\n<tr><td><a href=\"profile.php\?p={$player[0]}\">{$player[1]}</td><td>{$player[2]}</td><td>{$player[3]}</td><td>$percent%</td><td>{$player[4]}</td>";
} 

echo "\n</table>";

include ('includes/footer.inc'); ?>