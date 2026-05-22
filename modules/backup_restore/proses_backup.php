<?php
session_start();

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
} elseif ($_SESSION['hak_akses'] == 'Administrator') {
    require_once "../../config/database.php";

    $tables = array();
    $result = mysqli_query($mysqli, 'SHOW TABLES');
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }

    $sqlScript = "";
    
    // Header file sql
    $sqlScript .= "-- --------------------------------------------------------\n";
    $sqlScript .= "-- Host: {$host}\n";
    $sqlScript .= "-- Database: {$database}\n";
    $sqlScript .= "-- Tanggal Backup: " . date('Y-m-d H:i:s') . "\n";
    $sqlScript .= "-- --------------------------------------------------------\n\n";
    
    // Matikan foreign key checks agar proses restore lancar
    $sqlScript .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    foreach ($tables as $table) {
        $result = mysqli_query($mysqli, 'SELECT * FROM ' . $table);
        $num_fields = mysqli_num_fields($result);

        $sqlScript .= "DROP TABLE IF EXISTS `" . $table . "`;\n";
        $row2 = mysqli_fetch_row(mysqli_query($mysqli, 'SHOW CREATE TABLE ' . $table));
        $sqlScript .= "\n\n" . $row2[1] . ";\n\n";

        while ($row = mysqli_fetch_row($result)) {
            $sqlScript .= "INSERT INTO `" . $table . "` VALUES(";
            for ($j = 0; $j < $num_fields; $j++) {
                if (isset($row[$j])) {
                    $row[$j] = $mysqli->real_escape_string($row[$j]);
                    $sqlScript .= '"' . $row[$j] . '"';
                } else {
                    $sqlScript .= 'NULL';
                }

                if ($j < ($num_fields - 1)) {
                    $sqlScript .= ',';
                }
            }
            $sqlScript .= ");\n";
        }
        $sqlScript .= "\n\n\n";
    }

    $sqlScript .= "SET FOREIGN_KEY_CHECKS=1;\n";

    $file_name = 'backup_db_stok_' . date('Y-m-d_H-i-s') . '.sql';

    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename=' . $file_name);
    
    // Membersihkan output buffer sebelumnya agar file sql tidak corrupt
    if(ob_get_length() > 0) {
        ob_clean();
    }
    
    echo $sqlScript;
    exit;
} else {
    header('location: ../../404.html');
}
?>
