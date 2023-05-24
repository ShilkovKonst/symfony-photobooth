import flatpickr from "flatpickr";

var blockedDates = JSON.parse(
  document.getElementById("eventDate").dataset.blockedDates
);
var minDate = document.getElementById("eventDate").dataset.minDate;

var flatpickrElement = document.getElementById("eventDate");
if (flatpickrElement) {
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
}
