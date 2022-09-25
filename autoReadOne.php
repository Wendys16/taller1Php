<?php
require_once 'conexion.php';

try {

    //obetenemos los parametros enviados por POST
    $idAuto = $_GET['idAuto'];

    //consultamos el registro 
    $statement = $conn->prepare("SELECT * FROM AUTOS WHERE idAuto = :idAuto");
    $statement->bindParam(':idAuto', $idAuto);
    $statement->execute();
    $data = $statement->fetch(PDO::FETCH_ASSOC);
    //verificamos si existe el dato a eliminar
    if ($data == 0) {
        $jsonData =  [
            'mensaje' => 'El registo no existe'
        ];
    } else {
        //Retornamos resultados del registro eliminado
        $jsonData = $data;
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