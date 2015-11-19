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
$amanha = date('d/m/Y', strtotime("+1 day"));

$mes_atual = date('m/Y', $hoje);
$mes_anterior = date('m/Y', strtotime('-1 months', strtotime(date('Y-m-d'))));

$query="SELECT A.CD_ATENDIMENTO,
       P.NM_PACIENTE,
       TRUNC(A.DT_PREVISTA_ALTA)DT_PREVISTA_ALTA,
       L.DS_RESUMO,
       UI.DS_UNID_INT
  FROM ATENDIME A
  LEFT JOIN PACIENTE P
    ON P.CD_PACIENTE = A.CD_PACIENTE
  LEFT JOIN LEITO L
    ON L.CD_LEITO = A.CD_LEITO
  LEFT JOIN UNID_INT UI
    ON UI.CD_UNID_INT = L.CD_UNID_INT
 WHERE TRUNC(A.DT_PREVISTA_ALTA) = TRUNC(SYSDATE) + 1
 GROUP BY A.CD_ATENDIMENTO,
       P.NM_PACIENTE,
       TRUNC(A.DT_PREVISTA_ALTA),
        L.DS_RESUMO,
       UI.DS_UNID_INT
ORDER BY UI.DS_UNID_INT, P.NM_PACIENTE        ASC

";



$sql = oci_parse ( $conexao, $query );
	oci_execute ( $sql );



	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$dados[] =   array('ATENDIMENTO' => $row['CD_ATENDIMENTO'], 
		'PACIENTE' =>$row['NM_PACIENTE'], 
		'DT_PREVISTA_ALTA' => $row['DT_PREVISTA_ALTA'], 
		'LEITO' => $row['DS_RESUMO'], 
		'DT_PREVISTA_ALTA' => $row['DT_PREVISTA_ALTA'],
		'UNID_INT' => $row['DS_UNID_INT']
		
		);

	}

?>		