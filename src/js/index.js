$(function () {
    $('.modal').modal();
    $('.tabs').tabs();

    $('.header').html('<a class="waves-effect waves-light btn-salvar-informacoes btn btn-pagina-transferencias"><i class="material-icons right">shopping_cart</i>IR PARA TRANSFERÊNCIAS</a><a class="waves-effect waves-light btn-salvar-informacoes btn red lighten-2"  onClick="window.location.reload()"><i class="material-icons right">refresh</i>ATUALIZAR</a>');
    $('.btn-pagina-transferencias').attr('onClick', 'window.location.assign("/consulta_nota.html")');

    $.post('../src/components/import_xls.php',
        (data) => {
            var info = JSON.parse(data);
            
            $('.load').remove();
            $('.nenhuma_importada').show();
            $('.nenhuma_duplicadas').show();
            $('.nenhuma_rejeitados').show();

            if(info.duplicidade !== undefined) {

                var notas_duplicadas = 0;

                $.each(info.duplicidade, (id, vd) => {

                    notas_duplicadas++;
            
                    $('.nenhuma_duplicadas').remove();
                    $('#notas_duplicadas').append('<div class="notas_duplicadas"> <span class="loja_duplicadas">'+vd.loja+'</span> <div class="info col s12"> <div class="row"> <div class="input-field col s2"> <input id="nfe_'+id+'" type="text" class="validate" value="'+vd.nfe+'" disabled> <label for="nfe_'+id+'">NF-e/Série </label> </div> <div class="input-field col s2"> <input id="entrada_'+id+'" type="text" class="validate" value="'+vd.data+'" disabled> <label for="entrada_'+id+'">Entrada</label> </div> <div class="input-field col s7"> <input id="fornecedor_'+id+'" type="text" class="validate" value="'+vd.fornecedor+'" disabled> <label for="fornecedor_'+id+'">Fornecedor</label> </div> </div> </div>')
                    $('label[for="nfe_'+id+'"], label[for="entrada_'+id+'"], label[for="fornecedor_'+id+'"] ').addClass('active');

                });
                
            }
            
            if(info.importados !== undefined) {

                var notas_importadas = 0;

                $.each(info.importados, (ii, vi) => {

                    notas_importadas++;

                    $('.nenhuma_importada').remove();
                    $('#notas_importadas').append('<div class="notas_importadas"> <span class="loja_importada">'+vi.loja+'</span> <div class="info col s12"> <div class="row"> <div class="input-field col s2"> <input id="nfe_'+ii+'" type="text" class="validate" value="'+vi.nfe+'" disabled> <label for="nfe_'+ii+'">NF-e/Série </label> </div> <div class="input-field col s2"> <input id="entrada_'+ii+'" type="text" class="validate" value="'+vi.data+'" disabled> <label for="entrada_'+ii+'">Entrada</label> </div> <div class="input-field col s7"> <input id="fornecedor_'+ii+'" type="text" class="validate" value="'+vi.fornecedor+'" disabled> <label for="fornecedor_'+ii+'">Fornecedor</label> </div> </div> </div>')
                    $('label[for="nfe_'+ii+'"], label[for="entrada_'+ii+'"], label[for="fornecedor_'+ii+'"] ').addClass('active');
                });
            }
            
            if(info.rejeitados !== undefined) {

                var arquivos_rejeitados = 0;

                $.each(info.rejeitados, (ir, vr) => {

                    arquivos_rejeitados++;

                    $('.nenhuma_rejeitados').remove();
                    $('#arquivos_rejeitados').append('<div class="arquivos_rejeitados"> <span class="loja_rejeitados">Arquivo movido para pasta de Rejeitados</span> <div class="info col s12"> <div class="row rejeitados-content"> <div class="input-field col s4"> <input id="arquivo_'+ir+'" type="text" class="validate" value="'+vr.arquivo+'" disabled> <label for="arquivo_'+ir+'">Arquivo</label> </div> <div class="input-field col s8"> <input id="motivo_'+ir+'" type="text" class="validate" value="'+vr.motivo+'" disabled> <label for="motivo_'+ir+'">Motivo</label> </div> </div> </div> </div>')
                    $('label[for="arquivo_'+ir+'"], label[for="entrada_"], label[for="motivo_'+ir+'"] ').addClass('active');

                });
            } 

            if(notas_importadas <= 0 || notas_importadas == undefined) {
                $('.bg_notify_importados').remove();
            } else {
                $('.bg_notify_importados').show();
                $('.text_notify_importados').text(notas_importadas);
            }
            if(notas_duplicadas <= 0 || notas_duplicadas == undefined) {
                $('.bg_notify_duplicadas').remove();
            } else {
                $('.bg_notify_duplicadas').show();
                $('.text_notify_duplicadas').text(notas_duplicadas);
            }
            if(arquivos_rejeitados <= 0 || arquivos_rejeitados == undefined) {
                $('.bg_notify_rejeitados').remove();
            } else {
                $('.bg_notify_rejeitados').show();
                $('.text_notify_rejeitados').text(arquivos_rejeitados);
            }
            
        }
    );

    $('.btn-importadas').on('click', () =>{ 
        $('.indicator').removeClass('indicator_duplicadas');
        $('.indicator').removeClass('indicator_rejeitados');
    });

    $('.btn-duplicadas').on('click', () =>{ 
        $('.indicator').addClass('indicator_duplicadas');
        $('.indicator').removeClass('indicator_rejeitados');
    });


    $('.btn-rejeitados').on('click', () =>{ 
        $('.indicator').addClass('indicator_rejeitados');
        $('.indicator').removeClass('indicator_duplicadas');
    });

});
