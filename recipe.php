<?php
// same pretend “database”
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
    "steps" => [
      "Preheat oven to 450°F; prep produce and make glaze.",
      "Cook rice; sauté kale with garlic; mix into rice.",
      "Sear chicken and glaze until cooked through.",
      "Serve with roasted carrots; finish rice with crème fraîche and raisins."
    ],
    "cook_time_min" => 38,
    "difficulty" => "Advanced",
    "servings" => "2"
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
    "steps" => [
      "Boil potatoes; mash with butter and cream.",
      "Sear beef; rest.",
      "Sauté mushrooms; deglaze to make pan sauce.",
      "Plate beef, spoon sauce, serve with mash."
    ],
    "cook_time_min" => 35,
    "difficulty" => "Moderate",
    "servings" => "2"
  ],
];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$recipe = null;
foreach ($recipes as $r) {
  if ($r['id'] === $id) { $recipe = $r; break; }
}
if (!$recipe) {
  http_response_code(404);
  echo "Recipe not found. <a href='index.php'>Back</a>";
  exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($recipe['title']) ?> — My Cookbook</title>
  <style>
    body { font-family: system-ui, Arial, sans-serif; margin: 20px; max-width: 900px; }
    .badges span { display:inline-block; padding:4px 8px; border:1px solid #ddd; border-radius:999px; margin-right:6px; font-size: 13px; color:#444; }
    ul { line-height: 1.5; }
  </style>
</head>
<body>
  <p><a href="index.php">← Back to recipes</a></p>
  <h1><?= htmlspecialchars($recipe['title']) ?></h1>
  <div class="badges">
    <span><?= htmlspecialchars($recipe['category']) ?></span>
    <span><?= (int)$recipe['cook_time_min'] ?> min</span>
    <span><?= htmlspecialchars($recipe['difficulty']) ?></span>
    <span><?= htmlspecialchars($recipe['servings']) ?> servings</span>
  </div>
  <p><?= htmlspecialchars($recipe['description']) ?></p>

  <h2>Ingredients</h2>
  <ul>
    <?php foreach ($recipe['ingredients'] as $line): ?>
      <li><?= htmlspecialchars($line) ?></li>
    <?php endforeach; ?>
  </ul>

  <h2>Steps</h2>
  <ol>
    <?php foreach ($recipe['steps'] as $step): ?>
      <li><?= htmlspecialchars($step) ?></li>
    <?php endforeach; ?>
  </ol>
</body>
</html>
