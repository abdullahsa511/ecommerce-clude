import(common.tpl)

head > title = $this->product['name']

#hero-product | before = <?php 
    $heroproduct = $current_component = $this->_component['heroproduct']?? [];
    echo '<pre>';
    print_r($heroproduct);
    echo '</pre>';
?>
