#footer-section-container | before = <?php

    if(isset($current_component)) $previous_component = $current_component;
	$footer = $current_component = $this->_component['footer']?? [];
    
    // echo '<pre>';
    // print_r($footer);
    // echo '</pre>';

	$nonce = isset($this->parameters['nonce']) && !empty($this->parameters['nonce']) ? $this->parameters['nonce'] : "";
    $errors = isset($this->parameters['errors']) && !empty($this->parameters['errors']) ? $this->parameters['errors'] : [];
    $data = isset($this->parameters['data']) && !empty($this->parameters['data']) ? $this->parameters['data'] : [];

    $email_error = isset($errors['email']) && !empty($errors['email']) ? true : false;
    $nonce_error = isset($errors['nonce']) && !empty($errors['nonce']) ? true : false;

    $email = isset($this->parameters['email']) && !empty($this->parameters['email']) ? $this->parameters['email'] : "";

    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>

#subscription-form-nonce | value = <?php echo isset($nonce) ? $nonce : ""; ?>

#subscription-form | class = <?php echo isset($errors) && count($errors) > 0 ? "th-form  needs-validation was-validated invalid" : "th-form  needs-validation"; ?>

#email-feedback | innerText = <?php echo isset($errors['email']) ? $errors['email'] : ""; ?>
#email-feedback | if_exists = $email_error

#subscription-form | class = <?php echo isset($success) && !empty($success) ? "th-form  needs-validation was-validated valid" : "th-form  needs-validation"; ?>
#success-feedback | innerText = <?php echo isset($success) ? $success : ""; ?>

#subscriptionEmail | value = <?php echo $email; ?>

#subscriptionEmail | class = <?php echo $email_error ? "is-invalid form-control" : "form-control"; ?>

// Contact
p[data-v-footer-contact_email] > a | href = <?php echo isset($footer['contact_email']) ? 'mailto:' . $footer['contact_email'] : ''; ?>
p[data-v-footer-contact_email] > a | innerText = <?php echo isset($footer['contact_email']) ? $footer['contact_email'] : ''; ?>

p[data-v-footer-contact_phone] > a | href = <?php echo 'tel:1800157678'; ?>
p[data-v-footer-contact_phone] > a | innerText = <?php echo isset($footer['contact_phone']) ? $footer['contact_phone'] : ''; ?>

// Sydney office
[data-v-footer-sydney_office_name] | innerText = <?php echo isset($footer['sydney_office_name']) ? $footer['sydney_office_name'] : ''; ?>
[data-v-footer-sydney_office_address] | innerText = <?php echo isset($footer['sydney_office_address']) ? $footer['sydney_office_address'] : ''; ?>
p[data-v-footer-sydney_office_phone] > a | href = <?php echo isset($footer['sydney_office_phone']) ? 'tel:' . preg_replace('/\D/', '', $footer['sydney_office_phone']) : ''; ?>
p[data-v-footer-sydney_office_phone] > a | innerText = <?php echo isset($footer['sydney_office_phone']) ? $footer['sydney_office_phone'] : ''; ?>
[data-v-footer-sydney_office_hours] | innerText = <?php echo isset($footer['sydney_office_hours']) ? $footer['sydney_office_hours'] : ''; ?>

// Melbourne office
[data-v-footer-melbourne_office_name] | innerText = <?php echo isset($footer['melbourne_office_name']) ? $footer['melbourne_office_name'] : ''; ?>
[data-v-footer-melbourne_office_address] | innerText = <?php echo isset($footer['melbourne_office_address']) ? $footer['melbourne_office_address'] : ''; ?>
p[data-v-footer-melbourne_office_phone] > a | href = <?php echo isset($footer['melbourne_office_phone']) ? 'tel:' . preg_replace('/\D/', '', $footer['melbourne_office_phone']) : ''; ?>
p[data-v-footer-melbourne_office_phone] > a | innerText = <?php echo isset($footer['melbourne_office_phone']) ? $footer['melbourne_office_phone'] : ''; ?>
[data-v-footer-melbourne_office_hours] | innerText = <?php echo isset($footer['melbourne_office_hours']) ? $footer['melbourne_office_hours'] : ''; ?>

// Brisbane office (optional CMS fields)
[data-v-footer-brisbane_office_name] | innerText = <?php echo isset($footer['brisbane_office_name']) ? $footer['brisbane_office_name'] : ''; ?>
[data-v-footer-brisbane_office_address] | innerText = <?php echo isset($footer['brisbane_office_address']) ? $footer['brisbane_office_address'] : ''; ?>
p[data-v-footer-brisbane_office_phone] > a | href = <?php echo isset($footer['brisbane_office_phone']) ? 'tel:' . preg_replace('/\D/', '', $footer['brisbane_office_phone']) : ''; ?>
p[data-v-footer-brisbane_office_phone] > a | innerText = <?php echo isset($footer['brisbane_office_phone']) ? $footer['brisbane_office_phone'] : ''; ?>
[data-v-footer-brisbane_office_hours] | innerText = <?php echo isset($footer['brisbane_office_hours']) ? $footer['brisbane_office_hours'] : ''; ?>

// Subscription
[data-v-footer-subscription_title] | innerText = <?php echo isset($footer['subscription_title']) ? $footer['subscription_title'] : ''; ?>
[data-v-footer-subscription_description] | innerText = <?php echo isset($footer['subscription_description']) ? $footer['subscription_description'] : ''; ?>
input[data-v-footer-subscription_placeholder] | placeholder = <?php echo isset($footer['subscription_placeholder']) ? $footer['subscription_placeholder'] : ''; ?>
[data-v-footer-subscription_button_text] | innerText = <?php echo isset($footer['subscription_button_text']) ? $footer['subscription_button_text'] : ''; ?>

// Footer navigation
a[data-v-footer-footer_navigation_about] | innerText = <?php echo isset($footer['footer_navigation_about']) ? $footer['footer_navigation_about'] : ''; ?>
a[data-v-footer-footer_navigation_about] | href = <?php echo isset($footer['footer_navigation_about_url']) ? $footer['footer_navigation_about_url'] : ''; ?>
a[data-v-footer-footer_navigation_catalogue] | innerText = <?php echo isset($footer['footer_navigation_catalogue']) ? $footer['footer_navigation_catalogue'] : ''; ?>
a[data-v-footer-footer_navigation_catalogue] | href = <?php echo isset($footer['footer_navigation_catalogue_url']) ? $footer['footer_navigation_catalogue_url'] : ''; ?>
a[data-v-footer-footer_navigation_contact_us] | innerText = <?php echo isset($footer['footer_navigation_contact_us']) ? $footer['footer_navigation_contact_us'] : ''; ?>
a[data-v-footer-footer_navigation_contact_us] | href = <?php echo isset($footer['footer_navigation_contact_us_url']) ? $footer['footer_navigation_contact_us_url'] : ''; ?>
a[data-v-footer-footer_navigation_faqs] | innerText = <?php echo isset($footer['footer_navigation_faqs']) ? $footer['footer_navigation_faqs'] : ''; ?>
a[data-v-footer-footer_navigation_faqs] | href = <?php echo isset($footer['footer_navigation_faqs_url']) ? $footer['footer_navigation_faqs_url'] : ''; ?>
a[data-v-footer-footer_navigation_resources] | innerText = <?php echo isset($footer['footer_navigation_resources']) ? $footer['footer_navigation_resources'] : ''; ?>
a[data-v-footer-footer_navigation_resources] | href = <?php echo isset($footer['footer_navigation_resources_url']) ? $footer['footer_navigation_resources_url'] : ''; ?>

// Social media
.social-media-list > li | deleteAllButFirst
.social-media-list | prepend = <?php if(isset($footer['social_media'])){ foreach ($footer['social_media'] as $social_media) { ?>
.social-media-item span > a[data-v-url] | href = <?php echo isset($social_media["url"]) ? $social_media["url"] : ''; ?>
.social-media-item span > a[data-v-url] | aria-label = <?php echo isset($social_media["platform"]) ? $social_media["platform"] : ''; ?>
a[data-v-url] > i[data-v-icon] | class = <?php echo isset($social_media["icon"]) ? $social_media["icon"] : ''; ?>
.social-media-list | append = <?php }} ?>

// Copyright
a[data-v-footer-copyright_privacy_url] | href = <?php echo isset($footer['copyright_privacy_url']) ? $footer['copyright_privacy_url'] : ''; ?>
[data-v-footer-copyright_powered_by_url] | innerText = <?php echo isset($footer['copyright_powered_by_text']) ? $footer['copyright_powered_by_text'] : ''; ?>
span[data-v-footer-copyright_year] | innerText = <?php echo isset($footer['copyright_year']) ? $footer['copyright_year'] : ''; ?>

// Admin edit link
a[data-v-footer-edit-link] | href = <?php echo isset($footer['component_link']) ? $footer['component_link'] : ''; ?>
[data-v-footer-edit-link-container] | if_exists = $is_admin
a[data-v-footer-edit-link] | if_exists = $is_admin

#bundle-script | before = <?php $version = env('APP_VERSION', '1.0.0'); ?>
#bundle-script | src = <?php echo "/js/bundle.js?v=" . $version; ?>