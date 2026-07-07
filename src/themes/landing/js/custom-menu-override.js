// Custom JavaScript to override automatic th-item-has-children class addition

document.addEventListener('DOMContentLoaded', function() {
    // Function to remove th-item-has-children class from items that shouldn't have it
    function removeUnwantedThClasses() {
        const mobileMenuItems = document.querySelectorAll('.mobile-menu-list li.mobile-menu-item');
        
        mobileMenuItems.forEach(function(item) {
            // Check if the item has menu-item-has-children class
            const hasMenuChildren = item.classList.contains('menu-item-has-children');
            
            // If it doesn't have menu-item-has-children but has th-item-has-children, remove it
            if (!hasMenuChildren && item.classList.contains('th-item-has-children')) {
                item.classList.remove('th-item-has-children');
            }
        });
    }
    
    // Run immediately
    removeUnwantedThClasses();
    
    // Run after a short delay to ensure the original script has finished
    setTimeout(removeUnwantedThClasses, 100);
    
    // Run periodically to catch any dynamically added classes
    setInterval(removeUnwantedThClasses, 1000);
    
    // Override the original thmobilemenu function to prevent automatic class addition
    if (typeof jQuery !== 'undefined' && jQuery.fn.thmobilemenu) {
        const originalThMobileMenu = jQuery.fn.thmobilemenu;
        
        jQuery.fn.thmobilemenu = function(options) {
            // Call the original function
            const result = originalThMobileMenu.call(this, options);
            
            // After the original function runs, remove unwanted classes
            setTimeout(removeUnwantedThClasses, 50);
            
            return result;
        };
    }
}); 