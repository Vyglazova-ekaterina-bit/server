<?php include __DIR__ . '/../header.php'; ?>

<?php foreach ($articles as $article): ?>
    <h2><?= htmlspecialchars($article['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h2>
    <p><?= htmlspecialchars($article['text'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
    <hr>
<?php endforeach; ?>

<?php include __DIR__ . '/../footer.php'; ?>
