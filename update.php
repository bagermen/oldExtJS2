<?php

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
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

    $data = json_decode($data['data'], true);

    if (!isset($data['cid'])) {
        exit;
    }
    $id = intval($data['cid']);
    unset($data['cid']);

    $result = $connection->prepare('select * from calender where cid = :id');
    $result->execute(['id' => $id]);

    $calender = $result->fetch(PDO::FETCH_ASSOC);

    if (!$calender) {
        exit;
    }

    $calender = array_merge($calender, $data);
    unset($calender['cid']);
    $sql = 'update calender set ';

    $textFields = [
        "year", "name", "city", "country", "holder", "logo", "type", "game", "note", "url", "contact_name",
        "contact_email", "contact_phone", "name_ru", "ip", "wcup_stars", "is_result", "live_url", "place_sure",
        "age_categories", "tempo"
    ];
    $dateFields = [ "begin", "tot"];
    $floatFields = ["fee"];

    $i = 0;
    foreach ($calender as $field => $value) {
        if (in_array($field, $textFields)) {
            $sql .= sprintf("%s = '%s' ", $field, strval($value));
        } elseif (in_array($field, $dateFields)) {
            $timestamp = strtotime(strval($value));
            $datetime = new \DateTime();
            $datetime->setTimestamp($timestamp);
            $sql .= sprintf("%s = '%s' ", $field, $datetime->format('Y-m-d'));
        } elseif (in_array($field, $floatFields)) {
            $sql .= sprintf("%s = %s ", $field, (float) $value);
        } else {
            $sql .= sprintf("%s = %s ", $field, (int) $value);
        }

        if ($i < count($calender) - 1) {
            $sql .= ', ';
        }
        ++$i;
    }
    $sql .= 'where cid ='.$id;

    $connection->query($sql);

    // load data
    $result = $connection->prepare('select * from calender where cid = :id');
    $result->execute(['id' => $id]);

    $calender = $result->fetch(PDO::FETCH_ASSOC);

    $textFields = [
        "year", "name", "city", "country", "holder", "logo", "type", "game", "note", "url", "contact_name",
        "contact_email", "contact_phone", "name_ru", "ip", "wcup_stars", "is_result", "live_url", "place_sure",
        "age_categories", "tempo"
    ];
    $dateFields = [ "begin", "tot", "added_date"];
    $floatFields = ["fee"];
    $boolColumns = ["status", "type", "dates_sure", "place_sure", "is_open", "is_youth"];
    $rec = [];
    foreach ($calender as $idx => $field) {
        if (!is_numeric($idx)) {
            $rec[$idx] = $field;
            if (!in_array($idx, $textFields) && !in_array($idx, $dateFields) && !in_array($idx, $floatFields)&& !in_array($idx, $boolColumns)) {
                $rec[$idx] = intval($field);
            } elseif (in_array($idx, $floatFields)) {
                $rec[$idx] = floatval($field);
            } elseif (in_array($idx, $boolColumns)) {
                $rec[$idx] = !!$field;
            }
        }

    }

    $return = [
        'success' => true,
        'data' => $rec,
    ];

    header('Content-Type: application/json');
    echo json_encode($return);
}