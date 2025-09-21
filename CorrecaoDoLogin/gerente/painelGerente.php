<?php
session_start();
if (!isset($_SESSION['id_gerente'])) {
    header("Location: loginGerente.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Gerente</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
        }
        a {
            text-decoration: none;
            color: #fff;
        }
        .nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 1.5rem;
            color: #fff;
            background-color: #ff4b2b;
        }
        .nav .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav .logo img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        .nav .links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        .nav .links a {
            color: white;
            font-weight: bold;
            font-size: 1rem;
            padding: 0.4rem 0.8rem;
            transition: 0.3s;
        }
        .nav .links a:not(.login):hover {
            color: #fff;
            border-bottom: 2px solid #fff;
        }
        .nav .links .login {
            background-color: #fff;
            color: #212121;
            padding: 0.5rem 1.5rem;
            border-radius: 1.5rem;
        }
        .nav .links .login:hover {
            background-color:rgb(201, 60, 35);
            color: #fff;
        }
        h2 {
            padding: 1rem;
            text-align: center;
        }
        @media(max-width: 600px) {
            .nav {
                flex-direction: column;
                align-items: flex-start;
            }
            .nav .links {
                justify-content: flex-start;
                margin-top: 10px;
            }
            h2 {
                font-size: 1.3rem;
            }
        }

        .theme-toggle {
      background:rgba(255,255,255,0.2);
      border-radius:50%;
      width:40px;
      height:40px;
      display:flex;
      align-items:center;
      justify-content:center;
    }
    .theme-toggle:hover {
      background:rgba(255,255,255,0.35);
      cursor: pointer;
    }
    body.dark {
      background:#1e1e1e;
      color: black;
    }
    body h2{
        color: black;
        text-align: center;
    }
    body.dark h2{
        color: white;
        text-align: center;
    }
    body.dark .card {
      background:#2a2a2a;
      color:#ddd;
    }
    </style>
</head>
<body>

<nav class="nav">
    <div class="logo">
        <button class="theme-toggle" id="theme-toggle">🌙</button>

<h2>Bem-vindo Gerente, <?php echo $_SESSION['nome']; ?>!</h2>
<img src="../uploads/<?php echo $_SESSION['foto_perfil']; ?>" width="120" alt="Foto do gerente">
<p>Email: <?php echo $_SESSION['email']; ?></p>
    </div>

    <div class="links">
        <a href="painelDenuncias.php">Ver Denúcias</a>
        <a href="../dashboard/dashboard.php">Dashboard</a>
        <a class="login" href="logoutGerente.php">Sair</a>
</nav>

    <script src="../js/tema.js"></script>

</body>
</html>