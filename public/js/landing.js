/**
 * Aceh Data Warehouse - Landing Page JavaScript
 * File: public/js/landing.js
 * 
 * Mengelola interaksi sidebar, animasi, dan fitur dashboard
 */

document.addEventListener('DOMContentLoaded', function () {

    // ===================================================
    // 1. SIDEBAR MENU TOGGLE (Expand/Collapse Submenu)
    // ===================================================
    const menuItems = document.querySelectorAll('.sidebar-menu-item');

    menuItems.forEach(function (item) {
        const link = item.querySelector('a');
        const submenu = item.querySelector('.submenu');
        const chevron = item.querySelector('.chevron-icon');

        if (link && submenu) {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                // Tutup semua submenu lain
                menuItems.forEach(function (otherItem) {
                    if (otherItem !== item) {
                        const otherSubmenu = otherItem.querySelector('.submenu');
                        const otherChevron = otherItem.querySelector('.chevron-icon');
                        const otherLink = otherItem.querySelector('a');

                        if (otherSubmenu) {
                            otherSubmenu.style.maxHeight = null;
                            otherSubmenu.style.opacity = '0';
                            otherSubmenu.style.padding = '0';
                            otherSubmenu.style.margin = '0';
                        }
                        if (otherChevron) {
                            otherChevron.classList.remove('bi-chevron-down');
                            otherChevron.classList.add('bi-chevron-right');
                        }
                        if (otherLink) {
                            otherLink.style.backgroundColor = 'transparent';
                            otherLink.style.color = '#333';
                        }
                    }
                });

                // Toggle submenu saat ini
                if (submenu.style.maxHeight && submenu.style.maxHeight !== '0px') {
                    submenu.style.maxHeight = '0px';
                    submenu.style.opacity = '0';
                    submenu.style.padding = '0';
                    submenu.style.margin = '0';
                    if (chevron) {
                        chevron.classList.remove('bi-chevron-down');
                        chevron.classList.add('bi-chevron-right');
                    }
                    link.style.backgroundColor = 'transparent';
                    link.style.color = '#333';
                } else {
                    submenu.style.maxHeight = submenu.scrollHeight + 'px';
                    submenu.style.opacity = '1';
                    submenu.style.padding = '8px 0';
                    submenu.style.margin = '4px 0';
                    if (chevron) {
                        chevron.classList.remove('bi-chevron-right');
                        chevron.classList.add('bi-chevron-down');
                    }
                    link.style.backgroundColor = '#e8f5f0';
                    link.style.color = '#0d9488';
                }
            });
        }
    });

    // ===================================================
    // 2. SUBMENU LINK ACTIVE STATE
    // ===================================================
    const submenuLinks = document.querySelectorAll('.submenu a');

    submenuLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {

            // Hapus active state dari semua submenu link
            submenuLinks.forEach(function (l) {
                l.style.fontWeight = '400';
                l.style.color = '#555';
                l.style.backgroundColor = 'transparent';
                l.style.borderLeft = 'none';
                l.style.paddingLeft = '8px';
            });

            // Tambahkan active state ke link yang diklik
            this.style.fontWeight = '600';
            this.style.color = '#0d9488';
            this.style.backgroundColor = '#e8f5f0';
            this.style.borderLeft = '3px solid #0d9488';
            this.style.paddingLeft = '5px';
        });
    });

    // ===================================================
    // 3. TOP NAV ACTIVE STATE
    // ===================================================
    const navLinks = document.querySelectorAll('.top-nav-link');

    navLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            navLinks.forEach(function (l) {
                l.style.fontWeight = '500';
                l.style.color = '#666';
                l.style.borderBottom = 'none';
                l.style.paddingBottom = '0';
            });

            this.style.fontWeight = '700';
            this.style.color = '#1a1a2e';
            this.style.borderBottom = '2px solid #1a1a2e';
            this.style.paddingBottom = '2px';
        });
    });

    // ===================================================
    // 4. HERO SECTION ANIMATION ON LOAD
    // ===================================================
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        heroSection.style.opacity = '0';
        heroSection.style.transform = 'translateY(20px)';
        heroSection.style.transition = 'opacity 0.8s ease, transform 0.8s ease';

        setTimeout(function () {
            heroSection.style.opacity = '1';
            heroSection.style.transform = 'translateY(0)';
        }, 100);
    }

    // ===================================================
    // 5. SEKTOR UTAMA CARDS - STAGGERED ANIMATION
    // ===================================================
    const sektorCards = document.querySelectorAll('.sektor-card');

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const cardObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry, index) {
            if (entry.isIntersecting) {
                setTimeout(function () {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
                cardObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    sektorCards.forEach(function (card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        cardObserver.observe(card);
    });

    // ===================================================
    // 6. CARD HOVER EFFECT
    // ===================================================
    sektorCards.forEach(function (card) {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.1)';
            this.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.05)';
        });
    });

    // ===================================================
    // 7. BUTTON CLICK EFFECTS
    // ===================================================
    const buttons = document.querySelectorAll('.btn-cta');

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            // Ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            setTimeout(function () {
                ripple.remove();
            }, 600);
        });
    });

    // Tambahkan keyframe ripple via JS
    const styleSheet = document.createElement('style');
    styleSheet.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(styleSheet);

    // ===================================================
    // 8. DECORATIVE GRAPHIC ANIMATION (Floating Dots)
    // ===================================================
    const dots = document.querySelectorAll('.graphic-dot');

    dots.forEach(function (dot, index) {
        const duration = 2000 + Math.random() * 3000;
        const delay = Math.random() * 2000;

        dot.style.animation = `floatDot ${duration}ms ease-in-out ${delay}ms infinite alternate`;
    });

    const floatStyle = document.createElement('style');
    floatStyle.textContent = `
        @keyframes floatDot {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 0.4;
            }
            50% {
                opacity: 0.8;
            }
            100% {
                transform: translate(${Math.random() * 10 - 5}px, ${Math.random() * 10 - 5}px) scale(1.3);
                opacity: 0.4;
            }
        }
    `;
    document.head.appendChild(floatStyle);

    // ===================================================
    // 9. SCROLL TO TOP BUTTON (Optional)
    // ===================================================
    const scrollTopBtn = document.createElement('button');
    scrollTopBtn.innerHTML = '<i class="bi bi-arrow-up"></i>';
    scrollTopBtn.setAttribute('aria-label', 'Scroll to top');
    Object.assign(scrollTopBtn.style, {
        position: 'fixed',
        bottom: '30px',
        right: '30px',
        width: '45px',
        height: '45px',
        borderRadius: '50%',
        backgroundColor: '#1a1a2e',
        color: '#ffffff',
        border: 'none',
        fontSize: '18px',
        cursor: 'pointer',
        display: 'none',
        alignItems: 'center',
        justifyContent: 'center',
        boxShadow: '0 4px 15px rgba(0, 0, 0, 0.2)',
        zIndex: '9999',
        transition: 'background-color 0.3s ease, transform 0.3s ease'
    });

    document.body.appendChild(scrollTopBtn);

    window.addEventListener('scroll', function () {
        if (window.scrollY > 300) {
            scrollTopBtn.style.display = 'flex';
        } else {
            scrollTopBtn.style.display = 'none';
        }
    });

    scrollTopBtn.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    scrollTopBtn.addEventListener('mouseenter', function () {
        this.style.backgroundColor = '#0d9488';
        this.style.transform = 'scale(1.1)';
    });

    scrollTopBtn.addEventListener('mouseleave', function () {
        this.style.backgroundColor = '#1a1a2e';
        this.style.transform = 'scale(1)';
    });

    // ===================================================
    // 10. SIDEBAR TOGGLE FOR MOBILE (Responsive)
    // ===================================================
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    // Buat toggle button untuk mobile
    const mobileToggle = document.createElement('button');
    mobileToggle.innerHTML = '<i class="bi bi-list"></i>';
    mobileToggle.setAttribute('aria-label', 'Toggle sidebar');
    Object.assign(mobileToggle.style, {
        display: 'none',
        position: 'fixed',
        top: '15px',
        left: '15px',
        zIndex: '10001',
        width: '40px',
        height: '40px',
        borderRadius: '8px',
        backgroundColor: '#1a1a2e',
        color: '#ffffff',
        border: 'none',
        fontSize: '20px',
        cursor: 'pointer',
        alignItems: 'center',
        justifyContent: 'center'
    });

    document.body.appendChild(mobileToggle);

    // Overlay untuk mobile
    const overlay = document.createElement('div');
    Object.assign(overlay.style, {
        display: 'none',
        position: 'fixed',
        top: '0',
        left: '0',
        width: '100%',
        height: '100%',
        backgroundColor: 'rgba(0, 0, 0, 0.5)',
        zIndex: '999',
        opacity: '0',
        transition: 'opacity 0.3s ease'
    });

    document.body.appendChild(overlay);

    function handleResize() {
        if (window.innerWidth <= 768) {
            mobileToggle.style.display = 'flex';

            if (sidebar) {
                sidebar.style.transform = 'translateX(-100%)';
                sidebar.style.transition = 'transform 0.3s ease';
            }
            if (mainContent) {
                mainContent.style.marginLeft = '0';
            }
        } else {
            mobileToggle.style.display = 'none';
            overlay.style.display = 'none';

            if (sidebar) {
                sidebar.style.transform = 'translateX(0)';
            }
            if (mainContent) {
                mainContent.style.marginLeft = '260px';
            }
        }
    }

    mobileToggle.addEventListener('click', function () {
        if (sidebar.style.transform === 'translateX(0px)' || sidebar.style.transform === '') {
            sidebar.style.transform = 'translateX(-100%)';
            overlay.style.display = 'none';
        } else {
            sidebar.style.transform = 'translateX(0)';
            overlay.style.display = 'block';
            setTimeout(function () {
                overlay.style.opacity = '1';
            }, 10);
        }
    });

    overlay.addEventListener('click', function () {
        sidebar.style.transform = 'translateX(-100%)';
        overlay.style.opacity = '0';
        setTimeout(function () {
            overlay.style.display = 'none';
        }, 300);
    });

    window.addEventListener('resize', handleResize);
    handleResize(); // Panggil saat load

    // ===================================================
    // 11. TOOLTIP UNTUK IKON NAVBAR
    // ===================================================
    const navIcons = document.querySelectorAll('.nav-icon');

    navIcons.forEach(function (icon) {
        icon.style.position = 'relative';
        icon.style.cursor = 'pointer';

        icon.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.2)';
            this.style.transition = 'transform 0.2s ease';
        });

        icon.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
        });
    });

    // ===================================================
    // 12. COUNTER ANIMATION (Untuk Statistik di Masa Depan)
    // ===================================================
    function animateCounter(element, target, duration) {
        let start = 0;
        const increment = target / (duration / 16);
        const isDecimal = target % 1 !== 0;

        function updateCounter() {
            start += increment;
            if (start < target) {
                element.textContent = isDecimal ? start.toFixed(1) : Math.floor(start).toLocaleString('id-ID');
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = isDecimal ? target.toFixed(1) : target.toLocaleString('id-ID');
            }
        }

        updateCounter();
    }

    // Expose ke global scope jika dibutuhkan di blade
    window.AcehDataWarehouse = {
        animateCounter: animateCounter,
        openSubmenu: function (index) {
            const items = document.querySelectorAll('.sidebar-menu-item');
            if (items[index]) {
                const link = items[index].querySelector('a');
                if (link) link.click();
            }
        }
    };

    // ===================================================
    // 13. LOG INIT
    // ===================================================
    console.log('%c Aceh Data Warehouse ', 'background: #0d9488; color: #fff; font-size: 14px; padding: 5px 10px; border-radius: 4px;');
    console.log('%c Landing page initialized successfully. ', 'color: #0d9488;');

    // ===================================================
    // 14. AKSES MODUL LINK HOVER EFFECT
    // ===================================================
    const aksesLinks = document.querySelectorAll('a[href="#"][style*="color: #0d9488"]');

    aksesLinks.forEach(function (link) {
        link.style.transition = 'color 0.2s ease, transform 0.2s ease';
        link.style.padding = '4px 0';

        link.addEventListener('mouseenter', function () {
            this.style.color = '#0a7a6f';
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'translateX(4px)';
                icon.style.transition = 'transform 0.2s ease';
            }
        });

        link.addEventListener('mouseleave', function () {
            this.style.color = '#0d9488';
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'translateX(0)';
            }
        });
    });

});