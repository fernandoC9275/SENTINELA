<?php
// Conexão
include_once ('../../../../oracle/connec.php');

date_default_timezone_set('America/Sao_Paulo');


$hoje = time();
$ontem = $hoje - (24*3600);
$anteontem = $hoje - (24*7200);;

$data = date('d/m/Y H:i', $hoje);
$data_ontem = date('d/m/Y', $ontem);
$data_ontem2 = date('d/m/Y', $anteontem);

$mes_atual = date('m/Y', $hoje);
$mes_anterior = date('m/Y', strtotime('-1 months', strtotime(date('Y-m-d'))));



$sql ="
SELECT  CD_AVISO_CIRURGIA, NM_PACIENTE, SUM(QT_CONSUMO) TOTAL_ITENS FROM (
SELECT  AC.CD_AVISO_CIRURGIA,P.NM_PACIENTE, TRUNC(ICP.QT_CONSUMO) QT_CONSUMO, PROD.DS_PRODUTO
  FROM CONSUMO_PACIENTE CP
  LEFT JOIN AVISO_CIRURGIA AC
  ON AC.CD_AVISO_CIRURGIA = CP.CD_AVISO_CIRURGIA
  LEFT JOIN ATENDIME A
  ON A.CD_ATENDIMENTO = CP.CD_ATENDIMENTO
  LEFT JOIN PACIENTE P 
  ON P.CD_PACIENTE = A.CD_PACIENTE
  LEFT JOIN ITCONSUMO_PACIENTE ICP
  ON ICP.CD_CONSUMO_PACIENTE = CP.CD_CONSUMO_PACIENTE
  LEFT JOIN PRODUTO PROD
  ON PROD.CD_PRODUTO = ICP.CD_PRODUTO
 WHERE AC.TP_SITUACAO = 'C'
)
 GROUP BY CD_AVISO_CIRURGIA, NM_PACIENTE
 ORDER BY 3 DESC


";






	
$sql = oci_parse ( $conexao, $sql );
	oci_execute ( $sql );



	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$dados[] =   array('CD_AVISO_CIRURGIA' => $row['CD_AVISO_CIRURGIA'], 'NM_PACIENTE' => $row['NM_PACIENTE'], 'TOTAL_ITENS' => $row['TOTAL_ITENS']);
		
	
	}
	

	
	
	
	

?>		