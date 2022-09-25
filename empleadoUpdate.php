<?php
require_once 'conexion.php';

try {

    //obetenemos los parametros enviados por POST
    $idEmpleado = $_POST['idEmpleado'];
    $nombre        = $_POST['nombre'];
    $apellido      = $_POST['apellido'];
    $fecha_hora_entrega  = $_POST['fecha_hora_entrega'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $telefono        = $_POST['telefono'];
    $estatura      = $_POST['estatura'];
    $email          = $_POST['email'];
    $idAuto          = $_POST['idAuto'];
    $jsonData = array();
    //consultamos si existe el dato a modificar
    $statement = $conn->prepare("SELECT * FROM empleados WHERE idEmpleado = :idEmpleado");
    $statement->bindParam(':idEmpleado', $idEmpleado);
    $statement->execute();
    $dataDrop = $statement->fetch(PDO::FETCH_ASSOC);
    //verificamos si existe el dato a eliminar
    if ($dataDrop == 0) {
        $jsonData =  [
            'mensaje' => 'El registro que intenta modificar no existe, no existe',
        ];
    } else {
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
            $statement = $conn->prepare("UPDATE empleados SET nombre = :nombre , apellido = :apellido, fecha_hora_entrega = :fecha_hora_entrega, fecha_nacimiento = :fecha_nacimiento, telefono = :telefono, estatura = :estatura, email = :email, idAuto = :idAuto WHERE idEmpleado = :idEmpleado");
            // Asociamos los parametros de la consulta a las variables de los datos
            $statement->bindParam(':nombre', $nombre);
            $statement->bindParam(':apellido', $apellido);
            $statement->bindParam(':fecha_hora_entrega', $fecha_hora_entrega);
            $statement->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $statement->bindParam(':telefono', $telefono);
            $statement->bindParam(':estatura', $estatura);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':idAuto', $idAuto);
            $statement->bindParam(':idEmpleado', $idEmpleado);

            // verificamos si el dato se actualizo correctamente 
            if ($statement->execute()) {
                //si se actualizo con exito, ecuperamo el dato actualizado
                $sql = "SELECT * FROM empleados where idEmpleado = :idEmpleado";
                $statement = $conn->prepare($sql);
                $statement->bindParam(':idEmpleado', $idEmpleado);
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
                $jsonData =  [
                    'mensaje' => 'Registro actualizado satisfactoriamente',
                    'data' => $jsonData
                ];
            } else {
                $jsonData =  [
                    'mensaje' => 'Error, no se pudo actualizar  la informacion',
                ];
            }
        }
    }

    //Retornamos resultados
    header('Content-type:application/json;charset=utf-8');
    echo json_encode($jsonData);
    $conn = null;
} catch (PDOException $e) {
    header('Content-type:application/json;charset=utf-8');
    echo json_encode([
        'error' => [
            'codigo' => $e->getCode(),
            'mensaje' => $e->getMessage()
        ]
    ]);
}