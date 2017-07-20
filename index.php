<?php
require './config/bootstrap.php';

head();
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
								<div class="breadcrumb"><a href="./"><?php echo utf8_encode(htmlentities($site['name'])); ?></a> » <a href="./"><?php echo utf8_encode(htmlentities($site['description'])); ?></a></div>
							</div>
							<div class="actions span-4 last">
								<a title="Forum sur Wefrag" class="reply" href="<?php echo utf8_encode(htmlentities($site['forum'])); ?>">Forum</a>
							</div>
						</div>
						<div class="span-24">
							<table class="list posts">
								<tbody>
									<tr>
										<th class="post span-2 last">Histoire<span class="spent_time"></span></th>
									</tr>
									<tr class="post odd">
										<td class="content span-24 last">
											<div class="body">
												<?php echo bbcode(nl2br(utf8_encode(htmlentities($site['backstory'])))); ?>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="span-24">
							<table class="list posts">
								<tbody>
									<tr>
										<th class="post span-2 last">Règles du jeu<span class="spent_time"></span></th>
									</tr>
									<tr class="post odd">
										<td class="content span-24 last">
											<div class="body">
												<?php echo bbcode(nl2br(utf8_encode(htmlentities($site['rules'])))); ?>
											</div>
										</td>
									</tr>
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
