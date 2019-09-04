<?php

function itfinden_log($log_msg)
{
    $log_filename = "/home/itfinden/customer.itfinden.com/modules/addons/wt_note/itfinden_log";
    if (!file_exists($log_filename)) 
    {
        // create directory/folder uploads.
        mkdir($log_filename, 0777, true);
    }
    
    $log_file_data = $log_filename.'/log_' . date('d-M-Y') . '.log';
    // if you don't add `FILE_APPEND`, the file will be erased each time you add a log
    file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
}
function itfinden_dump($log_msg)
{
    $log_filename = "/home/itfinden/customer.itfinden.com/modules/addons/wt_note/itfinden_dump";
    
    $log_msg=var_export($log_msg,TRUE) 
	
    if (!file_exists($log_filename)) 
    {
        // create directory/folder uploads.
        mkdir($log_filename, 0777, true);
    }
    
    $log_file_data = $log_filename.'/dump_' . date('d-M-Y') . '.log';
    // if you don't add `FILE_APPEND`, the file will be erased each time you add a log
    file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
}



function sendTelegramMessage($pm) {
	global $vars;
	$application_chatid = mysql_fetch_array( select_query('tbladdonmodules', 'value', array('module' => 'wt_note', 'setting' => 'chatid') ), MYSQL_ASSOC );
	$application_botkey = mysql_fetch_array( select_query('tbladdonmodules', 'value', array('module' => 'wt_note', 'setting' => 'key') ), MYSQL_ASSOC );
	$chat_id 		= $application_chatid['value'];
	$botToken 		= $application_botkey['value'];

	$data = array(
		'chat_id' 	=> $chat_id,
		'text' 		=> PHP_EOL. $pm . PHP_EOL."-------------" . PHP_EOL. base64_decode("V0hNQ1MgSXRGaW5kZW4=")
	);
    
    itfinden_log($pm);
    
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://api.telegram.org/bot".$botToken."/sendMessage");
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_exec($curl);
	
	if(!curl_errno($curl))
        {
         $info = curl_getinfo($curl);
         itfinden_log ('Tiempo ' . $info['total_time'] . ' URL :  ' . $info['url']);
        }
        
        // Close handle
	
	curl_close($curl);

}

function Gen_Message($vars){
    
    $vars=str_replace('<BREAKLINE>',PHP_EOL,$vars);
    sendTelegramMessage($vars);
}


function itfinden_ClientAdd($vars) {
	global $customadminpath, $CONFIG;
	Gen_Message("Un nuevo usuario ha iniciado sesion :<BREAKLINE> ------------------------------------- <BREAKLINE>". $CONFIG['SystemURL'].'/'.$customadminpath.'/clientssummary.php?userid='.$vars['userid']);
}

function itfinden_InvoicePaid($vars) {
	global $customadminpath, $CONFIG;
	Gen_Message("Se pago una factura :<BREAKLINE> ------------------- <BREAKLINE> N_Factura : $vars[invoiceid] <BREAKLINE> Cantidad : $vars[total] <BREAKLINE>". $CONFIG['SystemURL'].'/'.$customadminpath.'/invoices.php?action=edit&id='.$vars['invoiceid']);
}

function itfinden_TicketOpen($vars) {
	global $customadminpath, $CONFIG;
	Gen_Message("Se creo un nuevo ticket :<BREAKLINE> ----------------------- <BREAKLINE> ID de entrada : $vars[ticketid] <BREAKLINE> Departamento : $vars[deptname] <BREAKLINE> Titulo de entrada : $vars[subject] <BREAKLINE>". $CONFIG['SystemURL'].'/'.$customadminpath.'/supporttickets.php?action=viewticket&id='.$vars['ticketid']);
}

function itfinden__TicketUserReply($vars) {
	global $customadminpath, $CONFIG;
	Gen_Message("La nueva respuesta al TICKET :<BREAKLINE> ----------------------------- <BREAKLINE> ID de entrada : $vars[ticketid] <BREAKLINE> Departamento : $vars[deptname] <BREAKLINE> Asunto : $vars[subject] <BREAKLINE>". $CONFIG['SystemURL'].'/'.$customadminpath.'/supporttickets.php?action=viewticket&id='.$vars['ticketid'], $application_botkey, $application_chatid);

}
function itfinden_admin_notificate($vars) {
	global $customadminpath, $CONFIG;
	$ip=$_SERVER['REMOTE_ADDR'];
	Gen_Message("Inicio de Session :<BREAKLINE> ----------------------------- <BREAKLINE> El $vars[admin_username] ha iniciado session <BREAKLINE> desde la ip $ip");

}
function itfinden_AcceptQuote($vars) {
	global $customadminpath, $CONFIG;
	$ip=$_SERVER['REMOTE_ADDR'];
	itfinden_dump($vars);
	Gen_Message("La Cotizacion :<BREAKLINE> ----------------------------- <BREAKLINE> El $vars[admin_username] ha iniciado session <BREAKLINE> desde la ip $ip");

}
function itfinden_AddInvoicePayment($vars) {
	global $customadminpath, $CONFIG;
	$ip=$_SERVER['REMOTE_ADDR'];
	itfinden_dump($vars);
	Gen_Message("se agrego pago a factura :<BREAKLINE> ----------------------------- <BREAKLINE> El $vars[admin_username] ha iniciado session <BREAKLINE> desde la ip $ip");

}

add_hook("AdminLogin", 1, "itfinden_admin_notificate");
add_hook("ClientAdd",1,"itfinden_ClientAdd");
add_hook("InvoicePaid",1,"itfinden_InvoicePaid");
add_hook("TicketOpen",1,"itfinden_TicketOpen");
add_hook("TicketUserReply",1,"itfinden_TicketUserReply");
#add_hook("AcceptQuote",1,"itfinden_AcceptQuote");
#add_hook("AddInvoicePayment",1,"itfinden_AddInvoicePayment");

