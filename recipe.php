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
$result = mysqli_query($connection, $sql);

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



// Load images from images/recipes/{id}/final, /ingredients, /steps
$finalImage        = null;
$ingredientsImages = [];
$stepsImages       = [];

$baseDir  = __DIR__ . '/images/recipes/' . $id;
$baseUrl  = 'images/recipes/' . $id . '/';
$extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Final (one main hero image)
$finalDir = $baseDir . '/final';
if (is_dir($finalDir)) {
  foreach ($extensions as $ext) {
    $matches = glob($finalDir . '/*.' . $ext);
    if ($matches && count($matches) > 0) {
      // Just use the first file as the hero image
      $finalImage = $baseUrl . 'final/' . basename($matches[0]);
      break;
    }
  }
}

// Ingredient photos
$ingredientsDir = $baseDir . '/ingredients';
if (is_dir($ingredientsDir)) {
  foreach ($extensions as $ext) {
    foreach (glob($ingredientsDir . '/*.' . $ext) as $path) {
      $ingredientsImages[] = $baseUrl . 'ingredients/' . basename($path);
    }
  }
}

// Step photos
$stepsDir = $baseDir . '/steps';
if (is_dir($stepsDir)) {
  foreach ($extensions as $ext) {
    foreach (glob($stepsDir . '/*.' . $ext) as $path) {
      $stepsImages[] = $baseUrl . 'steps/' . basename($path);
    }
  }
}


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

<?php if ($finalImage): ?>
  <div class="recipe-hero">
    <img src="<?= htmlspecialchars($finalImage) ?>" alt="<?= htmlspecialchars($recipe['title']) ?>">
  </div>
<?php endif; ?>

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


<div class="section-block ingredients-layout">
  <div class="ingredients-text">
    <h2>Ingredients</h2>
    <ul>
      <?php foreach ($ingredients as $item): ?>
        <li><?= htmlspecialchars($item) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <?php if (!empty($ingredientsImages)): ?>
    <div class="ingredients-photos">
      <?php foreach ($ingredientsImages as $src): ?>
        <figure class="recipe-photo">
          <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($recipe['title']) ?> – ingredient photo">
        </figure>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>



<div class="section-block steps-layout">
  <div class="steps-text">
    <h2>Steps</h2>
    <ol>
      <?php foreach ($steps as $step): ?>
        <?php
          // keep your number-stripping fix
          $cleanStep = preg_replace('/^\d+\.\s*/', '', $step);
        ?>
        <li><?= htmlspecialchars($cleanStep) ?></li>
      <?php endforeach; ?>
    </ol>
  </div>

  <?php if (!empty($stepsImages)): ?>
    <div class="steps-photos">
      <?php foreach ($stepsImages as $src): ?>
        <figure class="recipe-photo">
          <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($recipe['title']) ?> – step photo">
        </figure>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>




</body>
</html>
