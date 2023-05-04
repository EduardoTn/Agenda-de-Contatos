<?php
include "conectaBanco.php";
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
    <div class="h-100 row align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-sm"></div>
                <div class="col-sm-6">
                    <?php
                    $flagErro = FALSE;
                    if (isset($_POST['acao'])) {
                        $acao = $_POST['acao'];
                        if ($acao == 'salvar') {
                            $nomeUsuario = $_POST['nomeUsuario'];
                            $mailUsuario = $_POST['mailUsuario'];
                            $mail2Usuario = $_POST['mail2Usuario'];
                            $senhaUsuario = $_POST['senhaUsuario'];
                            $senha2Usuario = $_POST['senha2Usuario'];

                            if (!empty($nomeUsuario) && !empty($mailUsuario) && !empty($senhaUsuario) && !empty($mail2Usuario) && !empty($senha2Usuario) && $senhaUsuario == $senha2Usuario && $mailUsuario == $mail2Usuario && strlen($nomeUsuario)>= 5 && strlen($senhaUsuario)>= 8) {

                                $sqlUsuarios = "SELECT codigoUsuario FROM usuarios WHERE mailUsuario=:mailUsuario";
                                $sqlUsuariosST = $conexao->prepare($sqlUsuarios);
                                $sqlUsuariosST->bindValue(':mailUsuario', $mailUsuario);
                                $sqlUsuariosST->execute();
                                $quantidadeUsuarios = $sqlUsuariosST->rowCount();

                                if ($quantidadeUsuarios == 0) {
                                    $senhaUsuarioMd5 = md5($senhaUsuario);
                                    $sqlNovoUsuario = "INSERT INTO usuarios (nomeUsuario, mailUsuario, senhaUsuario) VALUES (:nomeUsuario, :mailUsuario, :senhaUsuario)";

                                    $sqlNovoUsuarioST = $conexao->prepare($sqlNovoUsuario);
                                    $sqlNovoUsuarioST->bindValue(":nomeUsuario", $nomeUsuario);
                                    $sqlNovoUsuarioST->bindValue(":mailUsuario", $mailUsuario);
                                    $sqlNovoUsuarioST->bindValue(":senhaUsuario", $senhaUsuarioMd5);

                                    if ($sqlNovoUsuarioST->execute()) {
                                        $mensagemAcao = "Novo usuario cadastrado com sucesso!!";
                                    } else {
                                        $flagErro = TRUE;
                                        $mensagemAcao = "Erro ao cadastrar usuario: " . $sqlNovoUsuarioST->errorCode();
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
                    }
                    ?>
                    <div class="card">
                        <div class="card-border bg-primary">
                            <div class="card-header bg primary text-white">
                                <h5> Cadastro Novo Usuario</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="novoUsuario" method="post" action="novoUsuario.php">
                                <input type="hidden" name="acao" value="salvar">
                                <div class="form-group mb-3">
                                    <label for="nomeUsuario"> Nome* </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="bi-person-circle"></i>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" id="nomeUsuario" name="nomeUsuario" placeholder="Digite seu Nome" value="<?= ($flagErro) ? $nomeUsuario : "" ?>" required>
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
                                                <input type="email" class="form-control" id="mailUsuario" name="mailUsuario" placeholder="Digite seu E-mail" value="<?= ($flagErro) ? $mailUsuario : "" ?>" required>
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
                                                <input type="email" class="form-control" id="mail2Usuario" name="mail2Usuario" placeholder="Confirme seu E-mail" value="<?= ($flagErro) ? $mail2Usuario : "" ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label for="senhaUsuario"> Senha* </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="bi-key-fill"></i>
                                                    </div>
                                                </div>
                                                <input type="password" class="form-control" id="senhaUsuario" name="senhaUsuario" placeholder="Digite sua Senha" value="<?= ($flagErro) ? $senhaUsuario : "" ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label for="senha2Usuario"> Confirme sua Senha* </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="bi-key-fill"></i>
                                                    </div>
                                                </div>
                                                <input type="password" class="form-control" id="senha2Usuario" name="senha2Usuario" placeholder="Confirme sua Senha" value="<?= ($flagErro) ? $senha2Usuario : "" ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="campo_senha">
                                    <div class="col-sm barra_senha"></div>
                                    <div class="col-sm"></div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-sm">
                                        <button type="submit" class="btn btn-primary btn-block btn-lg"> Cadastrar
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                <div class="col-sm">
                                    <a class="btn btn-block btn-link" href="index.php">Voltar</a>
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
    <script>
        jQuery.validator.setDefaults({
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {

                $(element).removeClass('is-invalid');
            }
        });
        $(document).ready(function() {
            jQuery(document).ready(function() {
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