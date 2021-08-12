<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>

    <script src="../../js/node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <title>Registrado</title>
</head>

<body class="container">
    <div class="text-center">
        <img src="../img/.png" style="width: 50%;" class="img-fluid">
    </div>

    <?php
    //hide error and notice
    // error_reporting(0);
    // ini_set('display_errors', 0);
    // Include conn
    require_once "../conexion/config.php";



    if (isset($_POST["submit"])) {
        $username = $_POST['user'];
        $password = $_POST['password'];
        $options = array("cost" => 4);
        $hashPassword = password_hash($password, PASSWORD_BCRYPT, $options);
        $nombre = $_POST['nombre'];
      

        if (mysqli_query($db, "INSERT INTO users (username, password, name) 
    values ('$username', '$hashPassword', '$nombre')")) {

            echo "
                <script>
                    alert('Registrado...');
                
                        window.location.href = '../index.php';
                  
            </script>
                ";
        } else {
            $compara = $db->query("SELECT EXISTS (SELECT username FROM users WHERE username='$username');");
            $row = mysqli_fetch_row($compara);

            if ($row[0] == "1") {
                echo
                "
                    <h2>Este usario ya esta registrado</h2>
                    <button class='btn btn-danger' onclick='window.history.back()'>Regresar</button>
                ";
            }
        }
    }

    ?>


</body>

</html>