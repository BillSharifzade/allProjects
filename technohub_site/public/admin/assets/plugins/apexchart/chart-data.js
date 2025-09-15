'use strict';

$(document).ready(function () {

  function generateData(baseval, count, yrange) {
    var i = 0;
    var series = [];
    while (i < count) {
      var x = Math.floor(Math.random() * (750 - 1 + 1)) + 1;;
      var y = Math.floor(Math.random() * (yrange.max - yrange.min + 1)) + yrange.min;
      var z = Math.floor(Math.random() * (75 - 15 + 1)) + 15;

      series.push([x, y, z]);
      baseval += 86400000;
      i++;
    }
    return series;
  }


  // Column chart
  if ($('#sales_chart').length > 0) {
    var columnCtx = document.getElementById("sales_chart"),
      columnConfig = {
        colors: ['#7638ff', '#fda600'],
        series: [
          {
            name: "Received",
            type: "column",
            data: [70, 150, 80, 180, 150, 175, 201, 60, 200, 120, 190, 160, 50]
          },
          {
            name: "Pending",
            type: "column",
            data: [23, 42, 35, 27, 43, 22, 17, 31, 22, 22, 12, 16, 80]
          }
        ],
        chart: {
          type: 'bar',
          fontFamily: 'Poppins, sans-serif',
          height: 350,
          toolbar: {
            show: false
          }
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '60%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
        },
        yaxis: {
          title: {
            text: '$ (thousands)'
          }
        },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return "$ " + val + " thousands"
            }
          }
        }
      };
    var columnChart = new ApexCharts(columnCtx, columnConfig);
    columnChart.render();
  }

  //Pie Chart
  if ($('#invoice_chart').length > 0) {
    var pieCtx = document.getElementById("invoice_chart"),
      pieConfig = {
        colors: ['#03C95A', '#E70D0D', '#AB47BC', '#FFC107'],
        series: [45, 15, 21, 5],
        chart: {
          fontFamily: 'Poppins, sans-serif',
          height: 150,
          type: 'donut',
          offsetX: -30,
        },
        labels: ['Paid', 'Overdue', 'Pending', 'Draft'],
        legend: { show: true },
        dataLabels: {
          enabled: false // Disable the data labels
        },
        plotOptions: {
          pie: {
            donut: {
              labels: {
                show: true,
                name: {
                  show: true,
                  fontSize: '2px',
                },
                value: {
                  show: true,
                  fontSize: '12px',
                  formatter: function (val) {
                    return val + "%";
                  }
                },
                total: {
                  show: true,
                  showAlways: true,
                  formatter: function (w) {
                    return w.globals.seriesTotals.reduce((a, b) => {
                      return 45;
                    }, 0);
                  },
                  label: 'Paid'
                }
              }
            }
          }
        },
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 300
            },
            legend: {
              position: 'right'
            }
          }
        }]
      };
    var pieChart = new ApexCharts(pieCtx, pieConfig);
    pieChart.render();
  }


  // Simple Line
  if ($('#s-line').length > 0) {
    var sline = {
      chart: {
        height: 350,
        type: 'line',
        zoom: {
          enabled: false
        },
        toolbar: {
          show: false,
        }
      },
      colors: ['#F26522'],
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'straight'
      },
      series: [{
        name: "Desktops",
        data: [10, 41, 35, 51, 49, 62, 69, 91, 148]
      }],
      title: {
        text: 'Product Trends by Month',
        align: 'left'
      },
      grid: {
        row: {
          colors: ['#f1f2f3', 'transparent'], // takes an array which will be repeated on columns
          opacity: 0.5
        },
      },
      xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
      }
    }

    var chart = new ApexCharts(
      document.querySelector("#s-line"),
      sline
    );

    chart.render();
  }


  // Simple Line Area
  if ($('#s-line-area').length > 0) {
    var sLineArea = {
      chart: {
        height: 350,
        type: 'area',
        toolbar: {
          show: false,
        }
      },
      colors: ['#F26522', '#888ea8'],
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth'
      },
      series: [{
        name: 'series1',
        data: [31, 40, 28, 51, 42, 109, 100]
      }, {
        name: 'series2',
        data: [11, 32, 45, 32, 34, 52, 41]
      }],

      xaxis: {
        type: 'datetime',
        categories: ["2018-09-19T00:00:00", "2018-09-19T01:30:00", "2018-09-19T02:30:00", "2018-09-19T03:30:00", "2018-09-19T04:30:00", "2018-09-19T05:30:00", "2018-09-19T06:30:00"],
      },
      tooltip: {
        x: {
          format: 'dd/MM/yy HH:mm'
        },
      }
    }

    var chart = new ApexCharts(
      document.querySelector("#s-line-area"),
      sLineArea
    );

    chart.render();
  }

  // Simple Column
  if ($('#s-col').length > 0) {
    var sCol = {
      chart: {
        height: 350,
        type: 'bar',
        toolbar: {
          show: false,
        }
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '55%',
          endingShape: 'rounded'
        },
      },
      colors: ['#F26522', '#4361ee'],
      dataLabels: {
        enabled: false
      },
      stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
      },
      series: [{
        name: 'Net Profit',
        data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
      }, {
        name: 'Revenue',
        data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
      }],
      xaxis: {
        categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
      },
      yaxis: {
        title: {
          text: '$ (thousands)'
        }
      },
      fill: {
        opacity: 1

      },
      tooltip: {
        y: {
          formatter: function (val) {
            return "$ " + val + " thousands"
          }
        }
      }
    }

    var chart = new ApexCharts(
      document.querySelector("#s-col"),
      sCol
    );

    chart.render();
  }


  // Simple Column Stacked
  if ($('#s-col-stacked').length > 0) {
    var sColStacked = {
      chart: {
        height: 350,
        type: 'bar',
        stacked: true,
        toolbar: {
          show: false,
        }
      },
      responsive: [{
        breakpoint: 480,
        options: {
          legend: {
            position: 'bottom',
            offsetX: -10,
            offsetY: 0
          }
        }
      }],
      plotOptions: {
        bar: {
          horizontal: false,
        },
      },
      colors: ['#F26522', '#E70D0D', '#03C95A', '#1B84FF'],
      series: [{
        name: 'PRODUCT A',
        data: [44, 55, 41, 67, 22, 43]
      }, {
        name: 'PRODUCT B',
        data: [13, 23, 20, 8, 13, 27]
      }, {
        name: 'PRODUCT C',
        data: [11, 17, 15, 15, 21, 14]
      }, {
        name: 'PRODUCT D',
        data: [21, 7, 25, 13, 22, 8]
      }],
      xaxis: {
        type: 'datetime',
        categories: ['01/01/2011 GMT', '01/02/2011 GMT', '01/03/2011 GMT', '01/04/2011 GMT', '01/05/2011 GMT', '01/06/2011 GMT'],
      },
      legend: {
        position: 'right',
        offsetY: 40
      },
      fill: {
        opacity: 1
      },
    }

    var chart = new ApexCharts(
      document.querySelector("#s-col-stacked"),
      sColStacked
    );

    chart.render();
  }

  // Simple Bar
  if ($('#s-bar').length > 0) {
    var sBar = {
      chart: {
        height: 350,
        type: 'bar',
        toolbar: {
          show: false,
        }
      },
      colors: ['#F26522'],
      plotOptions: {
        bar: {
          horizontal: true,
        }
      },
      dataLabels: {
        enabled: false
      },
      series: [{
        data: [400, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380]
      }],
      xaxis: {
        categories: ['South Korea', 'Canada', 'United Kingdom', 'Netherlands', 'Italy', 'France', 'Japan', 'United States', 'China', 'Germany'],
      }
    }

    var chart = new ApexCharts(
      document.querySelector("#s-bar"),
      sBar
    );

    chart.render();
  }

  // Mixed Chart
  if ($('#mixed-chart').length > 0) {
    var options = {
      chart: {
        height: 350,
        type: 'line',
        toolbar: {
          show: false,
        }
      },
      colors: ['#F26522', '#888ea8'],
      series: [{
        name: 'Website Blog',
        type: 'column',
        data: [440, 505, 414, 671, 227, 413, 201, 352, 752, 320, 257, 160]
      }, {
        name: 'Social Media',
        type: 'line',
        data: [23, 42, 35, 27, 43, 22, 17, 31, 22, 22, 12, 16]
      }],
      stroke: {
        width: [0, 4]
      },
      title: {
        text: 'Traffic Sources'
      },
      labels: ['01 Jan 2001', '02 Jan 2001', '03 Jan 2001', '04 Jan 2001', '05 Jan 2001', '06 Jan 2001', '07 Jan 2001', '08 Jan 2001', '09 Jan 2001', '10 Jan 2001', '11 Jan 2001', '12 Jan 2001'],
      xaxis: {
        type: 'datetime'
      },
      yaxis: [{
        title: {
          text: 'Website Blog',
        },

      }, {
        opposite: true,
        title: {
          text: 'Social Media'
        }
      }]

    }

    var chart = new ApexCharts(
      document.querySelector("#mixed-chart"),
      options
    );

    chart.render();
  }

  // Donut Chart

  if ($('#donut-chart').length > 0) {
    var donutChart = {
      chart: {
        height: 350,
        type: 'donut',
        toolbar: {
          show: false,
        }
      },
      // colors: ['#4361ee', '#888ea8', '#e3e4eb', '#d3d3d3'],
      series: [44, 55, 41, 17],
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 200
          },
          legend: {
            position: 'bottom'
          }
        }
      }]
    }

    var donut = new ApexCharts(
      document.querySelector("#donut-chart"),
      donutChart
    );

    donut.render();
  }

  // Radial Chart
  if ($('#radial-chart').length > 0) {
    var radialChart = {
      chart: {
        height: 350,
        type: 'radialBar',
        toolbar: {
          show: false,
        }
      },
      // colors: ['#4361ee', '#888ea8', '#e3e4eb', '#d3d3d3'],
      plotOptions: {
        radialBar: {
          dataLabels: {
            name: {
              fontSize: '22px',
            },
            value: {
              fontSize: '16px',
            },
            total: {
              show: true,
              label: 'Total',
              formatter: function (w) {
                return 249
              }
            }
          }
        }
      },
      series: [44, 55, 67, 83],
      labels: ['Apples', 'Oranges', 'Bananas', 'Berries'],
    }

    var chart = new ApexCharts(
      document.querySelector("#radial-chart"),
      radialChart
    );

    chart.render();
  }

  if ($('#sales_charts').length > 0) {
    var options = {
      series: [{
        name: 'Sales',
        data: [130, 210, 300, 290, 150, 50, 210, 280, 105],
      }, {
        name: 'Purchase',
        data: [-150, -90, -50, -180, -50, -70, -100, -90, -105]
      }],
      colors: ['#28C76F', '#EA5455'],
      chart: {
        type: 'bar',
        height: 320,
        stacked: true,

        zoom: {
          enabled: true
        }
      },
      responsive: [{
        breakpoint: 280,
        options: {
          legend: {
            position: 'bottom',
            offsetY: 0
          }
        }
      }],
      plotOptions: {
        bar: {
          horizontal: false,
          borderRadius: 4,
          borderRadiusApplication: "end", // "around" / "end" 
          borderRadiusWhenStacked: "all", // "all"/"last"
          columnWidth: '20%',
        },
      },
      dataLabels: {
        enabled: false
      },
      yaxis: {
        min: -200,
        max: 300,
        tickAmount: 5,
      },
      xaxis: {
        categories: [' Jan ', 'Feb', 'Mar', 'Apr',
          'May', 'Jun', 'Jul', 'Aug', 'Sep'
        ],
      },
      legend: { show: false },
      fill: {
        opacity: 1
      }
    };

    var chart = new ApexCharts(document.querySelector("#sales_charts"), options);
    chart.render();
  }

  if ($('#sales-analysis').length > 0) {
    var options = {
      series: [{
        name: "Sales Analysis",
        data: [25, 30, 18, 15, 22, 20, 30, 20, 22, 18, 15, 20]
      }],
      chart: {
        height: 273,
        type: 'area',
        zoom: {
          enabled: false
        }
      },
      colors: ['#FF9F43'],
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'straight'
      },
      title: {
        text: '',
        align: 'left'
      },
      xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
      },
      yaxis: {
        min: 10,
        max: 60,
        tickAmount: 5,
        labels: {
          formatter: (val) => {
            return val / 1 + 'K'
          }
        }
      },
      legend: {
        position: 'top',
        horizontalAlign: 'left'
      }
    };

    var chart = new ApexCharts(document.querySelector("#sales-analysis"), options);
    chart.render();
  }

  // Student Chart

  if ($('#teacher-chart').length > 0) {
    var donutChart = {
      chart: {
        height: 260,
        type: 'donut',
        toolbar: {
          show: false,
        }
      },
      colors: ['#3D5EE1', '#6FCCD8'],
      series: [346, 54],
      labels: ['Present', 'Absent'],
      legend: { show: false },
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            height: 180,
          },
          legend: {
            position: 'bottom'
          }
        }
      }]
    }

    var donut = new ApexCharts(
      document.querySelector("#teacher-chart"),
      donutChart
    );

    donut.render();
  }


  // Student Chart

  if ($('#staff-chart').length > 0) {
    var donutChart = {
      chart: {
        height: 260,
        type: 'donut',
        toolbar: {
          show: false,
        }
      },
      colors: ['#3D5EE1', '#6FCCD8'],
      series: [620, 80],
      labels: ['Present', 'Absent'],
      legend: { show: false },
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            height: 180,
          },
          legend: {
            position: 'bottom'
          }
        }
      }]
    }

    var donut = new ApexCharts(
      document.querySelector("#staff-chart"),
      donutChart
    );

    donut.render();
  }


  // Class Chart

  if ($('#class-chart').length > 0) {
    var donutChart = {
      chart: {
        height: 130,
        type: 'donut',
        toolbar: {
          show: false,
        },
        sparkline: {
          enabled: true
        }
      },
      colors: ['#3D5EE1', '#EAB300', '#E82646'],
      series: [45, 11, 2],
      labels: ['Good', 'Average', 'Below Average'],
      legend: { show: false },
      dataLabels: {
        enabled: false
      },
      yaxis: {
        tickAmount: 3,
        labels: {
          offsetX: -15,
        },
      },
      grid: {
        padding: {
          left: -8,
        },
      },
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 200
          },
          legend: {
            position: 'bottom'
          }
        }
      }]
    }

    var donut = new ApexCharts(
      document.querySelector("#class-chart"),
      donutChart
    );

    donut.render();
  }

  // Leaves Chart

  if ($('#leaves_chart').length > 0) {
    var donutChart = {
      chart: {
        height: 255,
        type: 'donut',
        toolbar: {
          show: false,
        }
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '50%'
        },
      },
      dataLabels: {
        enabled: false
      },

      series: [15, 10, 5, 10, 60],
      colors: ['#F26522', '#FFC107', '#E70D0D', '#03C95A', '#0C4B5E'],
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 200
          },
          legend: {
            show: false
          }
        }
      }],
      legend: {
        show: false
      }
    }

    var donut = new ApexCharts(
      document.querySelector("#leaves_chart"),
      donutChart
    );

    donut.render();
  }

  // Fees Chart

  if ($('#fees-chart').length > 0) {
    var sCol = {
      chart: {
        height: 275,
        type: 'bar',
        stacked: true,
        toolbar: {
          show: false,
        }
      },
      legend: {
        show: true,
        horizontalAlign: 'left',
        position: 'top',
        fontSize: '14px',
        labels: {
          colors: '#5D6369',
        }
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '50%',
          endingShape: 'rounded'
        },
      },
      colors: ['#3D5EE1', '#E9EDF4'],
      dataLabels: {
        enabled: false
      },
      stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
      },
      grid: {
        padding: {
          left: -8,
        },
      },
      series: [{
        name: 'Collected Fee',
        data: [30, 40, 38, 40, 38, 30, 35, 38, 40]
      }, {
        name: 'Total Fee',
        data: [45, 50, 48, 50, 48, 40, 40, 50, 55]
      }],
      xaxis: {
        categories: ['Q1: 2023', 'Q1: 2023', 'Q1: 2023', 'Q1: 2023', 'Q1: 2023', 'uQ1: 2023l', 'Q1: 2023', 'Q1: 2023', 'Q1: 2023'],
      },
      yaxis: {
      },
      yaxis: {
        tickAmount: 3,
        labels: {
          offsetX: -15
        },
      },
      fill: {
        opacity: 1

      },
      tooltip: {
        y: {
          formatter: function (val) {
            return "$ " + val + " thousands"
          }
        }
      }
    }

    var chart = new ApexCharts(
      document.querySelector("#fees-chart"),
      sCol
    );

    chart.render();
  }

  if ($('#exam-result-chart').length > 0) {
    var options = {
      chart: {
        type: 'bar',
        height: 310
      },
      series: [{
        name: 'Marks',
        data: [100, 92, 90, 82, 90] // Corresponding scores for Maths, Physics, Chemistry, English, Spanish
      }],
      xaxis: {
        categories: ['Mat', 'Phy', 'Che', 'Eng', 'Sci']
      },
      plotOptions: {
        bar: {
          distributed: true,
          columnWidth: '50%',
          colors: {
            backgroundBarColors: ['#E9EDF4', '#fff'],
            backgroundBarOpacity: 1,
            backgroundBarRadius: 5,
          },
          dataLabels: {
            position: 'top'
          },
        }
      },
      colors: ['#E9EDF4', '#3D5EE1', '#E9EDF4', '#E9EDF4', '#E9EDF4'], // Set specific colors for each bar
      tooltip: {
        y: {
          formatter: function (val) {
            return val + "%"
          }
        }
      },
      dataLabels: {
        enabled: true,
        formatter: function (val) {
          return val + "%";
        },
        offsetY: -20,
        style: {
          fontSize: '14px',
          colors: ["#304758"]
        }
      },
      grid: {
        yaxis: {
          lines: {
            show: false
          }
        },
      },

      legend: {
        show: false
      }
    }

    var chart = new ApexCharts(document.querySelector("#exam-result-chart"), options);
    chart.render();
  }

  if ($('#performance_chart').length > 0) {
    var options = {
      chart: {
        type: 'area',
        height: 355
      },
      series: [{
        name: 'Avg. Exam Score',
        data: [75, 68, 65, 68, 75] // Sample data
      }, {
        name: 'Avg. Attendance',
        data: [85, 78, 75, 78, 85] // Sample data
      }],
      xaxis: {
        categories: ['Quarter 1', 'Quarter 2', 'Half yearly', 'Model', 'Final']
      },
      tooltip: {
        y: {
          formatter: function (val) {
            return val + "%";
          }
        },
        shared: true,
        intersect: false,
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
          return `<div class="apexcharts-tooltip">${w.globals.labels[dataPointIndex]}<br>Exam Score: <span style="color: #1E90FF;">${series[0][dataPointIndex]}%</span><br>Attendance: <span style="color: #00BFFF;">${series[1][dataPointIndex]}%</span></div>`;
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth'
      },
      grid: {
        padding: {
          left: -15,
          right: 0,
        },
      },
      grid: {
        yaxis: {
          axisTicks: {
            show: true,
            borderType: 'solid',
            color: '#78909C',
            width: 6,
            offsetX: 0,
            offsetY: 0
          },

        },
      },
      yaxis: {
        labels: {
          offsetX: -15
        },
      },
      markers: {
        size: 5,
        colors: ['#1E90FF', '#00BFFF'],
        strokeColors: '#fff',
        strokeWidth: 2,
        hover: {
          size: 7
        }
      },
      colors: ['#3D5EE1', '#6FCCD8'], // Color for the lines
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.7,
          opacityTo: 0.9,
          stops: [0, 90, 100]
        }
      },
      legend: {
        position: 'bottom',
        horizontalAlign: 'center'
      }
    }
    var chart = new ApexCharts(document.querySelector("#performance_chart"), options);
    chart.render();
  }

  // Plan Chart

  if ($('#plan_chart').length > 0) {
    var donutChart = {
      chart: {
        height: 90,
        type: 'donut',
        toolbar: {
          show: false,
        },
        sparkline: {
          enabled: true
        }
      },
      grid: {
        show: false,
        padding: {
          left: 0,
          right: 0
        }
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '50%'
        },
      },
      dataLabels: {
        enabled: false
      },

      series: [95, 5],
      labels: [
        'Completed',
        'Pending'

      ],
      legend: { show: false },
      colors: ['#3D5EE1', '#E82646'],
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 100
          },
          legend: {
            position: 'bottom'
          }
        }
      }],
      legend: {
        position: 'bottom'
      }
    }

    var donut = new ApexCharts(
      document.querySelector("#plan_chart"),
      donutChart
    );

    donut.render();
  }

  if ($('#statistic_chart').length > 0) {
    var options = {
      chart: {
        type: 'line',
        height: 345,
      },
      series: [{
        name: 'Avg. Exam Score',
        data: [0, 32, 40, 50, 60, 52, 50, 44, 40, 60, 75, 70] // Sample data
      }, {
        name: 'Avg. Attendance',
        data: [0, 35, 43, 34, 30, 28, 25, 50, 60, 75, 77, 80] // Sample data
      }],
      xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
      },
      tooltip: {
        y: {
          formatter: function (val) {
            return val + "%";
          }
        },
        shared: true,
        intersect: false,
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
          return `<div class="apexcharts-tooltip">${w.globals.labels[dataPointIndex]}<br>Exam Score: <span style="color: #1E90FF;">${series[0][dataPointIndex]}%</span><br>Attendance: <span style="color: #00BFFF;">${series[1][dataPointIndex]}%</span></div>`;
        }
      },
      dataLabels: {
        enabled: false
      },
      grid: {
        yaxis: {
          lines: {
            show: true
          }
        },
      },
      yaxis: {
        labels: {
          offsetX: -15
        },
      },
      grid: {
        padding: {
          left: -8,
        },
      },
      markers: {
        size: 0,
        colors: ['#1E90FF', '#00BFFF'],
        strokeColors: '#fff',
        strokeWidth: 1,
        hover: {
          size: 7
        }
      },
      colors: ['#3D5EE1', '#6FCCD8'], // Color for the lines
      legend: {
        position: 'top',
        horizontalAlign: 'left'
      }
    }
    var chart = new ApexCharts(document.querySelector("#statistic_chart"), options);
    chart.render();
  }

  if ($('#attendance_chart2').length > 0) {
    var donutChart = {
      chart: {
        height: 290,
        type: 'donut',
        toolbar: {
          show: false,
        }
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '50%'
        },
      },
      dataLabels: {
        enabled: false
      },

      series: [60, 5, 15, 20],
      labels: [
        'Present',
        'Late',
        'Half Day',
        'Absent'
      ],
      colors: ['#1ABE17', '#1170E4', '#E9EDF4', '#E82646'],
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 200
          },
          legend: {
            position: 'left'
          }
        }
      }],
      legend: {
        position: 'left',
      }
    }

    var donut = new ApexCharts(
      document.querySelector("#attendance_chart2"),
      donutChart
    );

    donut.render();
  }

  // Total Earning
  if ($('#total-earning').length > 0) {
    var sLineArea = {
      chart: {
        height: 90,
        type: 'area',
        toolbar: {
          show: false,
        },
        sparkline: {
          enabled: true
        }
      },
      colors: ['#3D5EE1'],
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'straight'
      },
      series: [{
        name: 'Earnings',
        data: [50, 60, 40, 50, 45, 55, 50]
      }]
    }

    var chart = new ApexCharts(
      document.querySelector("#total-earning"),
      sLineArea
    );

    chart.render();
  }

  // Total Expenses
  if ($('#total-expenses').length > 0) {
    var sLineArea = {
      chart: {
        height: 90,
        type: 'area',
        toolbar: {
          show: false,
        },
        sparkline: {
          enabled: true
        }
      },
      colors: ['#E82646'],
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'straight'
      },
      series: [{
        name: 'Earnings',
        data: [40, 20, 60, 55, 50, 55, 40]
      }]
    }

    var chart = new ApexCharts(
      document.querySelector("#total-expenses"),
      sLineArea
    );

    chart.render();
  }

});

// Donut Chart

if ($('#storage-chart').length > 0) {
  var donutChart = {
    chart: {
      height: 200,
      type: 'donut',
      toolbar: {
        show: false,
      },
      offsetY: -10,
      events: {
        rendered: function () {
          // Adding the center text
          var chartElement = document.querySelector("#donutChart");
          var innerText = '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">' +
            '<span style="font-size: 24px; font-weight: bold;">Total</span><br>' +
            '<span style="font-size: 16px;">abb</span>' +
            '</div>';
          chartElement.innerHTML += innerText;
        }
      },
    },
    plotOptions: {
      pie: {
        startAngle: -100,
        endAngle: 100,
        donut: {
          size: '80%',
          labels: {
            show: true,
            name: {
              show: true,
            }
          }
        }
      }
    },
    dataLabels: {
      enabled: false
    },
    legend: {
      show: false
    },
    stroke: {
      show: false
    },
    colors: ['#0C4B5E', '#FFC107', '#1B84FF', '#AB47BC', '#FD3995'],
    series: [20, 20, 20, 20, 20],
    labels: ['Documents', 'Video', 'Music', 'Photos', 'Other'],
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 200
        },
        legend: {
          position: 'bottom'
        }
      }
    }],
    grid: {
      padding: {
        bottom: -60  // Reduce padding from the bottom
      }
    }
  }

  var donut = new ApexCharts(
    document.querySelector("#storage-chart"),
    donutChart
  );

  donut.render();
}

// Total Company

if ($('#total-chart').length > 0) {

  var options = {
    series: [{
      name: "Messages",
      data: [25, 66, 41, 12, 36, 9, 21]
    }],
    fill: {
      type: 'gradient',
      gradient: {
        opacityFrom: 0, // Start with 0 opacity (transparent)
        opacityTo: 0    // End with 0 opacity (transparent)
      }
    },
    chart: {
      foreColor: '#fff',
      type: "area",
      width: 50,
      toolbar: {
        show: !1
      },
      zoom: {
        enabled: !1
      },
      dropShadow: {
        enabled: 0,
        top: 3,
        left: 14,
        blur: 4,
        opacity: .12,
        color: "#fff"
      },
      sparkline: {
        enabled: !0
      }
    },
    markers: {
      size: 0,
      colors: ["#F26522"],
      strokeColors: "#fff",
      strokeWidth: 2,
      hover: {
        size: 7
      }
    },
    plotOptions: {
      bar: {
        horizontal: !1,
        columnWidth: "35%",
        endingShape: "rounded"
      }
    },
    dataLabels: {
      enabled: !1
    },
    stroke: {
      show: !0,
      width: 2.5,
      curve: "smooth"
    },
    colors: ["#F26522"],
    xaxis: {
      categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep"]
    },
    tooltip: {
      theme: "dark",
      fixed: {
        enabled: !1
      },
      x: {
        show: !1
      },
      y: {
        title: {
          formatter: function (e) {
            return ""
          }
        }
      },
      marker: {
        show: !1
      }
    }
  };

  var chart = new ApexCharts(document.querySelector("#active-chart"), options);
  chart.render();
}

// Active Company

if ($('#active-chart').length > 0) {

  var options = {
    series: [{
      name: "Active Company",
      data: [25, 40, 35, 20, 36, 9, 21]
    }],
    fill: {
      type: 'gradient',
      gradient: {
        opacityFrom: 0, // Start with 0 opacity (transparent)
        opacityTo: 0    // End with 0 opacity (transparent)
      }
    },
    chart: {
      foreColor: '#fff',
      type: "area",
      width: 50,
      toolbar: {
        show: !1
      },
      zoom: {
        enabled: !1
      },
      dropShadow: {
        enabled: 0,
        top: 3,
        left: 14,
        blur: 4,
        opacity: .12,
        color: "#fff"
      },
      sparkline: {
        enabled: !0
      }
    },
    markers: {
      size: 0,
      colors: ["#F26522"],
      strokeColors: "#fff",
      strokeWidth: 2,
      hover: {
        size: 7
      }
    },
    plotOptions: {
      bar: {
        horizontal: !1,
        columnWidth: "35%",
        endingShape: "rounded"
      }
    },
    dataLabels: {
      enabled: !1
    },
    stroke: {
      show: !0,
      width: 2.5,
      curve: "smooth"
    },
    colors: ["#F26522"],
    xaxis: {
      categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep"]
    },
    tooltip: {
      theme: "dark",
      fixed: {
        enabled: !1
      },
      x: {
        show: !1
      },
      y: {
        title: {
          formatter: function (e) {
            return ""
          }
        }
      },
      marker: {
        show: !1
      }
    }
  };

  var chart = new ApexCharts(document.querySelector("#total-chart"), options);
  chart.render();
}

// Inactive Company

if ($('#inactive-chart').length > 0) {

  var options = {
    series: [{
      name: "Inactive Company",
      data: [25, 10, 35, 5, 25, 28, 21]
    }],
    fill: {
      type: 'gradient',
      gradient: {
        opacityFrom: 0, // Start with 0 opacity (transparent)
        opacityTo: 0    // End with 0 opacity (transparent)
      }
    },
    chart: {
      foreColor: '#fff',
      type: "area",
      width: 50,
      toolbar: {
        show: !1
      },
      zoom: {
        enabled: !1
      },
      dropShadow: {
        enabled: 0,
        top: 3,
        left: 14,
        blur: 4,
        opacity: .12,
        color: "#fff"
      },
      sparkline: {
        enabled: !0
      }
    },
    markers: {
      size: 0,
      colors: ["#F26522"],
      strokeColors: "#fff",
      strokeWidth: 2,
      hover: {
        size: 7
      }
    },
    plotOptions: {
      bar: {
        horizontal: !1,
        columnWidth: "35%",
        endingShape: "rounded"
      }
    },
    dataLabels: {
      enabled: !1
    },
    stroke: {
      show: !0,
      width: 2.5,
      curve: "smooth"
    },
    colors: ["#F26522"],
    xaxis: {
      categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep"]
    },
    tooltip: {
      theme: "dark",
      fixed: {
        enabled: !1
      },
      x: {
        show: !1
      },
      y: {
        title: {
          formatter: function (e) {
            return ""
          }
        }
      },
      marker: {
        show: !1
      }
    }
  };

  var chart = new ApexCharts(document.querySelector("#inactive-chart"), options);
  chart.render();
}

// Location Company

if ($('#location-chart').length > 0) {

  var options = {
    series: [{
      name: "Inactive Company",
      data: [30, 40, 15, 23, 20, 23, 25]
    }],
    fill: {
      type: 'gradient',
      gradient: {
        opacityFrom: 0, // Start with 0 opacity (transparent)
        opacityTo: 0    // End with 0 opacity (transparent)
      }
    },
    chart: {
      foreColor: '#fff',
      type: "area",
      width: 50,
      toolbar: {
        show: !1
      },
      zoom: {
        enabled: !1
      },
      dropShadow: {
        enabled: 0,
        top: 3,
        left: 14,
        blur: 4,
        opacity: .12,
        color: "#fff"
      },
      sparkline: {
        enabled: !0
      }
    },
    markers: {
      size: 0,
      colors: ["#F26522"],
      strokeColors: "#fff",
      strokeWidth: 2,
      hover: {
        size: 7
      }
    },
    plotOptions: {
      bar: {
        horizontal: !1,
        columnWidth: "35%",
        endingShape: "rounded"
      }
    },
    dataLabels: {
      enabled: !1
    },
    stroke: {
      show: !0,
      width: 2.5,
      curve: "smooth"
    },
    colors: ["#F26522"],
    xaxis: {
      categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep"]
    },
    tooltip: {
      theme: "dark",
      fixed: {
        enabled: !1
      },
      x: {
        show: !1
      },
      y: {
        title: {
          formatter: function (e) {
            return ""
          }
        }
      },
      marker: {
        show: !1
      }
    }
  };

  var chart = new ApexCharts(document.querySelector("#location-chart"), options);
  chart.render();
}

if ($('#expense-analysis').length > 0) {
  var options = {
    series: [{
      name: "Sales Analysis",
      data: [10, 30, 18, 15, 22, 30, 40, 50, 40, 40, 60, 70]
    }],
    chart: {
      height: 190,
      type: 'area',
      zoom: {
        enabled: false
      }
    },
    colors: ['#FF9F43'],
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'smooth'
    },
    title: {
      text: '',
      align: 'left'
    },
    // grid: {
    //   row: {
    //     colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
    //     opacity: 0.5
    //   },
    // },
    xaxis: {
      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
    },
    yaxis: {
      min: 10,
      max: 60,
      tickAmount: 5,
      labels: {
        offsetX: -15,
        formatter: (val) => {
          return val / 1 + 'K'
        }
      }
    },
    legend: {
      position: 'top',
      horizontalAlign: 'left'
    }
  };

  var chart = new ApexCharts(document.querySelector("#expense-analysis"), options);
  chart.render();
}

if ($('#invoice-report').length > 0) {
  var options = {
    series: [{
      name: 'Total Invoices',
      data: [40, 30, 40, 30, 40, 30]
    }, {
      name: 'Paid Invoices',
      data: [20, 10, 20, 10, 20, 10]
    }],
    chart: {
      height: 250,
      type: 'area'
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'straight'
    },
    xaxis: {
      type: 'category',
      categories: ["January", "February", "March", "April", "May", "June", "July"]
    },
    yaxis: {
      labels: {
        offsetX: -15,
        formatter: function (value) {
          return value + "k"; // Display values with 'k' suffix
        }
      },
    },
    tooltip: {
      x: {
        formatter: function (value) {
          return value; // Tooltip shows month labels
        }
      },
      y: {
        formatter: function (value) {
          return value + "k"; // Tooltip shows amounts with 'k' suffix
        }
      }
    },
    colors: ['#FD3995','#FF9F43'],
    dataLabels: {
      enabled: false
    },
  };

  // Create the chart instance
  var chart = new ApexCharts(document.querySelector("#invoice-report"), options);
  chart.render();
}

if($('#payment-report').length > 0 ){
  var options = {
    series: [44, 55, 41, 17],
    chart: {
      type: 'donut',
    },
    colors: ['#0DCAF0', '#FD3995', '#AB47BC', '#FFC107'],
    labels: ['Paypal', 'Debit Card', 'Bank Transfer', 'Credit Card'],
    plotOptions: {
      pie: {
        startAngle: -90,
        endAngle: 270,
        stroke: {
          show: true,
          width: 10, // Width of the gap
          colors: ['#FFFFFF'] // Color of the gap
        },
        donut: {
          size: '80%' // Adjusts the size of the donut hole
        }
      }
    },
    dataLabels: {
      enabled: false
    },
    legend: {
      show: false // Set this to false to hide the legend
    },
    annotations: {
      position: 'front', // Ensure it appears above other elements
      style: {
        fontSize: '24px', // Adjust font size
        fontWeight: 'bold',
        color: '#000000' // Change color if needed
      },
      text: {
        // Set the annotation text
        text: '+14%',
        // Optional styling for the text box
        background: {
          enabled: true,
          foreColor: '#FFFFFF', // Text color
          border: '#000000', // Border color
          borderWidth: 1,
          borderRadius: 2,
          opacity: 0.7
        }
      },
      x: '50%', // Center horizontally
      y: '50%', // Center vertically
    },
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 200
        },
        legend: {
          show: false // Also hide legend on smaller screens
        }
      }
    }]
  };

  var chart = new ApexCharts(document.querySelector("#payment-report"), options);
  chart.render();
}

if($('#task-reports').length > 0 ){
  var options = {
    series: [40, 30, 20, 10],
    chart: {
      type: 'donut',
      width: 220,
    },
    colors: ['#03C95A', '#0DCAF0', '#FFC107', '#AB47BC'],
    labels: ['Completed ', 'Pending', 'Inprogress ', 'On Hold '],
    plotOptions: {
      pie: {
        startAngle: -90,
        endAngle: 270,
        stroke: {
          show: true,
          width: 10, // Width of the gap
          colors: ['#FFFFFF'] // Color of the gap
        },
        donut: {
          size: '80%' // Adjusts the size of the donut hole
        }
      }
    },
    dataLabels: {
      enabled: false
    },
    legend: {
      show: false // Set this to false to hide the legend
    },
    annotations: {
      position: 'front', // Ensure it appears above other elements
      style: {
        fontSize: '24px', // Adjust font size
        fontWeight: 'bold',
        color: '#000000' // Change color if needed
      },
      text: {
        // Set the annotation text
        text: '+14%',
        // Optional styling for the text box
        background: {
          enabled: true,
          foreColor: '#FFFFFF', // Text color
          border: '#000000', // Border color
          borderWidth: 1,
          borderRadius: 2,
          opacity: 0.7
        }
      },
      x: '50%', // Center horizontally
      y: '50%', // Center vertically
    },
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 200
        },
        legend: {
          show: false // Also hide legend on smaller screens
        }
      }
    }]
  };

  var chart = new ApexCharts(document.querySelector("#task-reports"), options);
  chart.render();
}


if($('#project-report').length > 0 ){
  var options = {
    series: [30, 10, 20, 40],
    chart: {
      width: 280,
      type: 'pie',
    },
    labels: ['Pending', 'On Hold', 'In Progress', 'Completed'], // Set your labels here
    colors: ['#0DCAF0', '#AB47BC', '#FFC107', '#03C95A'], // Custom colors for each segment
    dataLabels: {
      enabled: false // Disable data labels to remove numbers
    },
    legend: {
      show: false // Hide the legend
    },
    tooltip: {
      y: {
        formatter: function(value, { seriesIndex }) {
          return 'Value: ' + value; // Customize the tooltip text
        }
      }
    },
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 200
        },
        legend: {
          position: 'bottom'
        }
      }
    }]
  };

  var chart = new ApexCharts(document.querySelector("#project-report"), options);
  chart.render();
}

if ($('#employee-reports').length > 0) {
  var options = {
    series: [{
      name: 'Active Employees',
      data: [50, 55, 57, 56, 61, 58, 63, 60, 66]
    }, {
      name: 'Inactive Employees',
      data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
    }],
    chart: {
      type: 'bar',
      height: 180
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '55%',
        endingShape: 'rounded'
      }
    },
    colors: ['#03C95A', '#E8E9EA'], // Active Employees - Green, Inactive Employees - Gray
    dataLabels: {
      enabled: false, // Disable data labels
    },
    stroke: {
      show: true,
      width: 2,
      colors: ['transparent']
    },
    xaxis: {
      categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct']
    },yaxis: {
      labels: {
        offsetX: -15,
      }
    },
    fill: {
      opacity: 1
    },
    legend: {
      show: false
    },
    tooltip: {
      y: {
        formatter: function (val) {
          return "$ " + val + " thousands";
        }
      }
    }
  };

  var chart = new ApexCharts(document.querySelector("#employee-reports"), options);
  chart.render();
}

if ($('#attendance-report').length > 0) {
  var options = {
      series: [{
          name: "Present",
          data: [30, 65, 70, 75, 80, 95, 100, 70, 65] // Example data for Present
      }, {
          name: "Absent",
          data: [30, 55, 60, 65, 50, 70, 80, 60, 70] // Example data for Absent
      }],
      chart: {
          height: 200, // Change height here
          type: 'line',
          zoom: {
              enabled: false
          }
      },
      dataLabels: {
          enabled: false
      },
      stroke: {
          curve: 'smooth' // Change to 'smooth' for a nicer appearance
      },
      grid: {
          row: {
              colors: ['#f3f3f3', 'transparent'], // alternating row colors
              opacity: 0.5
          },
      },
      xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
      },yaxis: {
        labels: {
          offsetX: -15,
        }
      },
      colors: ['#28a745', '#ff69b4'] // Green for Present, Pink for Absent
  };

  var chart = new ApexCharts(document.querySelector("#attendance-report"), options);
  chart.render();
}

if ($('#leave-report').length > 0) {
  var options = {
    series: [{
      name: 'Annual Leave',
      data: [30, 40, 35, 50,50,60,30, 40, 35, 50,50,60] // Replace with your data
    }, {
      name: 'Casual Leave',
      data: [20, 30, 25, 40,50,60,20, 30, 25, 40,50,60] // Replace with your data
    }, {
      name: 'Medical Leave',
      data: [15, 10, 20, 15,50,60,15, 10, 20, 15,50,60] // Replace with your data
    }, {
      name: 'Others',
      data: [25, 20, 30, 35,50,60,25, 20, 30, 35,50,60] // Replace with your data
    }, 
  ],
    chart: {
      type: 'bar',
      height: 210, // Change this value to your desired height
      stacked: true,
      stackType: '100%'
    },
    responsive: [{
      breakpoint: 480,
      options: {
        legend: {
          position: 'bottom',
          offsetX: -10,
          offsetY: 0
        }
      }
    }],
    xaxis: {
      categories: ['Jan', 'Feb', 'Mar', 'Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec',] // Update to match your time frame
    },
    yaxis: {
      labels: {
        offsetX: -15,
      }
    },
    fill: {
      opacity: 1
    },
    legend: {
     show: false
    },
    colors: ['#03C95A', '#FFC107', '#0C4B5E', '#F26522'], // Set your colors here
    dataLabels: {
      enabled: false // Disable data labels
    }
  };

  var chart = new ApexCharts(document.querySelector("#leave-report"), options);
  chart.render();
}

if ($('#daily-report').length > 0) {
  var options = {
    series: [{
      name: "Present",
      data: [60, 40, 30, 20, 70, ] // Sample data for Present
    }, {
      name: "Absent",
      data: [20, 60, 45, 60, 80,] // Sample data for Absent
    }],
    chart: {
      height: 130, // Changed height
      type: 'line',
      zoom: {
        enabled: false
      }
    },
    dataLabels: {
      enabled: false
    },
    legend: {
      show: false
     },
    stroke: {
      curve: 'smooth' // You can change this to 'straight' if preferred
    },
    grid: {
      row: {
        colors: ['#f3f3f3', 'transparent'],
        opacity: 0.5
      }
    },
    xaxis: {
      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
    },
    yaxis: {
      labels: {
        offsetX: -15,
      }
    },
    colors: ['#4CAF50', '#F44336'] // Green for Present, Red for Absent
  };

  var chart = new ApexCharts(document.querySelector("#daily-report"), options);
  chart.render();
}

if ($('#revenue-chart').length > 0) {
  var sCol = {
      chart: {
          height: 200,
          type: 'bar',
          stacked: true,
          toolbar: {
            show: false,
          }
      },
      plotOptions: {
          bar: {
              horizontal: false,
              columnWidth: '50%',
              endingShape: 'rounded'  
          },
      },
      dataLabels: {
          enabled: false
      },
      stroke: {
          show: true,
          width: 1,
      },
      series: [{
           name: 'High',
          color: '#0E82FD',
          data: [50,40,15,45,35,48,65]
      }],
      xaxis: {
          categories: ['M','T', 'W', 'T','F','S','S'],
      },
      tooltip: {
          y: {
              formatter: function (val) {
                  return "$ " + val + "k"
              }
          }
      }
  }
  
  var chart = new ApexCharts(
      document.querySelector("#revenue-chart"),
      sCol
  );
  
  chart.render();
}

// Employee Department

if ($('#emp-department').length > 0) {
  var sBar = {
    chart: {
      height: 220,
      type: 'bar',
      padding: {
        top: 0,
        left: 0,
        right: 0,
        bottom: 0
      },
      toolbar: {
        show: false,
      }
    },
    colors: ['#FF6F28'],
    grid: {
      borderColor: '#E5E7EB',
      strokeDashArray: 5,
      padding: {
        top: -20,
        left: 0,   
        right: 0,
        bottom: 0
      }
    },
    plotOptions: {
      bar: {
        borderRadius: 5,
        horizontal: true,
        barHeight: '35%',
        endingShape: 'rounded'
      }
    },
    dataLabels: {
      enabled: false
    },
    series: [{
      data: [80, 110, 80, 20, 60, 100],
      name: 'Employee'
    }],
    xaxis: {
      categories: ['UI/UX', 'Development', 'Management', 'HR', 'Testing', 'Marketing'],
      labels: {
        style: {
          colors: '#111827', 
          fontSize: '13px',
        }
      }
    }
  }

  var chart = new ApexCharts(
    document.querySelector("#emp-department"),
    sBar
  );

  chart.render();
}

// Company Chart

if ($('#company-chart').length > 0) {
  var sColStacked = {
    chart: {
      height: 240,
      type: 'bar',
      toolbar: {
        show: false,
      }
    },
    colors: ['#212529'],
    responsive: [{
      breakpoint: 480,
      options: {
        legend: {
          position: 'bottom',
          offsetX: -10,
          offsetY: 0
        }
      }
    }],
    plotOptions: {
      bar: {
        borderRadius: 10, 
        borderRadiusWhenStacked: 'all',
        horizontal: false,
        endingShape: 'rounded',
        colors: {
          backgroundBarColors: ['#f3f4f5'], // Background color for bars
          backgroundBarOpacity: 0.5,
          hover: {
            enabled: true,
            borderColor: '#F26522', // Color when hovering over the bar
          }
        }
      },
    },
    series: [{
      name: 'Company',
      data: [40, 60, 20, 80, 60, 60, 60]
    }],
    xaxis: {
      categories: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
      labels: {
        style: {
          colors: '#6B7280', 
          fontSize: '13px',
        }
      }
    },
    yaxis: {
      labels: {
        offsetX: -15,
        show: false 
      }
    },
    grid: {
      borderColor: '#E5E7EB',
      strokeDashArray: 5,
      padding: {
        left: -8,
      },
    },
    legend: {
      show: false
    },
    dataLabels: {
      enabled: false // Disable data labels
    },
    fill: {
      opacity: 1
    },
  }

  var chart = new ApexCharts(
    document.querySelector("#company-chart"),
    sColStacked
  );

  chart.render();
}

// Plan Chart

if ($('#plan-overview').length > 0) {
  var donutChart = {
    chart: {
      height: 240,
      type: 'donut',
      toolbar: {
        show: false,
      }
    },
    colors: ['#FFC107', '#1B84FF', '#F26522'],
    series: [20, 60, 20],
    labels: ['Enterprise', 'Premium', 'Basic'],
    plotOptions: {
      pie: {
        donut: {
          size: '50%',
          labels: {
            show: false
          },
          borderRadius: 30
        }
      }
    },
    stroke: {
      lineCap: 'round',
      show: true,
      width: 0,    // Space between donut sections
      colors: '#fff'
    },
    dataLabels: {
      enabled: false
    },
    legend: { show: false },
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          height: 180,
        },
        legend: {
          position: 'bottom'
        }
      }
    }]
  }

  var donut = new ApexCharts(
    document.querySelector("#plan-overview"),
    donutChart
  );

  donut.render();
}

  // sales income

  if ($('#sales-income').length > 0) {
    var sColStacked = {
      chart: {
        height: 290,
        type: 'bar',
        stacked: true,
        toolbar: {
          show: false,
        }
      },
      colors: ['#FF6F28', '#F8F9FA'],
      responsive: [{
        breakpoint: 480,
        options: {
          legend: {
            position: 'bottom',
            offsetX: -10,
            offsetY: 0
          }
        }
      }],
      plotOptions: {
        bar: {
          borderRadius: 5, 
          borderRadiusWhenStacked: 'all',
          horizontal: false,
          endingShape: 'rounded'
        },
      },
      series: [{
        name: 'Income',
        data: [40, 30, 45, 80, 85, 90, 80, 80, 80, 85, 20, 80]
      }, {
        name: 'Expenses',
        data: [60, 70, 55, 20, 15, 10, 20, 20, 20, 15, 80, 20]
      }],
      xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct','Nov', 'Dec'],
        labels: {
          style: {
            colors: '#6B7280', 
            fontSize: '13px',
          }
        }
      },
      yaxis: {
        labels: {
          offsetX: -15,
          style: {
            colors: '#6B7280', 
            fontSize: '13px',
          }
        }
      },
      grid: {
        borderColor: '#E5E7EB',
        strokeDashArray: 5,
        padding: {
          left: -8,
        },
      },
      legend: {
        show: false
      },
      dataLabels: {
        enabled: false // Disable data labels
      },
      fill: {
        opacity: 1
      },
    }

    var chart = new ApexCharts(
      document.querySelector("#sales-income"),
      sColStacked
    );

    chart.render();
  }

  // Performance Chart
  if($('#performance_chart2').length > 0 ){
    var options = {
      series: [{
        name: "performance",
        data: [20, 20, 35, 35, 40, 60, 60]
    }],
      chart: {
      height: 273,
      type: 'area',
      zoom: {
        enabled: false
      }
    },
    colors: ['#03C95A'],
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'straight'
    },
    title: {
      text: '',
      align: 'left'
    },
    // grid: {
    //   row: {
    //     colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
    //     opacity: 0.5
    //   },
    // },
    xaxis: {
      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
    },
    yaxis: {
      min: 10,
      max: 60,
      tickAmount: 5,
          labels: {
            formatter: (val) => {
              return val / 1 + 'K'
            }
          }
        },
        legend: {
          position: 'top',
          horizontalAlign: 'left'
        }
    };

    var chart = new ApexCharts(document.querySelector("#performance_chart2"), options);
    chart.render();
  }

  // Deals Stage

  if ($('#deals_stage').length > 0) {
    var sColStacked = {
      chart: {
        height: 310,
        type: 'bar',
        stacked: true,
        toolbar: {
          show: false,
        }
      },
      colors: ['#FF6F28', '#F8F9FA'],
      responsive: [{
        breakpoint: 480,
        options: {
          legend: {
            position: 'bottom',
            offsetX: -10,
            offsetY: 0
          }
        }
      }],
      plotOptions: {
        bar: {
          borderRadius: 5, 
          horizontal: false,
          endingShape: 'rounded'
        },
      },
      series: [{
        name: 'Income',
        data: [80, 40, 100, 20]
      }, {
        name: 'Expenses',
        data: [100, 100, 100, 100]
      }],
      xaxis: {
        categories: ['Inpipeline', 'Follow Up', 'Schedule', 'Conversion'],
        labels: {
          style: {
            colors: '#6B7280', 
            fontSize: '13px',
          }
        }
      },
      yaxis: {
        labels: {
          offsetX: -15,
          style: {
            colors: '#6B7280', 
            fontSize: '13px',
          }
        }
      },
      grid: {
        borderColor: '#E5E7EB',
        strokeDashArray: 5
      },
      legend: {
        show: false
      },
      dataLabels: {
        enabled: false // Disable data labels
      },
      fill: {
        opacity: 1
      },
    }

    var chart = new ApexCharts(
      document.querySelector("#deals_stage"),
      sColStacked
    );

    chart.render();
  }
  if ($('#top_deal_chart').length > 0) {
  var options = {
    series: [{
    name: 'Series 1',
    data: [80, 50, 30, 40, 100, 20],
    color: '#F37438',
    
  }, {
    name: 'Series 2',
    data: [20, 30, 40, 80, 20, 80],
    color: '#B359C3',
  }, {
    name: 'Series 3',
    data: [44, 76, 78, 13, 43, 10],
    color: '#1CCE6B',
  }],
    chart: {
    height: 200,
    type: 'radar',
    dropShadow: {
      enabled: true,
      blur: 1,
      left: 1,
      top: 1
    }
  },
  plotOptions: {
    radar: {
      spiderWeb: false
    }
  },
  stroke: {
    width: 2,
    curve: 'smooth' 
  },
  fill: {
    opacity: 1
  },
  markers: {
    size: 0
  },
  yaxis: {
    stepSize: 20
  },
  legend: {
    show: false,
  },
  xaxis: {
    categories: ['Mar', 'Feb', 'Jan', 'Aug', 'Jul', 'Jun' , 'May' , 'Apr']
  }
  };

  var chart = new ApexCharts(document.querySelector("#top_deal_chart"), options);
  chart.render();
}

  // Pipeline Chart
  if ($('#pipeline_chart').length > 0) {
  var options = {
    series: [
    {
      name: "",
      data: [1380, 1100, 990, 880, 740, 540],
    },
  ],
    chart: {
    type: 'bar',
    height: 280,
  },
  plotOptions: {
    bar: {
      borderRadius: 0,
      horizontal: true,
      distributed: true,
      barHeight: '80%',
      isFunnel: true,
    },
  },
  colors: [
    '#F26522',
    '#F37438',
    '#F5844E',
    '#F69364',
    '#F7A37A',
    '#F9B291'
  ],
  dataLabels: {
    enabled: true,
    formatter: function (val, opt) {
      return opt.w.globals.labels[opt.dataPointIndex] 
    },
    dropShadow: {
      enabled: true,
    },
  },
  title: {
    align: 'top',
  },
  xaxis: {
    categories: ['Marketing : 7,898', 'Sales : 4658', 'Email : 2898', 'Chat : 789', 'Operational : 655', 'Calls : 454'],
  },
  legend: {
    show: false,
  },
  };

  var chart = new ApexCharts(document.querySelector("#pipeline_chart"), options);
  chart.render();
}

  // Leads Stage

  if ($('#leads_stage').length > 0) {
    var sColStacked = {
      chart: {
        height: 355,
        type: 'bar',
        stacked: true,
        toolbar: {
          show: false,
        }
      },
      colors: ['#FF6F28', '#F8F9FA'],
      responsive: [{
        breakpoint: 480,
        options: {
          legend: {
            position: 'bottom',
            offsetX: -10,
            offsetY: 0
          }
        }
      }],
      plotOptions: {
        bar: {
          borderRadius: 5, 
          borderRadiusWhenStacked: 'all',
          horizontal: false,
          endingShape: 'rounded'
        },
      },
      series: [{
        name: 'Income',
        data: [80, 40, 60, 40]
      }, {
        name: 'Expenses',
        data: [100, 100, 100, 100]
      }],
      xaxis: {
        categories: ['Competitor', 'Budget', 'Unresponsie', 'Timing'],
        labels: {
          style: {
            colors: '#6B7280', 
            fontSize: '9px',
          }
        }
      },
      yaxis: {
        labels: {
          offsetX: -15,
          style: {
            colors: '#6B7280', 
            fontSize: '10px',
          }
        }
      },
      grid: {
        borderColor: '#E5E7EB',
        strokeDashArray: 5
      },
      legend: {
        show: false
      },
      dataLabels: {
        enabled: false // Disable data labels
      },
      fill: {
        opacity: 1
      },
    }

    var chart = new ApexCharts(
      document.querySelector("#leads_stage"),
      sColStacked
    );

    chart.render();
  }

  if ($('#donut-chart-2').length > 0) {
  var options = {
    series: [25, 30, 10, 35], // Percentages for each section
    chart: {
        type: 'donut',
        height: 185,
    },
    labels: ['Paid', 'Google', 'Referals', 'Campaigns'], // Labels for the data
    colors: ['#FFC107', '#0C4B5E', '#AB47BC', '#FD3995'], // Colors from the image
    plotOptions: {
        pie: { 
            donut: {
                size: '60%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Google',
                        formatter: function (w) {
                            return '40%';
                        }
                    }
                }
            }
        }
    },
    stroke: {
   
  lineCap: 'round',
  show: true,
  width: 0,    // Space between donut sections
  colors: '#fff'
  },
    legend: {
      show: false,
    },
    dataLabels: {
      enabled: false
    },
    label: {
      show: false,
    }
};

var chart = new ApexCharts(document.querySelector("#donut-chart-2"), options);
chart.render();
 }

 if ($('#donut-chart-3').length > 0) {
  var options = {
    series: [15, 10, 5, 10, 60], // Percentages for each section
    chart: {
        type: 'donut',
        height: 290,
    },
    labels: ['Paid', 'Google', 'Referals', 'Campaigns' , 'Campaigns'], // Labels for the data
    colors: ['#F26522', '#FFC107', '#E70D0D', '#1B84FF' , '#0C4B5E'], // Colors from the image
    plotOptions: {
        pie: {
            donut: {
                size: '60%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Leads',
                        formatter: function (w) {
                            return '589';
                        }
                    }
                }
            }
        }
    },
    dataLabels: {
      enabled: false
    },
    legend: {
      show: false,
    },
    label: {
      show: false,
    }
};

var chart = new ApexCharts(document.querySelector("#donut-chart-3"), options);
chart.render();
 }

 // Revenue income

 if ($('#revenue-income').length > 0) {
  var sColStacked = {
    chart: {
      height: 230,
      type: 'bar',
      stacked: true,
      toolbar: {
        show: false,
      }
    },
    colors: ['#FF6F28', '#F8F9FA'],
    responsive: [{
      breakpoint: 480,
      options: {
        legend: {
          position: 'bottom',
          offsetX: -10,
          offsetY: 0
        }
      }
    }],
    plotOptions: {
      bar: {
        borderRadius: 5, 
        borderRadiusWhenStacked: 'all',
        horizontal: false,
        endingShape: 'rounded'
      },
    },
    series: [{
      name: 'Income',
      data: [40, 30, 45, 80, 85, 90, 80, 80, 80, 85, 20, 80]
    }, {
      name: 'Expenses',
      data: [60, 70, 55, 20, 15, 10, 20, 20, 20, 15, 80, 20]
    }],
    xaxis: {
      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct','Nov', 'Dec'],
      labels: {
        style: {
          colors: '#6B7280', 
          fontSize: '13px',
        }
      }
    },
    yaxis: {
      min: 0,    // Set the minimum value of the Y-axis to 0
      max: 100,
      labels: {
        offsetX: -15,
        style: {
          colors: '#6B7280', 
          fontSize: '13px',
        },
        formatter: function (value) {
          return value + "K"; // Divide by 1000 and append 'K'
        }
      }
    },
    grid: {
      borderColor: 'transparent',
      strokeDashArray: 5,
      padding: {
        left: -8,
      },
    },
    legend: {
      show: false
    },
    dataLabels: {
      enabled: false 
    },
    tooltip: {
      y: {
        formatter: function (val) {
          return  val / 10 + " k"
        }
      }
    },
    fill: {
      opacity: 1
    },
  }

  var chart = new ApexCharts(
    document.querySelector("#revenue-income"),
    sColStacked
  );

  chart.render();
}


 if ($('#heat_chart').length > 0) {
  var options = {
    chart: {
      type: 'heatmap',
      height: 300,
  },
  colors: [
    "#9CA3AF",
    "#F37438",
    "#9CA3AF",
    "#F37438",
    "#9CA3AF",
    "#F37438",
  ],
    series: [
      {
        name: "0",
        data: [{
          x: 'Mon',
          y: 22
        },
        {
          x: 'Tue',
          y: 29
        }, 
        {
          x: 'Wed',
          y: 13
        }, 
        {
          x: 'Thu',
          y: 32
        },
        {
          x: 'Fri',
          y: 32
        },
        {
          x: 'Sat',
          y: 32
        },
        {
          x: 'Sun',
          y: 32
        },
      ]
      },
      {
        name: "20",
        data: [{
          x: 'Mon',
          y: 22,
          color: '#ff5722'
        },
        {
          x: 'Tue',
          y: 29
        }, 
        {
          x: 'Wed',
          y: 13
        }, 
        {
          x: 'Thu',
          y: 32
        },
        {
          x: 'Fri',
          y: 32
        },
        {
          x: 'Sat',
          y: 32
        },
        {
          x: 'Sun',
          y: 32
        },
      ]
      },
      {
        name: "40",
        data: [{
          x: 'Mon',
          y: 22
        },
        {
          x: 'Tue',
          y: 29
        }, 
        {
          x: 'Wed',
          y: 13
        }, 
        {
          x: 'Thu',
          y: 32
        },
        {
          x: 'Fri',
          y: 32
        },
        {
          x: 'Sat',
          y: 32
        },
        {
          x: 'Sun',
          y: 32
        },
      ]
      },
      {
        name: "60",
        data: [{
          x: 'Mon',
          y: 0
        },
        {
          x: 'Tue',
          y: 29
        }, 
        {
          x: 'Wed',
          y: 13
        }, 
        {
          x: 'Thu',
          y: 32
        },
        {
          x: 'Fri',
          y: 0
        },
        {
          x: 'Sat',
          y: 0
        },
        {
          x: 'Sun',
          y: 32
        },
      ]
      },
      {
        name: "80",
        data: [{
          x: 'Mon',
          y: 0
        },
        {
          x: 'Tue',
          y: 20
        }, 
        {
          x: 'Wed',
          y: 13
        }, 
        {
          x: 'Thu',
          y: 32
        },
        {
          x: 'Fri',
          y: 0
        },
        {
          x: 'Sat',
          y: 0
        },
        {
          x: 'Sun',
          y: 32
        },
      ]
      },
      {
        name: "120",
        data: [{
          x: 'Mon',
          y: 0
        },
        {
          x: 'Tue',
          y: 0
        }, 
        {
          x: 'Wed',
          y: 75
        }, 
        {
          x: 'Thu',
          y: 0
        },
        {
          x: 'Fri',
          y: 0
        },
        {
          x: 'Sat',
          y: 0
        },
        {
          x: 'Sun',
          y: 0
        },
      ]
      },
    ]
  };
  var chart = new ApexCharts(document.querySelector("#heat_chart"), options);
  chart.render();
}

if ($('#payslip-chart').length > 0) {
var options = {
  series: [{
  data: [22, 20, 30, 45, 55, 45, 20, 70, 25, 30, 10,30]
}],
  chart: {
  type: 'line',
  height: 200,
},
xaxis: {
  categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct','Nov', 'Dec'],
  labels: {
  }
},
stroke: {
  curve: 'stepline',
},
dataLabels: {
  enabled: false
},
markers: {
  hover: {
    sizeOffset: 4
  }
},
colors: ['#FF5733'],
};

var chart = new ApexCharts(document.querySelector("#payslip-chart"), options);
chart.render();
}

if ($('#user-chart').length > 0) {
var options = {
  series: [{
    name: 'Data',
    data: [34, 44, 54, 21, 12, 43, 33, 23, 66, 66, 58, 29] // Sample data for each month
  }],
  chart: {
    type: 'bar',
    height: 185
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '55%',
      endingShape: 'rounded'
    },
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    show: true,
    width: 2,
    colors: ['transparent']
  },
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], // Months
  },
  fill: {
    opacity: 1
  },
  tooltip: {
    y: {
      formatter: function (val) {
        return val + " units";
      }
    }
  },
  colors: ['#00E396'] // Bar color (green in this case)
};

var chart = new ApexCharts(document.querySelector("#user-chart"), options);
chart.render();
}