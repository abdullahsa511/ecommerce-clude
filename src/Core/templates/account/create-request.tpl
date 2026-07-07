import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
import(components/account-sidebar-navigation.tpl, [data-v-component-accountnavigation])

#account-db-create-request | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $create_request = $current_component = $this->_component['accountcreaterequest']?? [];
    $recaptcha_site_key = (string) ($this->parameters['recaptcha_site_key'] ?? '');
    $recaptcha_action = (string) ($this->parameters['recaptcha_action'] ?? '');
?>

[data-v-recaptcha_site_key] | value = <?php echo htmlspecialchars($recaptcha_site_key, ENT_QUOTES, 'UTF-8'); ?>

[data-v-recaptcha_action] | value = <?php echo htmlspecialchars($recaptcha_action, ENT_QUOTES, 'UTF-8'); ?>

#createRequestForm | data-recaptcha-site-key = <?php echo htmlspecialchars($recaptcha_site_key, ENT_QUOTES, 'UTF-8'); ?>

#createRequestForm | data-recaptcha-action = <?php echo htmlspecialchars($recaptcha_action, ENT_QUOTES, 'UTF-8'); ?>

#g-recaptcha-response-create-request | value = <?php echo isset($this->parameters['g-recaptcha-response']) ? htmlspecialchars((string) $this->parameters['g-recaptcha-response'], ENT_QUOTES, 'UTF-8') : ''; ?>

[data-v-component-accountcreaterequest] [data-v-accountcreaterequest-*]|innerText = $create_request['@@__data-v-accountcreaterequest-(*)__@@']




#virtual-pinboard-item | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $virtualpinboard = $current_component = $this->_component['virtualpinboard']?? [];

    // echo '<pre>';
	// print_r($virtualpinboard);
	// echo '</pre>';
?>

div[data-v-virtualpinboard-list] > div[data-v-virtualpinboard-item] | deleteAllButFirst
div[data-v-virtualpinboard-list] | prepend = <?php if(isset($virtualpinboard['items'])){ foreach ($virtualpinboard['items'] as $item) { ?>
    img[data-v-virtualpinboarditem-image] | src = <?php echo $item['image']??""; ?>
    h3[data-v-virtualpinboarditem-type] | innerText = <?php echo $item['type']??""; ?>
    p[data-v-virtualpinboarditem-name] | innerText = <?php echo $item['name']??""; ?>
    span[data-v-virtualpinboarditem-description] | innerText = <?php echo $item['description']??""; ?>

    div[data-v-virtualitemoptions] > img[data-v-virtualitemoption-image] | deleteAllButFirst
    div[data-v-virtualitemoptions] | prepend = <?php if(isset($item['options'])){ foreach ($item['options'] as $option) { ?>
        img[data-v-virtualitemoption-image] | src = <?php echo $option['src']??""; ?>
    div[data-v-virtualitemoptions] | append = <?php }} ?>

    
    input[data-v-virtualpinboarditem-comment_placeholder] | placeholder = <?php echo $item['comment_placeholder']??""; ?>

div[data-v-virtualpinboard-list] | append = <?php }} ?>

import(components/footer.tpl, [data-v-component-footer])