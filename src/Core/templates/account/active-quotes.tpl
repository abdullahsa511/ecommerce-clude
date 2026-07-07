import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
import(components/account-sidebar-navigation.tpl, [data-v-component-accountnavigation])

#account-db-active-quotes | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $active_quotes = $current_component = $this->_component['accountactivequotes']??[];

    // echo '<pre>';
    // print_r($active_quotes);
    // echo '</pre>';

    // echo '<pre>';
    // print_r($this->parameters);
    // echo '</pre>';



    $active_quotes_initial_limit = 5;
    // Must be true when component is missing, quotes key is absent, or quotes list is empty.
    $has_active_quotes = isset($active_quotes['quotes'])
        && is_array($active_quotes['quotes'])
        && count($active_quotes['quotes']) > 0;
    $no_active_quotes = !$has_active_quotes;
?>


h2[data-v-active-quotes-title] | innerText = <?php echo $active_quotes['page_title']??""; ?>

ul[data-v-active-quotes-sort-options] > li | deleteAllButFirst
ul[data-v-active-quotes-sort-options] | prepend = <?php if(isset($active_quotes['sort_options'])){ foreach ($active_quotes['sort_options'] as $option) { ?>
a[data-v-active-quotes-sort-option] | innerText = <?php echo isset($option['text']) ? $option['text'] : '';?>
a[data-v-active-quotes-sort-option] | href = <?php echo isset($option['url']) ? $option['url'] : ''; ?>
ul[data-v-active-quotes-sort-options] | append = <?php }} ?>

div[data-v-active-quotes-list] > .row | deleteAllButFirst
button[data-v-active-quotes-sort-button] | innerText = <?php echo isset($active_quotes['sort_button_text']) ? $active_quotes['sort_button_text'] : ''; ?>

div[data-v-active-quotes-list] | prepend = <?php if(isset($active_quotes['quotes'])){ foreach ($active_quotes['quotes'] as $index => $quote) { ?>

div[data-v-active-quote-item] | class = <?php echo 'row th-mar th-quotes-carg-gap mb-0 mr-0'. ($index > 4 ? ' d-none' : ''); ?>
div[data-v-active-quotes-divider] | class = <?php echo 'd-flex justify-content-center my-60'. ($index > 4 ? ' d-none' : ''); ?>
    
h2[data-v-active-quote-number] | innerText = <?php echo '#' . (isset($quote['id']) ? $quote['id'] : '') . ''; ?>
h4[data-v-quote-card-title] | innerText = <?php echo isset($quote['job_title']) ? $quote['job_title'] : 'Quote Title'; ?>
p[data-v-active-quote-number-card] | innerText = <?php echo '#' . (isset($quote['id']) ? $quote['id'] : '') . ''; ?>
p[data-v-quote-card-description] | innerText = <?php echo isset($quote['description']) ? $quote['description'] : ''; ?>
span[data-v-active-quote-item-code] | innerText = <?php echo isset($order['item_code']) ? $order['item_code'] : ''; ?>
span[data-v-active-quote-account] | innerText = <?php echo isset($quote['account']) ? $quote['account'] : ''; ?>
span[data-v-active-quote-amount] | innerText = <?php echo isset($quote['amount']) ? "$".$quote['amount'] : ''; ?>
span[data-v-active-quote-created-date] | innerText = <?php echo isset($quote['created_date']) ? $quote['created_date'] : ''; ?>
a[data-v-comment-quote] | data-model-id = <?php echo isset($quote['id']) ? $quote['id'] : ''; ?>
a[data-v-comment-quote] | data-model-uuid = <?php echo isset($quote['uuid']) ? $quote['uuid'] : ''; ?>
a[data-v-comment-quote] | data-model-ref = <?php echo isset($quote['account']) ? $quote['account'] : ''; ?>
a[data-v-active-quote-track-link] | data-bs-target = <?php echo isset($quote['track_order_target']) ? $quote['track_order_target'] : ''; ?>
a[data-v-active-quote-track-link] | data-quote-id = <?php echo isset($quote['uuid']) && $quote['quote_status_id']!=2 ? $quote['uuid'] : ''; ?>
a[data-v-active-quote-track-link] | disabled = <?php echo isset($quote['uuid']) && $quote['quote_status_id']==2 ? 'disabled' : ''; ?>
a[data-v-active-quote-track-link] | class = <?php echo isset($quote['uuid']) && $quote['quote_status_id']==2 ? 'th-btn-disabled text-capitalize ' : 'th-btn-primary text-capitalize accept-quote-btn'; ?>
span[data-v-active-quote-track-text] | innerText = <?php echo isset($quote['status']) ? $quote['status'] : ''; ?>
a[data-v-active-quote-details-link] | href = <?php echo isset($quote['view_details_url']) ? $quote['view_details_url'] : ''; ?>
a[data-v-active-quote-details-link] | data-bs-target = <?php echo isset($quote['view_details_target']) ? $quote['view_details_target'] : ''; ?>
div[data-v-active-quote-offcanvas] | id = <?php echo isset($quote['offcanvas_id']) ? $quote['offcanvas_id'] : ''; ?>
div[data-v-active-quotes-list] | append = <?php }} ?>

div[data-v-no-active-quotes] | if_exists = $no_active_quotes
div[data-v-active-quotes-list-container] | if_exists = $has_active_quotes

import(components/footer.tpl, [data-v-component-footer])