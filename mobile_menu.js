// Mobile Menu Functionality
document.addEventListener('DOMContentLoaded', function() {
  // Create hamburger menu if it doesn't exist
  const navbar = document.querySelector('.navbar');
  const navlinks = document.querySelector('.navlinks');
  
  if (!document.querySelector('.hamburger')) {
    const hamburger = document.createElement('button');
    hamburger.className = 'hamburger';
    hamburger.setAttribute('aria-label', 'Toggle menu');
    hamburger.innerHTML = `
      <span></span>
      <span></span>
      <span></span>
    `;
    
    // Insert hamburger before registration button or at the end
    const regBtn = document.querySelector('.registration-btn');
    if (regBtn) {
      navbar.insertBefore(hamburger, regBtn);
    } else {
      navbar.appendChild(hamburger);
    }
  }
  
  // Create overlay if it doesn't exist
  if (!document.querySelector('.nav-overlay')) {
    const overlay = document.createElement('div');
    overlay.className = 'nav-overlay';
    document.body.appendChild(overlay);
  }
  
  const hamburger = document.querySelector('.hamburger');
  const overlay = document.querySelector('.nav-overlay');
  const dropdowns = document.querySelectorAll('.dropdown');
  
  // Toggle mobile menu
  function toggleMenu() {
    hamburger.classList.toggle('active');
    navlinks.classList.toggle('active');
    overlay.classList.toggle('active');
    document.body.style.overflow = navlinks.classList.contains('active') ? 'hidden' : '';
  }
  
  // Close mobile menu
  function closeMenu() {
    hamburger.classList.remove('active');
    navlinks.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
    
    // Close all dropdowns
    dropdowns.forEach(dropdown => {
      dropdown.classList.remove('active');
    });
  }
  
  // Hamburger click
  hamburger.addEventListener('click', toggleMenu);
  
  // Overlay click
  overlay.addEventListener('click', closeMenu);
  
  // Handle dropdown clicks on mobile
  dropdowns.forEach(dropdown => {
    const toggle = dropdown.querySelector('.dropdown-toggle');
    
    toggle.addEventListener('click', function(e) {
      if (window.innerWidth <= 768) {
        e.preventDefault();
        dropdown.classList.toggle('active');
      }
    });
  });
  
  // Close menu when clicking on non-dropdown links
  const directLinks = document.querySelectorAll('.navlinks > li:not(.dropdown) > a');
  directLinks.forEach(link => {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 768) {
        closeMenu();
      }
    });
  });
  
  // Close menu when clicking dropdown menu items
  const dropdownLinks = document.querySelectorAll('.dropdown-menu a');
  dropdownLinks.forEach(link => {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 768) {
        closeMenu();
      }
    });
  });
  
  // Handle window resize
  let resizeTimer;
  window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
      if (window.innerWidth > 768) {
        closeMenu();
      }
    }, 250);
  });
  
  // Prevent scroll on body when menu is open (iOS fix)
  let scrollPosition = 0;
  
  const preventScroll = function() {
    scrollPosition = window.pageYOffset;
    document.body.style.overflow = 'hidden';
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollPosition}px`;
    document.body.style.width = '100%';
  };
  
  const allowScroll = function() {
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('position');
    document.body.style.removeProperty('top');
    document.body.style.removeProperty('width');
    window.scrollTo(0, scrollPosition);
  };
  
  // Update menu toggle to use new scroll prevention
  const originalToggleMenu = toggleMenu;
  toggleMenu = function() {
    if (!navlinks.classList.contains('active')) {
      preventScroll();
    } else {
      allowScroll();
    }
    originalToggleMenu();
  };
  
  // Update closeMenu to use new scroll prevention
  const originalCloseMenu = closeMenu;
  closeMenu = function() {
    allowScroll();
    originalCloseMenu();
  };
  
  // Re-attach event listeners with updated functions
  hamburger.removeEventListener('click', originalToggleMenu);
  hamburger.addEventListener('click', toggleMenu);
  
  overlay.removeEventListener('click', originalCloseMenu);
  overlay.addEventListener('click', closeMenu);
});