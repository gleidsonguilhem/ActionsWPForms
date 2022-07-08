<!--
Author: Gleidson G. Guilhem
Created: 15/05/2022
Description: Page php to bring data from db7_forms. Each request (row) can be approved by HR and then, approved by manager.
Each approval request sends an email to HR or Manager. Once a request is approved by both HR and manager, the cycle is completed and 
no more actions is allowed.
-->
<!DOCTYPE html>
<html>
<head>
    <title>Crud Wordpress</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
</head>
<body>
    <div class="container">
        <!-- Opening main table -->
        <table id="datatableid" class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Post ID</th>
                    <th scope="col">Agente</th>
                    <th scope="col">Data Inicio</th>
                    <th scope="col">Data Termino</th>
                    <th scope="col">Total Dias</th>
                    <th scope="col" colspan="2">Ações</th>
                    <th scope="col" colspan="2">Status</th>
                </tr>
            </thead>
            <?php
                use function PHPSTORM_META\type;

                //Constant to get self file name to be used in link redirection
                $PAGE_NAME = basename(__FILE__, '.php');

                require_once('../wp-load.php');
                require 'sendmail.php';

                //Get Admin path to retrieve ID from php to WordPress
                $admin_php_path = ABSPATH . '/wp-admin/admin.php';

                $tabela = $wpdb->prefix . "db7_forms";
                $resultado = $wpdb->get_results("SELECT * FROM " . $tabela, OBJECT);

                //initializing count to bring data from database
                $i = 0;
                foreach ($resultado as $valores) {
                    //Unserializind data
                    $rows  = unserialize($resultado[$i]->form_value);
                    //Converting to Array
                    $array = json_decode(json_encode($rows), true);

                    //Extracting Name
                    $row_name = $array['nome'];
                    $nome = implode(" ", $row_name);

                    $row_approved = $array['Aprovado'];

                    //Extracting Aprovado
                    if ($row_approved) {
                        $status = implode(" ", $row_approved);
                    } else {
                        $status = '-';
                    }

                    //Extracting Date in (dd/mm/YYYY)
                    $sDate = date('d/m/Y', strtotime($valores->form_date));
                    $id = $valores->form_id;
                    $postID = $valores->form_post_id;

                    //Extracting Dat inicio, Dat Termino
                    $datIni = date('d/m/Y', strtotime($array['DataInicio']));
                    $datFim = date('d/m/Y', strtotime($array['DataTermino']));
                    $totalDias = $array['totalDias'];

                    //Calculate number of days
                    $diff = date(strtotime($array['DataInicio'])) - date(strtotime($array['DataTermino']));
                    $dateDiff = ceil(abs($diff / 86400));

                    //Showing Data (Populating table)
            ?>
            <tr>
                <td><b><?php echo $id; ?></b></td>
                <td><?php echo $postID; ?></td>
                <td><?php echo $nome; ?></td>
                <td><?php echo $datIni; ?></td>
                <td><?php echo $datFim; ?></td>
                <td><?php echo $dateDiff; ?></td>
                <td colspan='2'>
                    <button type="button" class="btn btn-info btn-sm atualizarbtn"><span class="bi bi-pencil-square"> Atualizar</button>
                    <button type="button" class="btn btn-danger btn-sm deletebtn"><span class="bi bi-person-x"> Apagar</button>
                </td>
                <td>
                    <?php
                    if ($status == "1") {
                        echo "<font style='font-weight: bold; color: blue;'><i class='bi bi-clipboard2-fill'> Aprovado RH </i>";
                    } else if ($status == "2") {
                        echo "<font style='font-weight: bolder; color: green';><i class='bi bi-clipboard2-data-fill'> Finalizado </i>";
                    } else {
                        echo "<font style='font-weight: lighter; color: gray';><i class='bi bi-clipboard2-fill'> -</i>";
                    }
                    ?>
                </td>
            </tr>
        <?php
            $i++;
        }
        ?>
        <!-- Closing main table -->
        </div>
        </table
        <?php
            $i = 0;
            //variable t0 define modal array size
            $size = 0;
            foreach ($resultado as $valores) {
                //Unserializind data
                $rows  = unserialize($resultado[$i]->form_value);
                //Converting to Array
                $array = json_decode(json_encode($rows), true);

                //Extracting Name
                $row_name = $array['nome'];
                $nome = implode("", $row_name);

                //Extracting Aprovado
                if ($row_approved) {
                    $status = implode("", $row_approved);
                } else {
                    $status = '-';
                }

                //Extracting Date in (dd/mm/YYYY)
                $sDate = date('d/m/Y', strtotime($valores->form_date));

                $id = $valores->form_id;
                $postId = $valores->form_post_id;

                $i++;
            }
        ?>
        <!-- ############################################## Start - Modal to Update Data ################################################ -->
        <div id="modalUpd" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
                        <h6><span class="badge badge-secondary" id="status"></span></h6>    
                        <input type="hidden" name="edit_id" id="edit_id" disabled  size="1" style='font-weight: bolder;'>
                        <table class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">PostID</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Inicio</th>
                                    <th scope="col">Termino</th>
                                    <th scope="col">Aprovar
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr>
                                        <td><label name="postID" id="postID"></td>
                                        <td><label name="nome" id="nome"></td>
                                        <td><label name="dateIni" id="dateIni">
                                        <td><label name="dateFim" id="dateFim"></td>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input" name="aprovadoHr" id="aprovadoHr" value="aprovadoHr" <?php  echo "";
                                                                                                                                                    echo " enabled"; ?>>RH<br>
                                            <input type="checkbox" class="form-check-input" name="aprovadoGr" id="aprovadoGr" value="aprovadoGr" <?php echo ""; echo " disabled"; ?>>Gerencia
                                        </td>
                                        <td width="25%"><button type="submit" id="atualizarsubmit" name="atualizarsubmit" class="btn btn-success btn-sm">Aprovar </button> <a href="<?php echo $PAGE_NAME ?>.php"><button type="button" class="btn btn-light btn-sm">Cancelar</button></a></td>
                                    </tr>
                                </form>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onClick="window.location.reload()">Fechar</button></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- ############################################## End - Modal to Update Data ################################################ -->
        <!-- ############################################## Start - Modal to DELETE Data ################################################ -->
        <div id="modalDel" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
                        <input type="hidden" name="delete_id" id="delete_id" ><br>
                        
                        <div align="center"><h4>Deseja remover esse regitro?</h4></div><br>
                        <table class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr>
                                        <td><label name="nome_del" id="nome_del"></td>
                                        </td>
                                        <td width="25%"><button type="submit" id="deletesubmit" name="deletesubmit" class="btn btn-danger btn-sm">Apagar </button> <a href="<?php echo $PAGE_NAME ?>.php"><button type="button" class="btn btn-light btn-sm">Cancelar</button></a></td>    
                                    </tr>
                                </form>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onClick="window.location.reload()">Close</button></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- ############################################## End - Modal to delete Data ################################################ -->

        <!-- ####################################################### UPDATE DATA ###################################################### -->
        <?php
            if ($_REQUEST["id"] != ""  && $_REQUEST["status"] != ""){
                $id_user = $_REQUEST["id"];
                $status_user[] = $_REQUEST["status"];

                $data = $wpdb->get_results("SELECT form_value FROM $tabela WHERE form_id = $id_user");

                $rows  = unserialize($data[0]->form_value);
                //Converting to Array
                $array = json_decode(json_encode($rows), true);

                //Extracting Name
                $row_name = $array['nome'];
                $nome_user = implode(" ", $row_name);
                //END Extracting Name

                $arr = json_decode(json_encode($data[0]),true);
                foreach($arr as $key => $value)
                {
                    $data_f = $value;
                }
                
                $unserialized = unserialize($data_f);

                $formValue = $unserialized;
                $formValue['Aprovado'] = $status_user;

                $serialized = serialize($formValue);
                
                $updated = $wpdb->query("UPDATE $tabela SET form_value='$serialized' WHERE form_id = $id_user");
                
                if($updated) {
                    echo "<script>alert(Atualizado com sucesso)</script>";
                    echo "<script>location.replace('$PAGE_NAME.php');</script>";
                    //require_once('sendmail.php');
                    sendEmail($nome_user);
                }else {
                    echo "Algo deu errado";
                }
            }
        ?>
        <!-- ####################################################### UPDATE DATA ###################################################### -->
        <!-- ####################################################### UPDATE DATA ###################################################### -->
        <?php
            if ($_REQUEST["id"] != ""  && $_REQUEST["param"] == "delete"){
                $id_user_del = $_REQUEST["id"];
                
                $deleted = $wpdb->query("DELETE FROM $tabela WHERE form_id = $id_user_del");
                
                if($deleted) {
                    echo "<script>alert(Atualizado com sucesso)</script>";
                    echo "<script>location.replace('$PAGE_NAME.php');</script>";
                }else {
                    echo "Algo deu errado";
                }
            }
        ?>
        <!-- ####################################################### UPDATE DATA ###################################################### -->
</body>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://smtpjs.com/v3/smtp.js"></script>
<script src="https://smtpjs.com/v3/smtp.js"></script>
<script>
    var edit_id = 0;
    var status = "";

    $(document).ready(function() {
        $('.atualizarbtn').on('click', function() {
            $('#modalUpd').modal('show');

            $tr = $(this).closest('tr');

            var data = $tr.children("td").map(function() {
                return $(this).text();
            }).get();

            $('#edit_id').val(data[0]);
            $('#postID').html(data[1]);
            $('#nome').html(data[2]);
            $('#dateIni').html(data[3]);
            $('#dateFim').html(data[4]);
            $('#status').html(data[7]);

            edit_id = data[0];
            aprHR = false;
            nome = data[2];
            status = data[7]

            //Control checkbox depending on the status
            if(status.trim() == "-") {
                $('input[name="aprovadoHr"]').prop('checked', false);
                $('input[name="aprovadoHr"]').attr("disabled", false); 
                $('input[name="aprovadoGr"]').prop('checked', false);
                $('input[name="aprovadoGr"]').attr("disabled", true); 
                document.getElementById("atualizarsubmit").disabled = true;
            }else if(status.trim() == "Aprovado RH") {
                $('input[name="aprovadoHr"]').prop('checked', true);
                $('input[name="aprovadoHr"]').attr("disabled", true); 
                $('input[name="aprovadoGr"]').prop('checked', false);
                $('input[name="aprovadoGr"]').attr("disabled", false); 
                document.getElementById("atualizarsubmit").disabled = true;
            }else if (status.trim() == "Aprovado Gerencia") {
                $('input[name="aprovadoHr"]').prop('checked', true);
                $('input[name="aprovadoHr"]').attr("disabled", true); 
                $('input[name="aprovadoGr"]').prop('checked', true);
                $('input[name="aprovadoGr"]').attr("disabled", true); 
                document.getElementById("atualizarsubmit").disabled = true;
            }

            /*Trigger email send if checkbox Aprovado HR is clicked*/
            $('input[name="aprovadoHr"]').on('change', function() {
                $('input[name="aprovadoHr"]').not(this).prop('checked', false);
                if (confirm("Um email sera enviado para aprovacao, deseja continuar?!\nEither OK or Cancel.") == true) {
                    $('input[name="aprovadoHr"]').prop('checked', true);
                    $('input[name="aprovadoHr"]').attr("disabled", true);
                    document.getElementById("atualizarsubmit").disabled = false;
                   status = "1";
                } else {
                    $('input[name="aprovadoHr"]').prop('checked', false);
                }
            });

            /*Trigger email send if checkbox Aprovado GR is clicked*/
            $('input[name="aprovadoGr"]').on('change', function() {
                if (confirm("Um email sera enviado para aprovacao, deseja continuar?!\nEither OK or Cancel.") == true) {
                    $('input[name="aprovadoGr"]').prop('checked', true);
                    $('input[name="aprovadoGr"]').attr("disabled", true);
                    document.getElementById("atualizarsubmit").disabled = false;
                    status = "2";
                }else {
                    $('input[name="aprovadoGr"]').prop('checked', false);
                }
            });
        });
        
        $('#atualizarsubmit').on('click', function(e) {
            $.ajax({
                url: "<?php echo $PAGE_NAME ?>.php",
                cache: false,
                type: "POST",
                dataType: "html",
                data:{
                    id_user: edit_id,
                    status: $('#status').val(),
                },
                success: function(data){
                }
            });
            e.preventDefault();
            window.location.href="<?php echo $PAGE_NAME ?>.php?id=" + edit_id + "&status=" + status.trim()
        });

        $('.deletebtn').on('click', function() {
            $('#modalDel').modal('show');

            $tr = $(this).closest('tr');

            var data = $tr.children("td").map(function() {
                return $(this).text();
            }).get();

            $('#delete_id').val(data[0]);
            $('#nome_del').html(data[2]);

            //Check data F12 (Console)
            console.log(data[0] + "," +
                data[2]);

            delete_id = data[0];
        });

        $('#deletesubmit').on('click', function(d) {
                $.ajax({
                    url: "<?php echo $PAGE_NAME ?>.php",
                    cache: false,
                    type: "POST",
                    dataType: "html",
                    data:{
                        id_del_user: delete_id,
                    },
                    success: function(data){
                }
            });
            d.preventDefault();
            param = "delete";
            window.location.href="<?php echo $PAGE_NAME ?>.php?id=" + delete_id + "&param=" + param
        });
    });
</script>
© <?php
    $copyYear = 2022; // Set your website start date
    $curYear = date('Y'); // Keeps the second year updated
    echo $copyYear . (($copyYear != $curYear) ? '-' . $curYear : '');
?><a href="https://www.paesetoth.com.br/sobre/"> Paes&Toth</a> Copyright
</html>