![image](https://github.com/user-attachments/assets/072bfad6-c56d-4a10-b5b1-d50ff79ee129)Sistema de Transferência de Mercadoria.
Implementado para Papelaria Bazar Opção com 5 lojas e Centro de Distribuição, o sistema consiste em gerar um arquivo.txt com todas as informações para serem importadas pelo sistema de ERP da Getway Automação e emitir Nota fiscal de Transferência.

As notas fiscais que são importadas são emitidas no Sistema de ERP depois de realizado a Entrada de Estoque internamente, após isso é emitido um relatório e salvo em formato XLS para compatibilidade do Sistema de Transferência.
Na pasta "./Notas Fiscais para teste" tem algumas notas fiscais para teste.

O arquivo gerado contém os códigos de barras;quantidade para criação da Nota fiscal de Transferência pela importação do arquivo.
Os arquivos são criados separadamente por estabelecimento de acordo com as quantidades inseridas.

Versão do PHP</br>
    Version 7.4.23.0

Configurar o "php.ini" do PHP na pasta C:\xampp\php:
    max_input_vars = 2500

Arquivos e Pastas:
    src
        components (Módulos com todas as tarefas e ações das transferências)
        css (Os arquivos de CSS das páginas)
        database (O arquivo de database do SQLite3 que contém os dados das notas fiscais importadas)
        js (Os arquivos de Javascript das páginas)
    Rejeitados
        Notas fiscais que não estão no padrão da estrutura do relatório emitido pelo Sistema de ERP.
    Notas
        Pasta para adicionar as notas fiscais para serem importadas pelo sistema.
    Importados
        Notas fiscais importadas.
    Transferências
        Arquivos .txt gerado pelo Sistema de Transferência para serem importadas na Emissão de Nota fiscal.


Na rota /index para importar as Notas fiscais para realizar as transferências.
    As notas estão importadas na pasta "./Notas".


Na rota /consulta_nota.html para consultar as Notas Fiscais que foram importadas e transferidas.
    Todas as notas fiscais que ainda não foram transferidas, estarão pendentes.


Na rota /transferir.html, recebe o parâmetro ?nfe={Numero da NFe/Série} para efetuar a transferência da NF-e requisitada.
    Exemplo: localhost/transferir.html?nfe=789123/001
    Lista de Produtos da Nota Fiscal com as quantidades do Estoque, adicionar individualmente as quantidades para serem transferidas para as filiais.
    Alterar código de barras para mudar as associações com a Emissão da Nota Fiscal no Sistema de ERP.

Será possível salvar todas as informações inseridas antes de gerar a transferência para caso queira efetuar mais tarde.
    É salvo no Banco de Dados em SQLite3 para manter registros e logs das transferências.
Gerar transferência irá criar arquivos de .txt nas pastas separadamente por estabelecimento e data.
Caso as quantidades superem a do estoque de entrada, será necessário inserir a senha para permitir a transferência.

Senha para permitir quantidades superiores: 142536

Tela Inicial de Importação
![screenshot](https://i.imgur.com/8p1sxNq.png)

Tela das Notas fiscais pendente para importação
![screenshot](https://i.imgur.com/oeEjjkP.png)

Tela de Distribuição de Estoque para Filiais
![screenshot](https://i.imgur.com/oeEjjkP.png)

Tela de Geração dos arquivos para Importação no Samb@net
![screenshot](https://i.imgur.com/RZavikS.png)
