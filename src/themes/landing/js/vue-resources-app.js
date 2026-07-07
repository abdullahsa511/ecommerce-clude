//###################### Resource Images Section Start here ##########################
import resourceImages from '/js/vue/designresources.js';
const url = window.location.href;
const match = url.match(/\/resources\/([^\/\?]+)/);

// ### declare global variables ###
// resource type declaration
let resourceType = 'images';
if (match && match[1]) {
    resourceType = match[1];
}
document.getElementById(resourceType + '-tab').classList.add('active');
// method declaration
let method = declareMethod(resourceType);

// declare method function // i can use it any where in the code
function declareMethod(resourceType) {
    switch (resourceType) {
        case 'images':
            return 'loadMoreDesignResourceImages';
            break;
        case 'models':
            return 'loadMoreDesignResourceModels';
            break;
        case 'documents':
            return 'loadMoreDesignResourceDocuments';
            break;
        case 'finishes':
            return 'loadMoreDesignResourceFinishes';
            break;
        case 'textiles':
            return 'loadMoreDesignResourceTextiles';
            break;
        default:
            return 'loadMoreDesignResourceImages';
            break;
    }
}

// update meta
// updateMeta(resourceType);
// function updateMeta(resourceType) {
//     const basePath = "/resources";

//     const routes = {
//         images: "/images",
//         models: "/models",
//         documents: "/documents",
//         finishes: "/finishes",
//         textiles: "/textiles"
//     };

//     const path = routes[resourceType] || "/images";

//     const url = basePath + path;

//     // canonical (can be full URL if needed)
//     const canonical = document.querySelector('link[rel="canonical"]');
//     if (canonical) {
//         canonical.setAttribute('href', window.location.origin + url);
//     }

//     // og:url
//     const ogUrl = document.querySelector('meta[property="og:url"]');
//     if (ogUrl) {
//         ogUrl.setAttribute('content', window.location.origin + url);
//     }

//     // IMPORTANT: only relative URL for history API
//     window.history.replaceState({}, '', url);
// }


let lastUrlObject = {}; // last url object if need i will use 
let urlObject = {}; // current url object if need i will use 
let payload = {}; // payload object if need i will use 

document.addEventListener("DOMContentLoaded", async function () {
    console.log('this is DOMContentLoaded');
    /** --------------------- Resource Application Start --------------------- */
    document.body.addEventListener('click', function (event) {
        let autocompleteList = document.getElementById('dropdown-menu');
        autocompleteList.classList.add('d-none');
    });
    // at first load vue app 
    await initializeVueApp(document.getElementById('vue-resource-app'));
    

    // ###### initialize vue app ######
    async function initializeVueApp(container, tab = false) {
        try {
            // Import the Vue app
            const { default: resourceImages } = await import('/js/vue/designresources.js');
            // Load more posts (this will dispatch action and render component if needed)
            const response = await resourceImages[method](container, tab);
            // check error if any
            if (response.error) {
                console.error('Error from resourceImages:', response.error);
            }
        } catch (error) {
            console.error('Error initializing Vue resource images app:', error);
            container.innerHTML = '<div class="alert alert-danger">Error loading images</div>';
            throw error;
        }
    }
    // ###### tab navigation vue app ######
    document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', async function (event) {
                event.preventDefault();
                event.stopPropagation();
                const appContainer = document.getElementById('vue-resource-app');
                appContainer.innerHTML = '';
                const btn = event.target.closest('.nav-link');
                const resourceType = btn.getAttribute('data-bs-target') ? btn.getAttribute('data-bs-target').replace('#', '') : '';
                
                const baseUrl = window.location.origin;
                const newUrl = `${baseUrl}/resources/${resourceType}`;
                if (window.location.pathname + window.location.search !== `/resources/${resourceType}`) {
                    window.history.replaceState({}, '', newUrl);
                }
                console.log(method);
                method = declareMethod(resourceType);
                await initializeVueApp(appContainer, true);
            });
        });
});
