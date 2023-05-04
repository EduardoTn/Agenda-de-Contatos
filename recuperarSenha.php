<?php
    require 'common/PHPMailer.php';
    require 'common/SMTP.php';
    require 'common/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;


 if(isset($_POST['mailUsuario'])){
    $mailUsuario = $_POST['mailUsuario'];
    include "conectaBanco.php";
    include "common/gerarSenha.php";

    $sqlUsuario = "SELECT codigoUsuario, nomeUsuario FROM usuarios WHERE mailUsuario=:mailUsuario LIMIT 1";
    $sqlUsuarioST = $conexao->prepare($sqlUsuario);

    $sqlUsuarioST->bindValue(':mailUsuario', $mailUsuario);
    $sqlUsuarioST->execute();

    $quantidadeUsuarios = $sqlUsuarioST->rowCount();
    if($quantidadeUsuarios == 1){
        $resultadoUsuario = $sqlUsuarioST->fetchAll();
        list($codigoUsuario, $nomeUsuario) = $resultadoUsuario[0];

        $nomeCompletoUsuario = explode(' ', $nomeUsuario);
        $nomeUsuario = $nomeCompletoUsuario[0];

        $novaSenha = gerarSenha(8);
        $novaSenhaMD5 = md5($novaSenha);

        $sqlAlterarSenha = "UPDATE usuarios SET senhaUsuario=:novaSenhaMD5 WHERE codigoUsuario=:codigoUsuario";
        $sqlAlterarSenhaST = $conexao->prepare($sqlAlterarSenha);

        $sqlAlterarSenhaST->bindValue(":novaSenhaMD5", $novaSenhaMD5);
        $sqlAlterarSenhaST->bindValue(":codigoUsuario", $codigoUsuario);
        if($sqlAlterarSenhaST->execute()){
            include 'common/constantes.php';
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Host = "smtp@gmail.com";
            $mail->Username = GUSER;
            $mail->Password = GPWD;

            $mensagem = "Olá, $nomeUsuario!<br/><br/>
            Recebemos sua solicitação de alteração de senha do sistema Agenda de Contatos. <br/><br/>
            Sua nova senha é: <span style=\"font-weight: bold; color: #FF0000\">$novaSenha</span>
            Para sua segurança, altere sua senha no primeiro acesso ao sistema. <br/><br/>
            Atenciosamente,<br/>
            Equipe de Desenvolvimento.";

            $mail->isHTML(true);
            $mail->CharSet = "UTF-8";
            $mail->setFrom(GUSER,GNAME);
            $mail->addAddress($mailUsuario);
            $mail->Subject = 'Recuperação de Senha';
            $mail->Body = $mensagem;

            if($mail->send()){
                header("Location: index.php?codMsg=008");
            }else{
                header("Location: index.php?codMsg=007");
            }
        }else{
            header("Location: index.php?codMsg=006");
        }
    }else{
        header("Location: index.php?codMsg=005");
    }
}else{
    header("Location: index.php?codMsg=004");
}
?>