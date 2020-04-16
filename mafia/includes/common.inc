<?php

$ten = 10; #Nothing is certain

function show_setups($query,$creator) {
  global $numrows;
  $result = mysql_query($query);
  $numrows = mysql_num_rows($result);
  if ($numrows != 0) {
   echo "\n\n<table class=\"bordered\">\n <tr><th width=\"20%\">Setup Name (Players)</th><th width=\"80%\">Description</th></tr>\n";
   while ($rows = mysql_fetch_array($result, MYSQL_NUM)) {
    echo "  <tr><td>{$rows[1]} ({$rows[3]})";
    if ($creator) { echo "<br> by <a href=\"profile?p={$rows[4]}\">{$rows[5]}</a>"; }
    echo "<br /><br /><a href=\"newgame.php?setup={$rows[0]}\">Use setup</a> | <a href=\"delete.php?setup={$rows[0]}&redirect=".basename($_SERVER['PHP_SELF'])."\">Delete</a></td><td class=\"smaller\">".$rows[2]."</td></tr>\n";
   }
   echo "</table>\n\n";
  }
  mysql_free_result($result);
}
    
function game_phase($phase) {
 if ($phase == 0) {
  return 'Gathering players';
 } else if ($phase == -128) {
  return 'Finished';
 } else if ($phase > 0) {
  return 'Day '.$phase;
 } else {
  return 'Night '.abs($phase);
 }
}
    
function show_games($query) {
  global $numrows;
  $result = mysql_query($query);
  $numrows = mysql_num_rows($result);
  if ($numrows != 0) {
   echo "\n\n<table class=\"bordered\">\n <tr><th width=\"80%\">Game Name</th><th width=\"20%\">Status</th></tr>\n";
   while ($rows = mysql_fetch_array($result, MYSQL_NUM)) {
    echo "  <tr><td><i>Game #{$rows[0]}</i> - {$rows[1]}</td><td class=\"center\"><a href=\"game.php?id={$rows[0]}\">".game_phase($rows[2])."</a></td></tr>\n";
   }
   echo "</table>\n\n";
  }
  mysql_free_result($result);
}

?>
