<?php
require './config/bootstrap.php';

head();
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
							Comming soon
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
