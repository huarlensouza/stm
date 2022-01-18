<?php 

    date_default_timezone_set('America/Sao_Paulo');
    $data = date("Y-m-d H:i:s", time());

    $path_sql = '../database/database.db';

    $db = new PDO("sqlite:".$path_sql);

    $post_nfe = $_POST['nfe'];
    $post_utilidades = $_POST['utilidades'] ? $_POST['utilidades'] : false;
    $post_papelaria = $_POST['papelaria']  ? $_POST['papelaria'] : false;
    $post_praca = $_POST['praca'] ? $_POST['praca'] : false;
    $post_sumidouro = $_POST['sumidouro'] ? $_POST['sumidouro'] : false;
    $post_distribuidora = $_POST['distribuidora'] ? $_POST['distribuidora'] : false;

    if($post_utilidades !==  false) { 
        foreach ($post_utilidades as $key_utilidades => $value_utilidades){
            $id_utilidades = $value_utilidades['ID'];
            $ean_utilidades = $value_utilidades['EAN'];
            $qtd_utilidades = $value_utilidades['QTD'];
            $desc_utilidades = $value_utilidades['DESC'];

            if($qtd_utilidades != 0 && $qtd_utilidades != '') {
                $search_utilidades = "SELECT * FROM PRODUTOS WHERE ID = '$id_utilidades' AND EAN = '$ean_utilidades' AND NFE = '$post_nfe' AND UTILIDADES = '$qtd_utilidades'";
                $query_utilidades = $db->query($search_utilidades);
                $fetch_search_utilidades = $query_utilidades->fetchAll(PDO::FETCH_ASSOC);

                $search_inclusao_utilidades = "SELECT * FROM PRODUTOS WHERE ID = '$id_utilidades' AND EAN = '$ean_utilidades' AND NFE = '$post_nfe'";
                $query_inclusao_utilidades = $db->query($search_inclusao_utilidades);
                $fetch_search_inclusao_utilidades = $query_inclusao_utilidades->fetchAll(PDO::FETCH_ASSOC);

                foreach($fetch_search_inclusao_utilidades as $value_utilidades) {
                    $qtd_atual_utilidades = $value_utilidades['UTILIDADES'];
                }

                if(count($fetch_search_utilidades) == 0 && $qtd_atual_utilidades != NULL) {
                    $sql_log_utilidades = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, UTILIDADES, ALTERACAO, DATA_ALTERACAO) VALUES ('$id_utilidades','$post_nfe', '$ean_utilidades', '$desc_utilidades', '$qtd_utilidades', '1', '$data')";
                    $update_log_utilidades = $db->exec($sql_log_utilidades);
                }

                if($qtd_atual_utilidades == NULL) {
                    $sql_inclusao_utilidades = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, UTILIDADES, DATA_INCLUSAO) VALUES ('$id_utilidades','$post_nfe', '$ean_utilidades', '$desc_utilidades', '$qtd_utilidades', '$data')";
                    $insert_inclusao_utilidades = $db->exec($sql_inclusao_utilidades);
                }

                $sql_utilidades = "UPDATE PRODUTOS SET UTILIDADES = '$qtd_utilidades' WHERE ID = '$id_utilidades' AND EAN = '$ean_utilidades' AND NFE = '$post_nfe'";
                $update_utilidades = $db->exec($sql_utilidades);
            } else {
                $sql_utilidades = "UPDATE PRODUTOS SET UTILIDADES = NULL WHERE ID = '$id_utilidades' AND EAN = '$ean_utilidades' AND NFE = '$post_nfe'";
                $update_utilidades = $db->exec($sql_utilidades);
            }
        }
    }
    
    if($post_papelaria !==  false) { 
        foreach ($post_papelaria as $key_papelaria => $value_papelaria){
            $id_papelaria = $value_papelaria['ID'];
            $ean_papelaria = $value_papelaria['EAN'];
            $qtd_papelaria = $value_papelaria['QTD'];
            $desc_papelaria = $value_papelaria['DESC'];

            if($qtd_papelaria != 0 && $qtd_papelaria != '') {
                $search_papelaria = "SELECT * FROM PRODUTOS WHERE ID = '$id_papelaria' AND EAN = '$ean_papelaria' AND NFE = '$post_nfe' AND PAPELARIA = '$qtd_papelaria'";
                $query_papelaria = $db->query($search_papelaria);
                $fetch_search_papelaria = $query_papelaria->fetchAll(PDO::FETCH_ASSOC);

                $search_inclusao_papelaria = "SELECT * FROM PRODUTOS WHERE ID = '$id_papelaria' AND EAN = '$ean_papelaria' AND NFE = '$post_nfe'";
                $query_inclusao_papelaria = $db->query($search_inclusao_papelaria);
                $fetch_search_inclusao_papelaria = $query_inclusao_papelaria->fetchAll(PDO::FETCH_ASSOC);

                foreach($fetch_search_inclusao_papelaria as $value_papelaria) {
                    $qtd_atual_papelaria = $value_papelaria['PAPELARIA'];
                }

                if(count($fetch_search_papelaria) == 0 && $qtd_atual_papelaria != NULL) {
                    $sql_log_papelaria = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, PAPELARIA, ALTERACAO, DATA_ALTERACAO) VALUES ('$id_papelaria','$post_nfe', '$ean_papelaria', '$desc_papelaria', '$qtd_papelaria', '1', '$data')";
                    $update_log_papelaria = $db->exec($sql_log_papelaria);
                }

                if($qtd_atual_papelaria == NULL) {
                    $sql_inclusao_papelaria = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, PAPELARIA, DATA_INCLUSAO) VALUES ('$id_papelaria','$post_nfe', '$ean_papelaria', '$desc_papelaria', '$qtd_papelaria', '$data')";
                    $insert_inclusao_papelaria = $db->exec($sql_inclusao_papelaria);
                }

                $sql_papelaria = "UPDATE PRODUTOS SET PAPELARIA = '$qtd_papelaria' WHERE ID = '$id_papelaria' AND EAN = '$ean_papelaria' AND NFE = '$post_nfe'";
                $update_papelaria = $db->exec($sql_papelaria);
            } else {
                $sql_papelaria = "UPDATE PRODUTOS SET PAPELARIA = NULL WHERE ID = '$id_papelaria' AND EAN = '$ean_papelaria' AND NFE = '$post_nfe'";
                $update_papelaria = $db->exec($sql_papelaria);
            }
        }
    }

    if($post_praca !==  false) { 
        foreach ($post_praca as $key_praca => $value_praca){
            $id_praca = $value_praca['ID'];
            $ean_praca = $value_praca['EAN'];
            $qtd_praca = $value_praca['QTD'];
            $desc_praca = $value_praca['DESC'];

            if($qtd_praca != 0 && $qtd_praca != '') {
                $search_praca = "SELECT * FROM PRODUTOS WHERE ID = '$id_praca' AND EAN = '$ean_praca' AND NFE = '$post_nfe' AND PRACA = '$qtd_praca'";
                $query_praca = $db->query($search_praca);
                $fetch_search_praca = $query_praca->fetchAll(PDO::FETCH_ASSOC);

                $search_inclusao_praca = "SELECT * FROM PRODUTOS WHERE ID = '$id_praca' AND EAN = '$ean_praca' AND NFE = '$post_nfe'";
                $query_inclusao_praca = $db->query($search_inclusao_praca);
                $fetch_search_inclusao_praca = $query_inclusao_praca->fetchAll(PDO::FETCH_ASSOC);

                foreach($fetch_search_inclusao_praca as $value_praca) {
                    $qtd_atual_praca = $value_praca['PRACA'];
                }

                if(count($fetch_search_praca) == 0 && $qtd_atual_praca != NULL) {
                    $sql_log_praca = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, PRACA, ALTERACAO, DATA_ALTERACAO) VALUES ('$id_praca','$post_nfe', '$ean_praca', '$desc_praca', '$qtd_praca', '1', '$data')";
                    $update_log_praca = $db->exec($sql_log_praca);
                }

                if($qtd_atual_praca == NULL) {
                    $sql_inclusao_praca = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, PRACA, DATA_INCLUSAO) VALUES ('$id_praca','$post_nfe', '$ean_praca', '$desc_praca', '$qtd_praca', '$data')";
                    $insert_inclusao_praca = $db->exec($sql_inclusao_praca);
                }

                $sql_praca = "UPDATE PRODUTOS SET PRACA = '$qtd_praca' WHERE ID = '$id_praca' AND EAN = '$ean_praca' AND NFE = '$post_nfe'";
                $update_praca = $db->exec($sql_praca);
            } else {
                $sql_praca = "UPDATE PRODUTOS SET PRACA = NULL WHERE ID = '$id_praca' AND EAN = '$ean_praca' AND NFE = '$post_nfe'";
                $update_praca = $db->exec($sql_praca);
            }
        }
    }

    if($post_sumidouro !==  false) { 
        foreach ($post_sumidouro as $key_sumidouro => $value_sumidouro){
            $id_sumidouro = $value_sumidouro['ID'];
            $ean_sumidouro = $value_sumidouro['EAN'];
            $qtd_sumidouro = $value_sumidouro['QTD'];
            $desc_sumidouro = $value_sumidouro['DESC'];

            if($qtd_sumidouro != 0 && $qtd_sumidouro != '') {
                $search_sumidouro = "SELECT * FROM PRODUTOS WHERE ID = '$id_sumidouro' AND EAN = '$ean_sumidouro' AND NFE = '$post_nfe' AND SUMIDOURO = '$qtd_sumidouro'";
                $query_sumidouro = $db->query($search_sumidouro);
                $fetch_search_sumidouro = $query_sumidouro->fetchAll(PDO::FETCH_ASSOC);

                $search_inclusao_sumidouro = "SELECT * FROM PRODUTOS WHERE ID = '$id_sumidouro' AND EAN = '$ean_sumidouro' AND NFE = '$post_nfe'";
                $query_inclusao_sumidouro = $db->query($search_inclusao_sumidouro);
                $fetch_search_inclusao_sumidouro = $query_inclusao_sumidouro->fetchAll(PDO::FETCH_ASSOC);

                foreach($fetch_search_inclusao_sumidouro as $value_sumidouro) {
                    $qtd_atual_sumidouro = $value_sumidouro['SUMIDOURO'];
                }

                if(count($fetch_search_sumidouro) == 0 && $qtd_atual_sumidouro != NULL) {
                    $sql_log_sumidouro = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, SUMIDOURO, ALTERACAO, DATA_ALTERACAO) VALUES ('$id_sumidouro','$post_nfe', '$ean_sumidouro', '$desc_sumidouro', '$qtd_sumidouro', '1', '$data')";
                    $update_log_sumidouro = $db->exec($sql_log_sumidouro);
                }

                if($qtd_atual_sumidouro == NULL) {
                    $sql_inclusao_sumidouro = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, SUMIDOURO, DATA_INCLUSAO) VALUES ('$id_sumidouro','$post_nfe', '$ean_sumidouro', '$desc_sumidouro', '$qtd_sumidouro', '$data')";
                    $insert_inclusao_sumidouro = $db->exec($sql_inclusao_sumidouro);
                }

                $sql_sumidouro = "UPDATE PRODUTOS SET SUMIDOURO = '$qtd_sumidouro' WHERE ID = '$id_sumidouro' AND EAN = '$ean_sumidouro' AND NFE = '$post_nfe'";
                $update_sumidouro = $db->exec($sql_sumidouro);
            } else {
                $sql_sumidouro = "UPDATE PRODUTOS SET SUMIDOURO = NULL WHERE ID = '$id_sumidouro' AND EAN = '$ean_sumidouro' AND NFE = '$post_nfe'";
                $update_sumidouro = $db->exec($sql_sumidouro); 
            }
        }
    }

    if($post_distribuidora !==  false) { 
        foreach ($post_distribuidora as $key_distribuidora => $value_distribuidora){
            $id_distribuidora = $value_distribuidora['ID'];
            $ean_distribuidora = $value_distribuidora['EAN'];
            $qtd_distribuidora = $value_distribuidora['QTD'];
            $desc_distribuidora = $value_distribuidora['DESC'];

            if($qtd_distribuidora != 0 && $qtd_distribuidora != '') {
                $search_distribuidora = "SELECT * FROM PRODUTOS WHERE ID = '$id_distribuidora' AND EAN = '$ean_distribuidora' AND NFE = '$post_nfe' AND DISTRIBUIDORA = '$qtd_distribuidora'";
                $query_distribuidora = $db->query($search_distribuidora);
                $fetch_search_distribuidora = $query_distribuidora->fetchAll(PDO::FETCH_ASSOC);

                $search_inclusao_distribuidora = "SELECT * FROM PRODUTOS WHERE ID = '$id_distribuidora' AND EAN = '$ean_distribuidora' AND NFE = '$post_nfe'";
                $query_inclusao_distribuidora = $db->query($search_inclusao_distribuidora);
                $fetch_search_inclusao_distribuidora = $query_inclusao_distribuidora->fetchAll(PDO::FETCH_ASSOC);

                foreach($fetch_search_inclusao_distribuidora as $value_distribuidora) {
                    $qtd_atual_distribuidora = $value_distribuidora['DISTRIBUIDORA'];
                }

                if(count($fetch_search_distribuidora) == 0 && $qtd_atual_distribuidora != NULL) {
                    $sql_log_distribuidora = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, DISTRIBUIDORA, ALTERACAO, DATA_ALTERACAO) VALUES ('$id_distribuidora','$post_nfe', '$ean_distribuidora', '$desc_distribuidora', '$qtd_distribuidora', '1', '$data')";
                    $update_log_distribuidora = $db->exec($sql_log_distribuidora);
                }

                if($qtd_atual_distribuidora == NULL) {
                    $sql_inclusao_distribuidora = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, DISTRIBUIDORA, DATA_INCLUSAO) VALUES ('$id_distribuidora','$post_nfe', '$ean_distribuidora', '$desc_distribuidora', '$qtd_distribuidora', '$data')";
                    $insert_inclusao_distribuidora = $db->exec($sql_inclusao_distribuidora);
                }

                $sql_distribuidora = "UPDATE PRODUTOS SET DISTRIBUIDORA = '$qtd_distribuidora' WHERE ID = '$id_distribuidora' AND EAN = '$ean_distribuidora' AND NFE = '$post_nfe'";
                $update_distribuidora = $db->exec($sql_distribuidora);
            } else {
                $sql_distribuidora = "UPDATE PRODUTOS SET DISTRIBUIDORA = NULL WHERE ID = '$id_distribuidora' AND EAN = '$ean_distribuidora' AND NFE = '$post_nfe'";
                $update_distribuidora = $db->exec($sql_distribuidora);
            }
        }
    }

    if($update_utilidades == 0) {
        $retorno['utilidades'] = false;
    } else {
        $retorno['utilidades'] = true;
    }
    if($update_papelaria == 0) {
        $retorno['papelaria'] = false;
    } else {
        $retorno['papelaria'] = true;
    }
    if($update_praca == 0) {
        $retorno['praca'] = false;
    } else {
        $retorno['praca'] = true;
    }
    if($update_sumidouro == 0) {
        $retorno['sumidouro'] = false;
    } else {
        $retorno['sumidouro'] = true;
    }
    if($update_distribuidora == 0) {
        $retorno['distribuidora'] = false;
    } else {
        $retorno['distribuidora'] = true;
    }
    
    if($update_utilidades == 1 || $update_papelaria == 1 || $update_praca == 1 || $update_sumidouro == 1 || $update_distribuidora == 1) {
        $sql_distribuidora = "UPDATE NOTAS SET EDICAO = '1', DATA_EDICAO = '$data' WHERE NFE = '$post_nfe'";
        $update_distribuidora = $db->exec($sql_distribuidora);
    }

    echo json_encode($retorno, true);

?>