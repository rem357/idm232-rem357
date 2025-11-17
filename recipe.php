<?php
// 1. Connect to the database
require 'connection.php';

// 2. Get the recipe ID from the URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
  http_response_code(404);
  echo "Invalid recipe ID. <a href='index.php'>Back</a>";
  exit;
}

// 3. Query the database for THIS recipe
$sql = "SELECT * FROM recipes WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
  http_response_code(404);
  echo "Recipe not found. <a href='index.php'>Back</a>";
  exit;
}

// 4. Turn the row into a PHP array
$recipe = mysqli_fetch_assoc($result);

// Convert long text fields into arrays for ingredients + steps
$ingredients = array_filter(array_map('trim', explode("\n", $recipe['ingredients'])));
$steps = array_filter(array_map('trim', explode("\n", $recipe['steps'])));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($recipe['title']) ?> — Cookbook</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>

<body>

  <p><a href="index.php">← Back to recipes</a></p>

  <h1><?= htmlspecialchars($recipe['title']) ?></h1>

  <div class="badges">
    <span><?= htmlspecialchars($recipe['category']) ?></span>
    <?php if (!empty($recipe['cook_time'])): ?>
      <span><?= htmlspecialchars($recipe['cook_time']) ?></span>
    <?php endif; ?>
    <?php if (!empty($recipe['difficulty'])): ?>
      <span><?= htmlspecialchars($recipe['difficulty']) ?></span>
    <?php endif; ?>
    <?php if (!empty($recipe['servings'])): ?>
      <span><?= htmlspecialchars($recipe['servings']) ?> servings</span>
    <?php endif; ?>
  </div>

  <?php if (!empty($recipe['description'])): ?>
    <p><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
  <?php endif; ?>

  <div class="section">
    <h2>Ingredients</h2>
    <ul>
      <?php foreach ($ingredients as $item): ?>
        <li><?= htmlspecialchars($item) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="section">
    <h2>Steps</h2>
    <ol>
      <?php foreach ($steps as $step): ?>
        <?php
          $cleanStep = preg_replace('/^\d+\.\s*/', '', $step);
        ?>
        <li><?= htmlspecialchars($cleanStep) ?></li>
      <?php endforeach; ?>
    </ol>
  </div>



</body>
</html>
