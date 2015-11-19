<?php
// Conexão
include_once ('../../../../oracle/connec.php');

date_default_timezone_set('America/Sao_Paulo');


$QUERY_HOJE = "SELECT LPAD (AVG (qtd_pacientes / leitos.qtd_leitos) * 100, 5) TX_OCUPACAO_HOJE
  FROM (SELECT COUNT (*) qtd_pacientes
          FROM atendime
         WHERE     atendime.tp_atendimento = 'I'
               AND atendime.dt_alta IS NULL
               AND cd_leito NOT IN (SELECT CD_LEITO 
                                    FROM LEITO 
                                    WHERE CD_UNID_INT IN (20,22,24,25)
                                    )),
       (SELECT COUNT (*) qtd_leitos
          FROM dbamv.leito
         WHERE tp_situacao = 'A' AND sn_extra = 'N' ) leitos ";
         



$QUERY_ONTEM = "SELECT LPAD (AVG (qtd_pacientes / leitos.qtd_leitos) * 100, 5) TX_OCUPACAO_ONTEM
  FROM (SELECT COUNT (*) qtd_pacientes
          FROM atendime
         WHERE     atendime.tp_atendimento = 'I'
                AND ATENDIME.DT_ALTA IS NULL
                AND (ATENDIME.DT_ATENDIMENTO < SYSDATE -1 )
               AND cd_leito NOT IN (SELECT CD_LEITO 
                                    FROM LEITO 
                                    WHERE CD_UNID_INT IN (20,22,24,25)
                                    )),
       (SELECT COUNT (*) qtd_leitos
          FROM dbamv.leito
         WHERE tp_situacao = 'A' AND sn_extra = 'N' ) leitos";         
         
         
  
         
         
         
$QUERY_ONTEM2 = "SELECT LPAD (AVG (qtd_pacientes / leitos.qtd_leitos) * 100, 5) TX_OCUPACAO_ONTEM2
  FROM (SELECT COUNT (*) qtd_pacientes
          FROM atendime
         WHERE     atendime.tp_atendimento = 'I'
                AND ATENDIME.DT_ALTA IS NULL
                AND (ATENDIME.DT_ATENDIMENTO < SYSDATE -2 )
               AND cd_leito NOT IN (SELECT CD_LEITO 
                                    FROM LEITO 
                                    WHERE CD_UNID_INT IN (20,22,24,25)
                                    )),
       (SELECT COUNT (*) qtd_leitos
          FROM dbamv.leito
         WHERE tp_situacao = 'A' AND sn_extra = 'N' ) leitos";       
         
      
        
        

$sql = oci_parse ( $conexao, $QUERY_HOJE );
	oci_execute ( $sql );
	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$TX_OCUPACAO_HOJE = $row ['TX_OCUPACAO_HOJE'];
	}
	
$sql = oci_parse ( $conexao, $QUERY_ONTEM );
	oci_execute ( $sql );
	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$TX_OCUPACAO_ONTEM = $row ['TX_OCUPACAO_ONTEM'];
	}	

$sql = oci_parse ( $conexao, $QUERY_ONTEM2 );
	oci_execute ( $sql );
	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$TX_OCUPACAO_ONTEM2 = $row ['TX_OCUPACAO_ONTEM2'];
	}	
	
	
	
	//VALIDAÇÃO THUMBS ARROW
	IF($TX_OCUPACAO_ONTEM > $TX_OCUPACAO_ONTEM2){
		$thumbs = 'arrow_up.png';
			}ELSE IF($TX_OCUPACAO_ONTEM < $TX_OCUPACAO_ONTEM2){
		$thumbs = 'arrow_down.png';
	}
	
	
	//COR CAIXA DE OCUPAÇÃO
	
	IF($TX_OCUPACAO_HOJE <=70){
		$cor_box = 'rgb(255,0,0)';
	}ELSE IF($TX_OCUPACAO_HOJE >=71 and $TX_OCUPACAO_HOJE <=85){
		$cor_box = 'rgb(0,128,0)';
	}ELSE IF($TX_OCUPACAO_HOJE >=86 ){
		$cor_box = 'rgb(255,255,0)';
	}
	
	date_default_timezone_set('America/Sao_Paulo');
	
	
	$hoje = time();
	$ontem = $hoje - (24*3600);
	$anteontem = $hoje - (24*7200);; 
	
	$data = date('d/m/Y H:i', $hoje);
	$data_ontem = date('d/m/Y', $ontem);
	$data_ontem2 = date('d/m/Y', $anteontem);
	

?>
