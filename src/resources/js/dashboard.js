import Chart from "chart.js/auto";

/**
 * canvas要素と設定を受け取り Chart インスタンスを生成するヘルパー関数
 *
 * @param {string} canvasId - canvas要素のID
 * @param {Function|Object} configBuilder - チャート設定オブジェクト（またはデータを引数に取る関数）
 */
function createChart(canvasId, configBuilder) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !canvas.dataset.chart) return null;

    try {
        const chartData = JSON.parse(canvas.dataset.chart);
        const config =
            typeof configBuilder === "function"
                ? configBuilder(chartData)
                : configBuilder;

        return new Chart(canvas, config);
    } catch (error) {
        console.error(`Chart initialization failed for #${canvasId}:`, error);
        return null;
    }
}

// --------------------------------------------------
// グラフの初期化
// --------------------------------------------------

// 1. 月別商談件数
createChart("monthlyDealsChart", (monthlyDeals) => ({
    type: "bar",
    data: {
        labels: monthlyDeals.labels,
        datasets: [
            {
                label: "商談件数",
                data: monthlyDeals.data,
                borderWidth: 1,
            },
        ],
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false,
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                },
            },
        },
    },
}));

// 2. 商談ステータス（ドーナツグラフ）
createChart("dealStatusChart", (dealStatus) => ({
    type: "doughnut",
    data: {
        labels: dealStatus.labels,
        datasets: [
            {
                data: dealStatus.data,
            },
        ],
    },
    options: {
        responsive: true,
        radius: "80%",
    },
}));

// 3. 月別営業活動件数
createChart("monthlyActivitiesChart", (monthlyActivities) => ({
    type: "line",
    data: {
        labels: monthlyActivities.labels,
        datasets: [
            {
                label: "営業活動件数",
                data: monthlyActivities.data,
                tension: 0.3,
            },
        ],
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false,
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                },
            },
        },
    },
}));

// 4. タスク完了率（ドーナツグラフ）
createChart("taskCompletionChart", (taskCompletion) => ({
    type: "doughnut",
    data: {
        labels: taskCompletion.labels,
        datasets: [
            {
                data: taskCompletion.data,
            },
        ],
    },
    options: {
        responsive: true,
        radius: "80%",
    },
}));
