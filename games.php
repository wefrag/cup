<?php
require './config/bootstrap.php';

head();
$games = $db->query('SELECT * FROM game ORDER BY name');
?>
		<div id="base">
			<div id="logo">
				<h1><a href="./"><?php echo $site['name']; ?></a></h1>
			</div>
			<div id="content">
				<div id="body">
					<div class="container">
						<div class="header span-24">
							<div class="span-20">
								<div class="breadcrumb"><a href="./">Wefrag Cup 2012</a> » <a href="./">Les Jeux</a></div>
							</div>
							<div class="actions span-4 last">
								<a title="Forum sur Wefrag" class="reply" href="<?php echo utf8_encode(htmlentities($site['forum'])); ?>">Forum</a>
 							</div>
						</div>
						<div class="span-24">
							<table class="list forums">
								<tbody>
									<tr class="category">
									<th class="title" colspan="3">Sélection des jeux</th>
								</tr>
								<?php
								foreach ($games AS $game) {
									?>
									<tr class="forum">
										<td class="span-15 last" style="background:url(<?php echo utf8_encode(htmlentities($game['image'])); ?>) center right no-repeat;height:100%">
											<div class="title">
												<a href="<?php echo utf8_encode(htmlentities($game['link_dl'])); ?>"><?php echo utf8_encode(htmlentities($game['name'])); ?></a>
											</div>
											<div class="description">
												<?php echo utf8_encode(htmlentities($game['type'])).' - '.utf8_encode(htmlentities($game['description'])).' - <span style="color:#CC3300;font-weight:bold;">'.($game['free'] ? 'FREE TO PLAY' : 'PAYANT').'</span>'; ?>
											</div>
										</td>
									</tr>
									<?php
								}
								?>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
