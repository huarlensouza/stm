<?php 

    ini_set('max_input_vars', 3000);
    
    $path_sql = '../database/database.db';
    $db = new PDO("sqlite:".$path_sql."");

    $post_nfe = isset($_POST['nfe']) ? $_POST['nfe'] : '';
    
    $sql_config = "SELECT * FROM CONFIG";
    $consulta_config = $db->query($sql_config);
    $results_config = $consulta_config->fetchAll(PDO::FETCH_ASSOC);
    foreach($results_config as $key_config => $value_config) {
        $config = $value_config['LAYOUT_TRANSFERIR'];
    }

    if($post_nfe !== '') {
        $sql_individual = "SELECT * FROM NOTAS WHERE NFE = '$post_nfe'";
        $nota_individual = $db->query($sql_individual);
        $result_individual = $nota_individual->fetchAll(PDO::FETCH_ASSOC);

        if(count($result_individual) == 0) {

            $retorno['erro'] = 'Nenhuma nota fiscal localizada no Banco de Dados'; 

        } else {

            foreach ($result_individual as $key => $value) {
                $retorno[$key]['layout_transferir'] = $config;
                $retorno[$key]['loja'] = $value['LOJA'];
                $retorno[$key]['nfe'] = $value['NFE'];
                $retorno[$key]['fornecedor'] = $value['FORNECEDOR'];
                $retorno[$key]['data_entrada'] = $value['DATA_ENTRADA'];
                $retorno[$key]['edicao'] = $value['EDICAO'];
                $retorno[$key]['data_edicao'] = $value['DATA_EDICAO'];
                $retorno[$key]['transferido'] = $value['TRANSFERIDO'];
                $retorno[$key]['data_transferido'] = $value['DATA_TRANSFERIDO'];
            }
        }

    } else {

        $consulta_notas = $db->query("SELECT * FROM NOTAS");
        $result_notas = $consulta_notas->fetchAll(PDO::FETCH_ASSOC);

        if(count($result_notas) == 0) {

            $retorno['erro'] = 'Nenhuma nota fiscal localizada no Banco de Dados'; 

        } else {

            foreach ($result_notas as $key => $value) {
                $retorno[$key]['layout_transferir'] = $config;
                $retorno[$key]['loja'] = $value['LOJA'];
                $retorno[$key]['loja'] = $value['LOJA'];
                $retorno[$key]['nfe'] = $value['NFE'];
                $retorno[$key]['fornecedor'] = $value['FORNECEDOR'];
                $retorno[$key]['data_entrada'] = $value['DATA_ENTRADA'];
                $retorno[$key]['edicao'] = $value['EDICAO'];
                $retorno[$key]['data_edicao'] = $value['DATA_EDICAO'];
                $retorno[$key]['transferido'] = $value['TRANSFERIDO'];
                $retorno[$key]['data_transferido'] = $value['DATA_TRANSFERIDO'];
            }
        }
    }

    echo json_encode($retorno, true);

?>