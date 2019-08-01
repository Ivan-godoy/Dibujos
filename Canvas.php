<?php

require_once 'vendor/autoload.php';

class Canvas implements \Ratchet\MessageComponentInterface
{

    private $conexiones = [];
    private $conexion;

    public function __construct(PDO $pdo)
    {
        $this->conexion = $pdo;
    }


    function onOpen(\Ratchet\ConnectionInterface $conn)
    {
        $this->conexiones[] = $conn;
    }


    function onClose(\Ratchet\ConnectionInterface $conn)
    {
        // TODO: Implement onClose() method.
    }


    function onError(\Ratchet\ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }


    function onMessage(\Ratchet\ConnectionInterface $from, $msg)
    {

        $datos = json_decode($msg, true);
        $stmt = $this->conexion->prepare("INSERT INTO coordenadas (coord_x, coord_y, color, grosor) VALUES (?, ?, ?, ?)");
        foreach ($datos as $coordenada){
            $stmt->bindValue(1, $coordenada['x']);
            $stmt->bindValue(2, $coordenada['y']);
            $stmt->bindValue(3, $coordenada['color']);
            $stmt->bindValue(4, $coordenada['grosor']);
            $stmt->execute();
        }
        foreach ($this->conexiones as $conexion) {
            if ($conexion != $from) {
                $conexion->send($msg);
            }
        }
    }
}