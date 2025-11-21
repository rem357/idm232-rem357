<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Cookbook — Find Recipes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
require 'connection.php';

// 1) Read filters from the URL
$q   = isset($_GET['q'])   ? trim($_GET['q'])   : '';
$cat = isset($_GET['cat']) ? trim($_GET['cat']) : 'all';

// 2) Get list of categories from the database for the dropdown
$cats = ['all'];
$catResult = mysqli_query($connection, "SELECT DISTINCT category FROM recipes WHERE category <> '' ORDER BY category");
if ($catResult) {
  while ($row = mysqli_fetch_assoc($catResult)) {
    $cats[] = $row['category'];
  }
}

// 3) Build the SQL query with optional filters
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

$sql = "
  SELECT id, title, category, description, ingredients, cook_time, difficulty
  FROM recipes
";

if ($where) {
  $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY title";

// 4) Run the query and collect results
$results = [];
$result = mysqli_query($connection, $sql);

if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $results[] = $row;
  }
} else {
  die('Query error: ' . mysqli_error($connection));
}

$total = count($results);
?>

<!-- Same header/nav as index.php -->
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

<!-- Hero-style search header -->
<section class="hero">
  <div class="row" style="justify-content:space-between;">
    <div>
      <h2>Find a recipe</h2>
      <p>Filter by keyword and category to narrow down your collection.</p>
    </div>
    <form class="search" method="get" action="search.php">
      <input
        type="text"
        name="q"
        placeholder="Search (e.g., chicken, pasta, broccoli…)"
        value="<?= htmlspecialchars($q) ?>"
      >
      <select name="cat">
        <?php
          foreach ($cats as $c) {
            $value = $c;
            $label = ($c === 'all') ? 'All categories' : $c;
            $sel = (strcasecmp($c, $cat) === 0) ? 'selected' : '';
            echo '<option value="'.htmlspecialchars($value).'" '.$sel.'>'.htmlspecialchars($label).'</option>';
          }
        ?>
      </select>
      <button type="submit">Search</button>
      <a class="btn" href="search.php">Reset</a>
    </form>
  </div>
</section>

<main>
  <section class="section">
    <div class="section-header">
      <h3>Search results</h3>
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
  </section>

  <?php if ($total === 0): ?>
    <div class="empty">
      <h2 style="margin:0 0 8px;">No recipes found</h2>

      <?php if ($q !== '' || strcasecmp($cat,'all')!==0): ?>
        <p>
          Your search
          <?= $q !== '' ? 'for “<strong>'.htmlspecialchars($q).'</strong>” ' : '' ?>
          <?= (strcasecmp($cat,'all')!==0) ? 'in <strong>'.htmlspecialchars($cat).'</strong> ' : '' ?>
          returned no results.
        </p>
      <?php else: ?>
        <p>You don’t have any recipes that match this filter yet.</p>
      <?php endif; ?>

      <ol style="text-align:left; max-width:600px; margin: 12px auto 0;">
        <li>Try a broader word (e.g., “chicken” instead of “ancho”).</li>
        <li>Clear category filters and search again.</li>
        <li>Search by an ingredient that’s definitely in the recipe.</li>
      </ol>

      <p style="margin-top:18px;">
        <a class="btn" href="search.php">Reset filters</a>
        <a class="btn" href="index.php" style="background:var(--accent-2); border-color:var(--accent-2); color:#333;">Back to Home</a>
      </p>
    </div>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($results as $r): ?>
        <article class="card">
          <h4 class="title"><?= htmlspecialchars($r['title']) ?></h4>

          <p class="meta">
            <?= htmlspecialchars($r['category']) ?>
            <?php if (!empty($r['cook_time'])): ?>
              · <?= htmlspecialchars($r['cook_time']) ?>
            <?php endif; ?>
            <?php if (!empty($r['difficulty'])): ?>
              · <?= htmlspecialchars($r['difficulty']) ?>
            <?php endif; ?>
          </p>

          <?php if (!empty($r['description'])): ?>
            <p><?= htmlspecialchars($r['description']) ?></p>
          <?php endif; ?>

          <?php if (!empty($r['ingredients'])): ?>
            <p style="margin-top:6px; font-size:13px;">
              <strong>Ingredients preview:</strong><br>
              <?= nl2br(htmlspecialchars($r['ingredients'])) ?>
            </p>
          <?php endif; ?>

          <div style="margin-top:8px;">
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
