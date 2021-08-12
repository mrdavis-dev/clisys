<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <title>clínica</title>
</head>

<body>

        <div class="container text-center">
        
            <img src="img/tulogo.png" style="width: 250px" alt="">

            <div class="mt-3 mb-5">
            <h4>Agenda una cita con nosotros</h4>
            <p>"visitanos tambien en nustras redes sociales"</p>
              <a href="https://www.facebook.com/clinicadentalanguizola/"><img style="width:30px" src="https://img.icons8.com/nolan/64/facebook.png"></a>
              <a href="https://www.instagram.com/Clinicadentalanguizola2/"><img style="width:30px" src="https://img.icons8.com/nolan/64/instagram-new.png"></a>
            </div>

                <form action="insert_exterior.php" method="POST">
                    
                        <div class="container mx-auto w-75 mb-4">
                            <label for="fecha">Fecha de cita:</label>
                            <input type="date" class="mb-1 form-control border border-primary" placeholder="" name="fecha">

                            <label for="hora">Hora de cita:</label>
                            <input type="time" class="mb-1 form-control border border-primary" placeholder="" name="hora">

                            <label for="nombre">Nombre:</label>
                            <input type="text" class="mb-1 form-control border border-primary" placeholder="Nombre y Apellido" name="nombre">

                            <label for="asunto">Asunto de la cita:</label>
                            <input type="text" class="mb-1 form-control border border-primary" placeholder="asunto" name="asunto">

                            <label for="">Doctor de preferencia:</label>
                            <select name="doctor" required class="form-control" id="">
                                <option value="">Seleccione un Doctor</option>
                                <option value="Dr. Júlio Anguizola Vial">Dr. Júlio Anguizola Vial</option>
                                <option value="Dr. Miguel Anguizola Severino">Dr. Miguel Anguizola Severino</option>
                                <option value="Dr. Amira Martínez de Anguizola">Dr. Amira Martínez de Anguizola</option>
                            </select>
                        </div>
                    
                        <input type="submit" class="btn btn-primary mb-5" name="" value="Enviar">
                </form>
            
        </div>

</body>

</html>

<?php 
// send notification telegram and save in data base.


?>