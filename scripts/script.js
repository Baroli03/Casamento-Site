console.log("Script carregado!");

const elementos = document.querySelectorAll('.aparecer');

const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('active');
    }
  });
}, {
  threshold: 0.1
});

elementos.forEach(el => {
  observer.observe(el);
});
