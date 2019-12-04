<?php
    session_start();
    require_once "session_config.php";
    $output = '';
    if(isset($_POST["export-btn"])){
        $sql = "SELECT `gear_id`, `name`, `type`, `status`, `brand`, `condition` FROM Gear NATURAL JOIN Has WHERE group_id = ".$_SESSION['group_id'];
        $result = mysqli_query($link, $sql);
        if (mysqli_num_rows($result) > 0){
            $output .= '
                <table class="table" bordered="1">
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Brand</th>
                        <th>Condition</th>
                    </tr>
            ';
            while($row = mysqli_fetch_array($result)){
                $output .= '
                    <tr>
                        <th>' .$row["name"]. '</th>
                        <th>' .$row["type"]. '</th>
                        <th>' .$row["status"]. '</th>
                        <th>' .$row["brand"]. '</th>
                        <th>' .$row["condition"]. '</th>
                    </tr>
                ';
            }
            $output .= '</table>';
            header("Content-Type: application/xls");
            header("Content-Disposition: attachment; filename=data.xls");
            echo $output;
        }
        else{
            echo "empty gear list";
        }
    }
?>