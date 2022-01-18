<?php
    date_default_timezone_set('America/Sao_Paulo');
    $data = date("d-m-Y H:i:s", time());
    $data_mesclado = date("Y-m-d H:i:s", time());
    ini_set('memory_limit', '-1');

    header('Content-Type: text/html; charset=utf-8');

    $path_sql = '../database/database.db';
    $db = new PDO("sqlite:".$path_sql."");
    
    $arquivos = array(
        'utilidades' => array (
            'Junho de 2021' => array (
                '../../Transfêrencias/(13066) Bazar Opção - Utilidades/Junho de 2021/637073-2 & 392 - A.W. FABER CASTELL S.A. - Copia (2).txt',
                '../../Transfêrencias/(13066) Bazar Opção - Utilidades/Junho de 2021/637073-2 & 392 - A.W. FABER CASTELL S.A. - Copia.txt',
                '../../Transfêrencias/(13066) Bazar Opção - Utilidades/Junho de 2021/637073-2 & 392 - A.W. FABER CASTELL S.A..txt',
            )
        ),
        'papelaria' => array (
            'Junho de 2021' => array (
                '../../Transfêrencias/(13067) Bazar Opção - Papelaria/Junho de 2021/79020-55 & 609 - NOVABRINK INDUSTRIA DE PLASTICOS LTDA - Copia (2).txt',
                '../../Transfêrencias/(13067) Bazar Opção - Papelaria/Junho de 2021/79020-55 & 609 - NOVABRINK INDUSTRIA DE PLASTICOS LTDA - Copia.txt',
                '../../Transfêrencias/(13067) Bazar Opção - Papelaria/Junho de 2021/79020-55 & 609 - NOVABRINK INDUSTRIA DE PLASTICOS LTDA.txt',
            )
        )
    ); 

    $sql_consulta = "SELECT * FROM MESCLAR";
    $consulta_id = $db->query($sql_consulta);
    $fetch_consulta_id = $consulta_id->fetchAll(PDO::FETCH_ASSOC);
    foreach($fetch_consulta_id as $value_id) {
        $id = $value_id['ID_MESCLAGEM'];
    }

    $id ? $id : 0;
    $id = ($id+1);

    foreach ($arquivos as $values_lojas) {

        foreach($values_lojas as $values_meses) {

            foreach($values_meses as $key => $values_path) {
                
                $array_path =  explode('/', $values_path);
    
                $loja_arquivo = $array_path[3];
                $data_arquivo = $array_path[4];
                $nome_arquivo = substr($array_path[5],0,-4);
    
                $sql_insert = "INSERT INTO MESCLAR (ID_MESCLAGEM, LOJA_ARQUIVO, DATA_ARQUIVO, NOME_ARQUIVO, PATH_ARQUIVO, DATA_MESCLADO) VALUES ('$id', '$loja_arquivo', '$data_arquivo', '$nome_arquivo', '$values_path', '$data_mesclado')";
                $results_insert = $db->prepare($sql_insert)->execute();
    
                $path = '../../Transfêrencias/'.$loja_arquivo.'/'.$data_arquivo;
                $nome_mesclagem = 'ID '.$id.' - '.substr($loja_arquivo,0,7).' - Unificado em '.substr($data,0,10).'.txt';
                $mesclagem_path = $path.'/'.$nome_mesclagem;
    
                if(file_exists($mesclagem_path)) {
                    unlink($mesclagem_path);
                } 
        
                $fp_utilidades = fopen($mesclagem_path, "a");
        
                $txt = file($values_path);
                foreach($txt as $content) {
                    $escreve_utilidades = fwrite($fp_utilidades, $content);
                }
    
                $efetuados_path = $path.'/Efetuados';
    
                if(!file_exists($efetuados_path)) {
                    mkdir($efetuados_path,0777,true);
                }
    
                rename($values_path, $efetuados_path."/".$nome_arquivo.' - ID '.$id.'.txt');
    
                $retorno[$loja_arquivo]['id'] = $id;
                $retorno[$loja_arquivo]['arquivo'] = $nome_mesclagem;
                $retorno[$loja_arquivo]['caminho'] = $mesclagem_path;
                $retorno[$loja_arquivo]['data_mesclado'] = $data_mesclado;
                $retorno[$loja_arquivo]['arquivos_mesclado'][$key] = $values_path;
    
            }
        }
    }

    echo json_encode($retorno, true);

?>