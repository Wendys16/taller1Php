<?php
require_once 'conexion.php';

try {

    //consultamos los registro 
    $statement = $conn->prepare("SELECT * FROM AUTOS WHERE idAuto");
    $statement->execute();
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);
    //verificamos si existe el dato a eliminar
    if ($data == 0) {
        $jsonData =  [
            'mensaje' => '0 regstros encontrados'
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