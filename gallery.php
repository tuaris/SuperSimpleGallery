<html>
	<head>
		<title>Sample Gallery</title>
	<script>

	</script>
	</head>

	<body>
		<ul id="gallery">

			<?php 
			$files = scandir(".");
			?>

			<?php foreach ($files as $aFile) {?>
				<?php if (substr($aFile, -4) == '.jpg') { ?>
			  <li><a href="<?php echo "$aFile"; ?>"><img src="thumb/<?php echo "$aFile"; ?>" width="100" height="80" /></a></li>
				<?php } ?>
			<?php } ?>
		</ul>
	</body>
</html>