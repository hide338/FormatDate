<?php
  require_once(__DIR__ . '/FormatDate.php');
  $format_date = new FormatDate();
  $output_format_date = $format_date->format('Y-m-d')

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FormatDate</title>
</head>
<body>
  <h1><?= $output_format_date ?></h1>
</body>
</html>