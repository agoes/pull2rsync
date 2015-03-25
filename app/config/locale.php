<?php if(!defined('ALLOWED')) die('Error');
/**
 * Locale configuration
 *
 * @author agoes
 * @version 1.0
 * @copyright agoes, 17 March, 2015
 **/

$language['en'] = array(
	'invalid_host' => 'Git repository host %s is not allowed.',
	'shell_exec_failed' => 'shell_exec is not available or not allowed this time.',
	'webhook_denied' => 'Webhook host is not allowed',
	'no_deploy' => 'deploy.json not found',
	'request_rsync_token' => 'rsync token request',
	'request_rsync_token_body' => 'Rsync has been requested, please use the token code below.',
	'rsync_error' => 'Hmm .. Something wrong with the rsync response, please contact your administrator.',
	'token_exists' => '<h3>Token has been sent to the authorized group in this repo.</h3> <ul>
		<li>If you\'re the ' . implode(', ', $config['rsync']['token_role']) . ' of the project, go check your inbox. We already sent you the token</li>
		<li>If you\'re not authorized yet to be sent, please contact your ' . implode(' or ', $config['rsync']['token_role']) . '</li>
		<li>If you (feel) authorized but still got no email about the token. Please contact the administrator</li>
	</u>',
	'rsync_complete' => 'rsync was beautifully done .. ',
	'invalid_rsync_token' => 'rsync failed, token may be incorrect',
	'rsync_thank_you' => 'Thank you for using pull2rsync'
);
