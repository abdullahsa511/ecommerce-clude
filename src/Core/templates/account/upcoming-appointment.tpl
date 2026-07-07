import(components/header.tpl, [data-v-component-header])
#account-navigation | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $account_nav = $current_component = $this->_component['accountnavigation']?? [];
?>

img[data-v-account-profile-avatar] | src = <?php echo $account_nav['profile']['avatar']??""; ?>
h6[data-v-account-profile-name] | innerText = <?php echo $account_nav['profile']['name']??""; ?>
p[data-v-account-profile-desc] | innerText = <?php echo $account_nav['profile']['description']??""; ?>

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
