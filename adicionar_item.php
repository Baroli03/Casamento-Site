<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>adicionar</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<?php
$arquivo_json = 'presentes.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = preg_replace('/[^a-z0-9\-]/', '-', strtolower(trim($_POST['nome'])));
    $descricao = trim($_POST['descricao']);
    $imagem = trim($_POST['imagem']);

    $novo_item = [
        "nome" => $nome,
        "descricao" => $descricao,
        "imagem" => $imagem
    ];

    $json = file_exists($arquivo_json) ? file_get_contents($arquivo_json) : '[]';
    $presentes = json_decode($json, true);
    $presentes[] = $novo_item;

    file_put_contents($arquivo_json, json_encode($presentes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    header("Location: index.php");
    exit();
}
?>

<h2 class ="titulo tenor">Adicionar Novo Presente</h2>

<form method="POST">
    <label for="nome">Identificador único (ex: cafeteira-gourmet):</label><br>
    <input type="text" name="nome" required><br><br>

    <label for="descricao">Descrição:</label><br>
    <textarea name="descricao" rows="4" cols="50" required></textarea><br><br>

    <label for="imagem">URL da imagem:</label><br>
    <input type="url" name="imagem" required><br><br>

    <button type="submit">Salvar</button>
</form>

</body>
</html>