<?php

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $connection = Connection::getConnection();

    $putfp = fopen('php://input', 'r');
    $putdata = '';
    while($data = fread($putfp, 1024))
        $putdata .= $data;
    fclose($putfp);
    $data = [];
    parse_str(urldecode($putdata), $data);

    if (!isset($data['data'])) {
        exit;
    }

    $id = intval($data['data']);

    if (empty($id)) {
        exit;
    }

    $result = $connection->prepare('delete from calender where cid = :id');
    $result->execute(['id' => $id]);

    $return = [
        'success' => true,
    ];

    header('Content-Type: application/json');
    echo json_encode($return);
}