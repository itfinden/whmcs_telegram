<?php
function sendTelegramMessage($pm) {
	global $vars;
	$application_chatid = mysql_fetch_array( select_query('tbladdonmodules', 'value', array('module' => 'wt_note', 'setting' => 'chatid') ), MYSQL_ASSOC );
	$application_botkey = mysql_fetch_array( select_query('tbladdonmodules', 'value', array('module' => 'wt_note', 'setting' => 'key') ), MYSQL_ASSOC );
	$chat_id 		= $application_chatid['value'];
	$botToken 		= $application_botkey['value'];

	$data = array(
		'chat_id' 	=> $chat_id,
		'text' 		=> $pm . "\n\n-------------n" . base64_decode("V0hNQ1MgSXRGaW5kZW4=")
	);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://api.telegram.org/bot$botToken/sendMessage");
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_exec($curl);
	curl_close($curl);
}

function wt_note_ClientAdd($vars) {
	global $customadminpath, $CONFIG;
	sendTelegramMessage("Un nuevo usuario ha iniciado sesión\n------------- \n\n". $CONFIG['SystemURL'].'/'.$customadminpath.'/clientssummary.php?userid='.$vars['userid']);
}

function wt_note_InvoicePaid($vars) {
	global $customadminpath, $CONFIG;
	sendTelegramMessage("Se pagó una factura :\n------------- \n\n N_Factura : $vars[invoiceid] \n\n Cantidad : $vars[total] \n\n". $CONFIG['SystemURL'].'/'.$customadminpath.'/invoices.php?action=edit&id='.$vars['invoiceid']);
}

function wt_note_TicketOpen($vars) {
	global $customadminpath, $CONFIG;
	sendTelegramMessage("Se creó un nuevo ticket :\n------------- \n\n ID de entrada : $vars[ticketid] \n\n Departamento : $vars[deptname] \n\n Titulo de entrada : $vars[subject] \n\n". $CONFIG['SystemURL'].'/'.$customadminpath.'/supporttickets.php?action=viewticket&id='.$vars['ticketid']);
}

function wt_note_TicketUserReply($vars) {
	global $customadminpath, $CONFIG;
	sendTelegramMessage("La nueva respuesta al TICKET :\n------------- \n\n ID de entrada : $vars[ticketid] \n\n Departamento : $vars[deptname] \n\n Asunto : $vars[subject] \n\n". $CONFIG['SystemURL'].'/'.$customadminpath.'/supporttickets.php?action=viewticket&id='.$vars['ticketid'], $application_botkey, $application_chatid);

}

add_hook("ClientAdd",1,"wt_note_ClientAdd");
add_hook("InvoicePaid",1,"wt_note_InvoicePaid");
add_hook("TicketOpen",1,"wt_note_TicketOpen");
add_hook("TicketUserReply",1,"wt_note_TicketUserReply");
