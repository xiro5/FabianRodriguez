<?php
// Define la contraseña que quieres usar
$password_plano = 'abc123';

// Genera el hash de la contraseña.
// PASSWORD_DEFAULT utiliza el mejor algoritmo de hashing disponible.
$password_hasheada = password_hash($password_plano, PASSWORD_DEFAULT);

// Imprime el hash. Cópialo para el siguiente paso.
echo "El hash de la contraseña es: " . $password_hasheada;
?>
