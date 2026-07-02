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
                <span class="widget-label"><?= e(__('sidebar_liturgical_colour')) ?></span>
                <span class="widget-value text-capitalize" id="sidebar-colour"><?= e(Language::get() === 'ta' ? ($sidebarLiturgicalColour === 'green' ? 'பச்சை' : ($sidebarLiturgicalColour === 'violet' ? 'ஊதா' : ($sidebarLiturgicalColour === 'white' ? 'வெள்ளை' : ($sidebarLiturgicalColour === 'red' ? 'சிவப்பு' : ($sidebarLiturgicalColour === 'rose' ? 'ரோஸ்' : $sidebarLiturgicalColour))))) : $sidebarLiturgicalColour) ?></span>
            </div>
        </div>
        <?php if ($sidebarSeason !== ''): ?>
        <p class="sidebar-season mb-0" id="sidebar-season"><?= e($sidebarSeason) ?></p>
        <?php else: ?>
        <p class="sidebar-season mb-0 d-none" id="sidebar-season"></p>
        <?php endif; ?>
    </div>

    <div class="sidebar-widget">
        <h3 class="sidebar-widget-title"><i class="bi bi-calendar3 me-2"></i><?= e(__('sidebar_navigate')) ?></h3>
        <ul class="sidebar-links list-unstyled mb-0">
            <li><a href="<?= url('index.php') ?>"><i class="bi bi-sun"></i> <?= e(__('nav_todays_readings')) ?></a></li>
            <li><a href="<?= url('pages/archive.php?year=' . $year) ?>"><i class="bi bi-calendar-month"></i> <?= e(__('sidebar_mass_readings_year', ['year' => $year])) ?></a></li>
            <li><a href="<?= url('pages/archive.php') ?>"><i class="bi bi-archive"></i> <?= e(__('sidebar_reading_archive')) ?></a></li>
            <li><a href="<?= url('pages/search.php') ?>"><i class="bi bi-search"></i> <?= e(__('sidebar_search_readings')) ?></a></li>
            <li><a href="<?= url('pages/saints.php') ?>"><i class="bi bi-star"></i> <?= e(__('sidebar_saints_calendar')) ?></a></li>
        </ul>
    </div>

    <div class="sidebar-widget">
        <h3 class="sidebar-widget-title"><i class="bi bi-share me-2"></i><?= e(__('sidebar_share_readings')) ?></h3>
        <div class="share-buttons" id="sidebar-share">
            <a href="#" class="share-btn share-facebook" data-share="facebook" title="<?= e(__('share_on_facebook')) ?>" aria-label="<?= e(__('share_on_facebook')) ?>"><i class="bi bi-facebook"></i></a>
            <a href="#" class="share-btn share-whatsapp" data-share="whatsapp" title="<?= e(__('share_on_whatsapp')) ?>" aria-label="<?= e(__('share_on_whatsapp')) ?>"><i class="bi bi-whatsapp"></i></a>
            <a href="#" class="share-btn share-twitter" data-share="twitter" title="<?= e(__('share_on_x')) ?>" aria-label="<?= e(__('share_on_x')) ?>"><i class="bi bi-twitter-x"></i></a>
            <a href="#" class="share-btn share-telegram" data-share="telegram" title="<?= e(__('share_on_telegram')) ?>" aria-label="<?= e(__('share_on_telegram')) ?>"><i class="bi bi-telegram"></i></a>
            <a href="#" class="share-btn share-email" data-share="email" title="<?= e(__('email_page')) ?>" aria-label="<?= e(__('email_page')) ?>"><i class="bi bi-envelope"></i></a>
        </div>
    </div>

    <div class="sidebar-widget">
        <h3 class="sidebar-widget-title"><i class="bi bi-heart me-2"></i><?= e(__('sidebar_stay_connected')) ?></h3>
        <div class="sidebar-social d-flex flex-wrap gap-2">
            <a href="https://facebook.com" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="https://instagram.com" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="https://twitter.com" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="X"><i class="bi bi-twitter-x"></i></a>
            <a href="https://youtube.com" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
        </div>
    </div>

    <div class="sidebar-widget sidebar-promo">
        <h3 class="sidebar-widget-title"><i class="bi bi-book me-2"></i><?= e(__('sidebar_about_readings')) ?></h3>
        <p class="small text-muted mb-2"><?= e(__('sidebar_promo_text')) ?></p>
        <a href="<?= url('pages/about.php') ?>" class="btn btn-outline-primary btn-sm w-100 rounded-pill"><?= e(__('learn_more')) ?></a>
    </div>
</aside>
