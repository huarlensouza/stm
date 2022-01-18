<?php
    date_default_timezone_set('America/Sao_Paulo');
    $data_importacao = date("Y-m-d H:i:s", time());

    $array_path = [];
    
    $lojas = array(
        '(13066) Bazar Opção - Utilidades',
        '(13067) Bazar Opção - Papelaria',
        '(13068) Bazar Opção - Praca',
        '(13069) Bazar Opção - Sumidouro',
        '(13489) Bazar Opção - Distribuidora'
    ); 

    $index = 0;
    while($index < count($lojas)) {
        $path = "../../Transfêrencias/".$lojas[$index];
        if ($diretorio = opendir($path)) {

            while(false !== ($pasta = readdir($diretorio))) {

                
            
                if(($pasta != ".") and ($pasta != "..")) {
                    
                    $caminho = $path.'/'.$pasta;
                    array_push($array_path, $caminho);
                }

            }
           
            closedir($diretorio);
        }

        $index++;
    }

    
    foreach($array_path as $key_path => $value_path) {

        

        
        $txt =  glob($value_path.'/*.txt');
        
        foreach($txt as $key => $value){

            $data = $array[4];
            
            if(substr($value,23,5) == '13066') {
                $array = explode('/', $value);
                $loja = $array[3];
                $data = $array[4];

                $retorno['13066'][$data][] = $value;
            }
            if(substr($value,23,5) == '13067') {
                $array = explode('/', $value);
                $loja = $array[3];
                $data = $array[4];

                $retorno['13067'][$data][] = $value;
            }
            if(substr($value,23,5) == '13068') {
                $array = explode('/', $value);
                $loja = $array[3];
                $data = $array[4];

                $retorno['13068'][$data][] = $value;
            }
            if(substr($value,23,5) == '13069') {
                $array = explode('/', $value);
                $loja = $array[3];
                $data = $array[4];

                $retorno['13069'][$data][] = $value;
            }
            if(substr($value,23,5) == '13489') {
                $array = explode('/', $value);
                $loja = $array[3];
                $data = $array[4];

                $retorno['13489'][$data][] = $value;
            }
        }

    }

    // echo '<pre>'; var_export($retorno); echo '</pre>';

    // echo '<pre>'; var_export($retorno); echo '</pre>';
    echo json_encode($retorno, true);

    // var_dump($pastas);
    // if(count($arquivos) == 0) {
    //     $retorno['erro'] = 'Nenhuma nota fiscal foi localizada.';
    //     echo json_encode($retorno, true);
    //     die();
    // }

    header('Content-Type: text/html; charset=utf-8');

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


    function utf8_encode_deep(&$input) {
        if (is_string($input)) {
            $input = utf8_encode($input);
        } else if (is_array($input)) {
            foreach ($input as &$value) {
                utf8_encode_deep($value);
            }
            unset($value);
        } else if (is_object($input)) {
            $vars = array_keys(get_object_vars($input));
    
            foreach ($vars as $var) {
                utf8_encode_deep($input->$var);
            }
        }
    }

    // echo json_encode($retorno, true);

?>