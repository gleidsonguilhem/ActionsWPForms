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
      <th scope="col">Value</th>
      <th scope="col">Date</th>
      <th scope="col">Actions</th>
    </tr>
  </thead>
  </div>

  <?php
  require_once('wp-load.php');

  $tabela = $wpdb->prefix."db7_forms";
  $resultado = $wpdb->get_results("SELECT * FROM ".$tabela);

  foreach ($resultado as $valores)
  {
    echo 
    "<tr>
    <th scope='col'>$valores->form_id</th>
    <th scope='col'>$valores->form_post_id</th>
    <th scope='col'>$valores->form_value</th>
    <th scope='col'>$valores->form_date</th>
    <td width='25%'>
      <a href='?atualizar=$valores->form_id'>
        <button type='button'>Atualizar</button>
      </a>
      </td>
      <td>
      <a href='?apagar=$valores->form_id'>
        <button type='button'>Apagar</button>
      </a>  
    </td>
    
    </tr>";
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
  
  if (isset($_GET['atualizar'])) 
  {
    $id = $_GET['atualizar'];
    $atualizar = $wpdb->get_results("SELECT * FROM ".$tabela." WHERE form_id= ".$id);
       
    foreach($atualizar as $print) {
      $post = $print->form_post_id;
      $value = $print->form_value;
    }

    echo "<table class='table table-striped'>
          <thead>
            <tr>
              <th scope=col'>ID</th>
              <th scope='col'>Post ID</th>
              <th scope='col'>Value</th>
              <th scope='col'>Date</th>
            </tr>
        </thead>
          <tbody>
            <form action='' method='post'>
              <tr>
                <td>$print->form_id</td>
                <td>$print->form_post_id</td>
                <td>
                  <input type='checkbox' name='aprovado[]' value='funcionario'>Aprovado&nbsp;
                  <input type='checkbox' name='aprovado[]' value='gerencia'>Arovado Gerencia
                </td>  
                <td>$print->form_date</td>
                <td width='25%'><button id='uptsubmit' name='atualizarsubmit' type='submit'>UPDATE</button> <a href='form_data.php'><button type='button'>CANCEL</button></a></td>
              </tr>
            </form>
          </tbody>
        </table> ";
      }

      if (isset($_POST['atualizarsubmit'])) {
        $nvalue = [];
        
        if(isset($_POST['aprovado']) == 'funcionario') {
          $nvalue[0] = "1";
        }
        if(isset($_POST['aprovado']) == 'gerencia') {
          $nvalue[0] = "2";
        }
        else {
          $nvalue[0] = "0";
        }

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
        
        echo "<script>location.replace('formdata.php');</script>";
      }
?>

</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>