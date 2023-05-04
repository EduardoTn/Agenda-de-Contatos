<?php
session_start();
$verificaUsuarioLogado = $_SESSION['verificaUsuarioLogado'];
if (!$verificaUsuarioLogado) {
    header("Location: index.php?codMsg=003");
} else {
    $codigoUsuarioLogado = $_SESSION['codigoUsuarioLogado'];
    $nomeUsuarioLogado = $_SESSION['nomeUsuarioLogado'];
    include "conectaBanco.php";
    include "common/formataData.php";
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
    <script src="js/dateITA.js"></script>
    <script src="js/jquery.mask.js"></script>

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
                        <a class="nav-link dropdown-toggle" id="menuCadastros" role="button" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi-card-list"></i> Cadastros</a>
                        <div class="dropdown-menu" aria-labelledby="menuCadastros">
                            <a class="dropdown-item" href="cadastroContato.php">
                                <i class="bi-person-fill"></i> Novo Contato</a>
                            <a class="dropdown-item" href="listaContatos.php">
                                <i class="bi-list-ul"></i> Lista de Contatos</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="menuConta" role="button" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                        <?php
                        $flagErro = FALSE;
                        $flagSucesso = FALSE;
                        $mostrarMensagem = FALSE;

                        $dadosContato = array('codigoContato', 'nomeContato', 'mailContato', 'nascimentoContato', 'sexoContato', 'fotoContato', 'fotoAtualContato', 'telefone1Contato', 'telefone2Contato', 'telefone3Contato', 'telefone4Contato', 'logradouroContato', 'complementoContato', 'bairroContato', 'estadoContato', 'cidadeContato');

                        foreach ($dadosContato as $campo) {
                            $$campo = "";
                        }
                        if (isset($_POST['codigoContato'])) {
                            $codigoContato = $_POST['codigoContato'];
                            $nomeContato = addslashes($_POST['nomeContato']);
                            $mailContato = $_POST['mailContato'];
                            $nascimentoContato = $_POST['nascimentoContato'];
                            if (isset($_POST['sexoContato'])) {
                                $sexoContato = $_POST['sexoContato'];
                            } else {
                                $sexoContato = "";
                            }
                            $fotoContato = $_FILES['fotoContato'];
                            $fotoAtualContato = $_POST['fotoAtualContato'];
                            $telefone1Contato = $_POST['telefone1Contato'];
                            $telefone2Contato = $_POST['telefone2Contato'];
                            $telefone3Contato = $_POST['telefone3Contato'];
                            $telefone4Contato = $_POST['telefone4Contato'];
                            $logradouroContato = addslashes($_POST['logradouroContato']);
                            $bairroContato = addslashes($_POST['bairroContato']);
                            $complementoContato = addslashes($_POST['complementoContato']);
                            $estadoContato = $_POST['estadoContato'];
                            $cidadeContato = $_POST['cidadeContato'];
                            $telefonesContato = array($telefone1Contato, $telefone2Contato, $telefone3Contato, $telefone4Contato);
                            $telefonesFiltradosContato = array_filter($telefonesContato);
                            $telefonesValidadosContato = preg_grep('/^\(\d{2}\)\s\d{4,5}\-\d{4}$/', $telefonesContato);

                            if ($telefonesFiltradosContato === $telefonesValidadosContato) {
                                $erroTelefones = FALSE;
                            } else {
                                $erroTelefones = true;
                            }


                            if (empty($nomeContato) || empty($sexoContato) || empty($mailContato) || empty($telefone1Contato) || empty($logradouroContato) || empty($complementoContato) || empty($bairroContato) || empty($cidadeContato) || empty($estadoContato) || empty($nomeContato) || strlen($nomeContato) < 5) {
                                $flagErro = true;
                                $mensagemAcao = "Preencha todos os campos obrigatorios corretamente. (*), Nome 5 caracteres minimo";
                            } else if (!empty($nascimentoContato) && !preg_match('/^(0?[1-9]|[1,2][0-9]|3[0,1])[\/](0?[1-9]|1[0,1,2])[\/]\d{4}$/', $nascimentoContato)) {
                                $flagErro = 'true';
                                $mensagemAcao = 'Digite a data corretamente (DD/MM/AAAA)';
                            } else if (!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/", $mailContato)) {
                                $flagErro = true;
                                $mensagemAcao = "Verifique o email informado!!";
                            } else if ($erroTelefones) {
                                $flagErro = true;
                                $mensagemAcao = "Os campos de telefone devem ser no formato (xx) xxxxx-xxxx";
                            } else if ($fotoContato['error'] != 4) {
                                if (!in_array($fotoContato['type'], array('imagem/jpg', 'image/jpeg', 'image/png')) || $fotoContato['size'] > 2000000) {
                                    $flagErro = true;
                                    $mensagemAcao = 'As fotos devem estar no formato correto e ter no maximo 2MB';
                                } else {
                                    list($larguraFoto, $alturaFoto) = getimagesize($fotoContato['tmp_name']);
                                    if ($larguraFoto > 500 || $alturaFoto > 200) {
                                        $flagErro = true;
                                        $mensagemAcao = "As dimensões da foto devem ser no maximo 500x200 pixels";
                                    }
                                }
                            }


                            if (!$flagErro) {
                                if (empty($codigoContato)) {
                                    $sqlContato = 'INSERT INTO contatos (codigoUsuario, nomeContato, nascimentoContato, sexoContato, mailContato, fotoContato, telefone1Contato, telefone2Contato, telefone3Contato, telefone4Contato, logradouroContato, complementoContato, bairroContato, cidadeContato, estadoContato) 
                                                    VALUES (:codigoUsuario, :nomeContato, :nascimentoContato, :sexoContato, :mailContato, :fotoContato, :telefone1Contato, :telefone2Contato, :telefone3Contato, :telefone4Contato, :logradouroContato, :complementoContato, :bairroContato, :cidadeContato, :estadoContato)';
                                    $sqlContatoST = $conexao->prepare($sqlContato);
                                    $sqlContatoST->bindValue(':codigoUsuario', $codigoUsuarioLogado);
                                    $sqlContatoST->bindValue(':nomeContato', $nomeContato);
                                    $nascimentoContato = formataData($nascimentoContato);
                                    $sqlContatoST->bindValue(':nascimentoContato', $nascimentoContato);
                                    $sqlContatoST->bindValue(':sexoContato', $sexoContato);
                                    $sqlContatoST->bindValue(':mailContato', $mailContato);
                                    $sqlContatoST->bindValue(':telefone1Contato', $telefone1Contato);
                                    $sqlContatoST->bindValue(':telefone2Contato', $telefone2Contato);
                                    $sqlContatoST->bindValue(':telefone3Contato', $telefone3Contato);
                                    $sqlContatoST->bindValue(':telefone4Contato', $telefone4Contato);
                                    $sqlContatoST->bindValue(':logradouroContato', $logradouroContato);
                                    $sqlContatoST->bindValue(':complementoContato', $complementoContato);
                                    $sqlContatoST->bindValue(':bairroContato', $bairroContato);
                                    $sqlContatoST->bindValue(':cidadeContato', $cidadeContato);
                                    $sqlContatoST->bindValue(':estadoContato', $estadoContato);

                                    if ($fotoContato['error'] == 0) {
                                        $extensaoFoto = pathinfo($fotoContato['name'], PATHINFO_EXTENSION);
                                        $nomeFoto = "fotos/" . strtotime(date("Y-m-d H:i:s")) . $codigoUsuarioLogado . '.' . $extensaoFoto;

                                        if (copy($fotoContato['tmp_name'], $nomeFoto)) {
                                            $fotoEnviada = true;
                                        } else {
                                            $fotoEnviada = false;
                                        }
                                        $sqlContatoST->bindValue(':fotoContato', $nomeFoto);
                                    } else {
                                        $sqlContatoST->bindValue(':fotoContato', '');
                                        $fotoEnviada = false;
                                    }
                                    if ($sqlContatoST->execute()) {
                                        $flagSucesso = true;
                                        $mensagemAcao = 'Novo contato cadastrado com sucesso';
                                    } else {
                                        $flagErro = true;
                                        $mensagemAcao = "Erro ao cadastrar novo contato. Codigo erro: $sqlContatoST->errorCode()";

                                        $nascimentoContato = formataData($nascimentoContato);
                                        if ($fotoEnviada) {
                                            unlink($nomeFoto);
                                        }
                                    }
                                } else {
                                    $sqlContato = "UPDATE contatos SET nomeContato=:nomeContato, nascimentoContato=:nascimentoContato, sexoContato=:sexoContato, mailContato=:mailContato, fotoContato=:fotoContato, telefone1Contato=:telefone1Contato, telefone2Contato=:telefone2Contato, telefone3Contato=:telefone3Contato, telefone4Contato=:telefone4Contato, logradouroContato=:logradouroContato, complementoContato=:complementoContato, bairroContato=:bairroContato, cidadeContato=:cidadeContato, estadoContato=:estadoContato 
                                                    WHERE codigoContato=:codigoContato AND codigoUsuario=:codigoUsuario";
                                    $sqlContatoST = $conexao->prepare($sqlContato);
                                    $sqlContatoST->bindValue(':codigoContato', $codigoContato);
                                    $sqlContatoST->bindValue(':codigoUsuario', $codigoUsuarioLogado);
                                    $sqlContatoST->bindValue(':nomeContato', $nomeContato);
                                    $nascimentoContato = formataData($nascimentoContato);
                                    $sqlContatoST->bindValue(':nascimentoContato', $nascimentoContato);
                                    $sqlContatoST->bindValue(':sexoContato', $sexoContato);
                                    $sqlContatoST->bindValue(':mailContato', $mailContato);
                                    $sqlContatoST->bindValue(':telefone1Contato', $telefone1Contato);
                                    $sqlContatoST->bindValue(':telefone2Contato', $telefone2Contato);
                                    $sqlContatoST->bindValue(':telefone3Contato', $telefone3Contato);
                                    $sqlContatoST->bindValue(':telefone4Contato', $telefone4Contato);
                                    $sqlContatoST->bindValue(':logradouroContato', $logradouroContato);
                                    $sqlContatoST->bindValue(':complementoContato', $complementoContato);
                                    $sqlContatoST->bindValue(':bairroContato', $bairroContato);
                                    $sqlContatoST->bindValue(':cidadeContato', $cidadeContato);
                                    $sqlContatoST->bindValue(':estadoContato', $estadoContato);

                                    if ($fotoContato["error"] == 0) {
                                        $extensaoFoto = pathinfo($fotoContato['name'], PATHINFO_EXTENSION);
                                        $nomeFoto = "fotos/" . strtotime(date("Y-m-d H:i:s")) . $codigoUsuarioLogado . '.' . $extensaoFoto;

                                        if (copy($fotoContato['tmp_name'], $nomeFoto)) {
                                            $fotoEnviada = true;
                                        } else {
                                            $fotoEnviada = false;
                                        }
                                        $sqlContatoST->bindValue(':fotoContato', $nomeFoto);
                                    } else {
                                        $sqlContatoST->bindValue(':fotoContato', $fotoAtualContato);
                                        $fotoEnviada = false;
                                    }
                                    if ($sqlContatoST->execute()) {
                                        if ($fotoEnviada && !empty($fotoAtualContato)) {
                                            unlink($fotoAtualContato);
                                        }
                                        $flagSucesso = true;
                                        $mensagemAcao = 'Contato Atualizado com sucesso!';
                                        $nascimentoContato = formataData($nascimentoContato);
                                    } else {
                                        $flagErro = true;
                                        $mensagemAcao = "Erro ao atualizar contato. Codigo erro: $sqlContatoST->errorCode()";

                                        $nascimentoContato = formataData($nascimentoContato);
                                        if ($fotoEnviada) {
                                            unlink($nomeFoto);
                                        }
                                    }
                                }
                            }
                        } else {
                            if (isset($_GET['codigoContato'])) { // abrir contato ja existente
                                $codigoContato = $_GET['codigoContato'];

                                $sqlContato = "SELECT * FROM contatos WHERE codigoContato=:codigoContato AND 
                                                codigoUsuario=:codigoUsuario";

                                $sqlContatoST = $conexao->prepare($sqlContato);
                                $sqlContatoST->bindValue(':codigoContato', $codigoContato);
                                $sqlContatoST->bindValue(':codigoUsuario', $codigoUsuarioLogado);

                                $sqlContatoST->execute();
                                $quantidadeContatos = $sqlContatoST->rowCount();

                                if ($quantidadeContatos == 1) {
                                    $resultadoContato = $sqlContatoST->fetchAll();

                                    list(
                                        $codigoContato, $codigoUsuario, $nomeContato, $nascimentoContato, $sexoContato,
                                        $mailContato, $fotoContato, $telefone1Contato, $telefone2Contato, $telefone3Contato,
                                        $telefone4Contato, $logradouroContato, $complementoContato, $bairroContato, $estadoContato,
                                        $cidadeContato
                                    ) = $resultadoContato[0];

                                    $fotoAtualContato = $fotoContato;

                                    $nascimentoContato = formataData($nascimentoContato);
                                } else {
                                    $flagErro = True;
                                    $mensagemAcao = "Contato não cadastrado.";
                                }
                            }
                        }
                        if ($flagErro) {
                            $classeMensagem = "alert-danger";
                            $mostrarMensagem = True;
                        } else if ($flagSucesso) {
                            $classeMensagem = "alert-success";
                            $mostrarMensagem = True;
                        }

                        if ($mostrarMensagem) {
                            echo "<div class=\"alert $classeMensagem alert-dismissible fade show \" role=\"alert\">
                            $mensagemAcao
                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                              <span aria-hidden=\"true\">&times;</span>
                            </button>
                          </div>";
                        }
                        ?>
                        <div class="card-header bg-primary">
                            <div class="card-header bg primary text-white">
                                <h5> Cadastro Novo Usuario</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="cadastroContato" method="post" enctype="multipart/form-data" action="cadastroContato.php">
                                <input type="hidden" name="codigoContato" value="">
                                <input type="hidden" name="fotoAtualContato" value="">
                                <h5 class="text-primary"> Dados Pessoais</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="form-group">
                                                    <label for="nomeContato"> Nome*</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <i class="bi-person-circle"></i>
                                                            </div>
                                                            <input class="form-control" type="text" name="nomeContato" id="nomeContato" placeholder="Digite o Nome" value="<?= $nomeContato ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="form-group">
                                                    <label for="nascimentoContato"> Contato</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <i class="bi-calendar-date"></i>
                                                            </div>
                                                            <input class="form-control" type="text" name="nascimentoContato" id="nascimentoContato" placeholder="DD/MM/AAAA" value="<?= $nascimentoContato ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="form-group">
                                                    <label for="sexoContato"> Sexo*</label>
                                                    <div class="input-group">
                                                        <div class="form-check form-check-inline">
                                                            <?php
                                                            if ($sexoContato == 'M') {
                                                                $checkedMasculino = 'checked';
                                                                $checkedFeminino = '';
                                                            } else if ($sexoContato == 'F') {
                                                                $checkedMasculino = '';
                                                                $checkedFeminino = 'checked';
                                                            } else {
                                                                $checkedMasculino = '';
                                                                $checkedFeminino = '';
                                                            }
                                                            ?>
                                                            <input class="form-check-input" type="radio" name="sexoContato" id="sexoMasculino" value="M" <?= $checkedMasculino ?>>
                                                            <label class="form-check-label" for="sexoMasculino">Masculino</label>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                                            <input class="form-check-input" type="radio" name="sexoContato" id="sexoFeminino" value="F" <?= $checkedFeminino ?>>
                                                            <label class="form-check-label" for="sexoFeminino">Feminino</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="form-group">
                                                    <label for="mailContato"> Email</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <i class="bi-at"></i>
                                                            </div>
                                                            <input class="form-control" type="email" name="mailContato" id="mailContato" placeholder="Digite o Email" value="<?= $mailContato ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="row">
                                                    <div class="col-sm">
                                                        <div class="form-group">
                                                            <label for="fotoContato"> Foto</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <div class="input-group-text">
                                                                        <i class="bi-file-earmark-person"></i>
                                                                    </div>
                                                                    <div class="custom-file">
                                                                        <input class="custom-file-input" type="file" name="fotoContato" id="fotoContato">
                                                                        <label for="fotoContato" class="custom-file-label"> Selecione a
                                                                            Foto... </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="text-primary"> Telefone</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="form-group">
                                                    <label for="telefone1Contato"> Telefone*</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <i class="bi-phone"></i>
                                                            </div>
                                                            <input class="form-control mascaraTelefone" type="text" name="telefone1Contato" id="telefone1Contato" placeholder="(xx) xxxxx-xxxx" value="<?= $telefone1Contato ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="form-group">
                                                    <label for="telefone2Contato"> Telefone</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <i class="bi-phone"></i>
                                                            </div>
                                                            <input class="form-control mascaraTelefone" type="text" name="telefone2Contato" id="telefone2Contato" placeholder="(xx) xxxxx-xxxx" value="<?= $telefone2Contato ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="form-group">
                                                    <label for="telefone3Contato"> Telefone</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <i class="bi-phone"></i>
                                                            </div>
                                                            <input class="form-control mascaraTelefone" type="text" name="telefone3Contato" id="telefone3Contato" placeholder="(xx) xxxxx-xxxx" value="<?= $telefone3Contato ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="form-group">
                                                    <label for="telefone4Contato"> Telefone</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <i class="bi-phone"></i>
                                                            </div>
                                                            <input class="form-control mascaraTelefone" type="text" name="telefone4Contato" id="telefone4Contato" placeholder="(xx) xxxxx-xxxx" value="<?= $telefone4Contato ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="text-primary"> Endereço</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="form-group">
                                                    <label for="logradouroContato"> Logradouro*</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <i class="bi-map"></i>
                                                            </div>
                                                            <input class="form-control" type="text" name="logradouroContato" id="logradouroContato" placeholder="Rua, Avenida, Etc..." value="<?= $logradouroContato ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="form-group">
                                                    <label for="complementoContato"> Complemento</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <i class="bi-pin"></i>
                                                            </div>
                                                            <input class="form-control" type="text" name="complementoContato" id="complementoContato" placeholder="Numero, Quadra, Lote..." value="<?= $complementoContato ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="form-group">
                                                    <label for="estadoContato"> Estado*</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <i class="bi-globe"></i>
                                                            </div>
                                                            <select class="form-control" name="estadoContato" id="estadoContato" required>
                                                                <option value=""> Escolha o Estado</option>
                                                                <?php
                                                                $sqlEstados = "SELECT codigoEstado, nomeEstado FROM estados";

                                                                $resultadoEstados = $conexao->query($sqlEstados)->fetchAll();

                                                                foreach ($resultadoEstados as list($codigoEstado, $nomeEstado)) {
                                                                    if ($estadoContato == $codigoEstado) {
                                                                        $selected = 'selected';
                                                                    } else {
                                                                        $selected = '';
                                                                    }
                                                                    echo "<option value=\"$codigoEstado\" $selected >$nomeEstado</option>\n";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="form-group">
                                                    <label for="bairroContato"> Bairro</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <i class="bi-pin"></i>
                                                            </div>
                                                            <input class="form-control" type="text" name="bairroContato" id="bairroContato" placeholder="Digite o Bairro" value="<?= $bairroContato ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm">
                                                <div class="form-group">
                                                    <label for="cidadeContato"> Cidade*</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <i class="bi-globe"></i>
                                                            </div>
                                                            <select class="form-control" name="cidadeContato" id="cidadeContato" required>
                                                                <option value=""> Escolha a Cidade</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm">
                                        <button type="submit" class="btn btn-primary btn-block btn-lg"> Salvar </button>
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
    <div class="modal fade" id="modalSobreAplicacao" tabindex="-1" role="dialog" aria-labelledby="sobreAplicacao" aria-hidden="true">
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
            $("#cadastroContato").validate({
                rules: {
                    nomeContato: {
                        minlenght: 5
                    },
                    nascimentoContato: {
                        dateITA: true
                    },
                    sexoContato: {
                        required: true
                    }
                }
            });
            $("#nascimentoContato").mask("00/00/0000");

            var SPMaskBehavior = function(val) {
                    return val.replace(/\D/g, '').lenght === 11 ? '(00) 00000-0000' : '(00) 00000-0009';
                },
                spOptions = {
                    onKeyPress: function(val, e, field, options) {
                        field.mask(SPMaskBehavior.apply({}, arguments), options);
                    }
                };
            $(".mascaraTelefone").mask(SPMaskBehavior, spOptions);

            $("#estadoContato").change(function() {
                $("#cidadeContato").html("<option>Carregando...</option>");
                $("#cidadeContato").load('listaCidades.php?codigoEstado=' + $("#estadoContato").val());
            });

            <?php
            if (!empty($estadoContato) && !empty($cidadeContato)) {
                echo " $(\"#cidadeContato\").html(\"<option>Carregando...</option>\");
                    $(\"#cidadeContato\").load('listaCidades.php?codigoEstado=" . $estadoContato . "&codigoCidade=" . $cidadeContato . "');";
            }
            ?>
        });
    </script>
</body>

</html>