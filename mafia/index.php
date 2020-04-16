<?php
include ('includes/header.inc');
require_once ('../../db/mysql_connect_mafia.php');

echo "<div class=\"right\">\n<h2 class=\"center\"><a href=\"stats.php\">Top Players</a> (score)</h2>\n<ol>";

$query = "SELECT player_id, player_name, player_score FROM mafia_players ORDER BY player_score DESC LIMIT 0, 12";
$result = mysql_query($query);

while ($rows = mysql_fetch_array($result, MYSQL_NUM)) {
 echo "\n<li><a href=\"profile.php?p={$rows[0]}\">{$rows[1]}</a> ({$rows[2]})</li>";
}
?>
</ol>
<p class="center"><a href="stats.php">See full statistics</a></p>
</div>
<h1 class="center"><i>If you are already familiar with the game of Mafia, you can <nobr><a href="games.php">start playing now</a>!</i></nobr></h1>
<h2>About</h2>
<p>This site is powered by <b>AutoMafia</b>, a web-based version of Mafia (also known as Werewolf or Vampire). See the <a href="help.php">help page</a> for more information on the game of Mafia and how to play it.</p>

<h3>Features</h3>
<p>The aim of AutoMafia is to perform the job of the moderator, while allowing for as much gameplay variety as possible:</p>
<ul><li><a href="charlist.php">Over 30 different character types</a> to choose from</li>
<li>Complete freedom for players to <a href="newsetup.php">create a virtually infinite variety of game setups</a> (including closed and random setups)</li>
<li>Potential for huge games with hundreds of players and numerous factions competing for victory (which would be very difficult for a human moderater to run)</li>
<li><a href="stats.php">Game statistics</a> with leaderboards for the most successful players and the most popular setups</li>
</ul>
<p>Planned features for future versions:</p>
<ul><li>A <b>narrator</b> role, to allow a player to sit out from the game and provide a story, while AutoMafia carries out the other moderator tasks</li>
<li>More characters (potential additions include; Mafia Janitor, Mafia Hitman, Bulletproof Villager, Bodyguard, and Jester)</li>
<li>Player avatars</li>
</ul>
<h3>Source</h3>
<p>The source code for AutoMafia is licenced under a 
<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Creative Commons Licence</a> and will be available as soon as version 1.0 comes out of beta. Note that this licence only applies to the AutoMafia source, and not to the design and layout of The Incredible Automafiaton website nor to any other work not included in the release package.</p>
<h3>Credits</h3>
<p>AutoMafia was written by me, <b>fool_on_the_hill</b>. With thanks to...</p>
<ul><li><a href="http://www.mafiascum.net/">mafiascum.net</a> for the extensive information available on their forums and wiki
<li>mafiascum.net user <a href="http://www.mafiascum.net/wiki/index.php?title=Mikeburnfire">mikeburnfire</a> for his brilliant flash guides, which I referred to heavily</li>
<li>Dan for introducing the game to the <a href="http://www.warwickfoos.co.uk/">warwickfoos</a> forums and getting us all thoroughly addicted</li>
<li>my old housemate Chris for suggesting the idea that became AutoMafia</li>
</ul>
<?php include ('includes/footer.inc'); ?>
