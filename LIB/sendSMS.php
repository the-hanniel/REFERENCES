<?php
require_once"../../LIB/phpmailler.php";
class SendNewEmail extends PHPMailer {
	public function __construct($data = null){
		if($data == NULL) return null;
		$SMTPDebug = 1;
		PHPMailer::IsSMTP(); // telling the class to use SMTP
		$Host       = "ssl://smtp.gmail.com"; // SMTP server
		$SMTPAuth   = true;                  // enable SMTP authentication
		$Port       = 465;          // set the SMTP port for the GMAIL server; 465 for ssl and 587 for tls
		$Username   = $data['USERNAME'];//"ruberandindap@gmail.com"; // Gmail account username
		$Password   = $data['PASSWORD'];//'PV1f$ael';        // Gmail account password

		PHPMailer::SetFrom($data['FROMADDRESS'],$data['FROMNAME']);//'ruberandindap@gmail.com', 'INYANGE'); //set from name

		$Subject = $data['SUBJECT'];
		PHPMailer::MsgHTML($data['MSG']);

		PHPMailer::AddAddress($data['TOADDRESS'],$data['TONAME']);//"00250726227394@sms.tigo.com.co", "To MADINE DUKUZUMUREMYI");

		if(!PHPMailer::Send()) {
			return false;
		} else {
		  return true;
		}
	}
}
?>