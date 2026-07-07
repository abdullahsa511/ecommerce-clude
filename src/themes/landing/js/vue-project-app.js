document.addEventListener("DOMContentLoaded", function () {


  function updateUrlPagination(per_page, current_page) {
    const url = new URL(window.location);
    url.searchParams.set("per_page", per_page);
    url.searchParams.set("current_page", current_page);
    window.history.replaceState({}, "", url.toString());
  }

    // update url page
    const paginationEl = document.getElementById('all-project-pagination');
    const currentPage = paginationEl?.dataset.currentPage || 1;
    const perPage = paginationEl?.dataset.perPage || 30;
    
    // console.log('currentPage blog app', currentPage ?? 1);
    // console.log('perPage blog app', perPage ?? 30);

    updateUrlPagination(perPage ?? 30, currentPage ?? 1);

    const loadMoreBtn = document.getElementById("load_more_button");
    
    loadMoreBtn.addEventListener("click", async function () {
      // Disable button during load
      loadMoreBtn.disabled = true;
      loadMoreBtn.textContent = 'Loading...';
      
      try {
        // Check if the app container already exists
        let appContainer = document.getElementById('th-project-app');
        if (!appContainer) {
          appContainer = document.createElement('div');
          appContainer.id = 'th-project-app';
          appContainer.className = 'row';
          
          // Insert the container before the button row
          const buttonRow = this.closest('.row');
          buttonRow.before(appContainer);
        }
        
        await initializeVueApp(appContainer);
        
        // Re-enable button
        loadMoreBtn.disabled = false;
        loadMoreBtn.textContent = 'Load More';
      } catch (error) {
        console.error('Error loading more posts:', error);
        loadMoreBtn.disabled = false;
        loadMoreBtn.textContent = 'Load More';
        alert('Failed to load posts. Please try again.');
      }
    });

    // Function to initialize Vue app with dynamic content
    async function initializeVueApp(container) {
      try {
        // Import the Vue app
        const { default: projectApp } = await import('/js/vue/projectlist.js');
        
        // Load more posts (this will dispatch action and render component if needed)
        const response = await projectApp.loadMoreProjectList(container);
        
        if (response.error) {
          console.error('Error from projectApp:', response.error);
        }
      } catch (error) {
        console.error('Error initializing Vue project app:', error);
        container.innerHTML = '<div class="alert alert-danger">Error loading project posts</div>';
        throw error;
      }
    }
  });
  