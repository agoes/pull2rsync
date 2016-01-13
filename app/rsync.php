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

// check deploy.json
$deploy = $repo->staging->document_root . '/' . $config['rsync']['deploy_file'];
if (!file_exists($deploy)) {
	writeLog('[failed] ' . $message['no_deploy']);
	exit;
}

if (function_exists('shell_exec')) {

	$deploy = json_decode(file_get_contents($deploy));

	// public directories or files
	$chmod = '';
	if (isset($deploy->writeable) && count($deploy->writeable) > 0) {
        // set writeable directories / files
        foreach($deploy->writeable as $i) {
            if ($i !== '/') {
                $chmod = shell_exec($cd . " && chmod 777 " . $i);
                writeLog($chmod);
            }
        }

		$chmod = '&& chmod -R 777 ' . implode(' ', $deploy->writeable);
	}

	// load exluded file / dir
	$exclude = $config['rsync']['exclude'];
	if (isset($deploy->rsync_exclude)) {
		$exclude = array_merge($exclude, $deploy->rsync_exclude);
	}
	$exclude = '--exclude \'' . implode('\' --exclude \'', $exclude) . '\'';


	// dry run first
	$opt = '--dry-run ' . $exclude;

    // port to use rsync
    $port = " -e \"ssh -p ";
    if (isset($repo->production->port)) {
        $port = $port . $repo->production->port . "\"";
    } elseif(isset($config['rsync']['port'])) {
        $port = $port . $config['rsync']['port'] . "\"";
    } else {
        $port = $port . 22 . "\"";
    }

	// rsync command
	$rsync = $cd . ' && rsync -azv ' . trim($opt) . ' * ' . $repo->production->auth . ':' . $repo->production->document_root . '/ ' . $port . ' ' . $suffix;
	$rsync = $cd . ' && rsync -azv ' . trim($opt) . ' . ' . $repo->production->auth . ':' . $repo->production->document_root . '/ ' . $suffix;
	// hide path and production auth
	$rsync_masked = 'cd /staging/path/of/<b>' . $repo->name . '</b> && rsync -azv <b>' . trim($opt) . ' .</b>  user@production-host:/production/path/of/<b>' . $repo->name . '/ ' . $chmod . '</b> ' . $suffix;
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

			// reject token
			if (isset($_GET['act']) && isset($_GET['token'])) {
				if ($_GET['act'] === 'reject' && $_GET['token'] === $currentToken->token) {
					$emailBody = '<h3>' . $message['rsync_rejected'] . '</h3>';
					$emailBody .= '<pre>' . $rsync_masked . '
						' . $rsync_cmd . '
						</pre>';

					unlink($tokenFile);
					sendMail($repo->production->roles, $emailBody, TRUE);
					redirect($_GET['module'], $_GET['id'],'', $message['rsync_rejected']);
				}
			} elseif ($currentToken->expire <= time()) {
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

						$emailBody = '<h3>' . $message['rsync_complete'] . ' ' . $message['rsync_thank_you'] . '</h3>'; $emailBody .= '<pre>' . $rsync_masked . '
							' . $rsync_cmd . '
							</pre>';

						// succeeded
						unlink($tokenFile);
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
			$emailBody .= 'Reject rsync : ' . baseURL() . '/index.php?module=' . $_GET['module'] . '&id=' . $_GET['id'] . '&act=reject&token=' . $token;
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
