<?php
$con = mysqli_connect("127.0.0.1", "root", "root", "yeticave", 3307);

if (!$con) {
  die("Ошибка подключения: " . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8");
