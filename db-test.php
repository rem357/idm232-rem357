<?php
// Use your existing connection file
require 'connection.php';

// Basic query: get all recipes
$sql = "SELECT * FROM recipes"; // table name = recipes
$result = mysqli_query($conn, $sql);

// Turn the result into a PHP array
$recipes = [];
if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $recipes[] = $row;
  }
} else {
  die("Query error: " . mysqli_error($conn));
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>DB Test — Recipes</title>
  <style>
    body { font-family: system-ui, Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; max-width: 900px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f4f4f8; }
  </style>
</head>
<body>
  <h1>Database Test — Recipes</h1>

  <?php if (empty($recipes)): ?>
    <p>No recipes found in the <strong>recipes</strong> table.</p>
  <?php else: ?>
    <table>
      <tr>
        <?php foreach (array_keys($recipes[0]) as $col): ?>
          <th><?= htmlspecialchars($col) ?></th>
        <?php endforeach; ?>
      </tr>
      <?php foreach ($recipes as $row): ?>
        <tr>
          <?php foreach ($row as $value): ?>
            <td><?= htmlspecialchars($value) ?></td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</body>
</html>
