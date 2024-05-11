import "./bootstrap.js";
import "./styles/app.scss";

require("bootstrap");
require("jquery");

if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
  // Prompt the user for camera access
  navigator.mediaDevices
    .getUserMedia({ video: true })
    .then(function (stream) {
      // User granted access to camera
      console.log("Camera access granted");
    })
    .catch(function (error) {
      // User denied access to camera or an error occurred
      console.error("Camera access denied:", error);
    });
} else {
  console.error("getUserMedia is not supported");
}

document.addEventListener("DOMContentLoaded", function () {
  const isAddArticleForm = document.querySelector(".add-article-form");

  if (isAddArticleForm) {
    document
      .getElementById("photo")
      .addEventListener("change", function (event) {
        var input = event.target;
        if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function () {
            var dataURL = reader.result;
            var preview = document.getElementById("preview");
            preview.src = dataURL;
            preview.style.display = "block";
            document.querySelector(".plus-sign").style.display = "none";
            document.querySelector(".photo-upload-box").style.border = "none";
          };

          reader.readAsDataURL(input.files[0]);
        }
      });
  }

  const isAdminArticlesTemplate = document.querySelector(
    ".admin-ajout-articles"
  );

  if (isAdminArticlesTemplate) {
    const clientSelect = document.getElementById("user_id");
    const enfantCheckboxesContainer = document.getElementById(
      "enfant-checkboxes-container"
    );
    document.getElementById("enfants_id").style.display = "none";
    document.getElementById("titre-enfants").style.display = "none";

    clientSelect.addEventListener("change", function loadEnfants() {
      const selectedClientId = clientSelect.value;

      fetch(`/admin/articles/enfants/${selectedClientId}`)
        .then((response) => response.json())
        .then((data) => {
          enfantCheckboxesContainer.innerHTML = "";

          data.forEach((enfant) => {
            console.log(enfant);
            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.name = "articles[enfants][]";
            checkbox.value = enfant.id;
            checkbox.id = "enfant_" + enfant.id;
            const label = document.createElement("label");
            label.htmlFor = "enfant_" + enfant.id;
            label.appendChild(document.createTextNode(enfant.prenom));
            enfantCheckboxesContainer.appendChild(checkbox);
            enfantCheckboxesContainer.appendChild(label);
          });
        });

      document.getElementById("titre-enfants").style.display = "block";
    });
  }

  const isArticlesBySizeTemplate = document.querySelector(".articles-by-size");

  if (isArticlesBySizeTemplate) {
    var buttons = document.querySelectorAll(".btn-toggle");

    buttons.forEach(function (button) {
      button.addEventListener("click", function () {
        var category = this.getAttribute("data-category");
        var articles = document.querySelectorAll(".toggle-div");

        articles.forEach(function (article) {
          if (article.getAttribute("data-category") === category) {
            if (article.classList.contains("articles-hidden")) {
              article.classList.remove("articles-hidden");
            } else {
              article.classList.add("articles-hidden");
            }
          } else {
            article.classList.add("articles-hidden");
          }
        });

        buttons.forEach(function (btn) {
          btn.classList.remove("active");
        });

        this.classList.add("active");
      });
    });

    // div.style.display = "none";

    // var btnToggle = document.querySelector(".btn-toggle");
    // btnToggle.addEventListener("click", function toggleDiv() {
    //   console.log("Current display value:", div.style.display);
    //   var div = document.querySelector(".toggle-div");
    //   if (div.style.display === "none") {
    //     div.style.display = "block";
    //   } else {
    //     div.style.display = "none";
    //   }
    // });
  }
});
