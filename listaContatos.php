<?php
session_start();
$verificaUsuarioLogado = $_SESSION['verificaUsuarioLogado'];
if(!$verificaUsuarioLogado){
    header("Location: index.php?codMsg003");
}else{
    $nomeUsuarioLogado = $_SESSION['nomeUsuarioLogado'];
    include "conectaBanco.php";
    $codigoUsuarioLogado = $_SESSION['codigoUsuarioLogado'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width = device-width, initial-scale=1">
    <title> Agenda de Contatos</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap-icons.css">
    <script src="js/jquery-3.3.1.js"></script>
    <script src="js/bootstrap.bundle.js"></script>
    <style>
        html {
            height: 100%;
        }

        body {
            background: url('img/dark-blue-background.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100%;
            overflow-x: hidden;
        }

        .custom-file-input~.custom-file-label::after {
            content: "Selecionar";
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/icone.svg" width="30" height="30" alt="Agenda de Contatos">
            </a>
            <button class="navbar-toggler" type="button" data-toggler="collapse" data-target="#navbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="menuCadastros" role="button" href="#"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi-card-list"></i> Cadastros</a>
                        <div class="dropdown-menu" aria-labelledby="menuCadastros">
                            <a class="dropdown-item" href="cadastroContato.php">
                                <i class="bi-person-fill"></i> Novo Contato</a>
                            <a class="dropdown-item" href="listaContatos.php">
                                <i class="bi-list-ul"></i> Lista de Contatos</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="menuConta" role="button" href="#" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="bi-gear-fill"></i> Minha Conta</a>
                        <div class="dropdown-menu" aria-labelledby="menuConta">
                            <a class="dropdown-item" href="alterarDados.php">
                                <i class="bi-pencil-square"></i> Alterar Dados</a>
                            <a class="dropdown-item" href="logout.php">
                                <i class="bi-door-open-fill"></i> Sair</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="modal" data-target="#modalSobreAplicacao">
                            <i class="bi-info-circle"></i> Sobre</a>
                    </li>
                </ul>
                <form class="form-inline my-2 my-lg-0" method="get" action="listaContatos.php">
                    <input type="search" class="form-control mr-sm-2" name="busca" placeholder="Pesquisar">
                    <button class="btn btn-outline-light my-2 my-sm-0" type="submit"> Pesquisar </button>
                </form>
            </div>
        </div>
    </nav>
    <div class="h-100 row align-items-center pt-5">
        <div class="container">
            <div class="row">
                <div class="col-sm"></div>
                <div class="col-sm-12">
                    <div class="card my-5">
                        <div class="card-header bg-primary">
                            <div class="card-header bg primary text-white">
                                <h5> Lista de Contatos</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Nome</th>
                                        <th scope="col">Telefone</th>
                                        <th scope="col">E-mail</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(isset($_GET['busca'])){
                                        $busca = '%' . $_GET['busca'] . '%';
                                    }
                                    else{
                                        $busca = '%%';
                                    }

                                    $sqlContatos = "SELECT codigoContato, nomeContato, mailContato, telefone1Contato FROM contatos WHERE codigoUsuario=:codigoUsuario AND nomeContato LIKE :busca ORDER BY nomeContato";
                                    $sqlContatosST = $conexao->prepare($sqlContatos);
                                    $sqlContatosST->bindValue('codigoUsuario',$codigoUsuarioLogado);
                                    $sqlContatosST->bindValue('busca',$busca);
                                    $sqlContatosST->execute();
                                    $quantidadeContato = $sqlContatosST->rowCount();
                                    if($quantidadeContato > 0){
                                        $resultadoContatos = $sqlContatosST->fetchAll();

                                        foreach($resultadoContatos as list($codigoContato, $nomeContato, $mailContato, $telefone1Contato)){
                                            echo "<tr>
                                            <th scope=\"row\">$codigoContato</th>
                                            <td>$nomeContato</td>
                                            <td>$telefone1Contato</td>
                                            <td>$mailContato</td>
                                            <td>
                                                <div class=\"dropdown\">
                                                    <a class=\"btn btn-secondary dropdown-toggle btn-sm\" href=\"#\"
                                                        role=\"button\" id=\"$codigoContato\" data-toggle=\"dropdown\" aria-haspopup=\"true\"
                                                        aria-expanded=\"false\">
                                                        Ações
                                                    </a>
    
                                                    <div class=\"dropdown-menu\" aria-labelledby=\"{id}\">
                                                        <a class=\"dropdown-item\" href=\"#\" data-toggle=\"modal\"
                                                            data-target=\"#visualizarContato\" data-whatever=\"$codigoContato\">
                                                            <i class=\"bi-eye\"></i> Visualizar
                                                        </a>
                                                        <a class=\"dropdown-item\" href=\"cadastroContato.php?codigoContato=$codigoContato\">
                                                            <i class=\"bi-pencil\"></i>Editar
                                                        </a>
                                                        <a class=\"dropdown-item\" href=\"excluirContato.php?codigoContato=$codigoContato\" onclick=\" return confirm('Deseja excluir esse contato?') \">
                                                            <i class=\"bi-trash\"></i>Excluir
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">

                        </div>
                    </div>
                </div>
                <div class="col-sm"></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalSobreAplicacao" tabindex="-1" role="dialog" aria-labelledby="sobreAplicacao"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sobreAplicacao">Sobre</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img src="img/logo.jpg">
                    <hr>
                    <p>Agenda de Contatos</p>
                    <p>Versão 1.0 </p>
                    <p>Todos os Direitos Reservados &copy; 2021</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="visualizarContato" tabindex="-1" role="dialog" aria-labelledby="visualizarDadosContato"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="visualizarDadosContato">Dados do Contato</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="dadosContato">

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    $(document).ready(function(){
        $('#visualizarContato').on('show.bs.modal', function(event){
            var origemContato =  $(event.relatedTarget);
            var codigoContato = origemContato.data('whatever');

            $('#dadosContato').load('visualizaContato.php?codigoContato=' + codigoContato);
        });
    });

</script>
</html>