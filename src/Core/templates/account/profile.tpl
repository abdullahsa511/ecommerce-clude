import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
import(components/account-sidebar-navigation.tpl, [data-v-component-accountnavigation])


#account-profile-data-container | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $profile = $current_component = $this->_component['accountprofile'] ?? [];
    // echo '<pre>';
    // print_r($profile);
    // echo '</pre>';


    $nonce = $this->parameters['nonce'] ?? '';
	$recaptcha_site_key = $this->parameters['recaptcha_site_key'] ?? '';
	$recaptcha_action = $this->parameters['recaptcha_action'] ?? '';
    $errors = $this->parameters['errors'] ?? [];
	$data = $this->parameters['data'] ?? [];

    // echo '<pre>';
    // print_r($errors);
    // echo '</pre>';

    // $first_name_error = isset($errors['first_name']) ? true : false;
    // $last_name_error = isset($errors['last_name']) ? true : false;
    // $email_error = isset($errors['email']) ? true : false;
    // $recaptcha_error = isset($errors['recaptcha']) ? true : false;

    $show_verified_badge = !empty($profile['verified-badge']);
    $displayHints = '';
    $displayName = trim($profile['display_name'] ?? '');
    $firstName   = trim($profile['first_name'] ?? '');
    $lastName    = trim($profile['last_name'] ?? '');
    
    if ($displayName !== '') {
        $displayHints = $displayName;
    } elseif ($firstName !== '') {
        // Display name should default to first name.
        $displayHints = $firstName . " " . $lastName;
    } else {
        $displayHints = 'Add your name';
    }
?>

[data-v-component-accountprofile] [data-v-accountprofile-*]|innerText = $profile['@@__data-v-accountprofile-(*)__@@']

[data-v-nonce] | value = <?php echo isset($nonce) ? $nonce : ''; ?>

[data-v-accountprofile-heading-label] | innerText = <?php echo isset($profile['first_name']) && !empty($profile['first_name']) ? 'Profile' : 'Accounts Details'; ?>
span[data-v-accountprofile-email] | innerText = <?php echo isset($profile['email']) ? $profile['email'] : ''; ?>
h3[data-v-accountprofile-display-name-hint] | innerText = <?php echo $displayHints; ?>
input[data-v-accountprofile-first-name] | value = <?php echo htmlspecialchars($profile['first_name'] ?? '', ENT_QUOTES); ?>
input[data-v-accountprofile-last-name] | value = <?php echo htmlspecialchars($profile['last_name'] ?? '', ENT_QUOTES); ?>
input[data-v-accountprofile-display-name] | value = <?php echo htmlspecialchars($profile['display_name'] ?? '', ENT_QUOTES); ?>
input[data-v-accountprofile-phone] | value = <?php echo htmlspecialchars($profile['phone'] ?? '', ENT_QUOTES); ?>
input[data-v-accountprofile-email-input] | value = <?php echo htmlspecialchars($profile['email-input'] ?? $profile['email'] ?? '', ENT_QUOTES); ?>
input[data-v-accountprofile-company] | value = <?php echo htmlspecialchars($profile['company'] ?? '', ENT_QUOTES); ?>
input[data-v-accountprofile-designation] | value = <?php echo htmlspecialchars($profile['designation'] ?? '', ENT_QUOTES); ?>
input[data-v-accountprofile-street] | value = <?php echo htmlspecialchars($profile['street'] ?? '', ENT_QUOTES); ?>
input[data-v-accountprofile-suburb] | value = <?php echo htmlspecialchars($profile['suburb'] ?? '', ENT_QUOTES); ?>
input[data-v-accountprofile-postcode] | value = <?php echo htmlspecialchars($profile['postcode'] ?? '', ENT_QUOTES); ?>
select[data-v-accountprofile-state] | value = <?php echo htmlspecialchars($profile['state'] ?? '', ENT_QUOTES); ?>
input[data-v-accountprofile-state-input] | value = <?php echo htmlspecialchars($profile['state'] ?? '', ENT_QUOTES); ?>
input[data-v-accountprofile-notify_orders] | value = <?php echo htmlspecialchars($profile['notify_orders'] ?? '', ENT_QUOTES); ?>
input[data-v-accountprofile-notify_quotes] | value = <?php echo htmlspecialchars($profile['notify_quotes'] ?? '', ENT_QUOTES); ?>
input[data-v-accountprofile-notify_product_news] | value = <?php echo htmlspecialchars($profile['notify_product_news'] ?? '', ENT_QUOTES); ?>

input[data-v-accountprofile-notify-orders] | checked = <?php echo !empty($profile['notify_orders']) ? 'checked' : ''; ?>
input[data-v-accountprofile-notify-quotes] | checked = <?php echo !empty($profile['notify_quotes']) ? 'checked' : ''; ?>
input[data-v-accountprofile-notify-product-news] | checked = <?php echo !empty($profile['notify_product_news']) ? 'checked' : ''; ?>

span[data-v-accountprofile-verified-badge] | if_exists = $show_verified_badge

import(components/footer.tpl, [data-v-component-footer])