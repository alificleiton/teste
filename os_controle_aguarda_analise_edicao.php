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

if ($clienteconfig_id == 123) {
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

// --------------
// get_peca_lista
// --------------
if ($acao == "get_peca_lista"){
	
	if($clienteconfig_id == '11'){
		// obter peças já solicitadas
		$sql = "SELECT (SELECT nf_item.det_prod_vUnCom FROM tb_prod_nfe_item nf_item 
			INNER JOIN tb_prod_nfe nf
			ON nf.nfe_id = nf_item.nfe_id
		and ( nf.clienteconfig_id = '".$clienteconfig_id."' or nf.clienteconfig_id = 7 or nf.clienteconfig_id = 190) 
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
        if ($clienteconfig_id == 123) 
            $order = "ORDER BY a.osprodutopeca_id";
        
		// obter peças já solicitadas
		$sql = "SELECT (SELECT nf_item.det_prod_vUnCom FROM tb_prod_nfe_item nf_item 
			INNER JOIN tb_prod_nfe nf
			ON nf.nfe_id = nf_item.nfe_id
		and  nf.clienteconfig_id = '".$clienteconfig_id."' 
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
					$order";
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
    while ($tmp_cadastro = mysqli_fetch_array($rs,MYSQL_ASSOC)){

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
		});
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
				Lista de Peças 22
				<table class="striped bordered hovered" style="width: 98%;">
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th class="text-center">Código</th>
							<th class="text-center">Peça</th>
							<? if ($clienteconfig_id == 136) { ?>
									<th class="text-center">Valor GSPN</th>
							<? } ?>
							<th class="text-center">Valor Fab.</th>
							<th class="text-center">Valor Unit.</th>
							<th class="text-center">Vlr c/ IPI</th>
							<th class="text-center">Qtde para Reparo</th>
							<th class="text-center">Qtde em Estoque</th>
							<th class="text-center">Status Peça</th>
                            <?
                                if ($clienteconfig_id == 123) {
                                    echo '<th class="text-center">Valor Venda</th>';
                                    echo '<th class="text-center">Desconto</th>';
                                    echo '<th class="text-center">M.O.</th>';
                                    echo '<th class="text-center">Total</th>';
                                }
                                ?>
                            <th class="text-center">Cobrar</th>
							<th class="text-center"></th>
						</tr>
					</thead>
					<tbody>
						<?
						$cont_peca = 0;
                        $cont_total = 0;
                        $mo = 0;
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
								<td>
                                    <?
                                        echo $tmp_filtro["produto_descricao"];
                                        if ($clienteconfig_id == 123) {
                                            if ($tmp_filtro["osprodutopeca_complementar"] == 'S') {
                                                $complementar = 'Peça é complementar';
                                                echo "<br><b>$complementar</b>";
                                            }
                                        }
                                    ?>
                                </td>

                                <? if ($clienteconfig_id == 136) { ?>
	                                <td>
	                                	<?php 
													 													
													$sql 			 = "SELECT cliente_id, empresacliente_id FROM tb_prod_care_cliente_config WHERE clienteconfig_id = $clienteconfig_id";
													$res_cliente 	 = $conn->getData($sql);
													$produto_codigo = $tmp_filtro["produto_codigo"];
													$empresacliente_id_gspn = $res_cliente[0]['empresacliente_id'];

													$sql 		= "SELECT preco_gspn_valor  FROM tb_cad_preco_gspn_variacao WHERE produto_gspn_codigo = '$produto_codigo' and empresacliente_id = $empresacliente_id_gspn";
													$res 		= $conn->getData($sql);
													if (empty($res[0]['produto_gspn_valor']) ){
														$tmp_filtro["valor_unitario_te"]=0;
													}else{
                                                        $tmp_filtro["valor_unitario_te"] = $res[0]['produto_gspn_valor'];
												    }
													
												


										?>
										<input type="text" id="valor_nf_ssg" name="valor_nf_ssg" value="<?=number_format($tmp_filtro["valor_unitario_te"], 2, ',', '.')?>" style="width: 70px; text-align:right;" disabled />
										<input type="hidden" id='osprodutopeca_custo_<?=$tmp_filtro["osprodutopeca_id"]?>' value='<?=$tmp_filtro["valor_unitario_te"]?>' />
	                                </td>
                            	<? } ?>
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

                                <?
                                    $readonly = '';
                                    $sim = fct_get_var('global.php', 'var_sim', $_SESSION["care-br"]["idioma_id"]);
                                    $nao = fct_get_var('global.php', 'var_nao', $_SESSION["care-br"]["idioma_id"]);
                                    // B2X Moema grava os valores da peça no Análise, e M.O. sempre será 150 reais na primeira peça
                                    if ($clienteconfig_id == 123) {
                                        $valor_venda = $tmp_filtro['osprodutopeca_valor_venda'];
                                        $cobertura = $_REQUEST["os_cobertura"];
                                        $produto_id_peca = $tmp_filtro["produto_id_peca"];
                                        if ((empty($valor_venda) || $valor_venda == '0.00') && $produto_id_peca != 314501 && $produto_id_peca != 314502 && $produto_id_peca != 314503) {
                                            $sql = "SELECT empresacliente_id, cliente_id FROM tb_prod_care_cliente_config WHERE clienteconfig_id = '$clienteconfig_id'";
                                            $result = $conn->sql($sql);
                                            $tmp = mysqli_fetch_array($result);
                                            $empresacliente_id = $tmp['empresacliente_id'];
                                            $cliente_id = $tmp['cliente_id'];
                                            $cobertura = substr($cobertura, 0, 3);
                                            $sql = "SELECT valor_venda 
                                                FROM tb_cad_tabela_produto_venda 
                                                WHERE cliente_id = '$cliente_id' 
                                                AND empresacliente_id = '$empresacliente_id' 
                                                AND cobertura = '$cobertura'
                                                AND produto_id = '" . $tmp_filtro["produto_id_peca"] . "'";
                                            $result = $conn->sql($sql);
                                            $tmp = mysqli_fetch_array($result);
                                            $valor_venda = $tmp['valor_venda'];
                                            $valor_venda = str_replace('.', ',', $valor_venda);
                                        }
                                        $total_venda += $tmp_filtro['osprodutopeca_valor_venda'];
                                        $total_desconto += $tmp_filtro['osprodutopeca_valor_desconto'];
                                        $total_mao_obra += $tmp_filtro['osprodutopeca_valor_mao_obra'];

                                        if (($tmp_filtro['osprodutopeca_valor_mao_obra'] == '0.00' || empty($tmp_filtro['osprodutopeca_valor_mao_obra'])) && $mo == 0 && $produto_id_peca != 157066 && $produto_id_peca != 164976 && $produto_id_peca != 165440 && $produto_id_peca != 166344 && $produto_id_peca != 167730 && $produto_id_peca != 168534 && $produto_id_peca != 168535 && $produto_id_peca != 170922 && $produto_id_peca != 170965 && $produto_id_peca != 170966 && $produto_id_peca != 170996 && $produto_id_peca != 171139 && $produto_id_peca != 171980 && $produto_id_peca != 171995 && $produto_id_peca != 172332 && $produto_id_peca != 172344 && $produto_id_peca != 172345 && $produto_id_peca != 172966 && $produto_id_peca != 177046 && $produto_id_peca != 177612 && $produto_id_peca != 177613 && $produto_id_peca != 179748 && $produto_id_peca != 206556 && $produto_id_peca != 209091 && $produto_id_peca != 282032 && $produto_id_peca != 282611 && $produto_id_peca != 283200 && $produto_id_peca != 284922 && $produto_id_peca != 284927 && $produto_id_peca != 284930 && $produto_id_peca != 285950 && $produto_id_peca != 286818 && $produto_id_peca != 287135 && $produto_id_peca != 289315 && $produto_id_peca != 289356 && $produto_id_peca != 290770 && $produto_id_peca != 290988 && $produto_id_peca != 291365 && $produto_id_peca != 304500 && $produto_id_peca != 306888 && $produto_id_peca != 306891 && $produto_id_peca != 306892 && $produto_id_peca != 306893 && $produto_id_peca != 306915 && $produto_id_peca != 306916 && $produto_id_peca != 306917 && $produto_id_peca != 306918 && $produto_id_peca != 306919 && $produto_id_peca != 306927 && $produto_id_peca != 313691 && $produto_id_peca != 313692 && $produto_id_peca != 313889 && $produto_id_peca != 314501 && $produto_id_peca != 314502 && $produto_id_peca != 314503) {
                                                $tmp_filtro['osprodutopeca_valor_mao_obra'] = '150.00';
                                        } elseif ($produto_id_peca == 157066 || $produto_id_peca == 164976 || $produto_id_peca == 165440 || $produto_id_peca == 166344 || $produto_id_peca == 167730 || $produto_id_peca == 168534 || $produto_id_peca == 168535 || $produto_id_peca == 170922 || $produto_id_peca == 170965 || $produto_id_peca == 170966 || $produto_id_peca == 170996 || $produto_id_peca == 171139 || $produto_id_peca == 171980 || $produto_id_peca == 171995 || $produto_id_peca == 172332 || $produto_id_peca == 172344 || $produto_id_peca == 172345 || $produto_id_peca == 172966 || $produto_id_peca == 177046 || $produto_id_peca == 177612 || $produto_id_peca == 177613 || $produto_id_peca == 179748 || $produto_id_peca == 206556 || $produto_id_peca == 209091 || $produto_id_peca == 282032 || $produto_id_peca == 282611 || $produto_id_peca == 283200 || $produto_id_peca == 284922 || $produto_id_peca == 284927 || $produto_id_peca == 284930 || $produto_id_peca == 285950 || $produto_id_peca == 286818 || $produto_id_peca == 287135 || $produto_id_peca == 289315 || $produto_id_peca == 289356 || $produto_id_peca == 290770 || $produto_id_peca == 290988 || $produto_id_peca == 291365 || $produto_id_peca == 304500 || $produto_id_peca == 306888 || $produto_id_peca == 306891 || $produto_id_peca == 306892 || $produto_id_peca == 306893 || $produto_id_peca == 306915 || $produto_id_peca == 306916 || $produto_id_peca == 306917 || $produto_id_peca == 306918 || $produto_id_peca == 306919 || $produto_id_peca == 306927 || $produto_id_peca == 313691 || $produto_id_peca == 313692 || $produto_id_peca == 313889 || $produto_id_peca == 314501 || $produto_id_peca == 314502 || $produto_id_peca == 314503) {
                                                $tmp_filtro['osprodutopeca_valor_mao_obra'] = '100.00';
                                        }

                                        $rotina_link = fct_get_rotina_invisivel(VAR_MENU_CABECALHO, 'produto_tabela_controle.php', $os_id);
                                        $acesso_liberado = strpos($rotina_link, "permite_valor_tabela_preco");
                                        
                                        $retorno = '';
                                        if (!$acesso_liberado){
											$retorno = 'readonly="true"';
										}
                                        ?>
                                        <td>
                                            <div class="input-control text">
                                                <input class="moeda valid" type="text" maxlength="9" id="osprodutopeca_valor_venda_<?=$tmp_filtro["osprodutopeca_id"]?>" name="osprodutopeca_valor_venda_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=number_format($valor_venda, 2, ',', '.')?>" <?=$retorno?>>
                                                </input>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="input-control text">
                                                <input class="moeda valid" type="text" maxlength="9" id="osprodutopeca_desconto_<?=$tmp_filtro["osprodutopeca_id"]?>" name="osprodutopeca_desconto_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=number_format($tmp_filtro['osprodutopeca_valor_desconto'], 2, ',', '.')?>" <?=$retorno?>>
                                                </input>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="input-control text">
                                                <input class="moeda valid" type="text" maxlength="9" id="osprodutopeca_valor_mao_obra_<?=$tmp_filtro["osprodutopeca_id"]?>" name="osprodutopeca_valor_mao_obra_<?=$tmp_filtro["osprodutopeca_id"]?>" value="<?=number_format($tmp_filtro['osprodutopeca_valor_mao_obra'], 2, ',', '.')?>" <?=$retorno?>>
                                                </input>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-control text">
                                                <?
                                                    $valor_total_peca = $valor_venda + $tmp_filtro['osprodutopeca_valor_mao_obra'] - $tmp_filtro['osprodutopeca_valor_desconto'];
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
										<select class="lista_cobrar" name="osprodutopeca_cobrar_<?=$tmp_filtro["osprodutopeca_id"]?>" id="osprodutopeca_cobrar_<?=$tmp_filtro["osprodutopeca_id"]?>" style="width: 70px; margin:auto;" onchange="valorVenda(<?=$tmp_filtro["osprodutopeca_id"] . ", '". number_format($tmp_filtro["osprodutopeca_valor_venda"], 2, ',', '.') ."',".$tmp_filtro["produto_id_peca"]?>);" <?=$readonly?>>
											<option value=""><?=fct_get_var('global.php', 'var_selecione', $_SESSION["care-br"]["idioma_id"])?></option>
											<option value="S" <? if ($tmp_filtro["osprodutopeca_cobrar"] == "S" || (($clienteconfig_id == '97' || $clienteconfig_id == '168') && empty($tmp_filtro['osproduto_cobrar']))) echo "selected"; ?> ><?=$sim?></option>
											<option value="N" <? if ($tmp_filtro["osprodutopeca_cobrar"] == "N") echo "selected"; ?> ><?=$nao?></option>
										</select>
									</div>
								</td>

								<td class="text-center">
									<a href="javascript: void(0);" onClick="updtPecaLista('<?=$tmp_filtro["osprodutopeca_id"]?>','<?=$tmp_filtro["produto_id_peca"]?>');" title="Alterar" alt="Alterar"><i class="icon-save"></i></a>
									<a href="javascript: void(0);" onClick="excluiCadastro('<?=$tmp_filtro["osprodutopeca_id"]?>', '<?=$tmp_filtro["produto_id_peca"]?>');" title="Excluir" alt="Excluir"><i class="icon-cancel"></i></a>
								</td>
							</tr>
							<?
							if ($clienteconfig_id == 20 || $clienteconfig_id == 82 || $clienteconfig_id == 136 || $clienteconfig_id == 137 || $clienteconfig_id == 138 || $clienteconfig_id == 139) {
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
                            if ($clienteconfig_id == 123) {
                                $total_geral = $total_venda + $total_mao_obra - $total_desconto;
                                $total_geral = number_format($total_geral, 2, ',', '.');
                                ?>
                                <tr>
                                    <td colspan="9" class="text-center"><b>Totais</b></td>
                                    <td class="text-center"><b><?=number_format($total_venda, 2, ',', '.');?></b></td>
                                    <td class="text-center"><b><?=number_format($total_desconto, 2, ',', '.');?></b></td>
                                    <td class="text-center"><b><?=number_format($total_mao_obra, 2, ',', '.');?></b></td>
                                    <td class="text-center"><b><?=$total_geral?></b></td>
                                    <td colspan="2"></td>
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
						if ($clienteconfig_id==7 || $clienteconfig_id==190 ){
                        	$sql_status_id = " AND (SELECT SUM(qtde) AS 'qtde' FROM tb_prod_estoque_status pes INNER JOIN tb_prod_estoque pe ON (pes.estoque_id = pe.estoque_id) WHERE pes.status_id = '60' AND pe.categoria_id = '21' AND pe.produto_id = estoque.produto_id AND pe.estoque_id = estoque.estoque_id) IS NOT NULL AND (SELECT SUM(qtde) AS 'qtde' FROM tb_prod_estoque_status pes INNER JOIN tb_prod_estoque pe ON (pes.estoque_id = pe.estoque_id) WHERE pes.status_id = '60' AND pe.categoria_id = '21' AND pe.produto_id = estoque.produto_id AND pe.estoque_id = estoque.estoque_id) > '0.0' ";
                        
                        
							$sqlestoque = "SELECT estoque.estoque_id as 'Estoque'
								FROM tb_prod_estoque estoque
							 	INNER JOIN tb_cad_produto produto ON estoque.produto_id = produto.produto_id 
								INNER JOIN tb_cad_produto produto_idioma ON produto.produto_id = produto_idioma.produto_id 
								INNER JOIN tb_cad_empresa fabricante ON (produto.empresa_id=fabricante.empresa_id) 
								INNER JOIN tb_cad_linha linha_idioma ON produto.linha_id = linha_idioma.linha_id 
								INNER JOIN tb_cad_estoque_categoria categoria_idioma ON estoque.categoria_id = categoria_idioma.categoria_id 
								LEFT JOIN tb_cad_estoque_pallet epa ON estoque.pallet_id = epa.pallet_id 
								LEFT JOIN tb_cad_estoque_prateleira epr ON epa.prateleira_id = epr.prateleira_id 
								LEFT JOIN tb_cad_estoque_rua er ON er.rua_id = epr.rua_id 
								LEFT JOIN tb_prod_empresa_cliente ec ON(er.empresacliente_id=ec.empresacliente_id) 
								LEFT JOIN tb_cad_produto_categoria f ON produto.produto_categoria_id = f.categoria_id 
	   
								   WHERE estoque.categoria_id in ('21') and (estoque.empresacliente_id ='29' or estoque.empresacliente_id ='939')  and produto.produto_id= '".$tmp_filtro["produto_id_peca"]."' ".$sql_status_id." ";
							$result_filtro_estoque = $conn->sql($sqlestoque);
							$estoque_id=0;
	                        while($tmp_estoque = mysqli_fetch_array($result_filtro_estoque)){
	                        		$estoque_id=$tmp_estoque["Estoque"];

	                        	}
	                        	$estoque=" -- 0";
	                        if  ( empty($estoque_id)){}
	                        else{
		                        	$sqlquantidade="SELECT sum(qtde) as Estoque
		    						  FROM tb_prod_estoque_status status
		    						WHERE estoque_id=".$estoque_id." and status_id=60" ;
		                        
		                        	$result_filtro_quantidade = $conn->sql($sqlquantidade);
		                       	while($tmp_quantidade= mysqli_fetch_array($result_filtro_quantidade)){
		                        		$estoque=" -- ".$tmp_quantidade["Estoque"];

		                        } 
	                        } 
                        }
                        else{
                    	  $estoque="";
                        }
						?>
						<option value="<?=$tmp_filtro["produto_id_peca"]?>" title="<?=$tmp_filtro["produto_descricao"]?>" alt="<?=$tmp_filtro["produto_descricao"]?>" <?if ($tmp_filtro["produto_id_peca"] == $produto_id_peca) echo "selected"; ?> ><?=$tmp_filtro["produto_codigo"]?> - <?=$tmp_filtro["produto_descricao"]?><?=$estoque?></option>
						<?
					}
					?>
				</select>
			</div>
		</div>
	</div>



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
    while ($tmp_cadastro = mysqli_fetch_array($rs,MYSQL_ASSOC)){

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
							if ($clienteconfig_id == 20 || $clienteconfig_id == 82 || $clienteconfig_id == 136 || $clienteconfig_id == 137 || $clienteconfig_id == 138 || $clienteconfig_id == 139) {
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
            || (($os_cobertura == 'BullitFG') && ($clienteconfig_id==85 || $clienteconfig_id==199 ))
            || (($os_cobertura == 'AvulsoFG') && ($clienteconfig_id==85 || $clienteconfig_id==199))
            || (($os_cobertura == 'CorporativoFG') && ($clienteconfig_id==85 || $clienteconfig_id==199))
            || (($os_cobertura == 'RenoveTecFG') && ($clienteconfig_id==85 || $clienteconfig_id==199))
			|| ($os_cobertura == 'ZURICH-QA') 
			|| ($os_cobertura == 'ZURICH-FAST-QA') 
			|| ($os_cobertura == 'ZURICH HAVAH QA')
			|| ($os_cobertura == 'LUIZASEG-QA') 
			|| ($os_cobertura == 'ORCAMENTO')
			|| ($os_cobertura == 'Cardif')
			|| ($os_cobertura == 'Luizaseg')
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
			|| ($os_cobertura == 'FORAGARANTIASAMSUNGBRANCA')
			|| ($os_cobertura == 'GARANTIA-SAMSUNGBRANCA')
			|| ($os_cobertura == 'FORADEGARANTIA-IHLB')
			|| ($os_cobertura == 'FORAGARANTIA-B2WCELULAR')
			|| ($os_cobertura == 'FORAGARANTIA-B2WMARROM')
			|| ($os_cobertura == 'FORADEGARANTIAINHOME')
			|| ($os_cobertura == 'FORA GARANTIA-SITE')
			|| strpos($os_cobertura,'ORCAMENTO')){
										
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
			if($clienteconfig_id==85 || $clienteconfig_id==199){
				$sql = "SELECT d.status_id,
				a.clienteconfigpasso_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
				FROM tb_prod_care_cliente_config_passo a
				INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
				INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
				INNER JOIN tb_cad_status d ON c.status_id = d.status_id
				INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
				INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
				AND d.status_id IN (" . $status_id . "," . STATUS_OS_AGUARDA_ORCAMENTO . ", ". STATUS_OS_EMBALAGEM.")
				AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
				ORDER BY a.clienteconfigpasso_ordem ";
			}

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
					AND d.status_id IN (" . $status_id . "," . STATUS_OS_AGUARDA_ORCAMENTO . ")
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
			if($clienteconfig_id==85 || $clienteconfig_id==199){
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
				AND d.status_id <> '" . STATUS_OS_REPARO . "'
				AND d.status_id <> '" . STATUS_OS_TESTE . "'
				AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
				ORDER BY a.clienteconfigpasso_ordem LIMIT 0,4";
			}

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
    
    if ($clienteconfig_id == '123') {
        switch ($os_peca_precisa) {
            case 'S':
                $peca_complementar = '';
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
                }
                if ($peca_complementar == 'S') {
                    $status = "10, 12";
                } elseif ($peca_complementar == 'MO') {
                    $status = "10, 24";
                } else {
                    $status = "10, 14";
                }
            break;
            case 'N':
                $status = "10, 16";
            break;
            default:
                $status = "10";
            break;
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

	// Barrafix / Helpsam / Edatec enviar para Ag. Peca
	if ($clienteconfig_id == "93" || $clienteconfig_id == "130" || $clienteconfig_id == "163" || $clienteconfig_id == "176" || $clienteconfig_id == "178"  ) {
		
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

	// Fixcenter
	if ($clienteconfig_id == "131" || $clienteconfig_id == "132") {
		
		$sql = "SELECT a.status_id,
				b.idiomastatus_titulo AS status_titulo, b.idiomastatus_descricao AS status_descricao
				FROM tb_cad_status a
				INNER JOIN tb_cad_idioma_status b ON a.status_id = b.status_id
				INNER JOIN tb_prod_perfil_status c ON a.status_id = c.status_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				WHERE a.status_id IN (" . STATUS_OS_APROVA_ORCAMENTO . "," . STATUS_OS_AGUARDA_PECA . "," . STATUS_OS_REPARO . ") AND a.status_ativo = 'S'
				AND a.status_id > '" . $tmp_status["status_id"] . "'
				AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'";
				$result_filtro = $conn->sql($sql);

		while($tmp_filtro = mysqli_fetch_array($result_filtro)){
			?>
			<option title="<?=$tmp_filtro["status_descricao"]?>" alt="<?=$tmp_filtro["status_descricao"]?>" value="<?=$tmp_filtro["status_id"]?>" <? if ($status_id == $tmp_filtro["status_id"]) echo "selected"; ?> ><?=$tmp_filtro["status_titulo"]?></option>
			<?
		}
	}
	

	if ($clienteconfig_id == "97" || $clienteconfig_id == "127" || $clienteconfig_id == "168") {
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

	// JMV Suzano e FILIAL incluir o ag. orçamento
	if ($clienteconfig_id == "138" || $clienteconfig_id == "82" ) {
		
		$sql = "SELECT a.status_id,
				b.idiomastatus_titulo AS status_titulo, b.idiomastatus_descricao AS status_descricao
				FROM tb_cad_status a
				INNER JOIN tb_cad_idioma_status b ON a.status_id = b.status_id
				INNER JOIN tb_prod_perfil_status c ON a.status_id = c.status_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				WHERE a.status_id IN (" . STATUS_OS_AGUARDA_ORCAMENTO . ") AND a.status_ativo = 'S'
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
	// obter serviços já informados
	$sql = "SELECT a.*,
				b.idiomaservico_titulo AS servico_titulo, b.idiomaservico_descricao AS servico_descricao
				FROM tb_prod_os_tipo_servico a
				INNER JOIN tb_cad_idioma_os_tipo_servico b ON a.servico_id = b.servico_id AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
				WHERE a.os_id = '" . $os_id . "'
				ORDER BY b.idiomaservico_titulo";
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

	<script type="text/javascript">
		$(document).ready(function() {
			// configuração formato moeda
			$(".moeda").maskMoney({decimal:",", thousands:".", precision:2});
		});
	</script>
	
	<div class="row">
		<!--<div class="span6 campos-form">-->
			<div class="input-control text">
				Lista de Serviços:
				<table class="striped bordered hovered">
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th class="text-center">Tipo do Serviço</th>
							<th class="text-center">Valor</th>
							<th class="text-center">Comentário</th>
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
								<td class="text-center" title="<?=$tmp_filtro["servico_descricao"]?>" alt="<?=$tmp_filtro["servico_descricao"]?>"><?=$tmp_filtro["servico_titulo"]?></td>
								<td class="text-center">
									<div class="input-control text">
										<input class="moeda" type="text" id="osservico_valor_<?=$tmp_filtro["osservico_id"]?>" name="osservico_valor_<?=$tmp_filtro["osservico_id"]?>" value="<?=number_format($tmp_filtro["osservico_valor"], 2, ',', '.')?>"  />
									</div>
								</td>
								<td class="text-center">
									<div class="input-control text">
										<input type="text" id="osservico_observacao_<?=$tmp_filtro["osservico_id"]?>" name="osservico_observacao_<?=$tmp_filtro["osservico_id"]?>" value="<?=$tmp_filtro["osservico_observacao"]?>" />
									</div>
								</td>
								<td class="text-center">
									<a href="javascript: void(0);" onClick="updtServicoLista('<?=$tmp_filtro["osservico_id"]?>');" title="Alterar" alt="Alterar"><i class="icon-save"></i></a>
									<a href="javascript: void(0);" onClick="excluiServicoLista('<?=$tmp_filtro["osservico_id"]?>');" title="Excluir" alt="Excluir"><i class="icon-cancel"></i></a>
								</td>
							</tr>
							<?
						}
						?>
					</tbody>
				<table>
			</div>
		<!--</div>-->
	</div>
	
	<!-- selecionar e adicionar peça -->
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
									b.idiomaservico_titulo AS servico_titulo, b.idiomaservico_descricao AS servico_descricao
									FROM tb_cad_os_tipo_servico a
									INNER JOIN tb_cad_idioma_os_tipo_servico b ON a.servico_id = b.servico_id AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
									INNER JOIN tb_cad_os_tipo_servico_empresa_cliente c ON a.servico_id = c.servico_id AND c.clienteconfig_id = '" . $clienteconfig_id . "'
									WHERE a.servico_ativo = 'S'
									AND a.servico_id NOT IN (SELECT servico_id
																FROM tb_prod_os_tipo_servico
																WHERE os_id = '" . $os_id . "')
									ORDER BY b.idiomaservico_titulo";
					$result_filtro = $conn->sql($sql);
					while($tmp_filtro = mysqli_fetch_array($result_filtro)){
						?>
						<option value="<?=$tmp_filtro["servico_id"]?>" title="<?=$tmp_filtro["servico_descricao"]?>" alt="<?=$tmp_filtro["servico_descricao"]?>" <? if ($tmp_filtro["servico_id"] == $servico_id) echo "selected"; ?> ><?=$tmp_filtro["servico_titulo"]?></option>
						<?
					}
					?>
				</select>
			</div>
		</div>
	</div>
	<?
}

?>

<?
$conn->fechar();
?>
