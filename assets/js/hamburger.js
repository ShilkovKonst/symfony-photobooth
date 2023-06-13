import '../styles/hamburger.css';

const hamburger = document.getElementById("collapse_toggler_header");
hamburger.addEventListener("click", function () {
  hamburger.classList.toggle("is-active");
});