<?php
require_once 'conexion.php';

try {

    //obetenemos los parametros enviados por POST
    $idAuto = $_POST['idAuto'];


    //consultamos el registro a eliminar
    $statement = $conn->prepare("SELECT * FROM AUTOS WHERE idAuto = :idAuto");
    $statement->bindParam(':idAuto', $idAuto);
    $statement->execute();
    $dataDrop = $statement->fetch(PDO::FETCH_ASSOC);
    //verificamos si existe el dato a eliminar
    if ($dataDrop == 0) {
        $jsonData =  [
            'mensaje' => 'El registo que desea eliminar no existe'
        ];
    } else {
        //verificamos sise puede eliminar el auto(solo se puede eliminar sei no esta asociado a un empleado)
        $statement = $conn->prepare("SELECT * FROM empleados WHERE idAuto = :idAuto");
        $statement->bindParam(':idAuto', $idAuto);
        $statement->execute();
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($data >= 0) {
            $jsonData =  [
                'mensaje' => 'No se pudo eliminar el registro, este auto esta asociado a un empleado'
            ];
        } else {

            // Eliminamos el registro
            $statement = $conn->prepare("DELETE FROM AUTOS WHERE idAuto = :idAuto");
            $statement->bindParam(':idAuto', $idAuto);
            $statement->execute();

            //Retornamos resultados del registro eliminado
            $jsonData =  [
                'mensaje' => 'Registro eliminado satisfactoriamente',
                'data' => $dataDrop
            ];
        }
    }

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