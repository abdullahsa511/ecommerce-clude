import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#pagination | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $parameters = $this->parameters??[];

    // echo '<pre>';
	// print_r($parameters);
	// echo '</pre>';

?>

div#pagination > div#current-page | innerHTML = <?php echo isset($parameters['current_page']) ? $parameters['current_page'] : ''; ?>
div#pagination > div#per-page | innerHTML = <?php echo isset($parameters['per_page']) ? $parameters['per_page'] : ''; ?>
div#pagination > div#offset | innerHTML = <?php echo isset($parameters['offset']) ? $parameters['offset'] : ''; ?>
div#pagination > div#context | innerHTML = <?php echo isset($parameters['context']) ? $parameters['context'] : ''; ?>
div#pagination > div#category | innerHTML = <?php echo isset($parameters['category']) ? $parameters['category'] : ''; ?>
div#pagination > div#model_id | innerHTML = <?php echo isset($parameters['model_id']) ? $parameters['model_id'] : ''; ?>
div#pagination > div#model_name | innerHTML = <?php echo isset($parameters['model_name']) ? $parameters['model_name'] : ''; ?>
div#pagination > div#total | innerHTML = <?php echo isset($parameters['total']) ? $parameters['total'] : ''; ?>





import(components/footer.tpl, [data-v-component-footer])