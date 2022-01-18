<?php
    date_default_timezone_set('America/Sao_Paulo');
    header('Content-Type: text/html; charset=utf-8');
    $data_importacao = date("Y-m-d H:i:s", time());

    $arquivos = glob("../../Notas/*.xls");

    if(count($arquivos) == 0) {
        $retorno['erro'] = 'Nenhuma nota fiscal foi localizada.';
        echo json_encode($retorno, true);
        die();
    }

    include 'excel_reader.php';

    $meses = array(
        "Janeiro",
        "Fevereiro",
        "Março",
        "Abril",
        "Maio",
        "Junho",
        "Julho",
        "Agosto",
        "Setembro",
        "Outubro",
        "Novembro",
        "Dezembro"
    );

    $path_sql = '../database/database.db';
    $db = new PDO("sqlite:".$path_sql."");

    $index = 0;
    while($index < count($arquivos)) {

        if(filesize($arquivos[$index]) < 2097152){

            $excel = new PhpExcelReader;
            $excel->read($arquivos[$index]);

            $tipo = utf8_encode($excel->sst[0]);

            if($tipo == 'Relação de Entrada de Notas') {
               
                $linhas = array_slice($excel->sheets[0]['cells'], 8);
            
                $cabecalho = array_slice($excel->sst, -4, 2);
        
                $endereço_length = strpos($cabecalho[1], " | N");
                $endereco = substr($cabecalho[1], 0, $endereço_length);
                
                if($endereco == '1 AV ALBERTO BRAUNE') {
                    $loja = '(13066) Bazar Opção - Utilidades';
                }
                if($endereco == '2 AV ALBERTO BRAUNE') {
                    $loja = '(13067) Bazar Opção - Papelaria';
                }
                if($endereco == 'PRC PRESIDENTE GETULIO VARGAS') {
                    $loja = '(13068) Bazar Opção - Praça';
                }
                if($endereco == 'RUA 10 DE JUNHO') {
                    $loja = '(13069) Bazar Opção - Sumidouro';
                }
                if($endereco == 'RUA DELEMONT') {
                    $loja = '(13489) Bazar Opção - Distribuidora';
                }

                utf8_encode($loja);
                
                $numnfce_complete = $excel->sst[19];
                $numero_nota = str_replace(' ', '', substr($numnfce_complete, 17));

                $data_entrada = substr($excel->sst[21],17);
                $ano = substr($data_entrada,6);
                $mes = (ltrim(substr($data_entrada,3, 2), "0")-1);
        
                $excel_fornecedor = substr($excel->sst[18], 12);
                $excel_fornecedor = str_replace('.', ' ', $excel_fornecedor);
                $fornecedor = preg_replace("/[:*?<>\\/|\\\]/", " ", $excel_fornecedor);
        
                $sql_consulta = "SELECT * FROM NOTAS WHERE NFE = '$numero_nota' AND LOJA = '$loja' AND FORNECEDOR = '$fornecedor'";
                $consulta_notas = $db->query($sql_consulta);
                $fetch_consulta_nota = $consulta_notas->fetchAll(PDO::FETCH_ASSOC);
        
                if(count($fetch_consulta_nota) != 0){
                    $retorno['duplicidade'][$index]['loja'] = $loja;
                    $retorno['duplicidade'][$index]['nfe'] = $numero_nota;
                    $retorno['duplicidade'][$index]['data'] = $data_entrada;
                    $retorno['duplicidade'][$index]['fornecedor'] = $fornecedor;
                    $retorno['duplicidade'][$index]['duplicidade'] = true; 
                    $importar = false;
                
                    $path = '../../Importados/'.$loja.'/'.utf8_encode($meses[$mes]).' de '.$ano;
            
                    if(!file_exists($path)) {
                        mkdir($path,0777,true);
                    }
        
                    rename($arquivos[$index], $path."/".str_replace('/', '-', $numero_nota).' & '.$fornecedor.'.xls');
                    
                } else {
        
                    $importar = true;
                }
        
                if($importar == true) {
                    $sql = "INSERT INTO NOTAS (LOJA, NFE, DATA_ENTRADA, FORNECEDOR)
                    VALUES ('$loja','$numero_nota','$data_entrada','$fornecedor')";
                    $results = $db->prepare($sql)->execute();
                    
                    $sql_log = "INSERT INTO LOG_IMPORT (LOJA, NFE, DATA_ENTRADA, FORNECEDOR, DATA_IMPORTACAO)
                    VALUES ('$loja','$numero_nota','$data_entrada','$fornecedor', '$data_importacao')";
                    $results_log = $db->prepare($sql_log)->execute();
            
                    $index_array = 0;
                    while($index_array < count($linhas)) {
                        if(array_search('Total Bruto Nota:', $linhas[$index_array])) {
                            $slice =  $index_array;
                        }
                        $index_array++;
                    }
            
                    $array =  array_splice($linhas, 0, $slice);
            
                    $i = 1;
                    foreach ($array as $value) {
        
                        $ean = $value[1]; //EAN
                        $descricao= $value[2]; //Descricao
                        $qtd = $value[6]; //QTD
                        $cmv = $value[14]; //CMV da numnfce
                        $margem = $value[19]; //Margem
                        $venda = $value[21]; //Venda da Nota
        
                        $sql_insert_produtos = "INSERT INTO PRODUTOS (NFE, EAN, DESCRICAO, QTD, CMV, MARGEM, VENDA)
                        VALUES ('$numero_nota','$ean','$descricao','$qtd','$cmv','$margem','$venda')";
                        $results_insert_produtos = $db->prepare($sql_insert_produtos)->execute();
                    
                        $i++;
                    }
        
                    $path = '../../Importados/'.$loja.'/'.utf8_encode($meses[$mes]).' de '.$ano;
            
                    if(!file_exists($path)) {
                        mkdir($path,0777,true);
                    }
        
                    $retorno['importados'][$index]['loja'] = $loja;
                    $retorno['importados'][$index]['nfe'] = $numero_nota;
                    $retorno['importados'][$index]['data'] = $data_entrada;
                    $retorno['importados'][$index]['fornecedor'] = $fornecedor;
                    $retorno['importados'][$index]['duplicidade'] = false; 
                
                    rename($arquivos[$index], $path."/".str_replace('/', '-', $numero_nota).' & '.$fornecedor.'.xls');
                }
                
    
            } else {
                $retorno['rejeitados'][$index]['arquivo'] = basename($arquivos[$index]);
                $retorno['rejeitados'][$index]['motivo'] = 'Arquivo com formato inválido para importação';
                $path = '../../Rejeitados/';
                if(!file_exists($path)) {
                    mkdir($path,0777,true);
                }
                rename($arquivos[$index], $path."/".basename($arquivos[$index]).'.xls');
            }

        } else {

            $retorno['rejeitados'][$index]['arquivo'] = basename($arquivos[$index]);
            $retorno['rejeitados'][$index]['motivo'] = 'Arquivo ultrapassa o tamanho permitido de 2mb';
            $path = '../../Rejeitados/';
            if(!file_exists($path)) {
                mkdir($path,0777,true);
            }
            rename($arquivos[$index], $path."/".basename($arquivos[$index]).'.xls');
        }

        $index++;
    }

    echo json_encode($retorno, true);

?>