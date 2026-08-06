/**
 * Refugios — main.js
 * Progressive enhancement: mobile menu, sticky header,
 * scroll animations, newsletter form, cart AJAX update.
 * NOTE: Tailwind config lives in functions.php (inline via wp_add_inline_script).
 *       Do NOT define window.tailwind.config here.
 */

(function () {
    'use strict';

    /* =========================================================
       1. MOBILE NAVIGATION TOGGLE
       ========================================================= */
    const fallbackNav = document.getElementById('primary-navigation');
    const menuToggles = document.querySelectorAll('.menu-toggle');

    function getTargetNav(toggle) {
        const targetId = toggle.getAttribute('aria-controls');
        return (targetId && document.getElementById(targetId)) || fallbackNav;
    }

    function setToggleIcon(toggle, isOpen) {
        const icon = toggle.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-bars', !isOpen);
            icon.classList.toggle('fa-xmark', isOpen);
        }
    }

    menuToggles.forEach((menuToggle) => {
        const primaryNav = getTargetNav(menuToggle);
        if (!primaryNav || menuToggle.dataset.menuListenerAdded) return;
        menuToggle.dataset.menuListenerAdded = "true";

        menuToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = primaryNav.classList.toggle('is-open');
            menuToggle.setAttribute('aria-expanded', String(isOpen));
            setToggleIcon(menuToggle, isOpen);
        }, true); // capture phase to preempt Elementor/homepage scripts

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!primaryNav.contains(e.target) && !menuToggle.contains(e.target)) {
                primaryNav.classList.remove('is-open');
                menuToggle.setAttribute('aria-expanded', 'false');
                setToggleIcon(menuToggle, false);
            }
        });

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && primaryNav.classList.contains('is-open')) {
                primaryNav.classList.remove('is-open');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.focus();
            }
        });

        // --- MOBILE ONLY: Accordion behavior for submenus ---
        // Al tocar categorías con hijos, abrir/cerrar panel en vez de ir a la URL (solo si <= 900px)
        const parentItems = primaryNav.querySelectorAll('li.menu-item-has-children > a');
        parentItems.forEach(link => {
            if (link.dataset.accordionListenerAdded) return;
            link.dataset.accordionListenerAdded = "true";
            link.addEventListener('click', (e) => {
                if (window.innerWidth <= 900) {
                    e.preventDefault(); // Previene navegar a 'Libros' y en su lugar abre menú
                    const parentLi = link.parentElement;
                    // Toggle is-active class to open/close
                    parentLi.classList.toggle('is-active');

                    // Opcional: Cerrar los hermanos si quieres que actúe como acordeón puro
                    // Mantiene el código simple si solo toggle
                }
            });
        });
    });

    /* =========================================================
       1b. SEARCH PANEL TOGGLE
       ========================================================= */
    const searchToggle = document.getElementById('nav-search-toggle');
    const searchPanel = document.getElementById('nav-search-panel');
    const searchClose = document.getElementById('nav-search-close');
    const searchInput = document.getElementById('nav-search-input');

    function openSearch() {
        if (!searchPanel) return;
        searchPanel.removeAttribute('hidden');
        searchPanel.setAttribute('aria-hidden', 'false');
        searchToggle?.setAttribute('aria-expanded', 'true');
        setTimeout(() => searchInput?.focus(), 50);
    }

    function closeSearch() {
        if (!searchPanel) return;
        searchPanel.setAttribute('hidden', '');
        searchPanel.setAttribute('aria-hidden', 'true');
        searchToggle?.setAttribute('aria-expanded', 'false');
        searchToggle?.focus();
    }

    searchToggle?.addEventListener('click', () => {
        const expanded = searchToggle.getAttribute('aria-expanded') === 'true';
        expanded ? closeSearch() : openSearch();
    });

    searchClose?.addEventListener('click', closeSearch);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && searchPanel && !searchPanel.hasAttribute('hidden')) {
            closeSearch();
        }
    });

    /* =========================================================
       2. STICKY HEADER SHADOW / SLIM MODE
       ========================================================= */
    const header = document.getElementById('site-header');

    if (header) {
        const onScroll = () => {
            if (window.scrollY > 60) {
                header.classList.add('scrolled');
                header.style.boxShadow = '0 4px 0 rgba(78,52,46,0.12)';
            } else {
                header.classList.remove('scrolled');
                header.style.boxShadow = '';
            }
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll(); // initial check
    }

    /* =========================================================
       3. SCROLL REVEAL ANIMATIONS
       ========================================================= */
    if ('IntersectionObserver' in window) {
        const targets = document.querySelectorAll(
            '.book-card, .blog-card, .blog-article-row, .pausa-stat, .pausa-image-wrapper'
        );

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.1, rootMargin: '0px 0px -48px 0px' }
        );

        targets.forEach((el, i) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(16px)';
            el.style.transition = `opacity 0.45s ease ${i * 0.06}s, transform 0.45s ease ${i * 0.06}s`;
            observer.observe(el);
        });

        // When animate-in class is added, reveal the element
        const styleId = 'rf-animate-style';
        if (!document.getElementById(styleId)) {
            const style = document.createElement('style');
            style.id = styleId;
            style.textContent = '.animate-in { opacity: 1 !important; transform: translate(0,0) !important; }';
            document.head.appendChild(style);
        }
    }

    /* =========================================================
       4. HERO SCROLL INDICATOR
       ========================================================= */
    const heroScroll = document.querySelector('.hero-scroll');
    if (heroScroll) {
        heroScroll.addEventListener('click', () => {
            const nextSection = document.getElementById('libros-destacados');
            if (nextSection) {
                nextSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
        heroScroll.style.cursor = 'pointer';
    }

    /* =========================================================
       5. LEGAL PAGE: TOC from headings
       ========================================================= */
    const tocList = document.getElementById('legal-toc-list');
    const legalMain = document.getElementById('legal-main');

    if (tocList && legalMain) {
        const headings = legalMain.querySelectorAll('h2');

        headings.forEach((h, i) => {
            const id = 'legal-section-' + i;
            h.id = id;

            const li = document.createElement('li');
            li.className = 'legal-toc__item';
            const a = document.createElement('a');
            a.href = '#' + id;
            a.textContent = h.textContent;
            a.addEventListener('click', (e) => {
                e.preventDefault();
                h.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
            li.appendChild(a);
            tocList.appendChild(li);
        });

        // Highlight active TOC item on scroll
        if (headings.length > 0 && 'IntersectionObserver' in window) {
            const tocObserver = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        const id = entry.target.id;
                        const item = tocList.querySelector(`a[href="#${id}"]`)?.parentElement;
                        if (item) {
                            item.classList.toggle('active', entry.isIntersecting);
                        }
                    });
                },
                { rootMargin: '-20% 0px -75% 0px' }
            );

            headings.forEach((h) => tocObserver.observe(h));
        }
    }

    /* =========================================================
       6. CONTACT FORM — native (no plugin)
       ========================================================= */
    const contactForm = document.getElementById('contact-page-form');
    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitBtn = contactForm.querySelector('[type="submit"]');
            const origText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Enviando…';

            const data = new FormData(contactForm);
            data.append('action', 'refugios_contact');

            try {
                const resp = await fetch(window.refugiosData?.ajaxUrl || '/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    body: data,
                });
                const json = await resp.json();

                rfShowNotice(
                    contactForm,
                    json.success
                        ? (json.data?.message || '¡Mensaje enviado! Te responderemos pronto.')
                        : (json.data?.message || 'Hubo un error. Por favor intenta de nuevo.'),
                    json.success ? 'success' : 'error'
                );

                if (json.success) contactForm.reset();
            } catch {
                rfShowNotice(contactForm, 'Error de conexión. Por favor intenta de nuevo.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = origText;
            }
        });
    }

    /* =========================================================
       7. NEWSLETTER FORM
       ========================================================= */
    const newsletterForm = document.getElementById('newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = newsletterForm.querySelector('[name="email"]').value;
            if (!email) return;

            // Show success message (integrate with Mailchimp / MC4WP as needed)
            const wrap = newsletterForm.parentElement;
            const msg = document.createElement('p');
            msg.style.cssText = 'font-family:var(--font-sans); font-size:0.875rem; color:var(--color-amber); margin-top:0.75rem; padding:0.75rem; border:2px solid var(--color-amber);';
            msg.textContent = '¡Gracias! Te has suscrito a La Pausa Semanal.';
            wrap.appendChild(msg);
            newsletterForm.style.opacity = '0.4';
            newsletterForm.style.pointerEvents = 'none';
        });
    }

    /* =========================================================
       8. CART COUNT UPDATE (WooCommerce AJAX)
       ========================================================= */
    function updateCartCount() {
        if (!window.refugiosData?.ajaxUrl) return;

        fetch(window.refugiosData.ajaxUrl + '?action=refugios_cart_count', {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then((r) => r.json())
            .then((json) => {
                if (json.success) {
                    const badge = document.querySelector('.nav-cart-count');
                    const count = json.data.count;
                    if (badge) {
                        badge.textContent = count;
                        badge.style.display = count > 0 ? 'inline-flex' : 'none';
                    }
                }
            })
            .catch(() => { });
    }

    // Update on WooCommerce custom events
    document.body.addEventListener('added_to_cart', updateCartCount);
    document.body.addEventListener('removed_from_cart', updateCartCount);

    /* =========================================================
       9. BOOK CARDS — hover border colour via CSS already
          Add accessible focus ring equivalent
       ========================================================= */
    document.querySelectorAll('.book-card, .blog-card').forEach((card) => {
        const link = card.querySelector('a[href]');
        if (link) {
            link.addEventListener('focus', () => card.classList.add('card-focus'));
            link.addEventListener('blur', () => card.classList.remove('card-focus'));
        }
    });

    /* =========================================================
       10. SMOOTH SCROLL — internal anchor links
       ========================================================= */
    document.querySelectorAll('a[href^="#"]').forEach((a) => {
        a.addEventListener('click', (e) => {
            const target = document.getElementById(a.getAttribute('href').slice(1));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* =========================================================
       HELPER: Show inline notice inside a form
       ========================================================= */
    function rfShowNotice(parent, message, type) {
        const existing = parent.querySelector('.rf-notice');
        if (existing) existing.remove();

        const div = document.createElement('div');
        div.className = 'rf-notice';
        const bg = type === 'success' ? 'rgba(217,160,102,0.12)' : 'rgba(192,57,43,0.08)';
        const color = type === 'success' ? 'var(--color-amber)' : '#c0392b';
        div.style.cssText = `
      margin-top: 1rem;
      padding: 0.875rem 1rem;
      border-left: 4px solid ${color};
      background: ${bg};
      font-family: var(--font-body);
      font-size: 0.9375rem;
      color: ${color};
    `;
        div.textContent = message;
        div.setAttribute('role', 'alert');
        div.setAttribute('aria-live', 'polite');
        parent.appendChild(div);
    }

    /* =========================================================
       11. FAQ ACCORDIONS (Closed by default)
       ========================================================= */
    if (document.body.classList.contains('page-id-14068')) {
        // Elementor uses jQuery heavily. Try plain JS approach on load to override its default.
        window.addEventListener('load', () => {
            setTimeout(() => {
                const activeTitles = document.querySelectorAll('.elementor-accordion .elementor-tab-title.elementor-active');
                const activeContents = document.querySelectorAll('.elementor-accordion .elementor-tab-content.elementor-active');

                activeTitles.forEach(title => {
                    title.classList.remove('elementor-active');
                    title.setAttribute('aria-expanded', 'false');
                });

                activeContents.forEach(content => {
                    content.classList.remove('elementor-active');
                    content.style.display = 'none';
                });
            }, 300); // 300ms retardo para ejecutarse después del init de Elementor
        });
    }

})();
