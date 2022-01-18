var qtd_produto = [];
var nfe_update = '';
var fornecedor_update = '';
var salvo = false;

$(document).ready( () => {
    const urlSearchParams = new URLSearchParams(window.location.search);
    var params = Object.fromEntries(urlSearchParams.entries());

    if(params.nfe == undefined || params.nfe == '') {

        $('.load-content').remove();
        $('.conteiner-centered').html(' <div class="erro-content"> <div class="error"><i class="material-icons">error_outline</i><div class="msg_erro">Houve um erro para carregar a página, clique no botão abaixo para navegar para página inicial das transferências</div> <div class="centered hidden" style="display: block;"> <div class="button" tabindex="0"> <a class="button_text">PÁGINA INICIAL</a> </div> </div> </div> </div>');
        $('.button').click( () => { window.location.replace('/consulta_nota.html') });

    } else {

        var nfe = params.nfe;

        $.post('../src/components/consulta_nota.php',{
                nfe : nfe
            }, (nota) => {
                var info_nota = jQuery.parseJSON(nota);
                
                if(info_nota.erro !== undefined) {
                    $('.load-content').remove();
                    $('.conteiner-centered').html(' <div class="erro-content"> <div class="error"><i class="material-icons">error_outline</i><div class="msg_erro">Houve um erro para carregar a página, clique no botão abaixo para navegar para página inicial das transferências</div> <div class="centered hidden" style="display: block;"><div class="button" tabindex="0"> <a class="button_text">PÁGINA INICIAL</a> </div> </div> </div> </div>');
                    $('.button').click( () => { window.location.replace('/consulta_nota.html') });
                } else {

                    if(info_nota[0].transferido == 0) {
                        nfe_update = info_nota[0].nfe;
                        fornecedor_update = info_nota[0].fornecedor;

                        $('.load-content').remove();
                        $('.erro-content').remove();

                        $('.header').html('<div clas="acoes"><a id="btn-voltar" class="waves-effect waves-light blue lighten-1 btn"><i class="material-icons right">arrow_back</i>MENU DAS NOTAS</a><a id="btn-gerar" class="waves-effect waves-light btn red lighten-2" onClick="gerar()"><i class="material-icons right">archive</i>GERAR TRANSFERÊNCIAS</a><a id="btn-salvar-informacoes" class="waves-effect waves-light btn" onClick="salvar();"><i class="material-icons right">save</i>SALVAR INFORMAÇÕES</a></div>');
                        $('#btn-voltar').click( () => { window.location.replace('/consulta_nota.html') });

                        $('.header').after('<div data-nfe="'+info_nota[0].nfe+'" class="nota_info"> <span class="nfe">Nota fiscal</span> <div class="nota_cabecalho"> <div class="cabecalho_info row"> <div id="nfe" class="input-field col"> <input id="num_nfe" type="text" class="validate" value="'+info_nota[0].nfe+'" disabled="disabled"> <label for="num_nfe" class="active">NF-e/Série</label> </div> <div id="fornecedor" class="input-field col"> <input id="fornecedor_nfe" type="text" class="validate" value="'+info_nota[0].fornecedor+'" disabled=""> <label for="fornecedor_nfe" class="active">Fornecedor</label> </div> <div id="data_entrada" class="input-field col"> <input id="entrada_nfe" type="text" class="validate" value="'+info_nota[0].data_entrada+'" disabled=""> <label for="entrada_nfe" class="active">Data da Entrada</label> </div> </div> </div> </div>');
                    

                    } else if(info_nota[0].transferido == 1) {
                        $('.load-content').remove();
                        $('.erro-content').remove();
                        $('.conteiner-centered').html(' <div class="erro-content"> <div class="error"><i class="material-icons">error_outline</i><b style="font-size:25px;">Nota fiscal já transferida</b><div class="msg_erro">Não foi possível completar operação, pois a Nota fiscal '+nfe+' solicitada já foi transferida anteriormente.</div> <div class="centered hidden" style="display: block;"><div class="button" tabindex="0"> <a class="button_text">CONSULTAR NOTAS</a> </div> </div> </div> </div>');
                        $('.button').click( () => { window.location.replace('/consulta_nota.html') });
                    }
                }
            }
        )

        $.post('../src/components/transferir_nota.php',{
                nfe : nfe
            }, (data) => {
            var info = jQuery.parseJSON(data)

            if(info.erro !== undefined) {
            } else {
                let config = info[0].layout_transferir;

                if(config == 1) {
                    $('.nota_fiscal').html('<div class="tabelas"><table class="tb_item striped"><thead></thead><tbody></tbody></table><table class="tb_loja striped"><thead></thead><tbody></tbody></table></div>');

                    $('.tb_item > thead').append('<tr><th class="th_n">Nº</th><th class="th_ean">EAN</th><th class="th_descricao">DESCRIÇÃO</th><th class="th_qtd">QTD</th></tr>');
                    Tipped.create('.th_qtd', 'Quantidades de unidades na Nota fiscal');
                    Tipped.create('.th_ean', 'Código de Barras associados na Entrada da Nota fiscal');

                    $('.tb_loja > thead').append('<tr><th class="th_utilidades">LOJA 1</th><th class="th_papelaria">LOJA 2</th><th class="th_praca">LOJA 3</th><th class="th_sumidouro">LOJA 4</th><th class="th_distribuidora">LOJA 5</th><th class="th_total">TOTAL</th></tr>');
                    Tipped.create('.th_utilidades', 'Bazar Opção - Utilidades');
                    Tipped.create('.th_papelaria', 'Bazar Opção - Papelaria');
                    Tipped.create('.th_praca', 'Bazar Opção - Praça');
                    Tipped.create('.th_sumidouro', 'Bazar Opção - Sumidouro');
                    Tipped.create('.th_distribuidora', 'Bazar Opção - Distribuidora');
                }

                $.each(info, (i, v) => {
                    qtd_produto[i] = i;

                    if(config == 1) {        
                        $('.tb_item > tbody').append('<tr id="nItem_cp_'+(i+1)+'" data-item="'+(i+1)+'" data-id="'+v.id+'"><td class="td_n">'+(i+1)+'</td><td class="td_ean"><a id="erro_'+(i+1)+'"><div class="input-field-compacto"> <input id="ean_'+(i+1)+'" data="erro_'+(i+1)+'" type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="16" class="compacto_ean remove_input" value="'+v.ean+'" disabled><span id="edit_ean_'+(i+1)+'" data-n="'+(i+1)+'" data-nfe="'+v.nfe+'" data-id="'+v.id+'" data-ean="'+v.ean+'" toggle-ean="#ean_'+(i+1)+'" class="field-icon compacto_icon toggle"><span class="material-icons on">edit</span></span> </div> </td></a><td id="description_'+(i+1)+'" class="td_desc" value="'+v.descricao+'">'+v.descricao.slice(0,60)+'</td><td id="qtd_'+(i+1)+'" class="td_qtd">'+v.qtd+'</td></tr></tbody>');

                        $('.tb_loja > tbody').append('<tr><td><input data-total="total_'+(i+1)+'" data="transferencia" id="utilidades_'+(i+1)+'" type="number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="7" class="validate remove_input_lojas compacto_trans"></td><td><input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="7" data="transferencia" id="papelaria_'+(i+1)+'" type="number" class="validate remove_input_lojas compacto_trans"></td><td><input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="7" data="transferencia" id="praca_'+(i+1)+'" type="number" class="validate remove_input_lojas compacto_trans"></td><td><input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="7" data="transferencia" id="sumidouro_'+(i+1)+'" type="number" class="validate remove_input_lojas compacto_trans"></td><td><input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="7" data="transferencia" id="distribuidora_'+(i+1)+'" type="number" class="validate remove_input_lojas compacto_trans"></td><td><input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="7" data-n="'+(i+1)+'" data-id="'+v.id+'" data-ean="'+v.ean+'" data-desc="'+v.descricao+'" data-qtd="'+v.qtd+'" id="total_'+(i+1)+'" type="number" class="validate remove_input_lojas  compacto_trans" disabled></td></tr></tbody>');

                        Tipped.create('#description_'+(i+1)+'', v.descricao);

                        const qtd_utilidades = document.getElementById('utilidades_'+(i+1)+'');
                        const qtd_papelaria = document.getElementById('papelaria_'+(i+1)+'');
                        const qtd_praca = document.getElementById('praca_'+(i+1)+'');
                        const qtd_sumidouro = document.getElementById('sumidouro_'+(i+1)+'');
                        const qtd_distribuidora = document.getElementById('distribuidora_'+(i+1)+'');
                    
                        qtd_utilidades.addEventListener('keyup', function(event) {
                            if(event.target == qtd_utilidades) {
                                var qtd_nota = parseFloat($('#qtd_'+(i+1)+'').text());
                                let total = $('#total_'+(i+1)+'');
                                var loja_1 = parseFloat(this.value);
                                var loja_2 = parseFloat($('#papelaria_'+(i+1)+'').val() ? $('#papelaria_'+(i+1)+'').val() : 0 );
                                var loja_3 = parseFloat($('#praca_'+(i+1)+'').val() ? $('#praca_'+(i+1)+'').val() : 0 );
                                var loja_4 = parseFloat($('#sumidouro_'+(i+1)+'').val() ? $('#sumidouro_'+(i+1)+'').val() : 0 );
                                var loja_5 = parseFloat($('#distribuidora_'+(i+1)+'').val() ? $('#distribuidora_'+(i+1)+'').val() : 0 );
                                if(this.value == '') {
                                    loja_1 = 0;
                                }
                                var soma = (loja_1+loja_2+loja_3+loja_4+loja_5);

                                if(soma <= qtd_nota) {
                                    total.addClass('menor_qtd');
                                    total.removeClass('maior_qtd');
                                } else if (soma > qtd_nota) {
                                    total.removeClass('menor_qtd');
                                    total.addClass('maior_qtd');
                                }
                                if(soma == 0) {
                                    total.attr('value', '')
                                } else {
                                    total.attr('value', soma)
                                }
                            }
                        });

                        qtd_papelaria.addEventListener('keyup', function(event) {
                            if(event.target == qtd_papelaria) {
                                var qtd_nota = parseFloat($('#qtd_'+(i+1)+'').text());
                                let total = $('#total_'+(i+1)+'');
                                var loja_1 = parseFloat($('#utilidades_'+(i+1)+'').val() ? $('#utilidades_'+(i+1)+'').val() : 0 );
                                var loja_2 = parseFloat(this.value);
                                var loja_3 = parseFloat($('#praca_'+(i+1)+'').val() ? $('#praca_'+(i+1)+'').val() : 0 );
                                var loja_4 = parseFloat($('#sumidouro_'+(i+1)+'').val() ? $('#sumidouro_'+(i+1)+'').val() : 0 );
                                var loja_5 = parseFloat($('#distribuidora_'+(i+1)+'').val() ? $('#distribuidora_'+(i+1)+'').val() : 0 );
                                if(this.value == '') {
                                    loja_2 = 0;
                                }
                                var soma = (loja_1+loja_2+loja_3+loja_4+loja_5);
                                if(soma <= qtd_nota) {
                                    total.addClass('menor_qtd');
                                    total.removeClass('maior_qtd');
                                } else if (soma > qtd_nota) {
                                    total.removeClass('menor_qtd');
                                    total.addClass('maior_qtd');
                                }
                                if(soma == 0) {
                                    total.attr('value', '')
                                } else {
                                    total.attr('value', soma)
                                }
                            }
                        });

                        qtd_praca.addEventListener('keyup', function(event) {
                            if(event.target == qtd_praca) {
                                var qtd_nota = parseFloat($('#qtd_'+(i+1)+'').text());
                                let total = $('#total_'+(i+1)+'');
                                var loja_1 = parseFloat($('#utilidades_'+(i+1)+'').val() ? $('#utilidades_'+(i+1)+'').val() : 0 );
                                var loja_2 = parseFloat($('#papelaria_'+(i+1)+'').val() ? $('#papelaria_'+(i+1)+'').val() : 0 );
                                var loja_3 = parseFloat(this.value);
                                var loja_4 = parseFloat($('#sumidouro_'+(i+1)+'').val() ? $('#sumidouro_'+(i+1)+'').val() : 0 );
                                var loja_5 = parseFloat($('#distribuidora_'+(i+1)+'').val() ? $('#distribuidora_'+(i+1)+'').val() : 0 );
                                if(this.value == '') {
                                    loja_3 = 0;
                                }
                                var soma = (loja_1+loja_2+loja_3+loja_4+loja_5);
                                if(soma <= qtd_nota) {
                                    total.addClass('menor_qtd');
                                    total.removeClass('maior_qtd');
                                } else if (soma > qtd_nota) {
                                    total.removeClass('menor_qtd');
                                    total.addClass('maior_qtd');
                                }
                                if(soma == 0) {
                                    total.attr('value', '')
                                } else {
                                    total.attr('value', soma)
                                }
                            }
                        });

                        qtd_sumidouro.addEventListener('keyup', function(event) {
                            if(event.target == qtd_sumidouro) {
                                var qtd_nota = parseFloat($('#qtd_'+(i+1)+'').text());
                                let total = $('#total_'+(i+1)+'');
                                var loja_1 = parseFloat($('#utilidades_'+(i+1)+'').val() ? $('#utilidades_'+(i+1)+'').val() : 0 );
                                var loja_2 = parseFloat($('#papelaria_'+(i+1)+'').val() ? $('#papelaria_'+(i+1)+'').val() : 0 );
                                var loja_3 = parseFloat($('#praca_'+(i+1)+'').val() ? $('#praca_'+(i+1)+'').val() : 0 );
                                var loja_4 = parseFloat(this.value);
                                var loja_5 = parseFloat($('#distribuidora_'+(i+1)+'').val() ? $('#distribuidora_'+(i+1)+'').val() : 0 );
                                if(this.value == '') {
                                    loja_4 = 0;
                                }
                                var soma = (loja_1+loja_2+loja_3+loja_4+loja_5);
                                if(soma <= qtd_nota) {
                                    total.addClass('menor_qtd');
                                    total.removeClass('maior_qtd');
                                } else if (soma > qtd_nota) {
                                    total.removeClass('menor_qtd');
                                    total.addClass('maior_qtd');
                                }
                                if(soma == 0) {
                                    total.attr('value', '')
                                } else {
                                    total.attr('value', soma)
                                }
                            }
                        });

                        qtd_distribuidora.addEventListener('keyup', function(event) {
                            if(event.target == qtd_distribuidora) {
                                var qtd_nota = parseFloat($('#qtd_'+(i+1)+'').text());
                                let total = $('#total_'+(i+1)+'');
                                var loja_1 = parseFloat($('#utilidades_'+(i+1)+'').val() ? $('#utilidades_'+(i+1)+'').val() : 0 );
                                var loja_2 = parseFloat($('#papelaria_'+(i+1)+'').val() ? $('#papelaria_'+(i+1)+'').val() : 0 );
                                var loja_3 = parseFloat($('#praca_'+(i+1)+'').val() ? $('#praca_'+(i+1)+'').val() : 0 );
                                var loja_4 = parseFloat($('#sumidouro_'+(i+1)+'').val() ? $('#sumidouro_'+(i+1)+'').val() : 0 );
                                var loja_5 = parseFloat(this.value);
                                if(this.value == '') {
                                    loja_5 = 0;
                                }
                                var soma = (loja_1+loja_2+loja_3+loja_4+loja_5);
                                if(soma <= qtd_nota) {
                                    total.addClass('menor_qtd');
                                    total.removeClass('maior_qtd');
                                } else if (soma > qtd_nota) {
                                    total.removeClass('menor_qtd');
                                    total.addClass('maior_qtd');
                                }
                                if(soma == 0) {
                                    total.attr('value', '')
                                } else {
                                    total.attr('value', soma)
                                }
                            }
                        });
                    }

                    if(v.ean_alterado == 1) {
                        let ean_notify = $('#ean_'+(i+1)+'').parent();
                        $('#ean_'+(i+1)+'').addClass('ean_alterado');
                        $('#ean_'+(i+1)+'').css({'cursor':'help'});
                        Tipped.create(ean_notify, 'Produto teve alteração no Código de Barras, não sendo mais o EAN importado da Nota fiscal');
                    }

                    if(v.utilidades != null) {
                        $('#utilidades_'+(i+1)).attr('value', v.utilidades);
                        $('label[for="utilidades_'+(i+1)+'"').addClass('active')
                    }

                    if(v.papelaria != null) {
                        $('#papelaria_'+(i+1)).attr('value', v.papelaria);
                        $('label[for="papelaria_'+(i+1)+'"').addClass('active')
                    }

                    if(v.praca != null) {
                        $('#praca_'+(i+1)).attr('value', v.praca);
                        $('label[for="praca_'+(i+1)+'"').addClass('active')
                    }

                    if(v.sumidouro != null) {
                        $('#sumidouro_'+(i+1)).attr('value', v.sumidouro);
                        $('label[for="sumidouro_'+(i+1)+'"').addClass('active')
                    }

                    if(v.distribuidora != null) {
                        $('#distribuidora_'+(i+1)).attr('value', v.distribuidora);
                        $('label[for="distribuidora_'+(i+1)+'"').addClass('active')
                    }

                    let total = $('#total_'+(i+1)+'');
                    var qtd_nota = parseFloat($('#qtd_'+(i+1)+'').text());
                    let db_utilidades = parseFloat(v.utilidades ? v.utilidades : 0);
                    let db_papelaria = parseFloat(v.papelaria ? v.papelaria : 0);
                    let db_praca = parseFloat(v.praca ? v.praca : 0);
                    let db_sumidouro = parseFloat(v.sumidouro ? v.sumidouro : 0);
                    let db_distribuidora = parseFloat(v.distribuidora ? v.distribuidora : 0);

                    total_soma = (db_utilidades+db_papelaria+db_praca+db_sumidouro+db_distribuidora);

                    if(total_soma <= qtd_nota) {
                        total.addClass('menor_qtd');
                        total.removeClass('maior_qtd');
                    } else if (total_soma > qtd_nota) {
                        total.removeClass('menor_qtd');
                        total.addClass('maior_qtd');
                    }

                    if(total_soma == 0) {
                        total.attr('value', '')
                    } else {
                        total.attr('value', total_soma)
                    }

                    var edit = 0;
                    $("#edit_ean_"+(i+1)).click(function (e) {
                        e.preventDefault();
                        $(this).toggleClass("toggle");
                        
                        var input_ean = $($(this).attr("toggle-ean"));

                        if (edit == 0) {
                            $(this).html('<span class="material-icons off">check</span >');
                            edit = 1;
                        } else {
                            if(input_ean.val() == '' || input_ean.val() == 0 ) {
                                input_ean.addClass('invalid');
                            } else {
                                $(this).html('<span class="material-icons on">edit</span >');
                                edit = 0;
                            }
                        }

                        if (input_ean.prop('disabled') == true) {
                            input_ean.removeAttr('disabled');
                            input_ean.addClass('active_edit');
                        } else {
                            if(input_ean.val() != '' && input_ean.val() != 0) {

                                if(input_ean.val() != $(this).attr("data-ean")) {
                                    $.post('../src/components/alterar_ean.php',{
                                            id  : $(this).attr("data-id"),
                                            nfe : $(this).attr("data-nfe"),
                                            ean : input_ean.val()
                                        }, (data_ean) => {
                                            var info_ean = jQuery.parseJSON(data_ean);

                                            if(info_ean.ean_alterado == true) {
                                            
                                                $(this).attr('data-ean', input_ean.val());
                                                $('#erro_'+$(this).attr('data-n')).removeClass('btn-floating pulse red lighten-2');
                                                input_ean.attr('disabled', 'disabled').removeClass('valid');
                                                input_ean.removeClass('active_edit ean_pendente').addClass('ean_alterado');
                                            } 
                                        }
                                    );
                                } else {
                                    input_ean.removeClass('active_edit');
                                    input_ean.css({'color':'#0000008c'})
                                    input_ean.attr('disabled', 'disabled').removeClass('valid');
                                    $('#erro_'+$(this).attr('data-n')).removeClass('btn-floating pulse red lighten-2');
                                }
                            }
                        }
                    });

                    $('form').on('change paste', 'input, select, textarea', function(){
                        if(salvo != true) {
                            window.onbeforeunload = function() {
                                return '';
                            }
                        }
                    });
                });
            }
        });
    }
});

var utilidades = [];
var papelaria = [];
var praca = [];
var sumidouro = [];
var distribuidora  = [];

function gerar() {
    if($('.active_edit').length > 0) {
        $('.active_edit').each( (index, value) => {
            $('#'+value.getAttribute('data')).addClass('btn-floating pulse red lighten-2')
        });
    } else {
        gerar_array(false);
    
        if(jQuery.isEmptyObject(utilidades) == true && jQuery.isEmptyObject(papelaria) == true && jQuery.isEmptyObject(praca) == true && jQuery.isEmptyObject(sumidouro) == true && jQuery.isEmptyObject(distribuidora) == true) {
            $('.footer').html('<div id="modal_save_erro" class="modal"><div class="modal-content"><div class="header_modal"><i class="material-icons" style="font-size:120px;color:#f8bb86;">error_outline</i></div><div  class="content_modal"><b>Erro ao gerar transferência</b><br/><div style="text-align:justify;"> Nenhum produto houve quantidade inserida, preencha pelo menos um produto para gerar a transferência</div></div><div class="footer_modal_save_erro"><button class="btn waves-effect waves-light blue lighten-1" onClick="fechar_modal()">FECHAR NOTIFICAÇÃO</button></div></div>');
            $('.modal').modal();
            $('#modal_save_erro').modal('open');
            $('input[data="transferencia"]').addClass('invalid');
        } else {
            if($('.maior_qtd').length > 0){
                $('.footer').html('<div id="modal_sup" class="modal"><div class="modal-content gerar"><div class="header_modal"><i class="material-icons" style="font-size:120px;color:#f8bb86;">error_outline</i></div><div  class="content_modal"><b>Você está preste a gerar uma Transferência </b><br/><div class="msg_confirmacao">Após a confirmação não será possível realizar qualquer edição, caso ainda não tenha certeza poderá salvar a transferência para gerar mais tarde</div><div class="qtd_superior"></div><div>Confira os produtos que estão com as quantidades superiores a quantidade na Nota fiscal<div class="item_superior"></div></div>Deseja realmente gerar arquivo?</div><div class="footer_modal_sup"><button class="btn waves-effect waves-light btn red lighten-2" onClick="gerar_confirmacao();fechar_modal();">SIM, GERAR</button><button class="btn waves-effect waves-light btn-nao" onClick="fechar_modal();">NÃO</button></div></div>');
               
                $('.modal').modal();
                $('#modal_sup').modal('open');

                $('.maior_qtd').each( function(index, value) {
                    let qtd_loja_1 = $('#utilidades_'+($(this).attr('data-n')-1)).val() ? $('#utilidades_'+($(this).attr('data-n')-1)).val() : 0;
                    let qtd_loja_2 = $('#papelaria_'+($(this).attr('data-n')-1)).val() ? $('#papelaria_'+($(this).attr('data-n')-1)).val() : 0;
                    let qtd_loja_3 = $('#praca_'+($(this).attr('data-n')-1)).val() ? $('#praca_'+($(this).attr('data-n')-1)).val() : 0;
                    let qtd_loja_4 = $('#sumidouro_'+($(this).attr('data-n')-1)).val() ? $('#sumidouro_'+($(this).attr('data-n')-1)).val() : 0;
                    let qtd_loja_5 = $('#distribuidora_'+($(this).attr('data-n')-1)).val() ? $('#distribuidora_'+($(this).attr('data-n')-1)).val() : 0;
                    let prod_n = $(this).attr('data-n');
                    let prod_id = $(this).attr('data-id');
                    let prod_ean = $(this).attr('data-ean');
                    let prod_desc =  $(this).attr('data-desc');
                    let prod_qtd = $(this).attr('data-qtd');

                    $('.item_superior').append('<div id="prod_sup_'+index+'">Nº: '+prod_n+' - '+prod_ean+' - <span id="desc_sup_'+index+'" style="cursor:help;">'+prod_desc.slice(0,30)+'</span> - NF-e: <b style="color:#26a69a;">'+prod_qtd+'</b></div><div class="lojas_sup">Loja 1: <b>'+qtd_loja_1+'</b> / Loja 2: <b>'+qtd_loja_2+'</b> / Loja 3: <b>'+qtd_loja_3+'</b> / Loja 4: <b>'+qtd_loja_4+'</b> / Loja 5: <b>'+qtd_loja_5+'</b> / Total: <b style="color:#E57373;">'+this.value+'</b></div>')

                    Tipped.create('#desc_sup_'+index+'', prod_desc);
                });
            } else {
                $('.footer').html('<div id="modal_gerar" class="modal"><div class="modal-content gerar"><div class="header_modal"><i class="material-icons" style="font-size:120px;color:#f8bb86;">error_outline</i></div><div  class="content_modal"><b>Você está preste a gerar uma Transferência </b><br/><div class="msg_confirmacao">Após a confirmação não será possível realizar qualquer edição, caso ainda não tenha certeza poderá salvar a transferência para gerar mais tarde</div>Deseja realmente gerar arquivo?</div><div class="footer_modal_gerar"><button class="btn waves-effect waves-light btn red lighten-2" onClick="gerar_confirmacao();fechar_modal();">SIM, GERAR</button><button class="btn waves-effect waves-light btn-nao" onClick="fechar_modal()">NÃO</button></div></div>');
                $('.modal').modal();
                $('#modal_gerar').modal('open');
            }
        }
    }
}

function gerar_confirmacao (confirmacao_sup) {
    var conf = confirmacao_sup ? confirmacao_sup : false;

    if($('.maior_qtd').length > 0 && conf == false){
        $('.footer').html('<div id="modal_senha" class="modal"><div class="modal-content gerar"><div class="header_modal"><i class="material-icons" style="font-size:120px;color:#f8bb86;">error_outline</i></div><div  class="content_modal"><b>Você está preste a gerar uma Transferência </b><br/><div class="msg_confirmacao">Para confirmar a transfêrencia com as quantidades superiores ao permitido da Nota fiscal, insira a senha de liberação</div><div class="col input-field" id="content-senha"><input id="confirmar_senha" type="password" class="validate"><label for="confirmar_senha">Senha</label><span id="erro_confirmar_senha" class="helper-text" data-error=""></span></span></div>Deseja realmente gerar arquivo com quantidades superiores?</div><div class="footer_modal_gerar"><button id="btn-confirmar" class="btn waves-effect waves-light btn red lighten-2">CONFIRMAR</button><button class="btn waves-effect waves-light btn-nao">NÃO</button></div></div>');
        $('.modal').modal();
        $('#modal_senha').modal('open');
        $('.btn-nao').click( () => {
            $('#modal_senha').modal('close');
        })

        $('#btn-confirmar').click( ()=>{
            var senha_sistema = '142536';
            if($('#confirmar_senha').val() === senha_sistema) {
                $('.load').show();
                $('#modal_senha').modal('close');
                gerar_confirmacao(true);
            } else {
                $('#confirmar_senha').removeClass('valid');
                $('#confirmar_senha').addClass('invalid');
                $('#erro_confirmar_senha').attr('data-error', 'Senha inválida, tente novamente');
            }
        });
    } else {
        gerar_array(true);

        $.post('../src/components/gerar_txt.php', {
                loja_utilidades     : utilidades,
                loja_papelaria      : papelaria,
                loja_praca          : praca,
                loja_sumidouro      : sumidouro,
                loja_distribuidora  : distribuidora
            }, (data) => {
                var info = jQuery.parseJSON(data);
                $('.load').hide();

                if(info.UTILIDADES != undefined || info.PAPELARIA != undefined || info.PRACA != undefined || info.SUMIDOURO != undefined || info.DISTRIBUIDORA != undefined) {
                    if(info.UTILIDADES == true) {
                        var status_utilidades = 'Gerado com sucesso';
                    } else {
                        var status_utilidades = 'Não houve transferência';
                    }

                    if(info.PAPELARIA == true) {
                        var status_papelaria = 'Gerado com sucesso';
                    } else {
                        var status_papelaria = 'Não houve transferência';
                    }

                    if(info.PRACA == true) {
                        var status_praca = 'Gerado com sucesso';
                    } else {
                        var status_praca = 'Não houve transferência';
                    }

                    if(info.SUMIDOURO== true) {
                        var status_sumidouro = 'Gerado com sucesso';
                    } else {
                        var status_sumidouro = 'Não houve transferência';
                    }

                    if(info.DISTRIBUIDORA == true) {
                        var status_distribuidora = 'Gerado com sucesso';
                    } else {
                        var status_distribuidora = 'Não houve transferência';
                    }

                    $('.footer').html('<div id="modal_txt" class="modal"><div class="modal-content"><div class="header_modal"><i class="material-icons" style="font-size:120px;color:#f8bb86;">filter_none</i></div><div  class="content_modal"><b style="font-size:24px">Arquivos gerados com sucesso</b><br/><div>Confira a situação dos arquivos gerados</div><div class="status"><div>Bazar Opção - Utilidades - <span class="status_utilidades">'+status_utilidades+'</span></div><div>Bazar Opção - Papelaria - <span class="status_papelaria">'+status_papelaria+'</span></div><div>Bazar Opção - Praca - <span class="status_praca">'+status_praca+'</span></div><div>Bazar Opção - Sumidouro - <span class="status_sumidouro">'+status_sumidouro+'</span></div><div>Bazar Opção - Distribuidora - <span class="status_distribuidora">'+status_distribuidora+'</span></div></div><div class="legenda">Os arquivos foram salvos na pasta Importados separadamente por Lojas.</div></div><div class="footer_modal_save_erro"><button class="btn waves-effect waves-light blue lighten-1" onClick="fechar_modal()">FECHAR NOTIFICAÇÃO</button></div></div>');
                    $('.modal').modal();
                    $('#modal_txt').modal({
                        onCloseEnd () {
                            window.onbeforeunload = function() {}
                            window.location.replace('/consulta_nota.html')
                        }
                    });

                    $('#modal_txt').modal('open');
                    
                    if(info.UTILIDADES == true) {
                        $('.status_utilidades').css({'color':'#26a69a'});
                    } else {
                        $('.status_utilidades').css({'color':'#E57373'});
                    }

                    if(info.PAPELARIA == true) {
                        $('.status_papelaria').css({'color':'#26a69a'});
                    } else {
                        $('.status_papelaria').css({'color':'#E57373'});
                    }

                    if(info.PRACA == true) {
                        $('.status_praca').css({'color':'#26a69a'});
                    } else {
                        $('.status_praca').css({'color':'#E57373'});
                    }

                    if(info.SUMIDOURO == true) {
                        $('.status_sumidouro').css({'color':'#26a69a'});
                    } else {
                        $('.status_sumidouro').css({'color':'#E57373'});
                    }

                    if(info.DISTRIBUIDORA == true) {
                        $('.status_distribuidora').css({'color':'#26a69a'});
                    } else {
                        $('.status_distribuidora').css({'color':'#E57373'});
                    }
                }
            }
        )
    }
}

function salvar() {
    salvo = true;
    
    if($('.active_edit').length > 0) {
        $('.active_edit').each( (index, value) => {
            $('#'+value.getAttribute('data')).addClass('btn-floating pulse red lighten-2')
            $('#'+value.getAttribute('id')).css({'position':'absolute'}).addClass('ean_pendente')
        });

    } else {
        gerar_array(false);
    
        if(jQuery.isEmptyObject(utilidades) == true && jQuery.isEmptyObject(papelaria) == true && jQuery.isEmptyObject(praca) == true && jQuery.isEmptyObject(sumidouro) == true && jQuery.isEmptyObject(distribuidora) == true) {
            $('.footer').html('<div id="modal_save_erro" class="modal"><div class="modal-content"><div class="header_modal"><i class="material-icons" style="font-size:120px;color:#f8bb86;">error_outline</i></div><div  class="content_modal"><b>Erro ao salvar transferência</b><br/><div style="text-align:justify;"> Nenhum produto teve quantidade inserida, preencha pelo menos um produto para salvar a transferência</div></div><div class="footer_modal_save_erro"><button class="btn waves-effect waves-light blue lighten-1" onClick="fechar_modal()">FECHAR NOTIFICAÇÃO</button></div></div>');
            $('.modal').modal();
            $('#modal_save_erro').modal('open');
            $('input[data="transferencia"]').addClass('invalid');
        } else {
            gerar_array(true);

            $.post('../src/components/salvar_informacoes.php',{
                nfe           : nfe_update,
                utilidades    : utilidades,
                papelaria     : papelaria,
                praca         : praca,
                sumidouro      : sumidouro,
                distribuidora : distribuidora
                }, (save) => {
                    var info_save = jQuery.parseJSON(save);

                    if(info_save.utilidades == true || info_save.papelaria == true || info_save.praca == true || info_save.sumidouro == true || info_save.distribuidora == true) {
                        $('.footer').html('<div id="modal_save" class="modal"><div class="modal-content"><div class="header_modal"><i class="material-icons" style="font-size:120px;color:#60bdb4;">assignment_turned_in</i></div><div  class="content_modal">Informações salvas com sucesso</div><div class="footer_modal_save"><button class="btn waves-effect waves-light blue lighten-1" onClick="fechar_modal()">FECHAR NOTIFICAÇÃO</button></div></div>');
                        $('.modal').modal();
                        $('#modal_save').modal({
                            onCloseEnd () {
                                window.onbeforeunload = function() {};
                                window.location.reload();
                            }
                        });
                        $('#modal_save').modal('open');
                    }
                }   
            )
        }
    }
}

function gerar_array(confirmacao) {
    utilidades = [];
    papelaria = [];
    praca = [];
    sumidouro = [];
    distribuidora  = [];

    $.each(qtd_produto, (i, v) => {
        if(confirmacao == false) {
            utilidades[i] = {
                NFE        : nfe_update,
                FORNECEDOR : fornecedor_update,
                ID         : $('div[data-item="'+(i+1)+'"], tr[data-item="'+(i+1)+'"]').attr('data-id'),
                EAN        : $('#ean_'+(i+1)+'').attr('value'),
                DESC       : $('#description_'+(i+1)+'').attr('value'),
                QTD        : $('#utilidades_'+(i+1)+'').val() ? $('#utilidades_'+(i+1)+'').val() : '0'
            }
            papelaria[i] = {
                NFE        : nfe_update,
                FORNECEDOR : fornecedor_update,
                ID         : $('div[data-item="'+(i+1)+'"], tr[data-item="'+(i+1)+'"]').attr('data-id'),
                EAN        : $('#ean_'+(i+1)+'').attr('value'),
                DESC       : $('#description_'+(i+1)+'').attr('value'),
                QTD        : $('#papelaria_'+(i+1)+'').val() ? $('#papelaria_'+(i+1)+'').val() : '0'
            }
            praca[i] = {
                NFE        : nfe_update,
                FORNECEDOR : fornecedor_update,
                ID         : $('div[data-item="'+(i+1)+'"], tr[data-item="'+(i+1)+'"]').attr('data-id'),
                EAN        : $('#ean_'+(i+1)+'').attr('value'),
                DESC       : $('#description_'+(i+1)+'').attr('value'),
                QTD        : $('#praca_'+(i+1)+'').val() ? $('#praca_'+(i+1)+'').val() : '0'
            }
            sumidouro[i] = {
                NFE        : nfe_update,
                FORNECEDOR : fornecedor_update,
                ID         : $('div[data-item="'+(i+1)+'"], tr[data-item="'+(i+1)+'"]').attr('data-id'),
                EAN        : $('#ean_'+(i+1)+'').attr('value'),
                DESC       : $('#description_'+(i+1)+'').attr('value'),
                QTD        : $('#sumidouro_'+(i+1)+'').val() ? $('#sumidouro_'+(i+1)+'').val() : '0'
            }
            distribuidora[i] = {
                NFE        : nfe_update,
                FORNECEDOR : fornecedor_update,
                ID         : $('div[data-item="'+(i+1)+'"], tr[data-item="'+(i+1)+'"]').attr('data-id'),
                EAN        : $('#ean_'+(i+1)+'').attr('value'),
                DESC       : $('#description_'+(i+1)+'').attr('value'),
                QTD        : $('#distribuidora_'+(i+1)+'').val() ? $('#distribuidora_'+(i+1)+'').val() : '0'
            }
        } else {
            utilidades[i] = {
                NFE        : nfe_update,
                FORNECEDOR : fornecedor_update,
                ID         : $('div[data-item="'+(i+1)+'"], tr[data-item="'+(i+1)+'"]').attr('data-id'),
                EAN        : $('#ean_'+(i+1)+'').attr('value'),
                DESC       : $('#description_'+(i+1)+'').attr('value'),
                QTD        : $('#utilidades_'+(i+1)+'').val() 
            }
            papelaria[i] = {
                NFE        : nfe_update,
                FORNECEDOR : fornecedor_update,
                ID         : $('div[data-item="'+(i+1)+'"], tr[data-item="'+(i+1)+'"]').attr('data-id'),
                EAN        : $('#ean_'+(i+1)+'').attr('value'),
                DESC       : $('#description_'+(i+1)+'').attr('value'),
                QTD        : $('#papelaria_'+(i+1)+'').val() 
            }
            praca[i] = {
                NFE        : nfe_update,
                FORNECEDOR : fornecedor_update,
                ID         : $('div[data-item="'+(i+1)+'"], tr[data-item="'+(i+1)+'"]').attr('data-id'),
                EAN        : $('#ean_'+(i+1)+'').attr('value'),
                DESC       : $('#description_'+(i+1)+'').attr('value'),
                QTD        : $('#praca_'+(i+1)+'').val()
            }
            sumidouro[i] = {
                NFE        : nfe_update,
                FORNECEDOR : fornecedor_update,
                ID         : $('div[data-item="'+(i+1)+'"], tr[data-item="'+(i+1)+'"]').attr('data-id'),
                EAN        : $('#ean_'+(i+1)+'').attr('value'),
                DESC       : $('#description_'+(i+1)+'').attr('value'),
                QTD        : $('#sumidouro_'+(i+1)+'').val() 
            }
            distribuidora[i] = {
                NFE        : nfe_update,
                FORNECEDOR : fornecedor_update,
                ID         : $('div[data-item="'+(i+1)+'"], tr[data-item="'+(i+1)+'"]').attr('data-id'),
                EAN        : $('#ean_'+(i+1)+'').attr('value'),
                DESC       : $('#description_'+(i+1)+'').attr('value'),
                QTD        : $('#distribuidora_'+(i+1)+'').val()
            }
        }
    });
}

function fechar_modal () {
    $('#modal_save').modal('close');
    $('#modal_save_erro').modal('close');
    $('#modal_gerar').modal('close');
    $('#modal_txt').modal('close');
    $('#modal_sup').modal('close');
}

//Importar pedido do Sistema, em construção
// function importar_pedido () {
//     $('.footer').html('<div id="modal_input" class="modal"> <div class="content_input"> <div class="container_input" style="width: 450px;"> <form action="" id="file_input" enctype="multipart/form-data"> <div class="file-field input-field"> <div class="btn"> <span>Importar</span> <input type="file" name="file" id="file" accept="application/vnd.ms-excel"> </div> <div class="file-path-wrapper"> <input class="file-path validate helper-input" type="text" placeholder="Selecione um pedido para importação"> <span class="helper-text"></span> </div> </div> </form> </div> </div></div>');

//     $('.modal').modal();

//     $('#modal_input').modal({
//         onCloseEnd () {
//             $('#file_input').trigger("reset");
//         }
//     });

//     $('#modal_input').modal('open');

//     $("#file").on('change', function () {
//         if($('#file').val() !== '') {
//             let file_size = $('#file')[0].files[0].size;
           
//             if (file_size < 10485760) {
//                 $('.file-path').addClass('valid').removeClass('invalid');
//                 $('.helper-text').attr('data-error', '')

//                 var file_name = $('input[type=file]').val().split('\\').pop();
//                 var file_type = file_name.split('.').pop().toLowerCase();

//                 if(jQuery.inArray(file_type, ['xls']) === 0) {
//                     $('.file-path').addClass('valid').removeClass('invalid');
//                     $('.helper-text').attr('data-error', '');

//                     var formData = new FormData($('#file_input')[0]);

//                     $.ajax({
//                         url: '../src/components/import_qtd.php',
//                         type: 'POST',
//                         data: formData,
//                         success: function (data) {
//                             let info = jQuery.parseJSON(data);
                            
//                             if(info.code_erro == '103') {
//                                 $('.file-path').removeClass('valid').addClass('invalid');
//                                 $('.helper-text').attr('data-error', 'Arquivo inválido para importação');
//                             } else if(info.code_erro == '100') {
//                                 $('.file-path').removeClass('valid').addClass('invalid');
//                                 $('.helper-text').attr('data-error', 'Arquivo maior do que permitido de 10MB');
//                             } else if (info.code_erro == '101') {
//                                 $('.file-path').removeClass('valid').addClass('invalid');
//                                 $('.helper-text').attr('data-error', 'Arquivo inválido, permitido apenas arquivos com extensão .xls');
//                             }

//                             $.each(info, (ix, vl) => {
//                                 $.each(vl.utilidades, (i1, v1) => {
//                                     $('#utilidades_'+i1).attr('value', v1);
//                                 })

//                                 $.each(vl.papelaria, (i1, v1) => {
//                                     $('#papelaria_'+i1).attr('value', v1);
//                                 })

//                                 $.each(vl.praca, (i1, v1) => {
//                                     $('#praca_'+i1).attr('value', v1);
//                                 })

//                                 $.each(vl.sumidouro, (i1, v1) => {
//                                     $('#sumidouro_'+i1).attr('value', v1);
//                                 })

//                                 $.each(vl.distribuidora, (i1, v1) => {
//                                     $('#distribuidora_'+i1).attr('value', v1);
//                                 })

//                                 let total = $('#total_'+(i+1)+'');
//                                 var qtd_nota = parseFloat($('#qtd_'+(i+1)+'').text());
//                                 let db_utilidades = parseFloat(v.utilidades ? v.utilidades : 0);
//                                 let db_papelaria = parseFloat(v.papelaria ? v.papelaria : 0);
//                                 let db_praca = parseFloat(v.praca ? v.praca : 0);
//                                 let db_sumidouro = parseFloat(v.sumidouro ? v.sumidouro : 0);
//                                 let db_distribuidora = parseFloat(v.distribuidora ? v.distribuidora : 0);

//                                 total_soma = (db_utilidades+db_papelaria+db_praca+db_sumidouro+db_distribuidora);

//                                 if(total_soma <= qtd_nota) {
//                                     total.addClass('menor_qtd');
//                                     total.removeClass('maior_qtd');
//                                 } else if (total_soma > qtd_nota) {
//                                     total.removeClass('menor_qtd');
//                                     total.addClass('maior_qtd');
//                                 }
//                                 if(total_soma == 0) {
//                                     total.attr('value', '')
//                                 } else {
//                                     total.attr('value', total_soma)
//                                 }
//                             })
//                         },
//                         cache: false,
//                         contentType: false,
//                         processData: false,
//                         xhr: function() {  
//                             var myXhr = $.ajaxSettings.xhr();
//                             if (myXhr.upload) { 
//                                 myXhr.upload.addEventListener('progress', function () {
//                                     //Construção load
//                                 }, false);
//                             }
//                         return myXhr;
//                         }
//                     });
//                 } else {
//                     $('.file-path').removeClass('valid').addClass('invalid');
//                     $('.helper-text').attr('data-error', 'Arquivo inválido, permitido apenas arquivos com extensão .xls')
//                 }
//             } else {
//                 console.log(file_size)
//                 $('.helper-text').attr('data-error', 'Arquivo maior do que permitido de 10MB')
//                 $('.file-path').removeClass('valid').addClass('invalid');
//             }
//         }
//     });
// }