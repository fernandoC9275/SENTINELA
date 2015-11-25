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
					<td colspan='10' height='1' bgcolor='#cccccc' style='font-size: 1px; line-height: 1px;'>&nbsp;</td>
				</tr>
				<tr style='background-color:#fab778;'>
					<td style='text-align:center;' colspan='10'>
						<h3>Notas fiscais  GRAVADAS - ".$data."</h3>
					</td>

				</tr>
				
				<tr>
					<td colspan='10' height='1' bgcolor='#cccccc' style='font-size: 1px; line-height: 1px;'>&nbsp;</td>
				</tr>
				<tr>
					<td height='10px' colspan='5'></td>
				</tr>
				
				
				<tr>
					
					<td  style='text-align:left;'  colspan='1'>
						<h4>Nota fiscal</h3> 
					</td>
					
					<td style='text-align:center;'  colspan='1'>
						<h4>Dt Emissão</h3> 
					</td>
					
					<td style='text-align:center;'  colspan='1'>
						<h4>Cliente</h3> 
					</td>
					
					<td  style='text-align:center;' colspan='1'>
						<h4>Atendimento</h3> 
					</td>
					
					<td style='text-align:center;'  colspan='1'>
						<h4>Nota Carioca</h3> 
					</td>
					
					<td  style='text-align:center;' colspan='1'>
						<h4>Vl nota</h3> 
					</td>
					
					<td  style='text-align:center;' colspan='1'>
						<h4>Usuário</h3> 
					</td>

					

					
				</tr>";
				
				
				
				foreach ($dados as $value) {
					
					$message .= "<tr>";
					$message .= "<td style='text-align:left;' colspan='1'><p style='font-size:9px;'>".$value['NOTA_FISCAL']."</p></td>";
					$message .= "<td style='text-align:center;'  colspan='1'><p style='font-size:9px;'>".$value['DT_EMISSAO']."</p></td>";
					$message .= "<td style='text-align:center;'  colspan='1'><p style='font-size:9px;'>".$value['NM_CLIENTE']."</p></td>";
					$message .= "<td style='text-align:center;'  colspan='1'><p style='font-size:9px;'>".$value['CD_ATENDIMENTO']."</p></td>";
					$message .= "<td style='text-align:center;'  colspan='1'><p style='font-size:9px;'>".$value['NOTA_CARIOCA']."</p></td>";
					$message .= "<td style='text-align:center;'  colspan='1'><p style='font-size:9px;'>R$ ".$value['VL_TOTAL_NOTA']."</p></td>";
					$message .= "<td style='text-align:right;'  colspan='1'><p style='font-size:9px;'>".$value['CD_USUARIO']."</p></td>";

					$message .= "</tr>";	
				};
			
				$message.="		
				
				
				<tr>
					<td colspan='10' height='1' bgcolor='#cccccc' style='font-size: 1px; line-height: 1px;'>&nbsp;</td>
				</tr>
				

				

				<!-- FIM DADOS-->
				
				
				<!--FOOTER-->
				<tr>
					<td height='10px' colspan='10'></td>
				</tr>
				
				<tr>
					<td style='padding: 100px; text-align:center;' colspan='10'>
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