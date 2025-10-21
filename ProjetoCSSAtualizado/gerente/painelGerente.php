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
<title>Painel do Gerente</title>

<style>
/* ==== BASE GLOBAL (mantém o estilo do CSS fornecido) ==== */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  background: #f6f5f7;
  font-family: 'Arial', sans-serif;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 100vh;
}

/* ==== NAVBAR ==== */
.nav {
  width: 100%;
  background: linear-gradient(to right, #ff4b2b, #ff416c);
  color: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 2rem;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.nav .logo {
  display: flex;
  align-items: center;
  gap: 15px;
}

.nav .logo img {
  width: 55px;
  height: 55px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #fff;
}

.nav .logo h2 {
  color: #fff;
  font-size: 20px;
  white-space: nowrap;
}

.nav .links {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.nav .links a {
  color: #fff;
  font-weight: bold;
  padding: 8px 16px;
  border-radius: 20px;
  transition: all 0.3s ease-in-out;
}

.nav .links a:hover {
  background-color: rgba(255,255,255,0.2);
}

.nav .links .login {
  background-color: #fff;
  color: #ff4b2b;
  border: none;
  font-weight: bold;
  transition: all 0.3s ease;
}

.nav .links .login:hover {
  background-color: #ff4b2b;
  color: #fff;
}

/* ==== CONTEÚDO PRINCIPAL ==== */
.main-content {
  margin-top: 40px;
  background: #fff;
  border-radius: 15px;
  padding: 30px 50px;
  width: 90%;
  max-width: 900px;
  box-shadow: 0 14px 28px rgba(0,0,0,0.15),
              0 10px 10px rgba(0,0,0,0.12);
  text-align: center;
  transition: 0.5s ease;
}

.main-content:hover {
  transform: scale(1.02);
}

.main-content h2 {
  color: #1a202c;
  font-size: 26px;
  margin-bottom: 15px;
}

.main-content p {
  color: #333;
  font-size: 16px;
  margin-bottom: 10px;
}

/* ==== BOTÃO DE TEMA ==== */
.theme-toggle {
  background: rgba(255,255,255,0.2);
  border: none;
  border-radius: 50%;
  width: 45px;
  height: 45px;
  font-size: 20px;
  color: #fff;
  cursor: pointer;
  transition: background 0.3s ease;
}

.theme-toggle:hover {
  background: rgba(255,255,255,0.4);
}

/* ==== MODO ESCURO ==== */
body.dark {
  background: #1e1e1e;
  color: #f1f1f1;
}

body.dark .main-content {
  background: #1a1a1aff;
  color: #ddd;
}
body.dark .main-content h2{
    color: #bbb;
}
body.dark .main-content p{
    color: #bbb;
}

body.dark .nav {
  background: linear-gradient(to right, #222, #444);
}

body.dark .nav .links a:hover {
  background-color: rgba(255,255,255,0.1);
}

body.dark .nav .links .login {
  background: #fff;
  color: #222;
}

body.dark .nav .links .login:hover {
  background: #ff4b2b;
  color: #fff;
}

/* ==== RESPONSIVIDADE ==== */
@media (max-width: 768px) {
  .nav {
    flex-direction: column;
    text-align: center;
  }

  .nav .links {
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 10px;
  }

  .main-content {
    padding: 20px;
  }
}
</style>
</head>

<body>

<!-- ==== NAVBAR ==== -->
<nav class="nav">
  <div class="logo">
    <button class="theme-toggle" id="theme-toggle">🌙</button>
    <img src="../uploads/<?php echo $_SESSION['foto_perfil']; ?>" alt="Foto do gerente">
    <h2>Olá, <?php echo $_SESSION['nome']; ?>!</h2>
  </div>

  <div class="links">
    <a href="painelDenuncias.php">Denúncias</a>
    <a href="../dashboard/dashboard.php">Dashboard</a>
    <a href="historicoAcoes.php">Histórico</a>
    <a class="login" href="logoutGerente.php">Sair</a>
  </div>
</nav>

<!-- ==== CONTEÚDO PRINCIPAL ==== -->
<section class="main-content">
  <h2>Bem-vindo ao Painel do Gerente</h2>
  <p>Email: <?php echo $_SESSION['email']; ?></p>
  <p>Aqui você pode gerenciar denúncias, acompanhar o histórico de ações e acessar o painel geral do sistema.</p>
</section>

<script>
// Script de alternância de tema
const btnTheme = document.getElementById('theme-toggle');
btnTheme.addEventListener('click', () => {
  document.body.classList.toggle('dark');
  btnTheme.textContent = document.body.classList.contains('dark') ? '☀️' : '🌙';
});
</script>

</body>
</html>