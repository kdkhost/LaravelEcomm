(function () {
    const THEME_KEY = 'admin-theme-mode';

    function convertLegacyBootstrapAttributes() {
        document.querySelectorAll('[data-toggle]').forEach((element) => {
            if (!element.getAttribute('data-bs-toggle')) {
                element.setAttribute('data-bs-toggle', element.getAttribute('data-toggle'));
            }
        });

        document.querySelectorAll('[data-target]').forEach((element) => {
            if (!element.getAttribute('data-bs-target')) {
                element.setAttribute('data-bs-target', element.getAttribute('data-target'));
            }
        });

        document.querySelectorAll('[data-dismiss]').forEach((element) => {
            if (!element.getAttribute('data-bs-dismiss')) {
                element.setAttribute('data-bs-dismiss', element.getAttribute('data-dismiss'));
            }
        });

        document.querySelectorAll('[data-parent]').forEach((element) => {
            if (!element.getAttribute('data-bs-parent')) {
                element.setAttribute('data-bs-parent', element.getAttribute('data-parent'));
            }
        });
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem(THEME_KEY, theme);

        const toggle = document.getElementById('adminThemeToggle');
        if (!toggle) {
            return;
        }

        const icon = toggle.querySelector('i');
        if (icon) {
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }

        toggle.setAttribute('title', theme === 'dark' ? 'Usar tema claro' : 'Usar tema escuro');
        toggle.setAttribute('aria-label', theme === 'dark' ? 'Usar tema claro' : 'Usar tema escuro');
    }

    function initThemeToggle() {
        const storedTheme = localStorage.getItem(THEME_KEY);
        const currentTheme = storedTheme || document.documentElement.getAttribute('data-bs-theme') || 'light';
        applyTheme(currentTheme);

        const toggle = document.getElementById('adminThemeToggle');
        if (!toggle) {
            return;
        }

        toggle.addEventListener('click', function () {
            const activeTheme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
            applyTheme(activeTheme === 'dark' ? 'light' : 'dark');
        });
    }

    function createPreviewCard(file, isExisting) {
        const card = document.createElement('div');
        card.className = 'admin-upload-card' + (isExisting ? ' is-existing' : '');

        const media = document.createElement('div');
        media.className = 'admin-upload-card-media';

        const meta = document.createElement('div');
        meta.className = 'admin-upload-card-meta';

        const name = document.createElement('span');
        name.className = 'admin-upload-card-name';
        name.title = file.name || 'Arquivo';
        name.textContent = file.name || 'Arquivo';

        const helper = document.createElement('small');
        helper.textContent = isExisting ? 'Arquivo atual' : 'Novo arquivo';

        if (file.type && file.type.startsWith('image/')) {
            const image = document.createElement('img');
            image.alt = file.name || 'Preview';
            image.src = file.url || '';
            media.appendChild(image);
        } else {
            const icon = document.createElement('div');
            icon.className = 'admin-upload-file-icon';
            icon.innerHTML = '<i class="fas fa-file-alt"></i>';
            media.appendChild(icon);
        }

        meta.appendChild(name);
        meta.appendChild(helper);
        card.appendChild(media);
        card.appendChild(meta);

        return card;
    }

    function rebuildPreview(container, input, existingCards) {
        const preview = container.querySelector('.js-admin-upload-preview');
        if (!preview) {
            return;
        }

        preview.innerHTML = '';

        existingCards.forEach((existing) => preview.appendChild(existing.cloneNode(true)));

        Array.from(input.files || []).forEach((file) => {
            const reader = new FileReader();
            reader.onload = function (event) {
                const card = createPreviewCard({
                    name: file.name,
                    type: file.type,
                    url: event.target.result,
                }, false);
                preview.appendChild(card);
            };

            if (file.type && file.type.startsWith('image/')) {
                reader.readAsDataURL(file);
            } else {
                const card = createPreviewCard({
                    name: file.name,
                    type: file.type,
                    url: '',
                }, false);
                preview.appendChild(card);
            }
        });
    }

    function mergeFiles(input, incomingFiles, multiple) {
        const transfer = new DataTransfer();

        if (multiple) {
            Array.from(input.files || []).forEach((file) => transfer.items.add(file));
            Array.from(incomingFiles || []).forEach((file) => transfer.items.add(file));
        } else if (incomingFiles && incomingFiles.length) {
            transfer.items.add(incomingFiles[0]);
        }

        input.files = transfer.files;
    }

    function initDropzones() {
        document.querySelectorAll('.js-admin-dropzone').forEach((container) => {
            const input = container.querySelector('.admin-upload-input');
            const preview = container.querySelector('.js-admin-upload-preview');
            if (!input || !preview) {
                return;
            }

            const multiple = container.dataset.multiple === '1';
            const existingCards = Array.from(preview.children);

            input.addEventListener('change', function () {
                rebuildPreview(container, input, existingCards);
            });

            ['dragenter', 'dragover'].forEach((eventName) => {
                container.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    container.classList.add('is-dragover');
                });
            });

            ['dragleave', 'dragend', 'drop'].forEach((eventName) => {
                container.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (eventName !== 'drop') {
                        container.classList.remove('is-dragover');
                    }
                });
            });

            container.addEventListener('drop', function (event) {
                container.classList.remove('is-dragover');
                const files = event.dataTransfer ? event.dataTransfer.files : null;
                if (!files || !files.length) {
                    return;
                }

                mergeFiles(input, files, multiple);
                rebuildPreview(container, input, existingCards);
            });
        });
    }

    function initScrollTop() {
        const button = document.querySelector('.scroll-to-top');
        const scroller = document.getElementById('adminContentScroll') || window;
        if (!button) {
            return;
        }

        const syncVisibility = function () {
            const top = scroller === window ? window.scrollY : scroller.scrollTop;
            button.style.display = top > 240 ? 'inline-flex' : 'none';
        };

        syncVisibility();
        scroller.addEventListener('scroll', syncVisibility, { passive: true });

        button.addEventListener('click', function (event) {
            event.preventDefault();

            if (scroller === window) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            scroller.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    function initSidebarState() {
        const currentPath = window.location.pathname.replace(/\/+$/, '');
        const links = document.querySelectorAll('.sidebar-menu a[href]');

        links.forEach((link) => {
            const href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:')) {
                return;
            }

            let url;

            try {
                url = new URL(href, window.location.origin);
            } catch (error) {
                return;
            }

            const linkPath = url.pathname.replace(/\/+$/, '');
            const isSamePath = linkPath && (linkPath === currentPath || (linkPath !== '/admin' && currentPath.startsWith(linkPath + '/')));

            if (!isSamePath) {
                return;
            }

            if (link.classList.contains('collapse-item')) {
                link.classList.add('active');
            } else {
                link.classList.add('active');
                const navItem = link.closest('.nav-item');
                if (navItem) {
                    navItem.classList.add('active', 'menu-open');
                }
            }

            const collapse = link.closest('.collapse');
            if (collapse) {
                collapse.classList.add('show');

                const triggerId = collapse.getAttribute('id');
                if (triggerId) {
                    const trigger = document.querySelector('[data-bs-target="#' + triggerId + '"], [data-target="#' + triggerId + '"]');
                    if (trigger) {
                        trigger.classList.add('active');
                        trigger.setAttribute('aria-expanded', 'true');
                        const parentItem = trigger.closest('.nav-item');
                        if (parentItem) {
                            parentItem.classList.add('active', 'menu-open');
                        }
                    }
                }
            }
        });
    }

    window.swal = function (arg1, arg2, arg3) {
        if (typeof arg1 === 'string') {
            return Swal.fire({
                title: arg1,
                text: typeof arg2 === 'string' ? arg2 : '',
                icon: typeof arg3 === 'string' ? arg3 : 'info',
            });
        }

        return Swal.fire(arg1 || {});
    };

    document.addEventListener('DOMContentLoaded', function () {
        convertLegacyBootstrapAttributes();
        initThemeToggle();
        initDropzones();
        initSidebarState();
        initScrollTop();
    });
})();
