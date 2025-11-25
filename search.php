<?php
require 'connection.php';

// read filters from the url (search keyword + category)
$q   = isset($_GET['q'])   ? trim($_GET['q'])   : '';
$cat = isset($_GET['cat']) ? trim($_GET['cat']) : 'all';

// get list of categories from the database for the dropdown + chips
$cats = ['all'];
$catResult = mysqli_query(
  $connection,
  "SELECT DISTINCT category FROM recipes WHERE category <> '' ORDER BY category"
);

if ($catResult) {
  while ($row = mysqli_fetch_assoc($catResult)) {
    if (!empty($row['category'])) {
      $cats[] = $row['category'];
    }
  }
}

// build a separate categories array (no "all") for the chips
$categories = [];
foreach ($cats as $c) {
  if ($c !== 'all') {
    $categories[] = $c;
  }
}

// build the sql query with optional search + category filters
$where = [];

if ($q !== '') {
  $safe = mysqli_real_escape_string($connection, $q);
  $like = "'%" . $safe . "%'";
  $where[] = "(title LIKE $like OR description LIKE $like OR ingredients LIKE $like)";
}

if ($cat !== '' && strtolower($cat) !== 'all') {
  $safeCat = mysqli_real_escape_string($connection, $cat);
  $where[] = "category = '$safeCat'";
}

$sql = "SELECT id, title, category FROM recipes";

if ($where) {
  $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY title";

// run the query and collect results into a php array
$results = [];
$result  = mysqli_query($connection, $sql);

if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $results[] = $row;
  }
} else {
  die('query error: ' . mysqli_error($connection));
}

// count how many recipes matched
$total = count($results);

// helper to grab the final hero image (same as on index)
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
  <title>Forkfolio — Find Recipes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link
    href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap"
    rel="stylesheet"
  >
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <!-- top strip + header (same as home) -->
  <div class="top-strip">
    <header class="site">
      <div class="brand">
        <div>Forkfolio</div>
      </div>
      <nav class="links">
        <a href="index.php">Home</a>
        <a href="search.php" class="active">Find Recipes</a>
        <a href="help.php">Help</a>
        <a href="add.php">Add Recipe</a>
      </nav>
    </header>

    <!-- hero with search bar and filters -->
    <section class="hero hero-search">
      <div class="hero-row">
        <div>
          <h2>Find a recipe</h2>
          <p>Search by keyword and narrow by category to explore your Forkfolio.</p>
        </div>

        <form class="search" method="get" action="search.php">
          <input
            type="text"
            name="q"
            placeholder="Search recipes…"
            value="<?= htmlspecialchars($q) ?>"
          >
          <select name="cat">
            <?php
              foreach ($cats as $c) {
                $value = $c;
                $label = ($c === 'all') ? 'All categories' : $c;
                $sel   = (strcasecmp($c, $cat) === 0) ? 'selected' : '';
                echo '<option value="'.htmlspecialchars($value).'" '.$sel.'>'.htmlspecialchars($label).'</option>';
              }
            ?>
          </select>
          <button type="submit">Search</button>
          <a class="btn" href="search.php">Reset</a>
        </form>
      </div>
    </section>
  </div>

  <!-- main content: styled the same way as index.php -->
  <main>
    <!-- summary + filters section (mirrors index.php structure) -->
    <section class="section">
      <div class="section-header">
        <h3>
          <?php if ($q === '' && (strtolower($cat) === 'all')): ?>
            Browse all recipes
          <?php else: ?>
            Search results
          <?php endif; ?>
        </h3>

        <div class="pill-row">
          <span class="pill"><strong><?= $total ?></strong> recipes</span>

          <?php if ($q !== ''): ?>
            <span class="pill">Keyword: “<?= htmlspecialchars($q) ?>”</span>
          <?php endif; ?>

          <?php if (strcasecmp($cat, 'all') !== 0): ?>
            <span class="pill">Category: <?= htmlspecialchars($cat) ?></span>
          <?php endif; ?>
        </div>
      </div>

      <!-- category chips so the user can quickly jump between filters -->
      <?php if (!empty($categories)): ?>
        <div class="chips">
          <?php
            $isAllActive = (strtolower($cat) === 'all');
            $qParam      = $q !== '' ? '&q=' . urlencode($q) : '';
          ?>
          <a
            class="chip"
            href="search.php?cat=all<?= $qParam ?>"
            style="<?= $isAllActive ? 'border-color:var(--accent); color:var(--ink); font-weight:600;' : '' ?>"
          >All</a>

          <?php foreach ($categories as $c): ?>
            <?php
              $active = (strcasecmp($c, $cat) === 0);
              $url    = 'search.php?cat=' . urlencode($c) . $qParam;
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

    <!-- empty state if there are no matches -->
    <?php if ($total === 0): ?>
      <div class="empty">
        <h2>No recipes found</h2>

        <?php if ($q !== '' || strcasecmp($cat,'all')!==0): ?>
          <p>
            Your search
            <?php if ($q !== ''): ?>
              for “<strong><?= htmlspecialchars($q) ?></strong>”
            <?php endif; ?>
            <?php if (strcasecmp($cat,'all')!==0): ?>
              in <strong><?= htmlspecialchars($cat) ?></strong>
            <?php endif; ?>
            returned no results.
          </p>
        <?php else: ?>
          <p>You don’t have any recipes that match this filter yet.</p>
        <?php endif; ?>

        <ol class="empty-tips">
          <li>Try a broader word (e.g., “chicken” instead of “ancho”).</li>
          <li>Clear category filters and search again.</li>
          <li>Search by an ingredient that’s definitely in the recipe.</li>
        </ol>

        <p class="empty-actions">
          <a class="btn" href="search.php">Reset filters</a>
          <a class="btn" href="index.php">Back to Home</a>
        </p>
      </div>

    <?php else: ?>
      <!-- extra section heading like index.php -->
      <div class="section">
        <h3>All recipes</h3>
      </div>

      <!-- results grid: same card structure + classes as index.php -->
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
