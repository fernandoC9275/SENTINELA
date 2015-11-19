<?php
require_once("../../../config/config.php");
require_once("../../../classes/phpmailer/class.phpmailer.php");
require_once("../../../classes/phpmailer/class.smtp.php");
require_once("dados.php");
require_once("index.php");


$message ="
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
</head>
<body style='margin:0px; padding:0px;'>
<!--HEADER-->
<table cellpadding='0' cellspacing='0' border='0' width='100%' bgcolor='#FFFFFF' align='center'>
	<tr>
		<td>
			<table cellpadding='0' cellspacing='0' border='0' width='700px' align='center' style='width:700px;font-family: Verdana; font-size: 12px;line-height:160%'>
				
				<tr>
					<td colspan='5'>
						<img src='".$base_url."images/logo.png' style='display:block' />
					</td>
				</tr>
				<tr>
					<td height='10px' colspan='5'></td>
				</tr>
				
				<!-- FIM HEADER-->
				
				
				
				<!--DADOS-->
				<tr>
					<td style='padding: 15px; text-align:center; background-color:".$cor_box.";' colspan='5'>
						<h1>".$TX_OCUPACAO_HOJE."%</h1>
						Taxa de ocupação em: <br> ".$data."h
					</td>

				</tr>
				<tr>
					<td height='10px' colspan='5'></td>
				</tr>
				<tr>
					<td width='5px' style='5px'></td>
					<td>
						<h3>Data</h3>
					</td>
					
					
					<td width='10px' style='width:10px'></td>
					<td>
						<h3>Taxa de ocupação</h3>
						
					</td>
					<td>
						<h3>Status</h3>
						
					</td>
					<td width='5px' style='5px'></td>
				</tr>
				
				
				
				<tr>
					<td width='5px' style='5px'></td>
					<td>
						<p>".$data_ontem." 23:59h</p>
					</td>
					
					
					<td width='10px' style='width:10px'></td>
					<td>
						<p>".$TX_OCUPACAO_ONTEM."%</p>
						
					</td>
					<td>
						<p><img src='".$base_url."images/".$thumbs."' alt='' style='width:20px; height:20px;'></p>
						
					</td>
					<td width='5px' style='5px'></td>
				</tr>
				
				<tr>
					<td width='5px' style='5px'></td>
					<td>
						<p>".$data_ontem2." 23:59h</p>
					</td>
					
					
					<td width='10px' style='width:10px'></td>
					<td>
						<p>".$TX_OCUPACAO_ONTEM2."%</p>
						
					</td>
					<td>
						<p></p>
						
					</td>
					<td width='5px' style='5px'></td>
				</tr>
				
		
				<tr>
					<td height='10px' colspan='5'></td>
				</tr>
				
				<tr>
					<td style='padding:15px; text-align:center;' colspan='5'>
						Dados colhidos pela portaria nº 312, de 30 de abril de 2002.
					</td>
				</tr>
				
				<!-- FIM DADOS-->
				
				
				<!--FOOTER-->
				<tr>
					<td height='10px' colspan='5'></td>
				</tr>
				
				<tr>
					<td style='padding: 100px; text-align:center;' colspan='5'>
						Desenvolvido pela T.I - CSSJ
					</td>
				</tr>
				
			</table>
		</td>
	</tr>
</table>
			<!--FIM FOOTER-->
</body>
</html>
";





$subject = $assuntoEmail;

$mailer = new PHPMailer();
$mailer->IsSMTP();
//$mailer->SMTPDebug = 1;
$mailer->Port = 587; //Indica a porta de conexão para a saída de e-mails
$mailer->CharSet = 'UTF-8';
$mailer->IsHTML(true);
$mailer->Host = 'smtp.office365.com';//Endereço do Host do SMTP Locaweb
$mailer->SMTPAuth = true; //define se haverá ou não autenticação no SMTP
$mailer->Username = 'sentinela@cssj.com.br'; //Login de autenticação do SMTP
$mailer->Password = 'SE2015la'; //Senha de autenticação do SMTP
$mailer->FromName = 'Sentinela - CSSJ'; //Nome que será exibido para o destinatário
$mailer->From = 'sentinela@cssj.com.br'; //Obrigatório ser a mesma caixa postal configurada no remetente do SMTP
foreach ($email as $e) {
   echo $mailer->AddAddress($e); 
}
$mailer->Subject = $subject;
$mailer->Body = $message;
$mailer->Send();

?>