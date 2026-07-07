import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
import(components/account-sidebar-navigation.tpl, [data-v-component-accountnavigation])


#dashboard-delivery-install | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $dashboard_delivery_install = $current_component = $this->_component['dashboarddeliveryinstall']?? [];
    $is_empty = count($dashboard_delivery_install['items']) == 0;
    $is_not_empty = count($dashboard_delivery_install['items']) > 0;
 ?>

[data-v-component-dashboarddeliveryinstall] [data-v-dashboarddeliveryinstall-*]|innerText = $dashboard_delivery_install['@@__data-v-dashboarddeliveryinstall-(*)__@@']

ul[data-v-dashboarddeliveryinstall-sort-options] > li | deleteAllButFirst
ul[data-v-dashboarddeliveryinstall-sort-options] | prepend = <?php if(isset($dashboard_delivery_install['sort_options'])){ foreach ($dashboard_delivery_install['sort_options'] as $option) { ?>
a[data-v-dashboarddeliveryinstall-sort-option] | innerText = <?php echo isset($option['text']) ? $option['text'] : '';?>
a[data-v-dashboarddeliveryinstall-sort-option] | href = <?php echo isset($option['url']) ? $option['url'] : ''; ?>
ul[data-v-dashboarddeliveryinstall-sort-options] | append = <?php }} ?>

button[data-v-dashboarddeliveryinstall-sort-button] | innerText = <?php echo isset($dashboard_delivery_install['sort_button_text']) ? $dashboard_delivery_install['sort_button_text'] : ''; ?>
[data-v-component-dashboarddeliveryinstall] > div.delivery-install-card > div.delivery-install-card-item | deleteAllButFirst
[data-v-component-dashboarddeliveryinstall] > div.delivery-install-card | prepend = <?php if(isset($dashboard_delivery_install['items'])){ foreach ($dashboard_delivery_install['items'] as $item) { 
    $date = date('d-m-Y', strtotime($item['date']));
    ?>
    p[data-v-dashboarddeliveryinstallitem-title] | innerText = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
    h5[data-v-dashboarddeliveryinstallitem-subtitle] | innerText = <?php echo isset($item["subtitle"]) ? $item["subtitle"] : ''; ?>
    span[data-v-dashboarddeliveryinstallitem-date] | innerText = <?php echo isset($item["date"]) ? $date : ''; ?>
    span[data-v-dashboarddeliveryinstallitem-time] | innerText = <?php echo isset($item["time"]) ? $item["time"] : ''; ?>
    span[data-v-dashboarddeliveryinstallitem-order_number] | innerText = <?php echo isset($item["order_number"]) ? $item["order_number"] : ''; ?>
    a[data-v-dashboarddeliveryinstallitem-link_text] | href = <?php echo isset($item["uuid"]) ? 'orders/'. $item["uuid"] : ''; ?>
    a[data-v-dashboarddeliveryinstallitem-link_text] | innerText = 'See Details'
[data-v-component-dashboarddeliveryinstall] > div.delivery-install-card | append = <?php }} ?>
div[data-v-dashboarddeliveryinstall-not-found] | if_exists = $is_empty
div[data-v-dashboarddeliveryinstall-view-more] | if_exists = $is_not_empty

import(components/footer.tpl, [data-v-component-footer])