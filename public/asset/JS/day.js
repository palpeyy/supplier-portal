// time.js

function updateTime() {
  var currentTime = new Date();
  var hours = currentTime.getHours();
  var minutes = currentTime.getMinutes();
  var seconds = currentTime.getSeconds();
  var day = currentTime.getDate();
  var month = currentTime.getMonth(); // Months start from 0
  var year = currentTime.getFullYear();
  var daysOfWeek = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  var monthsOfYear = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

  // Add leading zero if number is less than 10
  hours = (hours < 10 ? "0" : "") + hours;
  minutes = (minutes < 10 ? "0" : "") + minutes;
  seconds = (seconds < 10 ? "0" : "") + seconds;
  day = (day < 10 ? "0" : "") + day;

  var formattedTime = daysOfWeek[currentTime.getDay()] + ", " + day + " " + monthsOfYear[month] + " " + year + " " + hours + ":" + minutes + ":" + seconds;

  document.getElementById("current-time").innerText = formattedTime;
}

// Call updateTime every second
setInterval(updateTime, 1000);

// Call updateTime after the page loads
updateTime();
