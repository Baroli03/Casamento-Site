<?php
session_start();

// Carregar usuários
$usuarios = [];
$arquivo_usuarios = 'usuarios.json';
if (file_exists($arquivo_usuarios)) {
    $usuarios = json_decode(file_get_contents($arquivo_usuarios), true);
}

// Processar login
if (isset($_POST['login'], $_POST['senha'])) {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    foreach ($usuarios as $usuario) {
        if ($usuario['login'] === $login && $usuario['senha'] === $senha) {
            $_SESSION['usuario'] = $login;
            break;
        }
    }

    if (!isset($_SESSION['usuario'])) {
        $erroLogin = "Login ou senha incorretos.";
    }
}

// Processar cadastro
if (isset($_POST['novo_login'], $_POST['nova_senha'])) {
    $novo_login = $_POST['novo_login'];
    $nova_senha = $_POST['nova_senha'];

    $usuarios[] = ["login" => $novo_login, "senha" => $nova_senha];
    file_put_contents($arquivo_usuarios, json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $_SESSION['usuario'] = $novo_login;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Sair
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Carregar presentes
$arquivo_json = 'presentes.json';
$itens_extra = [];
if (file_exists($arquivo_json)) {
    $itens_extra = json_decode(file_get_contents($arquivo_json), true);
}

// Adicionar novo item
if (isset($_POST['novo_nome'], $_POST['novo_descricao'], $_POST['novo_imagem']) && isset($_SESSION['usuario'])) {
    $novo_item = [
        "nome" => $_POST['novo_nome'],
        "descricao" => $_POST['novo_descricao'],
        "imagem" => $_POST['novo_imagem']
    ];
    $itens_extra[] = $novo_item;
    file_put_contents($arquivo_json, json_encode($itens_extra, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $_SESSION['presente'] = $novo_item['nome'];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Seleção de presente
if (isset($_POST['presente'])) {
    $_SESSION['presente'] = $_POST['presente'];
}

$presente = $_SESSION['presente'] ?? null;

// Dados dos itens fixos
$fixos = [
    "panela-electrica" => [
        "descricao" => "Uma panela versátil com várias funções para facilitar o preparo de refeições.",
        "imagem" => "img/panela-electrica.jpg"
    ],
    "conjunto-toalha" => [
        "descricao" => "Toalhas macias e de alta qualidade para sua casa.",
        "imagem" => "img/conjunto-toalha.jpg"
    ],
    "jogo-copos" => [
        "descricao" => "Copos elegantes e resistentes para diversas ocasiões.",
        "imagem" => "img/jogo-copos.jpg"
    ],
    "liquidificador" => [
        "descricao" => "Perfeito para preparar sucos, vitaminas e diversas receitas.",
        "imagem" => "img/liquidificador.jpg"
    ],
    "conjunto-cama" => [
        "descricao" => "Conjunto completo para garantir seu conforto e bem-estar.",
        "imagem" => "img/conjunto-cama.jpg"
    ],
    "vale-presente" => [
        "descricao" => "Um presente flexível que permite escolher o que desejar.",
        "imagem" => "img/vale-presente.jpg"
    ]
];

// Verifica se presente está nos itens extras
$item_detalhes = null;
foreach ($itens_extra as $item) {
    if ($item['nome'] === $presente) {
        $item_detalhes = $item;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title id="descricoes" class ="titulo tenor">Escolha de Presente</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include 'header.php'; ?>



 <section id="Login">
<?php if (!isset($_SESSION['usuario'])): ?>
    <div id="logar">
    <h3>Login</h3>
    <?php if (!empty($erroLogin)) echo "<p style='color:red;'>$erroLogin</p>"; ?>
    <form method="POST">
        <input type="text" name="login" placeholder="Login" required><br><br>
        <input type="password" name="senha" placeholder="Senha" required><br><br>
        <button type="submit">Entrar</button>
    </form>
    </div>
    <div id="cadastrar">
    <h4>Ou cadastre-se:</h4>
    <form method="POST">
        <input type="text" name="novo_login" placeholder="Novo login" required><br><br>
        <input type="password" name="nova_senha" placeholder="Nova senha" required><br><br>
        <button type="submit">Criar Conta</button>
    </form>
    </div>
<?php else: ?>
    <p>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']) ?> | <a href="?logout=1">Sair</a></p>
<?php endif; ?>

</section>
<h2 id="descricoes" class ="titulo tenor">Escolha um presente:</h2>

<form method="POST">
    <select name="presente" required onchange="this.form.submit()">
        <option value="">-- Selecione --</option>

        <?php foreach ($fixos as $nome => $dados): ?>
            <option value="<?= $nome ?>" <?= ($presente == $nome) ? "selected" : "" ?>><?= ucwords(str_replace("-", " ", $nome)) ?></option>
        <?php endforeach; ?>

        <?php foreach ($itens_extra as $item): ?>
            <option value="<?= htmlspecialchars($item['nome']) ?>" <?= ($presente == $item['nome']) ? "selected" : "" ?>>
                <?= htmlspecialchars($item['nome']) ?>
            </option>
        <?php endforeach; ?>

        <option value="adicionar-novo-item" <?= ($presente == "adicionar-novo-item") ? "selected" : "" ?>>➕ Adicionar Novo Item</option>
    </select>
</form>

<?php if ($presente === "adicionar-novo-item"): ?>
    <hr>
    <?php if (isset($_SESSION['usuario'])): ?>
        <h3 id="descricoes" class ="titulo tenor">Adicionar Novo Presente</h3>
        <form method="POST">
            <input type="text" name="novo_nome" placeholder="Nome único" required><br><br>
            <textarea name="novo_descricao" placeholder="Descrição" required></textarea><br><br>
            <input type="text" name="novo_imagem" placeholder="URL da imagem" required><br><br>
            <button type="submit">Salvar Item</button>
        </form>
    <?php else: ?>
        <p id = "descricoes"class ="tenor">Você precisa estar logado para adicionar novos itens.</p>
    <?php endif; ?>
<?php endif; ?>

<?php if ($presente && $presente !== "adicionar-novo-item"): ?>
    <hr>
    <?php if ($item_detalhes): ?>
        <h3 id="descricoes" class ="titulo tenor"><?= htmlspecialchars($item_detalhes['nome']) ?></h3>
        <p id="descricoes" class ="tenor"><?= htmlspecialchars($item_detalhes['descricao']) ?></p>
        <img class = "img-presente" src="<?= htmlspecialchars($item_detalhes['imagem']) ?>" width="300">
    <?php elseif (isset($fixos[$presente])): ?>
        <h3 id="descricoes" class ="titulo tenor"><?= ucwords(str_replace("-", " ", $presente)) ?></h3>
        <p id="descricoes" class = "tenor"><?= htmlspecialchars($fixos[$presente]['descricao']) ?></p>
        <img class = "img-presente" src="<?= htmlspecialchars($fixos[$presente]['imagem']) ?>" width="300">
    <?php else: ?>
        <p id = "descricoes"class ="tenor">Presente não encontrado.</p>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>
