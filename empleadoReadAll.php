<?php
require_once 'conexion.php';

try {

    $jsonData = array();
    // consultamos los datos de los empleados
    $statement = $conn->prepare("SELECT * FROM empleados");
    $statement->execute();
    $resultEmpleado = $statement->fetchAll(PDO::FETCH_ASSOC);
    if ($resultEmpleado == 0) {
        $jsonData =  [
            'mensaje' => '0 regstros encontrados'
        ];
    } else {
        //recorremos los empleados
        foreach ($resultEmpleado as $row) {
            //consultamos los datos del auto de ese empleado
            $statement = $conn->prepare("SELECT * FROM autos where idAuto = :idAuto");
            $statement->bindParam(':idAuto', $row['idAuto']);
            $statement->execute();
            $resultAuto = $statement->fetch(PDO::FETCH_ASSOC);

            //creamos un objeto temporal con la informacion del empleado y el auto relacionado
            $dataTemp =  array(
                'idEmpleado ' => $row['idEmpleado'],
                'nombre' => $row['nombre'],
                'apellido' => $row['apellido'],
                'fecha_hora_entrega' => $row['fecha_hora_entrega'],
                'fecha_nacimiento' => $row['fecha_nacimiento'],
                'telefono' => $row['telefono'],
                'estatura' => $row['estatura'],
                'email' =>  $row['email'],
                'idAuto' => $resultAuto
            );
            //agregamos el objecto al arrray final
            array_push($jsonData, $dataTemp);
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