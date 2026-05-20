<?php include __DIR__ . '/../header.php'; ?>

Привет, <?= htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>!!!

<?php include __DIR__ . '/../footer.php'; ?>
