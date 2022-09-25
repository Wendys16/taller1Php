<?php
require_once 'conexion.php';

try {
    $jsonData = array();
    //obetenemos los parametros enviados por POST
    $nombre        = $_POST['nombre'];
    $apellido      = $_POST['apellido'];
    $fecha_hora_entrega          = $_POST['fecha_hora_entrega'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $telefono        = $_POST['telefono'];
    $estatura      = $_POST['estatura'];
    $email          = $_POST['email'];
    $idAuto          = $_POST['idAuto'];

    //consultamos si el auto que se le asigna al empleado existe en la tabla autos
    $statement = $conn->prepare("SELECT * FROM autos WHERE idAuto = :idAuto");
    $statement->bindParam(':idAuto', $idAuto);
    $statement->execute();
    $auto = $statement->fetch(PDO::FETCH_ASSOC);
    //verificamos si existe el dato a eliminar
    if ($auto == 0) {
        $jsonData =  [
            'mensaje' => 'El id del auto que intenta asignar al empleado, no existe',
        ];
    } else {
        // Creamos una sentencia preparada
        $statement = $conn->prepare("INSERT INTO empleados (nombre, apellido, fecha_hora_entrega, fecha_nacimiento, telefono, estatura, email, idAuto) VALUES (:nombre, :apellido, :fecha_hora_entrega, :fecha_nacimiento, :telefono, :estatura, :email, :idAuto)");
        // Asociamos los parametros de la consulta a las variables de los datos
        $statement->bindParam(':nombre', $nombre);
        $statement->bindParam(':apellido', $apellido);
        $statement->bindParam(':fecha_hora_entrega', $fecha_hora_entrega);
        $statement->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $statement->bindParam(':telefono', $telefono);
        $statement->bindParam(':estatura', $estatura);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':idAuto', $idAuto);

        // verificamos si el dato se inserta correctamente 
        if ($statement->execute()) {
            //obtenemos el empleado insertado
            $sql = "SELECT * FROM empleados order by idEmpleado desc limit 1";
            $statement = $conn->prepare($sql);
            $statement->execute();
            $resultEmpleado = $statement->fetch(PDO::FETCH_ASSOC);

            //consultamos los datos del auto de ese empleado
            $statement = $conn->prepare("SELECT * FROM autos where idAuto = :idAuto");
            $statement->bindParam(':idAuto', $resultEmpleado['idAuto']);
            $statement->execute();
            $resultAuto = $statement->fetch(PDO::FETCH_ASSOC);

            //creamos un objeto temporal con la informacion del empleado y el auto relacionado
            $dataTemp =  array(
                'idEmpleado ' => $resultEmpleado['idEmpleado'],
                'nombre' => $resultEmpleado['nombre'],
                'apellido' => $resultEmpleado['apellido'],
                'fecha_hora_entrega' => $resultEmpleado['fecha_hora_entrega'],
                'fecha_nacimiento' => $resultEmpleado['fecha_nacimiento'],
                'telefono' => $resultEmpleado['telefono'],
                'estatura' => $resultEmpleado['estatura'],
                'email' =>  $resultEmpleado['email'],
                'idAuto' => $resultAuto
            );
            //agregamos el objecto al arrray final
            array_push($jsonData, $dataTemp);
        } else {
            $jsonData =  [
                'mensaje' => 'Error, no se pudo insertar la informacion',
            ];
        }
    }

    //Retornamos resultados
    header('Content-type:application/json;charset=utf-8');
    echo json_encode($jsonData);
} catch (PDOException $e) {
    header('Content-type:application/json;charset=utf-8');
    echo json_encode([
        'error' => [
            'codigo' => $e->getCode(),
            'mensaje' => $e->getMessage()
        ]
    ]);
}