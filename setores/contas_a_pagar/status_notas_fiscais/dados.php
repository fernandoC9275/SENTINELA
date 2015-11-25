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




$QUERY = "
 SELECT NR_ID_NOTA_FISCAL || '-' || CD_SERIE NOTA_FISCAL,
       TO_CHAR(DT_EMISSAO,'DD/MM/YYYY') DT_EMISSAO,
       NM_CLIENTE,
       CASE
         WHEN CD_ATENDIMENTO IS NULL THEN 'NÃO INFORMADO'
         ELSE TO_CHAR(CD_ATENDIMENTO)
       END CD_ATENDIMENTO,
       CASE
         WHEN NR_NOTA_FISCAL_NFE IS NULL THEN 'NÃO INFORMADO'
         ELSE TO_CHAR(NR_NOTA_FISCAL_NFE)
       END NOTA_CARIOCA,
       VL_TOTAL_NOTA,
       CD_USUARIO
  FROM DBAMV.NOTA_FISCAL A
 WHERE CD_STATUS IS NULL
 order by dt_emissao ASC


 ";


        
        

$sql = oci_parse ( $conexao, $QUERY );
	oci_execute ( $sql );



	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$dados[] =   array('NOTA_FISCAL' => $row['NOTA_FISCAL'], 'DT_EMISSAO' =>$row['DT_EMISSAO'], 'NM_CLIENTE' => $row['NM_CLIENTE'], 'CD_ATENDIMENTO' => $row['CD_ATENDIMENTO'], 'NOTA_CARIOCA' => $row['NOTA_CARIOCA'], 'VL_TOTAL_NOTA' => $row['VL_TOTAL_NOTA'], 'CD_USUARIO' => $row['CD_USUARIO']);

	}
	
	
	
	
	

?>		