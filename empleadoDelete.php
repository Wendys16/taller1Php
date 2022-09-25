<?php
require_once 'conexion.php';

try {

    //obetenemos los parametros enviados por POST
    $idEmpleado = $_POST['idEmpleado'];
    $jsonData = array();

    //consultamos el registro a eliminar
    $statement = $conn->prepare("SELECT * FROM empleados WHERE idEmpleado = :idEmpleado");
    $statement->bindParam(':idEmpleado', $idEmpleado);
    $statement->execute();
    $resultEmpleado = $statement->fetch(PDO::FETCH_ASSOC);
    //verificamos si existe el dato a eliminar
    if ($resultEmpleado == 0) {
        $jsonData =  [
            'mensaje' => 'El registo que desea eliminar no existe'
        ];
    } else {

        //consultamos los datos del auto de ese empleado
        $statement = $conn->prepare("SELECT * FROM autos where idAuto = :idAuto");
        $statement->bindParam(':idAuto', $resultEmpleado['idAuto']);
        $statement->execute();
        $resultAuto = $statement->fetch(PDO::FETCH_ASSOC);

        // Eliminamos el registro
        $statement = $conn->prepare("DELETE FROM empleados WHERE idEmpleado = :idEmpleado");
        $statement->bindParam(':idEmpleado', $idEmpleado);
        $statement->execute();

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
            'mensaje' => 'Registro eliminado satisfactoriamente',
            'data' => $jsonData
        ];
    }
    //Retornamos resultados del registro eliminado
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