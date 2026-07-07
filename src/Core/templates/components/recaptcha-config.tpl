#krost-recaptcha-config | data-recaptcha-site-key = <?php echo htmlspecialchars((string) env('RECAPTCHA_SITE_KEY', ''), ENT_QUOTES, 'UTF-8'); ?>

#krost-recaptcha-config | data-recaptcha-action-contact = <?php echo htmlspecialchars((string) env('RECAPTCHA_ACTION', 'contact_submit'), ENT_QUOTES, 'UTF-8'); ?>

#krost-recaptcha-config | data-recaptcha-action-service = <?php echo htmlspecialchars((string) env('RECAPTCHA_ACTION_SERVICE', 'service_request'), ENT_QUOTES, 'UTF-8'); ?>

#krost-recaptcha-config | data-recaptcha-action-project = <?php echo htmlspecialchars((string) env('RECAPTCHA_ACTION_PROJECT', 'project_submission'), ENT_QUOTES, 'UTF-8'); ?>

#krost-recaptcha-config | data-recaptcha-action-booking = <?php echo htmlspecialchars((string) env('RECAPTCHA_ACTION_BOOKING', 'showroom_booking'), ENT_QUOTES, 'UTF-8'); ?>
