import '../styles/flatpickr.css'
import flatpickr from "flatpickr";

var blockedDates = Object.values(JSON.parse(
  document.getElementById("eventDate").dataset.blockedDates
));
var minDate = document.getElementById("eventDate").dataset.minDate;

  flatpickr("#eventDate", {
    allowInput: true,
    onOpen: function (selectedDates, dateStr, instance) {
      instance.input.readOnly = true;
    },
    onClose: function (selectedDates, dateStr, instance) {
      instance.input.readOnly = false;
      instance.input.blur();
    },
    disable: blockedDates,
    dateFormat: "Y-m-d",
    minDate: minDate,
  });
