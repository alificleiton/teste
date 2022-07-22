<?
//$allowSession = "nao";
require_once("lib/configs.php");
require_once("multi_idioma_request.php");

// setar sessão com nome da página para ser usada no controle de acesso
$_SESSION["care-br"]["submodulo_pagina"] = "os_controle_aguarda_analise.php";

$status_id = STATUS_OS_AGUARDA_ANALISE;

// request
$acao = $_REQUEST["acao"];
$cliente_id = $_REQUEST["cliente_id"];
$clienteconfig_id = $_REQUEST["clienteconfig_id"];
$os_id = $_REQUEST["os_id"];
$status_id_novo = $_REQUEST["status_id_novo"];
$produto_id = $_REQUEST["produto_id"];
$produto_id_peca = $_REQUEST["produto_id_peca"];
$osprodutopeca_id = $_REQUEST["osprodutopeca_id"];
$osprodutopeca_qtde = $_REQUEST["osprodutopeca_qtde"];
$osprodutopeca_cobrar = $_REQUEST["osprodutopeca_cobrar"];
$os_obs_retirada = $_REQUEST["os_obs_retirada"];
$os_tipo_servico = $_REQUEST["os_tipo_servico"];
$produto_id_emprestimo = $_REQUEST["produto_id_emprestimo"];
$numero_id = $_REQUEST["numero_id"];

$osprodutopeca_valor_venda = $_REQUEST['osprodutopeca_valor_venda'];
$osprodutopeca_desconto = $_REQUEST['osprodutopeca_desconto'];
$osprodutopeca_valor_mao_obra = $_REQUEST['osprodutopeca_valor_mao_obra'];

if(($produto_id_emprestimo == "") || ($produto_id_emprestimo == 'undefined')) 
	$produto_id_emprestimo = 'null';


$osservico_id = $_REQUEST["osservico_id"];
$servico_id = $_REQUEST["servico_id"];
$osservico_valor = str_replace(",", ".", str_replace(".", "", $_REQUEST["osservico_valor"]));
$osservico_desconto = str_replace(",", ".", str_replace(".", "", $_REQUEST["osservico_desconto"]));
if (empty($osservico_valor))
	$osservico_valor = 0;

// ------------------------------------
// confirmar análise técnica do produto
// ------------------------------------
if ($acao == "add"){
	// B2X SES, Moema e Fortaleza
    if ($clienteconfig_id == 101 || $clienteconfig_id == 110 || $clienteconfig_id == 112 || $clienteconfig_id == 123 || $clienteconfig_id == 124 || $clienteconfig_id == 131 || $clienteconfig_id == 133 || (strpos(B2X_SES_OS.B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
        $sql = "SELECT osprodutopeca_valor_venda, osprodutopeca_valor_mao_obra, osprodutopeca_valor_desconto, osprodutopeca_qtde FROM tb_prod_os_produto_peca WHERE os_id = $os_id";
        $res = $conn->sql($sql);
        while ($tmp = mysqli_fetch_array($res)) {
			$valor_peca = $tmp["osprodutopeca_valor_venda"] * $tmp["osprodutopeca_qtde"];
            $total_peca += $valor_peca;
            $total_mo += $tmp["osprodutopeca_valor_mao_obra"];
            $total_desconto += $tmp["osprodutopeca_valor_desconto"];
		}
		
		$sql = "SELECT * FROM tb_prod_os_servico WHERE os_id = $os_id";
        $res = $conn->sql($sql);
        while ($tmp = mysqli_fetch_array($res)) {
            $total_mo += $tmp["osservico_valor"];
            $total_desconto += $tmp["osservico_desconto"];
		}

        $total = $total_peca + $total_mo;
        $total_liquido = $total_peca + $total_mo - $total_desconto;
        if (empty($total)) {
            $total = '0.00';
        }
        if (empty($total_mo)) {
            $total_mo = '0.00';
        }
        if (empty($total_peca)) {
            $total_peca = '0.00';
        }
        if (empty($total_desconto)) {
            $total_desconto = '0.00';
        }
        if (empty($total_liquido)) {
            $total_liquido = '0.00';
        }
        if (empty($total_mo)) {
            $total_mo = '0.00';
        }
        if (empty($total_peca)) {
            $total_peca = '0.00';
        }
        $sql_update = ", os_valor_total = $total, os_valor_maodeobra = $total_mo, os_valor_pecas = $total_peca, os_desconto_total = $total_desconto, os_desconto_pecas = $total_desconto, os_valor_liquido = $total_liquido, os_valor_liquido_maodeobra = $total_mo, os_valor_liquido_pecas = $total_peca";
	}
	$sql = "UPDATE tb_prod_os SET
			status_id = '" . $status_id_novo . "',
			os_usuario_avaliacao_tecnica = '" . $_SESSION["care-br"]["usuario_nome"] . "',
			os_data_status = '" . getNow($clienteconfig_id) . "',
			os_tipo_servico = '" . $os_tipo_servico . "',
			os_data_update = '" . getNow($clienteconfig_id) . "',
			produto_id_emprestimo = " . $produto_id_emprestimo . "
			$sql_update
			WHERE os_id = '" . $os_id . "'";
	$conn->sql($sql);
	
	//Troca status de reserva do endereço
	if ($os_id>0){
		$sql_buscar_local_os = "SELECT numero_id FROM tb_prod_os WHERE os_id='".$os_id."' ";
		$res_buscar_local_os = $conn->sql($sql_buscar_local_os);
		$obj_buscar_local_os = mysqli_fetch_object($res_buscar_local_os);

		$sql = "UPDATE tb_cad_os_local_numero SET
				numero_disponivel = 'S'
				WHERE numero_id = '" . $obj_buscar_local_os->numero_id . "' ";
		$conn->sql($sql);
	}

	//Troca status de reserva do endereço
	if($numero_id>0) {
		$sql = "UPDATE tb_cad_os_local_numero SET
				numero_disponivel = 'N'
				WHERE numero_id = '" . $numero_id . "' ";
		$conn->sql($sql);
	}

	// log de alteração de status
	$sql = "INSERT INTO tb_prod_os_status SET
				os_id = '" . $os_id . "',
				status_id = '" . $status_id_novo . "',
				usuario_id = '" . $_SESSION["care-br"]["usuario_id"] . "',
				osstatus_data = '" . getNow($clienteconfig_id) . "',
				osstatus_comentario = 'ANÝLISE TÉCNICA'";
	$conn->sql($sql);
	
	// -------------------------------------------------------
	// gravar variáveis dinânicas de acordo com passo corrente
	// -------------------------------------------------------
	$sql = "SELECT a.clienteconfigpassodicionario_id, a.clienteconfigpasso_id, a.dicionario_id,
				b.dicionario_tipo, b.dicionario_mascara, b.dicionario_validacao, b.ramo_id, b.tipo_id, b.dicionario_os_campo,
				c.idiomadicionario_titulo AS dicionario_titulo, c.idiomadicionario_descricao AS dicionario_descricao, c.idiomadicionario_help AS dicionario_help, c.idiomadicionario_lista AS dicionario_lista
				FROM tb_prod_care_cliente_config_passo_dicionario_dados a
				INNER JOIN tb_cad_dicionario_dados b ON a.dicionario_id = b.dicionario_id
				INNER JOIN tb_cad_idioma_dicionario_dados c ON b.dicionario_id = c.dicionario_id
				INNER JOIN tb_prod_care_cliente_config_passo d ON a.clienteconfigpasso_id = d.clienteconfigpasso_id
				WHERE a.clienteconfigpasso_id IN (SELECT DISTINCT x.clienteconfigpasso_id
														FROM tb_prod_care_cliente_config_passo_dicionario_dados x
														INNER JOIN tb_cad_dicionario_dados y ON x.dicionario_id = y.dicionario_id
														INNER JOIN tb_prod_care_cliente_config_passo z ON x.clienteconfigpasso_id = z.clienteconfigpasso_id
														WHERE z.clienteconfig_id = '" . $clienteconfig_id . "' AND y.status_id = '" . $status_id . "' AND y.dicionario_ativo = 'S')
				AND d.clienteconfig_id = '" . $clienteconfig_id . "' 
				AND b.dicionario_tipo IN ('" . DICIONARIO_DADOS_TIPO_TEXTAREA . "','" . DICIONARIO_DADOS_TIPO_TEXTO . "', '" . DICIONARIO_DADOS_TIPO_DATA . "', '" . DICIONARIO_DADOS_TIPO_LISTA . "', '". DICIONARIO_DADOS_TIPO_MOTIVO . "', '" . DICIONARIO_DADOS_TIPO_SIM_NAO . "', 'os_local') 
				AND b.dicionario_ativo = 'S' 
				AND c.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
				ORDER BY a.clienteconfigpassodicionario_ordem";
	$result_dados = $conn->sql($sql);
	while($tmp_dados = mysqli_fetch_array($result_dados)){
		// tipo do dado diferente de motivo (1 para 1 registro)
		if ($tmp_dados["dicionario_tipo"] != DICIONARIO_DADOS_TIPO_MOTIVO){
			$osclienteconfigpassodicionario_valor = utf8_decode($_REQUEST["clienteconfigpassodicionario_id_" . $tmp_dados["clienteconfigpassodicionario_id"]]);
			$osclienteconfigpassodicionario_id = $_REQUEST["osclienteconfigpassodicionario_id_" . $tmp_dados["clienteconfigpassodicionario_id"]];
			if ($tmp_dados["dicionario_tipo"] == DICIONARIO_DADOS_TIPO_DATA){
				$osclienteconfigpassodicionario_valor = fct_conversorData($osclienteconfigpassodicionario_valor, 3);
				if (trim($osclienteconfigpassodicionario_valor) == "")
					$osclienteconfigpassodicionario_valor = "NULL";
				else
					$osclienteconfigpassodicionario_valor = "'" . $osclienteconfigpassodicionario_valor . "'";
			}else
				$osclienteconfigpassodicionario_valor = "'" . $osclienteconfigpassodicionario_valor . "'";
			
			// caso campo corrente do dicionário esteja associado a uma OS, gravar dados na própria estrutura da OS,
			// caso contrário, gravar na estrutura do dicionário de dados da OS
			if (!empty($tmp_dados["dicionario_os_campo"])){
				$sql = "UPDATE tb_prod_os SET 
							" . $tmp_dados["dicionario_os_campo"] . " = " . $osclienteconfigpassodicionario_valor . " 
							WHERE os_id = '" . $os_id . "'";
				$conn->sql($sql);
				
				if ($tmp_dados["dicionario_os_campo"] == "os_peca_precisa")
					$os_peca_precisa = str_replace("'", "", $osclienteconfigpassodicionario_valor);
				if($tmp_dados["dicionario_os_campo"] == "os_sub_status"){
					$sql = "UPDATE tb_prod_os SET 
						resultado_analise  = " . $osclienteconfigpassodicionario_valor . "
						WHERE os_id = '" . $os_id . "'";
					$conn->sql($sql);
				}					
			}else{
				if (empty($osclienteconfigpassodicionario_id)){
					// inclusão
					$sql = "INSERT INTO tb_prod_os_cliente_config_passo_dicionario_dados SET
								os_id = '" . $os_id . "',
								clienteconfigpassodicionario_id = '" . $tmp_dados["clienteconfigpassodicionario_id"] . "',
								osclienteconfigpassodicionario_valor = " . $osclienteconfigpassodicionario_valor . "";
					$conn->sql($sql);
				}else{
					//alteração
					$sql = "UPDATE tb_prod_os_cliente_config_passo_dicionario_dados SET
								os_id = '" . $os_id . "',
								clienteconfigpassodicionario_id = '" . $tmp_dados["clienteconfigpassodicionario_id"] . "',
								osclienteconfigpassodicionario_valor = " . $osclienteconfigpassodicionario_valor . "
								WHERE osclienteconfigpassodicionario_id = '" . $osclienteconfigpassodicionario_id . "'";
					$conn->sql($sql);
				}
			}
		}
		
		// tipo do dado = motivo (1 para N registros)
		if ($tmp_dados["dicionario_tipo"] == DICIONARIO_DADOS_TIPO_MOTIVO){
			// excluir registros antigos
			$sql = "DELETE FROM tb_prod_os_cliente_config_passo_dicionario_dados
						WHERE os_id = '" . $os_id . "' AND clienteconfigpassodicionario_id = '" . $tmp_dados["clienteconfigpassodicionario_id"] ."'";
			$conn->sql($sql);
			
			// selecionar motivos
			$sql = "SELECT a.motivo_id, a.motivo_exclusivo, a.motivo_cor, a.motivo_flag_observacao, a.motivo_aceita,
							b.idiomamotivo_sigla AS motivo_sigla, b.idiomamotivo_titulo AS motivo_titulo, b.idiomamotivo_descricao AS motivo_descricao
						FROM tb_cad_motivo a
						INNER JOIN tb_cad_idioma_motivo b ON a.motivo_id = b.motivo_id
						WHERE a.tipo_id = '" . $tmp_dados["tipo_id"] . "' AND a.motivo_ativo = 'S' AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] ."'
						ORDER BY b.idiomamotivo_sigla, b.idiomamotivo_titulo";
			$result_filtro = $conn->sql($sql);
			while($tmp_filtro = mysqli_fetch_array($result_filtro)){
				$motivo_id = $_REQUEST["motivo_id_" . $tmp_filtro["motivo_id"]] ;
				if (!empty($motivo_id)){
					// inclusão
					$sql = "INSERT INTO tb_prod_os_cliente_config_passo_dicionario_dados SET
								os_id = '" . $os_id . "',
								clienteconfigpassodicionario_id = '" . $tmp_dados["clienteconfigpassodicionario_id"] . "',
								motivo_id = '" . $motivo_id . "'";
					$conn->sql($sql);
				}
			}
		}
	}
	
	// caso não presice de peça para reparo, remover registros temporários do controle de peças utilizadas
	if ($os_peca_precisa == "N"){
		$sql = "DELETE FROM tb_prod_os_produto_peca
					WHERE os_id = '" . $os_id . "'";
		$conn->sql($sql);
	}

	/*
	// ---------------------------------------------
	// checklist aguarda análise (avaliação técnica)
	// ---------------------------------------------
	$sql = "SELECT a.clienteconfigpassodicionario_id, a.clienteconfigpasso_id, a.dicionario_id,
				b.dicionario_tipo, b.dicionario_mascara, b.dicionario_validacao, b.ramo_id, b.tipo_id, b.dicionario_os_campo,
				c.idiomadicionario_titulo AS dicionario_titulo, c.idiomadicionario_descricao AS dicionario_descricao, c.idiomadicionario_help AS dicionario_help, c.idiomadicionario_lista AS dicionario_lista
				FROM tb_prod_care_cliente_config_passo_dicionario_dados a
				INNER JOIN tb_cad_dicionario_dados b ON a.dicionario_id = b.dicionario_id
				INNER JOIN tb_cad_idioma_dicionario_dados c ON b.dicionario_id = c.dicionario_id
				INNER JOIN tb_prod_care_cliente_config_passo d ON a.clienteconfigpasso_id = d.clienteconfigpasso_id
				WHERE a.clienteconfigpasso_id IN (SELECT DISTINCT x.clienteconfigpasso_id
														FROM tb_prod_care_cliente_config_passo_dicionario_dados x
														INNER JOIN tb_cad_dicionario_dados y ON x.dicionario_id = y.dicionario_id
														INNER JOIN tb_prod_care_cliente_config_passo z ON x.clienteconfigpasso_id = z.clienteconfigpasso_id
														WHERE z.clienteconfig_id = '" . $clienteconfig_id . "' AND y.status_id = '" . $status_id . "' AND y.dicionario_ativo = 'S')
				AND d.clienteconfig_id = '" . $clienteconfig_id . "' 
				AND b.dicionario_tipo = '" . DICIONARIO_DADOS_TIPO_MOTIVO . "' 
				AND b.dicionario_ativo = 'S' 
				AND c.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
				ORDER BY a.clienteconfigpassodicionario_ordem";
	$result_dados = $conn->sql($sql);
	while($tmp_dados = mysqli_fetch_array($result_dados)){
		// excluir registros antigos
		$sql = "DELETE FROM tb_prod_os_cliente_config_passo_dicionario_dados
					WHERE os_id = '" . $os_id . "' AND clienteconfigpassodicionario_id = '" . $tmp_dados["clienteconfigpassodicionario_id"] ."'";
		$conn->sql($sql);
	
		$sql = "SELECT a.motivo_id, a.motivo_exclusivo, a.motivo_cor, a.motivo_flag_observacao, a.motivo_aceita,
					b.idiomamotivo_sigla AS motivo_sigla, b.idiomamotivo_titulo AS motivo_titulo, b.idiomamotivo_descricao AS motivo_descricao
					FROM tb_cad_motivo a
					INNER JOIN tb_cad_idioma_motivo b ON a.motivo_id = b.motivo_id
					WHERE a.tipo_id = '" . $tmp_dados["tipo_id"] . "' AND a.motivo_ativo = 'S' AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] ."'
					ORDER BY b.idiomamotivo_sigla, b.idiomamotivo_titulo";
		$result_filtro = $conn->sql($sql);
		while($tmp_filtro = mysqli_fetch_array($result_filtro)){
			$motivo_id = $_REQUEST["motivo_id_" . $tmp_filtro["motivo_id"]] ;
			if (!empty($motivo_id)){
				// inclusão
				$sql = "INSERT INTO tb_prod_os_cliente_config_passo_dicionario_dados SET
							os_id = '" . $os_id . "',
							clienteconfigpassodicionario_id = '" . $tmp_dados["clienteconfigpassodicionario_id"] . "',
							motivo_id = '" . $motivo_id . "'";
				$conn->sql($sql);
			}
		}
	}
	*/

	// Alterar Sub Status Barrafix quando enviar para Ag. Peca
	if ($clienteconfig_id == '93' && $status_id_novo == 14) {
		$sql = "UPDATE tb_prod_os SET
				os_sub_status = 'Aguardando Compra'
				WHERE os_id = '" . $os_id . "'";
		$conn->sql($sql);
	}

	// Casos do SES onde não passa por Ag. Peça e vai direto para Peça/Reparo
	if ($status_id_novo == 16 && ($clienteconfig_id == 101 || $clienteconfig_id == 110 || $clienteconfig_id == 112 || $clienteconfig_id == 124 || $clienteconfig_id == 133 || (strpos(B2X_SES_OS,"|".$clienteconfig_id."|")>0) ) ) {
		$usuario_id = $_SESSION["care-br"]["usuario_id"];
		$sql = "INSERT INTO tb_prod_os_status SET
			os_id = '$os_id',
			status_id = 14,
			usuario_id = '$usuario_id',
			osstatus_data = NOW(),
			osstatus_comentario = 'Possui saldo em estoque',
			os_sub_status = '$os_sub_status'";
		$conn->sql($sql);
	}
	
	echo fct_get_var('global.php', 'var_msg_os_analise_tecnica_confirmacao', $_SESSION["care-br"]["idioma_id"]) . "!";	
}

// ---------------------------------------
// adicionar peça para conserto do produto
// ---------------------------------------
if ($acao == "add_peca_lista"){
	
	$produto_id_peca = $_REQUEST["produto_id_peca"];
	$clienteconfig_id = $_REQUEST["clienteconfig_id"];
	$os_cobertura = $_REQUEST['os_cobertura'];
    $linha_id = $_REQUEST['linha_id'];
    $peca_complementar = $_REQUEST['peca_complementar'];
    $os_cobertura = $_REQUEST['os_cobertura'];
    
    $sql_insert = '';
    if ($clienteconfig_id == 20 || $clienteconfig_id == 82 || $clienteconfig_id == 123 || $clienteconfig_id == 110 || $clienteconfig_id == 112 || $clienteconfig_id == 101 || $clienteconfig_id == 124 || $clienteconfig_id == 131 || $clienteconfig_id == 133 || (strpos(B2X_SES_OS.B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
        $sql_insert = ", osprodutopeca_cobrar = 'S'";
    }

    $sql_complementar = "";
    if ($peca_complementar == 'Sim') {
        $sql_complementar = ", osprodutopeca_complementar = 'S'";
	}
	
	//Verificar se j� existe uma pe�a com cobrar sim, se n�o tiver, adicionar um servi�o de M�o de Obra
	$sql = "SELECT osprodutopeca_id FROM tb_prod_os_produto_peca WHERE os_id = '$os_id' AND osprodutopeca_cobrar = 'S'";
	$res = $conn->sql($sql);
	if (mysqli_num_rows($res) == 0) {
		$cobertura = trim(substr($os_cobertura, 0, 3));
		$sql = "SELECT servico_id, valor FROM tb_cad_servico WHERE clienteconfig_id = '$clienteconfig_id' AND titulo = 'Mao de Obra - $cobertura' LIMIT 1";
		$res = $conn->sql($sql);
		$dados = mysqli_fetch_array($res);
		$sql = "SELECT * FROM tb_prod_os_servico WHERE os_id = '$os_id' AND servico_id = '{$dados["servico_id"]}'";
		$res = $conn->sql($sql);
		if (mysqli_num_rows($res) == 0) {
			$sql = "INSERT INTO tb_prod_os_servico 
				SET os_id = '$os_id',
				servico_id = '{$dados["servico_id"]}',
				osservico_valor = '{$dados["valor"]}',
				osservico_desconto = 0";
			$conn->sql($sql);
		}
	}

	$sql = "INSERT INTO tb_prod_os_produto_peca SET
				os_id = '" . $os_id . "',
				produto_id = '" . $produto_id . "',
                produto_id_peca = '" . $produto_id_peca . "'
                $sql_insert $sql_complementar";
	$conn->sql($sql);
	$id_peca = $conn->id();

	$sql_log = "INSERT INTO tb_prod_log_peca (os_id, status, usuario_id, peca_log_data, osprodutopeca_id, produto_id_peca) VALUES ('".$os_id."', 'INSERIR', '" . $_SESSION["care-br"]["usuario_id"] . "', '" . getNow($clienteconfig_id) . "', '" . $id_peca . "', '". $produto_id_peca . "')";
	$conn->sql($sql_log);
	
	if($clienteconfig_id == '11'){
		//Calcula o valor total das peças com ipi e sem percorrendo toda  a tabela de tb_prod_os_produto_peca
		$sql_os_peca = "Select produto_id, produto_id_peca,osprodutopeca_qtde from tb_prod_os_produto_peca where os_id = '" . $os_id . "'";  	
	
		$result_filtro_peca = $conn->sql($sql_os_peca);
		$valor_total_peca 	= "0.00";
		$os_id_dt 		 	= "";
		while($tmp_filtro_peca = mysqli_fetch_array($result_filtro_peca)){
			//Verifica se a peça tem ipi
		 	$sql_peca_ipi 		= "Select produto_valor_fabrica_com_ipi from tb_cad_produto 
										where produto_id ='". $tmp_filtro_peca['produto_id_peca']."'";
			$result_filtro_peca_ipi = $conn->sql($sql_peca_ipi);
			
			$os_id_dt .= $tmp_filtro_peca['produto_id_peca']."|";
			while($tmp_filtro_peca_ipi = mysqli_fetch_array($result_filtro_peca_ipi)){

				if($tmp_filtro_peca_ipi ['produto_valor_fabrica_com_ipi'] <> '0.00'){
					$valor_total_peca += ($tmp_filtro_peca_ipi ['produto_valor_fabrica_com_ipi'] * $tmp_filtro_peca['osprodutopeca_qtde']);
						

				}else{
					$sql_peca_unit = "Select d.ide_dEmi, f.det_prod_vUnCom
							From tb_prod_nfe d
							INNER	JOIN tb_prod_nfe_item f
							on f.nfe_id = d.nfe_id
							and produto_id = '". $tmp_filtro_peca['produto_id_peca']."'
							and (d.clienteconfig_id = '".$clienteconfig_id. "' or d.clienteconfig_id = 7)
							order by nfi_id desc limit 1";
					$result_filtro_peca_unitario = $conn->sql($sql_peca_unit);
					
					while($tmp_filtro_peca_unit = mysqli_fetch_array($result_filtro_peca_unitario)){
						$valor_total_peca += ($tmp_filtro_peca_unit ['det_prod_vUnCom']  * $tmp_filtro_peca['osprodutopeca_qtde']);
					}

					
				}
			}
		}
		//Pega a data minima da nf da peças
		
		$os_dt = substr($os_id_dt,0,-1);
		$os_dt = str_replace("|",",",$os_dt);

		$sql_dt_min_pecas = "Select d.clienteconfig_id,Max( d.ide_dEmi) as max_dt_peca, MIn(d.ide_dEmi) as min_dt_peca, f.det_prod_vUnCom, produto_id
							 from tb_prod_nfe d
							INNER JOIN tb_prod_nfe_item f
							on  f.nfe_id = d.nfe_id
							and produto_id in($os_dt)
							and (d.clienteconfig_id = 11 or d.clienteconfig_id = 7)
							order by nfi_id desc 
							limit 1";
		$result_filtro_dt_peca_ = $conn->sql($sql_dt_min_pecas);
					
		while($tmp_filtro_dt_peca = mysqli_fetch_array($result_filtro_dt_peca_)){
				$dt_mim_nf_peca = $tmp_filtro_dt_peca['max_dt_peca'];
			
		}
		
		//Insere o valor total de pecas na coluna da tb_prod_os
		$sql_update_valor_pecas = "Update tb_prod_os set os_valor_pecas ='".$valor_total_peca."', data_nf_pecas='".$dt_mim_nf_peca."' where os_id= '".$os_id."'";
		$conn->sql($sql_update_valor_pecas);	

		
	}

	
	//funcao que reserva a peça
	if( ($os_cobertura <> 'ORCAMENTO') && ($os_cobertura <> utf8_encode('Orçamento')) && !strstr($os_cobertura,"ESTENDIDA") ){
		ReservaPecaOS($produto_id_peca,$clienteconfig_id,$os_id,1);
	}
}

// ---------------------------------------------------
// alterar peça para conserto do produto já adicionada
// ---------------------------------------------------
if ($acao == "updt_peca_lista"){
	$produto_id_peca = $_REQUEST["produto_id_peca"];
	$os_cobertura = $_REQUEST['os_cobertura'];
	$qtde_utilizada_baixada = $_REQUEST["qtde_utilizada_baixada"];
	$qtde_defeito_baixada = $_REQUEST["qtde_defeito_baixada"];
	$qtde_laboratorio = $_REQUEST["qtde_laboratorio"];
	$estoque_id = $_REQUEST["estoque_id"];
	$qtde_reservada = $_REQUEST["qtde_reservada"];
    $qtde_nao_utilizada = $_REQUEST["qtde_nao_utilizada"];

	$sql = "UPDATE tb_prod_os_produto_peca SET
				osprodutopeca_qtde = '" . $osprodutopeca_qtde . "',
				osprodutopeca_cobrar = '" . $osprodutopeca_cobrar . "'
				WHERE osprodutopeca_id = '" . $osprodutopeca_id . "'";
    $conn->sql($sql);
    
    // B2X Moema / Fortaleza / SES grava os valores da peça no Análise
    if ($clienteconfig_id == 101 || $clienteconfig_id == 110 || $clienteconfig_id == 112 || $clienteconfig_id == 123 || $clienteconfig_id == 124 || $clienteconfig_id == 131 || $clienteconfig_id == 133 || (strpos(B2X_SES_OS.B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
        $osprodutopeca_valor_venda = str_replace('.', '', $osprodutopeca_valor_venda);
        $osprodutopeca_valor_venda = str_replace(',', '.', $osprodutopeca_valor_venda);
        $osprodutopeca_valor_mao_obra = str_replace('.', '', $osprodutopeca_valor_mao_obra);
		$osprodutopeca_valor_mao_obra = str_replace(',', '.', $osprodutopeca_valor_mao_obra);
		// M�o de Obra na tb_prod_os_produto_peca ser� sempre zerada
		$osprodutopeca_valor_mao_obra = '0.00';
        $osprodutopeca_desconto = str_replace('.', '', $osprodutopeca_desconto);
        $osprodutopeca_desconto = str_replace(',', '.', $osprodutopeca_desconto);
        if ($osprodutopeca_cobrar == 'N' || $osprodutopeca_cobrar == 'cortesia') {
            $osprodutopeca_valor_venda = '0.00';
            $osprodutopeca_valor_mao_obra = '0.00';
            $osprodutopeca_desconto = '0.00';
        }
        $sql = "UPDATE tb_prod_os_produto_peca SET
            osprodutopeca_valor_venda = '$osprodutopeca_valor_venda',
            osprodutopeca_valor_mao_obra = '$osprodutopeca_valor_mao_obra',
            osprodutopeca_valor_desconto = '$osprodutopeca_desconto'
            WHERE osprodutopeca_id = '$osprodutopeca_id'";
        $conn->sql($sql);
    }
	
	if($clienteconfig_id == '11'){
		//Calcula o valor total das peças com ipi e sem percorrendo toda  a tabela de tb_prod_os_produto_peca
		$sql_os_peca = "Select produto_id, produto_id_peca,osprodutopeca_qtde from tb_prod_os_produto_peca where os_id = '" . $os_id . "'";  	
	
		$result_filtro_peca = $conn->sql($sql_os_peca);
		$valor_total_peca 	= "0.00";
		$os_id_dt 		 	= "";
		while($tmp_filtro_peca = mysqli_fetch_array($result_filtro_peca)){
			//Verifica se a peça tem ipi
		 	$sql_peca_ipi 		= "Select produto_valor_fabrica_com_ipi from tb_cad_produto 
										where produto_id ='". $tmp_filtro_peca['produto_id_peca']."'";
			$result_filtro_peca_ipi = $conn->sql($sql_peca_ipi);
			
			$os_id_dt .= $tmp_filtro_peca['produto_id_peca']."|";
			while($tmp_filtro_peca_ipi = mysqli_fetch_array($result_filtro_peca_ipi)){

				if($tmp_filtro_peca_ipi ['produto_valor_fabrica_com_ipi'] <> '0.00'){
					$valor_total_peca += ($tmp_filtro_peca_ipi ['produto_valor_fabrica_com_ipi'] * $tmp_filtro_peca['osprodutopeca_qtde']);
						

				}else{
					$sql_peca_unit = "Select d.ide_dEmi, f.det_prod_vUnCom
							From tb_prod_nfe d
							INNER	JOIN tb_prod_nfe_item f
							on f.nfe_id = d.nfe_id
							and produto_id = '". $tmp_filtro_peca['produto_id_peca']."'
							and (d.clienteconfig_id = '".$clienteconfig_id. "' or d.clienteconfig_id = 7)
							order by nfi_id desc limit 1";
					$result_filtro_peca_unitario = $conn->sql($sql_peca_unit);
					
					while($tmp_filtro_peca_unit = mysqli_fetch_array($result_filtro_peca_unitario)){
						$valor_total_peca += ($tmp_filtro_peca_unit ['det_prod_vUnCom']  * $tmp_filtro_peca['osprodutopeca_qtde']);
					}

					
				}
			}
		}
		//Pega a data minima da nf da peças
		
		$os_dt = substr($os_id_dt,0,-1);
		$os_dt = str_replace("|",",",$os_dt);

		$sql_dt_min_pecas = "Select d.clienteconfig_id,Max( d.ide_dEmi) as max_dt_peca, MIn(d.ide_dEmi) as min_dt_peca, f.det_prod_vUnCom, produto_id
							 from tb_prod_nfe d
							INNER JOIN tb_prod_nfe_item f
							on  f.nfe_id = d.nfe_id
							and produto_id in($os_dt)
							and (d.clienteconfig_id = 11 or d.clienteconfig_id = 7)
							order by nfi_id desc 
							limit 1";
		$result_filtro_dt_peca_ = $conn->sql($sql_dt_min_pecas);
					
		while($tmp_filtro_dt_peca = mysqli_fetch_array($result_filtro_dt_peca_)){
				$dt_mim_nf_peca = $tmp_filtro_dt_peca['max_dt_peca'];
			
		}
		
		//Insere o valor total de pecas na coluna da tb_prod_os
		$sql_update_valor_pecas = "Update tb_prod_os set os_valor_pecas ='".$valor_total_peca."', data_nf_pecas='".$dt_mim_nf_peca."' where os_id= '".$os_id."'";
		$conn->sql($sql_update_valor_pecas);	

		
	}


    $estoque_id = $_REQUEST["estoque_id"];
    $os_id = $_REQUEST["os_id"];
    $usar_reserva = '';
    if(empty($os_cobertura)){
		
		if ($osprodutopeca_cobrar == "N"){
			$os_cobertura = 'GARANTIA';
		}elseif($osprodutopeca_cobrar == "S"){
			$os_cobertura = 'ORCAMENTO';
		}

		$usar_reserva = 'S';//para verificar reserva de estoque padrao maxxlog
	}

    //funcao que reserva a peça
    if( ($os_cobertura <> 'ORCAMENTO') && ($os_cobertura <> utf8_encode('Orçamento')) && !strstr($os_cobertura,"ESTENDIDA") ){
    	if(!empty($produto_id_peca) && !empty($estoque_id)){
    		//ReservaPecaOS($produto_id_peca,$clienteconfig_id,$os_id,$osprodutopeca_qtde,$osprodutopeca_id);
    		$diff_baixa = $qtde_utilizada_baixada + $qtde_laboratorio;
    		$total = $diff_baixa + $qtde_reservada;

    		//TRATAR RESERVADA
			$diff_quantidade = ($osprodutopeca_qtde - $total) + $qtde_defeito_baixada + $qtde_nao_utilizada;
			
    		if($diff_quantidade < 0){ //retira peça

    			if($diff_baixa > 0){//peças ja sairam do estoque

    				$diff_quantidade_mais = str_replace("-", "", $diff_quantidade);

    				//echo 'peças ja sairam do estoque!';
    				$sql_log = "INSERT INTO tb_prod_estoque_status (os_id, estoque_id, status_id, qtde , data_status , comentario) 
							VALUES ('".$os_id."', '".$estoque_id."', '71', ".$diff_quantidade_mais." , NOW() , 'NAO UTILIZADO')";
					$conn->sql($sql_log);


					if($qtde_laboratorio > 0 ){

						$diff_quantidade = $diff_quantidade + $qtde_laboratorio;// se for == 0

						$diff_lab = $diff_quantidade - $qtde_laboratorio;  
						//$diff_quantidade = $diff_quantidade - $diff_quantidade_nova;
						$sql_log = "INSERT INTO tb_prod_estoque_status (os_id, estoque_id, status_id, qtde , data_status , comentario) 
							VALUES ('".$os_id."', '".$estoque_id."', '".STATUS_ESTOQUE_LABORATORIO."', ".$diff_lab." , NOW() , 'RETIRADA DA OS')";
						$conn->sql($sql_log);

					}

					if($qtde_utilizada_baixada > 0 ){

						if($diff_quantidade < 0 ){
								$sql_log = "INSERT INTO tb_prod_estoque_status (os_id, estoque_id, status_id, qtde , data_status , comentario) 
								VALUES ('".$os_id."', '".$estoque_id."', '".STATUS_ESTOQUE_UTILIZADA."', ".$diff_quantidade." , NOW() , 'RETIRADA DA OS')";
								$conn->sql($sql_log);
						}
					}

    			}else{ //peças ja reservadas ** retira a reserva

	    			$sql_log = "INSERT INTO tb_prod_estoque_status (os_id, estoque_id, status_id, qtde , data_status , comentario) 
							VALUES ('".$os_id."', '".$estoque_id."', '".STATUS_ESTOQUE_RESERVADA."', ".$diff_quantidade." , NOW() , 'RESERVA')";
					$conn->sql($sql_log);

					//desconta pq esta reservada
					if($usar_reserva == 'S'){
						$diff_quantidade = str_replace("-", "", $diff_quantidade);
	    				//RETORNA DISPONIVEL
						$sql_disponivel = "INSERT INTO tb_prod_estoque_status SET
											estoque_id = '" . $estoque_id. "',
											status_id = '" . STATUS_ESTOQUE_DISPONIVEL . "',
											data_status = NOW(),
											os_id = ".$os_id.",
											comentario = 'RETORNO OS $os_id',
											qtde = '" . $diff_quantidade . "'";
						$conn->sql($sql_disponivel);
	    			}
    			}

    		}elseif($diff_quantidade > 0){ //adiciona reserva

				//desconta pq esta reservada
				if($usar_reserva == 'S'){

					//quantidade em estoque
					$qtd_estoque = qtdEstoqueStatus($estoque_id,STATUS_ESTOQUE_DISPONIVEL);
					
					if($qtd_estoque > 0){

						//reserva peça
						insereReserva($os_id,$estoque_id,$diff_quantidade);

	    				//DESCONTA DISPONIVEL
						retiraDisponivel($os_id,$estoque_id,$diff_quantidade);
					}
					
    			}else{
    				//reserva peça
					insereReserva($os_id,$estoque_id,$diff_quantidade);
    			}
    		}
    	}
	}
}

// --------------------------
// excluir peça já adicionada
// --------------------------
if ($acao == "dlt"){

	$produto_id_peca = $_REQUEST["produto_id_peca"];
	$clienteconfig_id = $_REQUEST["clienteconfig_id"];
	$qtde_utilizada_baixada = $_REQUEST["qtde_utilizada_baixada"];
	$qtde_defeito_baixada = $_REQUEST["qtde_defeito_baixada"];
	$qtde_laboratorio = $_REQUEST["qtde_laboratorio"];
	$estoque_id = $_REQUEST["estoque_id"];

	$sql = "DELETE 
				FROM tb_prod_os_produto_peca
				WHERE osprodutopeca_id = '" . $osprodutopeca_id . "'";
	$conn->sql($sql);
    
	//remove peca config estoque
	RemovePecaEstoqueOS($os_id,$estoque_id,$qtde_utilizada_baixada,$qtde_laboratorio);
	
	$sql_log = "INSERT INTO tb_prod_log_peca (os_id, status, usuario_id, peca_log_data, osprodutopeca_id, produto_id_peca) VALUES ('".$os_id."', 'DELETE', '" . $_SESSION["care-br"]["usuario_id"] . "', '" . getNow($clienteconfig_id) . "', '" . $osprodutopeca_id . "', '". $produto_id_peca . "')";
	$conn->sql($sql_log);
	
	if($clienteconfig_id == '11'){
		//Calcula o valor total das peças com ipi e sem percorrendo toda  a tabela de tb_prod_os_produto_peca
		$sql_os_peca = "Select produto_id, produto_id_peca,osprodutopeca_qtde from tb_prod_os_produto_peca where os_id = '" . $os_id . "'";  	
	
		$result_filtro_peca = $conn->sql($sql_os_peca);
		$valor_total_peca 	= "0.00";
		$os_id_dt 		 	= "";
		while($tmp_filtro_peca = mysqli_fetch_array($result_filtro_peca)){
			//Verifica se a peça tem ipi
		 	$sql_peca_ipi 		= "Select produto_valor_fabrica_com_ipi from tb_cad_produto 
										where produto_id ='". $tmp_filtro_peca['produto_id_peca']."'";
			$result_filtro_peca_ipi = $conn->sql($sql_peca_ipi);
			
			$os_id_dt .= $tmp_filtro_peca['produto_id_peca']."|";
			while($tmp_filtro_peca_ipi = mysqli_fetch_array($result_filtro_peca_ipi)){

				if($tmp_filtro_peca_ipi ['produto_valor_fabrica_com_ipi'] <> '0.00'){
					$valor_total_peca += ($tmp_filtro_peca_ipi ['produto_valor_fabrica_com_ipi'] * $tmp_filtro_peca['osprodutopeca_qtde']);
						

				}else{
					$sql_peca_unit = "Select d.ide_dEmi, f.det_prod_vUnCom
							From tb_prod_nfe d
							INNER	JOIN tb_prod_nfe_item f
							on f.nfe_id = d.nfe_id
							and produto_id = '". $tmp_filtro_peca['produto_id_peca']."'
							and (d.clienteconfig_id = '".$clienteconfig_id. "' or d.clienteconfig_id = 7)
							order by nfi_id desc limit 1";
					$result_filtro_peca_unitario = $conn->sql($sql_peca_unit);
					
					while($tmp_filtro_peca_unit = mysqli_fetch_array($result_filtro_peca_unitario)){
						$valor_total_peca += ($tmp_filtro_peca_unit ['det_prod_vUnCom']  * $tmp_filtro_peca['osprodutopeca_qtde']);
					}

					
				}
			}
		}
		//Pega a data minima da nf da peças
		
		$os_dt = substr($os_id_dt,0,-1);
		$os_dt = str_replace("|",",",$os_dt);

		$sql_dt_min_pecas = "Select d.clienteconfig_id,Max( d.ide_dEmi) as max_dt_peca, MIn(d.ide_dEmi) as min_dt_peca, f.det_prod_vUnCom, produto_id
							 from tb_prod_nfe d
							INNER JOIN tb_prod_nfe_item f
							on  f.nfe_id = d.nfe_id
							and produto_id in($os_dt)
							and (d.clienteconfig_id = 11 or d.clienteconfig_id = 7)
							order by nfi_id desc 
							limit 1";
		$result_filtro_dt_peca_ = $conn->sql($sql_dt_min_pecas);
					
		while($tmp_filtro_dt_peca = mysqli_fetch_array($result_filtro_dt_peca_)){
				$dt_mim_nf_peca = $tmp_filtro_dt_peca['max_dt_peca'];
			
		}
		
		//Insere o valor total de pecas na coluna da tb_prod_os
		$sql_update_valor_pecas = "Update tb_prod_os set os_valor_pecas ='".$valor_total_peca."', data_nf_pecas='".$dt_mim_nf_peca."' where os_id= '".$os_id."'";
		$conn->sql($sql_update_valor_pecas);	

		
	}
	
}


if ($acao == "add_produto_emprestimo"){
	$return = 'false';
	$sql = "SELECT id_emprestimo FROM tb_prod_os_emprestimo WHERE os_id != '' AND id_emprestimo = '" . $produto_id_emprestimo ."' AND os_id != '" . $os_id . "'" ;
	$result_verificar_emprestimo = $conn->sql($sql);
	if(mysqli_num_rows($result_verificar_emprestimo)>0){
		echo $return;
		return;
	}
	$sql = "UPDATE tb_prod_os_emprestimo SET emprestimo_optou = NULL, emprestimo_data = NULL, os_id = NULL WHERE os_id = '" . $os_id . "'";
	$conn->sql($sql);	
	$sql = "UPDATE tb_prod_os_emprestimo SET emprestimo_optou = 'SIM', emprestimo_data = '" . getNow($clienteconfig_id) . "', os_id = " . $os_id . " WHERE id_emprestimo = '" . $produto_id_emprestimo ."'" ;				
	$conn->sql($sql);
}

if ($acao == "retirar_produto_emprestimo"){
	$sql = "UPDATE tb_prod_os_emprestimo SET emprestimo_optou = NULL, emprestimo_data = NULL, os_id = NULL WHERE os_id = '" . $os_id . "'";
	$conn->sql($sql);
}


// ----------------------------------------
// adicionar serviço para reparo do produto
// ----------------------------------------
if ($acao == "add_servico_lista"){

	$sql = "INSERT INTO tb_prod_os_servico SET
				os_id = '$os_id',
				servico_id = '$servico_id',
				osservico_desconto = '0',
				osservico_valor = (SELECT valor
										FROM tb_cad_servico a
										WHERE a.servico_id = '$servico_id' LIMIT 0,1)";
	$conn->sql($sql);
}

// ----------------------------------------------------
// alterar serviço para reparo do produto já adicionado
// ----------------------------------------------------
if ($acao == "updt_servico_lista"){
	$sql = "UPDATE tb_prod_os_servico SET
				osservico_valor = '" . $osservico_valor . "',
				osservico_desconto = '" . $osservico_desconto . "'
				WHERE osservico_id = '" . $osservico_id . "'";
	$conn->sql($sql);

	$sql = "select os_id from tb_prod_os_servico where osservico_id = '$osservico_id'";
	$os_id = $conn->getData($sql)[0]['os_id'];

	if($os_id > 0){
		// atualiza tb_prod_os
		$sql = "select 
			SUM(osprodutopeca_valor_venda) as valor_venda,
			SUM(osprodutopeca_valor_desconto) as desconto
			from tb_prod_os_produto_peca 
			where os_id = '$os_id' and osprodutopeca_cobrar = 'S'";
		$pecas = $conn->getData($sql)[0];

		$sql = "select
		SUM(osservico_valor) as valor_mo,
		SUM(osservico_desconto) as desconto
		FROM tb_prod_os_servico
		where os_id = '$os_id'";
		$mo = $conn->getData($sql)[0];

		$os_valor_pecas = (float) $pecas['valor_venda'];
		$os_desconto_pecas = (float) $pecas['desconto'];
		$os_valor_maodeobra = (float) $mo['valor_mo'];
		$os_desconto_maodeobra = (float) $mo['desconto'];
		$os_desconto_total = $os_desconto_pecas + $os_desconto_maodeobra;
		$os_valor_liquido_pecas = $os_valor_pecas - $os_desconto_pecas;
		$os_valor_liquido_maodeobra = $os_valor_maodeobra - $os_desconto_maodeobra;
		$os_valor_liquido = $os_valor_liquido_pecas + $os_valor_liquido_maodeobra;

		$sql = "update tb_prod_os set 
		os_valor_pecas = '$os_valor_pecas',
		os_desconto_pecas = '$os_desconto_pecas',
		os_valor_maodeobra = '$os_valor_maodeobra',
		os_desconto_maodeobra = '$os_desconto_maodeobra',
		os_desconto_total = '$os_desconto_total',
		os_valor_liquido_pecas = '$os_valor_liquido_pecas',
		os_valor_liquido_maodeobra = '$os_valor_liquido_maodeobra',
		os_valor_liquido = '$os_valor_liquido'
		where os_id = $os_id";

		$conn->sql($sql);	
	}
}

// -----------------------------
// excluir serviço já adicionado
// -----------------------------
if ($acao == "dlt_servico_lista"){
	$sql = "DELETE 
				FROM tb_prod_os_servico
				WHERE osservico_id = '" . $osservico_id . "'";
	$conn->sql($sql);
}

?>

<?
$conn->fechar();
?>
