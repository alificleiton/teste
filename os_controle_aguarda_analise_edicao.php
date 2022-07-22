<?
//$allowSession = "nao";
require_once("lib/configs.php");
require_once("multi_idioma_request.php");

// setar sessão com nome da página para ser usada no controle de acesso
$_SESSION["care-br"]["submodulo_pagina"] = "os_controle_aguarda_analise.php";

// request
$acao = $_REQUEST["acao"];
$cliente_id = $_REQUEST["cliente_id"];
$clienteconfig_id = $_REQUEST["clienteconfig_id"];
$status_id = $_REQUEST["status_id"];
$os_id = $_REQUEST["os_id"];
$produto_id = $_REQUEST["produto_id"];
$motivo_aceita = $_REQUEST["motivo_aceita"];
$os_cobertura = trim($_REQUEST["os_cobertura"]);
$os_cobertura = str_replace(" ", "", $os_cobertura);
$os_tipo_servico = $_REQUEST["os_tipo_servico"];
$os_peca_precisa = $_REQUEST["os_peca_precisa"];
$os_sub_status = trim($_REQUEST["os_sub_status"]);
$os_cobertura_aux = $os_cobertura;
$desconto = $_REQUEST["desconto"];

?>
<style type="text/css">
	.alert {
	    padding: 20px;
	    /*background-color: #f44336;
	    color: white;*/
	    opacity: 1;
	    /*transition: opacity 0.6s;
	    margin-bottom: 15px;*/

	    color: #004085;
    	background-color: #cce5ff;
    	border-color: #b8daff;
	}

	.alert.info {/*background-color: #2196F3;*/background-color: #cce5ff;}
</style>

<?php	

$limit = '0,3';

if($clienteconfig_id == 17) $limit = '0,2';
if($clienteconfig_id == 23) $limit = '0,4';
if($clienteconfig_id == 20) $limit = '0,1';


// busca da grid
$grid_busca = utf8_decode($_REQUEST["grid_busca"]);

// dados da OS
$sql = "SELECT a.*,
			DATEDIFF(NOW(), a.os_data_abertura) AS os_idade,
			c.idiomastatus_titulo AS status_titulo, c.idiomastatus_descricao AS status_descricao,
			d.usuario_nome,
			e.empresa_nome_fantasia AS empresa_nome_fantasia_varejo, e.empresa_razao_social AS empresa_razao_social_varejo,
			g.idiomaproduto_titulo AS produto_titulo, g.idiomaproduto_descricao AS produto_descricao,
			h.empresa_nome_fantasia AS empresa_nome_fantasia_fabricante, h.empresa_razao_social AS empresa_razao_social_fabricante,
			j.idiomalinha_titulo AS linha_titulo, j.idiomalinha_descricao AS linha_descricao
			FROM tb_prod_os a
			INNER JOIN tb_cad_status b ON a.status_id = b.status_id
			INNER JOIN tb_cad_idioma_status c ON b.status_id = c.status_id AND c.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
			INNER JOIN tb_cad_usuario d ON a.usuario_id = d.usuario_id
			INNER JOIN tb_cad_empresa e ON a.empresa_id_varejo = e.empresa_id
			INNER JOIN tb_cad_produto f ON a.produto_id = f.produto_id
			INNER JOIN tb_cad_idioma_produto g ON f.produto_id = g.produto_id AND g.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
			INNER JOIN tb_cad_empresa h ON f.empresa_id = h.empresa_id
			INNER JOIN tb_cad_linha i ON f.linha_id = i.linha_id
			INNER JOIN tb_cad_idioma_linha j ON i.linha_id = j.linha_id AND j.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
			INNER JOIN tb_prod_perfil_status k ON a.status_id = k.status_id AND k.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
			INNER JOIN tb_prod_perfil_empresa l ON a.empresa_id_fabricante = l.empresa_id AND l.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
			INNER JOIN tb_prod_perfil_linha m ON a.linha_id = m.linha_id AND m.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
            WHERE os_id = '" . $os_id . "'";
// B2X Moema / Fortaleza / SES
if ($clienteconfig_id == 101 || $clienteconfig_id == 110 || $clienteconfig_id == 112 || $clienteconfig_id == 123 || $clienteconfig_id == 124 || $clienteconfig_id == 131 || $clienteconfig_id == 133 || (strpos(B2X_SES_OS.B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
    $sql = "SELECT a.*,
			DATEDIFF(NOW(), a.os_data_abertura) AS os_idade,
			c.idiomastatus_titulo AS status_titulo, c.idiomastatus_descricao AS status_descricao,
			d.usuario_nome,
			e.empresa_nome_fantasia AS empresa_nome_fantasia_varejo, e.empresa_razao_social AS empresa_razao_social_varejo,
			g.idiomaproduto_titulo AS produto_titulo, g.idiomaproduto_descricao AS produto_descricao,
			h.empresa_nome_fantasia AS empresa_nome_fantasia_fabricante, h.empresa_razao_social AS empresa_razao_social_fabricante,
			j.idiomalinha_titulo AS linha_titulo, j.idiomalinha_descricao AS linha_descricao
			FROM tb_prod_os a
			INNER JOIN tb_cad_status b ON a.status_id = b.status_id
			INNER JOIN tb_cad_idioma_status c ON b.status_id = c.status_id AND c.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
			INNER JOIN tb_cad_usuario d ON a.usuario_id = d.usuario_id
			LEFT JOIN tb_cad_empresa e ON a.empresa_id_varejo = e.empresa_id
			INNER JOIN tb_cad_produto f ON a.produto_id = f.produto_id
			INNER JOIN tb_cad_idioma_produto g ON f.produto_id = g.produto_id AND g.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
			INNER JOIN tb_cad_empresa h ON f.empresa_id = h.empresa_id
			INNER JOIN tb_cad_linha i ON f.linha_id = i.linha_id
			INNER JOIN tb_cad_idioma_linha j ON i.linha_id = j.linha_id AND j.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
			INNER JOIN tb_prod_perfil_status k ON a.status_id = k.status_id AND k.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
			INNER JOIN tb_prod_perfil_empresa l ON a.empresa_id_fabricante = l.empresa_id AND l.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
			INNER JOIN tb_prod_perfil_linha m ON a.linha_id = m.linha_id AND m.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
            WHERE os_id = '" . $os_id . "'";
}

$result_cadastro = $conn->sql($sql);
while($tmp_cadastro = mysqli_fetch_array($result_cadastro)){
    $dados_cadastro[] = $tmp_cadastro;
}

//pesquisa se NF de serviço foi emitida
$sqlnf='select os_id from tb_prod_nfe where os_id='.$os_id." and ide_natOp = 'Venda de Mercadoria' and status_id<>52";
$resnf=$conn->sql($sqlnf);
$nf='';
if (mysqli_num_rows($resnf)>0){
    $nf='NF-e de venda gerada. Cancele a nota para editar/inserir item!<br>';
}

//pesquisa se NF de serviço foi emitida
$sqlnfs='select os_id from tb_prod_nfe_servico where os_id='.$os_id." and status_id<>52";
$resnfs=$conn->sql($sqlnfs);
$nfs='';
if (mysqli_num_rows($resnfs)>0){
  $nfs='NFS-e Gerada. Cancele a nota para editar o valor!';
}

// --------------
// get_peca_lista
// --------------
if ($acao == "get_peca_lista"){
	
	if($clienteconfig_id == '11'){
		// obter peças já solicitadas
		$sql = "SELECT (SELECT nf_item.det_prod_vUnCom FROM tb_prod_nfe_item nf_item 
			INNER JOIN tb_prod_nfe nf
			ON nf.nfe_id = nf_item.nfe_id
		and ( nf.clienteconfig_id = '$clienteconfig_id' or nf.clienteconfig_id = 7),
			(SELECT estoque.estoque_valor_compra FROM tb_prod_estoque estoque WHERE a.produto_id_peca = estoque.produto_id AND estoque.empresacliente_id = (SELECT empresacliente_id FROM tb_prod_care_cliente_config d WHERE d.clienteconfig_id = $clienteconfig_id) ORDER BY estoque.estoque_id DESC limit 1) AS 'valor_unitario_te'
			WHERE a.produto_id_peca = nf_item.produto_id ORDER BY nf_item.nfe_id DESC limit 1)	AS 'valor_unitario',
					a.*,
					b.produto_codigo,
					b.linha_id,
					b.produto_valor_fab,
					b.produto_valor_fabrica_com_ipi,
					b.produto_titulo AS produto_titulo, b.produto_descricao AS produto_descricao
					FROM tb_prod_os_produto_peca a
					INNER JOIN tb_cad_produto b ON a.produto_id_peca = b.produto_id
					WHERE a.os_id = '" . $os_id . "' AND a.produto_id = '" . $produto_id . "'
					ORDER BY b.produto_descricao";
		//echo $sql;
		$result_filtro = $conn->sql($sql);		
		
	}else{
        $order = "ORDER BY b.produto_descricao";
        if ($clienteconfig_id == 101 || $clienteconfig_id == 110 || $clienteconfig_id == 112 || $clienteconfig_id == 123 || $clienteconfig_id == 124 || $clienteconfig_id == 131 || $clienteconfig_id == 133 || (strpos(B2X_SES_OS.B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) 
            $order = "ORDER BY a.osprodutopeca_id";
        
		// obter peças já solicitadas
		/*$sql = "SELECT (SELECT nf_item.det_prod_vUnCom FROM tb_prod_nfe_item nf_item 
			INNER JOIN tb_prod_nfe nf
			ON nf.nfe_id = nf_item.nfe_id
		and  nf.clienteconfig_id = '$clienteconfig_id' 
			 WHERE a.produto_id_peca = nf_item.produto_id ORDER BY nf_item.nfe_id DESC limit 1)	AS 'valor_unitario',
					(SELECT estoque.estoque_valor_compra FROM tb_prod_estoque estoque WHERE a.produto_id_peca = estoque.produto_id AND estoque.empresacliente_id = (SELECT empresacliente_id FROM tb_prod_care_cliente_config d WHERE d.clienteconfig_id = $clienteconfig_id) ORDER BY estoque.estoque_id DESC limit 1) AS 'valor_unitario_te',
					a.*,
					b.produto_codigo,
					b.linha_id,
					b.produto_valor_fab,
					b.produto_valor_fabrica_com_ipi,
					b.produto_titulo AS produto_titulo, b.produto_descricao AS produto_descricao
					FROM tb_prod_os_produto_peca a
					INNER JOIN tb_cad_produto b ON a.produto_id_peca = b.produto_id
					WHERE a.os_id = '" . $os_id . "' AND a.produto_id = '" . $produto_id . "'
					$order";*/

		$sql = "SELECT 0 as 'valor_unitario',
					0 AS 'valor_unitario_te',
					a.*,
					b.produto_codigo,
					b.linha_id,
					b.produto_valor_fab,
					b.produto_valor_fabrica_com_ipi,
					b.produto_titulo AS produto_titulo, b.produto_descricao AS produto_descricao
					FROM tb_prod_os_produto_peca a
					INNER JOIN tb_cad_produto b ON a.produto_id_peca = b.produto_id
					WHERE a.os_id = '" . $os_id . "' AND a.produto_id = '" . $produto_id . "'
					$order";				
		//echo $sql;
		$result_filtro = $conn->sql($sql);
	}


 	//Seleciona os status de estoque
    $sql = "SELECT status.status_id, status_idioma.idiomastatus_titulo as status_titulo
            FROM tb_cad_status status
            INNER JOIN tb_cad_idioma_status status_idioma ON status.status_id = status_idioma.status_id
            WHERE status.status_tipo = 'estoque' AND status.status_id <> 60 AND status_idioma.idioma_id = '".$_SESSION["care-br"]["idioma_id"]."' ORDER BY status.ordem ASC";
    // AND status.status_titulo<>'Disponivel' <--- Tirar registro Disponivel da tabela da grid
    $rs = $conn->sql($sql);
    //$rows = mysqli_num_rows($rs);
    while ($tmp_cadastro = mysqli_fetch_array($rs)){

        $status[] = $tmp_cadastro;

    }
    //var_dump($status);

    $arrayAbreviacoes = array('Disponivel' => 'Dis.', 

    			'Utilizada' => '<label>
					Uti.
					<i class="icon-help" title="" alt=""></i>
					<div class="tooltip">
						Quantidade de Peça Utilizada
					</div>
				</label>', 
				'Reservada' => '<label>
					Res.
					<i class="icon-help" title="" alt=""></i>
					<div class="tooltip">
						Quantidade Reservada
					</div>
				</label>', 
				'Devolucao' => 
				'<label>
					Dev.
					<i class="icon-help" title="" alt=""></i>
					<div class="tooltip">
						Quantidade Devolvida/defeito
					</div>
				</label>', 
				'Laboratorio' => '<label>
					Lab.
					<i class="icon-help" title="" alt=""></i>
					<div class="tooltip">
						Quantidade Em Laboratorio
					</div>
				</label>', 
				'Descarte' => 'Des.', 
				'Nao Utilizada' => '<label>
					N/U
					<i class="icon-help" title="" alt=""></i>
					<div class="tooltip">
						Quantidade Não Utilizada
					</div>
				</label>');
    

	if (mysqli_num_rows($result_filtro) > 0){
		?>
		<input type="hidden" name="peca_add" id="peca_add" value="S" />
		<?
	}else{
		?>
		<input type="hidden" name="peca_add" id="peca_add" value="" />
		<?
	}
	?>
	<!-- configuração formato moeda -->
	<script type="text/javascript" src="js/jquery.maskMoney.js"></script>
	
	<script type="text/javascript">
		$(document).ready(function() {
			// configuração formato moeda
			$(".numero").maskMoney({decimal:"", thousands:"", precision:0});
            $(".moeda").maskMoney({decimal:",", thousands:".", precision:2});

            $(".valor_venda_item").focusout(function(){

				var num_item 			= $(this).attr('num_item');
				var valor       		= $(this).val();
				var valor_tabela_vendas = $("#osprodutopeca_valor_tabela_venda" + num_item).val();

				var valor_tabela_vendas_orig = valor_tabela_vendas;

				valor = valor.replace('.', '');
				valor = valor.replace(',', '.');
				valor = parseFloat(valor);

				valor_tabela_vendas  = valor_tabela_vendas.replace('.', '');
				valor_tabela_vendas  = valor_tabela_vendas.replace(',', '.');
				valor_tabela_vendas  = parseFloat(valor_tabela_vendas);

				if (valor < valor_tabela_vendas) {
					
					//Valor já aplicado pelo cliente e salvo no banco de dados para essa peça
					var valor_original = $("#valor_venda_" + num_item).val();
					
					if (valor_original > 0) {
						var valor_originalf = $("#valor_venda_" + num_item).attr('valuef');
						$("#osprodutopeca_valor_venda_" + num_item).val(valor_originalf);	
					}else{
						$("#osprodutopeca_valor_venda_" + num_item).val(valor_tabela_vendas_orig);	
					}

					alert('Erro. Valor de venda abaixo da tabela');
					return false;
				}


			});

			function validaDesconto(num_item){

				let valor_venda      = $("#osprodutopeca_valor_venda_"+num_item).val();
				let valor_compra     = $("input[nome_campo='valor_gspn_"+num_item+"']").attr('val_campo');
				let clienteconfig_id = $("#clienteconfig_id").val();
				let desconto         = $("#osprodutopeca_desconto_"+num_item).val();

				//Margem de imposto para Fortaleza
				if(clienteconfig_id == '131' || clienteconfig_id == '136'){ 
				    var imposto_aplicar = 1.405;
				
				//Margem de imposto para demais clientes
				}else{
				    var imposto_aplicar = 1.275;
				}

				desconto     = desconto.replace('.', '');
				desconto     = desconto.replace(',', '.');
				desconto     = parseFloat(desconto);
				
				valor_compra = parseFloat(valor_compra);
				
				valor_venda = valor_venda.replace('.', '');
				valor_venda = valor_venda.replace(',', '.');
				valor_venda = parseFloat(valor_venda);

				let compra_com_imposto = (valor_compra * imposto_aplicar) * 1.15;

				let liquido_venda = valor_venda - desconto;

				if( liquido_venda < compra_com_imposto){
				    alert('Desconto acima do permitido');
				    $("#osprodutopeca_desconto_"+num_item).val('0,00');
				}

				console.log({valor_venda:valor_venda, desconto:desconto, liquido_venda:liquido_venda, valor_compra:valor_compra, compra_com_imposto:compra_com_imposto, imposto:imposto_aplicar});
			}

			$(".desconto_item").keyup(function(){

				let num_item = $(this).attr('num_item');
				validaDesconto(num_item);
			});

		});

		function conferirTabelaVendaLista(osprodutopeca_id){ 
			var produto_id_peca	 	= $(".produto_id_peca_" + osprodutopeca_id).val();
			var os_id				= $("#os_id").val();
			var clienteconfig_id 	= $("#clienteconfig_id").val();
			var valor_venda_70	 	= $("#osprodutopeca_valor_venda_" + osprodutopeca_id).val();
			var valor_mao_obra	 	= $("#osprodutopeca_valor_mao_obra_" + osprodutopeca_id).val();
			var valor_banco 		= $("#osprodutopeca_valor_venda_banco_" + osprodutopeca_id).val()
			var valor_banco_mao		= $("#osprodutopeca_valor_venda_banco_mao_" + osprodutopeca_id).val()


			var cliente_id 			=  $("#cliente_id").val();
			var retorno				= '';
			/*
			if(valor_mao_obra){
				
				valor_mao_obra = 	parseFloat(valor_mao_obra.replace(",",".",valor_mao_obra))
			}

			if(valor_venda_70){
				
				valor_venda = parseFloat(valor_venda_70.replace(",",".",valor_venda_70));
			}

			var valor_venda 		=  valor_venda + valor_mao_obra;
			*/
			var valor_venda 		= valor_venda_70;

			if(produto_id_peca){
					
				$.ajax({
				type: "POST",
				data: {acao:'get_tabela_venda',produto_id_peca:produto_id_peca,os_id:os_id,clienteconfig_id:clienteconfig_id,valor_venda:valor_venda,cliente_id:cliente_id,tb_prod_os:'tb_prod_os'},
				async: false,
				url: 'os_venda_edicao.php',
				success: function(data) {
					if(data == 'nok'){
						alert("Permitido alterar somente 10% do valor da Venda. Consulte seu Supervisor!");
						//valor_banco = valor_banco.replace(".",",");
						valor_banco = valor_banco;
						$("#osprodutopeca_valor_venda_" + osprodutopeca_id).val(valor_banco);  
						
						retorno =  'nok';
					}
						retorno = 'ok'
				}
				});

			}else{
				if(valor_banco){
					valor_banco = valor_banco.replace(".",",");
				}
				$("#osprodutopeca_valor_venda_" + osprodutopeca_id).val(valor_banco);
			}

			return retorno;	
		}
	</script>

    <style>
        select[readonly] {
            background: #eee;
            pointer-events: none;
            touch-action: none;
        }
    </style>    
	
	<div class="row">
		<div class="campos-form">
			<div class="input-control text">
				Lista de Peças
				<?if ($nf!='' || $nfs != ''){?>
					<div class="alert info">
				  		<strong>Info!</strong> <?=$nf?> <?=$nfs?>
					</div>
				<?php
				}?> 
				<table class="striped bordered hovered" style="width: 100%;">
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th class="text-center">Código</th>
							<th class="text-center">Peça</th>
							<th class="text-center">Valor GSPN</th>
							<th class="text-center">Qtde para Reparo</th>
							<th class="text-center">Qtde em Estoque</th>
							<th class="text-center">Status Peça</th>
                            <?
                                if ($clienteconfig_id == 101 || $clienteconfig_id == 110 || $clienteconfig_id == 112 || $clienteconfig_id == 123 || $clienteconfig_id == 124 || $clienteconfig_id == 131 || $clienteconfig_id == 133 || (strpos(B2X_SES_OS.B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
                                    echo '<th class="text-center">Valor Venda</th>';
                                    echo '<th class="text-center">Desconto</th>';
                                    // echo '<th class="text-center">M.O.</th>';
                                    echo '<th class="text-center">Total</th>';
                                }
                                ?>
                            <th class="text-center">Cobrar</th>
                            <th class="text-center">Display</th>
							<th class="text-center"></th>
						</tr>
					</thead>
					<tbody>
						<?
						$cont_peca            = 0;
						$cont_total           = 0;
						$mo                   = 0;
						$total_valor_unitario = 0;
						$num_item 			  = 0;

						while($tmp_filtro = mysqli_fetch_array($result_filtro)){

							$num_item ++;
							
							$os_cobertura = $os_cobertura_aux;
							$valor_mao_obra = $tmp_filtro["osprodutopeca_valor_mao_obra"];
							
							//TIPO DE VERIFICAÇÃO DIFERENTE DE ESTOQUE PARA DETERMINADA EMPRESA QUE NAO UTILIZA COBERTURA
							if(empty($os_cobertura)){
								if ($tmp_filtro["osprodutopeca_cobrar"] == "N"){
									$os_cobertura = 'GARANTIA';
								}elseif($tmp_filtro["osprodutopeca_cobrar"] == "S"){
									$os_cobertura = 'ORCAMENTO';
								}
							}
							
							if ($clienteconfig_id == 101 || $clienteconfig_id == 110 || $clienteconfig_id == 112 || $clienteconfig_id == 123 || $clienteconfig_id == 124 || $clienteconfig_id == 131 || $clienteconfig_id == 133 || (strpos(B2X_SES_OS.B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
								$os_cobertura = $_REQUEST['os_cobertura'];
							}

							$categoria_id= get_categoria_estoque($clienteconfig_id,$tmp_filtro["linha_id"],$os_cobertura);
							$estoque_id  = get_estoqueID($tmp_filtro["produto_id_peca"],$categoria_id);
							$qtd_estoque = get_qtd_estoque($clienteconfig_id,$tmp_filtro["linha_id"],$tmp_filtro["produto_id_peca"],$os_cobertura);
							
							?>
							<tr>
								<td class="text-center">
									<?=++$cont_peca?>
									<input type="hidden" class="osprodutopeca_id" value="<?=$tmp_filtro["osprodutopeca_id"]?>" />
									<input type="hidden" name="produto_id_estoque_<?=$tmp_filtro["osprodutopeca_id"]?>" id="produto_id_estoque_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=$tmp_filtro["produto_id_peca"];?>">
								</td>
								
								<td class="text-center"><?=$tmp_filtro["produto_codigo"]?></td>
								<td>
                                    <?
                                        echo $tmp_filtro["produto_descricao"];
                                        if ($clienteconfig_id == 101 || $clienteconfig_id == 110 || $clienteconfig_id == 112 || $clienteconfig_id == 123 || $clienteconfig_id == 124 || $clienteconfig_id == 131 || $clienteconfig_id == 133 || (strpos(B2X_SES_OS.B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
                                            if ($tmp_filtro["osprodutopeca_complementar"] == 'S') {
                                                $complementar = 'Peça é complementar';
                                                echo "<br><b>$complementar</b>";
                                            }
                                        }
                                    ?>
                                </td>
								<td>
									<div class="input-control text">
										<?php 
												// $tmp_filtro["valor_unitario_te"] = 0;
												// if ($tmp_filtro["valor_unitario_te"] == 0) {
												
												$sql 			 = "SELECT cliente_id, empresacliente_id FROM tb_prod_care_cliente_config WHERE clienteconfig_id = $clienteconfig_id";
												$res_cliente 	 = $conn->getData($sql);
												$produto_id_peca = $tmp_filtro["produto_id_peca"];
												
												$sql 		= "SELECT id, produto_valor_compra FROM tb_cad_produto_gspn WHERE cliente_id = {$res_cliente[0]['cliente_id']} AND empresacliente_id = {$res_cliente[0]['empresacliente_id']} AND produto_id = $produto_id_peca";
												$res 		= $conn->getData($sql);
												
												/*
												* CASO EXISTA PREÇO NA tb_cad_produto_gspn ELE PREVALECE SOBRE O PREÇO DO ESTOQUE
												*/
												if (!empty($res[0]['produto_valor_compra']) && $res[0]['produto_valor_compra'] > 0) {
													$tmp_filtro["valor_unitario_te"] = $res[0]['produto_valor_compra'];
												}
												//

												$total_valor_unitario = $total_valor_unitario + $tmp_filtro["valor_unitario_te"];

												$cobertura 	= trim(substr($os_cobertura, 0, 3));
												
												switch ($cobertura) {
													case 'HHP':
														$linha_id = 3;
														break;
													case 'NPC':
														$linha_id = 5;
														break;
													case 'DTV':
														$linha_id = 2;
														break;
													case 'HA':
														$linha_id = 1;
														break;
													default:
														$linha_id = 3;
														break;
												}

												//Caso EXISTA produto cadastrado na tabela Produtos x GSPN
												if (!empty($res[0]['produto_valor_compra']) && $res[0]['produto_valor_compra'] > 0) {
													
													$valor_venda = $res[0]['produto_valor_compra'] * 1.8;

													//Arredonda pra cima 1.59 vira 2.00 por exemplo
													$valor_venda = ceil($valor_venda);

													$cobertura = trim(substr($os_cobertura, 0, 3));
													
													switch ($cobertura) {
														case 'HHP':
															$linha_id = 3;
															break;
														case 'NPC':
															$linha_id = 5;
															break;
														case 'DTV':
															$linha_id = 2;
															break;
														case 'HA':
															$linha_id = 1;
															break;
														default:
															$linha_id = 3;
															break;
													}
													
													$sql = "SELECT tabela_produto_id FROM tb_cad_tabela_produto_venda WHERE 
																cobertura = '$cobertura' AND
																cliente_id = {$res_cliente[0]['cliente_id']} AND
																empresacliente_id = {$res_cliente[0]['empresacliente_id']} AND
																produto_id = $produto_id_peca AND
																linha_id = $linha_id";

													$tmp = $conn->sql($sql);

													if (mysqli_num_rows($tmp) == 0) {
														
														$sql = "INSERT INTO tb_cad_tabela_produto_venda SET
															cobertura = '$cobertura',
															cliente_id = {$res_cliente[0]['cliente_id']},
															empresacliente_id = {$res_cliente[0]['empresacliente_id']},
															produto_id = $produto_id_peca,
															linha_id = $linha_id,
															valor_venda = $valor_venda";
														$conn->sql($sql);

														$sql = "INSERT INTO tb_cad_markup_automatico SET
															cliente_id = {$res_cliente[0]['cliente_id']},
															empresacliente_id = {$res_cliente[0]['empresacliente_id']},
															produto_id = $produto_id_peca,
															valor_compra = {$res[0]['produto_valor_compra']},
															valor_venda = $valor_venda,
															data = NOW()";
														$conn->sql($sql);
													}

												//Caso NÂO existaproduto cadastrado na tabela Produtos x GSPN
												} elseif ($res[0]['produto_valor_compra'] == 0 || empty($res[0]['produto_valor_compra'])) {
													
													// Verificar no GSPN o preço de compra
													switch ($res_cliente[0]['empresacliente_id']) {
														case '900':
															$cli = 'b2xMoema';
															break;
														case '907':
															$cli = 'cspFortaleza';
															break;
														case '883':
															$cli = 'b2xfranca';
															break;
														case '895':
															$cli = 'b2xBauru';
															break;
														case '910':
															$cli = 'b2xMorumbi';
															break;
														case '893':
															$cli = 'b2xShoppingD';
															break;
														case '909':
															$cli = 'b2xUberlandiaShopping';
															break;
													}

													include  "gspn/ipaas/config/auto_load.php";

													$op = new ServiceOrder(new Client($cli));

													$data["IsSihpAddr"]["AddressFlag"] = "Y";
													$data["IsSihpAddr"]["CustomerName"] = "B2X CARE SERVICOS TECNOLOGICOS LTDA";
													$data["IsSihpAddr"]["Street"] = "ALAMEDA DOS MARACATINS";
													$data["IsSihpAddr"]["City"] = "Sao Paulo";
													$data["IsSihpAddr"]["PostalCode"] = "04089000";
													$data["IsSihpAddr"]["State"] = "SP";
													$data["IsSihpAddr"]["PhoneNumber"] = "31043667";
													$data["IsSihpAddr"]["EmailAddress"] = "";
													$data["IvPoType"] = '5';
													$data["IvShippingMethod"] = '1';
													$data["IvPONo"] = '123';

													$data2["PartsNo"] 		 = $tmp_filtro["produto_codigo"];
													$data2["PartsQty"] 		 = "1";
													$data2["ServiceOrderNo"] = "";
													
													$data["ItPoItem"][0] = $data2;
													$response = $op->checkPO($data);
													$response = json_decode(json_encode($response), TRUE);

													//Caso não tenha resposta, troca o tipo de PO e consulta novamente
													$code = $response['response']['Return']['EvRetCode'];
												    
												    if ($code != '200') {
												        $data["IvPoType"] = '1';
												        $response         = $op->checkPO($data);
												        $response         = json_decode(json_encode($response), TRUE);
												    }

												    if (isset($response['response']['EtPoResult']['results'][0])) { 
														
														$UnitPrice 	 = $response['response']['EtPoResult']['results'][0]['UnitPrice'];
														$Amount 	 = $response['response']['EtPoResult']['results'][0]['Amount'];
														$valor_venda = $UnitPrice * 1.8;

														//Arredonda pra cima 1.59 vira 2.00 por exemplo
														$valor_venda = ceil($valor_venda);
											
														$sqlUp = "UPDATE 
																	tb_cad_produto_gspn 
																		SET
																			produto_valor_compra = '$UnitPrice',
																			data_atualizacao = NOW()
																	WHERE 
																		id = '{$res[0]['id']}'";
														$conn->sql($sqlUp);

														$res[0]['produto_valor_compra'] = $UnitPrice;

														$sql = "SELECT tabela_produto_id FROM tb_cad_tabela_produto_venda WHERE 
																cobertura = '$cobertura' AND
																cliente_id = {$res_cliente[0]['cliente_id']} AND
																empresacliente_id = {$res_cliente[0]['empresacliente_id']} AND
																produto_id = $produto_id_peca AND
																linha_id = $linha_id";
														
														$tmp = $conn->sql($sql);
														
														if (mysqli_num_rows($tmp) == 0) {
															
															$sql = "INSERT INTO tb_cad_tabela_produto_venda SET
																cobertura = '$cobertura',
																cliente_id = {$res_cliente[0]['cliente_id']},
																empresacliente_id = {$res_cliente[0]['empresacliente_id']},
																produto_id = $produto_id_peca,
																linha_id = $linha_id,
																valor_venda = $valor_venda";
															$conn->sql($sql);

															$sql = "INSERT INTO tb_cad_markup_automatico SET
																cliente_id = {$res_cliente[0]['cliente_id']},
																empresacliente_id = {$res_cliente[0]['empresacliente_id']},
																produto_id = $produto_id_peca,
																valor_compra = {$res[0]['produto_valor_compra']},
																valor_venda = $valor_venda,
																data = NOW()";
															$conn->sql($sql);
														}
													}
												}

												$valor_venda_calculado = $valor_venda;

											// }
										?>
										<input type="text" id="valor_nf_ssg" name="valor_nf_ssg" nome_campo='<?php echo "valor_gspn_".$tmp_filtro["osprodutopeca_id"]; ?>' val_campo="<?php echo $tmp_filtro["valor_unitario_te"] ?>" value="<?=number_format($tmp_filtro["valor_unitario_te"], 2, ',', '.')?>" style="width: 70px; text-align:right;" disabled />
										<input type="hidden" id='osprodutopeca_custo_<?=$tmp_filtro["osprodutopeca_id"]?>' value='<?=$tmp_filtro["valor_unitario_te"]?>' />
									</div>
								</td>
								<td class="text-center">
									<div class="input-control text">
										<input <?if ($nf!='' || $nfs!='') echo 'disabled="true" '?> class="numero" type="text" id="osprodutopeca_qtde_<?=$tmp_filtro["osprodutopeca_id"]?>" name="osprodutopeca_qtde_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=$tmp_filtro["osprodutopeca_qtde"]?>" style="width: 50px;" />
									</div>
								</td>
								<td class="text-center">
										<div class="input-control text">
											<input type="text" id="estoque_qtde_<?=$tmp_filtro["produto_id_peca"]?>" name="estoque_qtde_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?  echo $qtd_estoque ?>" style="width: 60px;" disabled />
						
										</div>
								</td>

								<td>
									
									<table class="striped bordered hovered">
										<thead>
											<tr>
												<?php
								                foreach ($status as $sts) {
								                    ?>
								                    <th class="text-center" style="font-size: 0.8em;"><?= $arrayAbreviacoes[$sts["status_titulo"]]; ?></th>
								                    <?php
								                }
								                ?>
											</tr>
										</thead>
										<tbody>
											<tr>
											<?php  foreach($status as $sts) { 
					                            //$sql_status = "SELECT sum(qtde) as qtde FROM tb_prod_estoque_status WHERE status_id='".$sts['status_id']."' AND estoque_id = '". $dados_cadastro["estoque_id"] ."' ";

					                            $sql_status = "SELECT sum(qtde) AS qtde, pe.estoque_id FROM tb_prod_estoque_status pes 
					                            INNER JOIN tb_prod_estoque pe ON(pes.estoque_id=pe.estoque_id) 
					                            WHERE pes.status_id='".$sts['status_id']."' AND pes.os_id='".$os_id."' 
					                            AND pe.produto_id='".$tmp_filtro["produto_id_peca"]."' 
					                            AND pe.categoria_id = '".$categoria_id."'";
					                            $dataEstoqueStatus = mysqli_fetch_object($conn->sql($sql_status));
					                            ?>
					                            <td>
					                            <center>
					                                <a href="#"><?php
					                                if($dataEstoqueStatus->qtde>0){
					                                    echo (round($dataEstoqueStatus->qtde)); 
					                                }
					                                else
					                                {
					                                    echo '0';
					                                }
					                                
					                                ?></a>
					                            </center>
					                            <input type="hidden" class="estoque_status_<?=$sts['status_id']?>_<?=$tmp_filtro["osprodutopeca_id"]?>"  id="estoque_status_<?=$sts['status_id']?>_<?=$tmp_filtro["osprodutopeca_id"]?>" name="estoque_status_<?=$sts['status_id']?>_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=round($dataEstoqueStatus->qtde)?>" />
					                            </td>
				                            <?php
				                            } 

				                            ?>
				                            <input type="hidden" class="estoque_id_<?=$tmp_filtro["osprodutopeca_id"]?>"  id="estoque_id_<?=$tmp_filtro["osprodutopeca_id"]?>" name="estoque_id_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=$estoque_id?>" />
											</tr>
										</tbody>
									</table>
								</td>

                                <?
                                    $readonly = '';
                                    $sim = fct_get_var('global.php', 'var_sim', $_SESSION["care-br"]["idioma_id"]);
                                    $nao = fct_get_var('global.php', 'var_nao', $_SESSION["care-br"]["idioma_id"]);
                                    // B2X Moema / Fortaleza / SES  grava os valores da peça no Análise, e M.O. sempre será 150 reais na primeira peça
                                    if ($clienteconfig_id == 101 || $clienteconfig_id == 110 || $clienteconfig_id == 112 || $clienteconfig_id == 123 || $clienteconfig_id == 124 || $clienteconfig_id == 131 || $clienteconfig_id == 133 || (strpos(B2X_SES_OS.B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
                                        $valor_venda = $tmp_filtro['osprodutopeca_valor_venda'];
                                        $cobertura = $_REQUEST["os_cobertura"];
                                        $produto_id_peca = $tmp_filtro["produto_id_peca"];
                                        if ((empty($valor_venda) || $valor_venda == '0.00') && $produto_id_peca != 314501 && $produto_id_peca != 314502 && $produto_id_peca != 314503) {
                                            $sql = "SELECT empresacliente_id, cliente_id FROM tb_prod_care_cliente_config WHERE clienteconfig_id = '$clienteconfig_id'";
                                            $result = $conn->sql($sql);
                                            $tmp = mysqli_fetch_array($result);
                                            $empresacliente_id = $tmp['empresacliente_id'];
                                            $cliente_id = $tmp['cliente_id'];
                                            

                                            $cobertura_trim = substr($cobertura, 0, 3);
                                            
                                            //Busca o valor de venda na tabela de cadastro
                                            $sql = "SELECT valor_venda 
                                                FROM tb_cad_tabela_produto_venda 
                                                WHERE cliente_id = '$cliente_id' 
                                                AND empresacliente_id = '$empresacliente_id' 
                                                AND (cobertura = '$cobertura_trim' OR cobertura = '$cobertura')
                                                AND produto_id = '" . $tmp_filtro["produto_id_peca"] . "'";
                                            $result = $conn->sql($sql);
											$tmp = mysqli_fetch_array($result);
											
											//Se não achar o valor de venda, busca sem usar a cobertura no filtro
											if (is_null($tmp)) {
												 
												switch ($cobertura_trim) {
													case 'HHP':
														$linha_id = 3;
														break;
													case 'NPC':
														$linha_id = 5;
														break;
													case 'DTV':
														$linha_id = 2;
														break;
													case 'HA':
														$linha_id = 1;
													break;
												}

												$sql = "SELECT * 
	                                                FROM tb_cad_tabela_produto_venda 
	                                                WHERE cliente_id = '$cliente_id' 
	                                                AND empresacliente_id = '$empresacliente_id'
	                                                AND linha_id = '$linha_id'
	                                                AND produto_id = '" . $tmp_filtro["produto_id_peca"] . "'";
	                                            $result = $conn->sql($sql);
												$tmp 	= mysqli_fetch_array($result);

												//Se encontrou o valor, atualiza a cobertura
												if (!is_null($tmp)) {
													
													//Pega os valores
													$tabela_produto_id = $tmp['tabela_produto_id'];
													$valor_venda 	   = $tmp['valor_venda'];

													//Atualiza a cobertura
													$sqlUp = "UPDATE 
																tb_cad_tabela_produto_venda 
															  SET
																cobertura = '$cobertura'
															  WHERE 
																tabela_produto_id = '$tabela_produto_id'";
													$conn->sql($sqlUp);
												}
											}
											
											$tmp['valor_venda'] . '<br>';
                                            $valor_venda = $tmp['valor_venda'];
											// $valor_venda = str_replace('.', ',', $valor_venda);
										}
										
										if ($tmp_filtro["osprodutopeca_cobrar"] == "S") {
											$total_venda += $tmp_filtro['osprodutopeca_valor_venda'] * $tmp_filtro["osprodutopeca_qtde"];
											$total_desconto += $tmp_filtro['osprodutopeca_valor_desconto'];
											// $total_mao_obra += $tmp_filtro['osprodutopeca_valor_mao_obra'];
										}

                                        if (($tmp_filtro['osprodutopeca_valor_mao_obra'] == '0.00' || empty($tmp_filtro['osprodutopeca_valor_mao_obra'])) && $mo == 0 && $produto_id_peca != 157066 && $produto_id_peca != 164976 && $produto_id_peca != 165440 && $produto_id_peca != 166344 && $produto_id_peca != 167730 && $produto_id_peca != 168534 && $produto_id_peca != 168535 && $produto_id_peca != 170922 && $produto_id_peca != 170965 && $produto_id_peca != 170966 && $produto_id_peca != 170996 && $produto_id_peca != 171139 && $produto_id_peca != 171980 && $produto_id_peca != 171995 && $produto_id_peca != 172332 && $produto_id_peca != 172344 && $produto_id_peca != 172345 && $produto_id_peca != 172966 && $produto_id_peca != 177046 && $produto_id_peca != 177612 && $produto_id_peca != 177613 && $produto_id_peca != 179748 && $produto_id_peca != 206556 && $produto_id_peca != 209091 && $produto_id_peca != 282032 && $produto_id_peca != 282611 && $produto_id_peca != 283200 && $produto_id_peca != 284922 && $produto_id_peca != 284927 && $produto_id_peca != 284930 && $produto_id_peca != 285950 && $produto_id_peca != 286818 && $produto_id_peca != 287135 && $produto_id_peca != 289315 && $produto_id_peca != 289356 && $produto_id_peca != 290770 && $produto_id_peca != 290988 && $produto_id_peca != 291365 && $produto_id_peca != 304500 && $produto_id_peca != 306888 && $produto_id_peca != 306891 && $produto_id_peca != 306892 && $produto_id_peca != 306893 && $produto_id_peca != 306915 && $produto_id_peca != 306916 && $produto_id_peca != 306917 && $produto_id_peca != 306918 && $produto_id_peca != 306919 && $produto_id_peca != 306927 && $produto_id_peca != 313691 && $produto_id_peca != 313692 && $produto_id_peca != 313889 && $produto_id_peca != 314501 && $produto_id_peca != 314502 && $produto_id_peca != 314503) {
											if(($_REQUEST["os_cobertura"] == "DTV - OW" || $_REQUEST["os_cobertura"] == "NPC - OW") && ($clienteconfig_id == 123 || $clienteconfig_id == 131 || (strpos(B2X_CSP_OS,"|".$clienteconfig_id."|")>0) )){
												$tmp_filtro['osprodutopeca_valor_mao_obra'] = '250.00';
											}
											else{
												$tmp_filtro['osprodutopeca_valor_mao_obra'] = '150.00';
											}
                                        } elseif ($produto_id_peca == 157066 || $produto_id_peca == 164976 || $produto_id_peca == 165440 || $produto_id_peca == 166344 || $produto_id_peca == 167730 || $produto_id_peca == 168534 || $produto_id_peca == 168535 || $produto_id_peca == 170922 || $produto_id_peca == 170965 || $produto_id_peca == 170966 || $produto_id_peca == 170996 || $produto_id_peca == 171139 || $produto_id_peca == 171980 || $produto_id_peca == 171995 || $produto_id_peca == 172332 || $produto_id_peca == 172344 || $produto_id_peca == 172345 || $produto_id_peca == 172966 || $produto_id_peca == 177046 || $produto_id_peca == 177612 || $produto_id_peca == 177613 || $produto_id_peca == 179748 || $produto_id_peca == 206556 || $produto_id_peca == 209091 || $produto_id_peca == 282032 || $produto_id_peca == 282611 || $produto_id_peca == 283200 || $produto_id_peca == 284922 || $produto_id_peca == 284927 || $produto_id_peca == 284930 || $produto_id_peca == 285950 || $produto_id_peca == 286818 || $produto_id_peca == 287135 || $produto_id_peca == 289315 || $produto_id_peca == 289356 || $produto_id_peca == 290770 || $produto_id_peca == 290988 || $produto_id_peca == 291365 || $produto_id_peca == 304500 || $produto_id_peca == 306888 || $produto_id_peca == 306891 || $produto_id_peca == 306892 || $produto_id_peca == 306893 || $produto_id_peca == 306915 || $produto_id_peca == 306916 || $produto_id_peca == 306917 || $produto_id_peca == 306918 || $produto_id_peca == 306919 || $produto_id_peca == 306927 || $produto_id_peca == 313691 || $produto_id_peca == 313692 || $produto_id_peca == 313889 || $produto_id_peca == 314501 || $produto_id_peca == 314502 || $produto_id_peca == 314503) {
											$tmp_filtro['osprodutopeca_valor_mao_obra'] = '100.00';
                                        }

                                        $rotina_link = fct_get_rotina_invisivel(VAR_MENU_CABECALHO, 'produto_tabela_controle.php', $os_id);
                                        $acesso_liberado = strpos($rotina_link, "permite_valor_tabela_preco");
                                        
                                        $retorno = '';
                                        if (!$acesso_liberado){
											$retorno = 'readonly="true"';
										}

										if ($clienteconfig_id == 123 || $clienteconfig_id == 131 || (strpos(B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
											if ($desconto) {
												$tmp_filtro['osprodutopeca_valor_desconto'] = ($desconto / 100) * $valor_venda;
											}
										} elseif ($desconto == 'remover') {
											$tmp_filtro['osprodutopeca_valor_desconto'] = 0;
										}


										$sql_empresaclienteid= "Select cliente_id, empresacliente_id from tb_prod_care_cliente_config where clienteconfig_id='".$clienteconfig_id."'";
										$res_empresaclienteid = $conn->sql($sql_empresaclienteid);
										while($dados_empresaclienteid = mysqli_fetch_array($res_empresaclienteid)){
											$empresacliente_id = $dados_empresaclienteid['empresacliente_id'];
											$cliente_id = $dados_empresaclienteid['cliente_id'];
                                        }

										if ($clienteconfig_id == 101 || $clienteconfig_id == 110 || $clienteconfig_id == 112 || $clienteconfig_id == 123 || $clienteconfig_id == 124 || $clienteconfig_id == 131 || $clienteconfig_id == 133 || (strpos(B2X_SES_OS.B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
                                            $cobertura = substr($os_cobertura, 0, 3);
                                        } else {
                                            $cobertura = $os_cobertura;
                                        }

										$sql_preco_venda = "SELECT valor_venda FROM tb_cad_tabela_produto_venda  WHERE produto_id ='".$tmp_filtro["produto_id_peca"]."'
										and cobertura = '". $cobertura ."' and cliente_id = '".$cliente_id."' and empresacliente_id = '".$empresacliente_id."'";

										$res_preco_venda = $conn->sql($sql_preco_venda);
										$retorno= '';
										$valor_venda_tabela = '';

										if(mysqli_num_rows($res_preco_venda)> 0){
											while($dados_tabela = mysqli_fetch_array($res_preco_venda)){
												$valor_venda_tabela = $dados_tabela['valor_venda'];
											}
										}

                                        ?>
                                        <td>
                                            <div class="input-control text">
                                                <input 
	                                                <?if ($nf!='' || $nfs!='') echo 'disabled="true" '?>  
	                                                class="moeda valor_venda_item valid" 
	                                                type="text" 
	                                                maxlength="9" 
	                                                id="osprodutopeca_valor_venda_<?=$tmp_filtro["osprodutopeca_id"]?>" 
	                                                name="osprodutopeca_valor_venda_<?=$tmp_filtro["osprodutopeca_id"]?>" 
	                                                value="<?=number_format($valor_venda, 2, ',', '.')?>" 
	                                                num_item='<?=$tmp_filtro["osprodutopeca_id"]?>'
	                                                <?=$retorno?>
                                                >
                                                </input>
												<input 
													type="hidden" 
													name="valor_venda_<?=$tmp_filtro["osprodutopeca_id"]?>" 
													id="valor_venda_<?=$tmp_filtro["osprodutopeca_id"]?>" 
													value="<?=$tmp_filtro['osprodutopeca_valor_venda']?>"
													valuef="<?=number_format($tmp_filtro['osprodutopeca_valor_venda'], 2, ',', '.');?>"
												>

												<span id='msg_erro_valor_venda_<?=$tmp_filtro["osprodutopeca_id"]?>' class='msg_erro_valor_venda' style="display: none; color: red; font-size: 10pt;">Erro. Valor de venda inferior a 80%</span>

													<input type="hidden" name="osprodutopeca_valor_tabela_venda<?=$tmp_filtro["osprodutopeca_id"]?>" id="osprodutopeca_valor_tabela_venda<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=number_format($valor_venda_tabela, 2, ',', '.')?>">

                                            </div>
                                        </td>

                                        <td>
                                            <div class="input-control text">
                                                <input 
                                                	<?if ($nf!='' || $nfs!='') echo 'disabled="true" '?> 
                                                	class="moeda desconto_item valid" 
                                                	type="text" 
                                                	maxlength="9" 
                                                	id="osprodutopeca_desconto_<?=$tmp_filtro["osprodutopeca_id"]?>" 
                                                	name="osprodutopeca_desconto_<?=$tmp_filtro["osprodutopeca_id"]?>" 
                                                	value="<?=number_format($tmp_filtro['osprodutopeca_valor_desconto'], 2, ',', '.')?>" 
                                                	<?=$retorno?>
                                                	num_item='<?=$tmp_filtro["osprodutopeca_id"]?>'
                                                >
                                                </input>
												<input type="hidden" name="valor_desconto_<?=$tmp_filtro["osprodutopeca_id"]?>" id="valor_desconto_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=$tmp_filtro['osprodutopeca_valor_desconto']?>">
                                            </div>
                                        </td>

                                        <!-- <td>
                                            <div class="input-control text">
                                                <input class="moeda valid" type="text" maxlength="9" id="osprodutopeca_valor_mao_obra_<?=$tmp_filtro["osprodutopeca_id"]?>" name="osprodutopeca_valor_mao_obra_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=number_format($tmp_filtro['osprodutopeca_valor_mao_obra'], 2, ',', '.')?>" <?=$retorno?>>
                                                </input>
												<input type="hidden" name="valor_maodeobra_<?=$tmp_filtro["osprodutopeca_id"]?>" id="valor_maodeobra_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=$valor_mao_obra?>">
                                            </div>
                                        </td> -->
                                        <td>
                                            <div class="input-control text">
                                                <?
                                                    $valor_total_peca = ($valor_venda * $tmp_filtro["osprodutopeca_qtde"]) - $tmp_filtro['osprodutopeca_valor_desconto'];
                                                    $mo++;
                                                ?>
                                                <input class="moeda valid" type="text" maxlength="9" id="osprodutopeca_valor_total_<?=$tmp_filtro["osprodutopeca_id"]?>" name="osprodutopeca_valor_total_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=number_format($valor_total_peca, 2, ',', '.')?>" disabled>
                                                </input>
                                            </div>
                                        </td>
                                        <?
                                        $sim = "OW";
                                        $nao = "LP";
                                        if ($dados_cadastro[0]["os_chamado_atendimento"] == 'Laudo Orcamento') {
                                            if ($tmp_filtro["osprodutopeca_complementar"] == 'N') {
                                                $readonly = 'readonly="readonly"';
                                            }
                                            ?>
                                            <input type="hidden" class="peca_complementar"  id="peca_complementar" name="peca_complementar" value="Sim" />
                                            <?
                                        } else {
                                            ?>
                                            <input type="hidden" class="peca_complementar"  id="peca_complementar" name="peca_complementar" value="Nao" />
                                            <?
                                        }
                                    }
                                ?>
								<td class="text-center">
									<div class="input-control text">
										<select <?if ($nf!='' || $nfs!='') echo 'disabled="true" '?> class="lista_cobrar" name="osprodutopeca_cobrar_<?=$tmp_filtro["osprodutopeca_id"]?>" id="osprodutopeca_cobrar_<?=$tmp_filtro["osprodutopeca_id"]?>" style="width: 70px; margin:auto;" onchange="valorVenda(<?=$tmp_filtro["osprodutopeca_id"] . ", '". number_format($tmp_filtro["osprodutopeca_valor_venda"], 2, ',', '.') ."',".$tmp_filtro["produto_id_peca"]?>);" <?=$readonly?>>
											<option value=""><?=fct_get_var('global.php', 'var_selecione', $_SESSION["care-br"]["idioma_id"])?></option>
											<option value="S" <? if ($tmp_filtro["osprodutopeca_cobrar"] == "S" || ($clienteconfig_id == '97' && empty($tmp_filtro['osproduto_cobrar']))) echo "selected"; ?> ><?=$sim?></option>
											<option value="N" <? if ($tmp_filtro["osprodutopeca_cobrar"] == "N") echo "selected"; ?> ><?=$nao?></option>
											<option value="cortesia" <? if ($tmp_filtro["osprodutopeca_cobrar"] == "cortesia") echo "selected"; ?> >Cortesia</option>
										</select>
										<input type="hidden" name="peca_cobrar_<?=$tmp_filtro['osprodutopeca_id']?>" id="peca_cobrar_<?=$tmp_filtro['osprodutopeca_id']?>" value="<?=$tmp_filtro["osprodutopeca_cobrar"]?>">
									</div>
								</td>

								<td>
									<?=$tmp_filtro['display']?>
								</td>


								<td class="text-center">
									<?if ($nf!='' || $nfs!='') {}else{?>
									<a href="javascript: void(0);" onClick="updtPecaLista('<?=$tmp_filtro["osprodutopeca_id"]?>');" title="Alterar" alt="Alterar"><i class="icon-save"></i></a>
									<a href="javascript: void(0);" onClick="excluiCadastro('<?=$tmp_filtro["osprodutopeca_id"]?>', '<?=$tmp_filtro["produto_id_peca"]?>');" title="Excluir" alt="Excluir"><i class="icon-cancel"></i></a>
									<?}?>
								</td>
							</tr>
							<?
							if ($clienteconfig_id == 20 || $clienteconfig_id == 82) {
								if ($os_cobertura == 'GARANTEC-ESTENDIDA' 
									|| $os_cobertura == 'LUIZASEG-ESTENDIDA' 
									|| $os_cobertura == 'CARDIF-ESTENDIDA' 
									|| $os_cobertura == 'ZURICH-ESTENDIDA' 
									|| $os_cobertura == 'ZURICH-FAST-ESTENDIDA' 
									|| $os_cobertura == 'ASSURANT-ESTENDIDA' 
									|| $os_cobertura == 'VIRGINIA-ESTENDIDA' 
									|| $os_cobertura == 'ASSURANT CELULAR-ESTENDIDA' 
									|| $os_cobertura == 'VIRGINIA CELULAR-ESTENDIDA') {
									// $valor_total = 0;
									$valor_total += $tmp_filtro["valor_unitario"];
									// echo $valor_total;
								}
							}
						}
						?>
						<input type="hidden" id="valor_total" value="<?=$valor_total?>">
                        <?
                            if ($clienteconfig_id == 101 || $clienteconfig_id == 110 || $clienteconfig_id == 112 || $clienteconfig_id == 123 || $clienteconfig_id == 124 || $clienteconfig_id == 131 || $clienteconfig_id == 133 || (strpos(B2X_SES_OS.B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
                                $total_geral = $total_venda + $total_mao_obra - $total_desconto;
                                $total_geral = number_format($total_geral, 2, ',', '.');
                                ?>
                                <tr>
									<td colspan="3" class="text-center"><b>Totais</b></td>
									<td class="text-center"><b><?=number_format($total_valor_unitario, 2, ',', '.')?></b></td>
									<td colspan="3"></td>
                                    <td class="text-center"><b><?=number_format($total_venda, 2, ',', '.');?></b></td>
                                    <td class="text-center"><b><?=number_format($total_desconto, 2, ',', '.');?></b></td>
                                    <!-- <td class="text-center"><b><?=number_format($total_mao_obra, 2, ',', '.');?></b></td> -->
                                    <td class="text-center"><b><?=$total_geral?></b></td>									
                                    <td></td>
									<?
										if (($clienteconfig_id == '123' || $clienteconfig_id == '131' || (strpos(B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) && $dados_cadastro[0]["resultado_orcamento"] == 'Orcamento Reprovado') {
											?>
											<td>
												<a href="javascript: void(0);" onClick="excluiCadastroTodos();" title="Excluir Todos" alt="Excluir Todos"><i class="icon-cancel"></i></a>
											</td>
											<?
										} elseif ($clienteconfig_id == '123' || $clienteconfig_id == '131' || (strpos(B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
											?>
											<td>
												<a href="javascript: void(0);" onClick="updtPecaLista('');" title="Alterar todas" alt="Alterar todas"><i class="icon-save"></i></a>
											</td>
											<?
										} else {
											?>
											<td></td>
											<?
										}
									?>
                                </tr>
                                <?
                            }
                        ?>
					</tbody>
				<table>
			</div>
		</div>
	</div>
	
	<!-- selecionar e adicionar peça -->
	<?if ($nf!='' || $nfs!='') {}else{?>
	<div class="row">
		<? 
			$modalidade = $conn->getData("select tipo_isento_taxa from tb_prod_os where os_id = $os_id")[0]['tipo_isento_taxa'];

			if($modalidade == 'Assurant'){ ?>
			<div class="span2 campos-form">	
				<p><b>É display?</b></p>
				<label for="coisa1"><input type="radio" value="S" id="coisa1" class="radioDisplay" name="input-display"> Sim</label>
				<label for="coisa2"><input type="radio" value="N" id="coisa2" class="radioDisplay" name="input-display"> Não</label>
			</div>
		<? } ?>		
		<div class="span6 campos-form">
			<div class="input-control select">
				<label>
					Adicionar Peça
					<i class="icon-help" title="" alt=""></i>
					<div class="tooltip">
						Selecione a Peça necessário ao reparo do produto
					</div>
				</label>
				<!-- link para cadastro de produtos -->
				<a href="produto_controle.php" title="Cadastro de Produto & Peça" alt="Cadastro de Produto & Peça" target="_blank"><i class="icon-cube"></i>Produto & Peça</a>
				<select name="produto_id_peca" id="produto_id_peca" OnChange="javascript: void(0); addPecaLista();">
					<option value=""><?=fct_get_var('global.php', 'var_selecione', $_SESSION["care-br"]["idioma_id"])?></option>
					<?
					// trazer somente peças que tenham fabricante e linha configurados para acesso no perfil do usuário logado
					// somente peças que ainda não estejam adicionadas ao reparo do produto
					$sql = "SELECT x.produto_id_peca, a.produto_codigo, b.idiomaproduto_titulo AS produto_titulo, b.idiomaproduto_descricao AS produto_descricao
								FROM tb_cad_produto_peca x
								INNER JOIN tb_cad_produto a ON x.produto_id_peca = a.produto_id
								INNER JOIN tb_cad_idioma_produto b ON a.produto_id = b.produto_id
								INNER JOIN tb_prod_perfil_empresa c ON a.empresa_id = c.empresa_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'				
								INNER JOIN tb_prod_perfil_linha d ON a.linha_id = d.linha_id AND d.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
								WHERE x.produto_id = '" . $produto_id . "' AND a.produto_ativo = 'S' AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
								AND x.produto_id_peca NOT IN (SELECT produto_id_peca
																FROM tb_prod_os_produto_peca
																WHERE os_id = '" . $os_id . "' AND produto_id = '" . $produto_id . "')
								ORDER BY b.idiomaproduto_descricao";
					$result_filtro = $conn->sql($sql);
					while($tmp_filtro = mysqli_fetch_array($result_filtro)){
						?>
						<option value="<?=$tmp_filtro["produto_id_peca"]?>" title="<?=$tmp_filtro["produto_descricao"]?>" alt="<?=$tmp_filtro["produto_descricao"]?>" <? if ($tmp_filtro["produto_id_peca"] == $produto_id_peca) echo "selected"; ?> ><?=$tmp_filtro["produto_codigo"]?> - <?=$tmp_filtro["produto_descricao"]?></option>
						<?
					}
					?>
				</select>

			</div>


		</div>
	</div>
	<?}?>



	<?
}

// --------------
// get_peca_lista
// --------------
if ($acao == "get_peca_lista_crc") {
	session_write_close();

	// obter peças já solicitadas
	$sql = "SELECT 
				0	AS 'valor_unitario',
				a.*,
				b.produto_codigo,
				b.linha_id,
				b.produto_valor_fab,
				b.produto_valor_fabrica_com_ipi,
				b.produto_titulo AS produto_titulo, b.produto_descricao AS produto_descricao
			FROM tb_prod_os_produto_peca a
			INNER JOIN tb_cad_produto b ON a.produto_id_peca = b.produto_id
			WHERE a.os_id = '" . $os_id . "' AND a.produto_id = '" . $produto_id . "'
			ORDER BY a.osprodutopeca_id ASC";
	//echo $sql;
	$result_filtro = $conn->sql($sql);


 	//Seleciona os status de estoque
    $sql = "SELECT status.status_id, status_idioma.idiomastatus_titulo as status_titulo
            FROM tb_cad_status status
            INNER JOIN tb_cad_idioma_status status_idioma ON status.status_id = status_idioma.status_id
            WHERE status.status_tipo = 'estoque' AND status.status_id <> 60 AND status_idioma.idioma_id = '".$_SESSION["care-br"]["idioma_id"]."' ORDER BY status.ordem ASC";
    // AND status.status_titulo<>'Disponivel' <--- Tirar registro Disponivel da tabela da grid
    $rs = $conn->sql($sql);
    //$rows = mysqli_num_rows($rs);
    while ($tmp_cadastro = mysqli_fetch_array($rs)){

        $status[] = $tmp_cadastro;

    }
    //var_dump($status);

    $arrayAbreviacoes = array('Disponivel' => 'Dis.', 

    			'Utilizada' => '<label>
					Uti.
					<i class="icon-help" title="" alt=""></i>
					<div class="tooltip">
						Quantidade de Peça Utilizada
					</div>
				</label>', 
				'Reservada' => '<label>
					Res.
					<i class="icon-help" title="" alt=""></i>
					<div class="tooltip">
						Quantidade Reservada
					</div>
				</label>', 
				'Devolucao' => 
				'<label>
					Dev.
					<i class="icon-help" title="" alt=""></i>
					<div class="tooltip">
						Quantidade Devolvida/defeito
					</div>
				</label>', 
				'Laboratorio' => '<label>
					Lab.
					<i class="icon-help" title="" alt=""></i>
					<div class="tooltip">
						Quantidade Em Laboratorio
					</div>
				</label>', 
				'Descarte' => 'Des.', 
				'Nao Utilizada' => '<label>
					N/U
					<i class="icon-help" title="" alt=""></i>
					<div class="tooltip">
						Quantidade Não Utilizada
					</div>
				</label>');
    

	if (mysqli_num_rows($result_filtro) > 0){
		?>
		<input type="hidden" name="peca_add" id="peca_add" value="S" />
		<?
	}else{
		?>
		<input type="hidden" name="peca_add" id="peca_add" value="" />
		<?
	}
	?>
	<!-- configuração formato moeda -->
	<script type="text/javascript" src="js/jquery.maskMoney.js"></script>
	
	<script type="text/javascript">
		$(document).ready(function() {
			// configuração formato moeda
			$(".numero").maskMoney({decimal:"", thousands:"", precision:0});
		});
	</script>
	
	<div class="row">
		<div class="campos-form">
			<div class="input-control text">
				Lista de Peças
				<table class="striped bordered hovered" style="width: 98%;">
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th class="text-center">Código</th>
							<th class="text-center">Peça</th>
							<th class="text-center">Valor Fab.</th>
							<th class="text-center">Valor Unit.</th>
							<th class="text-center">Vlr c/ IPI</th>
							<th class="text-center">Qtde para Reparo</th>
							<th class="text-center">Qtde em Estoque</th>
							<th class="text-center">Status Peça</th>
							<th class="text-center">Cobrar</th>
							<th class="text-center"></th>
						</tr>
					</thead>
					<tbody>
						<?
						$cont_peca = 0;
						$cont_total = 0;
						while($tmp_filtro = mysqli_fetch_array($result_filtro)){
							$os_cobertura = $os_cobertura_aux;
							//TIPO DE VERIFICAÇÃO DIFERENTE DE ESTOQUE PARA DETERMINADA EMPRESA QUE NAO UTILIZA COBERTURA
							if(empty($os_cobertura)){
								if ($tmp_filtro["osprodutopeca_cobrar"] == "N"){
									$os_cobertura = 'GARANTIA';
								}elseif($tmp_filtro["osprodutopeca_cobrar"] == "S"){
									$os_cobertura = 'ORCAMENTO';
								}
							}

							$categoria_id= get_categoria_estoque($clienteconfig_id,$tmp_filtro["linha_id"],$os_cobertura);
							$estoque_id  = get_estoqueID($tmp_filtro["produto_id_peca"],$categoria_id);
							$qtd_estoque = get_qtd_estoque($clienteconfig_id,$tmp_filtro["linha_id"],$tmp_filtro["produto_id_peca"],$os_cobertura);
							
							
							?>
							<tr>
								<td class="text-center">
									<?=++$cont_peca?>
									<input type="hidden" class="osprodutopeca_id" value="<?=$tmp_filtro["osprodutopeca_id"]?>" />
									<input type="hidden" name="produto_id_estoque_<?=$tmp_filtro["osprodutopeca_id"]?>" id="produto_id_estoque_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=$tmp_filtro["produto_id_peca"];?>">
								</td>
								
								<td class="text-center"><?=$tmp_filtro["produto_codigo"]?></td>
								<td><?=$tmp_filtro["produto_descricao"]?></td>
								<td><?=number_format($tmp_filtro["produto_valor_fab"], 2, ',', '')?></td>
								<?
								if($clienteconfig_id=='20'){
								?>
									<td><?=number_format($tmp_filtro["produto_valor_fabrica_com_ipi"], 2, ',', '')?></td>
								<?
								}else{
								?>
									<td><?=number_format($tmp_filtro["valor_unitario"], 2, ',', '')?></td>	
								<?
								}
								?>
								<td><?=number_format($tmp_filtro["produto_valor_fabrica_com_ipi"], 2, ',', '')?></td>
								<td class="text-center">
									<div class="input-control text">
										<input class="numero" type="text" id="osprodutopeca_qtde_<?=$tmp_filtro["osprodutopeca_id"]?>" name="osprodutopeca_qtde_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=$tmp_filtro["osprodutopeca_qtde"]?>" style="width: 50px;" />
									</div>
								</td>
								<td class="text-center">
										<div class="input-control text">
											<input type="text" id="estoque_qtde_<?=$tmp_filtro["produto_id_peca"]?>" name="estoque_qtde_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?  echo $qtd_estoque ?>" style="width: 60px;" disabled />
						
										</div>
								</td>

								<td>
									
									<table class="striped bordered hovered">
										<thead>
											<tr>
												<?php
								                foreach ($status as $sts) {
								                    ?>
								                    <th class="text-center" style="font-size: 0.8em;"><?= $arrayAbreviacoes[$sts["status_titulo"]]; ?></th>
								                    <?php
								                }
								                ?>
											</tr>
										</thead>
										<tbody>
											<tr>
											<?php  foreach($status as $sts) { 
					                            //$sql_status = "SELECT sum(qtde) as qtde FROM tb_prod_estoque_status WHERE status_id='".$sts['status_id']."' AND estoque_id = '". $dados_cadastro["estoque_id"] ."' ";

					                            $sql_status = "SELECT sum(qtde) AS qtde, pe.estoque_id FROM tb_prod_estoque_status pes 
					                            INNER JOIN tb_prod_estoque pe ON(pes.estoque_id=pe.estoque_id) 
					                            WHERE pes.status_id='".$sts['status_id']."' AND pes.os_id='".$os_id."' 
					                            AND pe.produto_id='".$tmp_filtro["produto_id_peca"]."' 
					                            AND pe.categoria_id = '".$categoria_id."'";
					                            $dataEstoqueStatus = mysqli_fetch_object($conn->sql($sql_status));
					                            ?>
					                            <td>
					                            <center>
					                                <a href="#"><?php
					                                if($dataEstoqueStatus->qtde>0){
					                                    echo (round($dataEstoqueStatus->qtde)); 
					                                }
					                                else
					                                {
					                                    echo '0';
					                                }
					                                
					                                ?></a>
					                            </center>
					                            <input type="hidden" class="estoque_status_<?=$sts['status_id']?>_<?=$tmp_filtro["osprodutopeca_id"]?>"  id="estoque_status_<?=$sts['status_id']?>_<?=$tmp_filtro["osprodutopeca_id"]?>" name="estoque_status_<?=$sts['status_id']?>_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=round($dataEstoqueStatus->qtde)?>" />
					                            </td>
				                            <?php
				                            } 

				                            ?>
				                            <input type="hidden" class="estoque_id_<?=$tmp_filtro["osprodutopeca_id"]?>"  id="estoque_id_<?=$tmp_filtro["osprodutopeca_id"]?>" name="estoque_id_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=$estoque_id?>" />
											</tr>
										</tbody>
									</table>
								</td>

								<td class="text-center">
									<div class="input-control text">
										<select class="lista_cobrar" name="osprodutopeca_cobrar_<?=$tmp_filtro["osprodutopeca_id"]?>" id="osprodutopeca_cobrar_<?=$tmp_filtro["osprodutopeca_id"]?>" style="width: 70px;" onchange="valorVenda(<?=$tmp_filtro["osprodutopeca_id"] . ", '". number_format($tmp_filtro["osprodutopeca_valor_venda"], 2, ',', '.') ."',".$tmp_filtro["produto_id_peca"]?>);" >
											<option value=""><?=fct_get_var('global.php', 'var_selecione', $_SESSION["care-br"]["idioma_id"])?></option>
                                            <? 
                                                if ($clienteconfig_id == 104) { ?>
                                                   <option value="S" <? if ($tmp_filtro["osprodutopeca_cobrar"] == "S") echo "selected"; ?> >OW</option>
                                                    <option value="N" <? if ($tmp_filtro["osprodutopeca_cobrar"] == "N") echo "selected"; ?> >LP</option> 
                                                <? } else { ?>
                                                    <option value="S" <? if ($tmp_filtro["osprodutopeca_cobrar"] == "S") echo "selected"; ?> ><?=fct_get_var('global.php', 'var_sim', $_SESSION["care-br"]["idioma_id"])?></option>
                                                    <option value="N" <? if ($tmp_filtro["osprodutopeca_cobrar"] == "N") echo "selected"; ?> ><?=fct_get_var('global.php', 'var_nao', $_SESSION["care-br"]["idioma_id"])?></option>
                                                <? }
                                            ?>
										</select>
									</div>
								</td>

								<td class="text-center">
									<a href="javascript: void(0);" onClick="updtPecaLista('<?=$tmp_filtro["osprodutopeca_id"]?>','<?=$tmp_filtro["produto_id_peca"]?>');" title="Alterar" alt="Alterar"><i class="icon-save"></i></a>
									<a href="javascript: void(0);" onClick="excluiCadastro('<?=$tmp_filtro["osprodutopeca_id"]?>', '<?=$tmp_filtro["produto_id_peca"]?>');" title="Excluir" alt="Excluir"><i class="icon-cancel"></i></a>
								</td>
							</tr>
							<?
							if ($clienteconfig_id == 20 || $clienteconfig_id == 82) {
								if ($os_cobertura == 'GARANTEC-ESTENDIDA' 
									|| $os_cobertura == 'LUIZASEG-ESTENDIDA' 
									|| $os_cobertura == 'CARDIF-ESTENDIDA' 
									|| $os_cobertura == 'ZURICH-ESTENDIDA' 
									|| $os_cobertura == 'ZURICH-FAST-ESTENDIDA' 
									|| $os_cobertura == 'ASSURANT-ESTENDIDA' 
									|| $os_cobertura == 'VIRGINIA-ESTENDIDA' 
									|| $os_cobertura == 'ASSURANT CELULAR-ESTENDIDA' 
									|| $os_cobertura == 'VIRGINIA CELULAR-ESTENDIDA') {
									// $valor_total = 0;
									$valor_total += $tmp_filtro["valor_unitario"];
									// echo $valor_total;
								}
							}
						}
						?>
						<input type="hidden" id="valor_total" value="<?=$valor_total?>">
					</tbody>
				<table>
			</div>
		</div>
	</div>
	
	<!-- selecionar e adicionar peça -->
	<div class="row">
		<div class="span6 campos-form">
			<div class="input-control select">
				<label>
					Adicionar Peça
					<i class="icon-help" title="" alt=""></i>
					<div class="tooltip">
						Selecione a Peça necessário ao reparo do produto
					</div>
				</label>
				<!-- link para cadastro de produtos -->
				<a href="produto_controle.php" title="Cadastro de Produto & Peça" alt="Cadastro de Produto & Peça" target="_blank"><i class="icon-cube"></i>Produto & Peça</a>
				<select name="produto_id_peca" id="produto_id_peca" OnChange="javascript: void(0); addPecaLista();">
				</select>
			</div>
		</div>
	</div>


	<?
}



// --------------
// consultar produtos emprestados
// --------------
if ($acao == "get_produto_emprestimo"){
	?>
		<option value="" title="Produto de Emprestimo" alt="Produto de Emprestimo">Selecione o Produto</option>					
	<?
	$sql = "SELECT b.id_emprestimo, a.produto_id, a.produto_codigo, f.idiomaproduto_descricao AS produto_descricao, e.empresa_nome_fantasia AS empresa, b.serial
				FROM tb_prod_os_emprestimo b
				INNER JOIN tb_cad_produto a ON b.produto_id = a.produto_id
				INNER JOIN tb_cad_idioma_produto f ON a.produto_id = f.produto_id AND f.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'								
				INNER JOIN tb_prod_perfil_empresa c ON a.empresa_id = c.empresa_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'				
				INNER JOIN tb_prod_perfil_linha d ON b.linha_id = d.linha_id AND d.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				INNER JOIN tb_cad_empresa e ON a.empresa_id = e.empresa_id				
				WHERE b.os_id = '" . $os_id . "'";

	$result_filtro = $conn->sql($sql);
	while($tmp_filtro = mysqli_fetch_array($result_filtro)){
		?>
		<option value="<?=$tmp_filtro["id_emprestimo"]?>" selected title="<?=$tmp_filtro["produto_descricao"]?>" alt="<?=$tmp_filtro["produto_descricao"]?>"><?=$tmp_filtro["empresa"]?> - <?=$tmp_filtro["produto_codigo"]?> - <?=$tmp_filtro["produto_descricao"]?> - <?=$tmp_filtro["serial"]?></option>
		<?
	}
	$sql = "SELECT b.id_emprestimo, a.produto_id, a.produto_codigo, f.idiomaproduto_descricao AS produto_descricao, e.empresa_nome_fantasia AS empresa, b.serial
				FROM tb_prod_os_emprestimo b
				INNER JOIN tb_cad_produto a ON b.produto_id = a.produto_id
				INNER JOIN tb_cad_idioma_produto f ON a.produto_id = f.produto_id AND f.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'								
				INNER JOIN tb_prod_perfil_empresa c ON a.empresa_id = c.empresa_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'				
				INNER JOIN tb_prod_perfil_linha d ON b.linha_id = d.linha_id AND d.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				INNER JOIN tb_cad_empresa e ON a.empresa_id = e.empresa_id				
				WHERE b.os_id is NULL AND a.produto_tipo = 'produto' AND a.produto_ativo = 'S' AND b.clienteconfig_id = " . $clienteconfig_id . "								
					AND b.linha_id IN (SELECT linha_id
												FROM tb_prod_os
												WHERE os_id = '" . $os_id . "')												
				ORDER BY f.idiomaproduto_descricao";
 	// echo $sql;
	$result_filtro = $conn->sql($sql);
	while($tmp_filtro = mysqli_fetch_array($result_filtro)){
		?>
		<option value="<?=$tmp_filtro["id_emprestimo"]?>" title="<?=$tmp_filtro["produto_descricao"]?>" alt="<?=$tmp_filtro["produto_descricao"]?>"><?=$tmp_filtro["empresa"]?> - <?=$tmp_filtro["produto_codigo"]?> - <?=$tmp_filtro["produto_descricao"]?> - <?=$tmp_filtro["serial"]?></option>
		<?
	}
}

// ---------------
// get_status_novo
// ---------------
if ($acao == "get_status_novo"){

	if($clienteconfig_id == '82'){
		echo "//".$os_cobertura.'//';
		echo strpos($os_cobertura,'FORA');
	}

	//echo $os_cobertura = str_replace(" ","",trim($os_cobertura));

	?>
	<option value=""><?=fct_get_var('global.php', 'var_selecione', $_SESSION["care-br"]["idioma_id"])?></option>
	<?
	if ($motivo_aceita == "S"){	
		if( ($os_cobertura == 'CARDIF-QA') 
			|| ($os_cobertura == 'Corporativo')
			|| ($os_cobertura == 'Avulso')
			|| ($os_cobertura == 'Locação')
			|| ($os_cobertura == 'ZURICH-QA') 
			|| ($os_cobertura == 'ZURICH-FAST-QA') 
			|| ($os_cobertura == 'LUIZASEG-QA') 
			|| ($os_cobertura == 'ORCAMENTO')
			|| ($os_cobertura == utf8_encode('Orçamento')) 
			|| ($os_cobertura == 'Garantec-ESTENDIDA') 
			|| ($os_cobertura == 'Assurant-ESTENDIDA') 
			|| strstr($os_cobertura,'ESTENDIDA') 
			|| ($os_cobertura == 'SERVICOESPECIAL')
			|| strstr($os_cobertura,'OW')
			//|| strpos($os_cobertura,'FORA') >= 0
			|| ($os_cobertura == 'FORAGARANTIA-CELULAR')
			|| ($os_cobertura == 'ORCAMENTOB2W')
			|| ($os_cobertura == 'FORAGARANTIA-INFO')
			|| ($os_cobertura == 'FORAGARANTIA-MARROM')
			|| ($os_cobertura == 'FORAGARANTIA-B2WCELULAR')
			|| ($os_cobertura == 'FORAGARANTIA-B2WMARROM')
			|| ($os_cobertura == 'FORADEGARANTIAINHOME')
			|| strpos($os_cobertura,'ORCAMENTO')){

			//echo "aqui";
										
			$sql = "SELECT d.status_id,
					a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
					FROM tb_prod_care_cliente_config_passo a
					INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
					INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
					INNER JOIN tb_cad_status d ON c.status_id = d.status_id
					INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
					INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
					WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
					AND d.status_id IN (" . $status_id . "," . STATUS_OS_AGUARDA_ORCAMENTO . ")
					AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
					ORDER BY a.clienteconfigpasso_ordem ";

			//verifica se o fluxo tem o orcamento rapido
			$sql_orc_rapido = "SELECT clienteconfigpassodicionario_id
				FROM tb_prod_care_cliente_config_passo_dicionario_dados a
				INNER JOIN tb_cad_dicionario_dados b ON a.dicionario_id = b.dicionario_id
				INNER JOIN tb_cad_idioma_dicionario_dados c ON b.dicionario_id = c.dicionario_id
				INNER JOIN tb_prod_care_cliente_config_passo d ON a.clienteconfigpasso_id = d.clienteconfigpasso_id
				WHERE a.clienteconfigpasso_id IN (SELECT DISTINCT x.clienteconfigpasso_id
														FROM tb_prod_care_cliente_config_passo_dicionario_dados x
														INNER JOIN tb_cad_dicionario_dados y ON x.dicionario_id = y.dicionario_id
														INNER JOIN tb_prod_care_cliente_config_passo z ON x.clienteconfigpasso_id = z.clienteconfigpasso_id
														WHERE z.clienteconfig_id = '" . $clienteconfig_id . "' AND y.status_id IN (4) AND y.dicionario_ativo = 'S')
				AND d.clienteconfig_id = '" . $clienteconfig_id . "' 
				AND b.dicionario_os_campo = 'os_orcamento_rapido'
				AND b.dicionario_ativo = 'S' 
				AND c.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'";

			//verifica se o status orcamento ou aprov orcamento esta ativo no fluxo, e delimita com status pode avançar
			$sql_status_orc = "SELECT DISTINCT x.clienteconfigpasso_id
														FROM tb_prod_care_cliente_config_passo_dicionario_dados x
														INNER JOIN tb_cad_dicionario_dados y ON x.dicionario_id = y.dicionario_id
														INNER JOIN tb_prod_care_cliente_config_passo z ON x.clienteconfigpasso_id = z.clienteconfigpasso_id
														WHERE z.clienteconfig_id = '" . $clienteconfig_id . "' AND y.status_id IN (12,13) AND y.dicionario_ativo = 'S'";
			$limit_orc = '0,3';
			if(mysqli_num_rows($conn->sql($sql_status_orc))>0){
				$limit_orc = '0,2';
			}
			if(mysqli_num_rows($conn->sql($sql_orc_rapido))>0){
				$sql = "SELECT d.status_id,
						a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
						FROM tb_prod_care_cliente_config_passo a
						INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
						INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
						INNER JOIN tb_cad_status d ON c.status_id = d.status_id
						INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
						INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
						WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
						AND d.status_id >= '" . $status_id . "'
						AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
						ORDER BY a.clienteconfigpasso_ordem LIMIT $limit_orc";				
			}

			if ($clienteconfig_id == '90') {

				$sql = "SELECT d.status_id,
					a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
					FROM tb_prod_care_cliente_config_passo a
					INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
					INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
					INNER JOIN tb_cad_status d ON c.status_id = d.status_id
					INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
					INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
					WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
					AND d.status_id IN (" . $status_id . "," . STATUS_OS_APROVA_ORCAMENTO . ")
					AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
					ORDER BY a.clienteconfigpasso_ordem ";	
			}
		}elseif ($clienteconfig_id=='115') {
			$sql = "SELECT d.status_id,
			a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
			FROM tb_prod_care_cliente_config_passo a
			INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
			INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
			INNER JOIN tb_cad_status d ON c.status_id = d.status_id
			INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
			INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
			WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
			AND d.status_id IN (" . $status_id . "," . STATUS_OS_AGUARDA_ORCAMENTO . ")
			AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
			ORDER BY a.clienteconfigpasso_ordem ";
		}
		elseif(empty($os_cobertura)){

			// defeito informado: fluxo normal de atualização de status
			$sql = "SELECT d.status_id,
					a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
					FROM tb_prod_care_cliente_config_passo a
					INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
					INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
					INNER JOIN tb_cad_status d ON c.status_id = d.status_id
					INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
					INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
					WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
					AND d.status_id >= '" . $status_id . "'
					AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
					ORDER BY a.clienteconfigpasso_ordem LIMIT $limit";

			if(($os_peca_precisa == 'N') && ($clienteconfig_id== 11)) {

				$sql = "SELECT d.status_id,
					a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
					FROM tb_prod_care_cliente_config_passo a
					INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
					INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
					INNER JOIN tb_cad_status d ON c.status_id = d.status_id
					INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
					INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
					WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
					AND d.status_id = '" . STATUS_OS_REPARO . "'
					AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
					ORDER BY a.clienteconfigpasso_ordem LIMIT 0,2";
			}
		}
		else{

			// defeito informado: fluxo normal de atualização de status
			$sql = "SELECT d.status_id,
					a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
					FROM tb_prod_care_cliente_config_passo a
					INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
					INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
					INNER JOIN tb_cad_status d ON c.status_id = d.status_id
					INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
					INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
					WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
					AND d.status_id >= '" . $status_id . "'
					AND d.status_id <> '" . STATUS_OS_AGUARDA_ORCAMENTO . "'
					AND d.status_id <> '" . STATUS_OS_APROVA_ORCAMENTO . "'
					AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
					ORDER BY a.clienteconfigpasso_ordem LIMIT 0,3";

			//tratamento PERDA DE GARANTIA
			if($os_tipo_servico == 18 || $os_tipo_servico == 6) {

				$sql = "SELECT d.status_id,
					a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
					FROM tb_prod_care_cliente_config_passo a
					INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
					INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
					INNER JOIN tb_cad_status d ON c.status_id = d.status_id
					INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
					INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
					WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
					AND d.status_id = '" . STATUS_OS_AGUARDA_COLETA . "'
					AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
					ORDER BY a.clienteconfigpasso_ordem LIMIT 0,1";
			}
			//serviços que nao utilizam peça
			elseif( (!empty($os_tipo_servico) && $os_peca_precisa == 'N') || $os_tipo_servico == 9 || $os_tipo_servico == 7 || $os_tipo_servico == 4 || $os_tipo_servico == 10 || $os_tipo_servico == 8 || $os_tipo_servico == 12) {

				$sql = "SELECT d.status_id,
				a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
				FROM tb_prod_care_cliente_config_passo a
				INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
				INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
				INNER JOIN tb_cad_status d ON c.status_id = d.status_id
				INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
				INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
				AND d.status_id = '" . STATUS_OS_REPARO . "'
				AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
				ORDER BY a.clienteconfigpasso_ordem LIMIT 0,2";
			}elseif(!empty($os_tipo_servico)){


				$sql = "SELECT d.status_id,
					a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
					FROM tb_prod_care_cliente_config_passo a
					INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
					INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
					INNER JOIN tb_cad_status d ON c.status_id = d.status_id
					INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
					INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
					WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
					AND d.status_id = '" . STATUS_OS_AGUARDA_PECA . "'
					AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
					ORDER BY a.clienteconfigpasso_ordem LIMIT 0,1";

			}	

		}
		if($clienteconfig_id == "76" && (strtoupper($os_sub_status) == 'PECA DESCONTINUADA')){
			// defeito informado: fluxo normal de atualização de status
			$sql = "SELECT d.status_id,
					a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
					FROM tb_prod_care_cliente_config_passo a
					INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
					INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
					INNER JOIN tb_cad_status d ON c.status_id = d.status_id
					INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
					INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
					WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
					AND d.status_id = '" . STATUS_OS_APROVA_ORCAMENTO . "'
					AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
					ORDER BY a.clienteconfigpasso_ordem LIMIT $limit";			
		}

		//echo $sql;

	}
	else{
		// sem defeito: seguir somente para teste
		$sql = "SELECT d.status_id,
					a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
					FROM tb_prod_care_cliente_config_passo a
					INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
					INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
					INNER JOIN tb_cad_status d ON c.status_id = d.status_id
					INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
					INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
					WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
					AND d.status_id = '" . STATUS_OS_TESTE . "'
					AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
					ORDER BY a.clienteconfigpasso_ordem LIMIT 0,1";

		$status_teste = mysqli_num_rows($conn->sql($sql));					

		//sem defeito e fluxo que não tenha status teste, vá para ag coleta
		if($status_teste == 0){
			$sql = "SELECT a.status_id,
					b.idiomastatus_titulo AS status_titulo, b.idiomastatus_descricao AS status_descricao
					FROM tb_cad_status a
					INNER JOIN tb_cad_idioma_status b ON a.status_id = b.status_id
					INNER JOIN tb_prod_perfil_status c ON a.status_id = c.status_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
					WHERE a.status_id = '" . STATUS_OS_AGUARDA_COLETA . "' AND a.status_ativo = 'S'
					AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'";				
		}
	}
	if($clienteconfig_id == "22" && ($os_sub_status == 'Sem Defeito' || $os_sub_status == 'Peca Descontinuada'|| $os_sub_status == 'Sem Conserto')){			
						
			$sql = "SELECT a.status_id,
					b.idiomastatus_titulo AS status_titulo, b.idiomastatus_descricao AS status_descricao
					FROM tb_cad_status a
					INNER JOIN tb_cad_idioma_status b ON a.status_id = b.status_id
					INNER JOIN tb_prod_perfil_status c ON a.status_id = c.status_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
					WHERE a.status_id = '" . STATUS_OS_AGUARDA_COLETA . "' AND a.status_ativo = 'S'
					AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'";	
	}	
	if($clienteconfig_id =="21" && ($os_sub_status == 'Sem Defeito' || $os_sub_status == 'Sem Conserto')){			
						
			$sql = "SELECT a.status_id,
					b.idiomastatus_titulo AS status_titulo, b.idiomastatus_descricao AS status_descricao
					FROM tb_cad_status a
					INNER JOIN tb_cad_idioma_status b ON a.status_id = b.status_id
					INNER JOIN tb_prod_perfil_status c ON a.status_id = c.status_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
					WHERE a.status_id = '" . STATUS_OS_TESTE . "' AND a.status_ativo = 'S'
					AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'";	
	}
	if($clienteconfig_id =="20" && $os_cobertura="ZURICH QA CSP"){			
						
		$sql = "SELECT d.status_id,
		a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
		FROM tb_prod_care_cliente_config_passo a
		INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
		INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
		INNER JOIN tb_cad_status d ON c.status_id = d.status_id
		INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
		INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '1'
		WHERE a.clienteconfig_id = '20' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
		AND d.status_id IN ('10','12')
		AND e.idioma_id = '1'
		ORDER BY a.clienteconfigpasso_ordem LIMIT 2";	
    }
	if(!empty($os_sub_status)){
		$sql_status_config = "SELECT sts.status_id,
						sts_pass.clienteconfigpasso_titulo AS status_titulo, sts.status_descricao AS status_descricao
			FROM tb_prod_status_config config 
			INNER JOIN tb_cad_status sts ON sts.status_id = config.status_id_novo
			INNER JOIN tb_prod_care_cliente_config_passo sts_pass ON sts_pass.status_id = sts.status_id 
				AND config.clienteconfig_id = sts_pass.clienteconfig_id
			WHERE config.clienteconfig_id = '" . $clienteconfig_id . "' 
			AND config.status_id_atual = '" . $status_id . "'
			AND config.sub_status ='".$os_sub_status."'";
		$result_status_config = $conn->sql($sql_status_config);
		if (mysqli_num_rows($result_status_config) > 0)
			$sql = $sql_status_config;
    }
    
    if ($clienteconfig_id == 101 || $clienteconfig_id == 110 || $clienteconfig_id == 112 || $clienteconfig_id == 123 || $clienteconfig_id == 124 || $clienteconfig_id == 131 || $clienteconfig_id == 133 || (strpos(B2X_SES_OS.B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
        switch ($os_peca_precisa) {
            case 'S':
				$peca_complementar = '';
				$reprovado = '';
                $sql = "SELECT osprodutopeca_complementar, osprodutopeca_cobrar FROM tb_prod_os_produto_peca WHERE os_id = '$os_id'";
                $result = $conn->sql($sql);
                while ($tmp = mysqli_fetch_array($result)) {
                    if ($tmp['osprodutopeca_complementar'] == 'S') {
                        $peca_complementar = 'S';
                    } elseif ($tmp['osprodutopeca_cobrar'] == 'S' && $tmp['osprodutopeca_complementar'] != 'N' && $tmp['osprodutopeca_complementar'] != 'MO'){
                        $peca_complementar = 'S';
                    } elseif ($tmp['osprodutopeca_complementar'] == 'MO') {
                        $peca_complementar = 'MO';
					}
					
					if (($clienteconfig_id == 123 || $clienteconfig_id == 131 || (strpos(B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) && $dados_cadastro[0]["resultado_orcamento"] == 'Orcamento Reprovado') {
						$reprovado = 'S';
					}
                }
                if ($peca_complementar == 'S' && $reprovado != 'S') {
                    $status = "10, 12";
                } elseif ($peca_complementar == 'MO' && $reprovado != 'S') {
                    $status = "10, 24";
                } else {
					if ($clienteconfig_id == 123 || $clienteconfig_id == 131 || (strpos(B2X_CSP_OS,"|".$clienteconfig_id."|")>0) ) {
						if ($reprovado == 'S') {
							$status = "14";
						} else {
							$status = "10, 14";
						}
					} else {
						$status = "10, 16";
					}
                }
            break;
			case 'N':
                $status = "10, 16";
            break;
            default:
                $status = "10";
            break;
        }

			$modalidade = $conn->getData("select tipo_isento_taxa from tb_prod_os where os_id = $os_id")[0]['tipo_isento_taxa'];

			if( (strpos("|".$modalidade, 'Assurant') >0 ) || (strpos("|".$modalidade, 'LuizaSeg') >0 ) || (strpos("|".$modalidade, 'Zurick') >0 ) ){
				$status = "13"; 

			} 		
        $sql = "SELECT d.status_id,
            a.clienteconfigpasso_titulo AS status_titulo, a.clienteconfigpasso_descricao AS status_descricao
            FROM tb_prod_care_cliente_config_passo a
            INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
            INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
            INNER JOIN tb_cad_status d ON c.status_id = d.status_id
            INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
            INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '1'
            WHERE a.clienteconfig_id = '$clienteconfig_id' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
            AND d.status_id IN ($status)
            AND e.idioma_id = '1'
            ORDER BY a.clienteconfigpasso_ordem LIMIT 3";
    }

	$result_filtro = $conn->sql($sql);
	while($tmp_filtro = mysqli_fetch_array($result_filtro)){
		?>
		<option title="<?=$tmp_filtro["status_descricao"]?>" alt="<?=$tmp_filtro["status_descricao"]?>" value="<?=$tmp_filtro["status_id"]?>" <? if ($status_id == $tmp_filtro["status_id"]) echo "selected"; ?> ><?=$tmp_filtro["status_titulo"]?></option>
		<?
	}
	// status acordo
	$sql = "SELECT a.status_id,
				b.idiomastatus_titulo AS status_titulo, b.idiomastatus_descricao AS status_descricao
				FROM tb_cad_status a
				INNER JOIN tb_cad_idioma_status b ON a.status_id = b.status_id
				INNER JOIN tb_prod_perfil_status c ON a.status_id = c.status_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				WHERE a.status_id IN (" . STATUS_OS_ACORDO . ") AND a.status_ativo = 'S'
				AND a.status_id > '" . $tmp_status["status_id"] . "'
				AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'";
	//$result_filtro = $conn->sql($sql);
	while($tmp_filtro = mysqli_fetch_array($result_filtro)){
		?>
		<option title="<?=$tmp_filtro["status_descricao"]?>" alt="<?=$tmp_filtro["status_descricao"]?>" value="<?=$tmp_filtro["status_id"]?>" <? if ($status_id == $tmp_filtro["status_id"]) echo "selected"; ?> ><?=$tmp_filtro["status_titulo"]?></option>
		<?
	}

	//fora de uso
	// status cancelado
	$sql = "SELECT a.status_id,
				b.idiomastatus_titulo AS status_titulo, b.idiomastatus_descricao AS status_descricao
				FROM tb_cad_status a
				INNER JOIN tb_cad_idioma_status b ON a.status_id = b.status_id
				INNER JOIN tb_prod_perfil_status c ON a.status_id = c.status_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				WHERE a.status_id IN (" . STATUS_OS_CANCELADO . ") AND a.status_ativo = 'S'
				AND a.status_id > '" . $tmp_status["status_id"] . "'
				AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'";
	//$result_filtro = $conn->sql($sql);
	while($tmp_filtro = mysqli_fetch_array($result_filtro)){
		?>
		<option title="<?=$tmp_filtro["status_descricao"]?>" alt="<?=$tmp_filtro["status_descricao"]?>" value="<?=$tmp_filtro["status_id"]?>" <? if ($status_id == $tmp_filtro["status_id"]) echo "selected"; ?> ><?=$tmp_filtro["status_titulo"]?></option>
		<?
	}

	//Casos de CID da PC Link (Onde o técnico enviará para finalizado)
	if ($clienteconfig_id == "51" && $os_sub_status == 'CID') {
		$sql = "SELECT a.status_id,
				b.idiomastatus_titulo AS status_titulo, b.idiomastatus_descricao AS status_descricao
				FROM tb_cad_status a
				INNER JOIN tb_cad_idioma_status b ON a.status_id = b.status_id
				INNER JOIN tb_prod_perfil_status c ON a.status_id = c.status_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				WHERE a.status_id IN (" . STATUS_OS_FINALIZADO . ") AND a.status_ativo = 'S'
				AND a.status_id > '" . $tmp_status["status_id"] . "'
				AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'";
		$result_filtro = $conn->sql($sql);
		while($tmp_filtro = mysqli_fetch_array($result_filtro)){
		?>
		<option title="<?=$tmp_filtro["status_descricao"]?>" alt="<?=$tmp_filtro["status_descricao"]?>" value="<?=$tmp_filtro["status_id"]?>" <? if ($status_id == $tmp_filtro["status_id"]) echo "selected"; ?> ><?=$tmp_filtro["status_titulo"]?></option>
		<?
		}
	}

	// Barrafix enviar para Ag. Peca
	if ($clienteconfig_id == "93") {
		$sql = "SELECT a.status_id,
				b.idiomastatus_titulo AS status_titulo, b.idiomastatus_descricao AS status_descricao
				FROM tb_cad_status a
				INNER JOIN tb_cad_idioma_status b ON a.status_id = b.status_id
				INNER JOIN tb_prod_perfil_status c ON a.status_id = c.status_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				WHERE a.status_id IN (" . STATUS_OS_AGUARDA_PECA . "," . STATUS_OS_REPARO . ") AND a.status_ativo = 'S'
				AND a.status_id > '" . $tmp_status["status_id"] . "'
				AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'";
				$result_filtro = $conn->sql($sql);
				while($tmp_filtro = mysqli_fetch_array($result_filtro)){
					?>
		<option title="<?=$tmp_filtro["status_descricao"]?>" alt="<?=$tmp_filtro["status_descricao"]?>" value="<?=$tmp_filtro["status_id"]?>" <? if ($status_id == $tmp_filtro["status_id"]) echo "selected"; ?> ><?=$tmp_filtro["status_titulo"]?></option>
		<?
		}
	}
	

	if ($clienteconfig_id == "97" || $clienteconfig_id == "127") {
		$sql = "SELECT a.status_id,
				b.idiomastatus_titulo AS status_titulo, b.idiomastatus_descricao AS status_descricao
				FROM tb_cad_status a
				INNER JOIN tb_cad_idioma_status b ON a.status_id = b.status_id
				INNER JOIN tb_prod_perfil_status c ON a.status_id = c.status_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				WHERE a.status_id IN (" . STATUS_OS_AGUARDA_PECA . ") AND a.status_ativo = 'S'
				AND a.status_id > '" . $tmp_status["status_id"] . "'
				AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'";
		$result_filtro = $conn->sql($sql);

		while($tmp_filtro = mysqli_fetch_array($result_filtro)){
			?>
			<option title="<?=$tmp_filtro["status_descricao"]?>" alt="<?=$tmp_filtro["status_descricao"]?>" value="<?=$tmp_filtro["status_id"]?>" <? if ($status_id == $tmp_filtro["status_id"]) echo "selected"; ?> ><?=$tmp_filtro["status_titulo"]?></option>
			<?
		}
	}
	

}

// -----------------
// get_servico_lista
// -----------------
if ($acao == "get_servico_lista"){
	// obter serviços
	$sql = "SELECT a.*, b.*
				FROM tb_prod_os_servico a
				LEFT JOIN tb_cad_servico b ON a.servico_id = b.servico_id
				WHERE a.os_id = '$os_id'
				ORDER BY b.titulo";
	$result_filtro = $conn->sql($sql);
	if (mysqli_num_rows($result_filtro) > 0){
		?>
		<input type="hidden" name="servico_add" id="servico_add" value="S" />
		<?
	}else{
		?>
		<input type="hidden" name="servico_add" id="servico_add" value="" />
		<?
	}
	?>

	<script type="text/javascript" src="js/jquery.maskMoney.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			// configuração formato moeda
			$(".moeda").maskMoney({decimal:",", thousands:".", precision:2});
		});
	</script>
	
	
	<div class="row">
		<div class="campos-form">
			<div class="input-control text">
				Lista de Serviços: 
				<?if ($nfs!=''){?>
					<div class="alert info">
				  		<strong>Info!</strong> <?=$nfs?>
					</div>
				<?php
				}?>
				<table class="striped bordered hovered" style="width: 100%;">
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th class="text-center">Título</th>
							<th class="text-center">Valor</th>
							<th class="text-center">Desconto</th>
							<th class="text-center"></th>
						</tr>
					</thead>
					<tbody>
						<?
						$cont_servico = 0;
						while($tmp_filtro = mysqli_fetch_array($result_filtro)){
							?>
							<tr>
								<td class="text-center"><?=++$cont_servico?></td>
								<td class="text-center" title="<?=$tmp_filtro["titulo"]?>" alt="<?=$tmp_filtro["titulo"]?>"><?=$tmp_filtro["titulo"]?></td>
								<td class="text-center">
									<div class="input-control text">
										<input <?if ($nfs!='') echo 'disabled="true" '?> class="moeda" type="text" id="osservico_valor_<?=$tmp_filtro["osservico_id"]?>" name="osservico_valor_<?=$tmp_filtro["osservico_id"]?>" value="<?=number_format($tmp_filtro["osservico_valor"], 2, ',', '.')?>"  />
									</div>
								</td>
								<td class="text-center">
									<div class="input-control text">
										<input <?if ($nfs!='') echo 'disabled="true" '?> class="moeda" type="text" id="osservico_desconto_<?=$tmp_filtro["osservico_id"]?>" name="osservico_desconto_<?=$tmp_filtro["osservico_id"]?>" value="<?=number_format($tmp_filtro["osservico_desconto"], 2, ',', '.')?>"  />
									</div>
								</td>

								<td class="text-center">
									<?if ($nfs!='') {}else{?>
									<a href="javascript: void(0);" onClick="updtServicoLista('<?=$tmp_filtro["osservico_id"]?>');" title="Alterar" alt="Alterar"><i class="icon-save"></i></a>
									<a href="javascript: void(0);" onClick="excluiServicoLista('<?=$tmp_filtro["osservico_id"]?>');" title="Excluir" alt="Excluir"><i class="icon-cancel"></i></a>
									<?}?>
								</td>
							</tr>
							<?
						}
						?>
					</tbody>
				<table>
			</div>
		</div>
	</div>
	
	<!-- selecionar e adicionar serviço -->
	<?if ($nfs!='') {}else{?>
	<div class="row">
		<div class="span6 campos-form">
			<div class="input-control select">
				<label>
					Adicionar Serviço
					<i class="icon-help" title="" alt=""></i>
					<div class="tooltip">
						Selecione o Tipo do Serviço utilizado no reparo do produto
					</div>
				</label>
				<select name="servico_id" id="servico_id" OnChange="javascript: void(0); addServicoLista();">
					<option value=""><?=fct_get_var('global.php', 'var_selecione', $_SESSION["care-br"]["idioma_id"])?></option>
					<?
					// trazer somente peças que tenham fabricante e linha configurados para acesso no perfil do usuário logado
					// somente peças que ainda não estejam adicionadas ao reparo do produto
					$sql = "SELECT DISTINCT a.servico_id,
									a.titulo
									FROM tb_cad_servico a
									WHERE a.servico_id NOT IN (SELECT servico_id
																FROM tb_prod_os_servico
																WHERE os_id = '" . $os_id . "')
										AND a.cliente_id = $cliente_id AND a.clienteconfig_id = $clienteconfig_id
									ORDER BY a.titulo";
					$result_filtro = $conn->sql($sql);
					while($tmp_filtro = mysqli_fetch_array($result_filtro)){
						?>
						<option value="<?=$tmp_filtro["servico_id"]?>" title="<?=$tmp_filtro["titulo"]?>" alt="<?=$tmp_filtro["titulo"]?>" <? if ($tmp_filtro["servico_id"] == $servico_id) echo "selected"; ?> ><?=$tmp_filtro["titulo"]?></option>
						<?
					}
					?>
				</select>
			</div>
		</div>
	</div>
	<?}?>
	<?
}

// -----------------
// viz_servico_lista
// -----------------
if ($acao == "viz_servico_lista"){
	// obter serviços
	$sql = "SELECT a.*, b.*
				FROM tb_prod_os_servico a
				LEFT JOIN tb_cad_servico b ON a.servico_id = b.servico_id
				WHERE a.os_id = '$os_id'
				ORDER BY b.titulo";
	$result_filtro = $conn->sql($sql);
	?>

	<script type="text/javascript" src="js/jquery.maskMoney.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			// configuração formato moeda
			$(".moeda").maskMoney({decimal:",", thousands:".", precision:2});
		});
	</script>
	
	<div class="row">
		<div class="campos-form">
			<div class="input-control text">
				Lista de Serviços:
				<table class="striped bordered hovered" style="width: 98%;">
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th class="text-center">Título</th>
							<th class="text-center">Valor</th>
							<th class="text-center">Desconto</th>
						</tr>
					</thead>
					<tbody>
						<?
						$cont_servico = 0;
						while($tmp_filtro = mysqli_fetch_array($result_filtro)){
							?>
							<tr>
								<td class="text-center"><?=++$cont_servico?></td>
								<td class="text-center" title="<?=$tmp_filtro["titulo"]?>" alt="<?=$tmp_filtro["titulo"]?>"><?=$tmp_filtro["titulo"]?></td>
								<td class="text-center">
									<div class="input-control text">
										<input class="moeda" type="text" disabled id="osservico_valor_<?=$tmp_filtro["osservico_id"]?>" name="osservico_valor_<?=$tmp_filtro["osservico_id"]?>" value="<?=number_format($tmp_filtro["osservico_valor"], 2, ',', '.')?>"  />
									</div>
								</td>
								<td class="text-center">
									<div class="input-control text">
										<input class="moeda" type="text" disabled id="osservico_desconto_<?=$tmp_filtro["osservico_id"]?>" name="osservico_desconto_<?=$tmp_filtro["osservico_id"]?>" value="<?=number_format($tmp_filtro["osservico_desconto"], 2, ',', '.')?>"  />
									</div>
								</td>
							</tr>
							<?
						}
						?>
					</tbody>
				<table>
			</div>
		</div>
	</div>
	<?
}

// -----------------
// get_servico_lista
// -----------------
if ($acao == "orc_servico_lista"){
	// obter serviços
	$sql = "SELECT a.*, b.*
				FROM tb_prod_os_servico a
				LEFT JOIN tb_cad_servico b ON a.servico_id = b.servico_id
				WHERE a.os_id = '$os_id'
				ORDER BY b.titulo";
	$result_filtro = $conn->sql($sql);
	if (mysqli_num_rows($result_filtro) > 0){
		?>
		<input type="hidden" name="servico_add" id="servico_add" value="S" />
		<?
	}else{
		?>
		<input type="hidden" name="servico_add" id="servico_add" value="" />
		<?
	}
	?>	

	<script type="text/javascript" src="js/jquery.maskMoney.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			// configuração formato moeda
			$(".moeda").maskMoney({decimal:",", thousands:".", precision:2});
		});
	</script>
		
	<div class="row">
		<div class="campos-form">
			<div class="input-control text">
				Lista de Serviços:
				<?if ($nfs!=''){?>
					<div class="alert info">
				  		<strong>Info!</strong> <?=$nfs?>
					</div>
				<?php
				}?> 
				
				<table class="striped bordered hovered" style="width: 98%;">
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th class="text-center">Título</th>
							<th class="text-center">Valor</th>
							<th class="text-center">Desconto</th>
							<th class="text-center"></th>
						</tr>
					</thead>
					<tbody>
						<?
						$cont_servico = 0;
						while($tmp_filtro = mysqli_fetch_array($result_filtro)){
							?>
							<tr>
								<td class="text-center"><?=++$cont_servico?></td>
								<td class="text-center" title="<?=$tmp_filtro["titulo"]?>" alt="<?=$tmp_filtro["titulo"]?>"><?=$tmp_filtro["titulo"]?></td>
								<td class="text-center">
									<div class="input-control text">
										<input <?if ($nfs!='') echo 'disabled="true" '?> class="moeda" type="text" id="osservico_valor_<?=$tmp_filtro["osservico_id"]?>" name="osservico_valor_<?=$tmp_filtro["osservico_id"]?>" value="<?=number_format($tmp_filtro["osservico_valor"], 2, ',', '.')?>"  />
									</div>
								</td>
								<td class="text-center">
									<div class="input-control text">
										<input <?if ($nfs!='') echo 'disabled="true" '?> class="moeda" type="text" id="osservico_desconto_<?=$tmp_filtro["osservico_id"]?>" name="osservico_desconto_<?=$tmp_filtro["osservico_id"]?>" value="<?=number_format($tmp_filtro["osservico_desconto"], 2, ',', '.')?>"  />
									</div>
								</td>
								<td class="text-center">
									<?if ($nfs!='') {}else{?> 
									<a href="javascript: void(0);" onClick="updtServicoLista('<?=$tmp_filtro["osservico_id"]?>');" title="Alterar" alt="Alterar"><i class="icon-save"></i></a>
									<a href="javascript: void(0);" onClick="excluiServicoLista('<?=$tmp_filtro["osservico_id"]?>');" title="Excluir" alt="Excluir"><i class="icon-cancel"></i></a>
									<?}?>
								</td>
							</tr>
							<?
						}
						?>
					</tbody>
				<table>
			</div>
		</div>
	</div>
	<?
}

?>

<?
$conn->fechar();
?>
