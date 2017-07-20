<?php
require './config/bootstrap.php';

if (!isset($_SESSION['id']))
	$_SESSION['id'] = false;

if ($_SESSION['id'] === false)
{
	require_once "Auth/OpenID/Consumer.php";
	require_once "Auth/OpenID/FileStore.php";
	require_once "Auth/OpenID/SReg.php";

	if (count($_GET) == 0)
	{
		$store = new Auth_OpenID_FileStore('./.openid');
		$consumer = new Auth_OpenID_Consumer($store);
		$auth = $consumer->begin('http://wefrag.com');
		$u = parse_url($baseUrl);
		$url = $auth->redirectURL($u['scheme'].'://'.$u['host'], $baseUrl.'/admin.php');
		header('location:'.$url);
		die();
	}
	else
	{
		$store = new Auth_OpenID_FileStore('./.openid');
		$consumer = new Auth_OpenID_Consumer($store);
		$answer = $consumer->complete($baseUrl.'/admin.php');
		if ($answer->status == Auth_OpenID_SUCCESS)
		{
			$sreg = new Auth_OpenID_SRegResponse();
			$obj = $sreg->fromSuccessResponse($answer);
			$infos = $obj->contents();
			$_SESSION['id'] = $_GET['openid_identity'];
			$_SESSION['login'] = get_login($_SESSION['id']);
			header('location:./admin.php');
			die();
		}
	}
}

head('<script src="./assets/js/jquery-1.7.2.min.js"></script>');
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
								<div class="breadcrumb"><a href="./"><?php echo utf8_encode(htmlentities($site['name'])); ?></a> » <a href="./admin.php">Mes Paramètres</a></div>
							</div>
							<div class="actions span-4 last">
								<a title="Forum sur Wefrag" class="reply" href="<?php echo utf8_encode(htmlentities($site['forum'])); ?>">Forum</a>
							</div>
						</div>
						<div class="span-24">
							<?php

							if ($_SESSION['id'] === false || !check_login($_SESSION['id'])) { ?>
							<pre style="padding-left:10px"><strong>Access denied.</strong> Vous n'êtes pas admin ou inscris dans une équipe, aller sur <a href="<?php echo utf8_encode(htmlentities($site['forum'])); ?>">le forum</a> pour vous inscrire.</pre>
							<?php }
							else {

								$page = 'mygames';
								$pages = array('config', 'map', 'teams', 'games', 'heros', 'planning');
								if (isset($_GET['page']) && in_array($_GET['page'], $pages))
									$page = $_GET['page'];
								$func = 'page_'.$page;

								// Lien admin
								if (check_admin($_SESSION['id'])) {
									?>
									<table class="list forums">
										<tbody>
											<tr class="category">
												<th class="title" colspan="6">
													Commandes admin
												</th>
											</tr>
											<tr class="forum unread">
												<td class="span-4 last" style="<?php if ($page == 'config') echo 'background:#ccc'; ?>">
													<div class="title">
														<a href="./admin.php?page=config">Configuration</a>
													</div>
													<div class="description">
														Nom du site, admins
													</div>
												</td>
												<td class="span-4 last" style="<?php if ($page == 'map') echo 'background:#ccc'; ?>">
													<div class="title">
														<a href="./admin.php?page=map">La Map</a>
													</div>
													<div class="description">
														Configurer les planètes
													</div>
												</td>
												<td class="span-4 last" style="<?php if ($page == 'teams') echo 'background:#ccc'; ?>">
													<div class="title">
														<a href="./admin.php?page=teams">Les Équipes</a>
													</div>
													<div class="description">
														Gérer les équipes
													</div>
												</td>
												<td class="span-4 last" style="<?php if ($page == 'games') echo 'background:#ccc'; ?>">
													<div class="title">
														<a href="./admin.php?page=games">Les Jeux</a>
													</div>
													<div class="description">
														Sélection des jeux
													</div>
												</td>
												<td class="span-4 last" style="<?php if ($page == 'heros') echo 'background:#ccc'; ?>">
													<div class="title">
														<a href="./admin.php?page=heros">Les Héros</a>
													</div>
													<div class="description">
														Gérer les héros
													</div>
												</td>
												<td class="span-4 last" style="<?php if ($page == 'planning') echo 'background:#ccc'; ?>">
													<div class="title">
														<a href="./admin.php?page=planning">Le Planning</a>
													</div>
													<div class="description">
														Gérer les parties
													</div>
												</td>
											</tr>
										</tbody>
									</table>

									<?php
								}
								// Si dans une team, selection des jeux que je souhaite jouer
								$func();

							} ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
