<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Cookbook — Filter</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: system-ui, Arial, sans-serif; margin: 20px; }
    header { display: flex; gap: 12px; align-items: center; margin-bottom: 16px; }
    input, select, button { padding: 8px 10px; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px; }
    .card { border: 1px solid #ddd; border-radius: 10px; padding: 12px; }
    .meta { color:#555; font-size: 14px; margin: 6px 0; }
    .empty { border: 1px dashed #ddd; border-radius: 10px; padding: 20px; text-align: center; }
    a.btn { display:inline-block; padding:8px 12px; border-radius:6px; background:#0066cc; color:#fff; text-decoration:none; border:1px solid #0066cc; }
    a.btn.secondary { background:#fff; color:#000; border:1px solid #ddd; }
  </style>
</head>
<body>
<?php
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

$q   = isset($_GET['q'])   ? trim($_GET['q'])   : '';
$cat = isset($_GET['cat']) ? trim($_GET['cat']) : 'all';

function matches_filter($recipe, $q, $cat) {
  if ($cat !== 'all' && strcasecmp($recipe['category'], $cat) !== 0) return false;
  if ($q === '') return true;
  $hay = strtolower($recipe['title'] . ' ' . $recipe['description'] . ' ' . implode(' ', $recipe['ingredients']));
  return strpos($hay, strtolower($q)) !== false;
}
$results = array_values(array_filter($recipes, fn($r) => matches_filter($r, $q, $cat)));
?>

<header>
  <h1 style="margin:0;">Find Recipes</h1>
  <a href="index.php" class="btn secondary">← Home</a>
</header>

<form method="get" action="search.php" style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom: 12px;">
  <input type="text" name="q" placeholder="Keyword (e.g., chicken, rice…)" value="<?= htmlspecialchars($q) ?>">
  <select name="cat">
    <?php
      $cats = ['all','Poultry','Beef','Seafood','Vegetarian'];
      foreach ($cats as $c) {
        $sel = (strcasecmp($c, $cat)===0) ? 'selected' : '';
        echo "<option value=\"".htmlspecialchars($c)."\" $sel>".htmlspecialchars(ucfirst($c))."</option>";
      }
    ?>
  </select>
  <button type="submit">Apply</button>
  <a class="btn secondary" href="search.php">Reset</a>
</form>

<?php if ($q !== '' || strcasecmp($cat,'all')!==0): ?>
  <p>Showing <?= $q!=='' ? '“<strong>'.htmlspecialchars($q).'</strong>” ' : 'all ' ?>
     <?= (strcasecmp($cat,'all')!==0) ? 'in <strong>'.htmlspecialchars($cat).'</strong>' : 'categories' ?>
  </p>
<?php endif; ?>

<?php if (!$results): ?>
  <div class="empty">
    <h2 style="margin:0 0 8px;">No recipes found</h2>

    <?php if ($q !== '' || strcasecmp($cat,'all')!==0): ?>
      <p>
        Your search
        <?= $q !== '' ? 'for “<strong>'.htmlspecialchars($q).'</strong>” ' : '' ?>
        <?= (strcasecmp($cat,'all')!==0) ? 'in <strong>'.htmlspecialchars($cat).'</strong> ' : '' ?>
        returned no results.
      </p>
    <?php endif; ?>

    <ol style="text-align:left; max-width:600px; margin: 12px auto 0;">
      <li>Try a broader word (e.g., “chicken” instead of “ancho”).</li>
      <li>Clear category filters and search again.</li>
      <li>Search by an ingredient that’s definitely in the recipe.</li>
    </ol>

    <p style="margin-top:18px;">
      <a class="btn" href="search.php?<?= http_build_query(['q'=>$q,'cat'=>$cat]) ?>">Back to Search</a>
      <a class="btn secondary" href="index.php">Go to Home</a>
    </p>
  </div>
<?php else: ?>
  <div class="grid">
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
  </div>
<?php endif; ?>
</body>
</html>
