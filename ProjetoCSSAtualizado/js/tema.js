document.addEventListener("DOMContentLoaded", () => {
  const body = document.body;
  const themeToggle = document.getElementById("theme-toggle");

  // Aplica tema salvo no localStorage
  if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
    if (themeToggle) themeToggle.textContent = "☀️";
  }

  // Alternar tema ao clicar no botão
  if (themeToggle) {
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
  }
});
