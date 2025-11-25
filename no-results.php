<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>No Results — My Forkfolio</title>
  <style>
    body { font-family: system-ui, Arial, sans-serif; margin: 20px; }
    .empty { border: 1px dashed #ddd; border-radius: 10px; padding: 20px; text-align: center; }
    a.btn { display:inline-block; padding:8px 12px; border-radius:6px; background:#0066cc; color:#fff; text-decoration:none; }
    a.btn.secondary { background:#fff; color:#000; border:1px solid #ddd; }
  </style>
</head>
<body>
<?php
$q   = isset($_GET['q'])   ? trim($_GET['q'])   : '';
$cat = isset($_GET['cat']) ? trim($_GET['cat']) : 'all';
?>
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

</div>

  <footer>
    Forkfolio · IDM 232-rem357
  </footer>

</body>
</html>
