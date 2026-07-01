<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

start_app_session();

$pageTitle = 'Contact — ' . APP_NAME;
$pageDescription = 'Contact Daily Gospel or submit a prayer request.';
$currentPage = 'contact';
$breadcrumbs = [
    ['label' => 'Contact'],
];
$extraScripts = ['assets/js/contact.js'];

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/breadcrumbs.php';
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title">Contact Us</h1>
        <p class="page-subtitle">We'd love to hear from you</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h4 mb-4"><i class="bi bi-envelope-heart text-primary me-2"></i>Send Feedback</h2>
                        <form id="feedback-form">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <div class="mb-3">
                                <label for="feedback-name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="feedback-name" name="name" placeholder="Your name">
                            </div>
                            <div class="mb-3">
                                <label for="feedback-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="feedback-email" name="email" placeholder="you@example.com">
                            </div>
                            <div class="mb-3">
                                <label for="feedback-subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="feedback-subject" name="subject" value="General Feedback">
                            </div>
                            <div class="mb-3">
                                <label for="feedback-message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="feedback-message" name="message" rows="4" required placeholder="Your message..."></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Rating</label>
                                <div class="rating-stars" id="feedback-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button type="button" class="btn btn-link p-0 me-1 star-btn" data-rating="<?= $i ?>" aria-label="Rate <?= $i ?> stars">
                                        <i class="bi bi-star fs-4"></i>
                                    </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" id="feedback-rating-value" name="rating" value="0">
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Send Feedback</button>
                            <div id="feedback-alert" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h4 mb-4"><i class="bi bi-heart text-primary me-2"></i>Prayer Request</h2>
                        <form id="prayer-form">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <div class="mb-3">
                                <label for="prayer-name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="prayer-name" name="name" placeholder="Your name">
                            </div>
                            <div class="mb-3">
                                <label for="prayer-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="prayer-email" name="email" placeholder="you@example.com">
                            </div>
                            <div class="mb-3">
                                <label for="prayer-request" class="form-label">Prayer Request <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="prayer-request" name="request" rows="4" required placeholder="Share your prayer intention..."></textarea>
                            </div>
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="prayer-anonymous" name="anonymous" value="1">
                                <label class="form-check-label" for="prayer-anonymous">Submit anonymously</label>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Submit Prayer Request</button>
                            <div id="prayer-alert" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
