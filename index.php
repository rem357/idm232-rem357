<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Cookbook</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="styles.css">
</head>

<body>
<?php

require 'connection.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q === '') {

  $sql = "SELECT id, title, category FROM recipes ORDER BY title";
} else {

  $safe = mysqli_real_escape_string($conn, $q);
  $like = "'%" . $safe . "%'";

  $sql = "
    SELECT id, title, category
    FROM recipes
    WHERE title       LIKE $like
       OR description LIKE $like
       OR ingredients LIKE $like
    ORDER BY title
  ";
}

$results = [];
$result = mysqli_query($conn, $sql);

if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $results[] = $row;
  }
} else {
  die('Query error: ' . mysqli_error($conn));
}

$categories = [];
$catResult = mysqli_query($conn, "SELECT DISTINCT category FROM recipes ORDER BY category");

if ($catResult) {
  while ($row = mysqli_fetch_assoc($catResult)) {
    if (!empty($row['category'])) {
      $categories[] = $row['category'];
    }
  }
}

$total = count($results);
?>

<header class="site">
  <div class="brand">
    <div class="brand-badge">🍽</div>
    <div>Cookbook</div>
  </div>
  <nav class="links">
    <a href="index.php">Home</a>
    <a href="search.php">Find Recipes</a>
    <a href="help.php">Help</a>
    <a href="add.php">Add Recipe</a>
  </nav>
</header>

<section class="hero">
  <div class="row" style="justify-content:space-between;">
    <div>
      <h2>Hello There</h2>
      <p>Welcome to the Cookbook. Search, browse, and save what you love.</p>
    </div>
    <form class="search" method="get" action="">
      <input type="text" name="q" placeholder="Search anything (e.g., chicken, rice, lemon…)" value="<?= htmlspecialchars($q) ?>">
      <button type="submit">Search</button>
    </form>
  </div>
</section>

<main>
  <section class="section">
    <div class="section-header">
      <h3><?= $q === '' ? 'Browse all recipes' : 'Search results' ?></h3>
      <div class="pill-row">
        <span class="pill"><strong><?= $total ?></strong> recipes</span>
        <?php if ($q !== ''): ?>
          <span class="pill">Filter: “<?= htmlspecialchars($q) ?>”</span>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($categories)): ?>
      <div class="chips">
        <?php foreach ($categories as $cat): ?>
          <span class="chip"><?= htmlspecialchars($cat) ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <?php if ($total === 0): ?>
    <div class="empty">
      <p>No recipes found matching <strong><?= htmlspecialchars($q) ?></strong>.</p>
      <p style="margin-top:10px;">
        <a class="btn" href="index.php">Reset</a>
        <a class="btn" href="search.php" style="background:var(--accent-2); border-color:var(--accent-2); color:#333;">Open full Search</a>
      </p>
    </div>
  <?php else: ?>
    <div class="section">
      <h3>All recipes</h3>
    </div>

    <div class="grid">
      <?php foreach ($results as $r): ?>
        <article class="card">
          <h4 class="title"><?= htmlspecialchars($r['title']) ?></h4>
          <div style="display:flex; gap:8px; margin-top:4px;">
            <a class="btn" href="recipe.php?id=<?= (int)$r['id'] ?>">View</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<footer>
  Cookbook · IDM 232
</footer>

</body>
</html>
