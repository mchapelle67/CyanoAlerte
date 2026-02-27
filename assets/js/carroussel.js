document.addEventListener('DOMContentLoaded', function () {
    const thumbnails = Array.from(document.querySelectorAll('[data-photo-thumb]'));
    const lightbox = document.getElementById('photo-lightbox');

    if (!thumbnails.length || !lightbox) {
        return;
    }

    const image = document.getElementById('photo-lightbox-image');
    const closeBtn = document.getElementById('photo-lightbox-close');
    const prevBtn = document.getElementById('photo-lightbox-prev');
    const nextBtn = document.getElementById('photo-lightbox-next');
    const counter = document.getElementById('photo-lightbox-counter');
    const photos = thumbnails.map((thumb) => ({
        src: thumb.dataset.photoSrc,
        alt: thumb.dataset.photoAlt || ''
    }));

    let currentIndex = 0;

    function renderPhoto(index) {
        currentIndex = (index + photos.length) % photos.length;
        image.src = photos[currentIndex].src;
        image.alt = photos[currentIndex].alt;
        counter.textContent = `${currentIndex + 1} / ${photos.length}`;

        const hasMultiple = photos.length > 1;
        prevBtn.classList.toggle('hidden', !hasMultiple);
        nextBtn.classList.toggle('hidden', !hasMultiple);
    }

    function openLightbox(index) {
        renderPhoto(index);
        lightbox.classList.remove('hidden');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }

    function closeLightbox() {
        lightbox.classList.add('hidden');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    }

    thumbnails.forEach((thumb, index) => {
        thumb.addEventListener('click', () => openLightbox(index));
    });

    closeBtn.addEventListener('click', closeLightbox);
    prevBtn.addEventListener('click', () => renderPhoto(currentIndex - 1));
    nextBtn.addEventListener('click', () => renderPhoto(currentIndex + 1));

    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (lightbox.classList.contains('hidden')) {
            return;
        }

        if (event.key === 'Escape') {
            closeLightbox();
        }

        if (event.key === 'ArrowLeft') {
            renderPhoto(currentIndex - 1);
        }

        if (event.key === 'ArrowRight') {
            renderPhoto(currentIndex + 1);
        }
    });
});
