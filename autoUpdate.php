<?php
require_once 'conexion.php';

try {
    //obetenemos los parametros enviados por POST
    $idAuto = $_POST['idAuto'];
    $placa_auto = $_POST['placa_auto'];
    $anho_modelo        = $_POST['anho_modelo'];
    $modelo      = $_POST['modelo'];
    $precio          = $_POST['precio'];

    //consultamos el registro a eliminar
    $statement = $conn->prepare("SELECT * FROM AUTOS WHERE idAuto = :idAuto");
    $statement->bindParam(':idAuto', $idAuto);
    $statement->execute();
    $dataDrop = $statement->fetch(PDO::FETCH_ASSOC);
    if ($dataDrop == 0) {
        $jsonData =  [
            'mensaje' => 'El registo que desea Actualizar no existe',
            'data' => null
        ];
    } else {
        // Creamos una sentencia preparada
        $statement = $conn->prepare('UPDATE autos SET placa_auto = :placa_auto, anho_modelo = :anho_modelo, modelo = :modelo, precio = :precio  WHERE idAuto = :idAuto');
        // Asociamos los parametros de la consulta a las variables de los datos
        $statement->bindParam(':placa_auto', $placa_auto);
        $statement->bindParam(':anho_modelo', $anho_modelo);
        $statement->bindParam(':modelo', $modelo);
        $statement->bindParam(':precio', $precio);
        $statement->bindParam(':idAuto', $idAuto);

        $r = $statement->execute();
        // verificamos si el dato se actualizaron  correctamente 
        if ($statement->execute()) {
            //si se actualizaron con exito, recuperamo el registro actualizado para devolverlo como respuesta
            $sql = "SELECT * FROM autos where idAuto = :idAuto";
            $statement = $conn->prepare($sql);
            $statement->bindParam(':idAuto', $idAuto);
            $statement->execute();
            $dataUpdate = $statement->fetch(PDO::FETCH_ASSOC);
            $jsonData =  [
                'mensaje' => 'Registro actualizado satisfactoriamente',
                'data' => $dataUpdate
            ];
        } else {
            $jsonData =  [
                'mensaje' => 'No fue posible actualizar el registro',
                'data' => null
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