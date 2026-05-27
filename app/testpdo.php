<?php 
try {
    $mysqlConnection = new PDO("mysql:host=mtp.cjdctzykicco.eu-west-1.rds.amazonaws.com;dbname=mtp-wp-site", "mtp_admin", "Tk3H7wPJRLxyIp3W");
    //If the exception is thrown, this text will not be shown
    echo 'connected';
  }
  //catch exception
  catch(Exception $e) {
    echo 'Message: ' .$e->getMessage();
  }

?>