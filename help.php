<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Help — Forkfolio</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link
    href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap"
    rel="stylesheet"
  >
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <!-- top strip + header / nav -->
  <div class="top-strip">
    <header class="site">
      <div class="brand">
        <div>Forkfolio</div>
      </div>
      <nav class="links">
        <a href="index.php">Home</a>
        <a href="search.php">Find Recipes</a>
        <a href="help.php" class="active">Help</a>
        <a href="add.php">Add Recipe</a>
      </nav>
    </header>

    <section class="hero">
      <div class="hero-row">
        <div class="hero-copy">
          <h2>Need some help?</h2>
          <p>Here’s how to browse, search, and open recipes in Forkfolio.</p>
        </div>
      </div>
    </section>
  </div>

  <!-- help content -->
  <main>
    <section class="section-block help-card">
      <p style="margin:0 0 14px;">
        <a href="index.php" class="back-pill">← Back to recipes</a>
      </p>

      <h2>How to use this site</h2>
      <p>Use the Home, Search, and Recipe pages together to quickly find what you want to cook.</p>

      <ul>
        <li><strong>browse:</strong> start on <em>home</em> to scroll through all saved recipes by card.</li>
        <li><strong>open a recipe:</strong> click <strong>view</strong> on any card to see the full page with hero image, ingredients, and steps.</li>
        <li><strong>add new recipes:</strong> go to <em>add recipe</em> to save a new dish with its core details.</li>
      </ul>

      <h3 class="help-section-heading">search & filter tips</h3>
      <p>use keywords and categories together to narrow things down.</p>
      <ul>
        <li>use the search bar on <em>home</em> for quick keyword searches (e.g., “chicken”, “broccoli”, “pasta”).</li>
        <li>use the <em>find recipes</em> page for keyword + category filtering at the same time.</li>
        <li>recipes are searchable by <strong>title</strong>, <strong>description</strong>, and <strong>ingredients</strong>.</li>
      </ul>

      <h3 class="help-section-heading">if you’re not finding anything…</h3>
      <ul>
        <li>try a broader keyword (for example, <em>“chicken”</em> instead of a very specific sauce name).</li>
        <li>set category to <strong>all</strong> and search again.</li>
        <li>check spelling for key ingredients you know are in the recipe.</li>
      </ul>

      <p class="help-meta">
        this site is a student project for idm 232 — it’s designed as a simple, personal Forkfolio rather than a public recipe site.
      </p>
    </section>
  </main>

  <footer>
    Forkfolio · IDM 232-rem357
  </footer>

</body>
</html>
