    <?php
    session_start();

    // Permitir apenas funcionários
    if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'funcionario') {
        header("Location: ../html/loginCadastro.html");
        exit;
    }

    $conn = new mysqli("localhost", "root", "", "sistema_usuarios");
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    $id_funcionario = intval($_SESSION['id']);
    $nomeFuncionario = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Funcionário';

    // Atualizar dados do perfil
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $endereco = $_POST['endereco'] ?? '';
        $senha = $_POST['senha'] ?? '';

        if (!empty($_FILES['foto_perfil']['name'])) {
            $foto_nome = basename($_FILES['foto_perfil']['name']);
            $foto_destino = "../uploads/" . $foto_nome;
            move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $foto_destino);
        }

        $query = "UPDATE usuarios SET nome=?, email=?, telefone=?, endereco=?";
        $params = [$nome, $email, $telefone, $endereco];
        $types = "ssss";

        if (!empty($senha)) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $query .= ", senha=?";
            $params[] = $senha_hash;
            $types .= "s";
        }

        if (!empty($_FILES['foto_perfil']['name'])) {
            $query .= ", foto_perfil=?";
            $params[] = $foto_nome;
            $types .= "s";
        }

        $query .= " WHERE id=?";
        $params[] = $id_funcionario;
        $types .= "i";

        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            header("Location: ../funcionario/painel_funcionario.php");
            exit;
        }
    }

    // Buscar dados do funcionário
    $stmt = $conn->prepare("SELECT nome, email, telefone, endereco, foto_perfil FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id_funcionario);
    $stmt->execute();
    $res = $stmt->get_result();
    $usuario = $res->fetch_assoc();
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
    <meta charset="UTF-8">
    <title>Editar Perfil - Funcionário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family: Arial, sans-serif; }
    body { background-color:#fffaf3; color:#212121; }
    a { text-decoration:none; color:#fff; transition:0.2s; }

    .nav {
        display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center;
        padding:0.8rem 1.5rem; color:#fff;
        background: linear-gradient(#ff4b2b,#ff416c);
    }
    .nav .logo { display:flex; align-items:center; gap:10px; }
    .nav .logo img { width:50px; height:50px; object-fit:cover; border-radius:50%; border:2px solid #fff; }
    .nav .links { display:flex; flex-wrap:wrap; gap:10px; margin-top:10px; }
    .nav .links a { font-weight:bold; color:white; padding:0.4rem 0.8rem; transition:0.3s; }
    .nav .links a:not(.login):hover { border-bottom:2px solid #fff; }
    .nav .links .login { background-color:#fff; color:#212121; padding:0.5rem 1.5rem; border-radius:1.5rem; }
    .nav .links .login:hover { background-color:rgb(201,60,35); color:#fff; }

    .container { max-width:500px; margin:2rem auto; background:#ffffff; padding:2rem; border-radius:10px; box-shadow:0 4px 12px #ff416c; }
    .container h2 { text-align:center; margin-bottom:1.5rem; }

    .form-group { margin-bottom:1.2rem; }
    label { display:block; font-weight:bold; margin-bottom:0.4rem; }
    input[type="text"], input[type="email"], input[type="password"], input[type="file"] { width:100%; padding:0.6rem; border:1px solid #ccc; border-radius:5px; }

    button { padding:10px 15px; border:none; border-radius:5px; background-color:rgb(185,40,74); color:#fff; cursor:pointer; transition:0.2s; }
    button:hover { background-color:rgb(170,36,67); }
    .button-group { display:flex; justify-content:space-between; gap:10px; margin-top:1rem; }

    @media(max-width:600px){ .nav { flex-direction:column; align-items:flex-start; } .nav .links { justify-content:flex-start; } }
    </style>
    </head>
    <body>
    <nav class="nav">
        <div class="logo">
            <?php if (!empty($usuario['foto_perfil'])): ?>
                <img src="../uploads/<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="foto de perfil">
            <?php endif; ?>
            <h1>Bem-vindo, <?php echo htmlspecialchars($nomeFuncionario); ?>!</h1>
        </div>
        <div class="links">
            <a href="../funcionario/galeria.php">Galeria</a>
            <a href="../php/solicitacoes.php">Solicitações de Contratação</a>
            <a href="../php/em_atendimento.php">Em Atendimento</a>
            <a href="../php/editar_perfil.php">Editar Perfil</a>
            <a href="../funcionario/mensagens.php">Mensagens</a>
            <a href="#">Sobre nós</a>
            <a class="login" href="../php/logout.php">Sair</a>
        </div>
    </nav>

    <div class="container">
        <h2>Atualizar Informações</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="text" name="telefone" id="telefone" value="<?php echo htmlspecialchars($usuario['telefone']); ?>">
            </div>
            <div class="form-group">
                <label for="endereco">Endereço:</label>
                <input type="text" name="endereco" id="endereco" value="<?php echo htmlspecialchars($usuario['endereco']); ?>">
            </div>
            <div class="form-group">
                <label for="senha">Nova Senha:</label>
                <input type="password" name="senha" id="senha" placeholder="Deixe em branco para não alterar">
            </div>
            <div class="form-group">
                <label for="foto_perfil">Foto de Perfil:</label>
                <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*">
            </div>
            <div class="button-group">
                <button type="submit">Salvar Alterações</button>
                <a href="../funcionario/painel_funcionario.php"><button type="button">Voltar</button></a>
            </div>
        </form>
    </div>
    </body>
    </html>
