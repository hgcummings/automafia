<?php
$page_title = '- Help';
include ('includes/header.inc');
?>

<h1>Help</h1>
<p>If you need help and can't find what you're looking for below, or you have a bug report or feature request, please <a href="contact.php">contact the site administrator</a>.
<h2>Common Questions</h2>
<ul>
<li><a href="#q1">What is Mafia?</a></li>
<li><a href="#q2">What is AutoMafia?</a></li>
<li><a href="#q3">How do I play?</a></li>
<li><a href="#q4">How are players and setups ranked?</a></li>
</ul>
<h2>Extended Articles</h2>
<ul><li><a href="charlist.php">Character List</a></li>
<li><a href="tutorial.php">Setup Creation Tutorial</a></li>
</ul>
<h3><a name="q1"></a>What is Mafia?</h3>
<p>Mafia is a game based around the idea of an 'informed minority' (the mafia) and an 'uninformed majority' (the townies or villagers).  The game alternates between night and day phases. Each day, the players can all talk to each other and vote for somebody to lynch. The aim of the villagers is to lynch the mafia, but the villagers don't know who the mafia are. When somebody is lynched, it is revealed to all the players side that person was on, then the night phase begins. During the night, the mafia can talk to each other and choose another player to kill before the next day begins.</p>
<p>The villagers win if they eliminate all of the mafia. The mafia win if they eliminate all of the villagers (actually if they achieve a majority, since a win would then be inevitable). Even players who were killed but are on the winning team are still considered to have won.</p>
<p>To make the game more interesting, there are certain special roles that can be given to individual players. One of the most common roles is a cop. Each night, the cop can 'investigate' one player and will be told if that player is mafia or not. He can then share this information with the other players the next day, although doing so will of course make him a target for the mafia the following night. There are many different special characters for both villagers and mafia (and indeed some special third-party roles) and they are not all beneficial.</p>
<h3><a name="q2"></a>What is AutoMafia?</a></h3>
<p>A game of mafia requires a moderator; to co-ordinate the game, assign characters to players, facilitate kills and investigations during the night, reveal the identity of murdered players and so on. AutoMafia is a web application that acts as a moderator and allows people to create, join and play games. Currently, AutoMafia supports <a href="charlist.php">over thiry different character types</a>.</p>
<h3><a name="q3"></a>How do I play?</a></h3>
<p>Once you have <a href="register.php">registered a username</a> you can start playing right away by going to the <a href="games.php">games screen</a>. If you see a game that is gathering players you can join it. Once it has enough players to start, you will be assigned a role and the game will begin. During the day phase you will be able to post messages for all other players to see and you can vote for which player to lynch. At night you will only be able to to talk if you are a member of the mafia or another special group, and only your fellow group members will be able to see your messages. If you are a member of the mafia or have another special role you may also be able to submit another name at night (e.g. for mafia to vote for who to kill, or for a cop to choose who to investigate).</p>
<p>If there are no games gathering players then you can <a href="newgame.php">start your own game</a>. This is very easy since there are a number of pre-determined setups (character combinations) for you to use. If you prefer though, you can <a href="newsetup.php">create your own setup</a> using the characters that AutoMafia supports. For more information see the <a href="tutorial.php">setup creation tutorial</a> and the <a href="charlist.php">character list</a>. 
<h3><a name="q4"></a>How are players and setups ranked?</a></h3>
<p>Players are ranked by score. Each time they win a game, their score is increased by the number of players in that game. This is a simple measure of how much time and effort is required for a win. Of course, this might not be a good measure if the setup used for the game was unbalanced or otherwise unfair. This is why we also rank setups by how often they are used, to allow people to choose popular setups and give players an incentive to create good setups. A setup's use count is only incremented when it is actually played (that is, enough players join to allow the game to start). 
</p>
<?php include ('includes/footer.inc'); ?>