<?php
// contato.php - processamento seguro do formulário
// Carrega variáveis de ambiente a partir de .env (opcional)
if (file_exists(__DIR__ . '/db/.env')) {
    $lines = file(__DIR__ . '/db/.env', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line),'#')===0) continue;
        [$k,$v] = array_map('trim', explode('=', $line, 2) + [null,null]);
        if ($k) putenv("$k=$v");
    }
}

$host = getenv('DB_HOST') ?: 'ec2-13-223-54-115.compute-1.amazonaws.com';
$db   = getenv('DB_NAME') ?: 'site_contato';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'ifsp';
$port = getenv('DB_PORT') ?: '3306';

// Validação mínima do formulário
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método não permitido.";
    exit;
}
if ($nome === '' || $email === '' || $mensagem === '') {
    http_response_code(400);
    echo "Preencha todos os campos.";
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Email inválido.";
    exit;
}

// Conexão PDO com tratamento de erros e charset
$dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Erro no servidor (DB).";
    exit;
}

// Inserção segura com prepared statement
$stmt = $pdo->prepare('INSERT INTO mensagens (nome,email,mensagem) VALUES (:nome,:email,:mensagem)');
$stmt->execute([
    ':nome' => $nome,
    ':email' => $email,
    ':mensagem' => $mensagem,
]);

echo "Mensagem enviada com sucesso.";
