<?php
/**
 * rsync.php
 *
 * request token and rsync (--dry-out first)
 *
 * @author agoes
 * @version 1.0
 * @copyright agoes, 19 March, 2015
 **/

if (function_exists('shell_exec')) {
	$cd = "cd " . $repo->staging->document_root;
	$suffix = $config['command']['_suffix'];

	// load exluded file / dir
	$deploy = json_decode(file_get_contents($deploy));
	$exclude = $config['rsync']['exclude'];
	if (isset($deploy->rsync_exclude)) {
		$exclude = array_merge($exclude, $deploy->rsync_exclude);
	}
	$exclude = '--exclude \'' . implode('\' --exclude ', $exclude) . '\'';

	// public directories or files
	$chmod = '';
	if (isset($deploy->writeable)) {
		$chmod = '&& chmod -R 777 ' . implode(' ', $deploy->writeable);
	}

	// dry run first
	$opt = '--dry-run ' . $exclude;

	// rsync command
	$rsync = $cd . ' && rsync -azv ' . trim($opt) . ' * ' . $repo->production->auth . ':' . $repo->production->document_root . '/ ' . $suffix;
	
	// hide path and production auth
	$rsync_masked = 'cd /staging/path/of/<b>' . $repo->name . '</b> && rsync -azv <b>' . trim($opt) . ' *</b>  user@production-host:/production/path/of/<b>' . $repo->name . '/ ' . $chmod . '</b> ' . $suffix;
	$response = shell_exec($rsync);


	if ($config['rsync']['hide_information'] === TRUE) {
		$rsync_info = $rsync_masked;
	} else {
		$rsync_info = $rsync;
	}

	$isTokenExists = FALSE;

	if (is_writeable($config['rsync']['token_path'])) {
		$tokenFile = $config['rsync']['token_path'] . '/' . $_GET['id'] . '.json';
		if (file_exists($tokenFile)) {
			// read file and check expire
			$currentToken = json_decode(file_get_contents($tokenFile));

			if ($currentToken->expire <= time()) {
				// delete old token
				unlink($tokenFile);

				// remove notification (if exists)
				purgeNotification();
			} else {
				// token still exists
				$isTokenExists = TRUE;

				// validate token input
				if (isset($_POST['validate_token'])) {
					if ($_POST['validate_token'] === $currentToken->token) {
						
						// do rsync
						$rsync_cmd = shell_exec(str_replace('--dry-run', '', $rsync));
						writeLog($rsync . "\n" . $rsync_cmd);

						$emailBody = '<h3>' . $message['rsync_complete'] . ' ' . $message['rsync_thank_you'] . '</h3>'; 
						$emailBody .= '<pre>' . $rsync_masked . '
							' . $rsync_cmd . '
							</pre>';

						sendMail($repo->production->roles, $emailBody, TRUE);

						redirect($_GET['module'], $_GET['id'], '', $message['rsync_complete']);

					} else {
						// empty or invalid token
						redirect($_GET['module'], $_GET['id'], '', $message['invalid_rsync_token']);
					}
				}	
			}
		}

		// request new token
		if (isset($_POST['request_token'])) {

			// create new token ... 
			$tokenFile = fopen($tokenFile,'w');
			$token = sha1(time() . md5(rand()) . $_GET['id']);
			$tokenAuthor = array();
			foreach ($repo->production->roles as $i) {
				$tokenAuthor[] = $i->email;
			}

			$expired = time() + $config['rsync']['token_expire'];

			// set tokenfile body
			$tokenBody = json_encode(array(
				'token' => $token,
				'expire' => $expired,
				'owner' => $tokenAuthor,
				'rsync' => base64_encode($rsync),
				'response' => base64_encode($response),
			));

			// write token file
			fwrite($tokenFile,trim($tokenBody));
			fclose($tokenFile);

			// send mail after create token
			$emailBody = "<h3>\n" . $rsync_info . "</h3><pre>" . $response . "\n</pre>";
			$emailBody .= "<p>" . $message['request_rsync_token_body'] . " (Expired at : " . date('d M Y H:i:s', time() + $config['rsync']['token_expire']) . ")</p><h2><pre style=\"color: #ff0000\">Token : " . $token . "</pre></h2>";
			sendMail($repo->production->roles, $emailBody);

			// token page
			redirect($_GET['module'], $_GET['id']);
		}
	}
} else {
	// shell exec is not available
	writeLog($message['shell_exec_failed']);
}

// EOF
