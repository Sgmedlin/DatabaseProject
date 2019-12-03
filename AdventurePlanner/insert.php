<?php
    session_start();
    require_once "session_config.php";
    if (isset($_POST["name"])){
        $name = $_POST["name"];
        $type = $_POST["type"];
        $status = $_POST["status"];
        $brand = $_POST["brand"];
        $condition = $_POST["condition"];
        $query = '';
        $hasQuery = '';
        $array_name = array();
        for($count = 0; $count<count($name); $count++){
            $item_name_clean = mysqli_real_escape_string($link, $name[$count]);
            $item_type_clean = mysqli_real_escape_string($link, $type[$count]);
            $item_status_clean = mysqli_real_escape_string($link, $status[$count]);
            $item_brand_clean = mysqli_real_escape_string($link, $brand[$count]);
            $item_condition_clean = mysqli_real_escape_string($link, $condition[$count]);
            $item_id_clean = $item_name_clean . rand() . rand();
            //$item_id_clean = $item_name_clean;
            array_push($array_name, $item_id_clean);
            if ($item_name_clean != '' && $item_type_clean != '' && $item_status_clean != '' && $item_brand_clean != '' && $item_condition_clean != ''){
                $query .= '
                INSERT INTO Gear VALUES("'.$item_id_clean.'", "'.$item_name_clean.'", "'.$item_type_clean.'", "'.$item_status_clean.'", "'.$item_brand_clean.'", "'.$item_condition_clean.'");  
                ';
                // $query .= '
                // INSERT INTO Has VALUES("'.$group_id.'", "'.$item_id_clean.'");  
                // ';
            }
        }
        if ($query != ''){
            if (mysqli_multi_query($link, $query)){
                $_SESSION["array_name"] = $array_name;
            }
            else{
                echo 'Error';
            }
        }
        else{
            echo 'All Fields are Required';
        }
    }
    mysqli_close($link);
?>