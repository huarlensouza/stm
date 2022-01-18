<?php 
    header('Content-Type: text/html; charset=utf-8');
    date_default_timezone_set('America/Sao_Paulo');

    $data = date("Y-m-d H:i:s", time());
    $quebra = chr(13).chr(10);
    ini_set('memory_limit', '-1');

    $path_sql = '../database/database.db';
    $db = new PDO("sqlite:".$path_sql."");

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
    
    $mes = ltrim(date("m", time()), '0');
    $ano = date("Y", time());

    $post_utilidades = $_POST['loja_utilidades'] ? $_POST['loja_utilidades'] : false;
    $post_papelaria = $_POST['loja_papelaria'] ? $_POST['loja_papelaria'] : false;
    $post_praca = $_POST['loja_praca'] ? $_POST['loja_praca'] : false;
    $post_sumidouro = $_POST['loja_sumidouro'] ? $_POST['loja_sumidouro'] : false;
    $post_distribuidora = $_POST['loja_distribuidora'] ? $_POST['loja_distribuidora'] : false;

    if($post_utilidades !== false) {

        $index_remove_utilidades = 0;
        while ($index_remove_utilidades < count($post_utilidades)) {
            if (($key_utilidades = array_search('', $post_utilidades)) !== false) {
                unset($post_utilidades[$key_utilidades]);
            }
            $index_remove_utilidades++;
        }
        $first_utilidades = reset($post_utilidades);

        $loja_utilidades = '(13066) Bazar Opção - Utilidades';
        $path_utilidades = '../../Transferências/'.$loja_utilidades.'/'.$meses[$mes-1].' de '.$ano;

        $nfe_utilidades = $first_utilidades['NFE'];
        $nfe = $first_utilidades['NFE'];
        $fornecedor_utilidades = $first_utilidades['FORNECEDOR'];

        if(!file_exists($path_utilidades)) {
            mkdir($path_utilidades.'/Efetuados', 0777, true);
        }

        $caminho_utilidades = $path_utilidades."/".str_replace('/', '-', $nfe_utilidades)." ".substr($loja_utilidades,0,7)." & ".$fornecedor_utilidades.".txt";
        if(file_exists($caminho_utilidades)) {
            unlink($caminho_utilidades);
        } 

        $index_utilidades = 0;
        while($index_utilidades < count($post_utilidades)) {
            
            if(is_array($post_utilidades[$index_utilidades]) && $verificar_utilidades = array_key_exists('EAN', $post_utilidades[$index_utilidades])){

                if($post_utilidades[$index_utilidades]['QTD'] != 0) {

                    $ean_utilidades = $post_utilidades[$index_utilidades]['EAN'];
                    $qtd_utilidades = $post_utilidades[$index_utilidades]['QTD'];
                    $id_utilidades = $post_utilidades[$index_utilidades]['ID'];
                    $desc_utilidades = $post_utilidades[$index_utilidades]['DESC'];

                    $search_ean_utilidades = "SELECT * FROM PRODUTOS WHERE ID = '$id_utilidades'";
                    $query_ean_utilidades = $db->query($search_ean_utilidades);
                    $fetch_search_ean_utilidades = $query_ean_utilidades->fetchAll(PDO::FETCH_ASSOC);

                    foreach($fetch_search_ean_utilidades as $value_ean) {
                        $ean_atual_utilidades = $value_ean['EAN'];
                    }

                    $search_utilidades = "SELECT * FROM PRODUTOS WHERE ID = '$id_utilidades' AND EAN = '$ean_utilidades' AND NFE = '$nfe_utilidades' AND UTILIDADES = '$qtd_utilidades'";
                    $query_utilidades = $db->query($search_utilidades);
                    $fetch_search_utilidades = $query_utilidades->fetchAll(PDO::FETCH_ASSOC);

                    $search_inclusao_utilidades = "SELECT * FROM PRODUTOS WHERE ID = '$id_utilidades' AND EAN = '$ean_utilidades' AND NFE = '$nfe_utilidades'";
                    $query_inclusao_utilidades = $db->query($search_inclusao_utilidades);
                    $fetch_search_inclusao_utilidades = $query_inclusao_utilidades->fetchAll(PDO::FETCH_ASSOC);

                    foreach($fetch_search_inclusao_utilidades as $value_utilidades) {
                        $qtd_atual_utilidades = $value_utilidades['UTILIDADES'];
                    }

                    if(count($fetch_search_utilidades) == 1) {
                        $sql_semAlteracao_utilidades = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, UTILIDADES, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_utilidades','$nfe_utilidades', '$ean_utilidades', '$desc_utilidades', '$qtd_utilidades', '1', '$data')";
                        
                        $insert_semAlteracao_utilidades = $db->exec($sql_semAlteracao_utilidades);
                    } else if(count($fetch_search_utilidades) == 0 && $qtd_atual_utilidades != NULL) {
                        $sql_comAlteracao_utilidades = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, UTILIDADES, ALTERACAO, DATA_ALTERACAO, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_utilidades','$nfe_utilidades', '$ean_utilidades', '$desc_utilidades', '$qtd_utilidades', '1', '$data', '1', '$data')";
                        $insert_comAlteracao_utilidades = $db->exec($sql_comAlteracao_utilidades);
                    }
                    if($qtd_atual_utilidades == NULL) {
                        $sql_inclusao_utilidades = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, UTILIDADES, DATA_INCLUSAO, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_utilidades','$nfe_utilidades', '$ean_utilidades', '$desc_utilidades', '$qtd_utilidades', '$data', '1', '$data')";
                        $insert_inclusao_utilidades = $db->exec($sql_inclusao_utilidades);
                    }
    
                    $sql_update_utilidades = "UPDATE PRODUTOS SET UTILIDADES = '$qtd_utilidades' WHERE ID = '$id_utilidades' AND EAN = '$ean_utilidades' AND NFE = '$nfe_utilidades'";
                    $update_utilidades = $db->exec($sql_update_utilidades);

                    $fp_utilidades = fopen($caminho_utilidades, "a");
                    
                    $escreve_utilidades = fwrite($fp_utilidades, $ean_utilidades.';'.$qtd_utilidades.$quebra);

                }

                if($post_utilidades[$index_utilidades]['QTD'] == 0) {
                    $ean_utilidades = $post_utilidades[$index_utilidades]['EAN'];
                    $qtd_utilidades = $post_utilidades[$index_utilidades]['QTD'];
                    $id_utilidades = $post_utilidades[$index_utilidades]['ID'];
                    $desc_utilidades = $post_utilidades[$index_utilidades]['DESC'];

                    $sql_update_utilidades = "UPDATE PRODUTOS SET UTILIDADES = NULL WHERE ID = '$id_utilidades' AND EAN = '$ean_utilidades' AND NFE = '$nfe_utilidades'";
                    $update_utilidades = $db->exec($sql_update_utilidades);

                    $retorno['UTILIDADES'] = false;
                }
                
            }
            $index_utilidades++;
        }

        if(file_exists($caminho_utilidades)) {
            $sql_log_utilidades = "INSERT INTO LOG_TRANSFERENCIA (LOJA, NFE, FORNECEDOR, PATH_TXT, DATA_TRANSFERENCIA) VALUES ('$loja_utilidades', '$nfe_utilidades', '$fornecedor_utilidades', '$caminho_utilidades', '$data')";
            $results_log_utilidades = $db->prepare($sql_log_utilidades)->execute();
            $retorno['UTILIDADES'] = true;
        }

    } else {
        $retorno['UTILIDADES'] = false;
    }


    if($post_papelaria !== false) {

        $index_remove_papelaria = 0;
        while ($index_remove_papelaria < count($post_papelaria)) {
            if (($key_papelaria = array_search('', $post_papelaria)) !== false) {
                unset($post_papelaria[$key_papelaria]);
            }
            $index_remove_papelaria++;
        }
        $first_papelaria = reset($post_papelaria);

        $loja_papelaria = '(13067) Bazar Opção - Papelaria';
        $path_papelaria = '../../Transferências/'.$loja_papelaria.'/'.$meses[$mes-1].' de '.$ano;

        $nfe_papelaria = $first_papelaria['NFE'];
        $nfe = $first_papelaria['NFE'];
        $fornecedor_papelaria = $first_papelaria['FORNECEDOR'];

        if(!file_exists($path_papelaria)) {
            mkdir($path_papelaria, 0777, true);
        }

        $caminho_papelaria = $path_papelaria."/".str_replace('/', '-', $nfe_papelaria)." ".substr($loja_papelaria,0,7)." & ".$fornecedor_papelaria.".txt";
        if(file_exists($caminho_papelaria)) {
            unlink($caminho_papelaria);
        } 

        $index_papelaria = 0;
        while($index_papelaria < count($post_papelaria)) {
            if(is_array($post_papelaria[$index_papelaria]) && $verificar_papelaria = array_key_exists('EAN', $post_papelaria[$index_papelaria])){

                if($post_papelaria[$index_papelaria]['QTD'] != 0) {

                    $ean_papelaria = $post_papelaria[$index_papelaria]['EAN'];
                    $qtd_papelaria = $post_papelaria[$index_papelaria]['QTD'];
                    $id_papelaria = $post_papelaria[$index_papelaria]['ID'];
                    $desc_papelaria = $post_papelaria[$index_papelaria]['DESC'];

                    $search_papelaria = "SELECT * FROM PRODUTOS WHERE ID = '$id_papelaria' AND EAN = '$ean_papelaria' AND NFE = '$nfe_papelaria' AND PAPELARIA = '$qtd_papelaria'";
                    $query_papelaria = $db->query($search_papelaria);
                    $fetch_search_papelaria = $query_papelaria->fetchAll(PDO::FETCH_ASSOC);

                    $search_inclusao_papelaria = "SELECT * FROM PRODUTOS WHERE ID = '$id_papelaria' AND EAN = '$ean_papelaria' AND NFE = '$nfe_papelaria'";
                    $query_inclusao_papelaria = $db->query($search_inclusao_papelaria);
                    $fetch_search_inclusao_papelaria = $query_inclusao_papelaria->fetchAll(PDO::FETCH_ASSOC);

                    foreach($fetch_search_inclusao_papelaria as $value_papelaria) {
                        $qtd_atual_papelaria = $value_papelaria['PAPELARIA'];
                    }

                    if(count($fetch_search_papelaria) == 1) {
                        $sql_semAlteracao_papelaria = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, PAPELARIA, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_papelaria','$nfe_papelaria', '$ean_papelaria', '$desc_papelaria', '$qtd_papelaria', '1', '$data')";
                        
                        $insert_semAlteracao_papelaria = $db->exec($sql_semAlteracao_papelaria);
                    } else if(count($fetch_search_papelaria) == 0 && $qtd_atual_papelaria != NULL) {
                        $sql_comAlteracao_papelaria = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, PAPELARIA, ALTERACAO, DATA_ALTERACAO, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_papelaria','$nfe_papelaria', '$ean_papelaria', '$desc_papelaria', '$qtd_papelaria', '1', '$data', '1', '$data')";
                        $insert_comAlteracao_papelaria = $db->exec($sql_comAlteracao_papelaria);
                    }
                    if($qtd_atual_papelaria == NULL) {
                        $sql_inclusao_papelaria = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, PAPELARIA, DATA_INCLUSAO, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_papelaria','$nfe_papelaria', '$ean_papelaria', '$desc_papelaria', '$qtd_papelaria', '$data', '1', '$data')";
                        $insert_inclusao_papelaria = $db->exec($sql_inclusao_papelaria);
                    }
    
                    $sql_update_papelaria = "UPDATE PRODUTOS SET PAPELARIA = '$qtd_papelaria' WHERE ID = '$id_papelaria' AND EAN = '$ean_papelaria' AND NFE = '$nfe_papelaria'";
                    $update_papelaria = $db->exec($sql_update_papelaria);

                    $fp_papelaria = fopen($caminho_papelaria, "a");
                    
                    $escreve_papelaria = fwrite($fp_papelaria, $ean_papelaria.';'.$qtd_papelaria.$quebra);

                } 

                if($post_papelaria[$index_papelaria]['QTD'] == 0) {
                    $ean_papelaria = $post_papelaria[$index_papelaria]['EAN'];
                    $qtd_papelaria = $post_papelaria[$index_papelaria]['QTD'];
                    $id_papelaria = $post_papelaria[$index_papelaria]['ID'];
                    $desc_papelaria = $post_papelaria[$index_papelaria]['DESC'];
                    
                    $sql_update_papelaria = "UPDATE PRODUTOS SET PAPELARIA = NULL WHERE ID = '$id_papelaria' AND EAN = '$ean_papelaria' AND NFE = '$nfe_papelaria'";
                    $update_papelaria = $db->exec($sql_update_papelaria);

                    $retorno['PAPELARIA'] = false;
                }
            }
            $index_papelaria++;
        }

        if(file_exists($caminho_papelaria)) {
            $sql_log_papelaria = "INSERT INTO LOG_TRANSFERENCIA (LOJA, NFE, FORNECEDOR, PATH_TXT, DATA_TRANSFERENCIA) VALUES ('$loja_papelaria', '$nfe_papelaria', '$fornecedor_papelaria', '$caminho_papelaria', '$data')";
            $results_log_papelaria = $db->prepare($sql_log_papelaria)->execute();
            $retorno['PAPELARIA'] = true;
        } 

    } else {
        $retorno['PAPELARIA'] = false;
    }

    if($post_praca !== false) {

        $index_remove_praca = 0;
        while ($index_remove_praca < count($post_praca)) {
            if (($key_praca = array_search('', $post_praca)) !== false) {
                unset($post_praca[$key_praca]);
            }
            $index_remove_praca++;
        }
        $first_praca = reset($post_praca);

        $loja_praca = '(13068) Bazar Opção - Praça';
        $path_praca = '../../Transferências/'.$loja_praca.'/'.$meses[$mes-1].' de '.$ano;

        $nfe_praca = $first_praca['NFE'];
        $nfe = $first_praca['NFE'];
        $fornecedor_praca = $first_praca['FORNECEDOR'];

        if(!file_exists($path_praca)) {
            mkdir($path_praca, 0777, true);
        }

        $caminho_praca = $path_praca."/".str_replace('/', '-', $nfe_praca)." ".substr($loja_praca,0,7)." & ".$fornecedor_praca.".txt";
        if(file_exists($caminho_praca)) {
            unlink($caminho_praca);
        } 

        $index_praca = 0;
        while($index_praca < count($post_praca)) {
            if(is_array($post_praca[$index_praca]) && $verificar_praca = array_key_exists('EAN', $post_praca[$index_praca])){
                if($post_praca[$index_praca]['QTD'] != 0) {

                    $ean_praca = $post_praca[$index_praca]['EAN'];
                    $qtd_praca = $post_praca[$index_praca]['QTD'];
                    $id_praca = $post_praca[$index_praca]['ID'];
                    $desc_praca = $post_praca[$index_praca]['DESC'];

                    $search_praca = "SELECT * FROM PRODUTOS WHERE ID = '$id_praca' AND EAN = '$ean_praca' AND NFE = '$nfe_praca' AND PRACA = '$qtd_praca'";
                    $query_praca = $db->query($search_praca);
                    $fetch_search_praca = $query_praca->fetchAll(PDO::FETCH_ASSOC);

                    $search_inclusao_praca = "SELECT * FROM PRODUTOS WHERE ID = '$id_praca' AND EAN = '$ean_praca' AND NFE = '$nfe_praca'";
                    $query_inclusao_praca = $db->query($search_inclusao_praca);
                    $fetch_search_inclusao_praca = $query_inclusao_praca->fetchAll(PDO::FETCH_ASSOC);

                    foreach($fetch_search_inclusao_praca as $value_praca) {
                        $qtd_atual_praca = $value_praca['PRACA'];
                    }

                    if(count($fetch_search_praca) == 1) {
                        $sql_semAlteracao_praca = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, PRACA, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_praca','$nfe_praca', '$ean_praca', '$desc_praca', '$qtd_praca', '1', '$data')";
                        
                        $insert_semAlteracao_praca = $db->exec($sql_semAlteracao_praca);
                    } else if(count($fetch_search_praca) == 0 && $qtd_atual_praca != NULL) {
                        $sql_comAlteracao_praca = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, PRACA, ALTERACAO, DATA_ALTERACAO, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_praca','$nfe_praca', '$ean_praca', '$desc_praca', '$qtd_praca', '1', '$data', '1', '$data')";
                        $insert_comAlteracao_praca = $db->exec($sql_comAlteracao_praca);
                    }
                    if($qtd_atual_praca == NULL) {
                        $sql_inclusao_praca = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, PRACA, DATA_INCLUSAO, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_praca','$nfe_praca', '$ean_praca', '$desc_praca', '$qtd_praca', '$data', '1', '$data')";
                        $insert_inclusao_praca = $db->exec($sql_inclusao_praca);
                    }
    
                    $sql_update_praca = "UPDATE PRODUTOS SET PRACA = '$qtd_praca' WHERE ID = '$id_praca' AND EAN = '$ean_praca' AND NFE = '$nfe_praca'";
                    $update_praca = $db->exec($sql_update_praca);

                    $fp_praca = fopen($caminho_praca, "a");
                    
                    $escreve_praca = fwrite($fp_praca, $ean_praca.';'.$qtd_praca.$quebra);

                }

                if($post_praca[$index_praca]['QTD'] == 0) {

                    $ean_praca = $post_praca[$index_praca]['EAN'];
                    $qtd_praca = $post_praca[$index_praca]['QTD'];
                    $id_praca = $post_praca[$index_praca]['ID'];
                    $desc_praca = $post_praca[$index_praca]['DESC'];
    
                    $sql_update_praca = "UPDATE PRODUTOS SET PRACA = NULL WHERE ID = '$id_praca' AND EAN = '$ean_praca' AND NFE = '$nfe_praca'";
                    $update_praca = $db->exec($sql_update_praca);

                    $retorno['PRACA'] = false;
                }

            }
            $index_praca++;
        }

        if(file_exists($caminho_praca)) {
            $sql_log_praca = "INSERT INTO LOG_TRANSFERENCIA (LOJA, NFE, FORNECEDOR, PATH_TXT, DATA_TRANSFERENCIA) VALUES ('$loja_praca', '$nfe_praca', '$fornecedor_praca', '$caminho_praca', '$data')";
            $results_log_praca = $db->prepare($sql_log_praca)->execute();
            $retorno['PRACA'] = true;
        }
    } else {
        $retorno['PRACA'] = false;
    }

    if($post_sumidouro !== false) {

        $index_remove_sumidouro = 0;
        while ($index_remove_sumidouro < count($post_sumidouro)) {
            if (($key_sumidouro = array_search('', $post_sumidouro)) !== false) {
                unset($post_sumidouro[$key_sumidouro]);
            }
            $index_remove_sumidouro++;
        }
        $first_sumidouro = reset($post_sumidouro);

        $loja_sumidouro = '(13069) Bazar Opção - Sumidouro';
        $path_sumidouro = '../../Transferências/'.$loja_sumidouro.'/'.$meses[$mes-1].' de '.$ano;

        $nfe_sumidouro = $first_sumidouro['NFE'];
        $nfe = $first_sumidouro['NFE'];
        $fornecedor_sumidouro = $first_sumidouro['FORNECEDOR'];

        if(!file_exists($path_sumidouro)) {
            mkdir($path_sumidouro, 0777, true);
        }

        $caminho_sumidouro = $path_sumidouro."/".str_replace('/', '-', $nfe_sumidouro)." ".substr($loja_sumidouro,0,7)." & ".$fornecedor_sumidouro.".txt";
        if(file_exists($caminho_sumidouro)) {
            unlink($caminho_sumidouro);
        } 

        $index_sumidouro = 0;
        while($index_sumidouro < count($post_sumidouro)) {
            if(is_array($post_sumidouro[$index_sumidouro]) && $verificar_sumidouro = array_key_exists('EAN', $post_sumidouro[$index_sumidouro])){
                if($post_sumidouro[$index_sumidouro]['QTD'] != 0) {

                    $ean_sumidouro = $post_sumidouro[$index_sumidouro]['EAN'];
                    $qtd_sumidouro = $post_sumidouro[$index_sumidouro]['QTD'];
                    $id_sumidouro = $post_sumidouro[$index_sumidouro]['ID'];
                    $desc_sumidouro = $post_sumidouro[$index_sumidouro]['DESC'];

                    $search_sumidouro = "SELECT * FROM PRODUTOS WHERE ID = '$id_sumidouro' AND EAN = '$ean_sumidouro' AND NFE = '$nfe_sumidouro' AND SUMIDOURO = '$qtd_sumidouro'";
                    $query_sumidouro = $db->query($search_sumidouro);
                    $fetch_search_sumidouro = $query_sumidouro->fetchAll(PDO::FETCH_ASSOC);

                    $search_inclusao_sumidouro = "SELECT * FROM PRODUTOS WHERE ID = '$id_sumidouro' AND EAN = '$ean_sumidouro' AND NFE = '$nfe_sumidouro'";
                    $query_inclusao_sumidouro = $db->query($search_inclusao_sumidouro);
                    $fetch_search_inclusao_sumidouro = $query_inclusao_sumidouro->fetchAll(PDO::FETCH_ASSOC);

                    foreach($fetch_search_inclusao_sumidouro as $value_sumidouro) {
                        $qtd_atual_sumidouro = $value_sumidouro['SUMIDOURO'];
                    }

                    if(count($fetch_search_sumidouro) == 1) {
                        $sql_semAlteracao_sumidouro = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, SUMIDOURO, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_sumidouro','$nfe_sumidouro', '$ean_sumidouro', '$desc_sumidouro', '$qtd_sumidouro', '1', '$data')";
                        
                        $insert_semAlteracao_sumidouro = $db->exec($sql_semAlteracao_sumidouro);
                    } else if(count($fetch_search_sumidouro) == 0 && $qtd_atual_sumidouro != NULL) {
                        $sql_comAlteracao_sumidouro = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, SUMIDOURO, ALTERACAO, DATA_ALTERACAO, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_sumidouro','$nfe_sumidouro', '$ean_sumidouro', '$desc_sumidouro', '$qtd_sumidouro', '1', '$data', '1', '$data')";
                        $insert_comAlteracao_sumidouro = $db->exec($sql_comAlteracao_sumidouro);
                    }
                    if($qtd_atual_sumidouro == NULL) {
                        $sql_inclusao_sumidouro = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, SUMIDOURO, DATA_INCLUSAO, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_sumidouro','$nfe_sumidouro', '$ean_sumidouro', '$desc_sumidouro', '$qtd_sumidouro', '$data', '1', '$data')";
                        $insert_inclusao_sumidouro = $db->exec($sql_inclusao_sumidouro);
                    }
    
                    $sql_update_sumidouro = "UPDATE PRODUTOS SET SUMIDOURO = '$qtd_sumidouro' WHERE ID = '$id_sumidouro' AND EAN = '$ean_sumidouro' AND NFE = '$nfe_sumidouro'";
                    $update_sumidouro = $db->exec($sql_update_sumidouro);

                    $fp_sumidouro = fopen($caminho_sumidouro, "a");
                    
                    $escreve_sumidouro = fwrite($fp_sumidouro, $ean_sumidouro.';'.$qtd_sumidouro.$quebra);

                } 

                if($post_sumidouro[$index_sumidouro]['QTD'] == 0) {
                    $ean_sumidouro = $post_sumidouro[$index_sumidouro]['EAN'];
                    $qtd_sumidouro = $post_sumidouro[$index_sumidouro]['QTD'];
                    $id_sumidouro = $post_sumidouro[$index_sumidouro]['ID'];
                    $desc_sumidouro = $post_sumidouro[$index_sumidouro]['DESC'];
                    $sql_update_sumidouro = "UPDATE PRODUTOS SET SUMIDOURO = NULL WHERE ID = '$id_sumidouro' AND EAN = '$ean_sumidouro' AND NFE = '$nfe_sumidouro'";
                    $update_sumidouro = $db->exec($sql_update_sumidouro);
                    $retorno['SUMIDOURO'] = false;
                }
            }

            $index_sumidouro++;
        }

        if(file_exists($caminho_sumidouro)) {
            $sql_log_sumidouro = "INSERT INTO LOG_TRANSFERENCIA (LOJA, NFE, FORNECEDOR, PATH_TXT, DATA_TRANSFERENCIA) VALUES ('$loja_sumidouro', '$nfe_sumidouro', '$fornecedor_sumidouro', '$caminho_sumidouro', '$data')";
            $results_log_sumidouro = $db->prepare($sql_log_sumidouro)->execute();
            $retorno['SUMIDOURO'] = true;
        }
    } else {
        $retorno['SUMIDOURO'] = false;
    }

    if($post_distribuidora !== false) {

        $index_remove_distribuidora = 0;
        while ($index_remove_distribuidora < count($post_distribuidora)) {
            if (($key_distribuidora = array_search('', $post_distribuidora)) !== false) {
                unset($post_distribuidora[$key_distribuidora]);
            }
            $index_remove_distribuidora++;
        }
        $first_distribuidora = reset($post_distribuidora);

        $loja_distribuidora = '(13489) Bazar Opção - Distribuidora';
        $path_distribuidora = '../../Transferências/'.$loja_distribuidora.'/'.$meses[$mes-1].' de '.$ano;

        $nfe_distribuidora = $first_distribuidora['NFE'];
        $nfe = $first_distribuidora['NFE'];
        $fornecedor_distribuidora = $first_distribuidora['FORNECEDOR'];

        if(!file_exists($path_distribuidora)) {
            mkdir($path_distribuidora, 0777, true);
        }

        $caminho_distribuidora = $path_distribuidora."/".str_replace('/', '-', $nfe_distribuidora)." ".substr($loja_distribuidora,0,7)." & ".$fornecedor_distribuidora.".txt";
        if(file_exists($caminho_distribuidora)) {
            unlink($caminho_distribuidora);
        } 

        $index_distribuidora = 0;
        while($index_distribuidora < count($post_distribuidora)) {
            if(is_array($post_distribuidora[$index_distribuidora]) && $verificar_distribuidora = array_key_exists('EAN', $post_distribuidora[$index_distribuidora])){
                if($post_distribuidora[$index_distribuidora]['QTD'] != 0) {

                    $ean_distribuidora = $post_distribuidora[$index_distribuidora]['EAN'];
                    $qtd_distribuidora = $post_distribuidora[$index_distribuidora]['QTD'];
                    $id_distribuidora = $post_distribuidora[$index_distribuidora]['ID'];
                    $desc_distribuidora = $post_distribuidora[$index_distribuidora]['DESC'];

                    $search_distribuidora = "SELECT * FROM PRODUTOS WHERE ID = '$id_distribuidora' AND EAN = '$ean_distribuidora' AND NFE = '$nfe_distribuidora' AND DISTRIBUIDORA = '$qtd_distribuidora'";
                    $query_distribuidora = $db->query($search_distribuidora);
                    $fetch_search_distribuidora = $query_distribuidora->fetchAll(PDO::FETCH_ASSOC);

                    $search_inclusao_distribuidora = "SELECT * FROM PRODUTOS WHERE ID = '$id_distribuidora' AND EAN = '$ean_distribuidora' AND NFE = '$nfe_distribuidora'";
                    $query_inclusao_distribuidora = $db->query($search_inclusao_distribuidora);
                    $fetch_search_inclusao_distribuidora = $query_inclusao_distribuidora->fetchAll(PDO::FETCH_ASSOC);

                    foreach($fetch_search_inclusao_distribuidora as $value_distribuidora) {
                        $qtd_atual_distribuidora = $value_distribuidora['DISTRIBUIDORA'];
                    }

                    if(count($fetch_search_distribuidora) == 1) {
                        $sql_semAlteracao_distribuidora = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, DISTRIBUIDORA, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_distribuidora','$nfe_distribuidora', '$ean_distribuidora', '$desc_distribuidora', '$qtd_distribuidora', '1', '$data')";
                        
                        $insert_semAlteracao_distribuidora = $db->exec($sql_semAlteracao_distribuidora);
                    } else if(count($fetch_search_distribuidora) == 0 && $qtd_atual_distribuidora != NULL) {
                        $sql_comAlteracao_distribuidora = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, DISTRIBUIDORA, ALTERACAO, DATA_ALTERACAO, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_distribuidora','$nfe_distribuidora', '$ean_distribuidora', '$desc_distribuidora', '$qtd_distribuidora', '1', '$data', '1', '$data')";
                        $insert_comAlteracao_distribuidora = $db->exec($sql_comAlteracao_distribuidora);
                    }
                    if($qtd_atual_distribuidora == NULL) {
                        $sql_inclusao_distribuidora = "INSERT INTO LOG_SAVE (ID_PROD, NFE, EAN, DESCRICAO, DISTRIBUIDORA, DATA_INCLUSAO, TRANSFERENCIA, DATA_TRANSFERENCIA) VALUES ('$id_distribuidora','$nfe_distribuidora', '$ean_distribuidora', '$desc_distribuidora', '$qtd_distribuidora', '$data', '1', '$data')";
                        $insert_inclusao_distribuidora = $db->exec($sql_inclusao_distribuidora);
                    }
    
                    $sql_update_distribuidora = "UPDATE PRODUTOS SET DISTRIBUIDORA = '$qtd_distribuidora' WHERE ID = '$id_distribuidora' AND EAN = '$ean_distribuidora' AND NFE = '$nfe_distribuidora'";
                    $update_distribuidora = $db->exec($sql_update_distribuidora);

                    $fp_distribuidora = fopen($caminho_distribuidora, "a");
                    
                    $escreve_distribuidora = fwrite($fp_distribuidora, $ean_distribuidora.';'.$qtd_distribuidora.$quebra);
                }

                if($post_distribuidora[$index_distribuidora]['QTD'] == 0) {
                    $ean_distribuidora = $post_distribuidora[$index_distribuidora]['EAN'];
                    $qtd_distribuidora = $post_distribuidora[$index_distribuidora]['QTD'];
                    $id_distribuidora = $post_distribuidora[$index_distribuidora]['ID'];
                    $desc_distribuidora = $post_distribuidora[$index_distribuidora]['DESC'];
                    $sql_update_distribuidora = "UPDATE PRODUTOS SET DISTRIBUIDORA = NULL WHERE ID = '$id_distribuidora' AND EAN = '$ean_distribuidora' AND NFE = '$nfe_distribuidora'";
                    $update_distribuidora = $db->exec($sql_update_distribuidora);
                    $retorno['DISTRIBUIDORA'] = false;
                }
            }
            $index_distribuidora++;
        }

        if(file_exists($caminho_distribuidora)) {
            $sql_log_distribuidora = "INSERT INTO LOG_TRANSFERENCIA (LOJA, NFE, FORNECEDOR, PATH_TXT, DATA_TRANSFERENCIA) VALUES ('$loja_distribuidora', '$nfe_distribuidora', '$fornecedor_distribuidora', '$caminho_distribuidora', '$data')";
            $results_log_distribuidora = $db->prepare($sql_log_distribuidora)->execute();
            $retorno['DISTRIBUIDORA'] = true;
            
        }
    } else {
        $retorno['DISTRIBUIDORA'] = false;
    }

    if($retorno['UTILIDADES'] == true ||  $retorno['PAPELARIA'] == true ||  $retorno['PRACA'] == true ||  $retorno['SUMIDOURO'] == true ||  $retorno['DISTRIBUIDORA'] == true) {
        $sql_nfe = "UPDATE NOTAS SET TRANSFERIDO = '1', DATA_TRANSFERIDO = '$data' WHERE NFE = '$nfe'";
        $update_nfe = $db->exec($sql_nfe);
    }

    echo json_encode($retorno, true);


?>