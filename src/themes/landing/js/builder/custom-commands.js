// Custom Commands for Vvveb Builder
// This file contains custom commands that can be used in sections

// Example of a custom command object
const customCommands = {
    // Command for featured product slider
    createFeaturedProductSlider: {
        execute: function() {
            createFeaturedProductSlider();
            console.log("Featured product slider created");
        }
    },
    
    // Command for initializing Swiper
    initializeSwiper: {
        execute: function() {
            if (typeof Swiper !== 'undefined') {
                new Swiper('.swiper', {
                    slidesPerView: 1,
                    spaceBetween: 30,
                    loop: true,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                });
            }
        }
    },
    
    // Command for masonry layout
    initializeMasonry: {
        execute: function() {
            if (typeof Masonry !== 'undefined') {
                new Masonry('.grid', {
                    itemSelector: '.grid-item',
                    columnWidth: '.grid-sizer',
                    percentPosition: true
                });
            }
        }
    },
    
    // Command for custom animations
    initializeAnimations: {
        execute: function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true
                });
            }
        }
    }
};

// Function to get a command by name
function getCommand(commandName) {
    return customCommands[commandName];
}

// Function to execute a command
function executeCommand(commandName) {
    const command = getCommand(commandName);
    if (command && typeof command.execute === 'function') {
        setTimeout(() => {
            command.execute();
        }, 200);
    }
}

// Function to execute multiple commands
function executeCommands(commands) {
    if (Array.isArray(commands)) {
        commands.forEach(cmd => {
            if (typeof cmd === 'string') {
                executeCommand(cmd);
            } else if (typeof cmd === 'object' && cmd.execute) {
                setTimeout(() => {
                    cmd.execute();
                }, 200);
            }
        });
    }
} 