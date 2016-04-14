<?php
$file_name="reports/".$_REQUEST['file'];
header('Content-type: application/zip');
//open/save dialog box
//header('Content-Disposition: attachment; filename="\\"');
 header("Content-Disposition: attachment; filename=\"$file_name\"");
//read from server and write to buffer
readfile("reports/".$_REQUEST['file']);
?>

