<?php
session_start();
session_destroy();
header("Location: loginGerente.php");
exit;