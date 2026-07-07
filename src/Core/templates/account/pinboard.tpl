import(components/head.tpl, [data-v-component-head])

import(components/header.tpl, [data-v-component-header])


#account-pinboardlist-data-container | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $pinboard_list = $current_component = $this->_component['accountpinboardlist']?? [];

    // echo '<pre>';
    // print_r($pinboard_list);
    // echo '</pre>';


    $show_pinboard_success = isset($pinboard_list['message']) && $pinboard_list['message'] !== '';
    $message = isset($pinboard_list['message']) ? $pinboard_list['message'] : '';
    $pinboard_cancel_message = 'Your request has been cancelled.';
    $show_pinboard_cancel_detail = isset($message) && $message === $pinboard_cancel_message;
    $pinboard_success_subtext = $show_pinboard_cancel_detail ? 'You can submit a new request any time.' : '';
?>

h3[data-v-accountpinboardlist-message] | innerText = <?php echo $message ??""; ?>
p[data-v-accountpinboardlist-subtext] | innerText = <?php echo $pinboard_success_subtext; ?>
p[data-v-accountpinboardlist-subtext] | if_exists = $show_pinboard_cancel_detail
div[data-v-accountpinboardlist-cancel-detail] | if_exists = $show_pinboard_cancel_detail
div[data-v-pinboard-row] |  deleteAllButFirst
div[data-v-pinboard-row] | prepend = <?php if(isset($pinboard_list['pinboards'])){ foreach ($pinboard_list['pinboards'] as $list) { ?>
   div[data-v-pinboard-name] | innerText = <?php echo $list['pinboard_name']??""; ?>
   div[data-v-pinboard-customer-name] | innerText = <?php echo $list['customer_name']??""; ?>
   div[data-v-pinboard-customer-email] | innerText = <?php echo $list['customer_email']??""; ?>
   div[data-v-pinboard-item-count] | innerText = <?php echo $list['item_count']??""; ?>
   [data-v-pinboard-status] | innerHTML = <?php echo $list['pinboard_status_id'] == 0 ? 'Not Open In' : ($list['pinboard_status_id'] == 1 ? 'Open In' : 'Opted Out'); ?>
   div[data-v-pinboard-total] | innerText = <?php echo "$".$list['total_price']?? " "; ?>
   div[data-v-pinboard-link] > a | innerText = <?php echo 'View'; ?>
   div[data-v-pinboard-link] > a | href = <?php echo $pinboard_list['pinboard_details_link'].$list['pinboard_id']??""; ?>
div[data-v-pinboard-row] | append = <?php }} ?>
div[data-v-accountpinboardlist-success] | if_exists = $show_pinboard_success


import(components/account-sidebar-navigation.tpl, [data-v-component-accountnavigation])
#dashboard-pinboard-item | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $pinboard = $current_component = $this->_component['accountpinboard']?? [];
    // echo '<pre>';
    // print_r($pinboard);
    // echo '</pre>';

?>

div[data-v-accountpinboard-list] > div[data-v-accountpinboard-item] | deleteAllButFirst
div[data-v-accountpinboard-list] | prepend = <?php if(isset($pinboard['items'])){ foreach ($pinboard['items'] as $item) { ?>
    img[data-v-accountpinboarditem-image] | src = <?php echo $item['image']??""; ?>
    h4[data-v-accountpinboarditem-type] | innerText = <?php echo $item['type']??""; ?>
    h4[data-v-accountpinboarditem-name] | innerText = <?php echo $item['name']??""; ?>

    div[data-v-itemoptions] > img[data-v-itemoption-image] | deleteAllButFirst
    div[data-v-itemoptions] | prepend = <?php if(isset($item['options'])){ foreach ($item['options'] as $option) { ?>
        img[data-v-itemoption-image] | src = <?php echo $option['src']??""; ?>
    div[data-v-itemoptions] | append = <?php }} ?>

    p[data-v-accountpinboarditem-quote] | innerText = <?php echo $item['quote']??""; ?>
    input[data-v-accountpinboarditem-comment-placeholder] | placeholder = <?php echo $item['comment_placeholder']??""; ?>

    button[data-v-accountpinboarditem-white_btn] | innerText = <?php echo $item['white_btn']??""; ?>
    button[data-v-accountpinboarditem-black_btn] | innerText = <?php echo $item['black_btn']??""; ?>

div[data-v-accountpinboard-list] | append = <?php }} ?>



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