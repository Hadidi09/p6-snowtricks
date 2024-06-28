document.addEventListener("turbo:load", () => {
  let removeButton = document.querySelectorAll(".remove-image");

  removeButton.forEach((button) => {
    button.addEventListener("mousedown", function () {
      if (confirm("Voulez-vous vraiment supprimer cette image ?")) {
        const url = this.dataset.url;

        fetch(url, {
          method: "DELETE",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Content-Type": "application/json",
          },
        })
          .then((response) => {
            if (response.ok) {
              return response.json();
            } else {
              throw new Error("Erreur lors de la suppression de l'image");
            }
          })
          .then((data) => {
            console.log(data);
            this.closest(".d-flex").remove();
            flashMessage("Image supprimée !");
          })
          .catch((error) => console.error("Error:", error));
      }
    });
  });

  let removeVideo = document.querySelectorAll(".remove-video");

  removeVideo.forEach((button) => {
    button.addEventListener("mousedown", function () {
      if (confirm("Voulez-vous vraiment supprimer cette vidéo ?")) {
        const url = this.dataset.url;

        fetch(url, {
          method: "DELETE",
          headers: {
            "X-Requested-with": "XMLHttpRequest",
            "Content-Type": "application/json",
          },
        })
          .then((response) => {
            if (response.ok) {
              return response.json();
            } else {
              throw new Error("Erreur lors de la suppression de la vidéo");
            }
          })
          .then((data) => {
            console.log(data);
            this.closest(".d-flex").remove();
            flashMessage("video supprimée");
          })
          .catch((error) => console.error("Error:", error));
      }
    });
  });
  let removeFigure = document.querySelectorAll(".remove-figure");

  removeFigure.forEach((button) => {
    button.addEventListener("click", function () {
      if (confirm("Voulez-vous vraiment supprimer cette figure ?")) {
        const url = this.dataset.url;

        fetch(url, {
          method: "DELETE",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Content-Type": "application/json",
          },
        })
          .then((response) => {
            if (response.ok) {
              return response.json();
            } else {
              throw new Error("Erreur lors de la suppression de la figure");
            }
          })
          .then((data) => {
            console.log(data);
            this.closest(".card").remove(); // Assurez-vous que .my-2 est le bon parent à supprimer
            flashMessage("Figure supprimée");
          })
          .catch((error) => console.error("Error:", error));
      }
    });
  });

  function flashMessage($message) {
    return alert($message);
  }
});
