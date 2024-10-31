new Chart(document.getElementById("pie-chart"), {
    type: 'pie',
    data: data,
    options: {
	responsive: true,
	maintainAspectRatio: false,
      title: {
        display: true,
		
        text: 'Predicted world population (millions) in 2050'
      }
    }
});
