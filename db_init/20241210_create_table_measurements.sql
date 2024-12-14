CREATE TABLE measurements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    temperature FLOAT NOT NULL,
    timestamp DATETIME NOT NULL,
    weather VARCHAR(255),
    notes TEXT
);
