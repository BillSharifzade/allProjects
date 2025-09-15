$(function() {
	'use strict';
	if ($('#chartBar1').length > 0) {
	var ctx1 = document.getElementById('chartBar1').getContext('2d');
	new Chart(ctx1, {
		type: 'bar',
		data: {
			labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
			datasets: [{
				label: 'Sales',
				data: [24, 10, 32, 24, 26, 20],
				backgroundColor: '#664dc9'
			}]
		},
		options: {
			maintainAspectRatio: false,
			responsive: true,
			legend: {
				display: false,
				labels: {
					display: false
				}
			},
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true,
						fontSize: 10,
						max: 80
					}
				}],
				xAxes: [{
					barPercentage: 0.6,
					ticks: {
						beginAtZero: true,
						fontSize: 11
					}
				}]
			}
		}
	});
}
if ($('#chartBar2').length > 0) {
	var ctx2 = document.getElementById('chartBar2').getContext('2d');
	new Chart(ctx2, {
		type: 'bar',
		data: {
			labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
			datasets: [{
				label: 'Sales',
				data: [14, 12, 34, 25, 24, 20],
				backgroundColor: '#44c4fa'
			}]
		},
		options: {
			maintainAspectRatio: false,
			responsive: true,
			legend: {
				display: false,
				labels: {
					display: false
				}
			},
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true,
						fontSize: 10,
						max: 80
					}
				}],
				xAxes: [{
					barPercentage: 0.6,
					ticks: {
						beginAtZero: true,
						fontSize: 11
					}
				}]
			}
		}
	});
}
if ($('#chartBar3').length > 0) {
	var ctx3 = document.getElementById('chartBar3').getContext('2d');
	var gradient = ctx3.createLinearGradient(0, 0, 0, 250);
	gradient.addColorStop(0, '#44c4fa');
	gradient.addColorStop(1, '#664dc9');
	new Chart(ctx3, {
		type: 'bar',
		data: {
			labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
			datasets: [{
				label: 'Sales',
				data: [14, 12, 34, 25, 24, 20],
				backgroundColor: gradient
			}]
		},
		options: {
			maintainAspectRatio: false,
			responsive: true,
			legend: {
				display: false,
				labels: {
					display: false
				}
			},
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true,
						fontSize: 10,
						max: 80
					}
				}],
				xAxes: [{
					barPercentage: 0.6,
					ticks: {
						beginAtZero: true,
						fontSize: 11
					}
				}]
			}
		}
	});
}
if ($('#chartBar4').length > 0) {

	var ctx4 = document.getElementById('chartBar4').getContext('2d');
	new Chart(ctx4, {
		type: 'bar',
		data: {
			labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
			datasets: [{
				label: 'Sales',
				data: [14, 12, 34, 25, 24, 20],
				backgroundColor: ['#664dc9', '#44c4fa', '#38cb89', '#3e80eb', '#ffab00', '#ef4b4b']
			}]
		},
		options: {
			indexAxis: 'y',
			maintainAspectRatio: false,
			legend: {
				display: false,
				labels: {
					display: false
				}
			},
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true,
						fontSize: 10,
					}
				}],
				xAxes: [{
					ticks: {
						beginAtZero: true,
						fontSize: 11,
						max: 80
					}
				}]
			}
		}
	});
}
if ($('#chartBar5').length > 0) {
	var ctx5 = document.getElementById('chartBar5').getContext('2d');
	new Chart(ctx5, {
		type: 'bar',
		data: {
			labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
			datasets: [{
				data: [14, 12, 34, 25, 24, 20],
				backgroundColor: [ '#664dc9', '#38cb89', '#116e7c', '#ffab00', '#ef4b4b']
			}, {
				data: [22, 30, 25, 30, 20, 40],
				backgroundColor: '#44c4fa'
			}]
		},
		options: {
			indexAxis: 'y',
			maintainAspectRatio: false,
			legend: {
				display: false,
				labels: {
					display: false
				}
			},
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true,
						fontSize: 11,
					}
				}],
				xAxes: [{
					ticks: {
						beginAtZero: true,
						fontSize: 11,
						max: 80
					}
				}]
			}
		}
	});
}
	/** STACKED BAR CHART **/
if ($('#chartStacked1').length > 0) {
	var ctx6 = document.getElementById('chartStacked1');
	new Chart(ctx6, {
		type: 'bar',
		data: {
			labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
			datasets: [{
				data: [14, 12, 34, 25, 24, 20],
				backgroundColor: '#664dc9',
				borderWidth: 1,
				fill: true
			}, {
				data: [14, 12, 34, 25, 24, 20],
				backgroundColor:  '#44c4fa',
				borderWidth: 1,
				fill: true
			}]
		},
		options: {
			maintainAspectRatio: false,
			legend: {
				display: false,
				labels: {
					display: false
				}
			},
			scales: {
				yAxes: [{
					stacked: true,
					ticks: {
						beginAtZero: true,
						fontSize: 11
					}
				}],
				xAxes: [{
					barPercentage: 0.5,
					stacked: true,
					ticks: {
						fontSize: 11
					}
				}]
			}
		}
	});
}
if ($('#chartStacked2').length > 0) {
	var ctx7 = document.getElementById('chartStacked2');
	new Chart(ctx7, {
		type: 'bar',
		data: {
			labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
			datasets: [{
				data: [14, 12, 34, 25, 24, 20],
				backgroundColor: '#664dc9',
				borderWidth: 1,
				fill: true
			}, {
				data: [14, 12, 34, 25, 24, 20],
				backgroundColor:  '#44c4fa',
				borderWidth: 1,
				fill: true
			}]
		},
		options: {
			indexAxis: 'y',
			maintainAspectRatio: false,
			legend: {
				display: false,
				labels: {
					display: false
				}
			},
			scales: {
				yAxes: [{
					stacked: true,
					ticks: {
						beginAtZero: true,
						fontSize: 10,
						max: 80
					}
				}],
				xAxes: [{
					stacked: true,
					ticks: {
						beginAtZero: true,
						fontSize: 11
					}
				}]
			}
		}
	});
}
	/* LINE CHART */
if ($('#chartLine1').length > 0) {
	var ctx8 = document.getElementById('chartLine1');
	new Chart(ctx8, {
		type: 'line',
		data: {
			labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			datasets: [{
				data: [14, 12, 34, 25, 44, 36, 35, 25, 30, 32, 20, 25 ],
				borderColor: '#664dc9',
				borderWidth: 1,
				fill: false
			}, {
				data: [35, 30, 45, 35, 55, 40, 10, 20, 25, 55, 50, 45],
				borderColor: '#44c4fa',
				borderWidth: 1,
				fill: false
			}]
		},
		options: {
			maintainAspectRatio: false,
			legend: {
				display: false,
				labels: {
					display: false
				}
			},
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true,
						fontSize: 10,
						max: 80
					}
				}],
				xAxes: [{
					ticks: {
						beginAtZero: true,
						fontSize: 11
					}
				}]
			}
		}
	});
}
	/** AREA CHART **/
if ($('#chartArea1').length > 0) {
	var ctx9 = document.getElementById('chartArea1');
	ctx9.height = 220;

	var gradient1 = ctx3.createLinearGradient(0, 350, 0, 0);
	gradient1.addColorStop(0, 'rgba(102, 77, 201,0)');
	gradient1.addColorStop(1, 'rgba(102, 77, 201,.5)');
	var gradient2 = ctx3.createLinearGradient(0, 280, 0, 0);
	gradient2.addColorStop(0, 'rgba(91, 115, 232,0)');
	gradient2.addColorStop(1, 'rgba(91, 115, 232,.5)');
	new Chart(ctx9, {
		type: 'line',
		data: {
			labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			datasets: [{
				data: [14, 12, 34, 25, 44, 36, 35, 25, 30, 32, 20, 25 ],
				borderColor: '#664dc9',
				borderWidth: 1,
				backgroundColor: gradient1
			}, {
				data: [35, 30, 45, 35, 55, 40, 10, 20, 25, 65, 50, 45],
				borderColor: '#44c4fa',
				borderWidth: 1,
				backgroundColor: gradient2
			}]
		},
		options: {
			maintainAspectRatio: false,
			legend: {
				display: false,
				labels: {
					display: false
				}
			},
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true,
						fontSize: 10,
						max: 100
					}
				}],
				xAxes: [{
					ticks: {
						beginAtZero: true,
						fontSize: 11
					}
				}]
			}
		}
	});

	/** PIE CHART **/
	var datapie = {
		labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
		datasets: [{
			data: [35,20,8,15,24],
			backgroundColor: ['#664dc9', '#44c4fa', '#38cb89', '#3e80eb', '#ffab00', '#ef4b4b']
		}]
	};
	var optionpie = {
		maintainAspectRatio: false,
		responsive: true,
		legend: {
			display: false,
		},
		animation: {
			animateScale: true,
			animateRotate: true
		}
	};
}
	// For a doughnut chart
if ($('#chartPie').length > 0) {
	var ctx6 = document.getElementById('chartPie');
	var myPieChart6 = new Chart(ctx6, {
		type: 'doughnut',
		data: datapie,
		options: optionpie
	});
	/** PIE CHART **/
	var datapie = {
		labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
		datasets: [{
			data: [35,20,8,15,24],
			backgroundColor: ['#664dc9', '#44c4fa', '#38cb89', '#3e80eb', '#ffab00', '#ef4b4b']
		}]
	};
	var optionpie = {
		maintainAspectRatio: false,
		responsive: true,
		legend: {
			display: false,
		},
		animation: {
			animateScale: true,
			animateRotate: true
		}
	};
}
	// For a pie chart
if ($('#chartDonut').length > 0) {
	var ctx7 = document.getElementById('chartDonut');
	var myPieChart7 = new Chart(ctx7, {
		type: 'pie',
		data: datapie,
		options: optionpie
	});
	/** PIE CHART **/
	var datapie = {
		labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
		datasets: [{
			data: [35,20,8,15,24],
			backgroundColor: ['#664dc9', '#44c4fa', '#38cb89', '#3e80eb', '#ffab00', '#ef4b4b']
		}]
	};
	var optionpie = {
		maintainAspectRatio: false,
		responsive: true,
		legend: {
			display: false,
		},
		animation: {
			animateScale: true,
			animateRotate: true
		}
	};

	const data = {
		datasets: [{
			label: 'Appointment',
			data: [{
			  x: -10,
			  y: 0
			}, {
			  x: 0,
			  y: 10
			}, {
			  x: 10,
			  y: 5
			}, {
			  x: 0.5,
			  y: 5.5
			}],
			backgroundColor: '#1F9CC6'
		  }],
	  };
	  
	var ctx12 = document.getElementById('chartRadar');
	var radar = new Chart(ctx12, {
		type: 'scatter',
		data: data,
		options: {
			scales: {
			x: {
				type: 'linear',
				position: 'bottom'
			}
			}
		}
	  });
	}

	if($('#mySemiDonutChart').length > 0) {
		var ctx = document.getElementById('mySemiDonutChart').getContext('2d');
    var mySemiDonutChart = new Chart(ctx, {
        type: 'doughnut', // Chart type
        data: {
            labels: ['Ongoing','Onhold', 'Completed', 'Overdue'],
            datasets: [{
                label: 'Semi Donut',
                data: [20, 40, 20, 10],
                backgroundColor: ['#FFC107', '#1B84FF', '#03C95A', '#E70D0D'],
                borderWidth: -10,
                borderColor: 'transparent', // Border between segments
                hoverBorderWidth: 0,   // Border radius for curved edges
                cutout: '75%',   
				spacing: -30,
            }]
        },
		options: {
			rotation: -100,
			circumference: 185,
			layout: {
				padding: {
					top: -20,    // Set to 0 to remove top padding
					bottom: 20, // Set to 0 to remove bottom padding
				}
			},
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: {
					display: false // Hide the legend
				}
			},elements: {
				arc: {
					borderWidth: -30, // Ensure consistent overlap
					borderRadius: 30, // Add some rounding
				  }
			  },
		  }
    });
	}

	if($('#attendance').length > 0) {
		var ctx = document.getElementById('attendance').getContext('2d');
    var mySemiDonutChart = new Chart(ctx, {
        type: 'doughnut', // Chart type
        data: {
            labels: ['Late','Present', 'Permission', 'Absent'],
            datasets: [{
                label: 'Semi Donut',
                data: [40, 20, 30, 10],
                backgroundColor: ['#0C4B5E', '#03C95A', '#FFC107', '#E70D0D'],
                borderWidth: 5,
				borderRadius: 10,
                borderColor: '#fff', // Border between segments
                hoverBorderWidth: 0,   // Border radius for curved edges
                cutout: '60%',   
            }]
        },
		options: {
			rotation: -100,
			circumference: 200,
			layout: {
				padding: {
					top: -20,    // Set to 0 to remove top padding
					bottom: -20, // Set to 0 to remove bottom padding
				}
			},
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: {
					display: false // Hide the legend
				}
			},
		  }
    });
	}

	if ($('#deal_chart').length > 0) {
		
		var ctx = document.getElementById('deal_chart').getContext('2d');
var myRadarChart = new Chart(ctx, {
    type: 'radar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Ayg'], // Common labels for both series
        datasets: [
            {
                label: 'Email', // First series
                data: [40, 70, 20, 40, 40, 70, 40, 60],
                backgroundColor: '#2dcb73',
                borderColor: '#2dcb73',
                pointBackgroundColor: '#2dcb73',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(255, 99, 132, 1)', 
				tension: 0.3,
            },
            {
                label: 'Chat', // Second series
                data: [30, 30, 90, 30, 60, 30, 60, 90],
                backgroundColor: '#4b3088',
                borderColor: '#4b3088',
                pointBackgroundColor: '#4b3088',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(54, 162, 235, 1)',
				tension: 0.4,
            },
            {
                label: 'Series 3', // Second series
                data: [70, 43, 70, 90, 30, 30, 30, 40],
                backgroundColor: '#F26522',
                borderColor: '#F26522',
                pointBackgroundColor: '#F26522',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(54, 162, 235, 1)',
				tension: 0.4,
            }
        ]
    },
    options: {
		responsive: false,
        scales: {
            r: {
                angleLines: {
                    display: true,
                    color: '#e9e9e9' // Color of the radial lines
                },
                grid: {
                    circular: true // Make the grid lines circular
                },
                suggestedMin: 0,
                suggestedMax: 100,
				ticks: {
					stepSize: 30
				}
            }
        },
		plugins: {
			legend: {
				display: false // This hides the legend
			}
		}
    }
});
		
	}

});