$(() => {
    $('.modal').modal();
    $('.tabs').tabs();

    $.post('../src/components/consulta_nota.php',
    (data) => {
        var info = JSON.parse(data);
        
        $('.load').remove();

        $('.nenhuma_pendente').show();
        $('.nenhuma_transferida').show();

        if(info.erro !== undefined) {
            $('.header').html('<div clas="acoes" style="width:740px"   ><a id="btn-voltar" class="waves-effect waves-light blue lighten-1 btn"><i class="material-icons right">arrow_back</i>MENU DAS NOTAS</a></div>');
            $('#btn-voltar').on('click', () => { window.location.replace('/') });
        } else {

            var notas_pendentes = 0;
            var notas_transferidas = 0;

            $.each(info, (i, v) => {
                if(v.transferido == 0) {

                    notas_pendentes++;

                    var del_nfe = v.nfe;
                    del_serie = del_nfe.substring(del_nfe.indexOf('/')+1)
                    del_nfe = del_nfe.slice(0,del_nfe.indexOf('/'));

                    $('.nenhuma_pendente').remove();
                    $('#notas_pendentes').append('<div data-nfe="'+v.nfe+'" class="content"><div class="info_nota"><div class="cabecalho_nota"><span class="loja_pendente">'+v.loja+'</span></div> <div class="info col s12"> <div class="row"> <div class="input-field col s2"> <input id="nfe_'+i+'" type="text" class="validate" value="'+v.nfe+'" disabled> <label for="nfe_'+i+'">NF-e/Série </label> </div> <div class="input-field col s2"> <input id="entrada_'+i+'" type="text" class="validate" value="'+v.data_entrada+'" disabled> <label for="entrada_'+i+'">Entrada</label> </div> <div class="input-field col s7"> <input id="fornecedor_'+i+'" type="text" class="validate" value="'+v.fornecedor+'" disabled> <label for="fornecedor_'+i+'">Fornecedor</label> </div> </div> </div></div><div class="acoes_nota"><a class="waves-effect waves-light btn " href="/transferir.html?nfe='+v.nfe+'"><i class="material-icons right">shopping_cart</i>TRANSFERIR</a><a onClick="deletar('+del_nfe+','+del_serie+')" class="waves-effect waves-light btn red lighten-2"><i class="material-icons right">delete_forever</i>APAGAR</a></div>')
                    $('label[for="nfe_'+i+'"], label[for="entrada_'+i+'"], label[for="fornecedor_'+i+'"] ').addClass('active');

                    if(v.edicao == 1) {
                        $('div[data-nfe="'+v.nfe+'"] > .info_nota > .cabecalho_nota').after('<div class="edicao tooltipped" data-position="top" data-tooltip="Data da última edição: '+v.data_edicao+'">EM EDIÇÃO</div>');
                        $('.tooltipped').tooltip();
                    }

                }  else if(v.transferido == 1) {

                    notas_transferidas++;

                    $('.nenhuma_transferida').remove();
                    $('#notas_transferidas').append('<div data-nfe="'+v.nfe+'" class="content"><div class="notas_transferidas"> <span class="loja_transferida">'+v.loja+'</span> <div class="info col s12"> <div class="row"> <div class="input-field col s2"> <input id="nfe_'+i+'" type="text" class="validate" value="'+v.nfe+'" disabled> <label for="nfe_'+i+'">NF-e/Série </label> </div> <div class="input-field col s2"> <input id="entrada_'+i+'" type="text" class="validate" value="'+v.data_entrada+'" disabled> <label for="entrada_'+i+'">Entrada</label> </div> <div class="input-field col s7"> <input id="fornecedor_'+i+'" type="text" class="validate" value="'+v.fornecedor+'" disabled> <label for="fornecedor_'+i+'">Fornecedor</label> </div> </div> </div></div>')
                    $('label[for="nfe_'+i+'"], label[for="entrada_'+i+'"], label[for="fornecedor_'+i+'"] ').addClass('active');

                }
            });
            if(notas_pendentes <= 0) {
                $('.bg_notify_pendentes').remove();
            } else {
                $('.bg_notify_pendentes').show();
                $('.text_notify_pendentes').text(notas_pendentes);
            }
            if(notas_transferidas <= 0) {
                $('.bg_notify_transferidas').remove();
            } else {
                $('.bg_notify_transferidas').show();
                $('.text_notify_transferidas').text(notas_transferidas);
            }

            $('.header').html('<div clas="acoes" style="width:740px"   ><a id="btn-voltar" class="waves-effect waves-light blue lighten-1 btn"><i class="material-icons right">arrow_back</i>MENU DAS NOTAS</a></div>');
            $('#btn-voltar').on('click', () => { window.location.replace('/') });
        }
    });
    
    $('.btn-ignoradas').on('click', () =>{ 
        $('.indicator').addClass('indicator_transferida');
    });

    $('.btn-importadas').on('click', () =>{ 
        $('.indicator').removeClass('indicator_transferida');
    });
});

function deletar (nfe, serie) {
    $('.footer').html('<div id="modal_deletar" class="modal"><div class="modal-content deletar"><div class="header_modal"><i class="material-icons" style="font-size:120px;color:#f8bb86;">error_outline</i></div><div  class="content_modal">Você está preste a excluir uma Nota Fiscal <br/>Deseja realmente excluir?</div><div class="footer_modal_deletar"><button class="btn waves-effect waves-light btn red lighten-2" onClick="deletar_confirmacao('+nfe+','+serie+')">SIM, EXCLUIR</button><button class="btn waves-effect waves-light btn-nao" onClick="fechar_modal()">NÃO</button></div></div>');
    $('.modal').modal();
    $('#modal_deletar').modal('open');
}

function fechar_modal(){
    $('#modal_deletar').modal('close');
}

function deletar_confirmacao (nfe, serie) {
    var juncao = nfe+'/'+serie;
    $.post('../src/components/deletar_nota.php', {
        nota : juncao
        }, (data) => {
            var info = JSON.parse(data);
            if(info.deletado == true) {
                window.location.reload();
            } else {
                $('.footer').html('<div id="modal_erro" class="modal"><div class="modal-content erro"><div class="header_modal"><h6><b>Não foi possível apagar nota fiscal</b></h6></div><div  class="content_modal">Ocorreu um erro, entre em contato com administrador para verificar ocorrido!</div><div class="footer_modal"><button class="btn waves-effect waves-light" onclick="window.location.reload();">ATUALIZAR PÁGINA</button></div></div>');
                $('.modal').modal();
                $('#modal_erro').modal('open')
            }
        }
    );
}
