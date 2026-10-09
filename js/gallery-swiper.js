// Duplicate marquee items for seamless infinite scroll
document.querySelectorAll('.gallery-marquee-track').forEach(function(track) {
  var items = track.innerHTML;
  track.innerHTML = items + items;
});

let galleryGridOpen = false;

function toggleGalleryGrid() {
  const grid = document.getElementById('gallery-grid');
  const btn = document.getElementById('gallery-toggle-btn');
  const btnSpan = btn.querySelector('span');
  const marquee = document.querySelector('.gallery-marquee-wrapper');
  const lang = typeof currentLang !== 'undefined' ? currentLang : 'en';

  if (!galleryGridOpen) {
    grid.style.display = 'flex';
    grid.style.flexWrap = 'wrap';
    marquee.style.display = 'none';
    if (lang === 'pt') {
      btnSpan.textContent = 'Recolher';
    } else {
      btnSpan.textContent = 'Collapse';
    }
    galleryGridOpen = true;
  } else {
    grid.style.display = 'none';
    marquee.style.display = 'block';
    if (lang === 'pt') {
      btnSpan.textContent = 'Ver Todas as Fotos';
    } else {
      btnSpan.textContent = 'See All Photos';
    }
    galleryGridOpen = false;
  }
}
