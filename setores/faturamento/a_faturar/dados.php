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



$sql ="SELECT CONVENIO.CD_CONVENIO,
       CONVENIO.NM_CONVENIO,
       CONVENIO.QT_A_FATURAR,
       CONVENIO.QT_N_FATURADO,
       CONVENIO.QT_01,
       CONVENIO.QT_02,
       CONVENIO.QT_03,
       CONVENIO.QT_04,
       CONVENIO.QT_05,
       CONVENIO.QT_06
  FROM (SELECT CD_CONVENIO,
               NM_CONVENIO,
               SUM(QT_01) QT_01,
               SUM(QT_02) QT_02,
               SUM(QT_03) QT_03,
               SUM(QT_04) QT_04,
               SUM(QT_05) QT_05,
               SUM(QT_06) QT_06,
               SUM(QT_N_FATURADO) QT_N_FATURADO,
               SUM(QT_A_FATURAR) QT_A_FATURAR
          FROM (SELECT VM.CD_CONVENIO CD_CONVENIO,
                       VM.NM_CONVENIO NM_CONVENIO,
                       0 CONTA,
                       0 QT_01,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '10/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_02,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '09/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_03,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '08/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_04,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '07/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_05,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '06/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_06,
                       0 QT_N_FATURADO,
                       0 QT_A_FATURAR
                  FROM DBAMV.VM_FAT_SEMESTRAL_CONV VM
                 WHERE to_date(VM.MES, 'mm/yyyy') >=
                       to_date('06/2015', 'MM/YYYY')
                
                UNION ALL
                /*********************************************************************************************************************/
                
                SELECT CONVENIO.CD_CONVENIO CD_CONVENIO,
                       CONVENIO.NM_CONVENIO NM_CONVENIO,
                       REG_FAT.CD_REG_FAT CONTA,
                       NVL(REG_FAT.VL_TOTAL_CONTA, 0) QT_01,
                       0 QT_02,
                       0 QT_03,
                       0 QT_04,
                       0 QT_05,
                       0 QT_06,
                       0 QT_N_FATURADO,
                       0 QT_A_FATURAR
                  FROM DBAMV.FATURA,
                       DBAMV.REMESSA_FATURA,
                       DBAMV.REG_FAT,
                       DBAMV.CONVENIO
                 WHERE TO_CHAR(FATURA.DT_COMPETENCIA, 'MM/YYYY') = '$mes_atual'
                   AND FATURA.CD_FATURA = REMESSA_FATURA.CD_FATURA
                   AND REMESSA_FATURA.CD_REMESSA = REG_FAT.CD_REMESSA
                   AND REMESSA_FATURA.SN_FECHADA = 'S'
                   AND REG_FAT.SN_FECHADA = 'S'
                   AND REG_FAT.CD_CONVENIO = CONVENIO.CD_CONVENIO
                   AND CONVENIO.TP_CONVENIO <> 'H'
                
                UNION ALL
                
                SELECT CONVENIO.CD_CONVENIO CD_CONVENIO,
                       CONVENIO.NM_CONVENIO NM_CONVENIO,
                       ITREG_AMB.CD_REG_AMB CONTA,
                       NVL(ITREG_AMB.VL_TOTAL_CONTA, 0) QT_01,
                       0 QT_02,
                       0 QT_03,
                       0 QT_04,
                       0 QT_05,
                       0 QT_06,
                       0 QT_N_FATURADO,
                       0 QT_A_FATURAR
                  FROM DBAMV.FATURA,
                       DBAMV.REMESSA_FATURA REMESSA,
                       DBAMV.REG_AMB,
                       DBAMV.ITREG_AMB,
                       DBAMV.CONVENIO
                 WHERE TO_CHAR(FATURA.DT_COMPETENCIA, 'MM/YYYY') = '$mes_atual'
                   AND FATURA.CD_FATURA = REMESSA.CD_FATURA
                   AND REMESSA.CD_REMESSA = REG_AMB.CD_REMESSA
                   AND REG_AMB.CD_REG_AMB = ITREG_AMB.CD_REG_AMB
                   AND REMESSA.SN_FECHADA = 'S'
                   AND ITREG_AMB.SN_FECHADA = 'S'
                   AND ITREG_AMB.SN_PERTENCE_PACOTE = 'N'
                   AND ITREG_AMB.CD_CONVENIO = CONVENIO.CD_CONVENIO
                   AND NVL(ITREG_AMB.TP_PAGAMENTO, 'X') <> 'C'
                   AND REG_AMB.CD_MULTI_EMPRESA = 1
                   AND CONVENIO.TP_CONVENIO <> 'H'
                
                /***********************************************************************************************************************/
                UNION ALL
                
                /*:Pegar os Convênios que não faturaram no Semestre, mas tem algo a faturar ainda*/
                SELECT CD_CONVENIO,
                       NM_CONVENIO,
                       CONTA,
                       0             QT_01,
                       0             QT_02,
                       0             QT_03,
                       0             QT_04,
                       0             QT_05,
                       0             QT_06,
                       QT_N_FATURADO,
                       QT_A_FATURAR
                  FROM DBAMV.MVw_Res_Semestral_NA
                 WHERE CD_MULTI_EMPRESA = 1)
         GROUP BY CD_CONVENIO, NM_CONVENIO) CONVENIO
 WHERE exists (Select CD_CONVENIO
          from DBAMV.EMPRESA_CONVENIO
         where Empresa_convenio.cd_convenio = Convenio.Cd_convenio
         )
 order by NM_CONVENIO
";





$sql_a_faturar = "SELECT TO_CHAR(SUM(QT_A_FATURAR),'999G999G990D90') TOTAL_QT_A_FATURAR FROM (
SELECT CONVENIO.CD_CONVENIO,
       CONVENIO.NM_CONVENIO,
       CONVENIO.QT_A_FATURAR,
       CONVENIO.QT_N_FATURADO,
       CONVENIO.QT_01,
       CONVENIO.QT_02,
       CONVENIO.QT_03,
       CONVENIO.QT_04,
       CONVENIO.QT_05,
       CONVENIO.QT_06
  FROM (SELECT CD_CONVENIO,
               NM_CONVENIO,
               SUM(QT_01) QT_01,
               SUM(QT_02) QT_02,
               SUM(QT_03) QT_03,
               SUM(QT_04) QT_04,
               SUM(QT_05) QT_05,
               SUM(QT_06) QT_06,
               SUM(QT_N_FATURADO) QT_N_FATURADO,
               SUM(QT_A_FATURAR) QT_A_FATURAR
          FROM (SELECT VM.CD_CONVENIO CD_CONVENIO,
                       VM.NM_CONVENIO NM_CONVENIO,
                       0 CONTA,
                       0 QT_01,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '10/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_02,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '09/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_03,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '08/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_04,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '07/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_05,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '06/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_06,
                       0 QT_N_FATURADO,
                       0 QT_A_FATURAR
                  FROM DBAMV.VM_FAT_SEMESTRAL_CONV VM
                 WHERE to_date(VM.MES, 'mm/yyyy') >=
                       to_date('06/2015', 'MM/YYYY')
                
                UNION ALL
                /*********************************************************************************************************************/
                
                SELECT CONVENIO.CD_CONVENIO CD_CONVENIO,
                       CONVENIO.NM_CONVENIO NM_CONVENIO,
                       REG_FAT.CD_REG_FAT CONTA,
                       NVL(REG_FAT.VL_TOTAL_CONTA, 0) QT_01,
                       0 QT_02,
                       0 QT_03,
                       0 QT_04,
                       0 QT_05,
                       0 QT_06,
                       0 QT_N_FATURADO,
                       0 QT_A_FATURAR
                  FROM DBAMV.FATURA,
                       DBAMV.REMESSA_FATURA,
                       DBAMV.REG_FAT,
                       DBAMV.CONVENIO
                 WHERE TO_CHAR(FATURA.DT_COMPETENCIA, 'MM/YYYY') = '$mes_atual'
                   AND FATURA.CD_FATURA = REMESSA_FATURA.CD_FATURA
                   AND REMESSA_FATURA.CD_REMESSA = REG_FAT.CD_REMESSA
                   AND REMESSA_FATURA.SN_FECHADA = 'S'
                   AND REG_FAT.SN_FECHADA = 'S'
                   AND REG_FAT.CD_CONVENIO = CONVENIO.CD_CONVENIO
                   AND CONVENIO.TP_CONVENIO <> 'H'
                
                UNION ALL
                
                SELECT CONVENIO.CD_CONVENIO CD_CONVENIO,
                       CONVENIO.NM_CONVENIO NM_CONVENIO,
                       ITREG_AMB.CD_REG_AMB CONTA,
                       NVL(ITREG_AMB.VL_TOTAL_CONTA, 0) QT_01,
                       0 QT_02,
                       0 QT_03,
                       0 QT_04,
                       0 QT_05,
                       0 QT_06,
                       0 QT_N_FATURADO,
                       0 QT_A_FATURAR
                  FROM DBAMV.FATURA,
                       DBAMV.REMESSA_FATURA REMESSA,
                       DBAMV.REG_AMB,
                       DBAMV.ITREG_AMB,
                       DBAMV.CONVENIO
                 WHERE TO_CHAR(FATURA.DT_COMPETENCIA, 'MM/YYYY') = '$mes_atual'
                   AND FATURA.CD_FATURA = REMESSA.CD_FATURA
                   AND REMESSA.CD_REMESSA = REG_AMB.CD_REMESSA
                   AND REG_AMB.CD_REG_AMB = ITREG_AMB.CD_REG_AMB
                   AND REMESSA.SN_FECHADA = 'S'
                   AND ITREG_AMB.SN_FECHADA = 'S'
                   AND ITREG_AMB.SN_PERTENCE_PACOTE = 'N'
                   AND ITREG_AMB.CD_CONVENIO = CONVENIO.CD_CONVENIO
                   AND NVL(ITREG_AMB.TP_PAGAMENTO, 'X') <> 'C'
                   AND REG_AMB.CD_MULTI_EMPRESA = 1
                   AND CONVENIO.TP_CONVENIO <> 'H'
                
                /***********************************************************************************************************************/
                UNION ALL
                
                /*:Pegar os Convênios que não faturaram no Semestre, mas tem algo a faturar ainda*/
                SELECT CD_CONVENIO,
                       NM_CONVENIO,
                       CONTA,
                       0             QT_01,
                       0             QT_02,
                       0             QT_03,
                       0             QT_04,
                       0             QT_05,
                       0             QT_06,
                       QT_N_FATURADO,
                       QT_A_FATURAR
                  FROM DBAMV.MVw_Res_Semestral_NA
                 WHERE CD_MULTI_EMPRESA = 1)
         GROUP BY CD_CONVENIO, NM_CONVENIO) CONVENIO
 WHERE exists (Select CD_CONVENIO
          from DBAMV.EMPRESA_CONVENIO
         where Empresa_convenio.cd_convenio = Convenio.Cd_convenio
         )
 order by NM_CONVENIO
 )
";

$sql_nao_faturado ="
	SELECT TO_CHAR(SUM(QT_N_FATURADO),'999G999G990D90') TOTAL_QT_N_FATURADO FROM (
SELECT CONVENIO.CD_CONVENIO,
       CONVENIO.NM_CONVENIO,
       CONVENIO.QT_A_FATURAR,
       CONVENIO.QT_N_FATURADO,
       CONVENIO.QT_01,
       CONVENIO.QT_02,
       CONVENIO.QT_03,
       CONVENIO.QT_04,
       CONVENIO.QT_05,
       CONVENIO.QT_06
  FROM (SELECT CD_CONVENIO,
               NM_CONVENIO,
               SUM(QT_01) QT_01,
               SUM(QT_02) QT_02,
               SUM(QT_03) QT_03,
               SUM(QT_04) QT_04,
               SUM(QT_05) QT_05,
               SUM(QT_06) QT_06,
               SUM(QT_N_FATURADO) QT_N_FATURADO,
               SUM(QT_A_FATURAR) QT_A_FATURAR
          FROM (SELECT VM.CD_CONVENIO CD_CONVENIO,
                       VM.NM_CONVENIO NM_CONVENIO,
                       0 CONTA,
                       0 QT_01,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '10/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_02,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '09/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_03,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '08/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_04,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '07/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_05,
                       DECODE(LPAD(VM.MES, 7, '0'),
                              '06/2015',
                              NVL(VM.VALOR, 0),
                              0) QT_06,
                       0 QT_N_FATURADO,
                       0 QT_A_FATURAR
                  FROM DBAMV.VM_FAT_SEMESTRAL_CONV VM
                 WHERE to_date(VM.MES, 'mm/yyyy') >=
                       to_date('06/2015', 'MM/YYYY')
                
                UNION ALL
                /*********************************************************************************************************************/
                
                SELECT CONVENIO.CD_CONVENIO CD_CONVENIO,
                       CONVENIO.NM_CONVENIO NM_CONVENIO,
                       REG_FAT.CD_REG_FAT CONTA,
                       NVL(REG_FAT.VL_TOTAL_CONTA, 0) QT_01,
                       0 QT_02,
                       0 QT_03,
                       0 QT_04,
                       0 QT_05,
                       0 QT_06,
                       0 QT_N_FATURADO,
                       0 QT_A_FATURAR
                  FROM DBAMV.FATURA,
                       DBAMV.REMESSA_FATURA,
                       DBAMV.REG_FAT,
                       DBAMV.CONVENIO
                 WHERE TO_CHAR(FATURA.DT_COMPETENCIA, 'MM/YYYY') = '$mes_atual'
                   AND FATURA.CD_FATURA = REMESSA_FATURA.CD_FATURA
                   AND REMESSA_FATURA.CD_REMESSA = REG_FAT.CD_REMESSA
                   AND REMESSA_FATURA.SN_FECHADA = 'S'
                   AND REG_FAT.SN_FECHADA = 'S'
                   AND REG_FAT.CD_CONVENIO = CONVENIO.CD_CONVENIO
                   AND CONVENIO.TP_CONVENIO <> 'H'
                
                UNION ALL
                
                SELECT CONVENIO.CD_CONVENIO CD_CONVENIO,
                       CONVENIO.NM_CONVENIO NM_CONVENIO,
                       ITREG_AMB.CD_REG_AMB CONTA,
                       NVL(ITREG_AMB.VL_TOTAL_CONTA, 0) QT_01,
                       0 QT_02,
                       0 QT_03,
                       0 QT_04,
                       0 QT_05,
                       0 QT_06,
                       0 QT_N_FATURADO,
                       0 QT_A_FATURAR
                  FROM DBAMV.FATURA,
                       DBAMV.REMESSA_FATURA REMESSA,
                       DBAMV.REG_AMB,
                       DBAMV.ITREG_AMB,
                       DBAMV.CONVENIO
                 WHERE TO_CHAR(FATURA.DT_COMPETENCIA, 'MM/YYYY') = '$mes_atual'
                   AND FATURA.CD_FATURA = REMESSA.CD_FATURA
                   AND REMESSA.CD_REMESSA = REG_AMB.CD_REMESSA
                   AND REG_AMB.CD_REG_AMB = ITREG_AMB.CD_REG_AMB
                   AND REMESSA.SN_FECHADA = 'S'
                   AND ITREG_AMB.SN_FECHADA = 'S'
                   AND ITREG_AMB.SN_PERTENCE_PACOTE = 'N'
                   AND ITREG_AMB.CD_CONVENIO = CONVENIO.CD_CONVENIO
                   AND NVL(ITREG_AMB.TP_PAGAMENTO, 'X') <> 'C'
                   AND REG_AMB.CD_MULTI_EMPRESA = 1
                   AND CONVENIO.TP_CONVENIO <> 'H'
                
                /***********************************************************************************************************************/
                UNION ALL
                
                /*:Pegar os Convênios que não faturaram no Semestre, mas tem algo a faturar ainda*/
                SELECT CD_CONVENIO,
                       NM_CONVENIO,
                       CONTA,
                       0             QT_01,
                       0             QT_02,
                       0             QT_03,
                       0             QT_04,
                       0             QT_05,
                       0             QT_06,
                       QT_N_FATURADO,
                       QT_A_FATURAR
                  FROM DBAMV.MVw_Res_Semestral_NA
                 WHERE CD_MULTI_EMPRESA = 1)
         GROUP BY CD_CONVENIO, NM_CONVENIO) CONVENIO
 WHERE exists (Select CD_CONVENIO
          from DBAMV.EMPRESA_CONVENIO
         where Empresa_convenio.cd_convenio = Convenio.Cd_convenio
         )
 order by NM_CONVENIO
 )

";

	
$sql = oci_parse ( $conexao, $sql );
	oci_execute ( $sql );



	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$dados[] =   array('CONVENIO' => $row['NM_CONVENIO'], 'QT_A_FATURAR' => $row['QT_A_FATURAR'], 'QT_N_FATURADO' => $row['QT_N_FATURADO'], 'QT_01' => $row['QT_01']);
		
	
	}
	
	
	
	$sql = oci_parse ( $conexao, $sql_a_faturar );
	oci_execute ( $sql );



	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$total_a_faturar =  $row['TOTAL_QT_A_FATURAR'];
		
	
	}
	
	$sql = oci_parse ( $conexao, $sql_nao_faturado );
	oci_execute ( $sql );



	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$total_n_faturado =  $row['TOTAL_QT_N_FATURADO'];
		
	
	}
	
	
	
	
	
	
	
	

?>		