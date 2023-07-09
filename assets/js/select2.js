import "../styles/select2.css";
import $ from "jquery";
import "select2";

$(function () {
  $(".js-example-basic-single").select2({
    // allowClear: true,
    selectionCssClass: "form-control",
    width: "resolve",
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
              id: codePostal,
              text: codePostal + " | " + item.nom
            });
          });
        });
        console.log("results", results);
        return {
          results: results,
        };
      },
      cache: true,
    },
    selectOnClose: true,
    placeholder: "Search for a repository",
    minimumInputLength: 3,
    // templateResult: formatState,
    // templateSelection: formatZipSelection,
  });
});
