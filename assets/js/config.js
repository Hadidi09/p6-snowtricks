document.addEventListener("DOMContentLoaded", function () {
  let button = document.getElementById("show-cards-btn");
  let container = document.getElementById("cards-container");
  let spinner = document.getElementById("spinner");
  let buttonUp = document.getElementById("scroll-to-top-btn");

  if (button && container && spinner) {
    button.addEventListener("click", function () {
      spinner.classList.remove("d-none");
      button.classList.add("d-none");

      setTimeout(function () {
        spinner.classList.add("d-none");
        container.classList.remove("d-none");

        if (container.querySelectorAll(".card").length > 5) {
          buttonUp.style.display = "block";
        }
      }, 1000);
    });

    buttonUp.addEventListener("click", function () {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }
});
