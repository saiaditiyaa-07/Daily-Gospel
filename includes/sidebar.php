<?php
/** @var string|null $sidebarLiturgicalColour */
/** @var string|null $sidebarCelebration */
/** @var string|null $sidebarDate */
/** @var string|null $sidebarSeason */

$sidebarLiturgicalColour = $sidebarLiturgicalColour ?? 'green';
$sidebarCelebration = $sidebarCelebration ?? '';
$sidebarDate = $sidebarDate ?? date('Y-m-d');
$sidebarSeason = $sidebarSeason ?? '';
$year = (int) date('Y', strtotime($sidebarDate));
?>
<aside class="readings-sidebar">
    <div class="sidebar-widget liturgical-colour-widget <?= colour_class($sidebarLiturgicalColour) ?>">
        <div class="colour-indicator">
            <span class="colour-dot colour-dot-<?= e(strtolower($sidebarLiturgicalColour)) ?>" aria-hidden="true"></span>
            <div>
                <span class="widget-label">Liturgical Colour</span>
                <span class="widget-value text-capitalize" id="sidebar-colour"><?= e($sidebarLiturgicalColour) ?></span>
            </div>
        </div>
        <?php if ($sidebarSeason !== ''): ?>
        <p class="sidebar-season mb-0" id="sidebar-season"><?= e($sidebarSeason) ?></p>
        <?php else: ?>
        <p class="sidebar-season mb-0 d-none" id="sidebar-season"></p>
        <?php endif; ?>
    </div>

    <div class="sidebar-widget">
        <h3 class="sidebar-widget-title"><i class="bi bi-calendar3 me-2"></i>Navigate</h3>
        <ul class="sidebar-links list-unstyled mb-0">
            <li><a href="<?= url('index.php') ?>"><i class="bi bi-sun"></i> Today's Readings</a></li>
            <li><a href="<?= url('pages/archive.php?year=' . $year) ?>"><i class="bi bi-calendar-month"></i> Mass Readings — <?= $year ?></a></li>
            <li><a href="<?= url('pages/archive.php') ?>"><i class="bi bi-archive"></i> Reading Archive</a></li>
            <li><a href="<?= url('pages/search.php') ?>"><i class="bi bi-search"></i> Search Readings</a></li>
            <li><a href="<?= url('pages/saints.php') ?>"><i class="bi bi-star"></i> Saints Calendar</a></li>
        </ul>
    </div>

    <div class="sidebar-widget">
        <h3 class="sidebar-widget-title"><i class="bi bi-share me-2"></i>Share Readings</h3>
        <div class="share-buttons" id="sidebar-share">
            <a href="#" class="share-btn share-facebook" data-share="facebook" title="Share on Facebook" aria-label="Share on Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="share-btn share-whatsapp" data-share="whatsapp" title="Share on WhatsApp" aria-label="Share on WhatsApp"><i class="bi bi-whatsapp"></i></a>
            <a href="#" class="share-btn share-twitter" data-share="twitter" title="Share on X" aria-label="Share on X"><i class="bi bi-twitter-x"></i></a>
            <a href="#" class="share-btn share-telegram" data-share="telegram" title="Share on Telegram" aria-label="Share on Telegram"><i class="bi bi-telegram"></i></a>
            <a href="#" class="share-btn share-email" data-share="email" title="Email this page" aria-label="Email this page"><i class="bi bi-envelope"></i></a>
        </div>
    </div>

    <div class="sidebar-widget">
        <h3 class="sidebar-widget-title"><i class="bi bi-heart me-2"></i>Stay Connected</h3>
        <div class="sidebar-social d-flex flex-wrap gap-2">
            <a href="https://facebook.com" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="https://instagram.com" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="https://twitter.com" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="X"><i class="bi bi-twitter-x"></i></a>
            <a href="https://youtube.com" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
        </div>
    </div>

    <div class="sidebar-widget sidebar-promo">
        <h3 class="sidebar-widget-title"><i class="bi bi-book me-2"></i>About Readings</h3>
        <p class="small text-muted mb-2">Scripture texts are provided dynamically via Universalis and are never stored in our database.</p>
        <a href="<?= url('pages/about.php') ?>" class="btn btn-outline-primary btn-sm w-100 rounded-pill">Learn More</a>
    </div>
</aside>
