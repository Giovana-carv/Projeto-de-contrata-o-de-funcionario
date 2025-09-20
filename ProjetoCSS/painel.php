
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel do Usuário</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: #f9fafc;
      color: #333;
      line-height: 1.6;
      transition: background 0.4s, color 0.4s;
    }

    header {
      background: linear-gradient(135deg, cadetblue, teal);
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
      border-bottom-left-radius: 15px;
      border-bottom-right-radius: 15px;
      position: relative;
    }

    nav {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;
      gap: 20px;
    }

    nav img {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #fff;
      box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    }

    nav ul {
      display: flex;
      gap: 25px;
      list-style: none;
      align-items: center;
    }

    nav li a {
      text-decoration: none;
      color: #fff;
      font-weight: 600;
      font-size: 1rem;
      padding: 10px 18px;
      border-radius: 25px;
      transition: all 0.3s ease-in-out;
    }

    nav li a:hover,
    nav li a.active {
      background: #fff;
      color: cadetblue;
      box-shadow: 0 3px 8px rgba(0,0,0,0.2);
    }

    .theme-toggle {
      cursor: pointer;
      border: none;
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
      font-size: 1.2rem;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: 0.3s;
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

    main {
      padding: 40px 20px;
      max-width: 1200px;
      margin: auto;
    }

    .container {
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
    }

    .content {
      flex: 2;
      min-width: 300px;
    }

    .sidebar {
      flex: 1;
      min-width: 250px;
    }

    .card {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s, background 0.4s, color 0.4s;
    }

    .card:hover {
      transform: translateY(-3px);
    }

    h2, h3 {
      margin-bottom: 15px;
    }

    h2 {
      color: cadetblue;
    }

    h3 {
      color: teal;
    }

    p {
      font-size: 0.95rem;
      color: #555;
    }

    body.dark {
      background: #1e1e1e;
      color: #ddd;
    }

    body.dark .card {
      background: #2a2a2a;
      color: #ddd;
    }

    body.dark nav li a:hover,
    body.dark nav li a.active {
      background: #fff;
      color: #1e1e1e;
    }

    @media (max-width: 768px) {
      nav ul {
        flex-direction: column;
        position: absolute;
        top: 100%;
        right: 0;
        background: cadetblue;
        width: 220px;
        display: none;
        padding: 15px;
        border-bottom-left-radius: 10px;
        box-shadow: 0 5px 10px rgba(0,0,0,0.2);
      }

      nav ul.show {
        display: flex;
      }

      .menu-toggle {
        display: flex;
      }

      nav {
        justify-content: space-between;
      }
    }
  </style>
</head>
<body>
  <header>
    <nav>
      <img src="../fotos/campo.jpeg" alt="Foto do Usuário">
        <h1>Bem-vindo, <?php //echo htmlspecialchars($_SESSION['nome']); ?>!</h1>

      <!-- Botão hambúrguer -->
      <div class="menu-toggle" id="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
      </div>

      <ul id="menu">
        <li><a href="" class="active">Home</a></li>
        <li><a href="../php/perfilUsuario.php">Seu Perfil</a></li>
        <li><a href="">Sobre</a></li>
        <li><button class="theme-toggle" id="theme-toggle">🌙</button></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="container">
      <div class="content">
        <section class="card">
          <h2>Começo do conteúdo</h2>
          <p>Este é um exemplo de painel de usuário com layout moderno, menu responsivo e alternância de tema claro/escuro.</p>
        </section>
      </div>

      <aside class="sidebar">
        <div class="card">
          <h3>Barra Lateral</h3>
          <p>Conteúdo adicional ou atalhos rápidos.</p>
        </div>
      </aside>
    </div>
  </main>

  <script>
    // Menu hambúrguer
    const toggle = document.getElementById("menu-toggle");
    const menu = document.getElementById("menu");

    toggle.addEventListener("click", () => {
      menu.classList.toggle("show");
      toggle.classList.toggle("active");
    });

    // Tema claro/escuro
    const themeToggle = document.getElementById("theme-toggle");
    const body = document.body;

    // Carrega preferência salva
    if (localStorage.getItem("theme") === "dark") {
      body.classList.add("dark");
      themeToggle.textContent = "☀️";
    }

    themeToggle.addEventListener("click", () => {
      body.classList.toggle("dark");

      if (body.classList.contains("dark")) {
        themeToggle.textContent = "☀️";
        localStorage.setItem("theme", "dark");
      } else {
        themeToggle.textContent = "🌙";
        localStorage.setItem("theme", "light");
      }
    });
  </script>
</body>
</html>
