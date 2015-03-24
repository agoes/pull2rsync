<?php if(!defined('ALLOWED')) die('Error');
/**
 * Common used functions
 *
 * @author agoes
 * @version 1.0
 * @copyright agoes, 17 March, 2015
 **/

/**
 * Create & write log 
 *
 * @param string 
 * @param bool 
 * @return void
 * @author agoes
 **/
function writeLog($message, $printResponse = FALSE)
{
	global $config;

	// log directory is not exists or not writeable
	$log = $config['log']['path'] . '/' . $_GET['id'] . '.log';
	if (!is_writeable($config['log']['path'] . '/')) {
		die($log . ' is not writeable');
	} 

	// write
	$log = fopen($log,'a');
	fwrite($log,date('[Y/m/d H:i:s]') . " " . trim($_SERVER['REQUEST_URI']. "\n " . $message) ."\n" . "===============" . "\n");
	fclose($log);

	// print error to browser
	if ($printResponse === TRUE) {
		die($message);
	}
}

/**
 * Clean log file
 *
 * @return void
 * @author agoes
 **/
function purgeLog()
{
	global $config;

	// log directory is not exists or not writeable
	$log = $config['log']['path'] . '/' . $_GET['id'] . '.log';
	if (!file_exists($config['log']['path'] . '/')) {
		die($log . ' not found');
	} 

	// write
	$log = fopen($log,'w');
	fwrite($log,date('[Y/m/d H:i:s]') . " " . trim($message) ."\n");
	fclose($log);
}

/**
 * Send mail via phpmailer
 *
 * https://github.com/PHPMailer/PHPMailer
 *
 * @param array
 * @param string
 * @param bool
 * @return void
 **/
function sendMail($recipients, $body, $sendToAll = FALSE)
{
	global $config;
	global $language;

	$message = $language[$config['language']];
	$mail = new PHPMailer;

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->SMTPDebug = 0;
	$mail->Host = $config['smtp']['host'];  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->SMTPKeepAlive = true;
	$mail->CharSet = 'utf-8';

	$mail->Username = $config['smtp']['username'];                 // SMTP username
	$mail->Password = $config['smtp']['password'];                           // SMTP password
	$mail->SMTPSecure = $config['smtp']['secure'];                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = $config['smtp']['port'];                                    // TCP port to connect to

	$mail->From = $config['smtp']['from'];
	$mail->FromName = $config['smtp']['from_name'] ;

	foreach ($recipients as $i) {
		if ($sendToAll === TRUE) {
			$mail->addAddress($i->email, $i->name);               // Name is optional
		} else {
			if (in_array($i->role, $config['rsync']['token_role'], TRUE)) {
				$mail->addAddress($i->email, $i->name);               // Name is optional
			} 
		}

		// reply to
		$mail->addReplyTo($i->email, $i->name);               // Name is optional

	}

	$mail->isHTML(TRUE);                                  // Set email format to HTML

	$mail->Subject = '[' . $message['request_rsync_token'] . '] ' . $_GET['id']; 
	$mail->Body    = $body;

	// send
	$mail->send();
}

/**
 * Get base url
 *
 * @return string
 **/
function baseURL()
{
	return ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ) ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
}

/**
 * get assets url (based on baseurl)
 *
 * @param string
 * @return string
 * @author agoes
 **/
function assets($path)
{
	return baseURL() . $path;
}

/**
 * redirect to module
 *
 * @param string
 * @param string
 * @return void
 * @author agoes
 **/
function redirect($module, $id, $queryString = '', $flashMessage = '')
{
	if ($flashMessage !== '') {
		$_SESSION['notification'] = array(
			'message' => $flashMessage,
			'expire' => time() + 1,
		);
	}

	header('location:' . baseURL() . '/index.php?module=' . $module . '&id=' . $id . $queryString);
}

/**
 * Show flash data
 *
 * @param string
 * @return string
 * @author agoes
 **/
function showNotification()
{
	if (isset($_SESSION['notification']) && $_SESSION['notification']['expire'] >= time()) {
		return $_SESSION['notification']['message'];
	} else {
		purgeNotification();
		return FALSE;
	}
}

/**
 * Remove all notifications
 *
 * @return void
 * @author agoes 
 **/
function purgeNotification()
{
	if (isset($_SESSION['notification'])) {
		unset($_SESSION['notification']);
	}
}

// EOF
