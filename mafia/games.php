<?php include ('includes/header.inc');
require_once ('../../db/mysql_connect_mafia.php');
include ('includes/common.inc');

echo '<h1>Mafia Games</h1>';

if ((isset($_SESSION['player_id'])) && (is_numeric($_SESSION['player_id']))) {
 echo '<h2>Your Current Games</h2>';
 $query = "SELECT mafia_games.game_id, game_name, game_phase FROM mafia_games, mafia_roles WHERE player_id={$_SESSION['player_id']} AND game_phase!=-128 AND mafia_games.game_id=mafia_roles.game_id ORDER BY game_id ASC";
 show_games($query);
 if ($numrows == 0) {
  echo '<p>You are not currently playing in any games of mafia</p>';
 }
}

echo '<h2>Browse Games</h2>';
echo '<p>You can join one of the new games below, or <a href="newgame.php">create your own game</a>';

$query = "SELECT game_id, game_name, game_phase FROM mafia_games"; 

if ((isset($_GET['start'])) && (is_numeric($_GET['start']))) {
 $start = $_GET['start'];
} else {
 $start = 0;
}

if ((isset($_GET['new'])) && ($_GET['new'] == 1)) {
 $new = 1;
 echo "<p><b>Showing only games that are gathering players</b> - <a href=\"games.php?start=$start&new=0\">View all games</a></p>";
 $query .= " WHERE game_phase=0 ORDER BY game_id ASC";
} else {
 $new = 0;
 echo "<p><b>Showing all games</b> - <a href=\"games.php?start=$start&new=1\">View only games that are gathering players</a></p>";
 $query .= " ORDER BY game_id DESC";
} 

$query .= " LIMIT $start, $ten";

if ($start > 0) {
 $nav = '<div class="sepia center"><a href="newgame.php?start='.($start-$ten)."&new=$new\">Previous page</a></div>"; 
}
    
show_games($query);
    
if ($numrows == $ten) {
 $nav = '<div class="sepia center"><a href="newgame.php?start='.($start+$ten)."&new=$new\">Next page</a></div>"; 
}


include ('includes/footer.inc');
?>