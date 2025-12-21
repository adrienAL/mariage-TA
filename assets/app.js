// Fix vh sur mobile (100vh réel)
function setRealVh() {
  document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
}
window.addEventListener('resize', setRealVh);
window.addEventListener('orientationchange', setRealVh);
setRealVh();


// MOT DE PASSE (avant login)
const pwdBtn = document.getElementById('pwd-btn');
const pwdInput = document.getElementById('pwd-input');

// Soumission avec la touche Entrée
if (pwdInput) {
pwdInput.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') {
    e.preventDefault();
    pwdBtn.click(); // on déclenche exactement le même comportement que le bouton
  }
});
}

if (pwdBtn) {
  pwdBtn.addEventListener('click', async () => {
    const pwd = document.getElementById('pwd-input').value.trim();
    const res = await fetch('api/check_password.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({password: pwd})
    });
    const data = await res.json();
    const err = document.getElementById('pwd-error');
    if (data.success) {
      // on recharge la page pour afficher la SPA
      window.location.reload();
    } else {
      err.textContent = data.message || 'Mot de passe incorrect';
    }
  });
}

const tabs = document.querySelectorAll('.tab-btn');
const pages = document.querySelectorAll('.page');

function showPage(target) {
  pages.forEach(p => p.classList.remove('active'));
  const page = document.getElementById(target);
  if (page) {
    page.classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
}
// showPage now also toggles header visibility: header is visible on all pages except
// the home landing state (hidden until user scrolls)
function showPage(target) {
  pages.forEach(p => p.classList.remove('active'));
  const page = document.getElementById(target);
  if (page) {
    page.classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  if (target !== 'home') {
    // ensure header visible on any non-home page
    document.body.classList.add('show-header');
  } else {
    // returning to home: hide header again if we're at the top
    setTimeout(() => {
      if (window.scrollY === 0) {
        document.body.classList.remove('show-header');
        document.body.classList.add('landing');
      }
    }, 300);
  }
}

tabs.forEach(btn => {
  btn.addEventListener('click', () => {
    const target = btn.getAttribute('data-tab');
    // on ne change pas de page quand on clique sur "Nous contacter"
    if (target === 'contact') {
      return;
    }
    showPage(target);
  });
});

// Rickroll interne (popup + video locale)
const contactTab = document.querySelector('.tab-btn[data-tab="contact"]');
const rickrollOverlay = document.getElementById('rickroll-overlay');
const rickrollVideo = document.getElementById('rickroll-video');

// éléments du menu mobile
const burger = document.querySelector('.burger');
const tabsContainer = document.querySelector('.tabs');

if (contactTab && rickrollOverlay) {

  contactTab.addEventListener('click', (e) => {
    e.preventDefault();

    // 1) Fermer le menu burger si ouvert
    if (tabsContainer && tabsContainer.classList.contains('open')) {
      tabsContainer.classList.remove('open');
      if (burger) {
        burger.classList.remove('open');
        burger.setAttribute('aria-expanded', 'false');
      }
    }

    // 2) Afficher la popup
    rickrollOverlay.classList.add('visible');

    // 3) Lancer la vidéo
    if (rickrollVideo) {
      rickrollVideo.currentTime = 0;
      rickrollVideo.play().catch(()=>{});
    }
  });

  // Fermeture classique
  const rrClose = rickrollOverlay.querySelector('.popup-close');
  if (rrClose) {
    rrClose.addEventListener('click', () => {
      rickrollOverlay.classList.remove('visible');
      if (rickrollVideo) rickrollVideo.pause();
    });
  }

  // Fermeture clic sur fond
  rickrollOverlay.addEventListener('click', (e) => {
    if (e.target === rickrollOverlay) {
      rickrollOverlay.classList.remove('visible');
      if (rickrollVideo) rickrollVideo.pause();
    }
  });
}

// Bouton "Réserver ma place au dortoir" -> page Shaduns
const shadunsButtons = document.querySelectorAll('.open-shaduns');
shadunsButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    showPage('shaduns');
  });
});


// Popup globale
const popupOverlay = document.getElementById('global-popup');
const popupMessage = document.getElementById('popup-message');
const popupClose = popupOverlay ? popupOverlay.querySelector('.popup-close') : null;

function showPopup(message) {
  if (!popupOverlay || !popupMessage) return;
  popupMessage.innerHTML = message;
  popupOverlay.classList.add('visible');
}

// Click sur le nom du site -> retour à l'accueil
const brand = document.querySelector('.brand');
if (brand) {
  brand.style.cursor = 'pointer';
  brand.addEventListener('click', () => {
    showPage('home');
    window.scrollTo({top: 0, behavior: 'smooth'});
    // try to hide header after returning to top
    setTimeout(() => {
      if (window.scrollY === 0) {
        document.body.classList.remove('show-header');
      }
    }, 700);
  });
}


function hidePopup() {
  if (!popupOverlay) return;
  popupOverlay.classList.remove('visible');
}

if (popupClose) {
  popupClose.addEventListener('click', hidePopup);
}
if (popupOverlay) {
  // clic sur le fond pour fermer
  popupOverlay.addEventListener('click', (e) => {
    if (e.target === popupOverlay) {
      hidePopup();
    }
  });
}

// Afficher l’accueil + popup après une réponse valide
function showHomeWithPopup(message) {
  // activer la page "home"
  pages.forEach(p => p.classList.remove('active'));
  const homePage = document.getElementById('home');
  if (homePage) {
    homePage.classList.add('active');
  }

  // si burger menu ouvert, le refermer (optionnel)
  const burger = document.querySelector('.burger');
  const tabsContainer = document.querySelector('.tabs');
  if (burger && tabsContainer && tabsContainer.classList.contains('open')) {
    tabsContainer.classList.remove('open');
    burger.classList.remove('open');
    burger.setAttribute('aria-expanded', 'false');
  }

  window.scrollTo({top: 0, behavior: 'smooth'});
  showPopup(message);
  // ensure header hides again when returning to home at top
  setTimeout(() => {
    if (window.scrollY === 0) document.body.classList.remove('show-header');
  }, 500);
}

// Afficher une popup en fonction des paramètres d'URL (ex: retour shaduns)
const urlParams = new URLSearchParams(window.location.search);
const popupParam = urlParams.get('popup');

if (popupParam === 'shaduns_ok') {
  // On force l’affichage de la page d’accueil
  pages.forEach(p => p.classList.remove('active'));
  const homePage = document.getElementById('home');
  if (homePage) {
    homePage.classList.add('active');
  }
  window.scrollTo({ top: 0, behavior: 'smooth' });

  showPopup('Merci, nous avons bien enregistré votre réservation pour le dortoir.');
}


// Burger menu (mobile)
/*const burger = document.querySelector('.burger');
const tabsContainer = document.querySelector('.tabs');*/

if (burger && tabsContainer) {
  burger.addEventListener('click', () => {
    const isOpen = tabsContainer.classList.toggle('open');
    burger.classList.toggle('open', isOpen);
    burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  });

  // Fermer le menu après avoir cliqué sur un onglet
  tabs.forEach(btn => {
    btn.addEventListener('click', () => {
      tabsContainer.classList.remove('open');
      burger.classList.remove('open');
      burger.setAttribute('aria-expanded', 'false');
    });
  });
}


// Compte à rebours jusqu'au 24 octobre 2026 à 15h (Beaujolais / Paris)
function startCountdown() {
  const targetDate = new Date('2026-10-24T16:00:00+02:00').getTime();

  setInterval(() => {
    const now = new Date().getTime();
    const distance = targetDate - now;

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000*60*60*24)) / (1000*60*60));
    const mins = Math.floor((distance % (1000*60*60)) / (1000*60));
    const secs = Math.floor((distance % (1000*60)) / 1000);

    document.getElementById('cd-days').textContent = days >= 0 ? days : 0;
    document.getElementById('cd-hours').textContent = hours >= 0 ? hours : 0;
    document.getElementById('cd-mins').textContent = mins >= 0 ? mins : 0;
    document.getElementById('cd-secs').textContent = secs >= 0 ? secs : 0;
  }, 1000);
}
if (document.getElementById('countdown')) {
  startCountdown();
}

// Simple carousel responsive (autoplay + controls + touch)
function initCarousel() {
  const carousel = document.querySelector('.carousel');
  if (!carousel) return;
  const track = carousel.querySelector('.carousel-track');
  const slides = Array.from(carousel.querySelectorAll('.slide'));
  const prev = carousel.querySelector('.carousel-btn.prev');
  const next = carousel.querySelector('.carousel-btn.next');
  const dotsContainer = carousel.querySelector('.carousel-dots');
  let index = 0;
  let slideWidth = carousel.getBoundingClientRect().width;
  let timer = null;

  // build dots
  slides.forEach((_, i) => {
    const btn = document.createElement('button');
    btn.addEventListener('click', () => goTo(i));
    dotsContainer.appendChild(btn);
  });

  const dots = Array.from(dotsContainer.children);

  function update() {
    // use pixel translation to avoid percent rounding issues on mobile
    track.style.transform = `translateX(${-(index * slideWidth)}px)`;
    dots.forEach(d => d.classList.remove('active'));
    if (dots[index]) dots[index].classList.add('active');
  }

  function goTo(i) {
    index = (i + slides.length) % slides.length;
    update();
    resetAuto();
  }

  function nextSlide() { goTo(index + 1); }
  function prevSlide() { goTo(index - 1); }

  if (next) next.addEventListener('click', nextSlide);
  if (prev) prev.addEventListener('click', prevSlide);

  // autoplay
  function startAuto() {
    timer = setInterval(nextSlide, 5000);
  }
  function resetAuto() {
    if (timer) clearInterval(timer);
    startAuto();
  }

  // pause on hover/focus
  carousel.addEventListener('mouseenter', () => { if (timer) clearInterval(timer); });
  carousel.addEventListener('mouseleave', startAuto);

  // touch support (with simple threshold)
  let startX = 0;
  carousel.addEventListener('touchstart', (e) => {
    startX = e.touches[0].clientX;
    if (timer) clearInterval(timer);
  }, {passive: true});

  carousel.addEventListener('touchend', (e) => {
    const dx = (e.changedTouches[0].clientX - startX);
    if (Math.abs(dx) > (slideWidth * 0.12)) { // 12% of width threshold
      if (dx < 0) nextSlide(); else prevSlide();
    }
    resetAuto();
  }, {passive: true});

  // init
  update();
  startAuto();
  // responsive resize
  window.addEventListener('resize', () => {
    // recompute slide width and reposition to current index
    slideWidth = carousel.getBoundingClientRect().width;
    update();
  });
}

document.addEventListener('DOMContentLoaded', initCarousel);

// Header reveal on scroll: show header when user scrolls down
function initHeaderReveal() {
  // header behaviour depends on which page is currently active
  function onScroll() {
    const home = document.getElementById('home');
    const isHomeActive = home && home.classList.contains('active');

    if (!isHomeActive) {
      // On other pages header must always be visible and landing removed
      document.body.classList.add('show-header');
      document.body.classList.remove('landing');
      return;
    }

    // home page behaviour: hide header at very top, show when scrolled
    if (window.scrollY > 20) {
      document.body.classList.add('show-header');
      document.body.classList.remove('landing');
    } else {
      document.body.classList.remove('show-header');
      document.body.classList.add('landing');
    }
  }

  window.addEventListener('scroll', onScroll, {passive: true});
  // call once to set initial state
  onScroll();
}

document.addEventListener('DOMContentLoaded', initHeaderReveal);

// Scroll-down button behavior (from hero to next section)
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('hero-scroll');
  if (!btn) return;
  // smoother scrolling: target the `.intro` block (if present) and animate
  function smoothScrollTo(endY, duration = 700) {
    const startY = window.scrollY || window.pageYOffset;
    const distance = endY - startY;
    const startTime = performance.now();

    function easeInOutQuad(t) { return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t; }

    function step(now) {
      const time = Math.min(1, (now - startTime) / duration);
      const eased = easeInOutQuad(time);
      window.scrollTo(0, Math.round(startY + distance * eased));
      if (time < 1) requestAnimationFrame(step);
    }

    requestAnimationFrame(step);
  }

  btn.addEventListener('click', (e) => {
    e.preventDefault();
    // Prefer the .intro element; fall back to #deroule or #content
    const intro = document.querySelector('.intro');
    const target = intro || document.getElementById('deroule') || document.getElementById('content');
    if (!target) return;

    const headerOffset = document.body.classList.contains('show-header') ? document.querySelector('.topbar')?.offsetHeight || 0 : 0;
    const targetTop = target.getBoundingClientRect().top + window.scrollY;
    const offsetAboveIntro = 15; // px above the intro element
    const finalY = Math.max(0, targetTop - headerOffset - offsetAboveIntro);

    // animate with a comfortable duration
    smoothScrollTo(finalY, 700);
  });
});

/*
// Musique auto
const music = document.getElementById('bg-music');
if (music) {
  // certains navigateurs demandent une interaction avant de jouer
  document.addEventListener('click', () => {
    if (music.paused) music.play().catch(()=>{});
  }, {once: true});
}
*/
///////////////////////////////////////////
//           Formulaire RSVP             //
///////////////////////////////////////////

const rsvpForm = document.getElementById('rsvp-form');
if (rsvpForm) {
  const nbPersonnesInput = rsvpForm.querySelector('input[name="nb_personnes"]');
  const extraGuestsContainer = document.getElementById('extra-guests');
  const extraFieldsContainer = document.getElementById('rsvp-extra-fields');
  const presenceSelect = document.getElementById('rsvp-presence');

  function renderExtraGuests() {
    if (!nbPersonnesInput || !extraGuestsContainer) return;
    let nb = parseInt(nbPersonnesInput.value, 10);
    if (isNaN(nb) || nb < 1) nb = 1;

    extraGuestsContainer.innerHTML = '';

    if (nb <= 1) return;

    for (let i = 2; i <= nb; i++) {
      const row = document.createElement('div');
      row.className = 'form-row';

      const label = document.createElement('label');
      label.textContent = `Prénom de la personne ${i}`;

      const input = document.createElement('input');
      input.type = 'text';
      input.name = `other_firstname_${i}`;

      row.appendChild(label);
      row.appendChild(input);
      extraGuestsContainer.appendChild(row);
    }
  }

  function toggleExtraFields() {
    if (!presenceSelect || !extraFieldsContainer) return;
    const presence = presenceSelect.value;
    if (presence === 'non') {
      extraFieldsContainer.style.display = 'none';
      // On remet à 1 et on nettoie les invités supplémentaires
      if (nbPersonnesInput) {
        nbPersonnesInput.value = 1;
      }
      if (extraGuestsContainer) {
        extraGuestsContainer.innerHTML = '';
      }
    } else {
      extraFieldsContainer.style.display = '';
    }
  }

  if (nbPersonnesInput) {
    nbPersonnesInput.addEventListener('input', renderExtraGuests);
    nbPersonnesInput.addEventListener('change', renderExtraGuests);
  }
  if (presenceSelect) {
    presenceSelect.addEventListener('change', toggleExtraFields);
  }

  // init
  renderExtraGuests();
  toggleExtraFields();

  // Soumission du formulaire
  rsvpForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Désactive le bouton pour éviter le spam
    const submitBtn = rsvpForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
  
    const formData = new FormData(rsvpForm);

    try {
      const res = await fetch('api/rsvp.php', {
        method: 'POST',
        body: formData
      });
      const data = await res.json();
      const status = document.getElementById('rsvp-status');
      status.textContent = data.message;
      status.classList.remove('success', 'error');

        if (data.success) {
            status.classList.add('success');
            rsvpForm.reset();
            renderExtraGuests();
            toggleExtraFields();
            
            // On regarde la réponse : oui / non
            const presence = formData.get('presence');
            
            // Si la personne ne vient pas
            if (presence === 'non') {
                showHomeWithPopup("Tant pis pour toi 😜");
            } else {
                // Si la personne vient, on garde ton message avec le nombre de personnes
                
                let nb = parseInt(formData.get('nb_personnes'), 10);
                if (isNaN(nb) || nb < 1) nb = 1;
                
                const popupMessage = `Et ${nb > 1 ? ' ' + nb + ' réservations qui vont bien' : ' ' + nb + ' réservation qui va bien'} ❤️`;
                
                showHomeWithPopup(popupMessage);
            }

        } else {
            status.classList.add('error');
            // Popup d’erreur optionnelle
            showPopup(data.message || 'Aie ça a chier, essaye encore ou appel nous');
        } 
    } catch (err) {
        showPopup('Là c\'est la tuile');
    } finally {
        // Réactivation du bouton (si nécessaire)
        submitBtn.disabled = false;
    }
  });
}

// Formulaire Shaduns
const shadunsForm = document.getElementById('shaduns-form');
if (shadunsForm) {
  const nbInput = document.getElementById('shaduns-nb');
  const extraContainer = document.getElementById('shaduns-extra-guests');
  const statusEl = document.getElementById('shaduns-status');

  function renderShadunsGuests() {
    if (!nbInput || !extraContainer) return;
    let nb = parseInt(nbInput.value, 10);
    if (isNaN(nb) || nb < 1) nb = 1;

    extraContainer.innerHTML = '';

    if (nb <= 1) return;

    for (let i = 2; i <= nb; i++) {
      const row = document.createElement('div');
      row.className = 'form-row';

      const label = document.createElement('label');
      label.textContent = 'Personne ' + i + ' – prénom et nom';

      const input = document.createElement('input');
      input.type = 'text';
      input.name = 'person_' + i;

      row.appendChild(label);
      row.appendChild(input);
      extraContainer.appendChild(row);
    }
  }

  if (nbInput) {
    nbInput.addEventListener('input', renderShadunsGuests);
    nbInput.addEventListener('change', renderShadunsGuests);
    renderShadunsGuests();
  }

  shadunsForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (statusEl) {
      statusEl.textContent = '';
      statusEl.classList.remove('success', 'error');
    }

    try {
      const res = await fetch('api/shaduns_resa.php', {
        method: 'POST',
        body: new FormData(shadunsForm)
      });
      const data = await res.json();

      if (!data.success) {
        if (statusEl) {
          statusEl.textContent = data.message || 'Bon on dirait que ça marche pas, essaye encore ou appel nous';
          statusEl.classList.add('error');
        }
        return;
      }

      // Succès : reset formulaire + retour à l'accueil + popup
      shadunsForm.reset();
      renderShadunsGuests();
      showHomeWithPopup('Bien joué ma gueule, tu vas pouvoir pioncer au chaud !');

    } catch (err) {
      if (statusEl) {
        statusEl.textContent = 'Je sais pas qui de nous deux a fait Nimp mais ça a pas marché';
        statusEl.classList.add('error');
      }
    }
  });
}


// Lightbox pour les photos du domaine
const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightbox-img');
const lightboxClose = lightbox ? lightbox.querySelector('.lightbox-close') : null;
const galleryImages = document.querySelectorAll('.domain-gallery img');

if (lightbox && lightboxImg && galleryImages.length > 0) {
  galleryImages.forEach(img => {
    img.addEventListener('click', () => {
      lightboxImg.src = img.src;
      lightboxImg.alt = img.alt || '';
      lightbox.classList.add('visible');
      lightbox.setAttribute('aria-hidden', 'false');
    });
  });

  function closeLightbox() {
    lightbox.classList.remove('visible');
    lightbox.setAttribute('aria-hidden', 'true');
    lightboxImg.src = '';
  }

  if (lightboxClose) {
    lightboxClose.addEventListener('click', closeLightbox);
  }

  lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) {
      closeLightbox();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && lightbox.classList.contains('visible')) {
      closeLightbox();
    }
  });
}

