window.addEventListener("DOMContentLoaded", () => {
  fetch("components/navbar.html")
    .then(res => res.text())
    .then(data => {
      document.getElementById("navbar").innerHTML = data;
    });

  fetch("components/sidebar.html")
    .then(res => res.text())
    .then(data => {
      document.getElementById("sidebar").innerHTML = data;
    });
});