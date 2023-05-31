// Obtener elementos del formulario y el calendario
var dayInput = document.getElementById("day");
var rangeStartInput = document.getElementById("range_start");
var rangeEndInput = document.getElementById("range_end");
var routeIdInput = document.getElementById("route_id");
var searchButton = document.getElementById("searchButton");
var calendar = document.getElementById("calendar");

// Crear el calendario
function createCalendar(data) {
  // Obtener el número de días en el mes
  var daysInMonth = new Date(data.year, data.month + 1, 0).getDate();

  // Crear el encabezado del calendario
  var header = document.createElement("div");
  header.classList.add("calendar-header");
  header.textContent = new Date(data.year, data.month).toLocaleString("default", {
    month: "long",
    year: "numeric"
  });
  calendar.innerHTML = "";
  calendar.appendChild(header);

  // Crear los días de la semana
  var weekDays = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
  var daysOfWeekContainer = document.createElement("div");
  daysOfWeekContainer.classList.add("calendar-days-of-week");
  for (var i = 0; i < weekDays.length; i++) {
    var dayOfWeek = document.createElement("div");
    dayOfWeek.classList.add("calendar-day-of-week");
    dayOfWeek.textContent = weekDays[i];
    daysOfWeekContainer.appendChild(dayOfWeek);
  }
  calendar.appendChild(daysOfWeekContainer);

  // Crear los días del calendario
  var daysContainer = document.createElement("div");
  daysContainer.classList.add("calendar-days");
  for (var i = 1; i <= daysInMonth; i++) {
    var day = document.createElement("div");
    day.classList.add("calendar-day");
    day.textContent = i;

    // Marcar el día actual
    if (
      data.year === currentYear &&
      data.month === currentMonth &&
      i === currentDay
    ) {
      day.classList.add("today");
    }

    daysContainer.appendChild(day);
  }
  calendar.appendChild(daysContainer);
}

// Obtener el año y mes actual
var currentDate = new Date();
var currentYear = currentDate.getFullYear();
var currentMonth = currentDate.getMonth();
var currentDay = currentDate.getDate();

// Crear el calendario con el año y mes actual
createCalendar({
  year: currentYear,
  month: currentMonth
});

// Función para realizar la búsqueda en base a los filtros
function performSearch() {
  // Obtener los valores de los filtros
  var dayFilter = dayInput.value;
  var rangeStartFilter = rangeStartInput.value;
  var rangeEndFilter = rangeEndInput.value;
  var routeIdFilter = routeIdInput.value;

  // Realizar la solicitud AJAX para obtener los resultados filtrados
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var filteredData = JSON.parse(xhr.responseText);
      createCalendar(filteredData);
    }
  };

  // Construir la URL de la solicitud
  var url =  "http://localhost/calendar";
  var params = [];
  if (dayFilter) {
    params.push("day=" + encodeURIComponent(dayFilter));
  }
  if (rangeStartFilter && rangeEndFilter) {
    params.push("range_start=" + encodeURIComponent(rangeStartFilter));
    params.push("range_end=" + encodeURIComponent(rangeEndFilter));
  }
  if (routeIdFilter) {
    params.push("route_id=" + encodeURIComponent(routeIdFilter));
  }
  if (params.length > 0) {
    url += "?" + params.join("&");
  }

  // Realizar la solicitud
  xhr.open("GET", url, true);
  xhr.send();
}

// Agregar evento de clic al botón de búsqueda
searchButton.addEventListener("click", performSearch);
