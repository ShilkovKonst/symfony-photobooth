import '../styles/flatpickr.css'
import flatpickr from "flatpickr";

var blockedDates = Object.values(JSON.parse(
  document.getElementById("create_reservation_eventDate").dataset.blockedDates
));
var minDate = document.getElementById("create_reservation_eventDate").dataset.minDate;

  flatpickr("#create_reservation_eventDate", {
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
