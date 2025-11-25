<?php
// connect to the database
require 'connection.php';

// get the recipe id from the url
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// if the id is missing or invalid, show a basic 404
if ($id <= 0) {
  http_response_code(404);
  echo "invalid recipe id. <a href='index.php'>back</a>";
  exit;
}

// query the database for this single recipe
$sql    = "SELECT * FROM recipes WHERE id = $id LIMIT 1";
$result = mysqli_query($connection, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
  http_response_code(404);
  echo "recipe not found. <a href='index.php'>back</a>";
  exit;
}

// turn the row into a php array we can use
$recipe = mysqli_fetch_assoc($result);

// convert long text fields into arrays for ingredients + steps

// split ingredients on newlines or semicolons
$ingredients = preg_split('/[\r\n;]+/', $recipe['ingredients']);
$ingredients = array_filter(array_map('trim', $ingredients));

// split steps on newlines
$steps = array_filter(array_map('trim', explode("\n", $recipe['steps'])));

// set up image paths for images/recipes/{id}/final, /ingredients, /steps
$finalImage        = null;
$ingredientsImages = [];
$stepsImages       = [];

$baseDir    = __DIR__ . '/images/recipes/' . $id;
$baseUrl    = 'images/recipes/' . $id . '/';
$extensions = ['avif'];

// main hero image (final)
$finalDir = $baseDir . '/final';
if (is_dir($finalDir)) {
  foreach ($extensions as $ext) {
    $matches = glob($finalDir . '/*.' . $ext);
    if ($matches && count($matches) > 0) {
      $finalImage = $baseUrl . 'final/' . basename($matches[0]);
      break;
    }
  }
}

// ingredient photos
$ingredientsDir = $baseDir . '/ingredients';
if (is_dir($ingredientsDir)) {
  foreach ($extensions as $ext) {
    foreach (glob($ingredientsDir . '/*.' . $ext) as $path) {
      $ingredientsImages[] = $baseUrl . 'ingredients/' . basename($path);
    }
  }
}

// step photos
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
  <title><?= htmlspecialchars($recipe['title']) ?> — Forkfolio</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap"
    rel="stylesheet"
  >
  <link rel="stylesheet" href="styles.css">
</head>

<body>
  <!-- shared header / nav -->
  <div class="top-strip">
    <header class="site">
      <div class="brand">
        <div>Forkfolio</div>
      </div>
      <nav class="links">
        <a href="index.php" class="active">Home</a>
        <a href="search.php">Find Recipes</a>
        <a href="help.php">Help</a>
        <a href="add.php">Add Recipe</a>
      </nav>
    </header>
  </div>

  <!-- back link to all recipes -->
  <div class="back-row">
    <a href="index.php" class="back-pill">← all recipes</a>
  </div>

    <!-- recipe title -->
  <h1><?= htmlspecialchars($recipe['title']) ?></h1>

  <!-- main hero image -->
  <?php if ($finalImage): ?>
    <div class="recipe-hero">
      <img
        src="<?= htmlspecialchars($finalImage) ?>"
        alt="<?= htmlspecialchars($recipe['title']) ?>"
      >
    </div>
  <?php endif; ?>

  <!-- badges row (category, time, difficulty, servings) -->
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

  <!-- optional description -->
  <?php if (!empty($recipe['description'])): ?>
    <p class="recipe-description">
      <?= nl2br(htmlspecialchars($recipe['description'])) ?>
    </p>
  <?php endif; ?>

  <!-- ingredients section -->
  <div class="section-block ingredients-layout">
    <div class="ingredients-text">
      <h2>ingredients</h2>
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
            <img
              src="<?= htmlspecialchars($src) ?>"
              alt="<?= htmlspecialchars($recipe['title']) ?> – ingredient photo"
            >
          </figure>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- steps section -->
  <section class="section-block steps-block">
    <ol class="steps-list">
      <?php foreach ($steps as $index => $step): ?>
        <?php
          // strip off "1. " etc if it exists
          $cleanStep = preg_replace('/^\d+\.\s*/', '', $step);
        ?>
        <li class="step-item">
          <span class="step-label">step <?= $index + 1 ?></span>
          <p><?= htmlspecialchars($cleanStep) ?></p>
        </li>
      <?php endforeach; ?>
    </ol>

    <?php if (!empty($stepsImages)): ?>
      <div class="steps-gallery">
        <?php foreach ($stepsImages as $src): ?>
          <figure class="step-photo">
            <img
              src="<?= htmlspecialchars($src) ?>"
              alt="<?= htmlspecialchars($recipe['title']) ?> – process photo"
            >
          </figure>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <footer>
    Forkfolio · IDM 232-rem357
  </footer>

</body>
</html>
