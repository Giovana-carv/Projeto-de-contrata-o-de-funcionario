<?php
session_start();
session_destroy();
header("Location: ../html/loginCadastro2.html");
exit;
?>