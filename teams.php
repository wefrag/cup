<?php
require './config/bootstrap.php';

head();

$team = array('first'=>array(), 'second'=>array());
$team['first']['chef'] = $db->query('SELECT * FROM member WHERE id_groupe = 1 and id_team = 1')->fetch();
$team['first']['sous-chef'] = $db->query('SELECT * FROM member WHERE id_groupe = 2 and id_team = 1')->fetchAll();
$team['first']['membre'] = $db->query('SELECT * FROM member WHERE id_groupe is null and id_team = 1')->fetchAll();

$team['second']['chef'] = $db->query('SELECT * FROM member WHERE id_groupe = 1 and id_team = 2')->fetch();
$team['second']['sous-chef'] = $db->query('SELECT * FROM member WHERE id_groupe = 2 and id_team = 2')->fetchAll();
$team['second']['membre'] = $db->query('SELECT * FROM member WHERE id_groupe is null and id_team = 2')->fetchAll();

$avancement = array(0, 0);
$planets = $db->query('SELECT id, team, level FROM planet WHERE level > 0 ORDER BY level')->fetchAll();
$points = array(1=>5, 2=>6.25, 3=>7.5, 4=>10);

foreach ($planets AS $planet)
	if ($planet['team'] !== null)
		$avancement[$planet['team'] - 1] += $points[$planet['level']];

?>
		<div id="base">
			<div id="logo">
				<h1><a href="./"><?php echo utf8_encode(htmlentities($site['name'])); ?></a></h1>
			</div>
			<div id="content">
				<div id="body">
					<div class="container">
						<div class="header span-24">
							<div class="span-20">
								<div class="breadcrumb"><a href="./"><?php echo $site['name']; ?></a> » <a href="./teams.php">Les Équipes</a></div>
							</div>
							<div class="actions span-4 last">
								<a title="Forum sur Wefrag" class="reply" href="<?php echo utf8_encode(htmlentities($site['forum'])); ?>">Forum</a>
							</div>
						</div>
						<div class="span-24">
							<table class="list posts">
								<tbody>
									<tr>
										<th class="post span-2 last" colspan="2">Avancement des équpies</th>
									</tr>
									<tr>
										<td style="text-align:center;font-weight:bold;background:url(./assets/img/progressbar.png)  <?php echo (100 - intval($avancement[0])); ?>%;"><?php echo number_format($avancement[0], 2); ?> %</td>
										<td style="text-align:center;font-weight:bold;background:url(./assets/img/progressbar.png) -<?php echo (100 - intval($avancement[1])); ?>%;"><?php echo number_format($avancement[1], 2); ?> %</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="span-12">
							<table class="list posts">
								<tbody>
									<tr>
										<th class="post span-2 last" style="color:red; font-size:16px" colspan="2"><?php echo utf8_encode(htmlentities($site['name_team_first'])); ?></th>
									</tr>
									<tr class="topic">
										<td class="title span-23" colspan="2"><span style="font-weight:bold;color:black">Chef :</span></td>
									</tr>
									<tr class="topic">
										<td class="title span-23"><a href="<?php echo utf8_encode(htmlentities(get_id($team['first']['chef']['id']))); ?>"><?php echo utf8_encode(htmlentities(get_login($team['first']['chef']['id']))); ?></a></td>
										<td class="span-1" style="text-align:center">
											<?php if (!empty($team['first']['chef']['id_steam'])) { ?><a href="<?php echo $team['first']['chef']['id_steam']; ?>"><img src="http://store.steampowered.com/favicon.ico" /> <?php } ?></a>
										</td>
									</tr>
									<tr class="topic">
										<td class="title span-23" colspan="2"><span style="font-weight:bold;color:black">Sous-chefs :</span></td>
									</tr>
									<?php foreach ($team['first']['sous-chef'] AS $user) { ?>
										<tr class="topic">
											<td class="title span-23"><a href="<?php echo utf8_encode(htmlentities(get_id($user['id']))); ?>"><?php echo utf8_encode(htmlentities(get_login($user['id']))); ?></a></td>
											<td class="span-1" style="text-align:center">
												<?php if (!empty($user['id_steam'])) { ?><a href="<?php echo $user['id_steam']; ?>"><img src="http://store.steampowered.com/favicon.ico" /> <?php } ?></a>
											</td>
										</tr>
									<?php } ?>
									<tr class="topic">
										<td class="title span-23" colspan="2"><span style="font-weight:bold;color:black">Membres :</span></td>
									</tr>
									<?php foreach ($team['first']['membre'] AS $user) { ?>
										<tr class="topic">
											<td class="title span-23"><a href="<?php echo utf8_encode(htmlentities(get_id($user['id']))); ?>"><?php echo utf8_encode(htmlentities(get_login($user['id']))); ?></a></td>
											<td class="span-1" style="text-align:center">
												<?php if (!empty($user['id_steam'])) { ?><a href="<?php echo $user['id_steam']; ?>"><img src="http://store.steampowered.com/favicon.ico" /> <?php } ?></a>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<div class="span-12 last">
							<table class="list posts">
								<tbody>
									<tr>
										<th class="post span-2 last" style="color:red; font-size:16px" colspan="2"><?php echo utf8_encode(htmlentities($site['name_team_second'])); ?></th>
									</tr>
									<tr class="topic">
										<td class="title span-23" colspan="2"><span style="font-weight:bold;color:black">Chef :</span></td>
									</tr>
									<tr class="topic">
										<td class="title span-23"><a href="<?php echo utf8_encode(htmlentities(get_id($team['second']['chef']['id']))); ?>"><?php echo utf8_encode(htmlentities(get_login($team['second']['chef']['id']))); ?></a></td>
										<td class="span-1" style="text-align:center">
											<?php if (!empty($team['second']['chef']['id_steam'])) { ?><a href="<?php echo $team['second']['chef']['id_steam']; ?>"><img src="http://store.steampowered.com/favicon.ico" /> <?php } ?></a>
										</td>
									</tr>
									<tr class="topic">
										<td class="title span-23" colspan="2"><span style="font-weight:bold;color:black">Sous-chefs :</span></td>
									</tr>
									<?php foreach ($team['second']['sous-chef'] AS $user) { ?>
										<tr class="topic">
											<td class="title span-23"><a href="<?php echo utf8_encode(htmlentities(get_id($user['id']))); ?>"><?php echo utf8_encode(htmlentities(get_login($user['id']))); ?></a></td>
											<td class="span-1" style="text-align:center">
												<?php if (!empty($user['id_steam'])) { ?><a href="<?php echo $user['id_steam']; ?>"><img src="http://store.steampowered.com/favicon.ico" /> <?php } ?></a>
											</td>
										</tr>
									<?php } ?>
									<tr class="topic">
										<td class="title span-23" colspan="2"><span style="font-weight:bold;color:black">Membres :</span></td>
									</tr>
									<?php foreach ($team['second']['membre'] AS $user) { ?>
										<tr class="topic">
											<td class="title span-23"><a href="<?php echo utf8_encode(htmlentities(get_id($user['id']))); ?>"><?php echo utf8_encode(htmlentities(get_login($user['id']))); ?></a></td>
											<td class="span-1" style="text-align:center">
												<?php if (!empty($user['id_steam'])) { ?><a href="<?php echo $user['id_steam']; ?>"><img src="http://store.steampowered.com/favicon.ico" /> <?php } ?></a>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
