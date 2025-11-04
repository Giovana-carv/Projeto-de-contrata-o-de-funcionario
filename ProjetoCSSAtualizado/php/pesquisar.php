    <?php
    include('conexao.php');
    session_start();
    $mensagem = '';
    $tipo_mensagem = '';

    if (isset($_SESSION['mensagem'])) {
        $mensagem = $_SESSION['mensagem'];
        $tipo_mensagem = $_SESSION['tipo_mensagem'] ?? 'sucesso';
        unset($_SESSION['mensagem'], $_SESSION['tipo_mensagem']);
    }

    $busca = $_GET['ocupacao'] ?? '';

    $sql = "SELECT * FROM usuarios WHERE tipo = 'funcionario' AND ocupacao LIKE ?";
    $stmt = $conn->prepare($sql);
    $termo = "%" . $busca . "%";
    $stmt->bind_param("s", $termo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Foto do cliente
    $stmtFoto = $conn->prepare("SELECT foto_perfil, nome FROM usuarios WHERE id = ?");
    $stmtFoto->bind_param("i", $_SESSION['id']);
    $stmtFoto->execute();
    $resFoto = $stmtFoto->get_result();
    $foto = $resFoto->fetch_assoc();
    ?>

    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar Funcionários</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f2f2f2;
        transition: 0.4s;
    }

    header {
        background: linear-gradient(135deg, #ff416c, #bb072d);
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
    }

    nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    nav .logo {
        display: flex;
        align-items: center;
        gap: 15px;
        color: #fff;
        font-weight: bold;
    }

    nav .logo img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
    }

    nav ul {
        display: flex;
        gap: 15px;
        list-style: none;
        align-items: center;
    }

    nav li a,
    nav li button {
        text-decoration: none;
        color: #fff;
        font-weight: 600;
        padding: 10px 15px;
        border-radius: 25px;
        transition: 0.3s;
        border: none;
        background: none;
        cursor: pointer;
    }

    nav li a:hover,
    nav li a.active {
        background: #fff;
        color: #ff416c;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
    }

    .theme-toggle {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .theme-toggle:hover {
        background: rgba(255, 255, 255, 0.35);
    }

    .menu-toggle {
        display: none;
        flex-direction: column;
        cursor: pointer;
        position: absolute;
        right: 20px;
        top: 25px;
        z-index: 1000;
    }

    .menu-toggle span {
        height: 3px;
        width: 25px;
        background: #fff;
        margin: 4px 0;
        border-radius: 2px;
        transition: 0.4s;
    }

    .menu-toggle.active span:nth-child(1) {
        transform: rotate(45deg) translate(5px, 5px);
    }

    .menu-toggle.active span:nth-child(2) {
        opacity: 0;
    }

    .menu-toggle.active span:nth-child(3) {
        transform: rotate(-45deg) translate(6px, -6px);
    }

    /* Pesquisa e botões */
    form {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 0 auto 30px auto;
        align-items: center;
        justify-content: center;
        max-width: 700px;
    }

    input[type="text"] {
        flex: 1 1 250px;
        max-width: 350px;
        padding: 10px 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
    }

    .voltar,
    .voltar a,
    button[type="submit"] {
        background-color: #ff416c;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 15px;
        cursor: pointer;
        transition: 0.3s;
        text-decoration: none;
    }

    .voltar:hover,
    button[type="submit"]:hover {
        background-color: #c03557;
    }

    /* Cards */
    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        gap: 20px;
        padding: 20px;
        margin: 0 auto 20px auto;
        align-items: center;
        max-width: 700px;
    }

    .card img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #ff416c;
    }

    .info {
        flex: 1;
    }

    .info strong {
        font-size: 20px;
        margin-bottom: 8px;
        display: block;
        color: #333;
    }

    .info p {
        font-size: 16px;
        color: #000;
        margin: 6px 0;
    }

    .info form {
        margin-top: 10px;
    }

    .info form select,
    .info form button {
        padding: 6px 10px;
        font-size: 15px;
        border-radius: 6px;
        border: none;
        margin-top: 5px;
    }

    .info form select {
        background: #f0f0f0;
        margin-right: 10px;
    }

    .btn-avaliar {
        background: #007BFF;
        color: #fff;
        transition: 0.3s;
    }

    .btn-avaliar:hover {
        background: #0056b3;
    }

    .btn-contratar {
        background: #4CAF50;
        color: #fff;
        transition: 0.3s;
    }

    .btn-contratar:hover {
        background: #3e8e41;
    }

    .imagens {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .imagens img:last-child {
        border-radius: 8px;
        width: 100px;
        height: auto;
        border: 2px dashed #aaa;
    }

    /* Mensagens */
    .mensagem {
        padding: 15px;
        margin: 0 auto 20px auto;
        border-radius: 8px;
        font-size: 16px;
        text-align: center;
        animation: fadeout 3s ease-in-out forwards;
        max-width: 700px;
    }

    .mensagem.sucesso {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .mensagem.erro {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @keyframes fadeout {
        0% {
            opacity: 1;
        }
        70% {
            opacity: 1;
        }
        100% {
            opacity: 0;
            display: none;
        }
    }

    /* Tema escuro */
    body.dark {
        background: #1e1e1e;
        color: #ddd;
    }

    body.dark .card {
        background: #2a2a2a;
        color: white;
    }

    body.dark strong {
        color: white;
    }

    body.dark p {
        color: white;
    }

    /* Responsivo */
    @media (max-width: 768px) {
        nav ul {
            flex-direction: column;
            position: absolute;
            top: 100%;
            right: 0;
            background: #ff416c;
            width: 220px;
            display: none;
            padding: 15px;
            border-bottom-left-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        nav ul.show {
            display: flex;
        }

        .menu-toggle {
            display: flex;
        }
    }


    </style>
    </head>
    <body>

    <header>
    <nav>
        <div class="logo">
            <?php if(!empty($foto['foto_perfil'])): ?>
                <img src="../uploads/<?php echo htmlspecialchars($foto['foto_perfil']); ?>" alt="Foto do Cliente">
            <?php endif; ?>
            <h1>Bem-vindo, <?php echo htmlspecialchars($foto['nome']); ?>!</h1>
        </div>
        <div class="menu-toggle" id="menu-toggle"><span></span><span></span><span></span></div>
        <ul id="menu">
            <li><a href="../cliente/painel_cliente.php">Início</a></li>
                <li><a href="../php/top10.php">Top 10 Funcionários</a></li>
                <li><a href="../php/editar_perfil_cliente.php">Editar Perfil</a></li>
                <li><a href="../php/pesquisar.php">Pesquisar</a></li>
                <li><a href="../cliente/notificacoes.php">Notificações</a></li>
                <li><a href="../php/logout.php">Sair</a></li>
            <li><button class="theme-toggle" id="theme-toggle">🌙</button></li>
        </ul>
    </nav>
    </header>
    /

    <?php if ($mensagem): ?>
        <div class="mensagem <?= $tipo_mensagem ?>">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <form method="GET">
        <input type="text" name="ocupacao" placeholder="Pesquisar por ocupação..." value="<?php echo htmlspecialchars($busca); ?>">
        <button type="submit" class="voltar">Buscar</button>
        <a href="../cliente/painel_cliente.php" class="voltar">Voltar</a>
    </form>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="card">
            <div class="imagens">
                <img src="../uploads/<?php echo $row['foto_perfil']; ?>" alt="Foto de perfil">
                <img src="../uploads/<?php echo $row['certificado']; ?>" alt="Certificado">
            </div>
            <div class="info">
                <strong><?php echo $row['nome']; ?></strong>
                <p>Ocupação: <?php echo htmlspecialchars($row['ocupacao']); ?></p>
                <p>
                    Avaliação Média: 
                    <?php 
                    $estrelas = round($row['media_avaliacao']);
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $estrelas ? "★" : "☆";
                    }
                    ?>
                </p>
                <form action="avaliar.php" method="POST">
                    <input type="hidden" name="id_funcionario" value="<?= $row['id'] ?>">
                    <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                    Avaliar:
                    <select name="estrelas" required>
                        <option value="1">1 estrela</option>
                        <option value="2">2 estrelas</option>
                        <option value="3">3 estrelas</option>
                        <option value="4">4 estrelas</option>
                        <option value="5">5 estrelas</option>
                    </select>
                    <button type="submit" class="btn-avaliar">Enviar</button>
                </form>

                <form action="contratar.php" method="POST">
                    <input type="hidden" name="funcionario_id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                    <button type="submit" class="btn-contratar">Contratar</button>
                </form>
            </div>
        </div>
    <?php } ?>

    <script>
    // Menu hambúrguer
    const toggle = document.getElementById("menu-toggle");
    const menu = document.getElementById("menu");
    toggle.addEventListener("click",()=>{menu.classList.toggle("show"); toggle.classList.toggle("active");});

    // Tema claro/escuro
    const themeToggle = document.getElementById("theme-toggle");
    const body = document.body;
    if(localStorage.getItem("theme")==="dark"){body.classList.add("dark"); themeToggle.textContent="☀️";}
    themeToggle.addEventListener("click",()=>{
        body.classList.toggle("dark");
        if(body.classList.contains("dark")){themeToggle.textContent="☀️"; localStorage.setItem("theme","dark");}
        else{themeToggle.textContent="🌙"; localStorage.setItem("theme","light");}
    });
    </script>
    </body>
    </html>
