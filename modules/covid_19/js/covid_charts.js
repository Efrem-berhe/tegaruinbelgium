/**
 * @file
 * JavaScript integration between Google and Drupal.
 */

(function ($, googleCharts) {
  'use strict';
  /**
   * Helper function to draw a Google Chart.
   *
   * @param {string} chartId - Chart Id.
   * @param {string} chartType - Chart Type.
   * @param {string} dataTable - Data.
   * @param {string} googleChartOptions - Options.
   *
   * @return {function} Draw Chart.
   */
  googleCharts.drawChart = function (chartId, chartType, dataTable, googleChartOptions) {
    return function () {
      var data = google.visualization.arrayToDataTable(JSON.parse(dataTable));
      var options = JSON.parse(googleChartOptions);

      var googleChartTypeObject = JSON.parse(chartType);
      var googleChartTypeFormatted = googleChartTypeObject.type;
      var chart;

      switch (googleChartTypeFormatted) {
        case 'BarChart':
          chart = new google.visualization.BarChart(document.getElementById(chartId));
          break;
        case 'ColumnChart':
          chart = new google.visualization.ColumnChart(document.getElementById(chartId));
          break;
        case 'DonutChart':
          chart = new google.visualization.PieChart(document.getElementById(chartId));
          break;
        case 'PieChart':
          chart = new google.visualization.PieChart(document.getElementById(chartId));
          break;
        case 'ScatterChart':
          chart = new google.visualization.ScatterChart(document.getElementById(chartId));
          break;
        case 'AreaChart':
          chart = new google.visualization.AreaChart(document.getElementById(chartId));
          break;
        case 'LineChart':
          chart = new google.visualization.LineChart(document.getElementById(chartId));
          break;
        case 'SplineChart':
          chart = new google.visualization.LineChart(document.getElementById(chartId));
          break;
        case 'GaugeChart':
          chart = new google.visualization.Gauge(document.getElementById(chartId));
          break;
        case 'GeoChart':
          chart = new google.visualization.GeoChart(document.getElementById(chartId));
      }
      // Fix for https://www.drupal.org/project/charts/issues/2950654.
      // Would be interested in a different approach that allowed the default
      // colors to be applied first, rather than unsetting.
      if (options['colors'].length > 10) {
        for (var i in options) {
          if (i === 'colors') {
            delete options[i];
            break;
          }
        }
      }

      // Rewrite the colorAxis item to include the colors: key
      if (typeof options['colorAxis'] != 'undefined') {
        var num_colors = options['colorAxis'].length;
        var colors = options['colorAxis'];
        options['colorAxis'] = options['colorAxis'].splice(num_colors);
        options['colorAxis'] = {colors: colors};
      }
      options.vAxis = { 'gridlines': {'count': 10} };
      //options.hAxis = { 'gridlines': {'count': 7 , 'minSpacing': 60} };
      console.log(options);
      chart.draw(data, options);
    };
  };

}(jQuery, Drupal.googleCharts));
