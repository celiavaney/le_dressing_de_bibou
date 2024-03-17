import "./bootstrap.js";
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import "./styles/app.scss";

require("bootstrap");
require("jquery");

console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉");

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
            console.log(checkbox.name);
            console.log(checkbox.value);
            console.log(checkbox.id);
            console.log(label.htmlFor);
          });
        });

      document.getElementById("titre-enfants").style.display = "block";
    });
  }

  // var div = (document.querySelector(".toggle-div").style.display = "none");

  // console.log(div);

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
});
