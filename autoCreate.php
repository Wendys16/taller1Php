<?php
require_once 'conexion.php';

try {

    //obetenemos los parametros enviados por POST
    $placa_auto = $_POST['placa_auto'];
    $anho_modelo        = $_POST['anho_modelo'];
    $modelo      = $_POST['modelo'];
    $precio          = $_POST['precio'];

    // Creamos una sentencia preparada
    $statement = $conn->prepare("INSERT INTO autos (placa_auto, anho_modelo, modelo, precio) VALUES (:placa_auto, :anho_modelo, :modelo, :precio)");

    // Asociamos los parametros de la consulta a las variables de los datos
    $statement->bindParam(':placa_auto', $placa_auto);
    $statement->bindParam(':anho_modelo', $anho_modelo);
    $statement->bindParam(':modelo', $modelo);
    $statement->bindParam(':precio', $precio);

    // verificamos si el dato se inserta correctamente 
    if ($statement->execute()) {
        //si se inserto con exito, ecuperamo el dato insertado para devolverlo como respuesta
        $sql = "SELECT * FROM autos order by idAuto desc limit 1";
        $statement = $conn->prepare($sql);
        $statement->execute();
        $jsonData = $statement->fetch(PDO::FETCH_ASSOC);
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