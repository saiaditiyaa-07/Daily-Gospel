    </main>

    <footer class="site-footer mt-auto">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="<?= url('assets/images/logo.svg') ?>" alt="" width="32" height="32">
                        <h5 class="mb-0 text-white"><?= e(APP_NAME) ?></h5>
                    </div>
                    <p class="text-white-50 mb-0">Daily Catholic Mass readings with liturgical calendar information. Readings provided dynamically via Universalis.</p>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white mb-3">Explore</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="<?= url('index.php') ?>">Home</a></li>
                        <li><a href="<?= url('pages/archive.php') ?>">Archive</a></li>
                        <li><a href="<?= url('pages/search.php') ?>">Search</a></li>
                        <li><a href="<?= url('pages/saints.php') ?>">Saints</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white mb-3">About</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="<?= url('pages/about.php') ?>">About Us</a></li>
                        <li><a href="<?= url('pages/contact.php') ?>">Contact</a></li>
                        <li><a href="<?= url('pages/privacy.php') ?>">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white mb-3">Connect</h6>
                    <div class="social-icons d-flex gap-2 mb-3">
                        <a href="https://facebook.com" class="social-link" aria-label="Facebook" target="_blank" rel="noopener noreferrer"><i class="bi bi-facebook"></i></a>
                        <a href="https://twitter.com" class="social-link" aria-label="Twitter" target="_blank" rel="noopener noreferrer"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://instagram.com" class="social-link" aria-label="Instagram" target="_blank" rel="noopener noreferrer"><i class="bi bi-instagram"></i></a>
                        <a href="https://youtube.com" class="social-link" aria-label="YouTube" target="_blank" rel="noopener noreferrer"><i class="bi bi-youtube"></i></a>
                    </div>
                    <p class="text-white-50 small mb-0">
                        Readings © Universalis Publishing Ltd.
                        <a href="https://universalis.com" class="text-white-50" target="_blank" rel="noopener">universalis.com</a>
                    </p>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <p class="text-white-50 small mb-0">&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.</p>
                <p class="text-white-50 small mb-0">Built with faith and devotion.</p>
            </div>
        </div>
    </footer>

    <script>
        window.DailyGospel = {
            baseUrl: <?= json_encode(rtrim(APP_URL, '/')) ?>,
            apiUrl: <?= json_encode(rtrim(APP_URL, '/') . '/api') ?>,
            csrfToken: <?= json_encode(csrf_token()) ?>
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= url('assets/js/dark-mode.js') ?>"></script>
    <script src="<?= url('assets/js/app.js') ?>"></script>
    <?php if (!empty($extraScripts) && is_array($extraScripts)): ?>
        <?php foreach ($extraScripts as $script): ?>
            <script src="<?= url($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
