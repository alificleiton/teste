<?
//$allowSession = "nao";
require_once("lib/configs.php");
require_once("multi_idioma_request.php");
require_once("os_funcoes.php");

// setar sessão com nome da página para ser usada no controle de acesso
$_SESSION["care-br"]["submodulo_pagina"] = "os_controle_aguarda_analise.php";

// request
$acao = $_REQUEST["acao"];
if (empty($acao)) 	$acao = "add";

// configurações do cliente
$cliente_id = $_REQUEST["cliente_id"];
$clienteconfig_id = $_REQUEST["clienteconfig_id"];
$produto_id_emprestimo = $_REQUEST["produto_id_emprestimo"];

// request
$os_id = $_REQUEST["os_id"];

$status_id = STATUS_OS_AGUARDA_ANALISE;

$sql_os = "SELECT os_orcamento_rapido FROM tb_prod_os WHERE os_id='$os_id'";
$res_os = $conn->sql($sql_os);
$obj_os = mysqli_fetch_object($res_os);

//casos de OS que foram selecionado orcamento rapido
// if($obj_os->os_orcamento_rapido=='S' || $clienteconfig_id == '79'){
// 	$acao = 'add_orc_rapido';
// }

// ---------------
// leitura da peça
// ---------------
if ($acao == "add"){
	include("header.php");
	
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
				LEFT JOIN tb_cad_empresa e ON a.empresa_id_varejo = e.empresa_id
				LEFT JOIN tb_prod_os_planner_tecnico pt ON pt.id_planner_tecnico = a.usuario_tecnico_reparo
				INNER JOIN tb_cad_produto f ON a.produto_id = f.produto_id
				INNER JOIN tb_cad_idioma_produto g ON f.produto_id = g.produto_id AND g.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
				INNER JOIN tb_cad_empresa h ON f.empresa_id = h.empresa_id
				INNER JOIN tb_cad_linha i ON f.linha_id = i.linha_id
				INNER JOIN tb_cad_idioma_linha j ON i.linha_id = j.linha_id AND j.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
				INNER JOIN tb_prod_perfil_status k ON a.status_id = k.status_id AND k.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				INNER JOIN tb_prod_perfil_empresa l ON a.empresa_id_fabricante = l.empresa_id AND l.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				INNER JOIN tb_prod_perfil_linha m ON a.linha_id = m.linha_id AND m.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
				WHERE os_id = '" . $os_id . "'";
	$result_cadastro = $conn->sql($sql);
	while($tmp_cadastro = mysqli_fetch_array($result_cadastro)){
		$dados_cadastro[] = $tmp_cadastro;
	}
	?>

	<!-- combobox unido com autocomplete -->
	  <link rel="stylesheet" href="css/1.11.4jquery-ui.css">
	  <script src="js/1.11.4jquery-ui.js"></script>
	  <style>
	  
	  .custom-combobox {
	    position: relative;
	    display: inline-block;
	  }
	  .custom-combobox-toggle {
	    position: absolute;
	    top: 0;
	    bottom: 0;
	    margin-left: -1px;
	    padding: 0;
	  }
	  .custom-combobox-input {
	    margin: 0;
	    padding: 5px 10px;
	  }
	  </style>
	  <script>

	  (function( $ ) {

	    $.widget( "custom.combobox", {
	      _create: function() {
	        this.wrapper = $( "<span>" )
	          .addClass( "custom-combobox" )
	          .insertAfter( this.element );
	 
	        this.element.hide();
	        this._createAutocomplete();
	        this._createShowAllButton();
	      },
	 
	      _createAutocomplete: function() {
	        var selected = this.element.children( ":selected" ),
	          value = selected.val() ? selected.text() : "";
	 
	        this.input = $( "<input>" )
	          .appendTo( this.wrapper )
	          .val( value )
	          .attr( "title", "" )
	          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
	          .autocomplete({
	            delay: 0,
	            minLength: 0,
	            source: $.proxy( this, "_source" )
	          })
	          .tooltip({
	            tooltipClass: "ui-state-highlight"
	          });
	 
	        this._on( this.input, {
	          autocompleteselect: function( event, ui ) {
	            ui.item.option.selected = true;
	            this._trigger( "select", event, {
	              item: ui.item.option
	            });
	          },
	 
	          autocompletechange: "_removeIfInvalid"
	        });
	      },
	 
	      _createShowAllButton: function() {
	        var input = this.input,
	          wasOpen = false;
	 
	        $( "<a>" )
	          .attr( "tabIndex", -1 )
	          .attr( "title", "Exibir itens" )
	          .tooltip()
	          .appendTo( this.wrapper )
	          .button({
	            icons: {
	              primary: "ui-icon-triangle-1-s"
	            },
	            text: false
	          })
	          .removeClass( "ui-corner-all" )
	          .addClass( "custom-combobox-toggle ui-corner-right" )
	          .mousedown(function() {
	            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
	          })
	          .click(function() {
	            input.focus();
	 
	            // Close if already visible
	            if ( wasOpen ) {
	              return;
	            }
	 
	            // Pass empty string as value to search for, displaying all results
	            input.autocomplete( "search", "" );
	          });
	      },
	 
	      _source: function( request, response ) {
	        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
	        response( this.element.children( "option" ).map(function() {
	          var text = $( this ).text();
	          if ( this.value && ( !request.term || matcher.test(text) ) )
	            return {
	              label: text,
	              value: text,
	              option: this
	            };
	        }) );
	      },
	 
	      _removeIfInvalid: function( event, ui ) {
	 
	        // Selected an item, nothing to do
	        if ( ui.item ) {
	          return;
	        }
	 
	        // Search for a match (case-insensitive)
	        var value = this.input.val(),
	          valueLowerCase = value.toLowerCase(),
	          valid = false;
	        this.element.children( "option" ).each(function() {
	          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
	            this.selected = valid = true;
	            return false;
	          }
	        });
	 
	        // Found a match, nothing to do
	        if ( valid ) {
	          return;
	        }
	 
	        // Remove invalid value
	        this.input
	          .val( "" )
	          .attr( "title", value + " invalido! Escolha um item da lista" )
	          .tooltip( "open" );
	        this.element.val( "" );
	        this._delay(function() {
	          this.input.tooltip( "close" ).attr( "title", "" );
	        }, 2500 );
	        this.input.autocomplete( "instance" ).term = "";
	      },
	 
	      _destroy: function() {
	        this.wrapper.remove();
	        this.element.show();
	      }
	    });
	  })( jQuery );

	  function localArmazenamento(local, numero_id){
		$.ajax({
		  type: "POST",
		  url: 'os_controle_edicao.php',
		  data: {acao: 'get_campos_local', local_id: local, numero_id: numero_id},
		  async: false,
		  success: function(resposta) {
			$(".numero_local_os").html(resposta);
		  }
		});
	}

	  </script>

	<!-- validator -->
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="js/funcoes_jquery.js"></script>
	<script type="text/javascript" src="js/modern/dialog.js"></script>	
	<!-- fim validator -->
	
	<script type="text/javascript">
		$(document).ready(function() {
			$.ajax({
			  type: "POST",
			  data: {acao:'get_subStatus_config', os_id: $("#os_id").val()},
			  async: false,
			  url: 'os_controle_acao.php',
			  success: function(data) {
				if(data != ''){
					if(data == 'hide'){
						$(".label-os_sub_status").hide();
						$(".os_sub_status").hide();
						$(".os_sub_status").val('');
					}else{
						$(".os_sub_status").html(data);
					}
				}
			  }
			});			
			var os_prod_emprestimo = $(".os_prod_emprestimo").val();
			if (os_prod_emprestimo == "S"){
     			$("#select_produto").show();
				getProdutoEmprestimo();
			}else{
     			$("#select_produto").hide();     				
     		}

			$("#agenda_tecnico").hide();

			if(orc = $(".os_sub_status").val() == 'Aguarda Retirada'){
				$("#agenda_tecnico").show();
			}

			$(".os_sub_status").click(function(){

				var orc = $(".os_sub_status").val();
				orc = orc.trim();

				if(orc == 'Aguarda Retirada'){
					$("#agenda_tecnico").show();
				}
			});


			var tipo_servico = $("#os_tipo_servico").val();

			if(tipo_servico == 18){
				//getStatusNovo();
			}

			$(".os_solicitante_servico").change(function(){

				$.ajax({
				  type: "POST",
				  url: 'os_controle_aguarda_analise_edicao.php',
				  success: function() {
				  var verificacliente="<?=$clienteconfig_id?>";	
				  if (verificacliente==131){
					  if ($(".os_solicitante_servico").val()=="X09-Recusa no reparo"){	
					    $("#status_id_novo").append('<option title="Finalizado" alt="Finalizado" value="26">Finalizado</option>');
					    $("#status_id_novo option[value='13']").hide();
					  	$("#status_id_novo option[value='14']").hide();
					  	$("#status_id_novo option[value='16']").hide();

					  }
					  else{
					  	$("#status_id_novo option[value='26']").remove();
					  	$("#status_id_novo option[value='13']").show();
					  	$("#status_id_novo option[value='14']").show();
					  	$("#status_id_novo option[value='16']").show();

					  }
				   }  
				}

				});	



			});


			$("#os_tipo_servico").change(function(){
				
				getStatusNovo();
				
			});
			if($("#clienteconfig_id").val()=='67' && $(".os_sub_status").val() != 'Sem Reparo'){
	 			$(".label-os_tipo_avaria").hide();
	 			$(".os_tipo_avaria").hide();			
	 		}
	 		//na fixcomm (67) sub-status sem reparo tem um campo especifico para marcar o sem reparo, campo tipo_avaria
			$(".os_sub_status").change(function(){	
	 			$(".label-os_tipo_avaria").hide();
	 			$(".os_tipo_avaria").hide();			
				if($("#clienteconfig_id").val()=='67' && $(".os_sub_status").val()=='Sem Reparo'){
		 			$(".label-os_tipo_avaria").show();
		 			$(".os_tipo_avaria").show();						
				}
				getStatusNovo();				
			});			


			getPecaLista();
			getServicoLista();
			
			// setar status de acordo com checklist
			getStatusNovo();
			//pushSubStatus();
			
			// validator: adicionando campos obrigatóros para validação pela classe
			jQuery.validator.addClassRules({
			  requerido: {
				required: true
			  }
			});

			// validator: alterando mensagem padrão da validação
			jQuery.extend(jQuery.validator.messages, {
				required: "<?=fct_get_var('global.php', 'var_campo_obrigatorio', $_SESSION["care-br"]["idioma_id"])?>"
			});


			
			// validar dados
			$('#frm_cadastro').validate({
				rules:{
					status_id_novo: "required",
					produto_id_emprestimo:"required"			
				},
				messages:{
					status_id_novo: "<?=fct_get_var('global.php', 'var_campo_obrigatorio', $_SESSION["care-br"]["idioma_id"])?>",
					produto_id_emprestimo: "<?=fct_get_var('global.php', 'var_campo_obrigatorio', $_SESSION["care-br"]["idioma_id"])?>"
				},
				submitHandler: function(form) {
					// verificar ação: somente submeter formulário caso seja ação de submit
					var acao_form = $("#acao_form").val();
					if (acao_form == "submit"){
						// seta tipo como valor informado, para o caso de motivos com preenchimento obrigatório
						var tipo_requerido = "";
						$(".tipo_requerido").each(function(){
							if (this.value == ""){
								campo_alvo = this.id;
								tipo_requerido += $("#" + campo_alvo).attr("tipo_descricao").trim() + ".\n";
							}
						});
						if (tipo_requerido != ""){
							alert("<?=fct_get_var('global.php', 'var_campos_obrigatorios', $_SESSION["care-br"]["idioma_id"])?>:\n\n" + tipo_requerido);
							return false;
						}
			
						// caso OS precise de peça para reparo, verificar se peça foi informada
						var os_peca_precisa = $(".os_peca_precisa").val();
						if (os_peca_precisa == "S"){
							// verificar se foi adicionada peça de reparo ao produto corrente
							var peca_add = $("#peca_add").val();
							if (peca_add == ""){
								alert("<?=fct_get_var('global.php', 'var_msg_os_peca_nao_adicionada', $_SESSION["care-br"]["idioma_id"])?>.");
								return false;
							}
						}
				
						$("#numero_id").val($(".numero_local_os").val());
				
						// confirmar operação e submeter formulário via ajax
						if (confirm("<?=fct_get_var('global.php', 'var_msg_os_analise_tecnica_confirma', $_SESSION["care-br"]["idioma_id"])?>")){												
							var cliente_id = $("#cliente_id").val();
							var clienteconfig_id = $("#clienteconfig_id").val();
							var status_id = $("#status_id").val();						
							var produto_id_emprestimo = $("#produto_id_emprestimo").val();
							var os_prod_emprestimo = $(".os_prod_emprestimo").val();
							var os_cobertura = $("#os_cobertura").val();
							if($("#clienteconfig_id").val()=='11' || $("#clienteconfig_id").val()=='67')
								os_cobertura = 'ORCAMENTO';
							if(os_prod_emprestimo == 'S'){
								if(addProdutoEmprestimo() == false){
									alert("Produto ja emprestado!");
									return false;
								}
							}	
							else{
								retirarProdutoEmprestimo();
							}	

							//valida estoque
							if(!ValidaEstoque()){
								return false;
							}	

							if(os_cobertura != ''){

								//baixa peças do estoque
								baixaQtdDisponivel();

								if($("#clienteconfig_id").val()=='11' || $("#clienteconfig_id").val()=='67'){
									reservaPecaOS();
								}

							}else{
								reservaQtdDisponivel();//usado para reservar peça e descontar do estoque maxxlog
							}

							$.ajax({
							  type: "POST",
							  data: $("#frm_cadastro").serialize()+"&produto_id_emprestimo="+produto_id_emprestimo,
							  async: false,
							  url: 'os_controle_aguarda_analise_acao.php',
							  success: function(data) {
								alert(data);
								EnviarEmail($("#os_id").val(),clienteconfig_id,$("#status_id_novo").val());
								window.location.href="os_controle.php?cliente_id=" + cliente_id + "&clienteconfig_id=" + clienteconfig_id + "&status_id=" + status_id;
							  }
							});
						}
					}
					return false;
				}
			});

			$("#status_id_novo").change('click',function(){
				if($("#clienteconfig_id").val()==9 || $("#clienteconfig_id").val()==12 || $("#clienteconfig_id").val()==19){
					pushSubStatus();
				}
			});						
		});

		function EnviarEmail(os_id,clienteconfig_id,status_id_email){

        	var os_cobertura = $("#os_cobertura").val();
        	var email_solicitante = $("#os_solicitante_email").val();
        	//alert(email_solicitante);
        	//if(email_solicitante!=''){
	    		$.ajax({
					url: 'os_controle_acao.php',
					type: 'POST',
					data: "acao_id=enviar_email&clienteconfig_id="+clienteconfig_id+"&os_id="+os_id+"&os_cobertura="+os_cobertura+"&os_solicitante_email_para_envio="+email_solicitante+"&status_id_email="+status_id_email, 
					async: false,
					success: function(resposta){
						if(resposta != ''){
							alert(resposta);
						}
					}
				});
	    	//}
        }

		function ValidaEstoque(){

			var linha_id = <?=$dados_cadastro[0]['linha_id']?>;
			var os_cobertura = $("#os_cobertura").val();
			if($("#clienteconfig_id").val()=='11' || $("#clienteconfig_id").val()=='67')
				os_cobertura = 'ORCAMENTO';			
			var msg = '';

			$.ajax({
			  type: "POST",
			  data: {acao:'validar_estoque',os_id:$("#os_id").val(),linha_id:linha_id,os_cobertura:os_cobertura,clienteconfig_id:$("#clienteconfig_id").val()},
			  async: false,
			  url: 'os_controle_aguarda_peca_acao.php',
			  success: function(data) {
			  	//return false;
			  	if(data != '' && $("#status_id_novo").val() >= 16){

				  	// alteração de todas as linhas
                    var osprodutopeca_id;
					$(".osprodutopeca_id").each(function(){
						osprodutopeca_id = $(this).val();
						var pallet_id = '';
			
						var osprodutopeca_qtde = $("#osprodutopeca_qtde_" + osprodutopeca_id).val();
						var produto_id_estoque = $("#produto_id_estoque_" + osprodutopeca_id).val();
						var estoque_qtde       = $("#estoque_qtde_" + produto_id_estoque).val();
						var utilizada_qtde     = $("#estoque_status_62_" + osprodutopeca_id).val();
						var reservada_qtde     = $("#estoque_status_64_" + osprodutopeca_id).val();
						var laboratorio_qtde   = $("#estoque_status_68_" + osprodutopeca_id).val();
						var nao_utilizada_qtde = $("#estoque_status_71_" + osprodutopeca_id).val();

						var compara = parseInt(osprodutopeca_qtde) - (parseInt(utilizada_qtde) + parseInt(laboratorio_qtde));
			
						if(parseInt(estoque_qtde) < parseInt(compara)){
							var qtd_solicitar = parseInt(compara) -  parseInt(estoque_qtde); 
					  		$( "select[name=categoria_id_"+osprodutopeca_id+"]" ).focus();
					  		$( "select[name=categoria_id_"+osprodutopeca_id+"]" ).attr('style','border-color:red');
					  		// validator: adicionando campos obrigatóros para validação pela classe
					  	
					  		msg = msg + osprodutopeca_id + ':' + qtd_solicitar  + ';' ;

						}

                        if ($("#clienteconfig_id").val() == 123 && (produto_id_estoque == 314501 || produto_id_estoque == 314502 || produto_id_estoque == 314503)) {
                            msg = '';
                        }
													
						//$( "select[name=estoque_qtde_"+osprodutopeca_id+"]" ).focus();
								
						var os_id = $("#os_id").val();
						pallet_id = $( "select[name=pallet_id_"+osprodutopeca_id+"]" ).val();
						
					});
			  	}else{
			  		return true;
			  	}

			  }
			});
			
			if(msg != ''){
						$.Dialog({
						'title'      : 'Baixar Estoque!',
						'content'    : baixaEstoque(msg),
						'draggable'  : true,
						'keepOpened' : true,
						'position'   : {
										'offsetY' : 30
						},																	
						'closeButton': true,
						'buttonsAlign': 'center',
						'buttons'    : {
							'<?=fct_get_var('global.php', 'var_botao_cancelar', $_SESSION["care-br"]["idioma_id"])?>'	: {
								'action': function() {}
							}
						}
						});
						return false;
			}else{
				return true;
			}
		}


		function baixaQtdDisponivel(){

			if($("#status_id_novo").val() >= 16){

			  	// alteração de todas as linhas
				var osprodutopeca_id;
				$(".osprodutopeca_id").each(function(){
					osprodutopeca_id = $(this).val();
					var pallet_id = '';
		
					var osprodutopeca_qtde = $("#osprodutopeca_qtde_" + osprodutopeca_id).val();
					var produto_id_estoque = $("#produto_id_estoque_" + osprodutopeca_id).val();
					var estoque_qtde       = $("#estoque_qtde_" + produto_id_estoque).val();
					var utilizada_qtde     = $("#estoque_status_62_" + osprodutopeca_id).val();
					var reservada_qtde     = $("#estoque_status_64_" + osprodutopeca_id).val();
					var laboratorio_qtde   = $("#estoque_status_68_" + osprodutopeca_id).val();
					var nao_utilizada_qtde = $("#estoque_status_71_" + osprodutopeca_id).val();
					var qtde_defeito 	   = $("#estoque_status_70_" + osprodutopeca_id).val();
					var estoque_id 		   = $("#estoque_id_" + osprodutopeca_id).val();
					//var compara = parseInt(osprodutopeca_qtde) - (parseInt(utilizada_qtde) + parseInt(laboratorio_qtde));
					if(estoque_id != ''){

						$.ajax({
						  type: "POST",
						  url: 'os_controle_aguarda_peca_acao.php',
						  data: {acao: 'baixar_qtd_disponivel',qtde_defeito:qtde_defeito,reservada_qtde:reservada_qtde,nao_utilizada_qtde:nao_utilizada_qtde,laboratorio_qtde:laboratorio_qtde,utilizada_qtde:utilizada_qtde,estoque_id:estoque_id,produto_id_estoque: produto_id_estoque, osprodutopeca_id: osprodutopeca_id,os_id:$("#os_id").val(), osprodutopeca_qtde: osprodutopeca_qtde},
						  async: false,
						  success: function(data) {
						 	if(data != ''){
						 		alert(data);
						 	}
						  }
						});
					}
				});
		  	}
		}

		//função para reservar peça e ja descontar do estoque usada para cobertura vazia
		function reservaQtdDisponivel(){

			if($("#status_id_novo").val() == 14){

			  	// alteração de todas as linhas
				var osprodutopeca_id;
				$(".osprodutopeca_id").each(function(){
					osprodutopeca_id = $(this).val();
					var pallet_id = '';
					var status_id_novo 	   = $("#status_id_novo").val();
					var osprodutopeca_qtde = $("#osprodutopeca_qtde_" + osprodutopeca_id).val();
					var produto_id_estoque = $("#produto_id_estoque_" + osprodutopeca_id).val();
					var estoque_qtde       = $("#estoque_qtde_" + produto_id_estoque).val();
					var utilizada_qtde     = $("#estoque_status_62_" + osprodutopeca_id).val();
					var reservada_qtde     = $("#estoque_status_64_" + osprodutopeca_id).val();
					var laboratorio_qtde   = $("#estoque_status_68_" + osprodutopeca_id).val();
					var nao_utilizada_qtde = $("#estoque_status_71_" + osprodutopeca_id).val();
					var qtde_defeito 	   = $("#estoque_status_70_" + osprodutopeca_id).val();
					var estoque_id 		   = $("#estoque_id_" + osprodutopeca_id).val();
					//var compara = parseInt(osprodutopeca_qtde) - (parseInt(utilizada_qtde) + parseInt(laboratorio_qtde));
					if(estoque_id != ''){

						$.ajax({
						  type: "POST",
						  url: 'os_controle_aguarda_peca_acao.php',
						  data: {acao: 'reserva_qtd_disponivel',status_id_novo:status_id_novo,qtde_defeito:qtde_defeito,reservada_qtde:reservada_qtde,nao_utilizada_qtde:nao_utilizada_qtde,laboratorio_qtde:laboratorio_qtde,utilizada_qtde:utilizada_qtde,estoque_id:estoque_id,produto_id_estoque: produto_id_estoque, osprodutopeca_id: osprodutopeca_id,os_id:$("#os_id").val(), osprodutopeca_qtde: osprodutopeca_qtde},
						  async: false,
						  success: function(data) {
						 	if(data != ''){
						 		alert(data);
						 	}
						  }
						});
					}
				});
		  	}
		}

		//função para reservar peça e ja descontar do estoque usada para cobertura vazia
		function reservaPecaOS(){

			if($("#status_id_novo").val() == 14){

				if($("#clienteconfig_id").val()=='11' || $("#clienteconfig_id").val()=='67')
				os_cobertura = 'ORCAMENTO';		

			  	// alteração de todas as linhas
				var osprodutopeca_id;
				$(".osprodutopeca_id").each(function(){
					osprodutopeca_id = $(this).val();
					var pallet_id = '';
					var status_id_novo 	   = $("#status_id_novo").val();
					var osprodutopeca_qtde = $("#osprodutopeca_qtde_" + osprodutopeca_id).val();
					var produto_id_estoque = $("#produto_id_estoque_" + osprodutopeca_id).val();
					var estoque_qtde       = $("#estoque_qtde_" + produto_id_estoque).val();
					var utilizada_qtde     = $("#estoque_status_62_" + osprodutopeca_id).val();
					var reservada_qtde     = $("#estoque_status_64_" + osprodutopeca_id).val();
					var laboratorio_qtde   = $("#estoque_status_68_" + osprodutopeca_id).val();
					var nao_utilizada_qtde = $("#estoque_status_71_" + osprodutopeca_id).val();
					var qtde_defeito 	   = $("#estoque_status_70_" + osprodutopeca_id).val();
					var estoque_id 		   = $("#estoque_id_" + osprodutopeca_id).val();
					var clienteconfig_id   = $("#clienteconfig_id").val();
					//var compara = parseInt(osprodutopeca_qtde) - (parseInt(utilizada_qtde) + parseInt(laboratorio_qtde));
					if(estoque_id != ''){

						$.ajax({
						  type: "POST",
						  url: 'os_controle_aguarda_peca_acao.php',
						  data: {acao: 'reserva_peca',os_cobertura:os_cobertura,clienteconfig_id:clienteconfig_id,status_id_novo:status_id_novo,qtde_defeito:qtde_defeito,reservada_qtde:reservada_qtde,nao_utilizada_qtde:nao_utilizada_qtde,laboratorio_qtde:laboratorio_qtde,utilizada_qtde:utilizada_qtde,estoque_id:estoque_id,produto_id_peca: produto_id_estoque, osprodutopeca_id: osprodutopeca_id,os_id:$("#os_id").val(), osprodutopeca_qtde: osprodutopeca_qtde},
						  async: false,
						  success: function(data) {
						 	if(data != ''){
						 		alert(data);
						 	}
						  }
						});
					}
				});
		  	}
		}

		function baixaEstoque(pecas){
			$.ajax({
			  type: "POST",
			  url: 'os_controle_aguarda_peca_edicao.php',
			  data: {acao: 'baixaEstoque', pecas:pecas},
			  async: false,
			  success: function(data) {
			  	dialog_confim = data;
			  }
			});
			return dialog_confim;
		}


		function AgendaTecnico(os_id){

				$.Dialog({
				'title'      : 'Selecione o Tecnico',
				'content'    : addTecnico(),
				'draggable'  : true,
				'keepOpened' : true,
				'position'   : {
								'offsetY' : 30
				},																	
				'closeButton': true,
				'buttonsAlign': 'right',
				'buttons'    : {
					'<?=fct_get_var('global.php', 'var_botao_ok', $_SESSION["care-br"]["idioma_id"])?>'	: {
						'action': function() {
							var msg = "";
							var tecnico = $('#tecnico').val();
							var bairro = (typeof($('#bairro').val()) == 'undefined' ? "" : $('#bairro').val());							
							var cidade = (typeof($('#cidade').val()) == 'undefined' ? "" : $('#cidade').val());
							var clienteconfig_id = $("#clienteconfig_id").val();
							var roteiro = "";
							
							if (tecnico == "")		msg += 'selecione o tecnico!';
							
							if (msg != ""){
								//alert(msg);
								//return false;
							}
							if( typeof($("#roteiro_id").val()) != 'undefined') roteiro = $("#roteiro_id").val();							
							
							var sub_status = 'Aguarda Retirada';
							
							window.open("planner_controle.php?tecnico=" + tecnico + "&os_id=" + os_id + "&bairro=" + bairro + "&cidade=" + cidade + "&sub_status=" + sub_status + "&clienteconfig_id=" + clienteconfig_id+ "&roteiro=" + roteiro);
							
						}						
					},
					'<?=fct_get_var('global.php', 'var_botao_cancelar', $_SESSION["care-br"]["idioma_id"])?>'	: {
						'action': function() {}
					}
				}
			});

				
		}


		function addTecnico(os_id){
			var cliente_id = $("#cliente_id").val();
			var clienteconfig_id = $("#clienteconfig_id").val();
			//var status_id = $("#status_id").val();
			var tec = "";
			
			$.ajax({
			  type: "POST",
			  data: {os_id:os_id,cliente_id:cliente_id,clienteconfig_id:clienteconfig_id},
			  async: false,
			  url: 'os_controle_edicao.php?acao=busca_tecnico',
			  success: function(data) {
				tec = data;
			  }
			});
			return tec;
		}

		
		function excluiCadastro(osprodutopeca_id, produto_id_peca){
			if (confirm("<?=fct_get_var('global.php', 'var_confirma_exclusao', $_SESSION["care-br"]["idioma_id"])?>")){
				
	            var os_id = $("#os_id").val();
	            var clienteconfig_id = $("#clienteconfig_id").val();

				os_id = $("#os_id").val();
				qtde_utilizada_baixada = $("#estoque_status_62_" + osprodutopeca_id).val();
				qtde_nao_utilizada_baixada = $("#estoque_status_71_" + osprodutopeca_id).val();
				qtde_defeito_baixada = $("#estoque_status_70_" + osprodutopeca_id).val();
				qtde_laboratorio = $("#estoque_status_68_" + osprodutopeca_id).val();
				qtde = $("#osprodutopeca_qtde_" + osprodutopeca_id).val();
				estoque_id = $("#estoque_id_" + osprodutopeca_id).val();

				$.ajax({
				  	type: "POST",
				  	url: 'os_controle_aguarda_analise_acao.php',
				  	data: {acao: 'dlt', estoque_id:estoque_id, qtde_defeito_baixada: qtde_defeito_baixada, qtde_utilizada_baixada:qtde_utilizada_baixada, qtde_laboratorio:qtde_laboratorio,osprodutopeca_id: osprodutopeca_id, os_id: os_id, produto_id_peca: produto_id_peca, clienteconfig_id: clienteconfig_id},
				  	async: false,
				  	success: function(data) {
				  		//alert(data);
						getPecaLista();
						if (clienteconfig_id == 20 || clienteconfig_id == 82 || clienteconfig_id == 136 || clienteconfig_id == 137 || clienteconfig_id == 138 || clienteconfig_id == 139) {
							var os_cobertura = $("#os_cobertura").val();
							if (os_cobertura == 'GARANTEC-ESTENDIDA' || os_cobertura == 'LUIZASEG-ESTENDIDA' || os_cobertura == 'CARDIF-ESTENDIDA' || os_cobertura == 'ZURICH-ESTENDIDA' || os_cobertura == 'ZURICH-FAST-ESTENDIDA' || os_cobertura == 'ASSURANT-ESTENDIDA' || os_cobertura == 'VIRGINIA-ESTENDIDA' || os_cobertura == 'ASSURANT CELULAR-ESTENDIDA' || os_cobertura == 'VIRGINIA CELULAR-ESTENDIDA') {	
								var qtde_item = $('#tabela_peca>tbody>tr').length;
								var valor_total = $("#valor_total").val();
								if (valor_total < 110) {
									if (qtde_item == 0) {
										cont_total = 0;
									}
								}
							}
						}
				  	}
				});
					
				if (qtde_utilizada_baixada > 0 || qtde_laboratorio > 0){

					alert('Pecas utilizada(s) e no laboratorio foram movidas para NAO UTILIZADA(S) para dar entrada no Estoque!');
				}
			}
		}
		
		function getPecaLista(){
			var os_id = $("#os_id").val();
			var produto_id = $("#produto_id").val();
			var os_peca_precisa = $(".os_peca_precisa").val();
			var clienteconfig_id = $("#clienteconfig_id").val();
			var os_cobertura = $("#os_cobertura").val();
			if (os_peca_precisa == "S"){
				$.ajax({
				  type: "POST",
				  url: 'os_controle_aguarda_analise_edicao.php',
				  data: {acao: 'get_peca_lista', os_id: os_id, produto_id: produto_id,clienteconfig_id:clienteconfig_id,os_cobertura:os_cobertura},
				  async: false,
				  success: function(data) {
					$("#peca_lista").html(data);
					
					$( "#produto_id_peca" ).combobox({
						select: function(event, ui) {
				           addPecaLista();
				        }
					});

				  }
				});
			}else{
				$("#peca_lista").html("");
			}
			getStatusNovo();
		}

		var cont_total = 0;
		
		function addPecaLista(){
			var os_id = $("#os_id").val();
			var produto_id = $("#produto_id").val();
			var produto_id_peca = $("#produto_id_peca").val();
			var clienteconfig_id = $("#clienteconfig_id").val();
			var linha_id = $("#linha_id").val();
			var os_cobertura = $("#os_cobertura").val();
			var peca_complementar = '';
			if(clienteconfig_id == '11'){
				os_cobertura = 'ORCAMENTO';
			} else if(clienteconfig_id == '123') {
			    peca_complementar = $("#peca_complementar").val();
			}

			$.ajax({
			  type: "POST",
			  url: 'os_controle_aguarda_analise_acao.php',
			  data: {acao: 'add_peca_lista', linha_id:linha_id,os_cobertura:os_cobertura,os_id: os_id, produto_id: produto_id, produto_id_peca: produto_id_peca, clienteconfig_id: clienteconfig_id, peca_complementar: peca_complementar},
			  async: false,
			  success: function(data) {
			  	//alert(data);
				getPecaLista();
				getServicoLista();
				if (clienteconfig_id == 20 || clienteconfig_id == 82 || clienteconfig_id == 136 || clienteconfig_id == 137 || clienteconfig_id == 138 || clienteconfig_id == 139) {
					var valor_total = $("#valor_total").val();
					if (os_cobertura == 'GARANTEC-ESTENDIDA' || os_cobertura == 'LUIZASEG-ESTENDIDA' || os_cobertura == 'CARDIF-ESTENDIDA' || os_cobertura == 'ZURICH-ESTENDIDA' || os_cobertura == 'ZURICH-FAST-ESTENDIDA' || os_cobertura == 'ASSURANT-ESTENDIDA' || os_cobertura == 'VIRGINIA-ESTENDIDA' || os_cobertura == 'ASSURANT CELULAR-ESTENDIDA' || os_cobertura == 'VIRGINIA CELULAR-ESTENDIDA') {
						if (valor_total > 110 && cont_total == 0) {
							alert("O valor das pecas ultrapassou R$110,00. Sub Status alterado para AGP-FORA DO CUSTO");
							$(".os_sub_status").val("AGP-FORA DO CUSTO");
							cont_total++;
						}
					}
				}
			  }
			});
		}

		function gerarNf(tipo,os_id,empresacliente_id){
        	var clienteconfig_id = $("#clienteconfig_id").val();
        	var cliente_id = $("#cliente_id").val();

        	// Informar que o valor total é diferente do valor recebido
			var valor_total = parseFloat($("#os_valor_liquido").val());
			var valor_recebido = parseFloat($("#valor_recebido").val());
			//grupo multifix
            var clientes="<?=MULTIFIX?>";
			if ( (valor_recebido < valor_total) && (clientes.indexOf("|"+$("#clienteconfig_id").val()+"|")>0)	&& tipo!='gerar_nfs' ) {
				alert ("O valor recebido é menor do que o valor total do orçamento.");
				return false;
				
			}
			//cancelar NFS
			if(tipo == 'dialogCancelaNFSe'){
				setTimeout(function() { dialogCancelaNFSe(os_id,empresacliente_id); }, 50);
				return;
			}
			
        	if('<?=strtoupper(trim(retira_acentuacao($dados_cadastro[0]['os_sub_status'])))?>' == 'SEM DEFEITO'
        		|| '<?=strtoupper(trim(retira_acentuacao($dados_cadastro[0]['os_sub_status'])))?>' == 'REPROVADO' 
        		|| '<?=strtoupper(trim(retira_acentuacao($dados_cadastro[0]['os_sub_status'])))?>' == 'ORCAMENTO REPROVADO'
        		){
        		if(tipo == 'dialogNFe'){
        			alert("Sub-status '"+ '<?=strtoupper(trim(retira_acentuacao($dados_cadastro[0]['os_sub_status'])))?>' + "', permitido gerar apenas NFS!");
        			return false;
        		}

        		var retorno = '';
	            $.Dialog({
	                'title'      : '<?=utf8_decode('Atenção')?>',
	                'content'    : formSemDefeito(),
	                'draggable'  : true,
	                'keepOpened' : true,
	                'position'   : {
	                    'offsetY' : 30
	                },
	                'closeButton': true,
	                'buttonsAlign': 'right',
	                'buttons'    : {
	                    'OK e confirmar gerar NFS'	: {
	                        'action': function() {
	                        	if(confirm("Salvar o valor e Gerar a NFS?")){
		                            retorno = 'ok';
		                            $("#taxa_deslocamento").val($("#valor_taxa").val());
		                            $("#os_valor_total").val($("#valor_taxa").val().replace(".",","));
		                            $("#os_valor_liquido").val($("#valor_taxa").val().replace(".",","));

		                            //salva o valor de taxa
						            $.ajax({
						                type: "POST",
						                url: 'os_controle_faturamento_acao.php',
						                data: {acao:"set_valor_sem_defeito", os_id: $("#os_id").val(),clienteconfig_id:clienteconfig_id,taxa_deslocamento:$("#valor_taxa").val()},
						                async: false,
						                success: function(data) {
						                }
						            }); 

						       		if(tipo == 'gerar_nfs'){
						                if (clienteconfig_id == '51'){
						                	setTimeout(function() { dialogNFeServ(os_id); }, 1200);
						                }else{
						                	window.open("os_controle_gerar_nfse_edicao.php?acao=add&cliente_id="+cliente_id+"&empresacliente_id="+empresacliente_id+"&os_id="+os_id);
						                }
						            }
						            //cancelar NFS
						            if(tipo == 'dialogCancelaNFSe'){
										setTimeout(function() { dialogCancelaNFSe(os_id,empresacliente_id); }, 1200);
						            	
						            }
						            //gerar NF de pecas
						            if(tipo == 'dialogNFe'){
						            	alert("Sub-status '"+ tipo + "', permitido gerar apenas NFS!");
						            }		
	                        	}else{
	                        		retorno = 'nok';
	                        	}                      
	                        }
	                    },
	                    '<?=fct_get_var('global.php', 'var_botao_cancelar', $_SESSION["care-br"]["idioma_id"])?>'	: {
	                        'action': function() {
	                        	retorno = 'nok';
	                        }
	                    }
	                }
	            });
	       	}else{
	       		//NF de servico
	       		if(tipo == 'gerar_nfs'){
	                if (clienteconfig_id == '51'){
	                	dialogNFeServ(os_id);
	                }else{
	                	window.open("os_controle_gerar_nfse_edicao.php?acao=add&cliente_id="+cliente_id+"&empresacliente_id="+empresacliente_id+"&os_id="+os_id);
	                }
	            }
	            //cancelar NFS
	            if(tipo == 'dialogCancelaNFSe'){
	            	dialogCancelaNFSe(os_id,empresacliente_id);
	            }
	            //gerar NF de pecas
				if (clienteconfig_id == 90) {
					gerarNfLasan(os_id);
				} else {
					if(tipo == 'dialogNFe'){
						dialogNFe(os_id);	
					}
				}
	        }
        }

        function formSemDefeito(){   
        	var form = '';
            $.ajax({
                type: "POST",
                url: 'os_controle_faturamento_edicao.php',
                data: {acao:"form_sem_defeito", os_id: $("#os_id").val()},
                async: false,
                success: function(data) {
                    form = data;
                }
            }); 
            return form;	        
        }

        function dialogNFeServ(os_id){

            $.Dialog({
                'title'      : 'NFS-e OS',
                'content'    : "<?=utf8_decode('Gerar Nota de Serviço?')?>",
                'draggable'  : true,
                'keepOpened' : true,
                'position'   : {
                    'offsetY' : 30
                },
                'closeButton': true,
                'buttonsAlign': 'right',
                'buttons'    : {
                    '<?=fct_get_var('global.php', 'var_botao_ok', $_SESSION["care-br"]["idioma_id"])?>' : {
                        'action': function() {
                            //gera xml de servico
                            $.ajax({
                                type: "POST",
                                url: 'lib/nfe/acoes/NFe/MakeNFe.php',
                                data: {os_id: os_id, itens: 'servico'},
                                async: false,
                                success: function(data) {
                                    alert(data);
                                }
                            });
                        }
                    },
                    '<?=fct_get_var('global.php', 'var_botao_cancelar', $_SESSION["care-br"]["idioma_id"])?>'   : {
                        'action': function() {}
                    }
                }
            });
        }

         function dialogCancelaNFSe(os_id,empresacliente_id){

            //var empresacliente_id = $("#empresacliente_id").val();

            $.Dialog({
                    'title'      : 'Cancelar NFS-e',
                    'content'    : getCert(empresacliente_id, true),
                    'draggable'  : true,
                    'keepOpened' : true,
                    'position'   : {
                                    'offsetY' : 30
                    },                                                                  
                    'closeButton': true,
                    'buttonsAlign': 'right',
                    'buttons'    : {
                        '<?=fct_get_var('global.php', 'var_botao_ok', $_SESSION["care-br"]["idioma_id"])?>' : {
                            'action': function() {                      
                                
                                var pass = $("#senha_nfe").val();
                                var certs = $("#certificado").val();
                                var xJust = $("#xjust").val();
                                if(xJust  == ''){
                                    alert('preencha a justificativa!');
                                    return false;
                                }
                                 $.ajax({
                                  type: "POST",
                                  data: {acao:'cancelar_nfse',empresacliente_id:empresacliente_id,os_id:os_id,senha:pass,cert:certs,xJust:xJust},
                                  async: false,
                                  url: 'os_controle_gerar_nfse_acao.php',
                                  success: function(data) {
                                     //
                                    //alert(data);
                                    //loadGrid();
                                    
                                    if(data == '100 - Autorizado o uso da NF-e'){
                                        css = 'color:green';
                                    }else{
                                        css = 'color:red';
                                    }
                                
                                    //alert(resposta);      
                                    //resposta = data;
                                    $.Dialog.content('<div  class="row"><span style="' +css+ '" ><b>' + data + '</b></span></div> ');
                                    $('#dialogButtons').html('');
                                    //loadGrid();
                                  }
                                });

                                return false;
                                
                            }
                        },
                        '<?=fct_get_var('global.php', 'var_botao_cancelar', $_SESSION["care-br"]["idioma_id"])?>'   : {
                            'action': function() {}
                        }
                    }
                });

        }

        function getCert(empresacliente_id, justificativa){
            
            $.ajax({
              type: "POST",
              data: {acao: 'get_nfe',empresacliente_id:empresacliente_id, justificativa: justificativa},
              async: false,
              url: 'nf_xml_controle_edicao.php',
              success: function(data) {
                campos = data;
              }
            });
			//alert(campos);
            return campos;
			
        }

        function dialogNFe(os_id){
			var str="|"+getNFe(os_id);
			if (str.indexOf("Essa NF possui peças sem valores definidos.")>0 ){
				 $.Dialog({
	                'title'      : 'Itens OS',
	                'content'    : getNFe(os_id),
	                'draggable'  : true,
	                'keepOpened' : true,
	                'position'   : {
	                    'offsetY' : 30
	                },
	                'closeButton': true,
	                'buttonsAlign': 'right',
	                'buttons'    : {
	                    
	                    '<?=fct_get_var('global.php', 'var_botao_cancelar', $_SESSION["care-br"]["idioma_id"])?>'	: {
	                        'action': function() {}
	                    }
	                }
            	});
			}
			else{
            $.Dialog({
                'title'      : 'Itens OS',
                'content'    : getNFe(os_id),
                'draggable'  : true,
                'keepOpened' : true,
                'position'   : {
                    'offsetY' : 30
                },
                'closeButton': true,
                'buttonsAlign': 'right',
                'buttons'    : {
                    '<?=fct_get_var('global.php', 'var_botao_ok', $_SESSION["care-br"]["idioma_id"])?>'	: {
                        'action': function() {
                            var lista_itens = "";
                            var contem_item = 0;
							
                            $('.ckbItem').each(function(){
                                contem_item = contem_item + 1;
                                if($(this).is(':checked')){
                                    if (lista_itens != '')	lista_itens += ' , ';
                                    lista_itens += $(this).val();
                                }
                            });

                            if (lista_itens == '' && contem_item > 0){
                                alert('<?=fct_get_var('global.php', 'var_selecione_1_item', $_SESSION["care-br"]["idioma_id"])?>!');
                                return false;
                            }else if(contem_item == 0){
                                return;
                            }
							
							//mostra aviso nota
                            var res = gerarXML(os_id, lista_itens);
								$("#aviso").show();
							
								if (res.match(/Nota Fiscal de Num.: (.*?) gerada com sucesso!/))
								{
									$("#autoriza_nfe").show();
									$("#danfe_view").show();
								}
								$("#aviso_nfe").html(res);
								window.location = "#aviso";
                        }
                    },
                    '<?=fct_get_var('global.php', 'var_botao_cancelar', $_SESSION["care-br"]["idioma_id"])?>'	: {
                        'action': function() {}
                    }
                }
            });
            }
		  // alert(dados);
		
		//return 'ok';
        }

        function getNFe(os_id){
            $.ajax({
                type: "POST",
                url: 'os_controle_gerar_nfse_acao.php',
                data: {acao: 'dialogNFe', os_id: os_id},
                async: false,
                success: function(data) {
                    dialog_NFe = data;
                }
            });
            return dialog_NFe;
        }

        function gerarXML(os_id, itens){
			var retorno = "";
			
            $.ajax({
                type: "POST",
                url: 'os_controle_gerar_nfse_acao.php',
                data: {acao: 'valida_xml', os_id: os_id, itens: itens},
                async: false,
                success: function(data) {
                    valida = data;		
					
                }
            });

            if(valida != 'NV'){ // NV = Nao Valida, para clientes que nao emitem NFE
                if(valida != 'OK'){
					alert(valida);
                    return retorno;
                }
				
            } else {
                alert('Cliente sem permissão para gerar NF-e!');
                return retorno;
            }

            $.ajax({
                type: "POST",
                url: 'lib/nfe/acoes/NFe/MakeNFe.php',
                data: {os_id: os_id, itens: itens},
                async: false,
                success: function(data) {
					retorno = data;										
                }
            });
            
			return retorno;
        }
		
		function updtPecaLista(osprodutopeca_id,produto_id_peca){
			var osprodutopeca_qtde = $("#osprodutopeca_qtde_" + osprodutopeca_id).val();
            var clienteconfig_id = $("#clienteconfig_id").val();
            var os_id = $("#os_id").val();
            var os_cobertura = $("#os_cobertura").val();
            if(clienteconfig_id == '11'){
				os_cobertura = 'ORCAMENTO';
			}
            var osprodutopeca_cobrar = $("#osprodutopeca_cobrar_" + osprodutopeca_id).val();
			
			qtde_reservada = $("#estoque_status_64_" + osprodutopeca_id).val();
			qtde_laboratorio = $("#estoque_status_68_" + osprodutopeca_id).val();
			qtde_utilizada_baixada = $("#estoque_status_62_" + osprodutopeca_id).val();
			qtde_nao_utilizada_baixada = $("#estoque_status_71_" + osprodutopeca_id).val();
			qtde_defeito_baixada = $("#estoque_status_70_" + osprodutopeca_id).val();
			estoque_id = $("#estoque_id_" + osprodutopeca_id).val();
			var produto_id_estoque = $("#produto_id_estoque_" + osprodutopeca_id).val();
			var estoque_qtde       = $("#estoque_qtde_" + produto_id_estoque).val();

            // B2X Moema grava os valores da peça no Análise
            var osprodutopeca_valor_venda = '';
            var osprodutopeca_desconto = '';
            var osprodutopeca_valor_mao_obra = '';
            if (clienteconfig_id == 123) {
                osprodutopeca_valor_venda = $("#osprodutopeca_valor_venda_" + osprodutopeca_id).val();
                osprodutopeca_desconto = $("#osprodutopeca_desconto_" + osprodutopeca_id).val();
                osprodutopeca_valor_mao_obra = $("#osprodutopeca_valor_mao_obra_" + osprodutopeca_id).val();

                if (osprodutopeca_valor_venda == '') {
                    osprodutopeca_valor_venda = '0,00';
                } 
                if(osprodutopeca_desconto == '') {
                    osprodutopeca_desconto = '0,00';
                }
                if(osprodutopeca_valor_mao_obra == '') {
                    osprodutopeca_valor_mao_obra = '0,00';
                }
            }

			//total = parseInt(qtde_reservada) + parseInt(qtde_utilizada_baixada) + parseInt(qtde_laboratorio) + parseInt(qtde_nao_utilizada_baixada) + parseInt(qtde_defeito_baixada);
			ver = parseInt(osprodutopeca_qtde) - (parseInt(qtde_utilizada_baixada) + parseInt(qtde_laboratorio));
			
			/*if(ver < 0){

				alert('Peça já utilizada!');
				return;

			}*/

			$.ajax({
			  type: "POST",
			  url: 'os_controle_aguarda_analise_acao.php',
			  data: {acao: 'updt_peca_lista',estoque_id:estoque_id, qtde_nao_utilizada_baixada:qtde_nao_utilizada_baixada ,qtde_reservada:qtde_reservada,qtde_defeito_baixada: qtde_defeito_baixada, qtde_utilizada_baixada:qtde_utilizada_baixada, qtde_laboratorio:qtde_laboratorio,os_cobertura:os_cobertura,produto_id_peca:produto_id_peca, clienteconfig_id:clienteconfig_id, osprodutopeca_id: osprodutopeca_id, osprodutopeca_cobrar:osprodutopeca_cobrar , osprodutopeca_qtde: osprodutopeca_qtde, estoque_id: estoque_id, os_id: os_id, osprodutopeca_valor_venda: osprodutopeca_valor_venda, osprodutopeca_desconto: osprodutopeca_desconto, osprodutopeca_valor_mao_obra: osprodutopeca_valor_mao_obra},
			  async: false,
			  success: function(data) {
			  	//alert(data);
				getPecaLista();
			  }
			});
		}

		function getProdutoEmprestimo(){
			var os_id = $("#os_id").val();
			var produto_id = $("#produto_id").val();
			var os_prod_emprestimo = $(".os_prod_emprestimo").val();
			var clienteconfig_id = $("#clienteconfig_id").val();			
			if (os_prod_emprestimo == "S"){
     			$("#select_produto").show();				
				$.ajax({
				  type: "POST",
				  url: 'os_controle_aguarda_analise_edicao.php',
				  data: {acao: 'get_produto_emprestimo', os_id: os_id, produto_id: produto_id, clienteconfig_id:clienteconfig_id},
				  async: false,
				  success: function(data) {
					$("#produto_id_emprestimo").html(data);										        
				  }
				});
			}else{
     			$("#select_produto").hide();	
     			$("#produto_id_emprestimo").val();   
			}
		}
		
		function addProdutoEmprestimo(){
			var os_id = $("#os_id").val();
			var produto_id_emprestimo = $("#produto_id_emprestimo").val();
			var clienteconfig_id = $("#clienteconfig_id").val();
			var retorno = false;
			$.ajax({
			  type: "POST",
			  url: 'os_controle_aguarda_analise_acao.php',
			  data: {acao: 'add_produto_emprestimo', os_id: os_id, produto_id_emprestimo: produto_id_emprestimo, clienteconfig_id: clienteconfig_id},
			  async: false,
			  success: function(data) {
			  	if(data != "false"){
			  		retorno = true;
			  	}
			  }
			});
			return retorno;
		}

		function retirarProdutoEmprestimo(){			
			var os_id = $("#os_id").val();
			$.ajax({
			  type: "POST",
			  url: 'os_controle_aguarda_analise_acao.php',
			  data: {acao: 'retirar_produto_emprestimo', os_id: os_id},
			  async: false,
			  success: function(data) {			
			  }
			});
		}		
		
		function imprimitEtiqueta() { 
			var os_id = $("#os_id").val();
			window.open("etiqueta_service_gspn.php?os_id="+os_id);
		}

		function geraLaudo(){
			var cliente_id = $("#cliente_id").val();
			var clienteconfig_id = $("#clienteconfig_id").val();
			var os_id = $("#os_id").val();
			var laudo_tipo = '1';
			
			$.ajax({
			  type: "POST",
			  url: 'os_controle_laudo_acao.php',
			  data: {acao: 'seta_laudo', cliente_id: cliente_id, clienteconfig_id: clienteconfig_id, laudo_tipo: laudo_tipo},
			  async: false,
			  success: function(data) {
			  	if(data !="")
				laudo_tipo = data;
			  }
			});		

			if(laudo_tipo != ""){				
				window.open(laudo_tipo+"?cliente_id=" + cliente_id + "&clienteconfig_id=" + clienteconfig_id + "&os_id=" + os_id);				
				return;
			}	
		}
		
		
		function getServicoLista(){
			var cliente_id = $("#cliente_id").val();
			var clienteconfig_id = $("#clienteconfig_id").val();
			var os_id = $("#os_id").val();
			$.ajax({
			  type: "POST",
			  url: 'os_controle_reparo_edicao.php',
			  data: {acao: 'get_servico_lista', cliente_id: cliente_id, clienteconfig_id: clienteconfig_id, os_id: os_id},
			  async: false,
			  success: function(data) {
				$("#servico_lista").html(data);
				if ($("#servico_add").val()=='S'){
					$("#status_id_novo option[value='12']").remove();
					$("#status_id_novo").append('<option title="Or&ccedil;amento Complementar" alt="Or&ccedil;amento Complementar" value="12">Or&ccedil;amento Complementar</option>');

				}
				else{
                   $("#status_id_novo option[value='12']").remove();
				}
			  }
			});
		}
		
		function addServicoLista(){
			var cliente_id = $("#cliente_id").val();
			var clienteconfig_id = $("#clienteconfig_id").val();
			var os_id = $("#os_id").val();
			var servico_id = $("#servico_id").val();
			$.ajax({
			  type: "POST",
			  url: 'os_controle_aguarda_analise_acao.php',
			  data: {acao: 'add_servico_lista', cliente_id: cliente_id, clienteconfig_id: clienteconfig_id, os_id: os_id, servico_id: servico_id},
			  async: false,
			  success: function(data) {
				getServicoLista();
			  }
			});
		}
		
		function updtServicoLista(osservico_id){
			var osservico_valor = $("#osservico_valor_" + osservico_id).val();
			var osservico_observacao = $("#osservico_observacao_" + osservico_id).val();
			$.ajax({
			  type: "POST",
			  url: 'os_controle_aguarda_analise_acao.php',
			  data: {acao: 'updt_servico_lista', osservico_id: osservico_id, osservico_valor: osservico_valor, osservico_observacao: osservico_observacao},
			  async: false,
			  success: function(data) {
				getServicoLista();
			  }
			});
		}
		
		function excluiServicoLista(osservico_id){
			//if (confirm("<?=fct_get_var('global.php', 'var_confirma_exclusao', $_SESSION["care-br"]["idioma_id"])?>")){
				$.ajax({
				  type: "POST",
				  url: 'os_controle_aguarda_analise_acao.php',
				  data: {acao: 'dlt_servico_lista', osservico_id: osservico_id},
				  async: false,
				  success: function(data) {
					getServicoLista();
				  }
				});
			//}
		}
		
		
		function setaMotivoExclusivo(tipo_id, motivo_exclusivo){
			var motivo_exclusivo_contrario;
			if (motivo_exclusivo == "S")
				motivo_exclusivo_contrario = "N";
			else
				motivo_exclusivo_contrario = "S";
			var classe_motivo = "tipo_" + tipo_id + "_motivo_exclusivo_" + motivo_exclusivo;
			var classe_motivo_contrario = "tipo_" + tipo_id + "_motivo_exclusivo_" + motivo_exclusivo_contrario;
			$("." + classe_motivo_contrario).attr("checked", false);
			
			// seta tipo como valor informado, para o caso de motivos com preenchimento obrigatório
			var tipo_id_selecionado = "";
			$(".tipo_" + tipo_id).each(function(){
				if($(this).is(':checked')){
					tipo_id_selecionado = "S";
				}
			});
			$("#tipo_id_" + tipo_id).val(tipo_id_selecionado);
			
			// setar status de acordo com checklist
			getStatusNovo();
		}
		
		function getStatusNovo(){
			var cliente_id = $("#cliente_id").val();
			var clienteconfig_id = $("#clienteconfig_id").val();
			var status_id = $("#status_id").val();
			var os_tipo = $("#os_tipo").val();
			var os_cobertura = $("#os_cobertura").val();
			var os_tipo_servico =$("#os_tipo_servico").val();
			var os_peca_precisa =$(".os_peca_precisa").val();
			var os_sub_status =$(".os_sub_status").val();
            var os_id = $("#os_id").val();


			// aplicar regras para aceitar ou rejeitar produto
			var motivo_aceita = "S";
			$(".motivo_aceita_N").each(function(){
				if($(this).is(':checked')){
					motivo_aceita = "N";
				}
			});
			
			$.ajax({
			  type: "POST",
			  data: {acao: 'get_status_novo', cliente_id: cliente_id, clienteconfig_id: clienteconfig_id, status_id: status_id, motivo_aceita: motivo_aceita,os_tipo:os_tipo,os_cobertura:os_cobertura,os_tipo_servico:os_tipo_servico,os_peca_precisa:os_peca_precisa, os_sub_status:os_sub_status, os_id: os_id},
			  async: false,
			  url: 'os_controle_aguarda_analise_edicao.php',
			  success: function(data) {
				$("#status_id_novo").html(data);
				getServicoLista();
			  }
			});
		}

		function dialogComentariogeral(){
			
			$.Dialog({
				'title'      : '<?=fct_get_var('global.php', 'var_dialog_comentario', $_SESSION["care-br"]["idioma_id"])?>',
				'content'    : getComentario(),
				'draggable'  : true,
				'keepOpened' : true,
				'position'   : {
								'offsetY' : 30
				},																	
				'closeButton': true,
				'buttonsAlign': 'right',
				'buttons'    : {
					'<?=fct_get_var('global.php', 'var_botao_cancelar', $_SESSION["care-br"]["idioma_id"])?>'	: {
						'action': function() {}
					}
				}
			});
		}
		
		function getComentario(){
			var cliente_id = $("#cliente_id").val();
			var clienteconfig_id = $("#clienteconfig_id").val();
			var status_id = $("#status_id").val();
			var os_id = $("#os_id").val();
			var comentario = "";
			
			$.ajax({
			  type: "POST",
			  data: {os_id:os_id,status_id:status_id},
			  async: false,
			  url: 'os_controle_edicao.php?acao=comentario_geral',
			  success: function(data) {
				comentario = data;
			  }
			});
			return comentario;
		}	

		function dialogComentario(){
			// obrigat�rio selecionar ao menos 1 item da lista
			
			//$.Dialog().close();
			//$.Dialog.close();

			$.Dialog.content(addComentario() + '<div class="row"><div class="span6 campos-form"><div class="input-control text"><button class="button" type="button" onclick="salvarcomentario()">Salvar</button></div></div></div> ');
			
		}

		function salvarcomentario() {
			var msg = "";
			var os_id = $("#lista_os_selecionada").val();
			var status_id_atual = $("#status_id_atual").val();
			var status_observacao = $("#status_observacao").val();
			if (status_observacao == "")		msg += '<?=fct_get_var('global.php', 'var_os_comentario_help', $_SESSION["care-br"]["idioma_id"])?>.\n';

			if (msg != ""){
				alert(msg);
				return false;
			}
			
			$.ajax({
			  type: "POST",
			  data: $("#frm_grid").serialize(),
			  async: false,
                  url: 'os_controle_comentario.php?acao_id=comentario_gravar&status_id_atual=' + status_id_atual + '&status_observacao=' + status_observacao + '&lista_os=' + os_id,
			  success: function(data) {
				alert(data);
				$.Dialog.close();
			  }
			});
							//return false;
		}
		
		function addComentario(){
			var cliente_id = $("#cliente_id").val();
			var clienteconfig_id = $("#clienteconfig_id").val();
			var status_id = $("#status_id").val();
			var lista_os = $("#os_id").val();
			var comentario = "";
			
			$.ajax({
			  type: "POST",
			  data: {lista_os:lista_os,status_id:status_id,clienteconfig_id:clienteconfig_id,cliente_id:cliente_id},
			  async: false,
			  url: 'os_controle_comentario.php?acao_id=comentario',
			  success: function(data) {
				comentario = data;
			  }
			});
			return comentario;
		}	

		function pushSubStatus(){
			var status_id_novo = $("#status_id_novo").val();	
			var clienteconfig_id = $("#clienteconfig_id").val();
			var status_id = $("#status_id").val();	

			$.ajax({
			  type: "POST",
			  async: false,
			  url: 'os_controle_aguarda_envio_new_edicao.php?acao=get_sub_status'+"&clienteconfig_id="+clienteconfig_id+"&status_id="+status_id,
			  success: function(data) {
				$('.os_sub_status').html(data);
			  }
			});			
			switch (clienteconfig_id) {
				case '12':
					if(status_id_novo !=<?=STATUS_OS_PRONTO?>){
				       $('.os_sub_status').html("");
				    }																
					break;	
				case '9':					
					if(status_id_novo == <?=STATUS_OS_AGUARDA_PECA?>){
                        $('.os_sub_status').html("<option value='<?=utf8_decode(' Ag.Peça')?>'><?=utf8_decode('Ag.Peça')?></option>')");						
				    }	
					
				break;
			}
		}						
	</script>
	
	<div class="page secondary">
        <div class="page-header">
            <div class="page-header-content titulo-internas">
                <h1><?php echo utf8_decode('Análise Técnica')?><small><?=$pagina_titulo?></small></h1>
				<a href="os_controle.php?cliente_id=<?=$cliente_id?>&clienteconfig_id=<?=$clienteconfig_id?>&status_id=<?=$status_id?>" class="back-button big page-back" title="<?=fct_get_var('global.php', 'var_retornar', $_SESSION["care-br"]["idioma_id"])?>" alt="<?=fct_get_var('global.php', 'var_retornar', $_SESSION["care-br"]["idioma_id"])?>"></a>
            </div>
        </div>

        <div class="page-region">
            <div class="form-pags">
				<div class="grid">
					<form id="frm_cadastro" name="frm_cadastro" method="post">
						<input type="hidden" name="acao" id="acao" value="<?=$acao?>" />
						<input type="hidden" name="acao_form" id="acao_form" value="add_peca" />
						<input type="hidden" name="cliente_id" id="cliente_id" value="<?=$cliente_id?>" />
						<input type="hidden" name="clienteconfig_id" id="clienteconfig_id" value="<?=$clienteconfig_id?>" />
						<input type="hidden" name="status_id" id="status_id" value="<?=$status_id?>" />
						<input type="hidden" name="os_id" id="os_id" value="<?=$os_id?>" />
						<input type="hidden" name="produto_id" id="produto_id" value="<?=$dados_cadastro[0]["produto_id"]?>" />
						<input type="hidden" name="numero_id" id="numero_id" value="<?=$numero_id?>" />

						<fieldset>
                        <legend style="color:black; font-weight: bold">Dados Cliente</legend>
						<div class="row">
							<div class="span2 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_os_id', $_SESSION["care-br"]["idioma_id"])?>
									<i class="icon-help"></i>
									<div class="tooltip">
										<?=fct_get_var('global.php', 'var_os_id_help', $_SESSION["care-br"]["idioma_id"])?>
									</div>
								</label>
								<div class="input-control text">
									<input type="text" id="lista_os" name="lista_os" value="<?=$dados_cadastro[0]["os_id"]?>" disabled />
								</div>
							</div>
							<div class="span3 campos-form">
								<label>
									OS Tipo
									<i class="icon-help"></i>
									<div class="tooltip">
										OS Tipo
									</div>
								</label>
								<div class="input-control text">
									<input type="text" id="os_tipo" name="os_tipo" value="<?=$dados_cadastro[0]["os_tipo"]?>" disabled />
								</div>
							</div>
							<div class="span3 campos-form">
								<label>
									Cobertura
									<i class="icon-help"></i>
									<div class="tooltip">
										Cobertura
									</div>
								</label>
								<div class="input-control text">
									<input type="text" id="os_cobertura" name="os_cobertura" value="<?=$dados_cadastro[0]["os_cobertura"]?>" disabled />
								</div>
							</div>

							<div class="span4 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_nome_cliente', $_SESSION["care-br"]["idioma_id"])?>
								</label>
								<div class="input-control text">
									<input type="text" id="os_solicitante" name="os_solicitante" value="<?=$dados_cadastro[0]["os_solicitante"]?>" disabled />
								</div>
							</div>
							<div class="span3 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_cpf_cnpj', $_SESSION["care-br"]["idioma_id"])?>
								</label>
								<div class="input-control text">
									<input type="text" id="os_solicitante_cpf" name="os_solicitante_cpf" value="<?=$dados_cadastro[0]["os_solicitante_cpf"]?>" disabled />
								</div>
							</div>

							<div class="span3 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_email', $_SESSION["care-br"]["idioma_id"])?>
								</label>
								<div class="input-control text">
									<input type="text" id="os_solicitante_email" name="os_solicitante_email" value="<?=$dados_cadastro[0]["os_solicitante_email"]?>" disabled />
								</div>
							</div>
						
							<div class="span3 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_telefone', $_SESSION["care-br"]["idioma_id"])?>
								</label>
								<div class="input-control text">
									<input type="text" id="os_solicitante_telefone" name="os_solicitante_telefone" value="<?=$dados_cadastro[0]["os_solicitante_telefone"]?>" disabled />
								</div>
							</div>
							<div class="span3 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_celular', $_SESSION["care-br"]["idioma_id"])?>
								</label>
								<div class="input-control text">
									<input type="text" id="os_solicitante_celular" name="os_solicitante_celular" value="<?=$dados_cadastro[0]["os_solicitante_celular"]?>" disabled />
								</div>
							</div>
							<div class="span2 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_cep', $_SESSION["care-br"]["idioma_id"])?>
								</label>
								<div class="input-control text">
									<input type="text" id="os_solicitante_cep" name="os_solicitante_cep" value="<?=$dados_cadastro[0]["os_solicitante_cep"]?>" disabled />
								</div>
							</div>
							<div class="span4 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_endereco', $_SESSION["care-br"]["idioma_id"])?>
								</label>
								<div class="input-control text">
									<input type="text" id="os_solicitante_endereco" name="os_solicitante_endereco" value="<?=$dados_cadastro[0]["os_solicitante_endereco"]?>" disabled />
								</div>
							</div>
							<div class="span2 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_numero', $_SESSION["care-br"]["idioma_id"])?>
								</label>
								<div class="input-control text">
									<input type="text" id="os_solicitante_numero" name="os_solicitante_numero" value="<?=$dados_cadastro[0]["os_solicitante_numero"]?>" disabled />
								</div>
							</div>
							<div class="span4 campos-form">
								<label>
									Complemento
								</label>
								<div class="input-control text">
									<input type="text" id="os_solicitante_compl" name="os_solicitante_compl" value="<?=$dados_cadastro[0]["os_solicitante_compl"]?>" disabled />
								</div>
							</div>

							<div class="span3 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_bairro', $_SESSION["care-br"]["idioma_id"])?>
								</label>
								<div class="input-control text">
									<input type="text" id="os_solicitante_bairro" name="os_solicitante_bairro" value="<?=$dados_cadastro[0]["os_solicitante_bairro"]?>" disabled />
								</div>
							</div>
							<div class="span4 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_cidade', $_SESSION["care-br"]["idioma_id"])?>
								</label>
								<div class="input-control text">
									<input type="text" id="os_solicitante_cidade" name="os_solicitante_cidade" value="<?=$dados_cadastro[0]["os_solicitante_cidade"]?>" disabled />
								</div>
							</div>
							<div class="span1 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_uf', $_SESSION["care-br"]["idioma_id"])?>
								</label>
								<div class="input-control text">
									<input type="text" id="os_solicitante_uf" name="os_solicitante_uf" value="<?=$dados_cadastro[0]["os_solicitante_uf"]?>" disabled />
								</div>
							</div>
						</div>
					</fieldset>
					<fieldset>
                        <legend style="color:black; font-weight: bold">Dados do Produto</legend>
						<div class="row">
							<div class="span3 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_os_serial', $_SESSION["care-br"]["idioma_id"])?>
									<i class="icon-help"></i>
									<div class="tooltip">
										<?=fct_get_var('global.php', 'var_os_serial_help', $_SESSION["care-br"]["idioma_id"])?>
									</div>
								</label>
								<div class="input-control text">
									<input type="text" id="lista_os" name="lista_os" value="<?=$dados_cadastro[0]["os_produto_serial"]?>" disabled />
								</div>
							</div>
							<div class="span3 campos-form">
								<label>
									IMEI
									<i class="icon-help"></i>
									<div class="tooltip">
										IMEI
									</div>
								</label>
								<div class="input-control text">
									<input type="text" id="lista_os" name="lista_os" value="<?=$dados_cadastro[0]["os_imei"]?>" disabled />
								</div>
							</div>
							<div class="span3 campos-form">
								<label>
                                    <?
                                        if ($clienteconfig_id == 123) {
                                            echo "Service GSPN";
                                        } else {
                                            fct_get_var('global.php', 'var_os_chamado', $_SESSION["care-br"]["idioma_id"]);
                                        }
                                    ?>
									<i class="icon-help"></i>
									<div class="tooltip">
										<?=fct_get_var('global.php', 'var_os_chamado_help', $_SESSION["care-br"]["idioma_id"])?>
									</div>
								</label>
								<div class="input-control text">
									<input type="text" id="lista_os" name="lista_os" value="<?=$dados_cadastro[0]["os_chamado_numero"]?>" disabled />
								</div>
							</div>
							<div class="span4 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_os_fabricante', $_SESSION["care-br"]["idioma_id"])?>
									<i class="icon-help"></i>
									<div class="tooltip">
										<?=fct_get_var('global.php', 'var_os_fabricante_help', $_SESSION["care-br"]["idioma_id"])?>
									</div>
								</label>
								<div class="input-control text">
									<input type="text" id="lista_os" name="lista_os" value="<?=$dados_cadastro[0]["empresa_razao_social_fabricante"]?>" disabled />
								</div>
							</div>
							<div class="span6 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_os_produto', $_SESSION["care-br"]["idioma_id"])?>
									<i class="icon-help"></i>
									<div class="tooltip">
										<?=fct_get_var('global.php', 'var_os_produto_help', $_SESSION["care-br"]["idioma_id"])?>
									</div>
								</label>
								<div class="input-control text">
									<input type="text" id="lista_os" name="lista_os" value="<?=$dados_cadastro[0]["produto_descricao"]?>" disabled />
								</div>
							</div>
							<div class="span4 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_os_codigo', $_SESSION["care-br"]["idioma_id"])?>
									<i class="icon-help"></i>
									<div class="tooltip">
										<?=fct_get_var('global.php', 'var_os_codigo_help', $_SESSION["care-br"]["idioma_id"])?>
									</div>
								</label>
								<div class="input-control text">
									<input type="text" id="lista_os" name="lista_os" value="<?=$dados_cadastro[0]["os_produto_codigo"]?>" disabled />
								</div>
							</div>
							
							<?
							if (!empty($dados_cadastro[0]["os_produto_serial_novo"])){
								?>
								<div class="span4 campos-form">
									<label>
										Novo Serial
										<i class="icon-help"></i>
										<div class="tooltip">
											Novo Serial
										</div>
									</label>
									<div class="input-control text">
										<input type="text" id="lista_os" name="lista_os" value="<?=$dados_cadastro[0]["os_produto_serial_novo"]?>" disabled />
									</div>
								</div>
								<?
							}
							?>
							
							<div class="span2 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_os_idade', $_SESSION["care-br"]["idioma_id"])?>
									<i class="icon-help"></i>
									<div class="tooltip">
										<?=fct_get_var('global.php', 'var_os_idade_help', $_SESSION["care-br"]["idioma_id"])?>
									</div>
								</label>
								<div class="input-control text">
									<input type="text" id="lista_os" name="lista_os" value="<?=$dados_cadastro[0]["os_idade"]?>" disabled />
								</div>
							</div>
							<div class="span11 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_motivo', $_SESSION["care-br"]["idioma_id"])?>
									<i class="icon-help"></i>
									<div class="tooltip">
										<?=fct_get_var('global.php', 'var_motivo_help', $_SESSION["care-br"]["idioma_id"])?>
									</div>
								</label>
								<div class="input-control text">
									<textarea disabled id="defeito_reportado" name="defeito_reportado"><?=$dados_cadastro[0]["os_motivo"]?></textarea>
								</div>
							</div>

							<input type="hidden" id="linha_id" name="linha_id" value="<?=$dados_cadastro[0]["linha_id"]?>" disabled />
						</div>
						
						<?
						// ---------------------------------------------------------------------------------
						// nf varejo data aguarda envio (dados informados na triagem, somente para consulta)
						// ---------------------------------------------------------------------------------
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
																			WHERE z.clienteconfig_id = '" . $clienteconfig_id . "' AND y.status_id IN (" . STATUS_OS_AGUARDA_ENVIO . ") AND y.dicionario_ativo = 'S')
									AND d.clienteconfig_id = '" . $clienteconfig_id . "' 
									AND b.dicionario_tipo = '" . DICIONARIO_DADOS_TIPO_DATA . "' 
									AND b.dicionario_ativo = 'S' 
									AND c.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
									ORDER BY a.clienteconfigpassodicionario_ordem";
						$result_dados = $conn->sql($sql);
						while($tmp_dados = mysqli_fetch_array($result_dados)){
							$class = "";
							if (!empty($tmp_dados["dicionario_validacao"])){
								if (!empty($class))	$class .= " ";
								$class .= $tmp_dados["dicionario_validacao"];
							}
							
							$tmp_dados["osclienteconfigpassodicionario_id"] = "";
							$tmp_dados["osclienteconfigpassodicionario_valor"] = "";
							
							// caso campo corrente do dicionário esteja associado a uma OS, buscar dados na própria estrutur da OS,
							// caso contrário, buscar na estrutura do dicionário de dados da OS
							if (!empty($tmp_dados["dicionario_os_campo"]))
								$tmp_dados["osclienteconfigpassodicionario_valor"] = $dados_cadastro[0][$tmp_dados["dicionario_os_campo"]];
							else{
								$sql = "SELECT osclienteconfigpassodicionario_id, osclienteconfigpassodicionario_valor
											FROM tb_prod_os_cliente_config_passo_dicionario_dados
											WHERE os_id = '" . $os_id . "' AND clienteconfigpassodicionario_id = '" . $tmp_dados["clienteconfigpassodicionario_id"] . "'";
								$result_valor = $conn->sql($sql);
								while($tmp_valor = mysqli_fetch_array($result_valor)){
									$tmp_dados["osclienteconfigpassodicionario_id"] = $tmp_valor["osclienteconfigpassodicionario_id"];
									$tmp_dados["osclienteconfigpassodicionario_valor"] = $tmp_valor["osclienteconfigpassodicionario_valor"];
								}
							}
							
							if ($tmp_dados["dicionario_tipo"] == DICIONARIO_DADOS_TIPO_DATA)
								$tmp_dados["osclienteconfigpassodicionario_valor"] = fct_conversorData($tmp_dados["osclienteconfigpassodicionario_valor"], 4);
							
							if ($tmp_dados["dicionario_tipo"] == DICIONARIO_DADOS_TIPO_DATA){
								?>
								<div class="span3 campos-form">
									<label>
										<?=$tmp_dados["dicionario_titulo"]?>
										<i class="icon-help" title="" alt="<?=$tmp_dados["dicionario_help"]?>"></i>
										<div class="tooltip">
											<?=$tmp_dados["dicionario_help"]?>
										</div>
									</label>
									<div class="input-control text">
										<input class="<?=$class?>" type="text" id="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>" name="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>" placeholder="<?=$tmp_dados["dicionario_descricao"]?>" value="<?=$tmp_dados["osclienteconfigpassodicionario_valor"]?>" disabled />
									</div>
								</div>
								<?
							}
						}
						?>
						
						<?
						// --------------------------------------------------------------------------------------------------
						// checklist aguarda recebimento e aguarda envio (dados informados na triagem, somente para consulta)
						// --------------------------------------------------------------------------------------------------
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
																			WHERE z.clienteconfig_id = '" . $clienteconfig_id . "' AND y.status_id IN (" . STATUS_OS_AGUARDA_RECEBIMENTO . "," . STATUS_OS_AGUARDA_ENVIO . ") AND y.dicionario_ativo = 'S')
									AND d.clienteconfig_id = '" . $clienteconfig_id . "' 
									AND b.dicionario_tipo = '" . DICIONARIO_DADOS_TIPO_MOTIVO . "' 
									AND b.dicionario_ativo = 'S' 
									AND c.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
									ORDER BY a.clienteconfigpassodicionario_ordem";
						$result_dados = $conn->sql($sql);
						while($tmp_dados = mysqli_fetch_array($result_dados)){
							?>
							<div class="row row-list">
								<div class="campos-form">
									<label>
										<?=$tmp_dados["dicionario_titulo"]?>
										<i class="icon-help"></i>
										<div class="tooltip">
											<?=$tmp_dados["dicionario_descricao"]?>
										</div>
									</label>
									<?
									if ($tmp_dados["dicionario_validacao"] == "requerido"){
										?>
										<input type="hidden" class="tipo_requerido" id="tipo_id_<?=$tmp_dados["tipo_id"]?>" tipo_descricao="<?=$tmp_dados["dicionario_titulo"]?>" value="<?=$tmp_dados["dicionario_id"]?>">
										<?
									}
									?>
									<ol class="unstyled three-columns">
										<?
										$sql = "SELECT a.motivo_id, a.motivo_exclusivo, a.motivo_cor, a.motivo_flag_observacao, a.motivo_aceita,
													b.idiomamotivo_sigla AS motivo_sigla, b.idiomamotivo_titulo AS motivo_titulo, b.idiomamotivo_descricao AS motivo_descricao
													FROM tb_cad_motivo a
													INNER JOIN tb_cad_idioma_motivo b ON a.motivo_id = b.motivo_id
													WHERE a.tipo_id = '" . $tmp_dados["tipo_id"] . "' AND a.motivo_ativo = 'S' AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] ."'
													ORDER BY b.idiomamotivo_sigla, b.idiomamotivo_titulo";
										$result_filtro = $conn->sql($sql);
										while($tmp_filtro = mysqli_fetch_array($result_filtro)){
											$class = "motivo_aceita_" . $tmp_filtro["motivo_aceita"] . " tipo_" . $tmp_dados["tipo_id"] . " tipo_" . $tmp_dados["tipo_id"] . "_motivo_exclusivo_" . $tmp_filtro["motivo_exclusivo"];

											// ------------------------------------
											// verificar se já existe valor gravado
											// ------------------------------------
											
											// tipo do dado = motivo (1 para N registros)
											$checked = "";
											if (!empty($os_id)){
												$sql = "SELECT osclienteconfigpassodicionario_id, osclienteconfigpassodicionario_valor
															FROM tb_prod_os_cliente_config_passo_dicionario_dados
															WHERE os_id = '" . $os_id . "' AND clienteconfigpassodicionario_id = '" . $tmp_dados["clienteconfigpassodicionario_id"] . "' AND motivo_id = '" . $tmp_filtro["motivo_id"] . "'";
												$result_valor = $conn->sql($sql);
												while($tmp_valor = mysqli_fetch_array($result_valor)){
													$checked = "checked";
													?>
													<li>
														<label class="input-control checkbox" title="" alt="<?=$tmp_filtro["motivo_descricao"]?>">
															<input class="<?=$class?>" type="checkbox" value="<?=$tmp_filtro["motivo_id"]?>" id="motivo_id_<?=$tmp_filtro["motivo_id"]?>" name="motivo_id_<?=$tmp_filtro["motivo_id"]?>" OnClick="javascript: void(0); setaMotivoExclusivo(<?=$tmp_dados["tipo_id"]?>, '<?=$tmp_filtro["motivo_exclusivo"]?>');" <?=$checked?> disabled>
															<span class="helper fg-color-<?=$tmp_filtro["motivo_cor"]?>"><?=$tmp_filtro["motivo_titulo"]?></span>
															<div class="tooltip"><?=$tmp_filtro["motivo_descricao"]?></div>
														</label>
													</li>
													<?
												}
											}
										}											
										?>
									</ol>
								</div>
							</div>
							<?
						}
						?>
					</fieldset>
					<fieldset>
                        <legend style="color:black; font-weight: bold">Dados da Analise</legend>
						
						<?
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
							?>
							<div class="row row-list">
								<div class="campos-form">
									<label>
										<?=$tmp_dados["dicionario_titulo"]?>
										<i class="icon-help"></i>
										<div class="tooltip">
											<?=$tmp_dados["dicionario_descricao"]?>
										</div>
									</label>
									<?
									if ($tmp_dados["dicionario_validacao"] == "requerido"){
										?>
										<input type="hidden" class="tipo_requerido" id="tipo_id_<?=$tmp_dados["tipo_id"]?>" tipo_descricao="<?=$tmp_dados["dicionario_titulo"]?>" value="<?=$tmp_dados["dicionario_id"]?>">
										<?
									}
									?>
									<ol class="unstyled three-columns">
										<?
										$sql = "SELECT a.motivo_id, a.motivo_exclusivo, a.motivo_cor, a.motivo_flag_observacao, a.motivo_aceita,
													b.idiomamotivo_sigla AS motivo_sigla, b.idiomamotivo_titulo AS motivo_titulo, b.idiomamotivo_descricao AS motivo_descricao
													FROM tb_cad_motivo a
													INNER JOIN tb_cad_idioma_motivo b ON a.motivo_id = b.motivo_id
													WHERE a.tipo_id = '" . $tmp_dados["tipo_id"] . "' AND a.motivo_ativo = 'S' AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] ."'
													ORDER BY b.idiomamotivo_sigla, b.idiomamotivo_titulo";
										$result_filtro = $conn->sql($sql);
										while($tmp_filtro = mysqli_fetch_array($result_filtro)){
											$class = "motivo_aceita_" . $tmp_filtro["motivo_aceita"] . " tipo_" . $tmp_dados["tipo_id"] . " tipo_" . $tmp_dados["tipo_id"] . "_motivo_exclusivo_" . $tmp_filtro["motivo_exclusivo"];

											// ------------------------------------
											// verificar se já existe valor gravado
											// ------------------------------------
											
											// tipo do dado = motivo (1 para N registros)
											$checked = "";
											if (!empty($os_id)){
												$sql = "SELECT osclienteconfigpassodicionario_id, osclienteconfigpassodicionario_valor
															FROM tb_prod_os_cliente_config_passo_dicionario_dados
															WHERE os_id = '" . $os_id . "' AND clienteconfigpassodicionario_id = '" . $tmp_dados["clienteconfigpassodicionario_id"] . "' AND motivo_id = '" . $tmp_filtro["motivo_id"] . "'";
												$result_valor = $conn->sql($sql);
												while($tmp_valor = mysqli_fetch_array($result_valor))
													$checked = "checked";
											}
											?>
											<li>
												<label class="input-control checkbox" title="" alt="<?=$tmp_filtro["motivo_descricao"]?>">
													<input class="<?=$class?>" type="checkbox" value="<?=$tmp_filtro["motivo_id"]?>" id="motivo_id_<?=$tmp_filtro["motivo_id"]?>" name="motivo_id_<?=$tmp_filtro["motivo_id"]?>" OnClick="javascript: void(0); setaMotivoExclusivo(<?=$tmp_dados["tipo_id"]?>, '<?=$tmp_filtro["motivo_exclusivo"]?>');" <?=$checked?>>
													<span class="helper fg-color-<?=$tmp_filtro["motivo_cor"]?>"><?=$tmp_filtro["motivo_titulo"]?></span>
													<div class="tooltip"><?=$tmp_filtro["motivo_descricao"]?></div>
												</label>
											</li>
											<?
											if (!empty($checked)){
												?>
												<script type="text/javascript">
													setaMotivoExclusivo(<?=$tmp_dados["tipo_id"]?>, '<?=$tmp_filtro["motivo_exclusivo"]?>');
												</script>
												<?
											}
										}											
										?>
									</ol>
								</div>
							</div>
							<?
						}
						?>
						<!-- carregar lista de peças requeridas aqui -->
						<div id="peca_lista"></div>

						<!-- carregar lista de peças requeridas aqui -->
						<div id="servico_lista"></div>
						<script>getServicoLista();</script>

						<?
							$sql = "SELECT DISTINCT a.usuario_id, a.usuario_nome
										FROM tb_cad_usuario a
										WHERE a.perfil_id IN (SELECT perfil_id
																FROM tb_prod_os_planner_tecnico
																WHERE clienteconfig_id = '$clienteconfig_id')
										ORDER BY  a.usuario_nome";
							if($clienteconfig_id=='20' && $os_tipo=='externo' && $os_cobertura='PITZI'){
								$sql = "SELECT DISTINCT a.usuario_id, a.usuario_nome
								FROM tb_cad_usuario a
								WHERE a.perfil_id IN (SELECT perfil_id
														FROM tb_prod_os_planner_tecnico
														WHERE clienteconfig_id = '$clienteconfig_id' AND perfil_id='74')
								ORDER BY  a.usuario_nome";
							}
							if (strpos(JMV,"|".$clienteconfig_id."|")>0){

						?>					
						
						<div class="span6 campos-form">
							<div class="input-control select">
								<label class="tecnico_reparo" >
									Tecnico Reparo Externo
									<i class="icon-help" title="" alt="tecnico"></i>
									<div class="tooltip">
										Tecnico Reparo Externo
									</div>
								</label>
								<select class="tecnico_reparo" id="tecnico_reparo" name="tecnico_reparo">
									<option value=""></option>
									<?
									$result_tecnico = $conn->sql($sql);
									while($tmp_filtro1 = mysqli_fetch_array($result_tecnico)){
										?>
										<option value="<?=$tmp_filtro1["usuario_id"]?>" title="<?=$tmp_filtro1["usuario_nome"]?>" <? if ($tmp_filtro1["usuario_id"] == $dados_cadastro[0]['usuario_tecnico_reparo']) echo "selected"; ?> alt="<?=$tmp_filtro1["usuario_nome"]?>"><?=$tmp_filtro1["usuario_id"].' - '.$tmp_filtro1["usuario_nome"]?></option>
										<?
									}
									?>
								</select>
							</div>
						</div>
						<?}?>
												
						<div class="row">
							<?
							// ------------------------------
							// dicionario de dados tipo lista
							// ------------------------------
							$sql = "SELECT a.clienteconfigpassodicionario_id, a.clienteconfigpasso_id, a.dicionario_id,
										b.dicionario_tipo, b.dicionario_mascara, b.dicionario_validacao, b.ramo_id, b.tipo_id, b.dicionario_os_campo,
										c.idiomadicionario_titulo AS dicionario_titulo, c.idiomadicionario_descricao AS dicionario_descricao, c.idiomadicionario_help AS dicionario_help, c.idiomadicionario_lista AS dicionario_lista,
										b.dicionario_lista AS dicionario_lista_valor
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
										AND b.dicionario_tipo IN ('servico','" . DICIONARIO_DADOS_TIPO_TEXTAREA . "','" . DICIONARIO_DADOS_TIPO_TEXTO . "', '" . DICIONARIO_DADOS_TIPO_DATA . "', '" . DICIONARIO_DADOS_TIPO_LISTA . "', '" . DICIONARIO_DADOS_TIPO_SIM_NAO  . "', '" . DICIONARIO_DADOS_TIPO_FOTO . "', 'os_local') 
										AND b.dicionario_ativo = 'S' 
										AND c.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
										ORDER BY a.clienteconfigpassodicionario_ordem";
							$result_dados = $conn->sql($sql);
							while($tmp_dados = mysqli_fetch_array($result_dados)){
								$class = "";
								if (!empty($tmp_dados["dicionario_validacao"])){
									if (!empty($class))	$class .= " ";
									$class .= $tmp_dados["dicionario_validacao"];
								}
								

								$tmp_dados["osclienteconfigpassodicionario_id"] = "";
								$tmp_dados["osclienteconfigpassodicionario_valor"] = "";
								
								// caso campo corrente do dicionário esteja associado a uma OS, buscar dados na própria estrutur da OS,
								// caso contrário, buscar na estrutura do dicionário de dados da OS
								if (!empty($tmp_dados["dicionario_os_campo"]))
									$tmp_dados["osclienteconfigpassodicionario_valor"] = $dados_cadastro[0][$tmp_dados["dicionario_os_campo"]];
								else{
									$sql = "SELECT osclienteconfigpassodicionario_id, osclienteconfigpassodicionario_valor
												FROM tb_prod_os_cliente_config_passo_dicionario_dados
												WHERE os_id = '" . $os_id . "' AND clienteconfigpassodicionario_id = '" . $tmp_dados["clienteconfigpassodicionario_id"] . "'";
									$result_valor = $conn->sql($sql);
									while($tmp_valor = mysqli_fetch_array($result_valor)){
										$tmp_dados["osclienteconfigpassodicionario_id"] = $tmp_valor["osclienteconfigpassodicionario_id"];
										$tmp_dados["osclienteconfigpassodicionario_valor"] = $tmp_valor["osclienteconfigpassodicionario_valor"];
									}
								}


								if ($tmp_dados["dicionario_tipo"] == 'servico'){
									
									?>
									<div class="span5 campos-form">
										<div class="input-control select">
											<label>
												<?=$tmp_dados["dicionario_titulo"]?>
												<i class="icon-help" title="" alt="<?=$tmp_dados["dicionario_help"]?>"></i>
												<div class="tooltip">
													<?=$tmp_dados["dicionario_help"]?>
												</div>
											</label>

											<select class='requerido' name="os_tipo_servico" id="os_tipo_servico">
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
													<option value="<?=$tmp_filtro["servico_id"]?>" title="<?=$tmp_filtro["servico_descricao"]?>" alt="<?=$tmp_filtro["servico_descricao"]?>" <? if ($tmp_filtro["servico_id"] == $dados_cadastro[0]['os_tipo_servico']) echo "selected"; ?> ><?=$tmp_filtro["servico_titulo"]?></option>
													<?
												}
												?>
											</select>
											
										</div>
									</div>
									<?
								}
								
								if ($tmp_dados["dicionario_tipo"] == DICIONARIO_DADOS_TIPO_DATA)
									$tmp_dados["osclienteconfigpassodicionario_valor"] = fct_conversorData($tmp_dados["osclienteconfigpassodicionario_valor"], 4);
								
								if ($tmp_dados["dicionario_tipo"] == DICIONARIO_DADOS_TIPO_TEXTAREA){
											?>
											<div class="span6 campos-form">
												<label>
													<?=$tmp_dados["dicionario_titulo"]?>
													<i class="icon-help" title="" alt="<?=$tmp_dados["dicionario_help"]?>"></i>
													<div class="tooltip">
														<?=$tmp_dados["dicionario_help"]?>
													</div>
												</label>
												<div class="input-control textarea">
													<textarea class="<?=$class?>" id="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>" name="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>"><?=$tmp_dados["osclienteconfigpassodicionario_valor"]?></textarea>
												</div>											
											</div>
											<?
								}	

								if ($tmp_dados["dicionario_tipo"] == "os_local"){
								?>
								<div class="span6 campos-form">
									<div class="input-control select">
										<label>
											<span class='campo_obrigatorio-local' style="font-size: 11pt;display: none;">* </span>	
											Local OS
											<i class="icon-help" title="" alt="<?=$tmp_dados["dicionario_help"]?>"></i>
											<div class="tooltip">
												Escolha o local onde ser armazenado a OS
											</div>
										</label>
										
										<select class="<?=$class?> local_os" id="local_os" onchange="javascript: localArmazenamento(this.value);" name="local_os">
											<option value="">Selecione o Local</option>
											<?php
											$sql_buscar_local = "SELECT * FROM tb_cad_os_local_numero WHERE numero_id='".$tmp_dados["osclienteconfigpassodicionario_valor"]."' ";
											$res_buscar_local = $conn->sql($sql_buscar_local);
											$obj_buscar_local = mysqli_fetch_object($res_buscar_local);

											$sql = "SELECT * FROM tb_cad_os_local WHERE local_disponivel = 'S' AND clienteconfig_id='".$clienteconfig_id."' ORDER BY local_nome ";
											$result_filtro = $conn->sql($sql);
											while($tmp_filtro = mysqli_fetch_array($result_filtro)){
												?>
												<option value="<?=$tmp_filtro["local_id"]?>" title="<?=$tmp_filtro["local_nome"]?>" alt="<?=$tmp_filtro["local_nome"]?>" 
													<?php if ($tmp_filtro["local_id"] == $obj_buscar_local->local_id){
														echo "selected"; 
														$local_id = $tmp_filtro["local_id"];
													}
													?> ><?=$tmp_filtro["local_nome"]?></option>
												<?php
											}
											?>
										</select>
										
										<?php
										if($local_id>0){
											echo '<script type="text/javascript">';
												echo '$(document).ready(function() {';
													echo 'localArmazenamento('.$local_id.','.$tmp_dados["osclienteconfigpassodicionario_valor"].');';
												echo '});';
											echo '</script>';
										}
										//echo '--'.$sql_buscar_local;
										?>
									</div>
								</div>

								<div class="span6 campos-form">
									<div class="input-control select">
										<label>
											<span class='campo_obrigatorio-<?=$tmp_dados["dicionario_os_campo"]?>' style="font-size: 11pt;display: none;">* </span>	
											<?=$tmp_dados["dicionario_titulo"]?>
											<i class="icon-help" title="" alt="<?=$tmp_dados["dicionario_help"]?>"></i>
											<div class="tooltip">
												<?=$tmp_dados["dicionario_help"]?>
											</div>
										</label>
										<select class="<?=$class?> numero_local_os" id="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>" name="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>">
											<option value=""><?=$tmp_dados["dicionario_descricao"]?></option>
										</select>

									</div>
								</div>
								<?
							}

								if ($tmp_dados["dicionario_tipo"] == DICIONARIO_DADOS_TIPO_TEXTO){
									?>
									<div class="span6 campos-form">
										<label>
											<?=$tmp_dados["dicionario_titulo"]?>
											<i class="icon-help" title="" alt="<?=$tmp_dados["dicionario_help"]?>"></i>
											<div class="tooltip">
												<?=$tmp_dados["dicionario_help"]?>
											</div>
										</label>
										<div class="input-control text">
											<input class="<?=$class?>" type="text" id="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>" name="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>" placeholder="<?=$tmp_dados["dicionario_descricao"]?>" value="<?=$tmp_dados["osclienteconfigpassodicionario_valor"]?>" />
											<button class="btn-clear"></button>
										</div>
									</div>
									<?
								}
								
								if ($tmp_dados["dicionario_tipo"] == DICIONARIO_DADOS_TIPO_DATA){
									?>
									<div class="span6 campos-form">
										<label>
											<?=$tmp_dados["dicionario_titulo"]?>
											<i class="icon-help" title="" alt="<?=$tmp_dados["dicionario_help"]?>"></i>
											<div class="tooltip">
												<?=$tmp_dados["dicionario_help"]?>
											</div>
										</label>
										<div class="input-control text">
											<input class="<?=$class?>" type="text" id="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>" name="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>" placeholder="<?=$tmp_dados["dicionario_descricao"]?>" value="<?=$tmp_dados["osclienteconfigpassodicionario_valor"]?>" />
											<button class="btn-clear"></button>
										</div>
									</div>
									<?
								}
								
								if ($tmp_dados["dicionario_tipo"] == DICIONARIO_DADOS_TIPO_LISTA){
									$campo_os = $tmp_dados["dicionario_os_campo"];
									?>
									<div class="span6 campos-form">
										<div class="input-control select">
											<label class="label-<?=$campo_os?>">
												<?=$tmp_dados["dicionario_titulo"]?>
												<i class="icon-help" title="" alt="<?=$tmp_dados["dicionario_help"]?>"></i>
												<div class="tooltip">
													<?=$tmp_dados["dicionario_help"]?>
												</div>
											</label>
											<select class="<?=$class." ".$campo_os ?>" id="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>" name="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>" >
												<option value=""><?=$tmp_dados["dicionario_descricao"]?></option>
												<?
												$dicionario_lista = explode(";", $tmp_dados["dicionario_lista"]);
												$dicionario_lista_valor = explode(";", $tmp_dados["dicionario_lista_valor"]);												
												for ($i = 0; $i < count($dicionario_lista); $i++){
													?>
													<option value="<?=$dicionario_lista_valor[$i]?>" <? if ($dicionario_lista_valor[$i] == $tmp_dados["osclienteconfigpassodicionario_valor"]) echo "selected"; ?> ><?=$dicionario_lista[$i]?></option>
													<?
												}
												?>
											</select>
										</div>
									</div>
									<?
								}
								
								if ($tmp_dados["dicionario_tipo"] == DICIONARIO_DADOS_TIPO_SIM_NAO){
									$class_especifico = "";
									$onchange_especifico = "";
									if ($tmp_dados["dicionario_os_campo"] == "os_peca_precisa"){
										$class_especifico = " os_peca_precisa";
										$onchange_especifico = "OnChange='javascript: void(0); getPecaLista();'";
									}
									if ($tmp_dados["dicionario_os_campo"] == "os_prod_emprestimo"){
										$class_especifico = " os_prod_emprestimo";
										$onchange_especifico = "OnChange='javascript: void(0); getProdutoEmprestimo();'";
									}

									?>
									<div class="span6 campos-form">
										<div class="input-control select">
											<label>
												<?=$tmp_dados["dicionario_titulo"]?>
												<i class="icon-help" title="" alt="<?=$tmp_dados["dicionario_help"]?>"></i>
												<div class="tooltip">
													<?=$tmp_dados["dicionario_help"]?>
												</div>
											</label>
											<select class="<?=$class?><?=$class_especifico?>" id="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>" name="clienteconfigpassodicionario_id_<?=$tmp_dados["clienteconfigpassodicionario_id"]?>" <?=$onchange_especifico?> >
												<option value=""><?=$tmp_dados["dicionario_descricao"]?></option>
												<option value="S" <?php if ($tmp_dados["osclienteconfigpassodicionario_valor"] == "S") echo "selected"; ?> ><?=fct_get_var('global.php', 'var_sim', $_SESSION["care-br"]["idioma_id"])?></option>
												<option value="N" <?php if ($tmp_dados["osclienteconfigpassodicionario_valor"] == "N") echo "selected"; ?> ><?=fct_get_var('global.php', 'var_nao', $_SESSION["care-br"]["idioma_id"])?></option>
											</select>
										</div>
									</div>
									<?
									if ($tmp_dados["dicionario_os_campo"] == "os_prod_emprestimo"){
									?>
									<div name="select_produto" id="select_produto">
										<div class="span6 campos-form">
											<div class="input-control select">
												<label>
													Adicionar Produto de Emprestimo
													<i class="icon-help" title="" alt=""></i>
													<div class="tooltip">
														Selecione produto ao emprestimo
													</div>
												</label>
												<!-- link para cadastro de produtos -->
												<a href="emprestimo_produto.php?cliente_id=<?=$cliente_id?>&clienteconfig_id=<?=$clienteconfig_id?>" title="Cadastro de Produto para Emprestimos" alt="Cadastro de Produto para Emprestimos" target="_blank"><i class="icon-cube"></i>Produtos de Emprestimos</a>
												<select name="produto_id_emprestimo" id="produto_id_emprestimo">
												</select>
											</div>
										</div>
									</div>
									<?
									}
									
								}
								
								if ($tmp_dados["dicionario_tipo"] == DICIONARIO_DADOS_TIPO_FOTO){
									?>
									<!-- dialog -->
									<script type="text/javascript" src="js/modern/dialog.js"></script>	
									
									<script type="text/javascript">
									function dialogFoto(funcao){
										$.Dialog({
											'title'      : 'Foto',
											'content'    : getFoto(funcao),
											'draggable'  : true,
											'keepOpened' : true,
											'position'   : {
															'offsetY' : 30
											},																	
											'closeButton': true,
											'buttonsAlign': 'right',
											'buttons' : {
												'OK' : {
													action: function(){
													$.post('os_controle_foto.php', $("#frm-upload").serialize(), function(data, textStatus, xhr) {
												            alert(data);
												        });
													}
												},
												'Cancelar' : {
													action: function(){
														return false;
													}
												}
											}
										});
									}

									function getFoto(funcao){
										var foto = "";
										$.ajax({
										  type: "POST",
										  data: {acao: funcao, os_id: '<?=$os_id?>'},
										  async: false,
										  url: 'os_controle_foto_edicao.php',
										  success: function(data) {
											foto = data;
										  }
										});
										return foto;
									}
									</script>
									
                                    <?
                                        $tamanho = '5';
                                        if ($clienteconfig_id == 123) {
                                            $tamanho = '6';
                                        }
                                    ?>
									<div class="span<?=$tamanho?> campos-form">
										<div class="input-control select">
											<label>
												<?=$tmp_dados["dicionario_titulo"]?>
												<i class="icon-help" title="" alt="<?=$tmp_dados["dicionario_help"]?>"></i>
												<div class="tooltip"> 
													<?=$tmp_dados["dicionario_help"]?>
												</div>
											</label>
											<a href="#dialog_foto" onClick="dialogFoto('gerenciar');" title="Gerenciador de fotos" title="Gerenciador de fotos"><i class="icon-camera"></i></a>
											<a href="#dialog_foto" onClick="dialogFoto('foto_os');" title="Fotos já adicionadas" title="Fotos já adicionadas"><i class="icon-pictures"></i></a>
										</div>
									</div>
									<?
								}
							}

							if(trim($dados_cadastro[0]['os_produto_retorno_cliente']) == 'Retorno Cliente' && $dados_cadastro[0]["os_produto_serial"] != ''){
								verifica_reincidencia($os_id, $clienteconfig_id, $dados_cadastro[0]["os_produto_serial"]);
							}
							?>		

							<div class="span6 campos-form">
								<label>
									<?=fct_get_var('global.php', 'var_os_status_novo', $_SESSION["care-br"]["idioma_id"])?>
									<i class="icon-help"></i>
									<div class="tooltip">
										<?=fct_get_var('global.php', 'var_os_status_novo_help', $_SESSION["care-br"]["idioma_id"])?>
									</div>
								</label>
								<div class="input-control text">
									<select name="status_id_novo" id="status_id_novo">
										<!--
										<option value=""><?=fct_get_var('global.php', 'var_selecione', $_SESSION["care-br"]["idioma_id"])?></option>
										<?
										/*
										$sql = "SELECT d.status_id,
													e.idiomastatus_titulo AS status_titulo, e.idiomastatus_descricao AS status_descricao
													FROM tb_prod_care_cliente_config_passo a
													INNER JOIN tb_prod_care_cliente_config_passo_dicionario_dados b ON a.clienteconfigpasso_id = b.clienteconfigpasso_id
													INNER JOIN tb_cad_dicionario_dados c ON b.dicionario_id = c.dicionario_id 
													INNER JOIN tb_cad_status d ON c.status_id = d.status_id
													INNER JOIN tb_cad_idioma_status e ON d.status_id = e.status_id
													INNER JOIN tb_prod_perfil_status f ON d.status_id = f.status_id AND f.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
													WHERE a.clienteconfig_id = '" . $clienteconfig_id . "' AND c.dicionario_ativo = 'S' AND d.status_ativo = 'S'
													AND d.status_id >= '" . $status_id . "'
													AND e.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'
													ORDER BY a.clienteconfigpasso_ordem LIMIT 0,3";
										$result_filtro = $conn->sql($sql);
										while($tmp_filtro = mysqli_fetch_array($result_filtro)){
											?>
											<option title="<?=$tmp_filtro["status_descricao"]?>" alt="<?=$tmp_filtro["status_descricao"]?>" value="<?=$tmp_filtro["status_id"]?>" <? if ($status_id == $tmp_filtro["status_id"]) echo "selected"; ?> ><?=$tmp_filtro["status_titulo"]?></option>
											<?
										}

										// status cancelado
										$sql = "SELECT a.status_id,
													b.idiomastatus_titulo AS status_titulo, b.idiomastatus_descricao AS status_descricao
													FROM tb_cad_status a
													INNER JOIN tb_cad_idioma_status b ON a.status_id = b.status_id
													INNER JOIN tb_prod_perfil_status c ON a.status_id = c.status_id AND c.perfil_id = '" . $_SESSION["care-br"]["perfil_id"] . "'
													WHERE a.status_id IN (" . STATUS_OS_CANCELADO . ") AND a.status_ativo = 'S'
													AND a.status_id >= '" . $tmp_status["status_id"] . "'
													AND b.idioma_id = '" . $_SESSION["care-br"]["idioma_id"] . "'";
										$result_filtro = $conn->sql($sql);
										while($tmp_filtro = mysqli_fetch_array($result_filtro)){
											?>
											<option title="<?=$tmp_filtro["status_descricao"]?>" alt="<?=$tmp_filtro["status_descricao"]?>" value="<?=$tmp_filtro["status_id"]?>" <? if ($status_id == $tmp_filtro["status_id"]) echo "selected"; ?> ><?=$tmp_filtro["status_titulo"]?></option>
											<?
										}
										*/
										?>
										-->
									</select>
								</div>
							</div>
						</div>
					</fieldset>
						
						<div class="row">
							<div class="btn-salvar">
								<input type="reset" value="<?=fct_get_var('global.php', 'var_botao_redefinir', $_SESSION["care-br"]["idioma_id"])?>">
								<input type="submit" value="<?=fct_get_var('global.php', 'var_botao_confirmar', $_SESSION["care-br"]["idioma_id"])?>" OnClick="javascript: void(0); $('#acao_form').val('submit');">
								<input type="button" value="Gerar Laudo" OnClick="javascript: void(0); geraLaudo();">

								<? if($cliente_id == 53) { ?>
									<input type="button" value="Imprimir Etiqueta" OnClick="javascript: void(0); imprimitEtiqueta();">
								<? } ?>

								<a href="javascript: void(0);" onClick="dialogComentariogeral();"><i class="icon-comments-5"></i><?=utf8_decode("Histórico de Comentários")?></a>								
								<a id='agenda_tecnico' href="javascript: void(0);" onClick="AgendaTecnico(<?php echo $os_id ?>);"><i class="icon-calendar"></i><?=utf8_decode("Agendar Retirada Produto")?></a>

								
                                    <?		
                                        $empresacliente_id = $dados_cadastro[0]["empresacliente_id"];
                                        //$rotina_link_nf = get_rotina_invisivel(VAR_MENU_CABECALHO, $_SESSION["care-br"]["submodulo_pagina"]);
                                        //$acesso_liberado = strpos($rotina_link_nf, "gerar_os_nfs");
                                        if ($clienteconfig_id==178){

                                        	$sql_linha = "SELECT * FROM tb_prod_care_cliente_config WHERE clienteconfig_id = '$clienteconfig_id'  ";
											$result_filtro = $conn->sql($sql_linha);
											while($tmp_filtro = mysqli_fetch_array($result_filtro)){
												$empresacliente_id=$tmp_filtro["empresacliente_id"];
											}
                                            
                                                if (empty($dados_cadastro[0]["xml_serv"])){?>    
                                                    <a href="javascript: void(0);" onClick="gerarNf('gerar_nfs',<?=$dados_cadastro[0]["os_id"]?>,<?=$empresacliente_id?>);" title="Gerar NFS-e" alt="Gerar NFS-e"><i class="icon-new"></i>NFS</a><?											
                                                }else{
                                                    $acesso_liberado = strpos($rotina_link_nf, "cancelar_os_nfs");
                                                    if ($acesso_liberado){?>
                                                        <a id='cancela_nfse' href="javascript: void(0);" onClick="gerarNf('dialogCancelaNFSe','<?=$dados_cadastro[0]['os_id']?>','<?=$empresacliente_id?>');"><i class="icon-cancel"></i>Cancel-NF</a> 
                                                        <!-- <a id='cancela_nfse' href="javascript: void(0);" onClick="dialogCancelaNFSe('<?=$dados_cadastro[0]['1os_id']?>','<?=$empresacliente_id?>');"><i class="icon-cancel"></i>Cancel-NF</a>  -->
                                                    <?}
                                                
                                            }										
                                        }								
                                        										
                                    ?>
                               			
							</div>
						</div>
					</form>
				</div>
			</div>
        </div>
    </div>
	<?
	include("footer.php");
}

if ($acao == "add_orc_rapido"){
	header("Location: os_controle_aguarda_orcamento.php?cliente_id=$cliente_id&clienteconfig_id=$clienteconfig_id&os_id=$os_id"); 
}


$conn->fechar();
?>
