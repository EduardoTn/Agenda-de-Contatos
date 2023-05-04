<?php
session_start();
$verificaUsuarioLogado = $_SESSION['verificaUsuarioLogado'];
if(!$verificaUsuarioLogado){
    header("Location: index.php?codMsg=003");
}else{
    include "conectaBanco.php";
    $nomeUsuarioLogado = $_SESSION['nomeUsuarioLogado'];
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
    <script src="js/jquery.validate.js"></script>
    <script src="js/messages_pt_BR.js"></script>
    <script src="js/pwstrength-bootstrap.js"></script>
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
                <span class="navbar-text ml-4">
                    Olá <b> <?= $nomeUsuarioLogado ?> </b>, seja bem-vindo(a)!
                </span>
            </div>
        </div>
    </nav>
    <div class="h-100 row align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-sm"></div>
                <div class="col-sm-12">
                    <div class="card">
                    <?php
                    $flagErro = FALSE;
                    if (isset($_POST['acao'])) {
                        $acao = $_POST['acao'];
                        if ($acao == 'salvar') {
                            $nomeUsuario = $_POST['nomeUsuario'];
                            $mailUsuario = $_POST['mailUsuario'];
                            $mail2Usuario = $_POST['mail2Usuario'];
                            $senhaAtualUsuario = $_POST['senhaAtualUsuario'];
                            $senhaUsuario = $_POST['senhaUsuario'];
                            $senha2Usuario = $_POST['senha2Usuario'];

                            if (!empty($senhaAtualUsuario) && !empty($nomeUsuario) && !empty($mailUsuario) && !empty($senhaUsuario) && !empty($mail2Usuario) && !empty($senha2Usuario) && $senhaUsuario == $senha2Usuario && $mailUsuario == $mail2Usuario && strlen($nomeUsuario)>= 5 && strlen($senhaUsuario)>= 8) {
                                $senhaAtualUsuarioMD5 = md5($senhaAtualUsuario);

                                $sqlSenhaUsuario = "SELECT codigoUsuario FROM usuarios WHERE codigoUsuario=:codigoUsuario AND senhaUsuario=:senhaUsuario";
                                $sqlSenhaUsuarioST = $conexao->prepare($sqlSenhaUsuario);
                                $sqlSenhaUsuarioST->bindValue(":codigoUsuario", $codigoUsuarioLogado);
                                $sqlSenhaUsuarioST->bindValue(":senhaUsuario", $senhaAtualUsuarioMD5);

                                $sqlSenhaUsuarioST->execute();
                                $quantidadeUsuarios = $sqlSenhaUsuarioST->rowCount();
                                if($quantidadeUsuarios == 1){
                                $sqlUsuarios = "SELECT codigoUsuario FROM usuarios WHERE mailUsuario=:mailUsuario AND codigoUsuario<>:codigoUsuario";
                                $sqlUsuariosST = $conexao->prepare($sqlUsuarios);
                                $sqlUsuariosST->bindValue(':mailUsuario', $mailUsuario);
                                $sqlUsuariosST->bindValue(':codigoUsuario', $codigoUsuarioLogado);
                                $sqlUsuariosST->execute();
                                $quantidadeUsuarios = $sqlUsuariosST->rowCount();

                                if ($quantidadeUsuarios == 0) {
                                    $senhaUsuarioMd5 = md5($senhaUsuario);
                                    if($senhaAtualUsuarioMD5 == $senhaUsuario){
                                        $senhaUsuarioMD5 = $senhaAtualUsuarioMd5;
                                    }


                                    $sqlEditarUsuario = "UPDATE usuarios SET nomeUsuario =:nomeUsuario, mailUsuario=:mailUsuario, senhaUsuario=:senhaUsuario WHERE codigoUsuario=:codigoUsuario";

                                    $sqlEditarUsuarioST = $conexao->prepare($sqlEditarUsuario);
                                    $sqlEditarUsuarioST->bindValue(":codigoUsuario", $codigoUsuarioLogado);
                                    $sqlEditarUsuarioST->bindValue(":nomeUsuario", $nomeUsuario);
                                    $sqlEditarUsuarioST->bindValue(":mailUsuario", $mailUsuario);
                                    $sqlEditarUsuarioST->bindValue(":senhaUsuario", $senhaUsuarioMd5);

                                    if ($sqlEditarUsuarioST->execute()) {
                                        $mensagemAcao = "Cadastro alterado com sucesso!!";
                                    } else {
                                        $flagErro = TRUE;
                                        $mensagemAcao = "Erro ao cadastrar usuario: " . $sqlEditarUsuarioST->errorCode();
                                    }

                                    if (!$flagErro) {
                                        $classeMensagem = "alert-success";
                                    } else {
                                        $classeMensagem = "alert-danger";
                                    }
                                } else {
                                    $flagErro = TRUE;
                                    $mensagemAcao = "Email ja cadastrado";
                                    $classeMensagem = "alert-danger";
                                }
                            }else{
                                $flagErro = TRUE;
                                $mensagemAcao = "A senha atual informada está incorreta!";
                                $classeMensagem = "alert-danger";
                            }
                            }else{
                                $flagErro = TRUE;
                                    $mensagemAcao = "Preencha os campos Corretamente.\n 
                                                    As Senhas devem ser iguais e os Emails devem ser iguais.\n
                                                    Tamanho minimo para Nome = 5, Tamanho minimo para senha = 8.";
                                    $classeMensagem = "alert-danger";
                            }
                            echo "<div class=\"alert $classeMensagem alert-dismissible fade show\" role=\"alert\">
                            $mensagemAcao
                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                              <span aria-hidden=\"true\">&times;</span>
                            </button>
                          </div>";
                        }
                    }else{
                        $sqlUsuario = "SELECT nomeUsuario, mailUsuario, senhaUsuario FROM usuarios WHERE codigoUsuario=:codigoUsuario";

                        $sqlUsuarioST = $conexao->prepare($sqlUsuario);
                        $sqlUsuarioST->bindValue(":codigoUsuario", $codigoUsuarioLogado);
                        $sqlUsuarioST->execute();

                        $resultadoUsuario = $sqlUsuarioST->fetchAll();

                        list($nomeUsuario, $mailUsuario, $senhaUsuario) = $resultadoUsuario[0];
                        $mail2Usuario = $mailUsuario;
                        $senha2Usuario = $senhaUsuario;

                    }
                    ?>
                        <div class="card-border bg-primary">
                            <div class="card-header bg primary text-white">
                                <h5> Alterar Dados</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="novoUsuario" method="post" action="alterarDados.php">
                                <input type="hidden" name="acao" value="salvar"/>
                                <div class="form-group mb-3">
                                    <label for="nomeUsuario"> Nome* </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="bi-person-circle"></i>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" id="nomeUsuario" name="nomeUsuario"
                                            placeholder="Digite seu Nome" value="<?= $nomeUsuario ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label for="mailUsuario"> E-mail* </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="bi-at"></i>
                                                    </div>
                                                </div>
                                                <input type="email" class="form-control" id="mailUsuario"
                                                    name="mailUsuario" placeholder="Digite seu E-mail" value="<?= $mailUsuario ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label for="mail2Usuario"> Confirme E-mail* </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="bi-at"></i>
                                                    </div>
                                                </div>
                                                <input type="email" class="form-control" id="mail2Usuario"
                                                    name="mail2Usuario" placeholder="Confirme seu E-mail" value="<?= $mail2Usuario ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label for="senhaAtualUsuario"> Senha Atual* </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="bi-key-fill"></i>
                                                    </div>
                                                </div>
                                                <input type="password" class="form-control" id="senhaAtualUsuario"
                                                    name="senhaAtualUsuario" placeholder="Digite sua Senha Atual"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label for="senhaUsuario"> Nova Senha* </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="bi-key-fill"></i>
                                                    </div>
                                                </div>
                                                <input type="password" class="form-control" id="senhaUsuario"
                                                    name="senhaUsuario" placeholder="Digite sua Nova Senha" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label for="senha2Usuario"> Confirme sua Nova Senha* </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="bi-key-fill"></i>
                                                    </div>
                                                </div>
                                                <input type="password" class="form-control" id="senha2Usuario"
                                                    name="senha2Usuario" placeholder="Confirme sua Nova Senha" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="campo_senha">
                                    <div class="col-sm barra_senha"></div>
                                    <div class="col-sm"></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm">
                                        <button type="submit" class="btn btn-primary btn-block btn-lg"> Salvar
                                        </button>
                                    </div>
                                </div>
                            </form>
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
    <script>
        jQuery.validator.setDefaults({
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {

                $(element).removeClass('is-invalid');
            }
        });
        $(document).ready(function () {
            jQuery(document).ready(function () {
                "use strict";
                var options = {};
                options.ui = {
                    container: "#campo_senha",
                    viewports: {
                        progress: ".barra_senha"
                    },
                    showVerdictsInsideProgressBar: true
                };
                $('#senhaUsuario').pwstrength(options);
            });
            $("#novoUsuario").validate({
                rules: {
                    nomeUsuario: {
                        minlenght: 5
                    },
                    mail2Usuario: {
                        equalTo: "#mailUsuario"
                    },
                    senha2Usuario: {
                        equalTo: "#senhaUsuario"
                    },
                    senhaUsuario: {
                        minlenght: 8
                    }
                }
            });
        });
    </script>
</body>

</html>