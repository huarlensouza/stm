<?php
    date_default_timezone_set('America/Sao_Paulo');

    include 'excel_reader.php';

    $lojas = [
        'utilidades',
        'papelaria',
        'praca',
        'sumidouro',
        'distribuidora'
    ];
    
    if(isset($_FILES['file']['name'])) {

        if($_FILES['file']['size'] < 10485760) {

            $file_name = $_FILES['file']['name'];
            $file_type = strtolower(substr($file_name,-3));

            if($file_type === 'xls') {

                $excel = new PhpExcelReader;
                $excel->read($_FILES['file']['tmp_name']);
    
                $array_qtd = array_slice($excel->sheets[1]['cells'], 9);
    
                foreach($lojas as $key_lj => $value_lj) {
            
                    foreach($array_qtd as $key => $value) {
                        
                        if($value[$key_lj+7] != null) {
            
                            $retorno['lojas'][$value_lj][$key] = $value[$key_lj+7] ; 
            
                        }
                    }
            
                }
    
            } else {
                $retorno['code_erro'] = '101';
                $retorno['erro'] = 'Arquivo inválido, permitido apenas arquivos com extensão .xls';
            }
        } else {
            $retorno['code_erro'] = '100';
            $retorno['erro'] = 'Arquivo com tamanho superior o permitido';
        }
    }

    if($retorno == null) {
        $retorno['code_erro'] = '103';
        $retorno['erro'] = 'Arquivo inválido para importação';
    }

    echo json_encode($retorno, true);

?>