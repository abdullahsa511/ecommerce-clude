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

<!-- img[data-v-account-profile-avatar] | src = <?php echo $account_nav['profile']['avatar']??""; ?> -->
div[data-v-account-profile-initials] | innerText = <?php echo $profile_initials; ?>
h6[data-v-account-profile-name] | innerText = <?php echo $account_nav['profile']['name']??""; ?>
h6[data-v-account-profile-email] | innerText = <?php echo $account_nav['profile']['email']??""; ?>
<!-- p[data-v-account-profile-desc] | innerText = <?php echo $account_nav['profile']['description']??""; ?> -->
p[data-v-account-profile-desc] | innerText = <?php echo $account_nav['profile']['email']??""; ?>

ul[data-v-account-sidebar-nav] > li | deleteAllButFirst
ul[data-v-account-sidebar-nav] | prepend = <?php if(isset($account_nav['navigation'])){ foreach ($account_nav['navigation'] as $nav) { 
    if ($nav['title'] == 'Recent Orders') {
        $text_indent = 'text-indent';
        $text_indent_icon = 'text-indent-icon';
    } else {
        $text_indent = '';
        $text_indent_icon = '';
    }
    
    ?>
a[data-v-account-sidebar-link] >span | innerText = <?php echo $nav['title']??""; ?>
a[data-v-account-sidebar-link] >span | class = <?php echo $text_indent; ?>

a[data-v-account-sidebar-link] | href = <?php echo $nav['url']??""; ?>
a[data-v-account-sidebar-link] > i | class = <?php echo $nav['icon']??""; ?>

a[data-v-account-sidebar-link] > i | class = <?php echo $text_indent_icon.' '.$nav['icon']; ?>
<!-- a[data-v-account-sidebar-link] | class = <?php echo 'nav-link'.(!empty($nav['active']) ? ' active' : ''); ?> -->

a[data-v-account-sidebar-link] | class = <?php 
    $currentPath = isset($_SERVER['REQUEST_URI']) 
        ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) 
        : '';

    $navPath = rtrim($nav['url'], '/');

    $isRequestURL = ($currentPath === $navPath || str_starts_with($currentPath, $navPath . '/'));

    if (!empty($nav['url']) && $isRequestURL) {
        echo 'nav-link active';
    } else {
        echo 'nav-link';
    }
?>


ul[data-v-account-sidebar-nav] | append = <?php }} ?>

div[data-v-account-sidebar-actions] > div | deleteAllButFirst
div[data-v-account-sidebar-actions] | prepend = <?php if(isset($account_nav['actions'])){ foreach ($account_nav['actions'] as $action) { ?>
a[data-v-account-sidebar-action-link] > span | innerText = <?php echo $action['title']??""; ?>
a[data-v-account-sidebar-action-link] | href = <?php echo $action['url']??""; ?>
a[data-v-account-sidebar-action-link] > i | class = <?php echo $action['icon']??""; ?>
div[data-v-account-sidebar-actions] | append = <?php }} ?>