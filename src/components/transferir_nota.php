<?php 
    $post_nfe = $_POST['nfe'];
    
    $path_sql = '../database/database.db';

    $db = new PDO("sqlite:".$path_sql."");
    
    $sql_config = "SELECT * FROM CONFIG";
    $consulta_config = $db->query($sql_config);
    $results_config = $consulta_config->fetchAll(PDO::FETCH_ASSOC);

    $sql_consulta_produtos = "SELECT * FROM NOTAS AS NT
    INNER JOIN PRODUTOS AS PD
    ON NT.NFE = PD.NFE WHERE NT.NFE = '$post_nfe' AND NT.TRANSFERIDO = 0 ORDER BY PD.ID ASC";
    $consulta_produtos = $db->query($sql_consulta_produtos);
    $results_produtos = $consulta_produtos->fetchAll(PDO::FETCH_ASSOC);

    if(count($results_produtos) == 0) {

        $retorno['erro'] = 'Nenhuma nota fiscal localizada no Banco de Dados'; 

    } else {
        foreach($results_config as $key_config => $value_config) {
            $config = $value_config['LAYOUT_TRANSFERIR'];
        }

        foreach ($results_produtos as $key => $value) {
            $retorno[$key]['layout_transferir'] = $config;
            $retorno[$key]['nfe'] = $value['NFE'];
            $retorno[$key]['id'] = $value['ID'];
            $retorno[$key]['ean'] = $value['EAN'];
            $retorno[$key]['descricao'] = $value['DESCRICAO'];
            $retorno[$key]['qtd'] = $value['QTD'];
            $retorno[$key]['cmv'] = $value['CMV'];
            $retorno[$key]['margem'] = $value['MARGEM'];
            $retorno[$key]['venda'] = $value['VENDA'];

            $retorno[$key]['utilidades'] = $value['UTILIDADES'];
            $retorno[$key]['papelaria'] = $value['PAPELARIA'];
            $retorno[$key]['praca'] = $value['PRACA'];
            $retorno[$key]['sumidouro'] = $value['SUMIDOURO'];
            $retorno[$key]['distribuidora'] = $value['DISTRIBUIDORA'];

            $retorno[$key]['ean_alterado'] = $value['EAN_ALTERADO'];
        }
    }

    echo json_encode($retorno, true);

?>