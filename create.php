<?php

require_once 'connection.php';

$connection = Connection::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['data'])) {
        exit;
    }

    $data = json_decode($_POST['data'], true);

    if (isset($data['cid'])) {
        exit;
    }

    $calender = $data;
    $sql = 'insert calender (%s) values (%s)';
    $fIns =[];
    $fData = [];

    $textFields = [
        "year", "name", "city", "country", "holder", "logo", "type", "game", "note", "url", "contact_name",
        "contact_email", "contact_phone", "name_ru", "ip", "wcup_stars", "is_result", "live_url", "place_sure",
        "age_categories", "tempo"
    ];
    $dateFields = [ "begin", "tot"];
    $floatFields = ["fee"];

    foreach ($calender as $field => $value) {
        if (in_array($field, $textFields)) {
            $value = sprintf("'%s'", strval($value));
        } elseif (in_array($field, $dateFields)) {
            $timestamp = strtotime(strval($value));
            $datetime = new \DateTime();
            $datetime->setTimestamp($timestamp);
            $value = sprintf("'%s'", $datetime->format('Y-m-d'));
        } elseif (in_array($field, $floatFields)) {
            $value = (float) $value;
        } else {
            $value = (int) $value;
        }

        $fIns[] = $field;
        $fData[] = $value;
    }

    $sql = sprintf($sql, implode(', ', $fIns), implode(', ', $fData));

    $connection->query($sql);

// load data
    $result = $connection->prepare('select * from calender where cid = :id');
    $result->execute(['id' => intval($connection->lastInsertId())]);

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

