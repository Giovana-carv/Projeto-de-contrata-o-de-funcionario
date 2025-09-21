<?php
session_start();
session_destroy();
header("Location: loginGerentev.php");
exit;
