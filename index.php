<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Cookbook</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#fcfcff;
      --card:#ffffff;
      --ink:#273043;
      --muted:#657289;
      --border:#e8ecf5;
      --accent:#6c8cff;      
      --accent-2:#ffd6e7;   
      --accent-3:#d7f8e4;   
      --chip:#f4f6ff;
    }
    *{box-sizing:border-box}
    body{
      margin:0; padding:20px;
      font-family: 'Nunito', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color:var(--ink); background:linear-gradient(180deg,#fff, var(--bg) 150px);
    }
    a{color:var(--ink); text-decoration:none}
    header.site{
      display:flex; gap:14px; align-items:center;
      margin:0 0 16px 0;
    }
    .brand{
      display:flex; align-items:center; gap:10px;
      font-weight:800; font-size:20px;
    }
    .brand-badge{
      width:36px; height:36px; display:grid; place-items:center;
      border-radius:12px; background:var(--accent);
      color:#fff; font-weight:800;
      box-shadow:0 6px 14px rgba(108,140,255,.25);
    }
    nav.links{margin-left:auto; display:flex; gap:8px; flex-wrap:wrap;}
    nav.links a{
      padding:8px 12px; background:#fff; border:1px solid var(--border); border-radius:10px;
      transition:transform .08s ease, box-shadow .08s ease;
    }
    nav.links a:hover{ transform:translateY(-1px); box-shadow:0 6px 14px rgba(0,0,0,.06);}

    .hero{
      display:grid; gap:12px; padding:16px;
      border:1px solid var(--border); border-radius:16px; background:var(--card);
      box-shadow:0 10px 20px rgba(108,140,255,.06);
    }
    .hero h2{ margin:0; font-size:22px}
    .hero p{ margin:0; color:var(--muted)}
    .hero .row{ display:flex; gap:12px; align-items:center; flex-wrap:wrap;}
    .pill{
      display:inline-flex; align-items:center; gap:6px;
      padding:6px 10px; background:var(--chip);
      border:1px solid var(--border); border-radius:999px; font-size:14px;
    }
    form.search{ display:flex; gap:8px; flex-wrap:wrap; margin-left:auto}
    input[type="text"], button, select{
      padding:10px 12px; border:1px solid var(--border); border-radius:10px; background:#fff;
    }
    button{
      background:var(--accent); color:#fff; border-color:var(--accent); cursor:pointer;
      box-shadow:0 8px 16px rgba(108,140,255,.2);
    }
    button:hover{ filter:brightness(0.97); }

    .stats{ display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px; margin:16px 0;}
    .stat{
      background:var(--card); border:1px solid var(--border); border-radius:14px; padding:14px; text-align:center;
    }
    .stat .num{ font-size:28px; font-weight:800}
    .stat .label{ color:var(--muted); font-size:14px}

    .section{ display:flex; align-items:center; justify-content:space-between; margin:18px 0 10px;}
    .section h3{ margin:0; font-size:18px}
    .chips{ display:flex; flex-wrap:wrap; gap:8px;}
    .chip{
      padding:8px 12px; border:1px solid var(--border); border-radius:999px; background:#fff;
      transition:transform .08s ease, box-shadow .08s ease;
    }
    .chip:hover{ transform:translateY(-1px); box-shadow:0 6px 14px rgba(0,0,0,.06); }

    .grid{
      display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap:12px; margin-top:8px;
    }
    .card{
      background:var(--card); border:1px solid var(--border); border-radius:14px; padding:14px;
      display:flex; flex-direction:column; gap:8px;
      transition:transform .08s ease, box-shadow .08s ease;
    }
    .card:hover{ transform:translateY(-2px); box-shadow:0 12px 18px rgba(0,0,0,.06); }
    .title{ font-weight:700; margin:0; font-size:16px}
    .btn{
      display:inline-block; padding:8px 12px; border-radius:10px; border:1px solid var(--border);
      background:#fff; text-align:center;
    }
    .btn.primary{ background:var(--accent); border-color:var(--accent); color:#fff }
    .empty{ text-align:center; padding:28px; border:1px dashed var(--border); border-radius:14px; background:#fff; }
    footer{ margin:24px 0 8px; color:var(--muted); font-size:13px; text-align:center;}
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

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
function matches($recipe,$q){ if($q==='') return true; $hay=strtolower($recipe['title'].' '.$recipe['description'].' '.implode(' ',$recipe['ingredients'])); return strpos($hay,strtolower($q))!==false; }
$results = array_values(array_filter($recipes, fn($r)=>matches($r,$q)));
$categories = array_values(array_unique(array_map(fn($r)=>$r['category'],$recipes)));
$total = count($recipes);
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
    </div>
    <form class="search" method="get" action="">
      <input type="text" name="q" placeholder="Search anything (e.g., chicken, rice, lemon…)" value="<?= htmlspecialchars($q) ?>">
      <button type="submit">Search</button>
    </form>
  </div>
</section>

<div class="section">
  <h3>Browse by category</h3>
  <div class="chips">
    <?php foreach ($categories as $c): ?>
      <a class="chip" href="search.php?cat=<?= urlencode($c) ?>"> <?= htmlspecialchars($c) ?></a>
    <?php endforeach; ?>
    <a class="chip" href="search.php?cat=all"> All</a>
  </div>
</div>

<p style="margin:12px 0;">
  <a href="add.php" class="btn primary">+ Add a Recipe</a>
</p>

<?php if ($q !== ''): ?>
  <p class="pill" style="margin:10px 0 0 0;">Showing search for <strong><?= htmlspecialchars($q) ?></strong></p>
<?php endif; ?>

<?php if (!$results): ?>
  <div class="empty" style="margin-top:12px;">
    <h3 style="margin:0 0 6px;">No recipes found</h3>
    <p class="muted">Try a broader word (ex:“chicken”), or browse by category above.</p>
    <p style="margin-top:10px;">
      <a class="btn" href="index.php">Reset</a>
      <a class="btn" href="search.php" style="background:var(--accent-2); border-color:var(--accent-2); color:#333;">Open full Search</a>
    </p>
  </div>
<?php else: ?>
  <div class="section">
    <h3>All recipes</h3>
    </div>
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

</body>
</html>
