<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

start_app_session();

$pageTitle = __('nav_contact') . ' — ' . APP_NAME;
$pageDescription = __('contact_subtitle');
$currentPage = 'contact';
$breadcrumbs = [
    ['label' => __('bc_contact')],
];
$extraScripts = ['assets/js/contact.js'];

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/breadcrumbs.php';
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title"><?= e(__('contact_title')) ?></h1>
        <p class="page-subtitle"><?= e(__('contact_subtitle')) ?></p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h4 mb-4"><i class="bi bi-envelope-heart text-primary me-2"></i><?= e(__('contact_send_feedback')) ?></h2>
                        <form id="feedback-form">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <div class="mb-3">
                                <label for="feedback-name" class="form-label"><?= e(__('contact_label_name')) ?></label>
                                <input type="text" class="form-control" id="feedback-name" name="name" placeholder="<?= e(__('contact_placeholder_name')) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="feedback-email" class="form-label"><?= e(__('contact_label_email')) ?></label>
                                <input type="email" class="form-control" id="feedback-email" name="email" placeholder="<?= e(__('contact_placeholder_email')) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="feedback-subject" class="form-label"><?= e(__('contact_label_subject')) ?></label>
                                <input type="text" class="form-control" id="feedback-subject" name="subject" value="General Feedback">
                            </div>
                            <div class="mb-3">
                                <label for="feedback-message" class="form-label"><?= e(__('contact_label_message')) ?> <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="feedback-message" name="message" rows="4" required placeholder="<?= e(__('contact_placeholder_message')) ?>"></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label"><?= e(__('contact_label_rating')) ?></label>
                                <div class="rating-stars" id="feedback-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button type="button" class="btn btn-link p-0 me-1 star-btn" data-rating="<?= $i ?>" aria-label="Rate <?= $i ?> stars">
                                        <i class="bi bi-star fs-4"></i>
                                    </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" id="feedback-rating-value" name="rating" value="0">
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4"><?= e(__('contact_btn_send_feedback')) ?></button>
                            <div id="feedback-alert" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h4 mb-4"><i class="bi bi-heart text-primary me-2"></i><?= e(__('contact_prayer_request')) ?></h2>
                        <form id="prayer-form">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <div class="mb-3">
                                <label for="prayer-name" class="form-label"><?= e(__('contact_label_name')) ?></label>
                                <input type="text" class="form-control" id="prayer-name" name="name" placeholder="<?= e(__('contact_placeholder_name')) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="prayer-email" class="form-label"><?= e(__('contact_label_email')) ?></label>
                                <input type="email" class="form-control" id="prayer-email" name="email" placeholder="<?= e(__('contact_placeholder_email')) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="prayer-request" class="form-label"><?= e(__('contact_label_prayer')) ?> <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="prayer-request" name="request" rows="4" required placeholder="<?= e(__('contact_placeholder_prayer')) ?>"></textarea>
                            </div>
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="prayer-anonymous" name="anonymous" value="1">
                                <label class="form-check-label" for="prayer-anonymous"><?= e(__('contact_submit_anonymous')) ?></label>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4"><?= e(__('contact_btn_submit_prayer')) ?></button>
                            <div id="prayer-alert" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
