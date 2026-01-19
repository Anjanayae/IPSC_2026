// Particle animation for space-themed background
document.addEventListener('DOMContentLoaded', function() {
  const particlesContainer = document.getElementById('particles');
  
  if (!particlesContainer) return;
  
  // Create particles
  const particleCount = 100;
  
  for (let i = 0; i < particleCount; i++) {
    const particle = document.createElement('div');
    particle.className = 'particle';
    
    // Random position
    const x = Math.random() * 100;
    const y = Math.random() * 100;
    
    // Random size
    const size = Math.random() * 3 + 1;
    
    // Random animation duration
    const duration = Math.random() * 3 + 2;
    
    // Random opacity
    const opacity = Math.random() * 0.5 + 0.3;
    
    particle.style.left = x + '%';
    particle.style.top = y + '%';
    particle.style.width = size + 'px';
    particle.style.height = size + 'px';
    particle.style.setProperty('--duration', duration + 's');
    particle.style.setProperty('--opacity', opacity);
    
    particlesContainer.appendChild(particle);
  }
  
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });
  
  // Intersection Observer for scroll animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
  };
  
  const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);
  
  // Observe all sections
  document.querySelectorAll('.feature-card, .highlight-card, .topic-item').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
  });
  
  // Add parallax effect to hero section
  let lastScrollTop = 0;
  window.addEventListener('scroll', function() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const planets = document.querySelectorAll('.planet');
    
    planets.forEach((planet, index) => {
      const speed = (index + 1) * 0.5;
      planet.style.transform = `translateY(${scrollTop * speed}px)`;
    });
    
    lastScrollTop = scrollTop;
  }, { passive: true });
  
  // Add hover effect to cards
  const cards = document.querySelectorAll('.feature-card, .highlight-card');
  
  cards.forEach(card => {
    card.addEventListener('mousemove', function(e) {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      
      const centerX = rect.width / 2;
      const centerY = rect.height / 2;
      
      const rotateX = (y - centerY) / 10;
      const rotateY = (centerX - x) / 10;
      
      card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px)`;
    });
    
    card.addEventListener('mouseleave', function() {
      card.style.transform = '';
    });
  });
  
  // Typing effect for theme text (optional enhancement)
  const themeText = document.querySelector('.theme-text');
  if (themeText) {
    const originalText = themeText.textContent;
    let charIndex = 0;
    
    function typeWriter() {
      if (charIndex === 0) {
        themeText.textContent = '';
      }
      
      if (charIndex < originalText.length) {
        themeText.textContent += originalText.charAt(charIndex);
        charIndex++;
        setTimeout(typeWriter, 50);
      }
    }
    
    // Start typing effect after a delay
    setTimeout(typeWriter, 2000);
  }
});

// Add cursor trail effect
document.addEventListener('mousemove', function(e) {
  // Create a small particle at cursor position occasionally
  if (Math.random() > 0.9) {
    const particle = document.createElement('div');
    particle.style.position = 'fixed';
    particle.style.left = e.clientX + 'px';
    particle.style.top = e.clientY + 'px';
    particle.style.width = '4px';
    particle.style.height = '4px';
    particle.style.borderRadius = '50%';
    particle.style.background = 'rgba(244, 114, 182, 0.6)';
    particle.style.pointerEvents = 'none';
    particle.style.zIndex = '9999';
    particle.style.animation = 'fadeOut 1s ease-out forwards';
    
    document.body.appendChild(particle);
    
    setTimeout(() => {
      particle.remove();
    }, 1000);
  }
});

// CSS for cursor trail animation
const style = document.createElement('style');
style.textContent = `
  @keyframes fadeOut {
    0% {
      opacity: 1;
      transform: scale(1);
    }
    100% {
      opacity: 0;
      transform: scale(0);
    }
  }
`;
document.head.appendChild(style);
