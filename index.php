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
// 1) Connect to the database
require 'connection.php';

// 2) Filters from the URL
$q   = isset($_GET['q'])   ? trim($_GET['q'])   : '';
$cat = isset($_GET['cat']) ? trim($_GET['cat']) : 'all';

// 3) Build the query for recipes
$where = [];

if ($q !== '') {
  $safe = mysqli_real_escape_string($conn, $q);
  $like = "'%" . $safe . "%'";
  $where[] = "(title LIKE $like OR description LIKE $like OR ingredients LIKE $like)";
}

if ($cat !== '' && strtolower($cat) !== 'all') {
  $safeCat = mysqli_real_escape_string($conn, $cat);
  $where[] = "category = '$safeCat'";
}

$sql = "SELECT id, title, category FROM recipes";

if ($where) {
  $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY title";

// 4) Run query and put rows into $results
$results = [];
$result = mysqli_query($conn, $sql);

if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $results[] = $row;
  }
} else {
  die('Query error: ' . mysqli_error($conn));
}

// 5) Get the list of categories for the chips
$categories = [];
$catResult = mysqli_query($conn, "SELECT DISTINCT category FROM recipes WHERE category <> '' ORDER BY category");

if ($catResult) {
  while ($row = mysqli_fetch_assoc($catResult)) {
    if (!empty($row['category'])) {
      $categories[] = $row['category'];
    }
  }
}

// 6) Count of recipes found
$total = count($results);

// helper to get final image for a recipe card
function get_final_image_for_recipe($id) {
  $baseDir = __DIR__ . '/images/recipes/' . $id . '/final';
  $baseUrl = 'images/recipes/' . $id . '/final/';
  $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

  if (!is_dir($baseDir)) {
    return null;
  }

  foreach ($extensions as $ext) {
    $matches = glob($baseDir . '/*.' . $ext);
    if ($matches && count($matches) > 0) {
      return $baseUrl . basename($matches[0]);
    }
  }

  return null;
}
?>

<div class="top-strip">
  <header class="site">
    <div class="brand">
      <div class="brand-badge">🍽</div>
      <div>Cookbook</div>
    </div>
    <nav class="links">
      <a href="index.php" class="active">Home</a>
      <a href="search.php">Find Recipes</a>
      <a href="help.php">Help</a>
      <a href="add.php">Add Recipe</a>
    </nav>
  </header>

  <section class="hero">
    <div class="row" style="justify-content:space-between;">
      <div>
        <h2>Hello there</h2>
        <p>Welcome to your recipe collection. Search, browse, and filter by category.</p>
      </div>
      <form class="search" method="get" action="index.php">
        <input
          type="text"
          name="q"
          placeholder="Search anything (e.g., chicken, rice, lemon…)"
          value="<?= htmlspecialchars($q) ?>"
        >
        <button type="submit">Search</button>
      </form>
    </div>
  </section>
</div>

<main>
  <section class="section">
    <div class="section-header">
      <h3>
        <?php if ($q === '' && (strtolower($cat) === 'all')): ?>
          Browse all recipes
        <?php else: ?>
          Filtered recipes
        <?php endif; ?>
      </h3>
      <div class="pill-row">
        <span class="pill"><strong><?= $total ?></strong> recipes</span>
        <?php if ($q !== ''): ?>
          <span class="pill">Keyword: “<?= htmlspecialchars($q) ?>”</span>
        <?php endif; ?>
        <?php if (strtolower($cat) !== 'all'): ?>
          <span class="pill">Category: <?= htmlspecialchars($cat) ?></span>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($categories)): ?>
      <div class="chips">
        <?php
          $isAllActive = (strtolower($cat) === 'all');
          // preserve q when switching between categories
          $qParam = $q !== '' ? '&q=' . urlencode($q) : '';
        ?>
        <a
          class="chip"
          href="index.php?cat=all<?= $qParam ?>"
          style="<?= $isAllActive ? 'border-color:var(--accent); color:var(--ink); font-weight:600;' : '' ?>"
        >All</a>

        <?php foreach ($categories as $c): ?>
          <?php
            $active = (strcasecmp($c, $cat) === 0);
            $url = 'index.php?cat=' . urlencode($c) . $qParam;
          ?>
          <a
            class="chip"
            href="<?= $url ?>"
            style="<?= $active ? 'border-color:var(--accent); color:var(--ink); font-weight:600;' : '' ?>"
          >
            <?= htmlspecialchars($c) ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <?php if ($total === 0): ?>
    <div class="empty">
      <p>No recipes found.</p>
      <p style="margin-top:10px;">
        <a class="btn" href="index.php">Reset filters</a>
        <a class="btn" href="search.php" style="background:var(--accent-2); border-color:var(--accent-2); color:#333;">Open full Search</a>
      </p>
    </div>
  <?php else: ?>
    <div class="section">
      <h3>All recipes</h3>
    </div>

    <div class="grid">
      <?php foreach ($results as $r): ?>
        <?php $finalImg = get_final_image_for_recipe($r['id']); ?>
        <article class="card">
          <?php if ($finalImg): ?>
            <div class="card-thumb">
              <img src="<?= htmlspecialchars($finalImg) ?>" alt="<?= htmlspecialchars($r['title']) ?>">
            </div>
          <?php endif; ?>

          <h4 class="title"><?= htmlspecialchars($r['title']) ?></h4>
          <p class="meta">
            <?= htmlspecialchars($r['category']) ?>
          </p>
          <div style="display:flex; gap:8px; margin-top:4px;">
            <a class="btn primary" href="recipe.php?id=<?= (int)$r['id'] ?>">View</a>
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
