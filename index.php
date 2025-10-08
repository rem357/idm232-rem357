<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Cookbook — PHP Test</title>
</head>
<body>
  <h1>Welcome to your Cookbook</h1>
  <p><?php echo '✅ PHP is working. Version: ' . PHP_VERSION; ?></p>
</body>
</html><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Cookbook</title>
  <style>
    body { font-family: system-ui, Arial, sans-serif; margin: 20px; }
    header { display: flex; gap: 12px; align-items: center; margin-bottom: 16px; }
    input, select, button { padding: 8px 10px; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px; }
    .card { border: 1px solid #ddd; border-radius: 10px; padding: 12px; }
    .meta { color:#555; font-size: 14px; margin: 6px 0; }
  </style>
</head>
<body>
<?php
  // 1) pretend “database”: just a PHP array
  $recipes = [
    [
      "id" => 1,
      "title" => "Ancho-Orange Chicken with Kale Rice & Roasted Carrots",
      "category" => "Poultry",
      "description" => "Smoky ancho glaze + orange. Roasted carrots & creamy kale rice.",
      "ingredients" => [
        "4 Boneless, Skinless Chicken Breasts",
        "1 Tbsp Ancho Chile Paste",
        "2 Tbsps Crème Fraîche",
        "3 Tbsps Golden Raisins",
        "¾ Cup Jasmine Rice",
        "Kale, Carrots, Garlic, Orange, Limes"
      ],
      "cook_time_min" => 38,
      "difficulty" => "Advanced"
    ],
    [
      "id" => 2,
      "title" => "Beef Medallions & Mushroom Sauce with Mashed Potatoes",
      "category" => "Beef",
      "description" => "Pan-seared beef with a rich mushroom pan sauce and fluffy mash.",
      "ingredients" => [
        "Beef Medallions",
        "Cremini Mushrooms",
        "Yukon Gold Potatoes",
        "Butter, Garlic, Stock, Cream"
      ],
      "cook_time_min" => 35,
      "difficulty" => "Moderate"
    ],
  ];

  // 2) read the search term from the URL like ?q=chicken
  $q = isset($_GET['q']) ? trim($_GET['q']) : '';

  // 3) filter recipes by title OR description OR ingredients
  function matches($recipe, $q) {
    if ($q === '') return true; // nothing typed → show all
    $hay = strtolower(
      $recipe['title'] . ' ' .
      $recipe['description'] . ' ' .
      implode(' ', $recipe['ingredients'])
    );
    return strpos($hay, strtolower($q)) !== false;
  }
  $results = array_values(array_filter($recipes, fn($r) => matches($r, $q)));
?>
<header>
  <h1 style="margin:0;">My Cookbook</h1>
  <!-- GET form: puts ?q=... in the URL -->
  <form method="get" action="" style="margin-left:auto; display:flex; gap:8px;">
    <input type="text" name="q" placeholder="Search recipes…" value="<?= htmlspecialchars($q) ?>">
    <button type="submit">Search</button>
  </form>
</header>

<p style="margin: 12px 0;">
  <a href="add.php" style="
    display: inline-block;
    padding: 8px 14px;
    background: #0066cc;
    color: white;
    border-radius: 6px;
    text-decoration: none;
  ">+ Add a Recipe</a>
</p>


<?php if ($q !== ''): ?>
  <p>Showing results for <strong><?= htmlspecialchars($q) ?></strong></p>
<?php endif; ?>

<div class="grid">
  <?php if (!$results): ?>
    <p>No recipes found.</p>
  <?php else: ?>
    <?php foreach ($results as $r): ?>
      <article class="card">
        <h3 style="margin:0 0 6px;"><?= htmlspecialchars($r['title']) ?></h3>
        <p class="meta">
          <?= htmlspecialchars($r['category']) ?> ·
          <?= (int)$r['cook_time_min'] ?> min ·
          <?= htmlspecialchars($r['difficulty']) ?>
        </p>
        <p><?= htmlspecialchars($r['description']) ?></p>
        <p style="margin-top:8px;"><strong>Ingredients:</strong><br>
          <?= htmlspecialchars(implode(", ", $r['ingredients'])) ?>
        </p>
        <p style="margin-top:10px;">
          <a href="recipe.php?id=<?= (int)$r['id'] ?>">View recipe</a>
        </p>
      </article>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
</body>
</html>

