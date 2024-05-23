<?php
function initializeDatabase() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "app_creator";

    // Crear conexión
    $conn = new mysqli($servername, $username, $password);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Crear base de datos
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql) !== TRUE) {
        die("Error al crear la base de datos: " . $conn->error);
    }

    // Seleccionar base de datos
    $conn->select_db($dbname);

    // Crear tabla commands
    $sql = "CREATE TABLE IF NOT EXISTS commands (
        id INT AUTO_INCREMENT PRIMARY KEY,
        command VARCHAR(255) NOT NULL,
        response TEXT NOT NULL
    )";
    if ($conn->query($sql) !== TRUE) {
        die("Error al crear la tabla: " . $conn->error);
    }

    // Insertar comandos predefinidos
    $sql = "INSERT INTO commands (command, response) VALUES
        ('crear app android', 'La aplicación Android se está generando. Por favor, espera.'),
        ('crear app ios', 'La aplicación iOS se está generando. Por favor, espera.')
    ON DUPLICATE KEY UPDATE
        response=VALUES(response)";

    if ($conn->query($sql) !== TRUE) {
        die("Error al insertar datos: " . $conn->error);
    }

    echo "Base de datos inicializada correctamente.";

    $conn->close();
}

initializeDatabase();
?>
