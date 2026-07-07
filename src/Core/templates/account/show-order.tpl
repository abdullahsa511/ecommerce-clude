import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
import(components/account-sidebar-navigation.tpl, [data-v-component-accountnavigation])


#account-show-order | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $show_order = $current_component = $this->_component['accountshoworder']??[];
    // echo '<pre>';
    // print_r($show_order);
    // echo '</pre>';
    // exit;
?>

h2[data-v-show-order-title] | innerText = <?php echo $show_order['page_title']??""; ?>

h4[data-v-show-order-summary-title] | innerText = <?php echo $show_order['show_order_summary']['title']??"";?>
h1[data-v-show-order-summary-id] | innerText = <?php echo $show_order['show_order_summary']['id']??""; ?>
p[data-v-show-order-summary-description] | innerText = <?php echo $show_order['show_order_summary']['description']??""; ?>
span[data-v-show-order-summary-account] | innerText = <?php echo $show_order['show_order_summary']['account']??""; ?>
span[data-v-show-order-summary-amount] | innerText = <?php echo $show_order['show_order_summary']['amount']??""; ?>
span[data-v-show-order-summary-created-date] | innerText = <?php echo $show_order['show_order_summary']['created_date']??""; ?>


div[data-v-table-section-title] | innerText = <?php echo $show_order['table']['section_title']??""; ?>
div[data-v-table-section-total] | innerText = <?php echo $show_order['table']['section_total']??""; ?>

div[data-v-table-items] > .th-row-wrapper | deleteAllButFirst
div[data-v-table-items] | prepend = <?php if(isset($show_order['table']['items'])){ foreach ($show_order['table']['items'] as $item) { ?>
img[data-v-table-item-image] | src = <?php echo $item['image']??""; ?>
img[data-v-table-item-image] | alt = <?php echo $item['alt']??""; ?>
span[data-v-table-item-description] | innerText = <?php echo $item['description']??""; ?>
div[data-v-table-item-quantity] | innerText = <?php echo $item['quantity']??""; ?>
div[data-v-table-item-unit-price] | innerText = <?php echo $item['unit_price']??""; ?>
div[data-v-table-item-total] | innerText = <?php echo $item['total_price']??""; ?>
img[data-v-table-item-comment-icon] | src = <?php echo $item['comment_icon']??""; ?>
div[data-v-table-items] | append = <?php }} ?>

span[data-v-table-footer-subtotal] | innerText = <?php echo $show_order['footer']['sub_total']??""; ?>
span[data-v-table-footer-gst] | innerText = <?php echo $show_order['footer']['gst']??""; ?>
span[data-v-table-footer-total] | innerText = <?php echo $show_order['footer']['total_inc_gst']??""; ?>


h2[data-v-team-managers-title] | innerText = <?php echo $active_quotes['team_managers']['title']??""; ?>

div[data-v-team-managers] > .col-sm-6 | deleteAllButFirst
div[data-v-team-managers] | prepend = <?php if(isset($show_order['team_managers']['members'])){ foreach ($show_order['team_managers']['members'] as $member) { ?>
div[data-v-team-member] | data-bg-src = <?php echo $member['image']??""; ?>
p[data-v-team-member-name] | innerText = <?php echo $member['name']??""; ?>
p[data-v-team-member-position] | innerText = <?php echo $member['position']??""; ?>
i[data-v-team-member-phone-icon] | class = <?php echo $member['phone_icon']??""; ?>
i[data-v-team-member-email-icon] | class = <?php echo $member['email_icon']??""; ?>
div[data-v-team-managers] | append = <?php }} ?>

h6[data-v-order-card-title] | innerText = <?php echo $show_order['order_card']['title']??""; ?>
p[data-v-order-card-id] | innerText = <?php echo $show_order['order_card']['id']??""; ?>
h2[data-v-order-card-order-number] | innerText = <?php echo $show_order['order_card']['id']??""; ?>
p[data-v-order-card-description] | innerText = <?php echo $show_order['order_card']['description']??""; ?>
span[data-v-order-card-account] | innerText = <?php echo $show_order['order_card']['account']??""; ?>
span[data-v-order-card-amount] | innerText = <?php echo $show_order['order_card']['amount']??""; ?>
span[data-v-order-card-created-date] | innerText = <?php echo $show_order['order_card']['created_date']??""; ?>
a[data-v-order-card-add-comment] | href = <?php echo $show_order['order_card']['add_comment_url']??""; ?>
a[data-v-order-card-view-quote] | href = <?php echo $show_order['order_card']['view_quote_url']??""; ?>
a[data-v-order-card-accept-quote] | href = <?php echo $show_order['order_card']['accept_quote_url']??""; ?>
a[data-v-order-card-accept-order-details-link] | href = <?php echo asset($show_order['order_card']['order_id']) ? "/account/recent-orders" : ""; ?>
a[data-v-recent-order-track-link] | data-order-id = <?php echo isset($show_order['show_order_summary']['id']) ? ltrim($show_order['show_order_summary']['id'], '#') : ''; ?>

import(components/footer.tpl, [data-v-component-footer])