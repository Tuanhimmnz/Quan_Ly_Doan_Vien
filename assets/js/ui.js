// === Lightweight UI interactions injected by ChatGPT ===
(function(){
  // Add 'js' class on root to allow progressive enhancement
  document.documentElement.classList.add('js');

  // Reveal-on-scroll using IntersectionObserver
  try {
    var prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var revealTargets = document.querySelectorAll('.reveal-on-scroll, .reveal');

    if(prefersReduced){
      revealTargets.forEach(function(el){
        el.classList.add('is-visible');
      });
    } else if('IntersectionObserver' in window && revealTargets.length){
      var observer = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if(entry.isIntersecting){
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      }, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });

      revealTargets.forEach(function(el){
        if(el.dataset.revealReady === '1') return;
        // skip tiny elements
        if(el.offsetHeight < 2 && el.offsetWidth < 2) return;
        el.dataset.revealReady = '1';
        observer.observe(el);
      });
    } else {
      revealTargets.forEach(function(el){
        el.classList.add('is-visible');
      });
    }
  } catch(e){ /* silent */ }

  // Smooth scroll for in-page anchors (most browsers support CSS scroll-behavior already)
  document.addEventListener('click', function(e){
    var a = e.target.closest('a[href^="#"]');
    if(!a) return;
    var id = a.getAttribute('href');
    if(id.length > 1){
      var target = document.querySelector(id);
      if(target){
        e.preventDefault();
        target.scrollIntoView({behavior:'smooth', block:'start'});
        history.pushState(null, '', id);
      }
    }
  }, {passive:false});

  // Auto-upgrade basic submit inputs to .btn.btn-primary for visual consistency
  document.querySelectorAll('input[type="submit"]:not(.btn)').forEach(function(el){
    el.classList.add('btn','btn-primary');
  });
})();
// End UI kit