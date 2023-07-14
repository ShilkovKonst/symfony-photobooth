import "../styles/select2.css";
import $ from "jquery";
import "select2";

$(function () {
  $(".js-example-basic-single").select2({
    // allowClear: true,
    selectionCssClass: "form-control",
    width: "resolve",
    ajax: {
      url: "https://geo.api.gouv.fr/communes",
      dataType: "json",
      delay: 500,
      data: function (params) {
        return {
          nom: params.term,
          fields: "codesPostaux",
          codeRegion: 11,
        };
      },
      processResults: function (data) {
        let list = data.map((el) => {
          return {
            codesPostaux: el.codesPostaux,
            nom: el.nom,
          };
        });
        let results = [];
        list.forEach((item) => {
          item.codesPostaux.forEach((codePostal) => {
            results.push({
              id: codePostal + " | " + item.nom,
              text: codePostal + " | " + item.nom,
            });
          });
        });
        console.log("results", results);
        return {
          results: results,
        };
      },
    },
    selectOnClose: true,
    placeholder: "Saisir un nom de ville",
    minimumInputLength: 3,
  });
});

$(function () {
  $(".js-data-example-ajax").select2({
    // allowClear: true,
    selectionCssClass: "form-control",
    width: "resolve",
    ajax: {
      url: "https://geo.api.gouv.fr/communes",
      dataType: "json",
      delay: 500,
      data: function (params) {
        return {
          nom: params.term,
          fields: "codesPostaux",
        };
      },
      processResults: function (data) {
        let list = data.map((el) => {
          return {
            codesPostaux: el.codesPostaux,
            nom: el.nom,
          };
        });
        let results = [];
        list.forEach((item) => {
          item.codesPostaux.forEach((codePostal) => {
            results.push({
              id: codePostal + " | " + item.nom,
              text: codePostal + " | " + item.nom,
            });
          });
        });
        console.log("results", results);
        return {
          results: results,
        };
      },
    },
    selectOnClose: true,
    placeholder: "Saisir un nom de ville",
    minimumInputLength: 3,
  });
});
