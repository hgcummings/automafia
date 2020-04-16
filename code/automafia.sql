CREATE TABLE mafia_players (
player_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
player_name VARCHAR(20) NOT NULL,
player_email CHAR(64) NULL,
player_password CHAR(40) NOT NULL,
player_wins SMALLINT UNSIGNED NOT NULL DEFAULT 0,
player_losses SMALLINT UNSIGNED NOT NULL DEFAULT 0,
player_score SMALLINT NOT NULL DEFAULT 0,
player_type ENUM ('Normal', 'Moderator', 'Admin') NOT NULL DEFAULT 'Normal',
player_avatar ENUM ('no','yes') NOT NULL DEFAULT 'no',
player_timezone TINYINT NOT NULL DEFAULT 0,
player_notifications ENUM ('On','Off') NOT NULL DEFAULT 'On',
player_style ENUM ('dark','light') NOT NULL DEFAULT 'dark',
PRIMARY KEY (player_id)
) ENGINE = MyISAM;

ALTER TABLE mafia_players AUTO_INCREMENT = 100;

CREATE TABLE mafia_games (
game_id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
game_phase TINYINT NOT NULL DEFAULT 0,
game_name TINYTEXT NOT NULL,
setup_id SMALLINT UNSIGNED NOT NULL,
game_start TINYINT NOT NULL DEFAULT 1,
game_narrator SMALLINT UNSIGNED NOT NULL DEFAULT 0,
PRIMARY KEY (game_id)
) ENGINE = MyISAM;

ALTER TABLE mafia_games AUTO_INCREMENT = 1;

CREATE TABLE mafia_characters (
character_id SMALLINT UNSIGNED NOT NULL,
character_name VARCHAR(32) NOT NULL,
character_description TEXT NOT NULL,
character_message TEXT NOT NULL,
character_action ENUM ('none', 'investigate', 'protect', 'kill', 'block', 'recruit') NOT NULL,
character_defwingroup TINYINT NOT NULL,
character_guilt ENUM('is not', 'is') NOT NULL,
PRIMARY KEY (character_id)
) ENGINE = MyISAM;

CREATE TABLE mafia_messages (
message_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
message_from SMALLINT UNSIGNED NOT NULL,
message_to SMALLINT UNSIGNED NOT NULL,
message_body TEXT NOT NULL,
game_id SMALLINT UNSIGNED NOT NULL,
message_timestamp DATETIME NOT NULL,
game_phase TINYINT NOT NULL,
PRIMARY KEY (message_id)
) ENGINE = MyISAM;
 
CREATE TABLE mafia_roles (
game_id MEDIUMINT UNSIGNED NOT NULL,
player_id SMALLINT UNSIGNED NOT NULL,
role_status ENUM ('Dead', 'Alive', 'Narrator') NOT NULL DEFAULT 'Alive',
character_id SMALLINT UNSIGNED NULL,
role_chatgroup TINYINT NULL DEFAULT 0,
role_wingroup TINYINT NULL,
role_replace SMALLINT UNSIGNED NOT NULL DEFAULT 0,
UNIQUE (game_id, player_id)
) ENGINE = MyISAM;

CREATE TABLE mafia_votes (
game_id MEDIUMINT UNSIGNED NOT NULL,
game_phase TINYINT NOT NULL,
vote_by SMALLINT UNSIGNED NOT NULL,
vote_for SMALLINT UNSIGNED NOT NULL,
UNIQUE (game_id, game_phase, vote_by, vote_for)
) ENGINE = MyISAM;
 
CREATE TABLE mafia_actions (
game_id MEDIUMINT UNSIGNED NOT NULL,
action_by SMALLINT UNSIGNED NOT NULL,
action_target SMALLINT UNSIGNED NOT NULL,
action_type ENUM ('none', 'investigate', 'protect', 'kill', 'block', 'recruit', 'groupkill') NOT NULL,
UNIQUE (game_id, action_by, action_target, action_type) 
) ENGINE = MyISAM;
 
CREATE TABLE mafia_setups (
setup_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
setup_characters TEXT NOT NULL,
setup_name TINYTEXT NOT NULL,
setup_description TEXT NOT NULL,
setup_creator SMALLINT UNSIGNED NOT NULL DEFAULT 0,
setup_players TINYINT UNSIGNED NOT NULL,
setup_allknown ENUM ('yes', 'no') NOT NULL,
setup_start ENUM ('day','night','either'),
setup_used MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
PRIMARY KEY (setup_id)
) ENGINE = MyISAM;

INSERT INTO mafia_characters (character_id, character_name, character_description, character_message, character_action, character_defwingroup, character_guilt) VALUES
('0', 'Villager', 'The villager is on the side of the town. They have no special abilities.', 'You are a <b>villager</b>. You are on the side of the <b>town</b>.', 'none', 0, 'is not'),
('3', 'Mafioso', 'The mafioso is an ordinary mafia member. They have no special abilities.', 'You are a <b>mafioso</b>.', 'none', 4, 'is'),
('6', 'Cop', 'The cop is on the side of the town. Each night they can choose a player to investigate and will be told whether or not that player is mafia.', 'You are a <b>cop</b>. You are on the side of the <b>town</b>. Each night you may choose one player to investigate and you will be told whether or not they are mafia.', 'investigate', 0, 'is not'),
('9', 'Doctor', 'The doctor is on the side of the town. Each night they may choose one player to protect. If that player is targeted for a night kill the doctor will save them.', 'You are a <b>doctor</b>. You are on the side of the <b>town</b>. Each night you may choose one player to protect. If they are targeted for a night kill they will survive.', 'protect', 0, 'is not'),
('12', 'Godfather', 'The Godfather is on the side of the mafia and the head of a mafia family. If investigated by a cop they will appear innocent.', 'You are the <b>Godfather</b>. You are the head of a <b>mafia</b> family. If investigated by a cop you will appear innocent.', 'none', 4, 'is not'),
('15', 'Serial Killer', 'The serial killer has no allegiance to either town or mafia. They can win only by being the last player to survive. Each night they can kill another player. A cop will not detect them.', 'You are a <b>serial killer</b>. You have <b>no allegiance</b> to either the town or the mafia. You can only win by being the last man standing. If investigated by a cop you will appear to be a villager. You have the ability to kill somebody each night.', 'kill', 9, 'is not'),
('18', 'Vigilante', 'The vigilante is on the side of the town. Each night they can choose a player to kill.', 'You are a <b>vigilante</b>. You are on the side of the <b>town</b>. Each night you can choose somebody to kill.', 'kill', 0, 'is not'),
('21', 'One-shot Vigilante', 'The one-shot vigilante is on the side of the town. On any night they may choose somebody to kill. Once the vigilante has successfully used this ability, they will become an ordinary villager.', 'You are a <b>one-shot vigilante</b>. You are on the side of the <b>town</b>. On any night you can choose somebody to kill. Once you have successfully used this ability, you will become an ordinary villager.', 'kill', 0, 'is not'),
('24', 'Hooker with a Heart of Gold', 'The hooker with a heart of gold is on the side of the town. Each night she may choose one player to seduce. Any action that player was due to carry out that night will be blocked. A mafia kill can be blocked by correctly choosing the mafia member who will actually carry out the attack.', 'You are a <b>hooker with a heart of gold</b>. You are on the side of the <b>town</b>. Each night you may choose one player to seduce. Any action they carry out that night will be blocked. A mafia kill can be blocked by correctly choosing the mafia member who will actually carry out the attack.', 'block', 0, 'is not'),
('27', 'Prostitute', 'The prostitute is on the side of the mafia. Each night she may choose one player to seduce. Any action that player was due to carry out that night will be blocked.', 'You are a <b>prostitute</b>. You are on the side of the <b>mafia</b>. Each night you may choose one player to seduce. Any action they carry out that night will be blocked.', 'block', 4, 'is'),
('30', 'Miller', 'The miller is on the side of the town. However, if investigated by a cop they will appear guilty. The player is told at the start of the game that they are a miller.', 'You are a <b>miller</b>. You are on the side of the <b>town</b>. You are however a dodgy character, and if investigated by the cop you will appear guilty.', 'none', 0, 'is'),
('33', 'Naive Miller', 'The naive miller is on the side of the town. However, if investigated by a cop they will appear guilty. The player is not told at the start of the game that they are a miller.', 'You are a <b>villager</b>. You are on the side of the <b>town</b>.', 'None', 0, 'is'),
('36', 'Naive Doctor', 'The naive doctor is on the side of the town. Each night they choose a player to protect. However, they have no actual protective abilities. They are told that they are an ordinary doctor.', 'You are a <b>doctor</b>. You are on the side of the <b>town</b>. Each night you may choose one player to protect. If they are targeted for a night kill they will survive.', 'protect', 0, 'is not'),
('39', 'Faith Healer', 'The faith healer is on the side of the town. Each night they may choose one player to protect. If that player is targeted for a night kill the faith healer has a 50% chance of saving them.', 'You are a <b>faith healer</b>. You are on the side of the <b>town</b>. Each night you may choose one player to protect. If they are targeted for a night kill you have a 50% chance of saving them.', 'protect', 0, 'is not'),
('42', 'Naive Cop', 'The naive cop is on the side of the town. They are simply told that they are a normal cop. Each night they can choose a player to investigate and will be told whether or not that player is mafia. However, they are naive and always get an innocent result.', 'You are a <b>cop</b>. You are on the side of the <b>town</b>. Each night you may choose one player to investigate and you will be told whether or not they are mafia.', 'investigate', 0, 'is not'),
('45', 'Paranoid Cop', 'The insane cop is on the side of the town. They are simply told that they are a normal cop. Each night they can choose a player to investigate and will be told whether or not that player is mafia. However, they are paranoid and always get a guilty result.', 'You are a <b>cop</b>. You are on the side of the <b>town</b>. Each night you may choose one player to investigate and you will be told whether or not they are mafia.', 'investigate', 0, 'is not'),
('48', 'Insane Cop', 'The insane cop is on the side of the town. They are simply told that they are a normal cop. Each night they can choose a player to investigate and will be told whether or not that player is mafia. However, they are insane and get the opposite result to a normal cop. This ability can therefore be useful once the player has figured out that they are insane.', 'You are a <b>cop</b>. You are on the side of the <b>town</b>. Each night you may choose one player to investigate and you will be told whether or not they are mafia.', 'investigate', 0, 'is not'),
('51', 'Tracker', 'The tracker is on the side of the town. Each night they can choose a player to track and will learn who, if anyone, that player targeted (but not what they were targeted for).', 'You are a <b>tracker</b>. You are on the side of the <b>town</b>. Each night you can choose a player to track and will learn who, if anyone, that player targeted (but not what they were targeted for).', 'investigate', 0, 'is not'),
('54', 'Watcher', 'The watcher is on the side of the town. Each night they can choose a player to watch and will learn who, if anyone, targeted that player (but not what they were targeted for).', 'You are a <b>watcher</b>. You are on the side of the <b>town</b>. Each night you can choose a player to watch and will learn who, if anyone, targeted that player (but not what they were targeted for).', 'investigate', 0, 'is not'),
('57', 'Mafia Cop', 'The mafia cop is a powerful investigative character on the side of the mafia. Each night they can choose a person to investigate and will be told their role.', 'You are a <b>cop</b>. You are on the side of the <b>mafia</b>. Each night you may choose one player to investigate and you will be told their role', 'investigate', 4, 'is'),
('60', 'Traitor', 'The traitor is on the side of the mafia, and knows who the mafia are, but cannot communicate with them. The mafia do not know who the traitor is. The traitor will appear innocent to cops.', 'You are a <b>traitor</b>, you are a villager on the side of the <b>mafia</b>. This means that you cannot talk to the mafia and will appear innocent to cops, but you will only win in the case of a mafia victory.', 'none', 4, 'is not'),
('63', 'Mafia Doctor', 'The mafia doctor can be useful in games with multiple mafia families, or other killing roles. Each night they may choose one player to protect. If that player is targeted for a night kill the doctor will save them.', 'You are a <b>doctor</b>. You are on the side of the <b>mafia</b>. Each night you may choose one player to protect. If they are targeted for a night kill they will survive.', 'protect', 4, 'is'),
('66', 'Weak Doctor', 'The doctor is on the side of the town. Each night they may choose one player to protect. If that player is targeted for a night kill the doctor will save them. However, if the weak doctor accidentally targets a mafia member or other antagonistic character they will die.', 'You are a <b>weak doctor</b>. You are on the side of the <b>town</b>. Each night you may choose one player to protect. If they are targeted for a night kill they will survive. However, if you accidentally target a mafia member or other antagonistic character you will die.', 'protect', 0, 'is not'),
('69', 'Lodge Master', 'The master is the head of a masonic lodge. Each night they can recruit another player to the lodge. However, if they attempt to recruit a mafia member or other antagonistic character they will die.', 'You are a <b>lodge master</b>. You are on the side of the <b>town</b> and the head of a masonic lodge. Each night you can recruit another player to the lodge. However, if you attempt to recruit a mafia member or other antagonistic character you will die.', 'recruit', 0, 'is not'),
('72', 'Cult Leader', 'The cult leader will win when everybody in the town has been converted to their cult. Each night they can recruit another player to the cult. However, if they attempt to recruit a mafia member or other antagonistic character they will die.', 'You are a <b>cult leader</b>. You will win when everybody in the town has been converted to your cult. Each night you can recruit another player to the cult. However, if you attempt to recruit a mafia member or other antagonistic character you will die.', 'recruit', 1, 'is not'),
('75', 'Cannibal', 'The cannibal has no allegiance to either town or mafia. They can win only by being the last player to survive. Each night they can kill another player. A cop will not detect them, but a federal investigator can.', 'You are a <b>cannibal</b>. You have <b>no allegiance</b> to either the town or the mafia. You can only win by being the last man standing. You have the ability to kill somebody each night. If investigated by an ordinary cop you will appear to be a villager. However, you can be investigated by a <b>federal agent</b>.', 'kill', 10, 'is not'),
('78', 'Federal Agent', 'The federal agent is on the side of the town. Each night they may choose one player to investigate and will be told whether or not that player is a cannibal. If the cannibal get killed, the federal agent will become a normal cop.', 'You are a <b>federal agent</b>. You are on the side of the <b>town</b>. Each night you may choose one player to investigate and you will be told whether or not they are a <b>cannibal</b>. If the cannibal get killed you will become a normal cop.', 'investigate', 0, 'is not'),
('81', 'Psychopath', 'The psychopath has no allegiance to either town or mafia. They can win only by being the last player to survive. Each night they can kill another player. A cop will not detect them. However, a psychiatrist can heal them, turning them into a normal villager.', 'You are a <b>psychopath</b>. You have <b>no allegiance</b> to either the town or the mafia. You can only win by being the last man standing. If investigated by an ordinary cop you will appear to be a villager. You have the ability to kill somebody each night. However, you can be healed (turned into a normal villager) by a <b>psychiatrist</b>.', 'kill', 11, 'is not'),
('84', 'Psychiatrist', 'The psychiatrist is on the side of the town. Each night they may target one player and if that player is a psychopath the psychiatrist will heal them (turning them into a normal villager). If the psychopath is healed or killed, the psychiatrist will become an ordinary doctor.', 'You are a <b>psychiatrist</b>. You are on the side of the <b>town</b>. Each night you may target one player and if they are the <b>psycopath</b> you will heal them (turning them into a normal villager). If the psychopath is healed or killed, you will become a <b>doctor</b>.', 'investigate', 0, 'is not'),
('87', 'Pyromaniac', 'The pyromaniac has no allegiance to either town or mafia. They can win only by being the last player to survive. Each night they can kill another player. A cop will not detect them. However, their victims can be protected by a firefighter.', 'You are a <b>pyromaniac</b>. You have <b>no allegiance</b> to either the town or the mafia. You can only win by being the last man standing. If investigated by an ordinary cop you will appear to be a villager. You have the ability to kill somebody each night. However, your targets can be saved by a <b>firefighter</b>.', 'kill', 12, 'is not'),
('90', 'Firefighter', 'The firefighter is on the side of the town. Each night they may choose one player to protect, and if that player is targeted by the pyromaniac they will survive. If the pyromaniac is killed, the firefighter becomes a normal townie.', 'You are a <b>firefighter</b>. You are on the side of the <b>town</b>. Each night you may choose one player to protect, and if they are targeted by <b>pyromaniac</b> they will survive.  If the pyromaniac is killed you will become a normal townie.', 'protect', 0, 'is not');