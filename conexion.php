<?php
    $conexion = new mysqli(
        "localhost:3307",
        "root",
        "",
        "app_gestion"
    );

    if($conexion -> connect_error){
        die("Failed to connect". $mysql -> connect_error);
    }