<?php
// Basic form state
$errors      = [];
$successData = null;

$title       = '';
$category    = '';
$description = '';
$cook_time   = '';
$servings    = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Pull data from POST
  $title       = trim($_POST["title"] ?? '');
  $category    = trim($_POST["category"] ?? '');
  $description = trim($_POST["description"] ?? '');
  $cook_time   = trim($_POST["cook_time"] ?? '');
  $servings    = trim($_POST["servings"] ?? '');

  // Basic validation
  if ($title === "") {
    $errors[] = "Title is required.";
  }
  if ($category === "") {
    $errors[] = "Category is required.";
  }
  if ($cook_time === "" || (int)$cook_time <= 0) {
    $errors[] = "Cook time must be greater than 0.";
  }

  // If no errors, store success info (this is just UI, not DB insert)
  if (empty($errors)) {
    $successData = [
      'title'       => $title,
      'category'    => $category,
      'description' => $description,
      'cook_time'   => (int)$cook_time,
      'servings'    => ($servings !== '' ? (int)$servings : null),
    ];
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add a Recipe — Forkfolio</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">

  <style>
    /* Add-page specific layout */

    .form-card {
      max-width: 650px;
    }

    .form-title {
      margin: 0 0 4px;
      font-size: 22px;
      font-weight: 800;
    }

    .form-intro {
      margin: 0 0 16px;
      font-size: 14px;
      color: var(--muted);
    }

    .form-card label {
      display: block;
      font-weight: 700;
      font-size: 14px;
      margin-top: 10px;
      margin-bottom: 4px;
    }

    .form-card input,
    .form-card textarea,
    .form-card select {
      width: 100%;
      padding: 8px 10px;
      border-radius: 10px;
      border: 1px solid var(--border);
      font-size: 14px;
      font-family: inherit;
      margin-bottom: 10px;
      background: #fff;
    }

    .form-card textarea {
      resize: vertical;
      min-height: 80px;
    }

    .form-actions {
      margin-top: 12px;
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .flash-success,
    .flash-errors {
      margin-bottom: 16px;
      padding: 10px 12px;
      border-radius: 10px;
      font-size: 14px;
    }

    .flash-success {
      background: var(--accent-3);
      border: 1px solid #6ac48f;
      color: #174527;
    }

    .flash-errors {
      background: #ffe7ea;
      border: 1px solid #f16a7b;
      color: #7a1f29;
    }

    .flash-errors ul {
      margin: 6px 0 0;
      padding-left: 20px;
    }

    .flash-success p {
      margin: 4px 0 0;
    }

    @media (max-width: 768px) {
      .form-card {
        max-width: 100%;
      }
    }
  </style>
</head>
<body>

<!-- Shared header / nav -->
<div class="top-strip">
  <header class="site">
    <div class="brand">
      <div>Forkfolio</div>
    </div>
    <nav class="links">
      <a href="index.php">Home</a>
      <a href="search.php">Find Recipes</a>
      <a href="help.php">Help</a>
      <a href="add.php" class="active">Add Recipe</a>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-row">
      <div class="hero-copy">
        <h2>Add a new recipe</h2>
        <p>[still working on this]</p>
      </div>
    </div>
  </section>
</div>

<main>
  <section class="section-block form-card">
    <p style="margin:0 0 12px;">
      <a href="index.php" class="back-pill">← Back to recipes</a>
    </p>

    <?php if (!empty($successData)): ?>
      <div class="flash-success">
        <strong>Recipe added!</strong>
        <p>
          <strong><?= htmlspecialchars($successData['title']) ?></strong>
          (<?= htmlspecialchars($successData['category']) ?>)
          — <?= htmlspecialchars((string)$successData['cook_time']) ?> minutes
          <?php if (!is_null($successData['servings'])): ?>
            · serves <?= htmlspecialchars((string)$successData['servings']) ?>
          <?php endif; ?>
        </p>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="flash-errors">
        <strong>Please fix the following:</strong>
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <h1 class="form-title">Add a New Recipe</h1>
    <p class="form-intro">Fill out the basics below. You can always refine instructions and photos later.</p>

    <form method="post" action="">
      <label for="title">Recipe title</label>
      <input
        type="text"
        id="title"
        name="title"
        placeholder="e.g. Lemon Pasta"
        value="<?= htmlspecialchars($title) ?>"
        required
      >

      <label for="category">Category</label>
      <select id="category" name="category" required>
        <option value="">-- Choose one --</option>
        <option value="Poultry"     <?= $category === 'Poultry'     ? 'selected' : '' ?>>Poultry</option>
        <option value="Beef"        <?= $category === 'Beef'        ? 'selected' : '' ?>>Beef</option>
        <option value="Seafood"     <?= $category === 'Seafood'     ? 'selected' : '' ?>>Seafood</option>
        <option value="Vegetarian"  <?= $category === 'Vegetarian'  ? 'selected' : '' ?>>Vegetarian</option>
        <option value="Pasta"       <?= $category === 'Pasta'       ? 'selected' : '' ?>>Pasta</option>
        <option value="Pork"        <?= $category === 'Pork'        ? 'selected' : '' ?>>Pork</option>
      </select>

      <label for="description">Short description</label>
      <textarea
        id="description"
        name="description"
        rows="3"
        placeholder="Quick summary (flavors, main ingredients, vibe)…"
      ><?= htmlspecialchars($description) ?></textarea>

      <label for="cook_time">Cook time (minutes)</label>
      <input
        type="number"
        id="cook_time"
        name="cook_time"
        min="1"
        step="1"
        value="<?= htmlspecialchars($cook_time) ?>"
      >

      <label for="servings">Servings</label>
      <input
        type="number"
        id="servings"
        name="servings"
        min="1"
        step="1"
        value="<?= htmlspecialchars($servings) ?>"
      >

      <div class="form-actions">
        <button type="submit" class="btn primary">Save recipe</button>
        <a href="index.php" class="btn">Cancel</a>
      </div>
    </form>
  </section>
</main>

  <footer>
    Forkfolio · IDM 232-rem357
  </footer>

</body>
</html>
