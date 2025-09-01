<?php
// listar.php - mostra mensagens salvas
if (file_exists(__DIR__ . '/db/.env')) {
    $lines = file(__DIR__ . '/db/.env', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line),'#')===0) continue;
        [$k,$v] = array_map('trim', explode('=', $line, 2) + [null,null]);
        if ($k) putenv("$k=$v");
    }
}

$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'site_contato';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'ifsp';
$port = getenv('DB_PORT') ?: '3306';

$dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco.";
    exit;
}

$stmt = $pdo->query('SELECT id,nome,email,mensagem,criado_em FROM mensagens ORDER BY criado_em DESC LIMIT 200');
$mensagens = $stmt->fetchAll();
?>
<!doctype html>
<html lang="pt-BR">
<head><meta charset="utf-8"/><title>Mensagens</title><link rel="stylesheet" href="estilos.css"></head>
<body>
<header><h1>Mensagens</h1><nav><a href="index.html">Voltar</a></nav></header>
<main>
<?php if (!$mensagens): ?>
  <p>Nenhuma mensagem encontrada.</p>
<?php else: foreach ($mensagens as $m): ?>
  <article style="border:1px solid #ddd;padding:.7rem;margin:.7rem 0;border-radius:6px;">
    <strong><?=htmlspecialchars($m['nome'])?></strong> — <small><?=htmlspecialchars($m['email'])?></small>
    <div style="margin-top:.5rem"><?=nl2br(htmlspecialchars($m['mensagem']))?></div>
    <div style="margin-top:.5rem;color:#666;font-size:.9rem"><?=htmlspecialchars($m['criado_em'])?></div>
  </article>
<?php endforeach; endif; ?>
</main>
</body>
</html>
