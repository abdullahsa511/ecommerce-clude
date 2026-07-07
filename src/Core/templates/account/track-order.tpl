import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
import(components/account-sidebar-navigation.tpl, [data-v-component-accountnavigation])


#dashboard-delivery-install | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $dashboard_delivery_install = $current_component = $this->_component['dashboarddeliveryinstall']?? [];

    // echo '<pre>';
	// print_r($dashboard_delivery_install);
	// echo '</pre>';
?>

[data-v-component-dashboarddeliveryinstall] [data-v-dashboarddeliveryinstall-*]|innerText = $dashboard_delivery_install['@@__data-v-dashboarddeliveryinstall-(*)__@@']

[data-v-component-dashboarddeliveryinstall] > div.delivery-install-card > div.delivery-install-card-item | deleteAllButFirst
[data-v-component-dashboarddeliveryinstall] > div.delivery-install-card | prepend = <?php if(isset($dashboard_delivery_install['items'])){ foreach ($dashboard_delivery_install['items'] as $item) { ?>
    p[data-v-dashboarddeliveryinstallitem-title] | innerText = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
    h5[data-v-dashboarddeliveryinstallitem-subtitle] | innerText = <?php echo isset($item["subtitle"]) ? $item["subtitle"] : ''; ?>
    span[data-v-dashboarddeliveryinstallitem-date] | innerText = <?php echo isset($item["date"]) ? $item["date"] : ''; ?>
    span[data-v-dashboarddeliveryinstallitem-time] | innerText = <?php echo isset($item["time"]) ? $item["time"] : ''; ?>
    span[data-v-dashboarddeliveryinstallitem-order_number] | innerText = <?php echo isset($item["order_number"]) ? $item["order_number"] : ''; ?>
    div[data-v-dashboarddeliveryinstallitem-link_text] | innerText = <?php echo isset($item["link_text"]) ? $item["link_text"] : ''; ?>
[data-v-component-dashboarddeliveryinstall] > div.delivery-install-card | append = <?php }} ?>

import(components/footer.tpl, [data-v-component-footer])