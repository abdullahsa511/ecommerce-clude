import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])

#dashboard-pinboard-item | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $pinboard = $current_component = $this->_component['accountpinboard']?? [];
    // echo '<pre>';
    // print_r($pinboard['pinboard_items'][0]);
    // echo '</pre>';

?>

div[data-v-accountpinboard-list] > div[data-v-accountpinboard-item] | deleteAllButFirst
h3[data-v-accountpinboard-name] | innerText = <?php echo $pinboard['pinboard_name']??""; ?>

div[data-v-accountpinboard-list] | prepend = <?php if(isset($pinboard['pinboard_items'])){ foreach ($pinboard['pinboard_items'] as $item) { ?>
    img[data-v-accountpinboarditem-image] | src = <?php echo isset($item['photo']) ? $item['photo'] : ''; ?>
    h4[data-v-accountpinboarditem-type] | innerText = <?php echo $item['model_type']?strtoupper($item['model_type']):""; ?>
    h4[data-v-accountpinboarditem-name] | innerText = <?php echo $item['title']??""; ?>

    div[data-v-itemoptions] > img[data-v-itemoption-image] | deleteAllButFirst
    div[data-v-itemoptions] | prepend = <?php if(isset($item['options'])){ foreach ($item['options'] as $option) { ?>
        img[data-v-itemoption-image] | src = <?php echo $option['src']??""; ?>
    div[data-v-itemoptions] | append = <?php }} ?>

    p[data-v-accountpinboarditem-quote] | innerText = <?php echo $item['description']??""; ?>
    input[data-v-accountpinboarditem-comment-placeholder] | placeholder = <?php echo $item['comment_placeholder']??""; ?>

    button[data-v-accountpinboarditem-white_btn] | innerText = <?php echo $item['white_btn']??""; ?>
    button[data-v-accountpinboarditem-black_btn] | innerText = <?php echo $item['black_btn']??""; ?>

div[data-v-accountpinboard-list] | append = <?php }} ?>

import(components/footer.tpl, [data-v-component-footer])