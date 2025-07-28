// Mobile Sidenav Functionality for HOSPROGRESO

document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sidenav = document.getElementById('sidenav-main');
    const sidenavOverlay = document.getElementById('sidenav-overlay');
    const iconSidenav = document.getElementById('iconSidenav');
    const body = document.body;



    // Function to show sidenav
    function showSidenav() {
        body.classList.add('sidenav-mobile-open');
        body.classList.remove('g-sidenav-show');
        // Prevent background scrolling
        body.style.overflow = 'hidden';
    }

    // Function to hide sidenav
    function hideSidenav() {
        body.classList.remove('sidenav-mobile-open');
        body.classList.remove('g-sidenav-show');
        // Restore background scrolling
        body.style.overflow = '';
    }

    // Mobile menu toggle button
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (body.classList.contains('sidenav-mobile-open')) {
                hideSidenav();
            } else {
                showSidenav();
            }
        });
    }

    // Close button in sidenav
    if (iconSidenav) {
        iconSidenav.addEventListener('click', function(e) {
            e.preventDefault();
            hideSidenav();
        });
    }



    // Overlay click to close sidenav
    if (sidenavOverlay) {
        sidenavOverlay.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            hideSidenav();
        });
    }

    // Prevent clicks inside sidenav from closing it
    if (sidenav) {
        sidenav.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Close sidenav when clicking outside (anywhere on overlay)
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 1200 && body.classList.contains('sidenav-mobile-open')) {
            // If click is not on sidenav or hamburger button, close menu
            if (!sidenav.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                e.preventDefault();
                e.stopPropagation();
                hideSidenav();
            }
        }
    }, true); // Use capture phase to catch events early

    // Close sidenav when clicking on a link (mobile only) - BUT NOT on collapse toggles
    const sidenavLinks = sidenav.querySelectorAll('a[href]:not([data-bs-toggle="collapse"])');
    sidenavLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Only close on mobile devices and only if it's a real navigation link
            if (window.innerWidth < 1200 && !this.hasAttribute('data-bs-toggle')) {
                // Small delay to allow the link to work
                setTimeout(() => {
                    hideSidenav();
                }, 100);
            }
        });
    });

    // Handle menu collapse animations
    const menuItems = sidenav.querySelectorAll('[data-bs-toggle="collapse"]');
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Add visual feedback
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });

    // Improve menu accessibility on mobile
    const menuLinks = sidenav.querySelectorAll('.nav-link');
    menuLinks.forEach(link => {
        link.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        link.addEventListener('touchend', function() {
            this.style.transform = '';
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1200) {
            // On desktop, ensure sidenav is visible
            body.classList.add('g-sidenav-show');
            body.classList.remove('sidenav-mobile-open');
        } else {
            // On mobile/tablet, hide sidenav by default
            body.classList.remove('g-sidenav-show');
            body.classList.remove('sidenav-mobile-open');
        }
    });

    // Initialize based on screen size
    if (window.innerWidth < 1200) {
        // Mobile/Tablet: hide sidenav by default
        body.classList.remove('g-sidenav-show');
        body.classList.remove('sidenav-mobile-open');
    } else {
        // Desktop: show sidenav by default
        body.classList.add('g-sidenav-show');
        body.classList.remove('sidenav-mobile-open');
    }

    // Handle escape key to close sidenav
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && window.innerWidth < 1200) {
            hideSidenav();
        }
    });

    // Handle touch events to close sidenav
    let touchStartX = 0;
    document.addEventListener('touchstart', function(e) {
        if (window.innerWidth < 1200 && body.classList.contains('sidenav-mobile-open')) {
            touchStartX = e.touches[0].clientX;
        }
    });

    document.addEventListener('touchend', function(e) {
        if (window.innerWidth < 1200 && body.classList.contains('sidenav-mobile-open')) {
            const touchEndX = e.changedTouches[0].clientX;
            const swipeDistance = touchStartX - touchEndX;
            
            // If swipe left on sidenav area, close it
            if (swipeDistance > 50 && touchStartX < 280) {
                hideSidenav();
            }
            
            // If touch outside sidenav area, close it
            if (touchEndX > 280) {
                hideSidenav();
            }
        }
    });

    // Allow body scroll - removed prevention to allow horizontal scroll when needed
});

// Additional mobile optimizations
document.addEventListener('DOMContentLoaded', function() {
    // Optimize touch interactions
    const touchElements = document.querySelectorAll('.nav-link, .btn, .dropdown-item');
    
    touchElements.forEach(element => {
        element.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        element.addEventListener('touchend', function() {
            this.style.transform = '';
        });
    });

    // Improve dropdown performance on mobile
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (toggle && menu) {
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target)) {
                    const bsDropdown = bootstrap.Dropdown.getInstance(toggle);
                    if (bsDropdown) {
                        bsDropdown.hide();
                    }
                }
            });
        }
    });

    // Optimize table scrolling on mobile
    const tables = document.querySelectorAll('.table-responsive');
    
    tables.forEach(table => {
        table.addEventListener('touchstart', function(e) {
            this.style.overflowX = 'auto';
        });
        
        table.addEventListener('touchend', function(e) {
            // Keep overflow auto for better mobile experience
        });
    });

    // Improve form interactions on mobile
    const formControls = document.querySelectorAll('input, select, textarea');
    
    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            // Ensure proper zoom behavior on iOS
            if (window.innerWidth <= 575.98) {
                this.style.fontSize = '16px';
            }
        });
    });

    // Optimize chart responsiveness
    const charts = document.querySelectorAll('.chart-canvas');
    
    charts.forEach(chart => {
        const resizeObserver = new ResizeObserver(entries => {
            entries.forEach(entry => {
                const canvas = entry.target;
                if (canvas.chart) {
                    canvas.chart.resize();
                }
            });
        });
        
        resizeObserver.observe(chart);
    });

    // Improve search modal on mobile
    const searchModal = document.querySelector('.search-modal');
    const searchInput = document.getElementById('globalSearchInput');
    
    if (searchModal && searchInput) {
        searchInput.addEventListener('focus', function() {
            if (window.innerWidth <= 767.98) {
                // Ensure proper positioning on mobile
                searchModal.style.paddingTop = '2vh';
            }
        });
    }

    // Optimize notifications on mobile
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationMenu = notificationDropdown?.nextElementSibling;
    
    if (notificationDropdown && notificationMenu) {
        notificationDropdown.addEventListener('click', function(e) {
            if (window.innerWidth < 1200) {
                // On mobile, convert dropdown to modal-like behavior - CENTERED
                notificationMenu.style.position = 'fixed';
                notificationMenu.style.top = '50%';
                notificationMenu.style.left = '50%';
                notificationMenu.style.right = 'auto';
                notificationMenu.style.bottom = 'auto';
                notificationMenu.style.transform = 'translate(-50%, -50%)';
                notificationMenu.style.maxWidth = '90vw';
                notificationMenu.style.width = '400px';
                notificationMenu.style.margin = '0';
                notificationMenu.style.borderRadius = '1rem';
                notificationMenu.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.3)';
                notificationMenu.style.maxHeight = '70vh';
                notificationMenu.style.overflowY = 'auto';
                notificationMenu.style.zIndex = '1060';
                
                // Force override any Bootstrap positioning
                notificationMenu.classList.remove('dropdown-menu-end');
                notificationMenu.style.setProperty('position', 'fixed', 'important');
                notificationMenu.style.setProperty('top', '50%', 'important');
                notificationMenu.style.setProperty('left', '50%', 'important');
                notificationMenu.style.setProperty('transform', 'translate(-50%, -50%)', 'important');

                // Add backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'notification-backdrop';
                backdrop.style.position = 'fixed';
                backdrop.style.top = '0';
                backdrop.style.left = '0';
                backdrop.style.width = '100%';
                backdrop.style.height = '100%';
                backdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                backdrop.style.zIndex = '1055';
                backdrop.style.backdropFilter = 'blur(3px)';
                document.body.appendChild(backdrop);

                // Close on backdrop click
                backdrop.addEventListener('click', function() {
                    const bsDropdown = bootstrap.Dropdown.getInstance(notificationDropdown);
                    if (bsDropdown) {
                        bsDropdown.hide();
                    }
                    backdrop.remove();
                });

                // Remove backdrop when dropdown closes
                notificationDropdown.addEventListener('hidden.bs.dropdown', function() {
                    backdrop.remove();
                }, { once: true });
            }
        });
    }

    // Improve card interactions on mobile
    const cards = document.querySelectorAll('.card');
    
    cards.forEach(card => {
        card.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        card.addEventListener('touchend', function() {
            this.style.transform = '';
        });
    });

    // Optimize button groups on mobile
    const buttonGroups = document.querySelectorAll('.btn-group');
    
    buttonGroups.forEach(group => {
        if (window.innerWidth <= 575.98) {
            // Convert horizontal button groups to vertical on mobile
            group.style.flexDirection = 'column';
            group.style.width = '100%';
            
            const buttons = group.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.style.width = '100%';
                button.style.marginBottom = '0.25rem';
                button.style.borderRadius = '0.375rem';
            });
        }
    });

    // Improve pagination on mobile
    const paginations = document.querySelectorAll('.pagination');
    
    paginations.forEach(pagination => {
        if (window.innerWidth <= 575.98) {
            pagination.style.justifyContent = 'center';
            pagination.style.flexWrap = 'wrap';
            
            const pageLinks = pagination.querySelectorAll('.page-link');
            pageLinks.forEach(link => {
                link.style.padding = '0.375rem 0.5rem';
                link.style.fontSize = '0.875rem';
            });
        }
    });

    // Optimize modals on mobile
    const modals = document.querySelectorAll('.modal');
    
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            if (window.innerWidth <= 575.98) {
                const dialog = this.querySelector('.modal-dialog');
                if (dialog) {
                    dialog.style.margin = '0.5rem';
                    dialog.style.maxWidth = 'calc(100% - 1rem)';
                }
            }
        });
    });

    // Improve accessibility on mobile
    const interactiveElements = document.querySelectorAll('button, a, input, select, textarea');
    
    interactiveElements.forEach(element => {
        // Ensure minimum touch target size
        if (window.innerWidth <= 575.98) {
            const rect = element.getBoundingClientRect();
            if (rect.width < 44 || rect.height < 44) {
                element.style.minHeight = '44px';
                element.style.minWidth = '44px';
            }
        }
    });

    // Optimize images for mobile
    const images = document.querySelectorAll('img');
    
    images.forEach(img => {
        if (window.innerWidth <= 575.98) {
            // Ensure images don't overflow on mobile
            img.style.maxWidth = '100%';
            img.style.height = 'auto';
        }
    });

    // Improve loading performance
    window.addEventListener('load', function() {
        // Remove loading states
        const loadingElements = document.querySelectorAll('.loading, .spinner');
        loadingElements.forEach(element => {
            element.style.display = 'none';
        });
    });

    // Handle orientation change
    window.addEventListener('orientationchange', function() {
        // Small delay to allow orientation change to complete
        setTimeout(() => {
            // Recalculate layouts
            const charts = document.querySelectorAll('.chart-canvas');
            charts.forEach(chart => {
                if (chart.chart) {
                    chart.chart.resize();
                }
            });
            
            // Adjust sidenav if needed
            if (window.innerWidth < 1200) {
                const body = document.body;
                const sidenav = document.getElementById('sidenav-main');
                if (!body.classList.contains('g-sidenav-show')) {
                    sidenav.style.transform = 'translateX(-100%)';
                }
            }
        }, 100);
    });
}); 