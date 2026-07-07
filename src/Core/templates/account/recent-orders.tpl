import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
import(components/account-sidebar-navigation.tpl, [data-v-component-accountnavigation])

#account-db-active-quotes | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $recent_orders = $current_component = $this->_component['accountrecentorders']?? [];

    // echo '<pre>';
    // print_r($recent_orders['orders']);
    // echo '</pre>';

    $recent_orders_initial_limit = 5;

    $recent_orders_list = isset($recent_orders['orders']) ? $recent_orders['orders'] : [];
    if (!is_array($recent_orders_list)) {
        $recent_orders_list = [];
    }
    $is_empty = count($recent_orders_list) === 0;
    $is_not_empty = count($recent_orders_list) > 0;

?>

h2[data-v-recent-orders-title] | innerText = <?php echo $recent_orders['page_title']??""; ?>

ul[data-v-recent-orders-sort-options] > li | deleteAllButFirst
ul[data-v-recent-orders-sort-options] | prepend = <?php if(isset($recent_orders['sort_options'])){ foreach ($recent_orders['sort_options'] as $option) { ?>
a[data-v-recent-orders-sort-option] | innerText = <?php echo isset($option['text']) ? $option['text'] : '';?>
a[data-v-recent-orders-sort-option] | href = <?php echo isset($option['url']) ? $option['url'] : ''; ?>
ul[data-v-recent-orders-sort-options] | append = <?php }} ?>

div[data-v-recent-orders-list] > .row | deleteAllButFirst
button[data-v-recent-orders-sort-button] | innerText = <?php echo isset($recent_orders['sort_button_text']) ? $recent_orders['sort_button_text'] : ''; ?>
label[data-v-recent-orders-view-more] | class = <?php echo 'th-btn text-capitalize bg-black text-white'; ?>
div[data-v-recent-orders-divider] | class = <?php echo 'd-flex justify-content-center my-60' ?>

div[data-v-recent-orders-list] | prepend = <?php foreach ($recent_orders_list as $index => $order) { ?>
div[data-v-recent-order-item] | class = <?php echo 'row th-mar th-quotes-carg-gap mb-0 mr-0 th-recent-order-row'. ($index > 4 ? ' d-none' : ''); ?>
div[data-v-recent-orders-divider] | class = <?php echo 'd-flex justify-content-center my-60'. ($index > 4 ? ' d-none' : ''); ?>
p[data-v-recent-order-number] | innerText = <?php echo '#' . (isset($order['id']) ? $order['id'] : '') . ''; ?>
p[data-v-recent-order-description] | innerText = <?php echo isset($order['description']) ? $order['description'] : ''; ?>
span[data-v-recent-order-account] | innerText = <?php echo isset($order['account']) ? $order['account'] : ''; ?>
span[data-v-recent-order-amount] | innerText = <?php echo isset($order['amount']) ? '$' . $order['amount'] : ''; ?>
span[data-v-recent-order-created-date] | innerText = <?php echo isset($order['created_date']) ? $order['created_date'] : ''; ?>
a[data-v-recent-order-track-link] | href = <?php echo isset($order['track_order_url']) ? $order['track_order_url'] : ''; ?>
a[data-v-recent-order-track-link] | data-bs-target = <?php echo isset($order['track_order_target']) ? $order['track_order_target'] : ''; ?>
a[data-v-recent-order-track-link] | data-order-id = <?php echo isset($order['id']) ? $order['id'] : ''; ?>
a[data-v-recent-order-details-link] | href = <?php echo isset($order['view_details_url']) ? $order['view_details_url'] : ''; ?>
a[data-v-recent-order-details-link] | data-bs-target = <?php echo isset($order['view_details_target']) ? $order['view_details_target'] : ''; ?>
div[data-v-recent-order-offcanvas] | id = <?php echo isset($order['offcanvas_id']) ? $order['offcanvas_id'] : ''; ?>
a[data-v-comment-order] | data-model-id = <?php echo isset($order['id']) ? $order['id'] : ''; ?>
a[data-v-comment-order] | data-model-uuid = <?php echo isset($order['uuid']) ? $order['uuid'] : ''; ?>
a[data-v-comment-order] | data-model-ref = <?php echo isset($order['account']) ? $order['account'] : ''; ?>
div[data-v-recent-orders-list] | append = <?php } ?>
[data-v-recent-orders-view-more] | if_exists = $is_not_empty
div[data-v-recent-orders-not-found] | if_exists = $is_empty

import(components/footer.tpl, [data-v-component-footer])