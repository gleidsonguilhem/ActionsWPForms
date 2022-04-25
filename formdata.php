<!DOCTYPE html>
<html>
<head>
  <title>Crud Wordpress</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div class="container">

<h1>Lista</h1>
<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Post ID</th>
      <th scope="col">Agente</th>
      <th scope="col">Data</th>
      <th scope="col" colspan="2">Ações</th>
      <th scope="col" colspan="2">Status</th>
    </tr>
  </thead>

<?php

use function PHPSTORM_META\type;

  require_once('../wp-load.php');

  $tabela = $wpdb->prefix."db7_forms";
  $resultado = $wpdb->get_results("SELECT * FROM ".$tabela, OBJECT);

  $i=0;
  //variable t0 define modal array size
  $size = 0;
  foreach ($resultado as $valores)
  {
    //Unserializind data
    $rows  = unserialize( $resultado[$i]->form_value );
    //Converting to Array
    $array = json_decode(json_encode($rows), true);
    
    //Extracting Name
    $row_name = $array['nome'];
    $nome = implode(" ", $row_name);

    //Extracting Aprovado
    $row_approved = $array['Aprovado'];
    $status = implode("", $row_approved);
    
    //Extracting Date in (dd/mm/YYYY)
    $sDate = date('d/m/Y',strtotime($valores->form_date));

    $id = $valores->form_id;
    $postId = $valores->form_post_id;

    //Showing Data (Populating table)
    echo 
   "<tr>
    <th scope='col'>$valores->form_id</th>
    <th scope='col'>$valores->form_post_id</th>
    <th scope='col'>$nome</th>
    <th scope='col'>$sDate</th>
    <td  colspan='2'>
      <button type='button' data-id='$valores->form_id' name='atualizar' class='btn btn-info btn-sm' data-toggle='modal' data-target='#myModal-$i'>atualizar</button>
      <button type='button' name='apagar' class='btn btn-danger btn-sm'>Apagar</button>
    </td>
      <th scope='col'>$status
    </th>
    </td>
    </tr>";
    
    $i++;
  }  

  echo "</div></table>";


  $i=0;
  //variable t0 define modal array size
  $size = 0;
  foreach ($resultado as $valores)
  {
    //Unserializind data
    $rows  = unserialize( $resultado[$i]->form_value );
    //Converting to Array
    $array = json_decode(json_encode($rows), true);
    
    //Extracting Name
    $row_name = $array['nome'];
    $nome = implode(" ", $row_name);

    //Extracting Aprovado
    $row_approved = $array['Aprovado'];
    $status = implode("", $row_approved);
    
    //Extracting Date in (dd/mm/YYYY)
    $sDate = date('d/m/Y',strtotime($valores->form_date));

    $id = $valores->form_id;
    $postId = $valores->form_post_id;

    //Showing Data (Populating table)
    echo
    "
        <!-- Modal -->
        <div id='myModal-$i' class='modal fade' role='dialog'>
        <div class='modal-dialog modal-lg'>
            <!-- Modal content-->
            <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body'>
                <table class='table table-striped'>
                <thead>
                <tr>
                    <th scope='col'>ID</th>
                    <th scope='col'>PostID</th>
                    <th scope='col'>Nome</th>
                    <th scope='col'>Date</th>
                    <th scope='col'>Action</th>
                </tr>
            </thead>
                <tbody>
                <form action='submit' method='post'>
                    <tr>
                    <td>$id</td>
                    <td>$postId</td>
                    <td>$nome</td>
                    <td>$sDate</td>
                    <td>
                        <input type='checkbox' name='aprovadoHr[]'  value='aprovadoFr'>Aprovado
                        <input type='checkbox' name='aprovadoGr[]'  value='aprovadoGr'>Arovado Gerencia
                    </td>  

                    <td width='25%'><button id='uptsubmit-$i' name='atualizarsubmit' type='submit' class='btn btn-success btn-sm'>UPDATE</button> <a href='tests.php'><button type='button' class='btn btn-warning btn-sm'>CANCEL</button></a></td>
                    </tr>
                </form>
                </tbody>
            </table>         
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
            </div>
            </div>
    </div>
    </div>";
    
    $i++;
  }
 

  if (isset($_POST['atualizar'])) 
  {
    $id = $_GET['atualizar'];
    $atualizar = $wpdb->get_results("SELECT * FROM ".$tabela." WHERE form_id= ".$id);
       
    foreach($atualizar as $print) {
      $post = $print->form_post_id;
      $value = $print->form_value;
    }
  }
  
  if(isset($_GET['apagar'])) 
  {
    $id = $_GET['apagar'];
    $delete_user = $wpdb->query("DELETE FROM ".$tabela." WHERE form_id = $id");
    
    if($delete_user) {
      echo "deleted successfully";
      echo "<script>location.reload();</script>";
    }else {
      echo "Delete Failed";
    }
  }
  
  if (isset($_POST['atualizarsubmit'])) {
    $nvalue = [];
    
    if(isset($_POST['aprovadoHr'])) {
      $nvalue[0] = "RH";
    }
    else if(isset($_POST['aprovadoGr'])) {
      $nvalue[0] = "Gerencia";
    }
    else {
      $nvalue[0] = "-";
    }

    echo $nvalue[0];

    $data = $wpdb->get_results("SELECT form_value FROM $tabela WHERE form_id = $id");

    $arr = json_decode(json_encode($data[0]),true);
    foreach($arr as $key => $value)
    {
      $data_f = $value;
    }
  
    $unserialized = unserialize($data_f);
    
    $formValue = $unserialized;
    $formValue['Aprovado'] = $nvalue;
    $serialized = serialize($formValue);

    $wpdb->query("UPDATE $tabela SET form_value='$serialized' WHERE form_id = $id");
    
    echo "<script>location.replace('tests.php');</script>";
  }
 
?>

</body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</html>

