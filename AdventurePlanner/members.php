<?php
    session_start();
    require_once "session_config.php";
    $output = '';
    if(isset($_POST["export-btn"])){
        $sql = "SELECT `user_id`, `name`, `membership_level` FROM User_Profile NATURAL JOIN belongs_to NATURAL JOIN Groups WHERE group_id=".$_SESSION['group_id'];
        $result = mysqli_query($link, $sql);
        if (mysqli_num_rows($result) > 0){
            $output .= '
                <table class="table" bordered="1">
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                    </tr>
            ';
            while($row = mysqli_fetch_array($result)){
                $output .= '
                    <tr>
                        
                    </tr>
                ';
            }
            // while($row = mysqli_fetch_array($result3)) {
            //     echo "<tr>";
            //     echo "<td> <a class='btn btn-link' role='button' href='user_details.php?id=" . $row['user_id'] . "'>" . $row['name'] .  "</td>";
            //     echo "<td>" . $row['membership_level'] . "</td>";
            //     $sql4 = "SELECT `name`, `user_id` FROM checks_out NATURAL JOIN Gear WHERE user_id=".$row['user_id'] ." AND group_id=".$_GET['id'];
            //     $result4 = mysqli_query($link, $sql4);
            //     $value = '';
            //     while ($row4 = mysqli_fetch_array($result4)){
            //         $value .= $row4['name'] . " , ";
            //     }
            //     echo "<td>" . $value . "</td>";	
            //     echo "<td>" . "</td>"; 
            //     echo "</tr>";
            //     }
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