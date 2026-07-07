import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#account-navigation | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $account_nav = $current_component = $this->_component['accountnavigation']?? [];
    // echo '<pre>';
    // print_r($account_nav);
    // echo '</pre>';
    $profile_name = trim((string)($account_nav['profile']['name'] ?? ''));
    $profile_initials = '';

    if ($profile_name !== '') {
        $profile_name_parts = preg_split('/\s+/', $profile_name, -1, PREG_SPLIT_NO_EMPTY);
        if (!empty($profile_name_parts)) {
            $first_initial = function_exists('mb_substr') ? mb_substr($profile_name_parts[0], 0, 1) : substr($profile_name_parts[0], 0, 1);
            $last_part = count($profile_name_parts) > 1 ? $profile_name_parts[count($profile_name_parts) - 1] : '';
            $last_initial = $last_part !== '' ? (function_exists('mb_substr') ? mb_substr($last_part, 0, 1) : substr($last_part, 0, 1)) : '';
            $profile_initials = strtoupper($first_initial . $last_initial);
        }
    }
?>

<!-- img[data-v-account-profile-avatar] | src = <?php echo $account_nav['profile']['avatar']??""; ?>  -->
div[data-v-account-profile-initials] | innerText = <?php echo $profile_initials; ?>
h6[data-v-account-profile-name] | innerText = <?php echo $account_nav['profile']['name']??""; ?>
p[data-v-account-profile-desc] | innerText = <?php echo ""; ?>

ul[data-v-account-sidebar-nav] > li | deleteAllButFirst
ul[data-v-account-sidebar-nav] | prepend = <?php if(isset($account_nav['navigation'])){ foreach ($account_nav['navigation'] as $nav) { ?>
a[data-v-account-sidebar-link] >span | innerText = <?php echo $nav['title']??""; ?>
a[data-v-account-sidebar-link] | href = <?php echo $nav['url']??""; ?>
a[data-v-account-sidebar-link] > i | class = <?php echo $nav['icon']??""; ?>
a[data-v-account-sidebar-link] | class = <?php echo 'nav-link'.(!empty($nav['active']) ? ' active' : ''); ?>
ul[data-v-account-sidebar-nav] | append = <?php }} ?>

div[data-v-account-sidebar-actions] > div | deleteAllButFirst
div[data-v-account-sidebar-actions] | prepend = <?php if(isset($account_nav['actions'])){ foreach ($account_nav['actions'] as $action) { ?>
a[data-v-account-sidebar-action-link] > span | innerText = <?php echo $action['title']??""; ?>
a[data-v-account-sidebar-action-link] | href = <?php echo $action['url']??""; ?>
a[data-v-account-sidebar-action-link] > i | class = <?php echo $action['icon']??""; ?>
div[data-v-account-sidebar-actions] | append = <?php }} ?>

#account-show-quote | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $active_quotes = $current_component = $this->_component['accountshowquote']??[];

    // echo '<pre>';
    // print_r($active_quotes);
    // echo '</pre>';
    // exit;
    // echo '<pre>';
    // print_r($this->parameters);
    // echo '</pre>';
?>

h2[data-v-active-quotes-title] | innerText = <?php echo $active_quotes['page_title']??""; ?>

a[data-v-quote-summary-action] | data-quote-id = <?php echo isset($active_quotes['quote_summary']['uuid']) ? $active_quotes['quote_summary']['uuid'] : ''; ?>
a[data-v-quote-summary-action] | disabled = <?php echo $active_quotes['quote_summary']['quote_status_id']==2 ? 'disabled' : ''; ?>
a[data-v-quote-summary-action] | class = <?php echo $active_quotes['quote_summary']['quote_status_id']==2 ? "th-btn-disabled text-capitalize " : "th-btn-primary text-capitalize quote-track-btn"; ?>
span[data-v-quote-summary-action-text] | innerText = <?php echo $active_quotes['quote_summary']['quote_status_text']??""; ?>

h4[data-v-quote-summary-title] | innerText = <?php echo $active_quotes['quote_summary']['title']??"";?>
h1[data-v-quote-summary-id] | innerText = <?php echo $active_quotes['quote_summary']['id']??""; ?>
p[data-v-quote-summary-description] | innerText = <?php echo $active_quotes['quote_summary']['description']??""; ?>
span[data-v-quote-summary-account] | innerText = <?php echo $active_quotes['quote_summary']['account']??""; ?>
span[data-v-quote-summary-amount] | innerText = <?php echo $active_quotes['quote_summary']['amount']??""; ?>
span[data-v-quote-summary-created-date] | innerText = <?php echo $active_quotes['quote_summary']['created_date']??""; ?>
a[data-v-comment-quote] |  data-model-type = 'Quote'
a[data-v-comment-quote] |  data-model-id = <?php echo isset($active_quotes['quote_summary']['quote_id']) ? $active_quotes['quote_summary']['quote_id'] : ''; ?>
a[data-v-comment-quote] |  data-model-uuid = <?php echo isset($active_quotes['quote_summary']['uuid']) ? $active_quotes['quote_summary']['uuid'] : ''; ?>
a[data-v-comment-quote] |  data-model-ref = <?php echo $active_quotes['quote_summary']['account']??""; ?>

<!-- Creating problem start -->

<!-- End creating problem -->

div[data-v-table-headers] > .th-cell | deleteAllButFirst
div[data-v-table-headers] | prepend = <?php if(isset($active_quotes['table']['headers'])){ foreach ($active_quotes['table']['headers'] as $key =>$header) { ?>
div[data-v-table-header] | innerText = <?php echo $header??""; ?>
div[data-v-table-header] | class = <?php
$class = 'th-cell';
if($header == 'Description') {
    $class .= ' th-col-span-3';
}else if($header == 'Item'){
    $class .= ' th-col-span-1';
} else if(($key+1) == count($active_quotes['table']['headers'])){
    $class .= ' text-end min-width-150';
}
else{
    $class .= ' text-end';
}
echo $class;
?>
div[data-v-table-headers] | append = <?php }} ?>

div[data-v-table-section-title] | innerText = <?php echo $active_quotes['table']['section_title']??""; ?>
div[data-v-table-section-total] | innerText = <?php echo $active_quotes['table']['section_total']??""; ?>

div[data-v-table-items] > .th-row-wrapper | deleteAllButFirst
div[data-v-table-items] | prepend = <?php if(isset($active_quotes['table']['items'])){ foreach ($active_quotes['table']['items'] as $item) { ?>
img[data-v-table-item-image] | src = <?php echo $item['image']??""; ?>
img[data-v-table-item-image] | alt = <?php echo $item['alt']??""; ?>
span[data-v-table-item-description] | innerText = <?php echo $item['description']??""; ?>
div[data-v-table-item-quantity] | innerText = <?php echo $item['quantity']??""; ?>
div[data-v-table-item-unit-price] | innerText = <?php echo $item['unit_price']??""; ?>
div[data-v-table-item-total] | innerText = <?php echo $item['item_total']??""; ?>
img[data-v-table-item-comment-icon] | src = <?php echo $item['comment_icon']??""; ?>
div[data-v-table-items] | append = <?php }} ?>

span[data-v-table-footer-subtotal] | innerText = <?php echo $active_quotes['footer']['sub_total']??""; ?>
span[data-v-table-footer-gst] | innerText = <?php echo $active_quotes['footer']['gst']??""; ?>
span[data-v-table-footer-total] | innerText = <?php echo $active_quotes['footer']['total_inc_gst']??""; ?>

h2[data-v-team-managers-title] | innerText = <?php echo $active_quotes['team_managers']['title']??""; ?>

div[data-v-team-managers] > .col-sm-6 | deleteAllButFirst
div[data-v-team-managers] | prepend = <?php if(isset($active_quotes['team_managers']['members'])){ foreach ($active_quotes['team_managers']['members'] as $member) { ?>
div[data-v-team-member] | data-bg-src = <?php echo $member['image']??""; ?>
p[data-v-team-member-name] | innerText = <?php echo $member['name']??""; ?>
p[data-v-team-member-position] | innerText = <?php echo $member['position']??""; ?>
i[data-v-team-member-phone-icon] | class = <?php echo $member['phone_icon']??""; ?>
i[data-v-team-member-email-icon] | class = <?php echo $member['email_icon']??""; ?>
div[data-v-team-managers] | append = <?php }} ?>

h4[data-v-quote-card-title] | innerText = <?php echo $active_quotes['quote_card']['title']??""; ?>
p[data-v-quote-card-id] | innerText = <?php echo $active_quotes['quote_card']['id']??""; ?>
p[data-v-quote-card-description] | innerText = <?php echo $active_quotes['quote_card']['description']??""; ?>
span[data-v-quote-card-account] | innerText = <?php echo $active_quotes['quote_card']['account']??""; ?>
span[data-v-quote-card-amount] | innerText = <?php echo $active_quotes['quote_card']['amount']??""; ?>
span[data-v-quote-card-created-date] | innerText = <?php echo $active_quotes['quote_card']['created_date']??""; ?>
a[data-v-quote-card-add-comment] | href = <?php echo $active_quotes['quote_card']['add_comment_url']??""; ?>
a[data-v-quote-card-view-quote] | href = <?php echo $active_quotes['quote_card']['view_quote_url']??""; ?>
a[data-v-quote-card-accept-quote] | href = <?php echo $active_quotes['quote_card']['accept_quote_url']??""; ?>
a[data-v-active-quote-track-link] | data-quote-id = <?php echo isset($active_quotes['quote_summary']['quote_id']) ? $active_quotes['quote_summary']['quote_id'] : ''; ?>
a[data-v-active-quote-track-link] | class = <?php echo isset($active_quotes['quote_summary']['quote_status_id']) && $active_quotes['quote_summary']['quote_status_id'] == 2 ? 'th-btn-disabled text-capitalize ': 'th-btn-primary text-capitalize accept-quote-btn'; ?>
span[data-v-active-quote-track-text] | innerText = <?php echo isset($active_quotes['quote_summary']['quote_status_id']) && $active_quotes['quote_summary']['quote_status_id'] == 2 ? 'Accepted': 'Accept Quote'; ?>



.modal-body | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$accountactivequotepayment = $current_component = $this->_component['accountactivequotepayment']?? [];
    
    // echo '<pre>';
	// print_r($accountactivequotepayment);
	// echo '</pre>';
?>

[data-v-component-accountactivequotepayment] [data-v-accountactivequotepayment-*]|innerText = $accountactivequotepayment['@@__data-v-accountactivequotepayment-(*)__@@']
[data-v-component-accountactivequotepayment] [data-v-accountactivequotepayment-powered_by_image] | src = <?php echo $accountactivequotepayment['powered_by_image']??""; ?>
[data-v-component-accountactivequotepayment] [data-v-accountactivequotepayment-contact_information_name] | innerText = <?php echo $accountactivequotepayment['contact_information_name']??""; ?>
[data-v-component-accountactivequotepayment] [data-v-accountactivequotepayment-contact_information_email] | innerText = <?php echo $accountactivequotepayment['contact_information_email']??""; ?>
[data-v-component-accountactivequotepayment] [data-v-accountactivequotepayment-shipping_phone] | innerText = <?php echo $accountactivequotepayment['shipping_phone']??""; ?>
[data-v-component-accountactivequotepayment] [data-v-accountactivequotepayment-shipping_address] | innerText = <?php echo $accountactivequotepayment['shipping_address']??""; ?>
[data-v-component-accountactivequotepayment] [data-v-accountactivequotepayment-shipping_suburb] | innerText = <?php echo $accountactivequotepayment['shipping_suburb']??""; ?>
[data-v-component-accountactivequotepayment] [data-v-accountactivequotepayment-shipping_state] | innerText = <?php echo $accountactivequotepayment['shipping_state']??""; ?>
[data-v-component-accountactivequotepayment] [data-v-accountactivequotepayment-shipping_country] | innerText = <?php echo $accountactivequotepayment['shipping_country']??""; ?>
[data-v-component-accountactivequotepayment] [data-v-accountactivequotepayment-billing_phone] | innerText = <?php echo $accountactivequotepayment['billing_phone']??""; ?>
[data-v-component-accountactivequotepayment] [data-v-accountactivequotepayment-billing_suburb] | innerText = <?php echo $accountactivequotepayment['billing_suburb']??""; ?>
[data-v-component-accountactivequotepayment] [data-v-accountactivequotepayment-billing_state] | innerText = <?php echo $accountactivequotepayment['billing_state']??""; ?>
[data-v-component-accountactivequotepayment] [data-v-accountactivequotepayment-billing_country] | innerText = <?php echo $accountactivequotepayment['billing_country']??""; ?>

import(components/footer.tpl, [data-v-component-footer])