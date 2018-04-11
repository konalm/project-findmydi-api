<?php 

$conn_string = 'host=localhost dbname=findmydi_dev user=postgres password=$$superstar';
$conn = pg_connect($conn_string);


$sql_query = 'CREATE TABLE migtest (
  column_a character varying,
  column_b character varying,
  column_c character varying
)';

pg_query($conn, $sql_query);

error_log('table created');


