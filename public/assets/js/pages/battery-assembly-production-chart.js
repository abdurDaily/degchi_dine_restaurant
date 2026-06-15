function getChartColorsArray(e) {
    if (null !== document.getElementById(e)) {
        var t =
                "data-colors" +
                ("-" + document.documentElement.getAttribute("data-theme") ??
                    ""),
            t =
                document.getElementById(e).getAttribute(t) ??
                document.getElementById(e).getAttribute("data-colors");
        if (t)
            return (t = JSON.parse(t)).map(function (e) {
                var t = e.replace(" ", "");
                return -1 === t.indexOf(",")
                    ? getComputedStyle(
                          document.documentElement
                      ).getPropertyValue(t) || t
                    : 2 == (e = e.split(",")).length
                    ? "rgba(" +
                      getComputedStyle(
                          document.documentElement
                      ).getPropertyValue(e[0]) +
                      "," +
                      e[1] +
                      ")"
                    : t;
            });
        console.warn("data-colors attributes not found on", e);
    }
}

var batteryAssemblyProductionChart = "";

function loadCharts() {
    var e = document.getElementById("battery-assembly-production-chart");

    if (e) {
        // Retrieve chart data
        var shifts = JSON.parse(e.getAttribute("data-shifts"));
        var productions = JSON.parse(e.getAttribute("data-productions"));

        // Process data for multiple shifts
        var series = shifts.map((shift, index) => ({
            name: 'Shift'+ ' ' + shift,
            data: [productions[index]],
        }));

        // Fetch colors
        var t = getChartColorsArray("battery-assembly-production-chart");
        if (t) {
            var options = {
                series: series,
                chart: { type: "bar", height: 341, toolbar: { show: !1 } },
                plotOptions: { bar: { horizontal: !1, columnWidth: "65%" } },
                stroke: { show: !0, width: 5, colors: ["transparent"] },
                xaxis: {
                    categories: [""],
                    axisTicks: { show: !1 },
                },
                yaxis: {
                    labels: {
                        formatter: function (e) {
                            return e;
                        },
                    },
                    tickAmount: 4,
                    min: 0,
                },
                fill: { opacity: 1 },
                legend: {
                    show: !0,
                    position: "bottom",
                    horizontalAlign: "center",
                    fontWeight: 500,
                    itemMargin: { horizontal: 8, vertical: 0 },
                    markers: { width: 10, height: 10 },
                },
                colors: t,
            };

            if ("" !== batteryAssemblyProductionChart) {
                batteryAssemblyProductionChart.destroy();
            }

            batteryAssemblyProductionChart = new ApexCharts(
                document.querySelector("#battery-assembly-production-chart"),
                options
            );
            batteryAssemblyProductionChart.render();
        }
    }
}

// Re-render charts on window resize
window.onresize = function () {
    setTimeout(() => {
        loadCharts();
    }, 0);
};

// Initial chart load
loadCharts();
