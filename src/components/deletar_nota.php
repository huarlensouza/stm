<?php 
    date_default_timezone_set('America/Sao_Paulo');
    $data = date("Y-m-d H:i:s", time());
    $quebra = chr(13).chr(10);
    ini_set('memory_limit', '-1');
    
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

    $post_nfe = $_POST['nota'];
    $path_sql = '../database/database.db';
    $db = new PDO("sqlite:".$path_sql."");

    $consulta_notas = $db->query("SELECT * FROM NOTAS WHERE NFE = '$post_nfe'");
    $result_notas = $consulta_notas->fetchAll(PDO::FETCH_ASSOC);

    $loja = $result_notas[0]['LOJA'];
    $numero_nota  = $result_notas[0]['NFE'];
    $data_entrada = $result_notas[0]['DATA_ENTRADA'];
    $fornecedor = $result_notas[0]['FORNECEDOR'];

    $ano = substr($data_entrada,6);
    $mes = ltrim(substr($data_entrada,3, 2), "0");

    $path_file =  '../../Importados/'.$loja.'/'.$meses[$mes-1].' de '.$ano;
    $arquivo = $path_file.'/'.str_replace('/', '-', $numero_nota).' & '.str_replace('.', ' ', $fornecedor).'.xls';
            
    $path_removidos = '../../Importados/'.$loja.'/'.$meses[$mes-1].' de '.$ano.'/Removidos';

    if(!file_exists($path_removidos)) {
        mkdir($path_removidos,0777,true);
    }

    rename($arquivo, $path_removidos.'/'.str_replace('/', '-', $numero_nota).' & '.str_replace('.', ' ', $fornecedor).'.xls');

    $sql_delete_nota = "DELETE FROM NOTAS WHERE NFE ='$post_nfe'";
    $sql_delete_produtos = "DELETE FROM PRODUTOS WHERE NFE ='$post_nfe'";

    $sql_log = "INSERT INTO LOG_DELETE (LOJA, NFE, DATA_ENTRADA, FORNECEDOR, DATA_DELETADO)
    VALUES ('$loja','$numero_nota','$data_entrada','$fornecedor', '$data')";
    
    $delete_nota = $db->exec($sql_delete_nota);
    $delete_produtos = $db->exec($sql_delete_produtos);
    $results_log = $db->prepare($sql_log)->execute();

    if($delete_nota == 0) {
        $retorno['deletado'] = true;
    } else {
        $retorno['deletado'] = true;
    }
    
    echo json_encode($retorno, true);

?>