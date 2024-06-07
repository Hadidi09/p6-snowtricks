document.addEventListener("DOMContentLoaded", () => {
  let button = document.getElementById("show-cards-btn");
  let container = document.getElementById("cards-container");
  let spinner = document.getElementById("spinner");
  let buttonUp = document.getElementById("scroll-to-top-btn");

  if (button && container && spinner) {
    button.addEventListener("click", () => {
      spinner.classList.remove("d-none");
      button.classList.add("d-none");

      setTimeout(() => {
        spinner.classList.add("d-none");
        container.classList.remove("d-none");

        if (container.querySelectorAll(".card").length > 5) {
          buttonUp.style.display = "block";
        }
      }, 1000);
    });

    buttonUp.addEventListener("click", () => {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  let buttons = document.querySelectorAll(".add_item_link");

  buttons.forEach((btn) => {
    btn.addEventListener("click", addFormToCollection);
  });

  function addFormToCollection(e) {
    const collectionHolder = document.querySelector(
      "." + e.currentTarget.dataset.collectionHolderClass
    );
    const item = document.createElement("div");

    item.innerHTML = collectionHolder.dataset.prototype.replace(
      /__name__/g,
      collectionHolder.dataset.index
    );

    const removeButton = document.createElement("button");
    removeButton.type = "button";
    removeButton.classList.add("btn", "btn-danger", "btn-sm", "remove-item");
    removeButton.textContent = "Supprimer";
    removeButton.addEventListener("click", () => item.remove());

    item.appendChild(removeButton);

    collectionHolder.appendChild(item);
    collectionHolder.dataset.index++;
  }
});
