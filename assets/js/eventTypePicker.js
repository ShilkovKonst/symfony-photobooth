const radioButtons = document.querySelectorAll('input[name="eventType"]');
const suppTypeInput = document.querySelector("#suppTypeInput");
// Add event listener to each radio button
radioButtons.forEach(function (radioButton) {
  radioButton.addEventListener("input", (e) => {
    if (e.target.checked && e.target.value === "Autre") {
      suppTypeInput.classList.add("active-input");
      suppTypeInput.classList.remove("desactive-input", "overflow-hidden");
    } else {
      suppTypeInput.classList.remove("active-input");
      suppTypeInput.classList.add("desactive-input", "overflow-hidden");
    }
  });
});
