<?php
    date_default_timezone_set('America/Sao_Paulo');
    $data = date("Y-m-d H:i:s", time());
    $quebra = chr(13).chr(10);
    ini_set('memory_limit', '-1');

    $path_sql = '../database/database.db';
    $db = new PDO("sqlite:".$path_sql."");

    $post_nfe = $_POST['nfe'];
    $post_id = $_POST['id'];
    $post_ean = $_POST['ean'];

    $sql_ean = "SELECT * FROM NOTAS AS NT
    INNER JOIN PRODUTOS AS PD
    ON NT.NFE = PD.NFE WHERE PD.NFE = '$post_nfe' AND PD.ID = '$post_id'";
    $query_ean = $db->query($sql_ean);
    $fetch_search_ean = $query_ean->fetchAll(PDO::FETCH_ASSOC);

    foreach($fetch_search_ean as $value) {
        $loja_atual = $value['LOJA'];
        $data_atual = $value['DATA_ENTRADA'];
        $fornecedor_atual = $value['FORNECEDOR'];
        $ean_atual = $value['EAN'];
        $desc_atual = $value['DESCRICAO'];
    }

    if($ean_atual != $post_ean) {
        $att_ean = "UPDATE PRODUTOS SET EAN = '$post_ean', EAN_ALTERADO = '1' WHERE NFE = '$post_nfe' AND ID = '$post_id'";
        $update_ean = $db->exec($att_ean); 

        $log_ean = "INSERT INTO LOG_EAN (LOJA, NFE, DATA_ENTRADA, FORNECEDOR, EAN_ANTIGO, DESCRICAO, EAN_NOVO, DATA_ALTERACAO) VALUES ('$loja_atual', '$post_nfe', '$data_atual', '$fornecedor_atual', '$ean_atual', '$desc_atual', '$post_ean', '$data')";
        $insert_log_ean = $db->exec($log_ean);

        $retorno['ean_alterado'] = true;
    }

    echo json_encode($retorno, true);
?>