<?php
function bbcode($text) {
	$text = preg_replace('!\[b\](.+)\[/b\]!isU', '<strong>$1</strong>', $text);
	$text = preg_replace('!\[i\](.+)\[/i\]!isU', '<i>$1</i>', $text);
	$text = preg_replace('!\[u\](.+)\[/u\]!isU', '<span style="text-decoration:underline;">$1</span>', $text);
	$text = preg_replace('!\[img\](.+)\[/img\]!isU', '<img src="$1" alt="img" />', $text);
	$text = preg_replace('!\[url\](.+)\[/url\]!isU', '<a href="$1" target="_blank">$1</a>', $text);
	$text = preg_replace('!\[url=([^\]]+)\](.+)\[/url\]!isU', '<a href="$1" target="_blank">$2</a>', $text);
	return($text);
}

function get_login($id_full) {
	if (substr($id_full, 0, strlen('http://forum.nofrag.com/users/')) == 'http://forum.nofrag.com/users/') {
		$id = explode('/', $id_full);
		$id = $id[count($id) - 1];
		return ($id);
	}
	return ($id_full);
}

function get_id($login) {
	if (substr($login, 0, strlen('http://forum.nofrag.com/users/')) != 'http://forum.nofrag.com/users/')
		return ('http://forum.nofrag.com/users/'.$login);
	return ($login);
}

function check_errors($errors) {
	foreach ($errors AS $error)
		if ($error)
			return (true);
	return (false);
}

function head($other = '') {
	global $site;

	$menu = array('index','galaxy','teams','games','planning','admin');
	$page = 'index';

	$current = $_SERVER['SCRIPT_NAME'];
	$current_exp = explode('/', $current); $current = substr($current_exp[count($current_exp) - 1], 0, -4);
	if (in_array($current, $menu))
		$page = $current;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo utf8_encode(htmlentities($site['name'])).' - '.utf8_encode(htmlentities($site['description'])); ?></title>
	<link href="./assets/css/style.css" rel="stylesheet" type="text/css" />
	<?php echo $other; ?>
</head>
<body>
	<div id="main">
		<div id="head">
			<div id="base">
				<div class="left">
					<span class="<?php echo ($page == 'index'    ? 'current' : 'menu'); ?>"><a href="./">Histoire</a></span>
					<span class="<?php echo ($page == 'galaxy'   ? 'current' : 'menu'); ?>"><a href="./galaxy.php">Le Système Gangbang</a></span>
					<span class="<?php echo ($page == 'teams'    ? 'current' : 'menu'); ?>"><a href="./teams.php">Les Équipes</a></span>
					<span class="<?php echo ($page == 'games'    ? 'current' : 'menu'); ?>"><a href="./games.php">Les Jeux</a></span>
					<span class="<?php echo ($page == 'planning' ? 'current' : 'menu'); ?>"><a href="./planning.php">Le Planning</a></span>
				</div>
				<div class="right">
					<span class="<?php echo ($page == 'admin' ? 'current' : 'menu'); ?>"><a href="./admin.php"><?php echo (isset($_SESSION['login']) ? $_SESSION['login'] : 'Login'); ?></a></span>
				</div>
			</div>
		</div>
		<?php
}


// USER PART
function isset_login($login) {
	if (substr($login, 0, strlen('http://forum.nofrag.com/users/')) != 'http://forum.nofrag.com/users/')
		$login = 'http://forum.nofrag.com/users/'.$login;
	$handle = curl_init($login);
	curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
	$response = curl_exec($handle);
	$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
	if ($httpCode == 404)
		return (false);
	return (true);
}

function in_team($login) {
	global $db;

	$id = strtolower(get_id($login));
	$req = $db->prepare('SELECT id FROM member WHERE lower(id) = :id');
	$res = $req->execute(array('id'=>$id));

	if (count($req->fetchAll()))
		return (true);
	return (false);
}

function check_login($login) {
	if (check_admin($login) == true)
		return (true);
	if (in_team($login))
		return (true);
	return (false);
}

function check_admin($login) {
	global $db;

	// check admin
	$admin = $db->query('SELECT admin FROM config')->fetch();
	if (strtolower($admin['admin']) == strtolower($login))
		return (true);
	$admins = $db->query('SELECT id FROM admin')->fetchAll();
	foreach ($admins AS $admin)
		if (strtolower($admin['id']) == strtolower($login))
			return (true);
	return (false);
}

function page_mygames() {
	global $db;

	if (isset($_GET['update'])) {
		$req = $db->prepare('SELECT * FROM member WHERE lower(id) = :id');
		$req->execute(array('id'=>strtolower($_SESSION['id'])));
		$user = $req->fetch();
		update_user($_SESSION['login'], $user['id_team'], $user['id_groupe']);
	}
	if (isset($_POST['play'])) {
		$req = $db->prepare('SELECT * FROM play WHERE id_member = :id'); $req->execute(array('id'=>$_SESSION['id']));
		$all = $req->fetchAll();
		$in_db = array();
		foreach ($all AS $a)
			$in_db[] = $a['id_game'];
		$new = $_POST['game'];
		$del = array_diff($in_db, $new);
		$add = array_diff($new, $in_db);

		foreach ($del AS $d) {
			$infos = array('id_member'=>$_SESSION['id'], 'id_game'=>$d);
			$req = $db->prepare('DELETE FROM play WHERE id_member = :id_member AND id_game = :id_game');
			$req->execute($infos);
		}

		foreach ($add AS $a) {
			$infos = array('id_member'=>$_SESSION['id'], 'id_game'=>$a);
			$req = $db->prepare('INSERT INTO play VALUES (:id_member, :id_game)');
			$req->execute($infos);
		}
	}

	$games = $db->query('SELECT * FROM game ORDER BY name')->fetchAll();
	$play  = $db->prepare('SELECT * FROM play WHERE id_member = :id_member'); $play->execute(array('id_member'=>$_SESSION['id'])); $play = $play->fetchAll();
	$games_id = array();
	foreach ($play AS $game) $games_id[] = $game['id_game'];
	?>
	<br/>
	<a href="./admin.php?update" style="color:white;background:black;padding:4px;border-radius:3px;float:right">Mettre à jour mon id STEAM depuis mon profile WeFrag</a><br/>
	<br/>
	<form method="post" action="./admin.php">
	<table class="list forums">
	<tbody>
		<tr class="category">
			<th colspan="2" class="title">
				Liste des parties auxquelles je souhaite participer
			</th>
			<th class="title" style="width:35px">
				G/P
			</th>
		</tr>
		<?php foreach ($games AS $game) { ?>
		<tr class="forum unread">
			<td>
				<input type='checkbox' name='game[]' value='<?php echo $game['id']; ?>' <?php if (in_array($game['id'], $games_id)) echo 'checked'; ?> />
			</td>
			<td style="" class="span-23 last">
				<a href="<?php echo $game['link_dl']; ?>"><?php echo utf8_encode(htmlentities($game['name'])); ?></a>
			</td>
			<td class="last">
				<?php if ($game['free']) echo 'Gratuit'; else echo 'Payant'; ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
	</table>
	<input type="hidden" name="play" />
	<input type="submit" value="Mettre à jour" />
	</form>
	<?php
}

// ADMIN PART
function page_config() {

	if (!check_admin($_SESSION['id']))
		return;

	global $db;
	$site   = $db->query('SELECT * FROM config')->fetch();;
	$admins = $db->query('SELECT * FROM admin');

	$form = array(
		'name' 			=> $site['name'],
		'description' 	=> $site['description'],
		'backstory' 	=> $site['backstory'],
		'rules' 		=> $site['rules'],
		'admin' 		=> $site['admin'],
		'forum' 		=> $site['forum'],
	);
	$error = array(
		'name' 			=> false,
		'description' 	=> false,
		'backstory' 	=> false,
		'rules' 		=> false,
		'admin' 		=> false,
		'forum' 		=> false
	);

	if (isset($_GET['remove']))
	{
		$req = $db->prepare('DELETE FROM admin WHERE id = :name');
		$req->execute(array('name'=>$_GET['remove']));
		header('location:./admin.php?page=config');
		$admins = $db->query('SELECT * FROM admin');
	}

	if (count($_POST) > 0)
	{
		$_POST = array_map('trim', $_POST);
		$_POST = array_map('utf8_decode', $_POST);

		if (isset($_POST['config'])) {

			if (empty($_POST['name']))
				$error['name'] = true;
			if (empty($_POST['description']))
				$error['description'] = true;
			if (empty($_POST['backstory']))
				$error['backstory'] = true;
			if (empty($_POST['rules']))
				$error['rules'] = true;
			if (empty($_POST['admin']))
				$error['admin'] = true;

			if (substr($_POST['admin'], 0, strlen('http://forum.nofrag.com/users/')) != 'http://forum.nofrag.com/users/')
				$error['admin'] = true;

			if (strtolower($_POST['admin']) != strtolower($site['admin'])) {
				if (!isset_login($_POST['admin']))
					$error['admin'] = true;
			}

			if (!check_errors($error)) {

				$insert = array(
					'name'        	=> $_POST['name'],
					'description'  	=> $_POST['description'],
					'backstory'   	=> $_POST['backstory'],
					'rules'       	=> $_POST['rules'],
					'forum'			=> $_POST['forum'],
					'admin'     	=> $_POST['admin']
				);

				if ($site['admin'] != $_SESSION['id'])
					unset($insert[':admin']);

				$sql_request = 'UPDATE config SET '; $flag = false;
				foreach ($insert AS $k=>$v)
				{
					if ($flag)
						$sql_request .= ', ';
					else
						$flag = true;
					$sql_request .= $k.' = :'.$k;
				}
				$sql_request .= ' WHERE config.admin = "'.$site['admin'].'"';

				$req = $db->prepare($sql_request);
				$req->execute($insert);


				$site   = $db->query('SELECT * FROM config')->fetch();;
				$form = array(
					'name' 			=> $site['name'],
					'description' 	=> $site['description'],
					'backstory' 	=> $site['backstory'],
					'rules' 		=> $site['rules'],
					'admin' 		=> $site['admin'],
					'forum' 		=> $site['forum'],
				);
			}
		}
		else if (isset($_POST['add_admin'])) {
			$req = $db->prepare('INSERT INTO admin VALUES (:name)');
			$req->execute(array('name'=>$_POST['admin']));
			$admins = $db->query('SELECT * FROM admin');
		}
	}

	?>
	<br/>
	<form method="post">
	<table class="list forums">
		<tbody>
			<tr class="category">
				<th class="title" colspan="2">
					Configuration Générale
				</th>
			</tr>
			<tr class="forum">
				<td class="span-5" style="<?php if ($error['name']) echo 'color:red'; ?>">
					Nom du site :
				</td>
				<td class="span-19 last">
					<input type="text" name="name" value="<?php echo utf8_encode(htmlentities($form['name'])); ?>" size="100" maxlength="80" />
				</td>
			</tr>
			<tr class="forum">
				<td class="span-5" style="<?php if ($error['description']) echo 'color:red'; ?>">
					Description :
				</td>
				<td class="span-19 last">
					<input type="text" name="description" value="<?php echo utf8_encode(htmlentities($form['description'])); ?>" size="100" maxlength="80" />
				</td>
			</tr>
			<tr class="forum">
				<td class="span-5" style="<?php if ($error['backstory']) echo 'color:red'; ?>">
					Histoire (bbcode autorisé):
				</td>
				<td class="span-19 last">
					<textarea name="backstory" style="width:95%"><?php echo utf8_encode(htmlentities($form['backstory'])); ?></textarea>
				</td>
			</tr>
			<tr class="forum">
				<td class="span-5" style="<?php if ($error['rules']) echo 'color:red'; ?>">
					Règles (bbcode autorisé):
				</td>
				<td class="span-19 last">
					<textarea name="rules" style="width:95%"><?php echo utf8_encode(htmlentities($form['rules'])); ?></textarea>
				</td>
			</tr>
			<tr class="forum">
				<td class="span-5" style="<?php if ($error['description']) echo 'color:red'; ?>">
					Forum officiel :
				</td>
				<td class="span-19 last">
					<input type="text" name="forum" value="<?php echo utf8_encode(htmlentities($form['forum'])); ?>" size="100" maxlength="80" />
				</td>
			</tr>
			<tr class="forum">
				<td class="span-5" style="<?php if ($error['admin']) echo 'color:red'; ?>">
					Admin des admins:
				</td>
				<td class="span-19 last">
					<input type="text" name="admin" value="<?php echo utf8_encode(htmlentities($form['admin'])); ?>" size="100" maxlength="80" <?php if ($_SESSION['id'] != $form['admin']) echo 'readonly="readonly"';?> />
				</td>
			</tr>
			<tr class="forum">
				<td class="span-5">
				</td>
				<td class="span-19 last" style="text-align:right">
					<input type="submit" value="Sauvegarder" />
				</td>
			</tr>
	    </tbody>
	</table>
	<input type="hidden" name="config" />
	</form>


	<form method="post">
	<table class="list forums">
		<tbody>
			<tr class="category">
				<th class="title" colspan="3">
					Liste des administrateurs
				</th>
			</tr>
			<?php
			if ($admins !== false)
				foreach ($admins AS $admin)
					{ ?>
					<tr class="forum">
						<td class="span-5" style="<?php if ($error['name']) echo 'color:red'; ?>">
							id :
						</td>
						<td class="span-19 last">
							<?php echo utf8_encode(htmlentities($admin['id'])); ?>
						</td>
						<td class="span-1 last">
							<a href="./admin.php?page=config&remove=<?php echo utf8_encode(htmlentities($admin['id'])) ?>" onclick="return confirm('Êtes-vous sûr?');">supprimer</a>
						</td>
					</tr>
					<?php } ?>
			<tr class="forum">
				<td class="span-5">
					Ajouter un admin :
				</td>
				<td class="span-19 last">
					<input type="text" name="admin" maxlength="255" size="100" placeholder="http://forum.nofrag.com/users/USERNAME" />
				</td>
				<td class="span-1 last" style="text-align:right">
					<input type="submit" value="Ajouter" />
				</td>
			</tr>
	    </tbody>
	</table>
	<input type="hidden" name="add_admin" />
	</form>
    <?php
}

function page_map() {
	if (!check_admin($_SESSION['id']))
		return;

	global $db;

	if (isset($_POST['update_map'])) {
		if (!empty($_POST['link'])) {
			$src = @file_get_contents($_POST['link']);
			$img = imagecreatefromstring($src);
			imagepng($img, __DIR__.'/../assets/img/map/'.time().'.png');
		}
	} else if (count($_POST) > 0) {
		$id = key($_POST);
		$u = array_map('trim', $_POST[$id]);
		$u = array_map('utf8_decode', $u);
		$req = $db->prepare('UPDATE planet SET name = :name, id_game = :id_game, team = :free WHERE id = :id');
		$u['id'] = $id;
		if (empty($u['free']))
			$u['free'] = null;
		$req->execute($u);
	}

	$games = $db->query('SELECT planet.id AS planet_id, planet.id_game AS game_id, planet.name AS planet_name, game.name AS game_name, planet.team AS status FROM planet LEFT JOIN game ON planet.id_game = game.id')->fetchAll();
	$list_game = $db->query('SELECT id, name FROM game')->fetchAll();
	$teams = $db->query('SELECT name_team_first AS name_1, name_team_second AS name_2 FROM config')->fetch();

	?>
	<br/>
	<form method="post">
	<table class="list forums">
		<tbody>
			<tr class="category">
				<th class="title" colspan="3">
					Configuration
				</th>
			</tr>
			<tr class="forum">
				<td class="span-5">
					Nouvelle map :
				</td>
				<td class="span-16 last">
					<input type="text" name="link" maxlength="255" size="80" placeholder="http://lien.vers.l.image.jpg" />
				</td>
				<td class="span-1 last" style="text-align:right">
					<input type="submit" value="Télécharger l'image" />
				</td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="update_map" />
	</form>

	<form method="post">
	<table class="list forums">
		<tbody>
			<tr class="category">
				<th class="title span-5">
					La planète
				</th>
				<th class="title span-5">
					Le jeu de la planète
				</th>
				<th class="title span-5">
					Habité par
				</th>
				<th class="title span-1" style="text-align:center">
					Actions
				</th>
			</tr>
			<?php foreach ($games AS $game) { ?>
			<tr class="forum" id="show_<?php echo $game['planet_id']; ?>">
				<td class="span-5">
					<?php echo utf8_encode(htmlentities($game['planet_name'])); ?>
				</td>
				<td class="span-5 last">
					<?php echo utf8_encode(htmlentities($game['game_name'])); ?>
				</td>
				<td class="span-5 last">
					<?php
					if ($game['status'] == null) echo 'Personne';
					else if ($game['status'] == '1') echo utf8_encode(htmlentities($teams['name_1']));
					else if ($game['status'] == '2') echo utf8_encode(htmlentities($teams['name_2']));
					?>
				</td>
				<td class="last" style="text-align:center">
					<a href="javascript:;" onclick="update(<?php echo $game['planet_id']; ?>);">modifier</a>
				</td>
			</tr>
			<form method="post">
			<tr class="forum" id="update_<?php echo $game['planet_id']; ?>" style="display:none">
				<td class="span-5">
					<input type="text" name="<?php echo $game['planet_id']; ?>[name]" value="<?php echo utf8_encode(htmlentities($game['planet_name'])); ?>" size="34" />
				</td>
				<td class="span-5 last">
					<select name="<?php echo $game['planet_id']; ?>[id_game]" style="width:260px" />
					<?php
					foreach ($list_game AS $g) { ?>
						<option value="<?php echo $g['id']; ?>" <?php if ($g['id'] == $game['game_id']) echo 'selected="selected"'; ?>><?php echo utf8_encode(htmlentities($g['name'])); ?></option>
					<?php }	?>
					</select>
				</td>
				<td class="span-5 last">
					<select name="<?php echo $game['planet_id']; ?>[free]" style="width:260px">
						<option value="">Personne - Planète libre</option>
						<option value="1" <?php if ($game['status'] == '1') echo 'selected="selected"'; ?>><?php echo utf8_encode(htmlentities($teams['name_1'])); ?></option>
						<option value="2" <?php if ($game['status'] == '2') echo 'selected="selected"'; ?>><?php echo utf8_encode(htmlentities($teams['name_2'])); ?></option>
					</select>
				</td>
				<td class="last" style="text-align:center">
					<input type="submit" value="modifier" />
				</td>
			</tr>
			</form>
			<?php } ?>
		</tbody>
	</table>
	<input type="hidden" name="map" />
	</form>
	<script>
	function update(id) {
		$('#show_'+id).hide();
		$('#update_'+id).css('display', 'table-row');
	}
	</script>
	<?php

}

function get_details($login) {
	$ret = array('id'=>get_id($login), 'steam'=>null);

	$src = @file_get_contents(get_id($login));
	preg_match('#href="(http://steamcommunity\.com/id/[^"]+)"#isU', $src, $match);
	if (isset($match[1]))
		$ret['steam'] = $match[1];
	preg_match('#<div class="breadcrumb"><a href="/forums">Forums</a> &raquo; ([^<]+)</div>#', $src, $match);
	$ret['id'] = get_id($match[1]);
	return ($ret);
}

function update_user($login, $team, $groupe = null) {
	if ($login == '')
		return (false);
	global $db;
	$req = $db->prepare('SELECT * FROM member WHERE lower(id) = :id');
	$req->execute(array('id'=>get_id($login)));
	$result = $req->fetchAll();

	if (empty($result)) { // NOT IN DB
		if (!isset_login($login))
			return (false);

		$infos = get_details($login);
		$req = $db->prepare('INSERT INTO member (id, id_team, id_groupe, id_steam) VALUES (:id, :id_team, :id_groupe, :id_steam)');
		$req->execute(array('id'=>$infos['id'], 'id_team'=>$team, 'id_groupe'=>$groupe, 'id_steam'=>$infos['steam']));
		return (true);
	}
	if (!isset_login($login))
		return (false);
	$req = $db->prepare('UPDATE member SET id_team = :id_team, id_groupe = :id_groupe, id_steam = :id_steam WHERE lower(id) = :id');
	$infos = get_details($login);
	$req->execute(array('id'=>strtolower(get_id($login)), 'id_team'=>$team, 'id_groupe'=>$groupe, 'id_steam'=>$infos['steam']));
	return (true);
}

function page_teams() {

	if (!check_admin($_SESSION['id']))
		return;

	global $db;

	$error_pseudo = '';

	if (count($_POST)) {
		if (isset($_POST['team_name'])) {
			unset($_POST['team_name']);

			$_POST['name'] = array_map('trim', $_POST['name']);
			$_POST['name'] = array_map('utf8_decode', $_POST['name']);

			if (!empty($_POST['name']['first']) && !empty($_POST['name']['second'])) {
				$req = $db->prepare('UPDATE config SET name_team_first=:first, name_team_second=:second');
				$st = $req->execute($_POST['name']);
			}
		}
		else if (isset($_POST['composition_team'])) {
			unset($_POST['composition_team']);

			$_POST['first'] = array_map('trim', $_POST['first']);
			$_POST['first'] = array_map('utf8_decode', $_POST['first']);

			$_POST['second'] = array_map('trim', $_POST['second']);
			$_POST['second'] = array_map('utf8_decode', $_POST['second']);

			$team = array(1=>'first', 2=>'second');
			for ($id_team = 1; $id_team <= 2; $id_team++) {
				// chef
				$req = $db->prepare('DELETE FROM member WHERE id_team = :id_team AND id_groupe = :id_groupe'); $req->execute(array('id_team'=>$id_team, 'id_groupe'=>1));
				if (!empty($_POST[$team[$id_team]]['chef']))
					if (!update_user(get_login($_POST[$team[$id_team]]['chef']), $id_team, 1))
						$error_pseudo .= $_POST[$team[$id_team]]['chef']." : n'existe pas sur le forum wefrag.\\\n  ";

				// sous-chefs
				$last_user = $db->query('SELECT id FROM member WHERE id_groupe = 2 AND id_team = '.$id_team)->fetchAll();
				$users = array(); foreach ($last_user AS $k=>$v) $users[] = get_login($v[0]); $in_db = array_map('strtolower', $users);
				$new = explode("\n", $_POST[$team[$id_team]]['sous-chef']); $new = array_map('trim', $new); $new = array_map('strtolower', $new);
				$add = array_diff($new, $in_db);
				$del = array_diff($in_db, $new);
				foreach ($del AS $user) {
					$req = $db->prepare('DELETE FROM member WHERE lower(id) = :id'); $req->execute(array('id'=>get_id($user))); }
				foreach ($add AS $user)
					if (!empty($user))
						if (!update_user($user, $id_team, 2))
							$error_pseudo .= $user." : n'existe pas sur le forum wefrag.\\\n  ";
				// membres
				$last_user = $db->query('SELECT id FROM member WHERE id_groupe IS NULL AND id_team = '.$id_team)->fetchAll();
				$users = array(); foreach ($last_user AS $k=>$v) $users[] = get_login($v[0]); $in_db = array_map('strtolower', $users);
				$new = explode("\n", $_POST[$team[$id_team]]['membre']); $new = array_map('trim', $new); $new = array_map('strtolower', $new);
				$add = array_diff($new, $in_db);
				$del = array_diff($in_db, $new);
				foreach ($del AS $user) {
					$req = $db->prepare('DELETE FROM member WHERE lower(id) = :id'); $req->execute(array('id'=>get_id($user))); }
				foreach ($add AS $user)
					if (!empty($user))
						if (!update_user($user, $id_team))
							$error_pseudo .= $user." : n'existe pas sur le forum wefrag.\\\n  ";
			}
		}
	}

	$site = $db->query('SELECT * FROM config')->fetch();
	$team = array('first'=>array(), 'second'=>array());
	$team['first']['chef'] = $db->query('SELECT * FROM member WHERE id_groupe = 1 and id_team = 1')->fetch();
	$team['first']['chef'] = $team['first']['chef']['id'];
	$team['first']['sous-chef'] = $db->query('SELECT * FROM member WHERE id_groupe = 2 and id_team = 1')->fetchAll();
	$list = '';
	foreach ($team['first']['sous-chef'] AS $user)
		$list .= get_login($user['id'])."\n";
	$team['first']['sous-chef'] = $list;
	$list = '';
	$team['first']['membre'] = $db->query('SELECT * FROM member WHERE id_groupe is null and id_team = 1')->fetchAll();
	foreach ($team['first']['membre'] AS $user)
		$list .= get_login($user['id'])."\n";
	$team['first']['membre'] = $list;

	$team['second']['chef'] = $db->query('SELECT * FROM member WHERE id_groupe = 1 and id_team = 2')->fetch();
	$team['second']['chef'] = $team['second']['chef']['id'];
	$team['second']['sous-chef'] = $db->query('SELECT * FROM member WHERE id_groupe = 2 and id_team = 2')->fetchAll();
	$list = '';
	foreach ($team['second']['sous-chef'] AS $user)
		$list .= get_login($user['id'])."\n";
	$team['second']['sous-chef'] = $list;
	$team['second']['membre'] = $db->query('SELECT * FROM member WHERE id_groupe is null and id_team = 2')->fetchAll();
	$list = '';
	foreach ($team['second']['membre'] AS $user)
		$list .= get_login($user['id'])."\n";
	$team['second']['membre'] = $list;

	if (!empty($error_pseudo))
		echo '<script>alert("'.$error_pseudo .'");</script>';

	?>
	<div class="span-24">
	<br/>
	<form method="post">
		<table class="list forums">
			<tbody>
				<tr class="category">
					<th class="title" colspan="3">
						Nom des équipes
					</th>
				</tr>
				<tr class="forum">
					<td class="span-5">
						Première équipe :
					</td>
					<td class="span-19 last">
						<input type="text" name="name[first]" maxlength="80" size="100" value="<?php echo utf8_encode(htmlentities($site['name_team_first'])); ?>" />
					</td>
				</tr>
				<tr class="forum">
					<td class="span-5">
						Seconde équipe :
					</td>
					<td class="span-19 last">
						<input type="text" name="name[second]" maxlength="80" size="100" value="<?php echo utf8_encode(htmlentities($site['name_team_second'])); ?>" />
					</td>
				</tr>
				<tr class="forum">
					<td colspan="2" style="text-align:right">
						<input type="submit" value="Mettre à jour" />
					</td>
				</tr>
		    </tbody>
		</table>
		<input type="hidden" name="team_name" />
	</form>
	<br/>
	</div>
	<form method="post">
		<div class="span-12">
			<table class="list posts">
				<tbody>
					<tr>
						<th colspan="2" class="post span-2 last">Composition de l'équipe <?php echo utf8_encode(htmlentities($site['name_team_first'])); ?></th>
					</tr>
					<tr class="topic">
						<td colspan="2" class="title span-23"><span style="font-weight:bold;color:black">Chef :</span><input style="margin-left:26px;width:85%" type="text" name="first[chef]" value="<?php echo @utf8_encode(htmlentities(get_login($team['first']['chef']))); ?>" /></td>
					</tr>
					<tr class="topic">
						<td colspan="2" class="title span-23">
							<span style="font-weight:bold;color:black">Sous-chefs :</span><br/>
							<textarea style="width:98%;height:auto;font-family:MS Shell Dlg;font-size:13px" rows="3" name="first[sous-chef]"><?php echo @utf8_encode(htmlentities($team['first']['sous-chef'])); ?></textarea>
						</td>
					</tr>
					<tr class="topic">
						<td colspan="2" class="title span-23">
							<span style="font-weight:bold;color:black">Membres :</span><br/>
							<textarea style="width:98%;height:auto;font-family:MS Shell Dlg;font-size:13px" rows="10" name="first[membre]"><?php echo @utf8_encode(htmlentities($team['first']['membre'])); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="span-12 last">
			<table class="list posts">
				<tbody>
					<tr>
						<th colspan="2" class="post span-2 last">Composition de l'équipe <?php echo utf8_encode(htmlentities($site['name_team_second'])); ?></th>
					</tr>
					<tr class="topic">
						<td colspan="2" class="title span-23"><span style="font-weight:bold;color:black">Chef :</span><input style="margin-left:26px;width:85%" type="text" name="second[chef]" value="<?php echo @utf8_encode(htmlentities(get_login($team['second']['chef']))); ?>" /></td>
					</tr>
					<tr class="topic">
						<td colspan="2" class="title span-23">
							<span style="font-weight:bold;color:black">Sous-chefs :</span><br/>
							<textarea style="width:98%;height:auto;font-family:MS Shell Dlg;font-size:13px" rows="3" name="second[sous-chef]"><?php echo @utf8_encode(htmlentities($team['second']['sous-chef'])); ?></textarea>
						</td>
					</tr>
					<tr class="topic">
						<td colspan="2" class="title span-23">
							<span style="font-weight:bold;color:black">Membres :</span><br/>
							<textarea style="width:98%;height:auto;font-family:MS Shell Dlg;font-size:13px" rows="10" name="second[membre]"><?php echo @utf8_encode(htmlentities($team['second']['membre'])); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			<input style="float:right" type="submit" value="Mettre à jour" />
		</div>
		<input type="hidden" name="composition_team" />
	</form>
    <?php
}

function page_games() {
	if (!check_admin($_SESSION['id']))
		return;
	global $db;
	$games = $db->query('SELECT * FROM game ORDER BY name');


	$error = array(
		'name'    		=> false,
		'image'   		=> false,
		'link_dl' 		=> false,
		'description' 	=> false,
		'type'	  		=> false,
		'free' 			=> false,
	);

	$alert = '';

	if (count($_POST) > 0) {

		$_POST = array_map('trim', $_POST);
		$_POST = array_map('utf8_decode', $_POST);

		if (empty($_POST['name']))
			$error['name'] = true;
		if (empty($_POST['image']))
			$error['image'] = true;
		if (empty($_POST['link_dl']))
			$error['link_dl'] = true;
		if (empty($_POST['description']))
			$error['description'] = true;
		if (empty($_POST['type']))
			$error['type'] = true;
		if (!isset($_POST['free']))
			$error['free'] = true;

		if (!check_errors($error)) {

			if (!isset($_POST['id'])) { // ADD
				$req = $db->prepare('INSERT INTO game (name, image, link_dl, free, type, description) VALUES (:name, :image, :link_dl, :free, :type, :description)');
				$req->execute($_POST);
			}
			else {						// EDIT
				$req = $db->prepare('UPDATE game SET name=:name, image=:image, link_dl=:link_dl, free=:free, type=:type, description=:description WHERE id = :id');
				$req->execute($_POST);
			}
			$games = $db->query('SELECT * FROM game ORDER BY name');
			$_POST = array();
		}
	}
	if (isset($_GET['remove']))
	{
		// check if used by a planet
		$nb = $db->query('SELECT COUNT(*) FROM planet WHERE id_game = '.intval($_GET['remove']))->fetchColumn();
		if ($nb > 0)
			$alert = '<script>alert("Impossible de supprimer ce jeu, il est utilisé sur une planète.");</script>';
		else {
			$a = $db->query('DELETE FROM game WHERE id = '.intval($_GET['remove']));
			header('location:./admin.php?page=games');
			die();
		}
	}

	if (!empty($alert))
		echo $alert;
	?>
	<br/>
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
						<td class="span-1 last" style="text-align:center">
							<a href="javascript:;" onclick="$('#game_<?php echo $game['id'];?>').css('display', 'table-row');">modifier</a><br/>
							<a href="./admin.php?page=games&remove=<?php echo $game['id']; ?>" onclick="return confirm('Êtes-vous sûr?');">supprimer</a>
						</td>
					</tr>
					<tr class="forum" id="game_<?php echo $game['id']; ?>" style="display:none">
						<td class="span-15 last" colspan="2">

							<form method="post">
							<table class="list forums">
								<tbody>
									<tr class="category">
										<th class="title" colspan="3">Modification du jeu</th>
									</tr>
									<tr class="forum">
										<td class="span-5">
											Nom du jeu :
										</td>
										<td class="span-19 last">
											<input type="text" name="name" value="<?php echo utf8_encode(htmlentities($game['name'])); ?>" size="100" maxlength="80" />
										</td>
									</tr>
									<tr class="forum">
										<td class="span-5">
											Image (background) :
										</td>
										<td class="span-19 last">
											<input type="text" name="image" value="<?php echo utf8_encode(htmlentities($game['image'])); ?>" size="100" maxlength="255" />
										</td>
									</tr>
									<tr class="forum">
										<td class="span-5">
											Lien d'achat/dl :
										</td>
										<td class="span-19 last">
											<input type="text" name="link_dl" value="<?php echo utf8_encode(htmlentities($game['link_dl'])); ?>" size="100" maxlength="255" />
										</td>
									</tr>
									<tr class="forum">
										<td class="span-5">
											Type de jeu:
										</td>
										<td class="span-19 last">
											<input type="text" name="type" value="<?php echo utf8_encode(htmlentities($game['type'])); ?>" size="100" maxlength="255" placeholder="FPS / Course / ..." />
										</td>
									</tr>
									<tr class="forum">
										<td class="span-5">
											Court texte :
										</td>
										<td class="span-19 last">
											<input type="text" name="description" value="<?php echo utf8_encode(htmlentities($game['description'])); ?>" size="100" maxlength="80" placeholder="Disponible sur Steam / Mod HL2 ..." />
										</td>
									</tr>
									<tr class="forum">
										<td class="span-5">
											Type de jeu :
										</td>
										<td class="span-19 last">
											<input type="radio" name="free" value="1" id="free"   <?php if ($game['free'] == "1") echo 'checked '; ?> /> <label for="free">Free To Play</label><br />
					       					<input type="radio" name="free" value="0" id="payant" <?php if ($game['free'] == "0") echo 'checked '; ?> /> <label for="payant">Payant</label><br />
										</td>
									</tr>
									<tr class="forum">
										<td class="span-5">
										</td>
										<td class="span-19 last" style="text-align:right">
											<input type="submit" value="Mettre à jour" />
										</td>
									</tr>
								</tbody>
							</table>
							<input type="hidden" name="id" value="<?php echo $game['id']; ?>" />
							</form>


						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>

		<form method="post">
		<table class="list forums">
			<tbody>
				<tr class="category">
					<th class="title" colspan="3">Ajouter un jeu</th>
				</tr>
				<tr class="forum">
					<td class="span-5" style="<?php if ($error['name']) echo 'color:red'; ?>">
						Nom du jeu :
					</td>
					<td class="span-19 last">
						<input type="text" name="name" value="<?php echo @htmlentities($_POST['name']); ?>" size="100" maxlength="80" />
					</td>
				</tr>
				<tr class="forum">
					<td class="span-5" style="<?php if ($error['image']) echo 'color:red'; ?>">
						Image (background) :
					</td>
					<td class="span-19 last">
						<input type="text" name="image" value="<?php echo @htmlentities($_POST['image']); ?>" size="100" maxlength="255" />
					</td>
				</tr>
				<tr class="forum">
					<td class="span-5" style="<?php if ($error['link_dl']) echo 'color:red'; ?>">
						Lien d'achat/dl :
					</td>
					<td class="span-19 last">
						<input type="text" name="link_dl" value="<?php echo @htmlentities($_POST['link_dl']); ?>" size="100" maxlength="255" />
					</td>
				</tr>
				<tr class="forum">
					<td class="span-5" style="<?php if ($error['type']) echo 'color:red'; ?>">
						Type de jeu:
					</td>
					<td class="span-19 last">
						<input type="text" name="type" value="<?php echo @htmlentities($_POST['type']); ?>" size="100" maxlength="255" placeholder="FPS / Course / ..." />
					</td>
				</tr>
				<tr class="forum">
					<td class="span-5" style="<?php if ($error['description']) echo 'color:red'; ?>">
						Court texte :
					</td>
					<td class="span-19 last">
						<input type="text" name="description" value="<?php echo @htmlentities($_POST['description']); ?>" size="100" maxlength="80" placeholder="Disponible sur Steam / Mod HL2 ..." />
					</td>
				</tr>
				<tr class="forum">
					<td class="span-5" style="<?php if ($error['free']) echo 'color:red'; ?>">
						Type de jeu :
					</td>
					<td class="span-19 last">
						<input type="radio" name="free" value="1" id="free"   /> <label for="free">Free To Play</label><br />
       					<input type="radio" name="free" value="0" id="payant" /> <label for="payant">Payant</label><br />
					</td>
				</tr>
				<tr class="forum">
					<td class="span-5">
					</td>
					<td class="span-19 last" style="text-align:right">
						<input type="submit" value="Ajouter" />
					</td>
				</tr>
			</tbody>
		</table>
		</form>
	</div>
	<?php
}

function page_planning() {
	echo 'Coming soon';
}

function page_heros() {
	if (!check_admin($_SESSION['id']))
		return;
	global $db;

	$heros = $db->query('SELECT * FROM hero ORDER BY name');

	$error = array(
		'name'    		=> false,
		'story'   		=> false,
		'image'			=> false
	);

	if (count($_POST) > 0) {

		$_POST = array_map('trim', $_POST);
//		$_POST = array_map('utf8_decode', $_POST);


		if (empty($_POST['name']))
			$error['name'] = true;
		if (empty($_POST['image']) && !isset($_POST['id']))
			$error['image'] = true;
		else if ((substr($_POST['image'], 0, strlen('http://')) != 'http://' && !isset($_POST['id'])) || (!empty($_POST['image']) && substr($_POST['image'], 0, strlen('http://')) != 'http://'))
			$error['image'] = true;
		if (empty($_POST['story']))
			$error['story'] = true;

		if (!check_errors($error)) {

			if (!isset($_POST['id'])) { // ADD
				$req = $db->prepare('INSERT INTO hero (name, story) VALUES (:name, :story)');
				$req->execute(array('name'=>$_POST['name'], 'story'=>$_POST['story']));
				$id = $req->fetch(PDO::FETCH_ASSOC);
				// image to do
			}
			else {						// EDIT
				$req = $db->prepare('UPDATE hero SET name=:name, story=:story WHERE id = :id');
				$req->execute(array('id'=>$_POST['id'], 'name'=>$_POST['name'], 'story'=>$_POST['story']));
			}
			$heros = $db->query('SELECT * FROM hero ORDER BY name');
			unset($_POST);
		}
	}
	if (isset($_GET['remove']))
	{
		// check if used by a planet
		/*
		$nb = $db->query('SELECT COUNT(*) FROM planet WHERE id_game = '.intval($_GET['remove']))->fetchColumn();
		if ($nb > 0)
			$alert = '<script>alert("Impossible de supprimer ce jeu, il est utilisé sur une planète.");</script>';
		else {
			$a = $db->query('DELETE FROM game WHERE id = '.intval($_GET['remove']));
			header('location:./admin.php?page=games');
			die();
		}
		*/
	}

	if (!empty($alert))
		echo $alert;
	?>
	<br/>
	<div class="span-24">
		<table class="list forums">
			<tbody>
				<tr class="category">
					<th class="title" colspan="3">Liste des heros</th>
				</tr>
				<?php
				foreach ($heros AS $hero) {
					?>
					<tr class="forum">
						<td>
							<img src="<?php echo $baseUrl; ?>/image/jacques-chirac-risque-150x150/150x150/110.jpg" />
						</td>
						<td style="vertical-align:top;">
							<div class="title">
								<a href="#"><?php echo utf8_encode(htmlentities($hero['name'])); ?></a>
							</div>
							<div class="description">
								<?php echo utf8_encode(htmlentities($hero['story'])); ?>
							</div>
						</td>
						<td style="text-align:center">
							<a href="javascript:;" onclick="$('#hero_<?php echo $hero['id'];?>').css('display', 'table-row');">modifier</a><br/>
							<a href="./admin.php?page=hero&remove=<?php echo $hero['id']; ?>" onclick="return confirm('Êtes-vous sûr?');">supprimer</a>
						</td>
					</tr>
					<tr id="hero_<?php echo $hero['id']; ?>" style="display:none">
						<td colspan="3">
						<form method="post">
						<table class="list forums">
							<tbody>
								<tr class="forum">
									<td class="span-5" style="<?php if ($error['name']) echo 'color:red'; ?>">
										Nom du hero :
									</td>
									<td class="span-19 last">
										<input type="text" name="name" value="<?php echo utf8_encode(htmlentities($hero['name'])); ?>" size="100" maxlength="80" />
									</td>
								</tr>
								<tr class="forum">
									<td class="span-5" style="<?php if ($error['image']) echo 'color:red'; ?>">
										Image (lien) :
									</td>
									<td class="span-19 last">
										<input type="text" name="image" value="" size="100" maxlength="255" placeholder="http://lien.vers.l.image.png" />
									</td>
								</tr>
								<tr class="forum">
									<td class="span-5" style="vertical-align:top;<?php if ($error['story']) echo 'color:red'; ?>">
										Description :
									</td>
									<td class="span-19 last">
										<textarea name="story" rows="3" cols="88" style="height:auto;width:auto"><?php echo utf8_encode(htmlentities($hero['story'])); ?></textarea>
									</td>
								</tr>
								<tr class="forum">
									<td class="span-5">
									</td>
									<td class="span-19 last" style="text-align:right">
										<input type="submit" value="Mettre à jour" />
									</td>
								</tr>
							</tbody>
						</table>
						<input type="hidden" name="id" value="<?php echo $hero['id']; ?>" />
						</form>
					</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>

		<form method="post">
		<table class="list forums">
			<tbody>
				<tr class="category">
					<th class="title" colspan="3">Ajouter un hero</th>
				</tr>
				<tr class="forum">
					<td class="span-5" style="<?php if ($error['name']) echo 'color:red'; ?>">
						Nom du hero :
					</td>
					<td class="span-19 last">
						<input type="text" name="name" value="<?php echo @htmlentities($_POST['name']); ?>" size="100" maxlength="80" />
					</td>
				</tr>
				<tr class="forum">
					<td class="span-5" style="<?php if ($error['image']) echo 'color:red'; ?>">
						Image (lien) :
					</td>
					<td class="span-19 last">
						<input type="text" name="image" value="<?php echo @htmlentities($_POST['image']); ?>" size="100" maxlength="255" placeholder="http://lien.vers.l.image.png" />
					</td>
				</tr>
				<tr class="forum">
					<td class="span-5" style="vertical-align:top;<?php if ($error['story']) echo 'color:red'; ?>">
						Description :
					</td>
					<td class="span-19 last">
						<textarea name="story" rows="3" cols="88" style="height:auto;width:auto"><?php echo @htmlentities($_POST['story']); ?></textarea>
					</td>
				</tr>
				<tr class="forum">
					<td class="span-5">
					</td>
					<td class="span-19 last" style="text-align:right">
						<input type="submit" value="Ajouter" />
					</td>
				</tr>
			</tbody>
		</table>
		</form>
	</div>
	<?php
}
