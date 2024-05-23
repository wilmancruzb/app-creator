CREATE DATABASE IF NOT EXISTS app_creator;

USE app_creator;

CREATE TABLE IF NOT EXISTS commands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    command VARCHAR(255) NOT NULL,
    response TEXT NOT NULL
);

INSERT INTO commands (command, response) VALUES
('crear app android', 'La aplicación Android se está generando. Por favor, espera.'),
('crear app ios', 'La aplicación iOS se está generando. Por favor, espera.');
