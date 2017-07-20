<?php
require './config/bootstrap.php';

head('<link href="./assets/css/infobulle.css" rel="stylesheet" type="text/css" /><script src="./assets/js/jquery-1.7.2.min.js"></script><script src="./assets/js/infobulle.js"></script>');


$maps = glob('./assets/img/map/*.png');
rsort($maps);

$map = $maps[0];
$planets = $db->query('SELECT planet.name AS planet_name, planet.team AS civilization, game.name AS game_name, game.id AS game_id FROM planet LEFT JOIN game ON game.id = planet.id_game')->fetchAll();
$teams = $db->query('SELECT name_team_first, name_team_second FROM config')->fetch();
$civ = array('libre', $teams[0], $teams[1]);

$games = $db->query('SELECT * FROM game')->fetchAll();
$plays = $db->query('SELECT id, id_game, id_team FROM play LEFT JOIN member ON member.id = play.id_member')->fetchAll();

$players = array();
foreach ($games AS $game)
	$players[$game['id']] = array(0, 0);
foreach ($plays AS $p)
	$players[$p['id_game']][$p['id_team'] - 1]++;

?>
</div>
<div id="galaxy">
	<img src="<?php echo $map; ?>" style="width:100%" usemap="#galaxy" style="z-index:10" />
	<map name="galaxy">
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="427"  y="529" r="125"   name="<?php echo utf8_encode(htmlentities($planets[0]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[0]['game_name'])); ?>" civid="<?php echo intval($planets[0]['civilization']);?>" habitant="<?php echo $civ[intval($planets[0]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[0]['game_id']][0]; ?>" 	nb2="<?php echo $players[$planets[0]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="2073" y="543" r="125"   name="<?php echo utf8_encode(htmlentities($planets[1]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[1]['game_name'])); ?>" civid="<?php echo intval($planets[1]['civilization']);?>" habitant="<?php echo $civ[intval($planets[1]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[1]['game_id']][0]; ?>" 	nb2="<?php echo $players[$planets[1]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="708"  y="760" r="45"    name="<?php echo utf8_encode(htmlentities($planets[2]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[2]['game_name'])); ?>" civid="<?php echo intval($planets[2]['civilization']);?>" habitant="<?php echo $civ[intval($planets[2]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[2]['game_id']][0]; ?>" 	nb2="<?php echo $players[$planets[2]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="742"  y="415" r="74"    name="<?php echo utf8_encode(htmlentities($planets[3]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[3]['game_name'])); ?>" civid="<?php echo intval($planets[3]['civilization']);?>" habitant="<?php echo $civ[intval($planets[3]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[3]['game_id']][0]; ?>" 	nb2="<?php echo $players[$planets[3]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="935"  y="271" r="55"    name="<?php echo utf8_encode(htmlentities($planets[4]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[4]['game_name'])); ?>" civid="<?php echo intval($planets[4]['civilization']);?>" habitant="<?php echo $civ[intval($planets[4]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[4]['game_id']][0]; ?>" 	nb2="<?php echo $players[$planets[4]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="1217" y="204" r="35"    name="<?php echo utf8_encode(htmlentities($planets[5]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[5]['game_name'])); ?>" civid="<?php echo intval($planets[5]['civilization']);?>" habitant="<?php echo $civ[intval($planets[5]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[5]['game_id']][0]; ?>" 	nb2="<?php echo $players[$planets[5]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="1539" y="261" r="80"    name="<?php echo utf8_encode(htmlentities($planets[6]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[6]['game_name'])); ?>" civid="<?php echo intval($planets[6]['civilization']);?>" habitant="<?php echo $civ[intval($planets[6]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[6]['game_id']][0]; ?>" 	nb2="<?php echo $players[$planets[6]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="1772" y="450" r="72"    name="<?php echo utf8_encode(htmlentities($planets[7]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[7]['game_name'])); ?>" civid="<?php echo intval($planets[7]['civilization']);?>" habitant="<?php echo $civ[intval($planets[7]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[7]['game_id']][0]; ?>" 	nb2="<?php echo $players[$planets[7]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="1635" y="923" r="90"    name="<?php echo utf8_encode(htmlentities($planets[8]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[8]['game_name'])); ?>" civid="<?php echo intval($planets[8]['civilization']);?>" habitant="<?php echo $civ[intval($planets[8]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[8]['game_id']][0]; ?>" 	nb2="<?php echo $players[$planets[8]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="886"  y="937" r="85"    name="<?php echo utf8_encode(htmlentities($planets[9]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[9]['game_name'])); ?>" civid="<?php echo intval($planets[9]['civilization']);?>" habitant="<?php echo $civ[intval($planets[9]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[9]['game_id']][0]; ?>" 	nb2="<?php echo $players[$planets[9]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="868"  y="575" r="40"    name="<?php echo utf8_encode(htmlentities($planets[10]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[10]['game_name'])); ?>" civid="<?php echo intval($planets[10]['civilization']);?>" habitant="<?php echo $civ[intval($planets[10]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[10]['game_id']][0]; ?>"	nb2="<?php echo $players[$planets[10]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="1120" y="310" r="50"    name="<?php echo utf8_encode(htmlentities($planets[11]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[11]['game_name'])); ?>" civid="<?php echo intval($planets[11]['civilization']);?>" habitant="<?php echo $civ[intval($planets[11]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[11]['game_id']][0]; ?>"	nb2="<?php echo $players[$planets[11]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="1585" y="683" r="35"    name="<?php echo utf8_encode(htmlentities($planets[12]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[12]['game_name'])); ?>" civid="<?php echo intval($planets[12]['civilization']);?>" habitant="<?php echo $civ[intval($planets[12]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[12]['game_id']][0]; ?>"	nb2="<?php echo $players[$planets[12]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="1244" y="839" r="80"    name="<?php echo utf8_encode(htmlentities($planets[13]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[13]['game_name'])); ?>" civid="<?php echo intval($planets[13]['civilization']);?>" habitant="<?php echo $civ[intval($planets[13]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[13]['game_id']][0]; ?>"	nb2="<?php echo $players[$planets[13]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="1056" y="664" r="35"    name="<?php echo utf8_encode(htmlentities($planets[14]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[14]['game_name'])); ?>" civid="<?php echo intval($planets[14]['civilization']);?>" habitant="<?php echo $civ[intval($planets[14]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[14]['game_id']][0]; ?>"	nb2="<?php echo $players[$planets[14]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="1479" y="484" r="20"    name="<?php echo utf8_encode(htmlentities($planets[15]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[15]['game_name'])); ?>" civid="<?php echo intval($planets[15]['civilization']);?>" habitant="<?php echo $civ[intval($planets[15]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[15]['game_id']][0]; ?>"	nb2="<?php echo $players[$planets[15]['game_id']][1]; ?>" />
		<area class="infobulle" shape="circle" coords="1,1,20" href="#" alt="" style="cursor:help;" x="1246" y="534" r="125"   name="<?php echo utf8_encode(htmlentities($planets[16]['planet_name'])); ?>"  		game="<?php echo utf8_encode(htmlentities($planets[16]['game_name'])); ?>" civid="<?php echo intval($planets[16]['civilization']);?>" habitant="<?php echo $civ[intval($planets[16]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[16]['game_id']][0]; ?>"	nb2="<?php echo $players[$planets[16]['game_id']][1]; ?>" />
		<area class="infobulle" shape="rect" coords="1,1,2,2" href="#" alt="" style="cursor:help;" x="1714" y="774" r="1872" s="551"  name="<?php echo utf8_encode(htmlentities($planets[17]['planet_name'])); ?>"	game="<?php echo utf8_encode(htmlentities($planets[17]['game_name'])); ?>" civid="<?php echo intval($planets[17]['civilization']);?>" habitant="<?php echo $civ[intval($planets[17]['civilization'])]; ?>" 	nb1="<?php echo $players[$planets[17]['game_id']][0]; ?>"	nb2="<?php echo $players[$planets[17]['game_id']][1]; ?>" />
	</map>
</div>

<script>
	function resize_galaxy() {
		var src_x = 2500;
		var src_y = 1406;

		var win_x = $('#galaxy').width();
		$('area').each(function(index) {
			var x = $(this).attr('x');
			var y = $(this).attr('y');
			var r = $(this).attr('r');

			var p = $('#galaxy').width() / src_x;


			var s = $(this).attr('s');
			if (typeof s === "undefined") {
				$(this).attr('coords', (x*p)+','+(y*p)+','+(r*p));
			}
			else {
				$(this).attr('coords', (x*p)+','+(y*p)+','+(r*p)+','+(s*p));
			}

		});
	}

	$(document).ready(function(){
		$(window).resize(function() {
			resize_galaxy();
		});
		resize_galaxy();

		var color = new Array('green', 'red', '#00A9FF');

		$('.infobulle').mouseover(function() {
			$.cursorMessage('<strong>Nom:</strong> '+$(this).attr('name')+'<br/><strong>Jeu: </strong>'+$(this).attr('game')+'<br/><strong>Habitant:</strong> <span style="color:'+color[$(this).attr('civid')]+'">'+$(this).attr('habitant')+'</span><br/><strong>Estimation du nombre de joueurs:</strong> <span style="color:'+color[1]+';">'+$(this).attr('nb1')+'</span> vs <span style="color:'+color[2]+'">'+$(this).attr('nb2')+'</span>', {hideTimeout:0})
		});
		$('.infobulle').mouseout($.hideCursorMessage);

	});
</script>
</body>
</html>
