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




$QUERY_NOTAS_NAO_CANCELADAS = "
 SELECT  to_char (SUM(NF.VL_TOTAL_NOTA),'999G999G990D90') SOMATORIO_NOTAS, C.NM_CONVENIO, TO_CHAR(SYSDATE,'MM/YYYY') MES_ANO
  FROM DBAMV.NOTA_FISCAL NF
  LEFT JOIN DBAMV.CONVENIO C
  ON C.CD_CONVENIO = NF.CD_CONVENIO
  WHERE NF.DT_CANCELAMENTO IS NULL
 AND TO_CHAR(NF.DT_EMISSAO,'MM/YYYY') = '$mes_atual'
 GROUP BY C.NM_CONVENIO, TO_CHAR(SYSDATE,'MM/YYYY')
 order by c.nm_convenio asc
 ";

 
$TOTAL_QUERY_NOTAS_NAO_CANCELADAS = "
select to_char (SUM(SOMATORIO_NOTAS),'999G999G990D90') SOMATORIO_NOTAS FROM (
SELECT SUM(NF.VL_TOTAL_NOTA) SOMATORIO_NOTAS, C.NM_CONVENIO, TO_CHAR(SYSDATE,'MM/YYYY') MES_ANO  
  FROM DBAMV.NOTA_FISCAL NF
  LEFT JOIN DBAMV.CONVENIO C
  ON C.CD_CONVENIO = NF.CD_CONVENIO
  WHERE NF.DT_CANCELAMENTO IS NULL
 AND TO_CHAR(NF.DT_EMISSAO,'MM/YYYY') = '$mes_atual'
 GROUP BY C.NM_CONVENIO, TO_CHAR(SYSDATE,'MM/YYYY')
 order by c.nm_convenio asc)
 ";

 
 
 
 $QUERY_NOTAS_CANCELADAS = "
 SELECT to_char (SUM(NF.VL_TOTAL_NOTA),'999G999G990D90') SOMATORIO_NOTAS, C.NM_CONVENIO, TO_CHAR(SYSDATE,'MM/YYYY') MES_ANO
  FROM DBAMV.NOTA_FISCAL NF
  LEFT JOIN DBAMV.CONVENIO C
  ON C.CD_CONVENIO = NF.CD_CONVENIO
  WHERE NF.DT_CANCELAMENTO IS NOT NULL
 AND TO_CHAR(NF.DT_EMISSAO,'MM/YYYY') = '$mes_atual'
 GROUP BY C.NM_CONVENIO, TO_CHAR(SYSDATE,'MM/YYYY')
 order by c.nm_convenio asc
 ";

 
$TOTAL_QUERY_NOTAS_CANCELADAS = "
select to_char (SUM(SOMATORIO_NOTAS),'999G999G990D90') SOMATORIO_NOTAS FROM (
SELECT SUM(NF.VL_TOTAL_NOTA) SOMATORIO_NOTAS, C.NM_CONVENIO, TO_CHAR(SYSDATE,'MM/YYYY') MES_ANO  
  FROM DBAMV.NOTA_FISCAL NF
  LEFT JOIN DBAMV.CONVENIO C
  ON C.CD_CONVENIO = NF.CD_CONVENIO
  WHERE NF.DT_CANCELAMENTO IS NOT NULL
 AND TO_CHAR(NF.DT_EMISSAO,'MM/YYYY') = '$mes_atual'
 GROUP BY C.NM_CONVENIO, TO_CHAR(SYSDATE,'MM/YYYY')
 order by c.nm_convenio asc)
 ";

$TOTAL_QUERY_NOTAS_NAO_CANCELADAS_MES_ANTERIOR = "
select to_char (SUM(SOMATORIO_NOTAS),'999G999G990D90') SOMATORIO_NOTAS FROM (
SELECT SUM(NF.VL_TOTAL_NOTA) SOMATORIO_NOTAS, C.NM_CONVENIO, TO_CHAR(SYSDATE,'MM/YYYY') MES_ANO  
  FROM DBAMV.NOTA_FISCAL NF
  LEFT JOIN DBAMV.CONVENIO C
  ON C.CD_CONVENIO = NF.CD_CONVENIO
  WHERE NF.DT_CANCELAMENTO IS NULL
 AND TO_CHAR(NF.DT_EMISSAO,'MM/YYYY') = '$mes_anterior'
 GROUP BY C.NM_CONVENIO, TO_CHAR(SYSDATE,'MM/YYYY')
 order by c.nm_convenio asc)
 ";
 
 $TOTAL_QUERY_NOTAS_CANCELADAS_MES_ANTERIOR = "
select to_char (SUM(SOMATORIO_NOTAS),'999G999G990D90') SOMATORIO_NOTAS FROM (
SELECT SUM(NF.VL_TOTAL_NOTA) SOMATORIO_NOTAS, C.NM_CONVENIO, TO_CHAR(SYSDATE,'MM/YYYY') MES_ANO  
  FROM DBAMV.NOTA_FISCAL NF
  LEFT JOIN DBAMV.CONVENIO C
  ON C.CD_CONVENIO = NF.CD_CONVENIO
  WHERE NF.DT_CANCELAMENTO IS NOT NULL
 AND TO_CHAR(NF.DT_EMISSAO,'MM/YYYY') = '$mes_anterior'
 GROUP BY C.NM_CONVENIO, TO_CHAR(SYSDATE,'MM/YYYY')
 order by c.nm_convenio asc)
 ";
        
        

$sql = oci_parse ( $conexao, $QUERY_NOTAS_NAO_CANCELADAS );
	oci_execute ( $sql );



	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$dados[] =   array('SOMATORIO' => $row['SOMATORIO_NOTAS'], 'NM_CONVENIO' =>$row['NM_CONVENIO'], 'MES_ANO' => $row['MES_ANO']);

	}
	
	
	
	
	
$sql = oci_parse ( $conexao, $TOTAL_QUERY_NOTAS_NAO_CANCELADAS );
	oci_execute ( $sql );



	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$total_geral =  $row['SOMATORIO_NOTAS'];

	}
	
	
	
	
	
	
	
	
	$sql = oci_parse ( $conexao, $QUERY_NOTAS_CANCELADAS );
	oci_execute ( $sql );



	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$dados_notas_canceladas[] =   array('SOMATORIO' => $row['SOMATORIO_NOTAS'], 'NM_CONVENIO' =>$row['NM_CONVENIO'], 'MES_ANO' => $row['MES_ANO']);

	}
	
	
	
	
	
$sql = oci_parse ( $conexao, $TOTAL_QUERY_NOTAS_CANCELADAS );
	oci_execute ( $sql );



	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$total_geral_canceladas =  $row['SOMATORIO_NOTAS'];

	}
	
	
$sql = oci_parse ( $conexao, $TOTAL_QUERY_NOTAS_NAO_CANCELADAS_MES_ANTERIOR );
	oci_execute ( $sql );



	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$total_geral_mes_anterior =  $row['SOMATORIO_NOTAS'];

	}	
	
	
	$sql = oci_parse ( $conexao, $TOTAL_QUERY_NOTAS_CANCELADAS_MES_ANTERIOR );
	oci_execute ( $sql );



	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$total_geral_canceladas_mes_anterior =  $row['SOMATORIO_NOTAS'];

	}	
?>		