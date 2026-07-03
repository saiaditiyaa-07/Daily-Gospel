    </main>

    <footer class="site-footer mt-auto">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-cross text-warning fs-3"></i>
                        <h5 class="mb-0 text-white font-serif"><?= e(__('app_name')) ?></h5>
                    </div>
                    <p class="text-white-50 mb-0"><?= e(__('footer_desc')) ?></p>
                    <?php if (Language::get() === 'ta'): ?>
                        <p class="fst-italic text-warning opacity-75 mt-3 mb-0 small" style="font-family: var(--dg-font-serif); font-size: 0.95rem; line-height: 1.4;">"உம் வார்த்தையே என் காலடிக்கு விளக்கு;<br>என் பாதைக்கு ஒளி."<br><span class="opacity-50">— திருப்பாடல்கள் 119:105</span></p>
                    <?php else: ?>
                        <p class="fst-italic text-warning opacity-75 mt-3 mb-0 small" style="font-family: var(--dg-font-serif); font-size: 0.95rem; line-height: 1.4;">"Your word is a lamp for my feet,<br>a light on my path."<br><span class="opacity-50">— Psalm 119:105</span></p>
                    <?php endif; ?>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white mb-3"><?= e(__('footer_explore')) ?></h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="<?= url('index.php') ?>"><?= e(__('nav_home')) ?></a></li>
                        <li><a href="<?= url('pages/archive.php') ?>"><?= e(__('nav_archive')) ?></a></li>
                        <li><a href="<?= url('pages/search.php') ?>"><?= e(__('nav_search')) ?></a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white mb-3"><?= e(__('footer_about')) ?></h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="<?= url('pages/about.php') ?>"><?= e(__('nav_about')) ?></a></li>
                        <li><a href="<?= url('pages/contact.php') ?>"><?= e(__('nav_contact')) ?></a></li>
                        <li><a href="<?= url('pages/privacy.php') ?>"><?= e(__('nav_privacy') === 'nav_privacy' ? 'Privacy Policy' : __('nav_privacy')) ?></a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white mb-3"><?= e(__('footer_connect')) ?></h6>
                    <div class="social-icons d-flex gap-2 mb-3">
                        <a href="https://facebook.com" class="social-link" aria-label="Facebook" target="_blank" rel="noopener noreferrer"><i class="bi bi-facebook"></i></a>
                        <a href="https://twitter.com" class="social-link" aria-label="Twitter" target="_blank" rel="noopener noreferrer"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://instagram.com" class="social-link" aria-label="Instagram" target="_blank" rel="noopener noreferrer"><i class="bi bi-instagram"></i></a>
                        <a href="https://youtube.com" class="social-link" aria-label="YouTube" target="_blank" rel="noopener noreferrer"><i class="bi bi-youtube"></i></a>
                    </div>
                    <p class="text-white-50 small mb-0">
                        <?php if (Language::get() === 'ta'): ?>
                            வாசகங்கள் © கத்தோலிக்க கேலரி.
                            <a href="https://www.catholicgallery.org" class="text-white-50" target="_blank" rel="noopener">catholicgallery.org</a>
                        <?php else: ?>
                            Readings © Universalis Publishing Ltd.
                            <a href="https://universalis.com" class="text-white-50" target="_blank" rel="noopener">universalis.com</a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <p class="text-white-50 small mb-0">&copy; <?= date('Y') ?> <?= e(__('app_name')) ?>. <?= e(__('footer_all_rights')) ?></p>
                <p class="text-white-50 small mb-0"><?= e(__('footer_built_with')) ?></p>
            </div>
        </div>
    </footer>

    <script>
        window.DailyGospel = {
            baseUrl: <?= json_encode(rtrim(APP_URL, '/')) ?>,
            apiUrl: <?= json_encode(rtrim(APP_URL, '/') . '/api') ?>,
            csrfToken: <?= json_encode(csrf_token()) ?>,
            lang: <?= json_encode(Language::get()) ?>
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= url('assets/js/dark-mode.js') ?>"></script>
    <script src="<?= url('assets/js/lang-switcher.js') ?>"></script>
    <script src="<?= url('assets/js/app.js') ?>"></script>
    <script src="<?= url('assets/js/theme-animations.js') ?>"></script>
    <?php if (!empty($extraScripts) && is_array($extraScripts)): ?>
        <?php foreach ($extraScripts as $script): ?>
            <script src="<?= url($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
