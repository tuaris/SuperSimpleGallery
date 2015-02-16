<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

$realm = 'Gallery Edit';
$users = array('admin' => '123456', 'user1' => '123456');

include 'lib/functions.inc.php';

if (!authenticate($users, $realm)){
	exit;
}

if(isset($_REQUEST['op'])){
	$operation = $_REQUEST['op'];

	if($operation == 'move'){
		move_image($_REQUEST['image'], $_REQUEST['by']);
	}
	if($operation == 'delete'){
		delete_image($_REQUEST['image']);
	}
	if($operation == 'restore'){
		restore_image($_REQUEST['image']);
	}
	if($operation == 'upload'){
		upload_image();
	}

	// Clear the operation if done
	if(!empty($operation)){
		header('location: ' . $_SERVER['PHP_SELF']);
	}
}

?>
<html>
<head>
<title>Edit Galerry</title>
<link rel="stylesheet" type="text/css" href="lib/style.css" media="screen">
</head>

<body>

	<h2>Upload new Image</h2>
	<form enctype="multipart/form-data" action="?op=upload" method="POST">
		<!-- MAX_FILE_SIZE must precede the file input field -->
		<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
		<!-- Name of input element determines name in $_FILES array -->
		Select file: <input name="upfile" type="file" />
		<input type="submit" value="Save Image" />
	</form>

	<h2>Current Active Images</h2>
	<div id="images">
		<ul id="images-list" class="image-list">

			<?php 
			$files = scandir(".");
			?>

			<?php foreach ($files as $aFile) {?>
				<?php if (substr($aFile, -3) == 'jpg') { ?>
				<?php 

				?>
				<li>
				<h3><?php echo getNumber($aFile); ?></h3>
				<div><a href="<?php echo "$aFile"; ?>"><img src="thumb/<?php echo "$aFile"; ?>?w=200&&h=120" width="200" height="160" /></a></div>

				<div id="fileOPS">
					<span class="file-op"><a href="?op=delete&&image=<?php echo $aFile; ?>" title="Delete"><img src="lib/trashcan_empty_alt.png" alt="Delete"/></a></span>
					<span class="move-op"><a href="?op=move&&image=<?php echo $aFile; ?>&&by=-5" title="Move Left by 5 Spaces"><img src="lib/arrow_left_double.png" alt="-5"/></a></span>
					<span class="move-op"><a href="?op=move&&image=<?php echo $aFile; ?>&&by=-1" title="Move Left by 1 Space"><img src="lib/arrow_left.png" alt="-1"/></a></span>
					<span class="move-op"><a href="?op=move&&image=<?php echo $aFile; ?>&&by=1" title="Move Right by 1 Space"><img src="lib/arrow_right.png" alt="+1"/></a></span>
					<span class="move-op"><a href="?op=move&&image=<?php echo $aFile; ?>&&by=5" title="Move Right by 5 Spaces"><img src="lib/arrow_right_double.png" alt="+5"/></a></span>  
				</div>
				</li>
				<?php } ?>
			<?php } ?>
		</ul>
	</div>

	<br clear="all" />

	<h2>Deleted Items</h2>
	<div id="deleted-images">
		<ul id="deleted-images-list" class="image-list">

			<?php 
			$files = scandir(".");
			?>

			<?php foreach ($files as $aFile) {?>
				<?php if (substr($aFile, -8) == '.deleted') { ?>
				<?php 

				?>
				<li>
				<h3>X<?php echo getNumber($aFile); ?></h3>
				<div><img src="thumb/<?php echo "$aFile"; ?>?w=100&&h=70" width="100" height="70" /></div>
				<div>
				<span class="file-op"><a href="?op=restore&&image=<?php echo $aFile; ?>" title="Restore"><img src="lib/system_restore.png" alt="Restore"/></a></span>
				</div>
				</li>
				<?php } ?>
			<?php } ?>
		</ul>
	</div>

</body>
</html>