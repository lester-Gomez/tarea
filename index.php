<?php declare(strict_types=1);

$host     = 'localhost';
$db       = 'tarea';
$user     = 'root';
$password = '';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
    PDO::ATTR_EMULATE_PREPARES   => false,                  
];

function getConexion(string $dsn, string $user, string $password, array $options): PDO {
    return new PDO($dsn, $user, $password, $options);
}


echo "<h2> 1. SELECT - Listar Usuarios</h2>";

try {
    $pdo = getConexion($dsn, $user, $password, $options);

    $sql = 'SELECT ID, PRIMER_NOMBRE, PRIMER_APELLIDO, ALIAS FROM USUARIO';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $usuarios = $stmt->fetchAll();

    if ($usuarios) {
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
        echo "<tr style='background-color: #4CAF50; color: white;'>";
        echo "<th>ID</th><th>Primer Nombre</th><th>Primer Apellido</th><th>Alias</th>";
        echo "</tr>";
        foreach ($usuarios as $usuario) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars((string)$usuario['ID']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario['PRIMER_NOMBRE']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario['PRIMER_APELLIDO']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario['ALIAS']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No se encontraron usuarios.<br>";
    }

} catch (\PDOException $e) {
    echo "Error en SELECT: " . $e->getMessage();
}

echo "<h2>2. INSERT - Insertar Nuevo Usuario</h2>";

try {
    $pdo = getConexion($dsn, $user, $password, $options);
    $id             = 3;
    $primerNombre   = 'Ana';
    $primerApellido = 'López';
    $alias          = 'alopez';

    $sql = 'INSERT INTO USUARIO (ID, PRIMER_NOMBRE, PRIMER_APELLIDO, ALIAS) 
            VALUES (:ID, :PRIMER_NOMBRE, :PRIMER_APELLIDO, :ALIAS)';
    
    $stmt = $pdo->prepare($sql);

    $resultado = $stmt->execute([
        'ID'              => $id,
        'PRIMER_NOMBRE'   => $primerNombre,
        'PRIMER_APELLIDO' => $primerApellido,
        'ALIAS'           => $alias
    ]);

    if ($resultado) {
        echo "¡Registro insertado con éxito! <br>";
        echo "El ID asignado al nuevo usuario es: " . $pdo->lastInsertId();
    }

} catch (\PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "Error: El ID o alias ya se encuentra registrado.";
    } else {
        echo "Error al insertar: " . $e->getMessage();
    }
}


echo "<h2>3. UPDATE - Actualizar Usuario</h2>";

try {
    $pdo = getConexion($dsn, $user, $password, $options);

    
    $id             = 3;
    $primerNombre   = 'Ana María';
    $primerApellido = 'López García';
    $alias          = 'alopez_actualizado';

    $sql = 'UPDATE USUARIO 
            SET PRIMER_NOMBRE  = :PRIMER_NOMBRE,
                PRIMER_APELLIDO = :PRIMER_APELLIDO,
                ALIAS           = :ALIAS
            WHERE ID = :ID';

    $stmt = $pdo->prepare($sql);

    $resultado = $stmt->execute([
        'ID'              => $id,
        'PRIMER_NOMBRE'   => $primerNombre,
        'PRIMER_APELLIDO' => $primerApellido,
        'ALIAS'           => $alias
    ]);

    $filasAfectadas = $stmt->rowCount();

    if ($resultado && $filasAfectadas > 0) {
        echo "¡Registro actualizado con éxito! 🎉<br>";
        echo "Filas afectadas: " . $filasAfectadas;
    } elseif ($resultado && $filasAfectadas === 0) {
        echo "No se encontró ningún registro con ID = $id para actualizar.<br>";
    }

} catch (\PDOException $e) {
    echo "Error al actualizar: " . $e->getMessage();
}




echo "<h2>4. DELETE - Eliminar Usuario</h2>";

try {
    $pdo = getConexion($dsn, $user, $password, $options);

    
    $id = 3;

    $sql = 'DELETE FROM USUARIO WHERE ID = :ID';

    $stmt = $pdo->prepare($sql);

    $resultado = $stmt->execute([
        'ID' => $id
    ]);

    $filasAfectadas = $stmt->rowCount();

    if ($resultado && $filasAfectadas > 0) {
        echo "¡Registro eliminado con éxito! 🗑️<br>";
        echo "Filas afectadas: " . $filasAfectadas;
    } elseif ($resultado && $filasAfectadas === 0) {
        echo "No se encontró ningún registro con ID = $id para eliminar.<br>";
    }

} catch (\PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "Error: No se puede eliminar porque tiene dependencias (llaves foráneas).";
    } else {
        echo "Error al eliminar: " . $e->getMessage();
    }
}

