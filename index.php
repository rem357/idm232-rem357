<?php
// connect to the database
require 'connection.php';

// read filters from the url (?q= & ?cat=)
$q   = isset($_GET['q'])   ? trim($_GET['q'])   : '';
$cat = isset($_GET['cat']) ? trim($_GET['cat']) : 'all';

$where = [];

// keyword search (title, description, ingredients)
if ($q !== '') {
  $safe = mysqli_real_escape_string($connection, $q);
  $like = "'%" . $safe . "%'";
  $where[] = "(title LIKE $like OR description LIKE $like OR ingredients LIKE $like)";
}

// category filter (skip if it's "all")
if ($cat !== '' && strtolower($cat) !== 'all') {
  $safeCat = mysqli_real_escape_string($connection, $cat);
  $where[] = "category = '$safeCat'";
}

// build the main query for recipes
$sql = "SELECT id, title, category FROM recipes";

if ($where) {
  $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY title";

// run the query and store results in an array
$results = [];
$result  = mysqli_query($connection, $sql);

if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $results[] = $row;
  }
} else {
  // show a basic error if something goes wrong
  die('Query error: ' . mysqli_error($connection));
}

// load distinct categories for the filter chips
$categories = [];
$catResult  = mysqli_query(
  $connection,
  "SELECT DISTINCT category FROM recipes WHERE category <> '' ORDER BY category"
);

if ($catResult) {
  while ($row = mysqli_fetch_assoc($catResult)) {
    if (!empty($row['category'])) {
      $categories[] = $row['category'];
    }
  }
}

// count how many recipes we ended up with
$total = count($results);

// helper to grab the hero image for a recipe (if it exists)
function get_final_image_for_recipe($id) {
  $baseDir    = __DIR__ . '/images/recipes/' . $id . '/final';
  $baseUrl    = 'images/recipes/' . $id . '/final/';
  $extensions = ['avif'];

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
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Forkfolio</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link
    href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap"
    rel="stylesheet"
  >
  <link rel="stylesheet" href="styles.css">
</head>

<body>

  <!-- top strip + main site header -->
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

    <!-- hero area with intro text + quick search -->
    <section class="hero">
      <div class="hero-row">
        <div class="hero-copy">
          <h2>Hello there</h2>
          <p>Welcome!</p>
          <p>Browse for recipes, get inspired, and figure out what’s for dinner tonight.</p>
        </div>

        <form class="search" method="get" action="index.php">
          <input
            type="text"
            name="q"
            placeholder="Search"
            value="<?= htmlspecialchars($q) ?>"
          >
          <button type="submit">Search</button>
        </form>
      </div>
    </section>
  </div>

  <main>
    <!-- summary + filters section -->
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

      <!-- category chips -->
      <?php if (!empty($categories)): ?>
        <div class="chips">
          <?php
            $isAllActive = (strtolower($cat) === 'all');
            // keep the current search term when switching categories
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
              $url    = 'index.php?cat=' . urlencode($c) . $qParam;
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

    <!-- results: either empty state or recipe cards -->
    <?php if ($total === 0): ?>
      <div class="empty">
        <p>No recipes found.</p>
        <p style="margin-top:10px;">
          <a class="btn" href="index.php">Reset filters</a>
          <a
            class="btn"
            href="search.php"
            style="background:var(--accent-2); border-color:var(--accent-2); color:#333;"
          >
            Open full Search
          </a>
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
                <img
                  src="<?= htmlspecialchars($finalImg) ?>"
                  alt="<?= htmlspecialchars($r['title']) ?>"
                >
              </div>
            <?php endif; ?>

            <h4 class="title"><?= htmlspecialchars($r['title']) ?></h4>

            <p class="meta">
              <?= htmlspecialchars($r['category']) ?>
            </p>

            <div class="card-actions">
              <a class="btn primary" href="recipe.php?id=<?= (int)$r['id'] ?>">View</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <footer>
    Forkfolio · IDM 232-rem357
  </footer>

</body>
</html>
