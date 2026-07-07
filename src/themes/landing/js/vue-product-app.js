//###################### Product List Section Start here ##########################
document.addEventListener("DOMContentLoaded", async function () {
  const categoryModule = await import('/js/vue/category.js');
  const categoryApp = categoryModule.default;

  try {
    let categoryAppContainer = document.getElementById('vue-app-product-list-container');
    if (categoryAppContainer) {
      await categoryApp.loadCategoryProductListing(categoryAppContainer);
    } 
  } catch (error) {
    console.error('Error loading category product listing:', error);
  }

});
//###################### Product List Section End here ##########################

//###################### Product Configurator Start here ##########################
document.addEventListener("DOMContentLoaded", async function () {
    // import product configurator app global variable
    const module = await import('/js/vue/productconfigurator.js');
    const productConfiguratorApp = module.default;
    const product = window.product;
    const modelData = window.modelData;
    const accessories = window.accessories;
    const productConfiguration = window.configuration;
    try {
      let appContainer = document.getElementById('th-product-configurator-app');
      if (appContainer) {
          await productConfiguratorApp.loadProductConfigurator(appContainer, productConfiguration, product, modelData, accessories);
      } 
    } catch (error) {
        console.error('Error loading product configurator:', error);
    }
});
//###################### Product Configurator End here ##########################

//###################### Search List Section Start here ##########################
document.addEventListener("DOMContentLoaded", async function () {
  const searchModule = await import('/js/vue/search.js');
  const searchApp = searchModule.default;

  try {
    let searchAppContainer = document.getElementById('vue-app-search-list-container');
    if (searchAppContainer) {
      await searchApp.loadCategoryProductListing(searchAppContainer);
    } 
  } catch (error) {
    console.error('Error loading search list:', error);
  }

});
//###################### Search List Section End here ##########################