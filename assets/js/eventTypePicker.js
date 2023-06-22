const radioButtons = document.querySelectorAll('input[name="create_reservation[eventType]"]');
const suppType = document.getElementById("suppTypeInput");
const addEventTypeInput = document.getElementById('create_reservation_addEventType');
// Add event listener to each radio button
radioButtons.forEach(function (radioButton) {
  radioButton.addEventListener("input", (e) => {
    if (e.target.checked && e.target.value === "Autre") {
      addEventTypeInput.setAttribute("required", true);
      suppType.classList.add("active-input");
      suppType.classList.remove("desactive-input", "overflow-hidden");
    } else {
      addEventTypeInput.removeAttribute("required", false);
      suppType.classList.remove("active-input");
      suppType.classList.add("desactive-input", "overflow-hidden");
    }
  });
});
