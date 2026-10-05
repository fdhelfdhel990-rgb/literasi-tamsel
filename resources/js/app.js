const query = (selector, root = document) => root.querySelector(selector);
const queryAll = (selector, root = document) => [...root.querySelectorAll(selector)];

const toast = (message) => {
    const element = query('#siteToast');
    if (!element) return;
    element.textContent = message;
    element.classList.add('visible');
    window.clearTimeout(window.__toastTimer);
    window.__toastTimer = window.setTimeout(() => element.classList.remove('visible'), 3200);
};

const header = query('[data-menu-shell]');
query('[data-menu-toggle]')?.addEventListener('click', (event) => {
    const open = header.classList.toggle('menu-open');
    event.currentTarget.setAttribute('aria-expanded', String(open));
});

query('#adminMenuBtn')?.addEventListener('click', () => document.body.classList.toggle('admin-menu-open'));

const copyText = async (text) => {
    try {
        if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(text);
        } else {
            const input = document.createElement('textarea');
            input.value = text;
            input.setAttribute('readonly', '');
            input.style.position = 'fixed';
            input.style.opacity = '0';
            document.body.append(input);
            input.select();
            const copied = document.execCommand('copy');
            input.remove();
            if (!copied) throw new Error('Clipboard unavailable');
        }
        toast('Tautan berhasil disalin.');
        return true;
    } catch {
        toast('Tidak berhasil menyalin tautan.');
        return false;
    }
};

queryAll('[data-copy-url]').forEach((button) => button.addEventListener('click', () => copyText(location.href)));
queryAll('[data-native-share]').forEach((button) => button.addEventListener('click', async () => {
    const title = button.dataset.shareTitle || document.title;
    try {
        if (navigator.share) await navigator.share({ title, text: title, url: location.href });
        else await copyText(location.href);
    } catch (error) {
        if (error.name !== 'AbortError') toast('Tautan tidak dapat dibagikan.');
    }
}));

const createPagination = (items, grid, pager, pageSize) => {
    let page = 1;
    let filtered = items;

    const render = () => {
        const maxPage = Math.max(1, Math.ceil(filtered.length / pageSize));
        page = Math.min(page, maxPage);
        items.forEach((item) => { item.hidden = true; });
        filtered.slice((page - 1) * pageSize, page * pageSize).forEach((item) => { item.hidden = false; });
        pager.replaceChildren();
        if (maxPage < 2) return;

        for (let number = 1; number <= maxPage; number += 1) {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = String(number);
            button.className = number === page ? 'active' : '';
            button.setAttribute('aria-label', `Halaman ${number}`);
            button.setAttribute('aria-current', number === page ? 'page' : 'false');
            button.addEventListener('click', () => {
                page = number;
                render();
                grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
            pager.append(button);
        }
    };

    return {
        update(nextItems) {
            filtered = nextItems;
            page = 1;
            render();
        },
    };
};

const library = query('[data-library]');
if (library) {
    const items = queryAll('.filter-book', library);
    const grid = query('#bookGrid');
    const pager = query('#bookPagination');
    const pagination = createPagination(items, grid, pager, 6);
    const applyFilters = () => {
        const term = query('#bookSearch').value.trim().toLocaleLowerCase('id');
        const genre = query('input[name="genre"]:checked')?.value;
        const publisher = query('#publisherFilter').value;
        let matches = items.filter((item) => (!term || item.dataset.title.includes(term))
            && (!genre || genre === 'Semua' || item.dataset.genre === genre)
            && (!publisher || item.dataset.publisher === publisher));
        const sort = query('#bookSort').value;
        if (sort !== 'default') matches = [...matches].sort((a, b) => a.dataset.title.localeCompare(b.dataset.title, 'id') * (sort === 'az' ? 1 : -1));
        grid.replaceChildren(...matches, ...items.filter((item) => !matches.includes(item)));
        query('#bookCount').textContent = String(matches.length);
        query('#bookEmpty').hidden = matches.length > 0;
        pagination.update(matches);
    };

    query('#bookSearch').addEventListener('input', applyFilters);
    queryAll('input[name="genre"]').forEach((input) => input.addEventListener('change', applyFilters));
    query('#publisherFilter').addEventListener('change', applyFilters);
    query('#bookSort').addEventListener('change', applyFilters);
    query('#resetFilters').addEventListener('click', () => {
        query('#bookSearch').value = '';
        query('input[name="genre"][value="Semua"]').checked = true;
        query('#publisherFilter').value = '';
        query('#bookSort').value = 'default';
        applyFilters();
    });
    applyFilters();
}

const posts = query('[data-posts]');
if (posts) {
    const items = queryAll('.filter-post', posts);
    const grid = query('#postGrid');
    const pagination = createPagination(items, grid, query('#postPagination'), 6);
    const applyFilters = () => {
        const term = query('#postQuery').value.trim().toLocaleLowerCase('id');
        const type = query('#postType').value;
        const matches = items.filter((item) => (!term || item.dataset.title.includes(term)) && (!type || item.dataset.type === type));
        query('#postCount').textContent = String(matches.length);
        query('#postEmpty').hidden = matches.length > 0;
        pagination.update(matches);
    };
    query('#postSearch').addEventListener('submit', (event) => { event.preventDefault(); applyFilters(); });
    query('#postQuery').addEventListener('input', applyFilters);
    query('#postType').addEventListener('change', applyFilters);
    applyFilters();
}

const motionAllowed = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const counterObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(({ target, isIntersecting }) => {
        if (!isIntersecting) return;
        observer.unobserve(target);
        const end = Number(target.dataset.count) || 0;
        const prefix = target.dataset.prefix || '';
        const suffix = target.dataset.suffix || '';
        if (!motionAllowed) {
            target.textContent = `${prefix}${end}${suffix}`;
            return;
        }
        const start = performance.now();
        const duration = 1450;
        const tick = (time) => {
            const progress = Math.min(1, (time - start) / duration);
            const eased = 1 - (1 - progress) ** 3;
            target.textContent = `${prefix}${Math.round(end * eased)}${suffix}`;
            if (progress < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    });
}, { threshold: 0.3 });
queryAll('[data-count]').forEach((element) => {
    const end = Number(element.dataset.count) || 0;
    const prefix = element.dataset.prefix || '';
    const suffix = element.dataset.suffix || '';
    if (motionAllowed) {
        element.textContent = `${prefix}0${suffix}`;
        counterObserver.observe(element);
    } else {
        element.textContent = `${prefix}${end}${suffix}`;
    }
});

const carousel = query('[data-partner-carousel]');
if (carousel) {
    const viewport = query('.partner-viewport', carousel);
    const track = query('.partner-strip', carousel);
    const originals = [...track.children];
    let paused = false;
    let offset = 0;
    let lastTime = 0;
    let cycleWidth = 0;
    let frame = null;
    const speed = 0.025;

    const setCloneTabOrder = (clone) => {
        if (!(clone instanceof HTMLElement)) return;
        clone.tabIndex = -1;
        queryAll('a, button, input, select, textarea, [tabindex]', clone).forEach((element) => {
            element.setAttribute('tabindex', '-1');
        });
    };

    const rebuild = () => {
        queryAll('[data-partner-clone]', track).forEach((node) => node.remove());
        track.style.transform = 'translate3d(0,0,0)';
        offset = 0;
        cycleWidth = track.scrollWidth;

        if (originals.length <= 1) return;

        const minimumTrackWidth = Math.max(viewport.clientWidth * 2, viewport.clientWidth + cycleWidth);
        while (track.scrollWidth < minimumTrackWidth) {
            originals.forEach((node) => {
                const clone = node.cloneNode(true);
                clone.dataset.partnerClone = 'true';
                clone.setAttribute('aria-hidden', 'true');
                setCloneTabOrder(clone);
                track.append(clone);
            });
        }
    };

    if (originals.length <= 1) {
        carousel.classList.add('is-static');
    } else {
        rebuild();
        window.addEventListener('resize', rebuild);
    }

    if (motionAllowed && originals.length > 1) {
        const animate = (time) => {
            if (!lastTime) lastTime = time;
            const delta = time - lastTime;
            lastTime = time;

            if (!paused && cycleWidth > 0) {
                offset = (offset + delta * speed) % cycleWidth;
                track.style.transform = `translate3d(${-offset}px,0,0)`;
            }

            frame = requestAnimationFrame(animate);
        };
        frame = requestAnimationFrame(animate);
    }

    const resume = () => { paused = false; };
    carousel.addEventListener('mouseenter', () => { paused = true; });
    carousel.addEventListener('mouseleave', resume);
    carousel.addEventListener('focusin', () => { paused = true; });
    carousel.addEventListener('focusout', resume);
    carousel.addEventListener('pointerdown', () => { paused = true; });
    carousel.addEventListener('pointerup', () => window.setTimeout(resume, 900));
    query('[data-partner-prev]', carousel)?.addEventListener('click', () => {
        if (!cycleWidth || originals.length <= 1) return;
        offset = (offset - 230 + cycleWidth) % cycleWidth;
        track.style.transform = `translate3d(${-offset}px,0,0)`;
    });
    query('[data-partner-next]', carousel)?.addEventListener('click', () => {
        if (!cycleWidth || originals.length <= 1) return;
        offset = (offset + 230) % cycleWidth;
        track.style.transform = `translate3d(${-offset}px,0,0)`;
    });

    window.addEventListener('beforeunload', () => {
        if (frame) cancelAnimationFrame(frame);
    });
}

const relatedSearch = query('#relatedSearch');
relatedSearch?.addEventListener('input', () => {
    const term = relatedSearch.value.toLocaleLowerCase('id');
    let visible = 0;
    queryAll('[data-related-post]').forEach((item) => {
        item.hidden = !item.textContent.toLocaleLowerCase('id').includes(term);
        if (!item.hidden) visible += 1;
    });
    const empty = query('#relatedEmpty');
    if (empty) empty.hidden = visible > 0;
});

queryAll('[data-image-input]').forEach((input) => input.addEventListener('change', () => {
    // Scope the preview to this form in case more than one upload exists.
    const form = input.closest('form') || document;
    const preview = query('[data-image-preview]', form);
    const wrapper = preview?.closest('.image-preview-wrap');
    const file = input.files?.[0];

    if (!preview || !wrapper || !file) return;

    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        input.value = '';
        toast('Pilih gambar JPG, PNG, atau WebP.');
        return;
    }

    if (preview.dataset.objectUrl) URL.revokeObjectURL(preview.dataset.objectUrl);

    const objectUrl = URL.createObjectURL(file);
    preview.dataset.objectUrl = objectUrl;
    preview.onload = () => URL.revokeObjectURL(objectUrl);
    preview.src = objectUrl;
    preview.hidden = false;
    wrapper.hidden = false;
}));
const roleSelect = query('#adminRole');
const permissionFields = query('[data-permission-fields]');
const syncPermissionFields = () => {
    if (!roleSelect || !permissionFields) return;
    const enabled = roleSelect.value === 'sub_admin';
    permissionFields.hidden = !enabled;
    queryAll('input[type="checkbox"]', permissionFields).forEach((input) => { input.disabled = !enabled; });
};
roleSelect?.addEventListener('change', syncPermissionFields);
syncPermissionFields();

document.addEventListener('error', (event) => {
    const image = event.target;
    if (!(image instanceof HTMLImageElement) || !image.matches('[data-image-fallback]')) return;
    const fallback = document.createElement('span');
    fallback.className = image.dataset.fallbackClass || 'image-fallback';
    fallback.textContent = image.dataset.fallbackText || image.alt || 'Gambar tidak tersedia';
    fallback.setAttribute('role', 'img');
    fallback.setAttribute('aria-label', fallback.textContent);
    image.replaceWith(fallback);
}, true);
