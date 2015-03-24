<!doctype html> 
<html lang="en">
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex,nofollow">
    <title>pull2rsync - Request Token</title>
	<link rel="stylesheet" href="<?php echo assets('/assets/style.css') ?>">
</head>
<body>
<div class="container">
	<h2>rsync request</h2>
	<pre><?php echo $rsync_info ?>
	

# response
<?php echo $response ?>
	</pre>

	<?php if(stristr($response, 'rsync error')): ?>
	<div class="warning">
		<?php echo $message['rsync_error'] ?>
	</div>
	<?php else: ?>
	<form method="post">
		<?php if($isTokenExists === TRUE): ?>
			<input type="text" name="validate_token" class="text" placeholder="Paste token here">
			<input type="submit" name="validate" value="validate token" class="btn" />
			<?php echo $message['token_exists']?>
		<?php else: ?>
			<input type="submit" name="request_token" value="request token" class="btn" />
		<?php endif; ?>
	</form>
	<?php endif; ?>
	<?php if(showNotification()): ?>
		<div class="message"><?php echo showNotification() ?></div>
	<?php endif; ?>
</div>
</body>
</html>
