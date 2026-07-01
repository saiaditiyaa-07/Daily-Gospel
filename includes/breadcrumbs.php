<?php
/** @var array<int, array{label: string, url?: string}>|null $breadcrumbs */
$breadcrumbs = $breadcrumbs ?? [];
if ($breadcrumbs === []) {
    return;
}
?>
<nav aria-label="breadcrumb" class="breadcrumb-nav">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
            <?php foreach ($breadcrumbs as $i => $crumb): ?>
                <?php if ($i === count($breadcrumbs) - 1 || empty($crumb['url'])): ?>
                    <li class="breadcrumb-item active" aria-current="page"><?= e($crumb['label']) ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item"><a href="<?= e($crumb['url']) ?>"><?= e($crumb['label']) ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </div>
</nav>
