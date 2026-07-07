import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#hero | before = <?php 
$hero = $current_component = $this->_component['herocatalogue']?? [];
// echo '<pre>';
// print_r($hero);
// echo '</pre>';

$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-component-herocatalogue] [data-v-herocatalogue-*] | innerText = $hero['@@__data-v-herocatalogue-(*)__@@']
section[data-v-herocatalogue-hero_image] | data-bg-src = <?php echo $hero['bg_image']; ?>
[data-v-component-herocatalogue] [data-v-herocatalogue-hero_title] | innerHTML = <?php echo $hero['hero_title']; ?>
div[data-v-contactsales-component-link] > a | href = <?php echo isset($hero['component_link']) ? $hero['component_link'] : ''; ?>
[data-v-component-herocatalogue] [data-v-herocatalogue-hero_subtitle]|innerHTML = <?php echo $hero['hero_subtitle']; ?>

[data-v-herocatalogue-button_group] [data-v-herocatalogue-button_div] | deleteAllButFirst
[data-v-herocatalogue-button_group] | prepend = <?php if(isset($hero['buttons']) && count($hero['buttons']) > 0){ foreach ($hero['buttons'] as $key => $button) { ?>

    a[data-v-herocatalogue-button_link] | class = <?php echo isset($button['anchor_class']) ? $button['anchor_class'] : ''; ?>
    a[data-v-herocatalogue-button_link] | href = <?php echo isset($button['link']) ? $button['link'] : ''; ?>
    span[data-v-herocatalogue-button_label] | innerText = <?php echo isset($button['title']) ? $button['title'] : ''; ?>
[data-v-herocatalogue-button_group] | append = <?php }} ?>

div[data-v-contactsales-component-link] | if_exists = $is_admin

#th-request-catalogue | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $request_catalogue = $current_component = $this->_component['requestcatalogue']?? [];
    $nonce = $this->parameters['nonce'] ?? '';
    $recaptcha_site_key = $this->parameters['recaptcha_site_key'] ?? '';
    $recaptcha_action = $this->parameters['recaptcha_action'] ?? '';
    // echo '<pre>';
	// print_r($request_catalogue);
	// echo '</pre>';
    $errors = $this->parameters['errors'] ?? [];
    $data = $this->parameters['data'] ?? [];

    // echo "<pre>";
    // print_r($data);
    // echo "</pre>";

    // echo "<pre>";
    // print_r($errors);
    // echo "</pre>";

    $catalogue_format_error = isset($errors['catalogue_format']) ? true : false;
    $first_name_error = isset($errors['first_name']) ? true : false;
    $last_name_error = isset($errors['last_name']) ? true : false;
    $email_error = isset($errors['email']) ? true : false;
    $phone_number_error = isset($errors['phone_number']) ? true : false;
    $company_error = isset($errors['company']) ? true : false;
    $mailing_address_error = isset($errors['mailing_address']) ? true : false;
    $recaptcha_error = isset($errors['recaptcha']) ? true : false;
?>
[data-v-nonce] | value = <?php echo $nonce; ?>

[data-v-recaptcha_site_key] | value = <?php echo isset($recaptcha_site_key) ? htmlspecialchars((string) $recaptcha_site_key, ENT_QUOTES, 'UTF-8') : ''; ?>

[data-v-recaptcha_action] | value = <?php echo isset($recaptcha_action) ? htmlspecialchars((string) $recaptcha_action, ENT_QUOTES, 'UTF-8') : ''; ?>

#g-recaptcha-response | value = <?php echo isset($data['g-recaptcha-response']) ? htmlspecialchars((string) $data['g-recaptcha-response'], ENT_QUOTES, 'UTF-8') : ''; ?>

#recaptcha-feedback | innerText = <?php echo $errors['recaptcha']??""; ?>
#recaptcha-feedback | if_exists = $recaptcha_error

#request-catalogue-form | class = <?php echo count($errors) > 0 ? "th-form  needs-validation was-validated invalid" : "th-form  needs-validation"; ?>

#catalogue-format-feedback | innerText = <?php echo $errors['catalogue_format']??""; ?>
#catalogue-format-feedback | if_exists = $catalogue_format_error

#first-name-feedback | innerText = <?php echo $errors['first_name']??""; ?>
#first-name-feedback | if_exists = $first_name_error

#last-name-feedback | innerText = <?php echo $errors['last_name']??""; ?>
#last-name-feedback | if_exists = $last_name_error

<!-- #email-feedback | innerText = <?php echo $errors['email']??""; ?> -->
#email-feedback | if_exists = $email_error

#phone-number-feedback | innerText = <?php echo $errors['phone_number']??""; ?>
#phone-number-feedback | if_exists = $phone_number_error

#company-feedback | innerText = <?php echo $errors['company']??""; ?>
#company-feedback | if_exists = $company_error     

#mailing-address-feedback | innerText = <?php echo $errors['mailing_address']??""; ?>
#mailing-address-feedback | if_exists = $mailing_address_error

#catalogue-format | value = <?php echo $data['catalogue_format']??""; ?>
#first-name | value = <?php echo $data['first_name']??""; ?>
#last-name | value = <?php echo $data['last_name']??""; ?>
#email | value = <?php echo $data['email']??""; ?>
#phone-number | value = <?php echo $data['phone_number']??""; ?>
#company | value = <?php echo $data['company']??""; ?>
#mailing-address | value = <?php echo $data['mailing_address']??""; ?>

#catalogue-format | class = <?php echo $catalogue_format_error ? "is-invalid form-control" : "form-control"; ?>
#first-name | class = <?php echo $first_name_error ? "is-invalid form-control" : "form-control"; ?>
#last-name | class = <?php echo $last_name_error ? "is-invalid form-control" : "form-control"; ?>
#email | class = <?php echo $email_error ? "is-invalid form-control" : "form-control"; ?>
#phone-number | class = <?php echo $phone_number_error ? "is-invalid form-control" : "form-control"; ?>
#company | class = <?php echo $company_error ? "is-invalid form-control" : "form-control"; ?>
#mailing-address | class = <?php echo $mailing_address_error ? "is-invalid form-control" : "form-control"; ?>

[data-v-component-requestcatalogue] [data-v-requestcatalogue-*]|innerText = $request_catalogue['@@__data-v-requestcatalogue-(*)__@@']
[data-v-component-requestcatalogue] [data-v-requestcatalogue-section_title]|innerHTML = <?php echo $request_catalogue['section_title']; ?>
[data-v-component-requestcatalogue] [data-v-requestcatalogue-section_subtitle]|innerHTML = <?php echo $request_catalogue['section_subtitle']; ?>
[data-v-component-requestcatalogue] [data-v-requestcatalogue-image] | src = <?php echo $request_catalogue['image']; ?>

[data-v-component-requestcatalogue] [data-v-requestcatalogue-image] | src = <?php echo $request_catalogue['image']; ?>
label[data-v-requestcatalogue-email] | innerHTML = <?php echo $request_catalogue['email']; ?>
label[data-v-requestcatalogue-company_name] | innerHTML = <?php echo $request_catalogue['company_name']; ?>
label[data-v-requestcatalogue-full_name] | innerHTML = <?php echo $request_catalogue['full_name']; ?>
label[data-v-requestcatalogue-type] | innerHTML = <?php echo $request_catalogue['type']; ?>
label[data-v-requestcatalogue-file] | innerHTML = <?php echo $request_catalogue['file']; ?>
label[data-v-requestcatalogue-text] | innerHTML = <?php echo $request_catalogue['text']; ?>

import(components/needhelp.tpl, [data-v-component-needhelp])
import(components/footer.tpl, [data-v-component-footer])
