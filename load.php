<?php

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $connection = Connection::getConnection();


    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 25;
    $text = isset($_GET['text']) ? strval($_GET['text']) : '';
    $fields = isset($_GET['fields']) && json_decode($_GET['fields']) !== false ? json_decode($_GET['fields'], true) : [];
    $order = isset($_GET['sort']) ? strval($_GET['sort']) : '';
    $dir = isset($_GET['dir']) ? strval($_GET['dir']) : 'asc';

    $sql = 'SELECT * FROM calender ';

    if (!empty($fields)) {
        $sql.= 'WHERE ';
        $textFields = [
            "year", "name", "city", "country", "holder", "logo", "type", "game", "note", "url", "contact_name",
            "contact_email", "contact_phone", "name_ru", "ip", "wcup_stars", "is_result", "live_url", "place_sure",
            "age_categories", "tempo"
        ];
        $dateFields = [ "begin", "tot", "added_date"];
        $floatFields = ["fee"];
        foreach ($fields as $idx => $field) {
            if (in_array($field, $textFields)) {
                $sql .= sprintf("%s like '%s%%' ", $field, strval($text));
            } elseif (in_array($field, $dateFields)) {
                $sql .= sprintf("%s = '%s%%' ", $field, strval($text));
            } elseif (in_array($field, $floatFields)) {
                $sql .= sprintf("%s = %s ", $field, (float) $text);
            } else {
                $sql .= sprintf("%s = %s ", $field, (int) $text);
            }

            if ($idx < count($fields) - 1) {
                $sql .= 'OR ';
            }
        }
    }

    $totalSql = sprintf('select count(*) as total from (%s) t', $sql);

    if ($order) {
        $sql .= sprintf('order by %s %s ', $order, $dir);
    }

    $sql .= sprintf('limit %s offset %s', $limit, $start);

    $result = [];
    $textFields = [
        "year", "name", "city", "country", "holder", "logo", "type", "game", "note", "url", "contact_name",
        "contact_email", "contact_phone", "name_ru", "ip", "wcup_stars", "is_result", "live_url", "place_sure",
        "age_categories", "tempo"
    ];
    $dateFields = [ "begin", "tot", "added_date"];
    $floatFields = ["fee"];
    $boolColumns = ["status", "type", "dates_sure", "place_sure", "is_open", "is_youth"];
    foreach ($connection->query($sql) as $fields) {
        $rec = [];
        foreach ($fields as $idx => $field) {
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
        $result[] = $rec;
    }

    $total = $connection->prepare($totalSql);
    $total->execute();

    $return = [
        'success' => true,
        'totalCount' => intval($total->fetchColumn()),
        'data' => $result,
    ];

    header('Content-Type: application/json');
    echo json_encode($return);
}



