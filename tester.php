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
  <title>Cookbook</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
<link
  href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&family=Italianno&family=Jacques+Francois+Shadow&family=Jeju+Myeongjo&display=swap"
  rel="stylesheet"
/>
  <link rel="stylesheet" href="tester.css">
</head>

<body>













  <!-- MAIN HEADER -->
<div class="top-strip">
  <header class="site forkfolio-header">

    <!-- left side: icon + logo block -->
    <div class="forkfolio-left">
      <div class="forkfolio-icon">
        <img src="images/assets/headericon.svg" alt="Fork and plate icon">
      </div>


      <div class="forkfolio-text">
        <div class="forkfolio-welcome">welcome to</div>
        <div class="forkfolio-logo">Forkfolio</div>
        <p class="forkfolio-tagline">Browse for recipes, get inspired, and figure out...</p>
        <p class="forkfolio-tagline">What’s for dinner tonight?</p>
      </div>
    </div>

    <!-- right side: nav buttons -->
    <nav class="links forkfolio-nav">
      <a href="index.php" class="active">Home</a>
      <a href="search.php">Find Recipes</a>
      <a href="help.php">Help</a>
      <a href="add.php">Add Recipe</a>
    </nav>

  </header>

  <!-- keep your hero/search section as-is below if you still want it -->
  <section class="hero">
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
  













