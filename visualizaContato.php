<?php
session_start();
$verificaUsuarioLogado = $_SESSION['verificaUsuarioLogado'];
if(!$verificaUsuarioLogado){
    header("Location: index.php?codMsg003");
}else{
    include "conectaBanco.php";
    include "common/formataData.php";
    $codigoUsuarioLogado = $_SESSION['codigoUsuarioLogado'];


    if(isset($_GET['codigoContato'])){
        $codigoContato = $_GET['codigoContato'];

        $sqlContato = "SELECT * FROM contatos WHERE codigoContato =:codigoContato AND codigoUsuario =:codigoUsuario";
        $sqlContatoST = $conexao->prepare($sqlContato);
        $sqlContatoST->bindValue(':codigoContato',$codigoContato);
        $sqlContatoST->bindValue(':codigoUsuario',$codigoUsuarioLogado);
        $sqlContatoST->execute();
        $quantidadeContatos = $sqlContatoST->rowCount();
        if($quantidadeContatos == 1){
            $resultadoContato = $sqlContatoST->fetchAll();
            list($codigoContato, $codigoUsuario, $nomeContato, $nascimentoContato, $sexoContato, $mailContato, $fotoContato, $telefone1Contato, $telefone2Contato, $telefone3Contato, $telefone4Contato, $logradouroContato, $complementoContato, $bairroContato, $estadoContato, $cidadeContato) = $resultadoContato[0];

            $nascimentoContato = formataData($nascimentoContato);
            if($sexoContato == 'M'){
                $sexoContato = 'Masculino';
            }else if ($sexoContato == 'F'){
                $sexoContato = 'Feminino';
            }

            $sqlEndereco = 'SELECT c.nomeCidade, e.nomeEstado FROM cidades as c, estados as e WHERE c.codigoCidade =:cidadeContato AND e.codigoEstado=:estadoContato';
            $sqlEnderecoST = $conexao->prepare($sqlEndereco);
            $sqlEnderecoST->bindValue(':cidadeContato', $cidadeContato);
            $sqlEnderecoST->bindValue(':estadoContato', $estadoContato);

            $sqlEnderecoST->execute();
            $resultadoEndereco = $sqlEnderecoST->fetchAll();
            list($cidadeContato, $estadoContato) = $resultadoEndereco[0];

            echo "<h5 class=\"text-primary\"> Dados Pessoais</h5>
            <hr>
            <div class=\"row\">
                <div class=\"col-sm\">
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            $nomeContato
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            $nascimentoContato
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            $sexoContato
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            $mailContato
                        </div>
                    </div>
                </div>
                <div class=\"col-sm\">
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            <div class=\"row\">
                                <div class=\"col-sm\">
                                    <img src=\"$fotoContato\" alt=\"$nomeContato\">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <h5 class=\"text-primary\"> Telefones</h5>
            <hr>
            <div class=\"row\">
                <div class=\"col-sm\">
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            $telefone1Contato
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            $telefone2Contato
                        </div>
                    </div>
                </div>
                <div class=\"col-sm\">
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            $telefone3Contato
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            $telefone4Contato
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <h5 class=\"text-primary\"> Endereço</h5>
            <hr>
            <div class=\"row\">
                <div class=\"col-sm\">
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            $logradouroContato
                        </div>
                    </div>
                </div>
            </div>
            <div class=\"row\">
                <div class=\"col-sm\">
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            $complementoContato
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            $estadoContato
                        </div>
                    </div>
                </div>
                <div class=\"col-sm\">
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            $bairroContato
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-sm\">
                            $cidadeContato
                        </div>
                    </div>
                </div>
            </div>";

        }
    }
}
?>