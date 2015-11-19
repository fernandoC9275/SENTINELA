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



$sql ="select COMP,
       QUANT_CONTAS_ABERTAS,
       concat ('R$ ',to_char (TOTAL_CONTAS_ABERTAS,'999G999G990D90')) TOTAL_CONTAS_ABERTAS,
       QUANT_FECHADO,
       concat ('R$ ',to_char (TOTAL_FECHADO,'999G999G990D90'))TOTAL_FECHADO ,
       QUANT_COM_REMESSA,
       concat ('R$ ',to_char (TOTAL_COM_REMESSA,'999G999G990D90'))TOTAL_COM_REMESSA,
       TOTAL_COM_REMESSA vl_TOTAL_COM_REMESSA

from (

select COMP,
       sum(QUANT_CONTAS_ABERTAS) QUANT_CONTAS_ABERTAS,
       sum(TOTAL_CONTAS_ABERTAS) TOTAL_CONTAS_ABERTAS,
       sum(QUANT_FECHADO) QUANT_FECHADO,
       sum(TOTAL_FECHADO) TOTAL_FECHADO,
       sum(QUANT_COM_REMESSA) QUANT_COM_REMESSA,
       sum(TOTAL_COM_REMESSA) TOTAL_COM_REMESSA
from (
Select to_char(nvl(dt_competencia, nvl(dt_final, dt_inicio)), 'MM/YYYY') comp
      ,sum(decode(reg_fat.cd_remessa, null, decode(reg_fat.sn_fechada, 'N', 1, 0), 0)) QUANT_CONTAS_ABERTAS
      ,sum(decode(reg_fat.cd_remessa, null, decode(reg_fat.sn_fechada, 'N', vl_total_conta, 0), 0)) TOTAL_CONTAS_ABERTAS
      ,sum(decode(reg_fat.cd_remessa, null, decode(reg_fat.sn_fechada, 'S', 1, 0), 0)) QUANT_FECHADO
      ,sum(decode(reg_fat.cd_remessa, null, decode(reg_fat.sn_fechada, 'S', vl_total_conta, 0), 0)) TOTAL_FECHADO
      ,sum(decode(reg_fat.cd_remessa, null, 0, decode(reg_fat.sn_fechada, 'S', 1, 0))) + sum(decode(reg_fat.cd_remessa, null, 0, decode(reg_fat.sn_fechada, 'N', 1, 0))) QUANT_COM_REMESSA
      ,sum(decode(reg_fat.cd_remessa, null, 0, decode(reg_fat.sn_fechada, 'S', vl_total_conta, 0))) + sum(decode(reg_fat.cd_remessa, null, 0, decode(reg_fat.sn_fechada, 'N', vl_total_conta, 0))) TOTAL_COM_REMESSA
  from dbamv.reg_fat
      ,dbamv.convenio
      ,dbamv.remessa_fatura
      ,dbamv.fatura
 where reg_fat.cd_convenio = convenio.cd_convenio
   and fatura.cd_fatura(+) = remessa_fatura.cd_fatura
   and reg_fat.cd_convenio not in (53,129)
   and remessa_fatura.cd_remessa(+) = reg_fat.cd_remessa
   and to_char(nvl(dt_competencia, nvl(dt_final, dt_inicio)), 'MM/YYYY') in
       (to_char(sysdate,'mm/yyyy'), to_char(add_months(sysdate,-1),'mm/yyyy'), to_char(add_months(sysdate,-2),'mm/yyyy'),to_char(add_months(sysdate,-3),'mm/yyyy'))

group by to_char(nvl(dt_competencia, nvl(dt_final, dt_inicio)), 'MM/YYYY')


union


Select distinct to_char(nvl(dt_competencia, dt_lancamento), 'MM/YYYY') comp
      ,sum(decode(reg_amb.cd_remessa, null, decode(fechada.sn_fechada, 'N', 1, 0), 0)) QUANT_CONTAS_ABERTAS
      ,sum(decode(reg_amb.cd_remessa, null, decode(fechada.sn_fechada, 'N', fechada.vl_total_conta, 0), 0)) TOTAL_CONTAS_ABERTAS
      ,sum(decode(reg_amb.cd_remessa, null, decode(fechada.sn_fechada, 'S', 1, 0), 0)) QUANT_FECHADO
      ,sum(decode(reg_amb.cd_remessa, null, decode(fechada.sn_fechada, 'S', fechada.vl_total_conta, 0), 0)) TOTAL_FECHADO
      ,sum(decode(reg_amb.cd_remessa, null, 0, decode(tp_convenio, 'P', 1, decode(fechada.sn_fechada, 'S', 1, 0)))) +
       sum(decode(reg_amb.cd_remessa, null, 0, decode(tp_convenio, 'P', 0, decode(fechada.sn_fechada, 'N', 1, 0)))) QUANT_COM_REMESSA
      ,sum(decode(reg_amb.cd_remessa, null, 0, decode(tp_convenio, 'P', fechada.vl_total_conta, decode(fechada.sn_fechada, 'S', fechada.vl_total_conta, 0))))+
       sum(decode(reg_amb.cd_remessa, null, 0, decode(tp_convenio, 'P', 0, decode(fechada.sn_fechada, 'N', fechada.vl_total_conta, 0)))) TOTAL_COM_REMESSA
  from dbamv.reg_amb
      ,dbamv.convenio
      ,dbamv.remessa_fatura
      ,dbamv.fatura
      ,(Select distinct cd_reg_amb, sn_fechada, sum(vl_total_conta) vl_total_conta
          from dbamv.itreg_amb
         where nvl(tp_pagamento, 'P') <> 'C'
           and sn_pertence_pacote = 'N'
        group by cd_reg_amb, sn_fechada) fechada
 where reg_amb.cd_convenio = convenio.cd_convenio
   and reg_amb.cd_reg_amb = fechada.cd_reg_amb
   and fatura.cd_fatura(+) = remessa_fatura.cd_fatura
   and reg_amb.cd_convenio not in (53,129)
   and remessa_fatura.cd_remessa(+) = reg_amb.cd_remessa
   and nvl(fechada.vl_total_conta,0) <> 0
   and to_char(nvl(dt_competencia, dt_lancamento), 'MM/YYYY') in
       (to_char(sysdate,'mm/yyyy'), to_char(add_months(sysdate,-1),'mm/yyyy'), to_char(add_months(sysdate,-2),'mm/yyyy'),to_char(add_months(sysdate,-3),'mm/yyyy'))


group by to_char(nvl(dt_competencia, dt_lancamento), 'MM/YYYY'))
group by comp
order by comp desc

)";

	
$sql = oci_parse ( $conexao, $sql );
	oci_execute ( $sql );



	while ( ($row = oci_fetch_array ( $sql, OCI_BOTH )) != false )
	{
		$dados[] =   array('COMPETENCIA' => $row['COMP'], 'TOTAL_CONTAS_ABERTAS' => $row['TOTAL_CONTAS_ABERTAS'], 'TOTAL_FECHADO' => $row['TOTAL_FECHADO'], 'TOTAL_COM_REMESSA' => $row['TOTAL_COM_REMESSA']);
		
	
	}
	
	
	
	
	

?>		