import "./bootstrap.js";
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import "./styles/app.scss";

require("bootstrap");

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
});
