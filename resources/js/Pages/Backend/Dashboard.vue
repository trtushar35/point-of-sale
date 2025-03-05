<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import BarChart from '@/Components/Chart/BarChart.vue';
import PieChart from '@/Components/Chart/PieChart.vue';
import DoughnutChart from '@/Components/Chart/DoughnutChart.vue';
import LineChart from '@/Components/Chart/LineChart.vue';
import PolarAreaChart from '@/Components/Chart/PolarAreaChart.vue';
import RadarChart from '@/Components/Chart/RadarChart.vue';

const props = defineProps(['dashboardData']);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <BackendLayout>
        <section class="w-full mt-4 transition duration-1000 ease-in-out">
            <div class="grid gap-4 xs:grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">



                <div class="w-full h-20 pl-1 bg-yellow-400 rounded-lg shadow-md">
                    <div class="flex justify-between w-full h-full px-4 py-2 bg-white rounded-lg">
                        <div class="my-auto">
                            <p class="text-sm font-bold sm:text-md">Active Users</p>
                            <p class="text-lg">{{ dashboardData.activeUsers }} </p>
                        </div>
                        <div class="my-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="w-full h-20 pl-1 bg-yellow-400 rounded-lg shadow-md">
                    <div class="flex justify-between w-full h-full px-4 py-2 bg-white rounded-lg">
                        <div class="my-auto">
                            <p class="text-sm font-bold sm:text-md">Inactive Users</p>
                            <p class="text-lg">{{ dashboardData.inActiveUsers }} </p>
                        </div>
                        <div class="my-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="w-full h-20 pl-1 bg-yellow-400 rounded-lg shadow-md">
                    <div class="flex justify-between w-full h-full px-4 py-2 bg-white rounded-lg">
                        <div class="my-auto">
                            <p class="text-sm font-bold sm:text-md">Active Users</p>
                            <p class="text-lg">{{ dashboardData.activeUsers }} </p>
                        </div>
                        <div class="my-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="w-full h-20 pl-1 bg-yellow-400 rounded-lg shadow-md">
                    <div class="flex justify-between w-full h-full px-4 py-2 bg-white rounded-lg">
                        <div class="my-auto">
                            <p class="text-sm font-bold sm:text-md">Inactive Users</p>
                            <p class="text-lg">{{ dashboardData.inActiveUsers }} </p>
                        </div>
                        <div class="my-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>



            </div>
        </section>

        <section class="grid gap-4 mt-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="flex flex-col bg-white rounded-lg shadow md:col-span-2 md:row-span-2">
                <div class="px-6 py-5 font-semibold border-b border-gray-100">The Attendance and left Employee per month
                </div>
                <div class="flex-grow p-4">
                    <div
                        class="flex items-center justify-center h-full px-4 py-16 text-3xl font-semibold text-gray-400 bg-gray-100 border-2 border-gray-200 border-dashed rounded-md">
                        <BarChart />
                    </div>
                </div>
            </div>

            <div class="flex flex-col bg-white rounded-lg shadow md:col-span-2 md:row-span-2">
                <div class="px-6 py-5 font-semibold border-b border-gray-100">The Attendance and left Employee per month
                </div>
                <div class="flex-grow p-4">
                    <div
                        class="flex items-center justify-center h-full px-4 py-16 text-3xl font-semibold text-gray-400 bg-gray-100 border-2 border-gray-200 border-dashed rounded-md">
                        <PieChart />
                    </div>
                </div>
            </div>

        </section>

        <section class="grid gap-4 mt-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="flex flex-col bg-white rounded-lg shadow md:col-span-2 md:row-span-2">
                <div class="px-6 py-5 font-semibold border-b border-gray-100">The Attendance and left Employee per month
                </div>
                <div class="flex-grow p-4">
                    <div
                        class="flex items-center justify-center h-full px-4 py-16 text-3xl font-semibold text-gray-400 bg-gray-100 border-2 border-gray-200 border-dashed rounded-md">
                        <DoughnutChart />
                    </div>
                </div>
            </div>

            <div class="flex flex-col bg-white rounded-lg shadow md:col-span-2 md:row-span-2">
                <div class="px-6 py-5 font-semibold border-b border-gray-100">The Attendance and left Employee per month
                </div>
                <div class="flex-grow p-4">
                    <div
                        class="flex items-center justify-center h-full px-4 py-16 text-3xl font-semibold text-gray-400 bg-gray-100 border-2 border-gray-200 border-dashed rounded-md">
                        <LineChart />
                    </div>
                </div>
            </div>

        </section>

        <section class="grid gap-4 mt-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="flex flex-col bg-white rounded-lg shadow md:col-span-2 md:row-span-2">
                <div class="px-6 py-5 font-semibold border-b border-gray-100">The Attendance and left Employee per month
                </div>
                <div class="flex-grow p-4">
                    <div
                        class="flex items-center justify-center h-full px-4 py-16 text-3xl font-semibold text-gray-400 bg-gray-100 border-2 border-gray-200 border-dashed rounded-md">
                        <PolarAreaChart />
                    </div>
                </div>
            </div>

            <div class="flex flex-col bg-white rounded-lg shadow md:col-span-2 md:row-span-2">
                <div class="px-6 py-5 font-semibold border-b border-gray-100">The Attendance and left Employee per month
                </div>
                <div class="flex-grow p-4">
                    <div
                        class="flex items-center justify-center h-full px-4 py-16 text-3xl font-semibold text-gray-400 bg-gray-100 border-2 border-gray-200 border-dashed rounded-md">
                        <RadarChart />
                    </div>
                </div>
            </div>

        </section>

    </BackendLayout>
</template>


<!--<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, nextTick } from 'vue';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps(['dashboardData']);

const dashboardData = {
    purchaseDue: 10,
    salesDue: 5,
    customers: 9,
    suppliers: 3,
    invoices: 0,
    sales: 0,
    expenses: 0,
    purchases: 0,
    inventory: 100,   // New field for Inventory
    pendingOrders: 8, // New field for Pending Orders
    revenue: 50000,   // New field for Revenue
    recentItems: [
        { slNo: 1, name: 'Item 1', price: '22,840.00' },
        { slNo: 2, name: 'Item 2', price: '1,000.00' },
        { slNo: 3, name: 'Item 3', price: '5,000.00' },
    ]
};

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const createChart = () => {
    const ctx = document.getElementById('myChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Purchase', 'Sales', 'Expense'],
                datasets: [{
                    label: 'Amount',
                    data: [20000, 15000, 10000],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    } else {
        console.error("Chart element not found");
    }
};

onMounted(() => {
    nextTick(() => {
        createChart();
    });
});
</script>

<template>
    <BackendLayout>
         <section class="w-full mt-4 bg-gray-100 p-4">
            <div class="bg-green-600 text-white p-2 text-center rounded-lg">
                <h1 class="text-xl font-bold">Welcome Admin!</h1>
            </div>

            <div class="flex justify-end space-x-2 mt-4">
                <button class="bg-blue-500 text-white px-4 py-2 rounded">Today</button>
                <button class="bg-blue-500 text-white px-4 py-2 rounded">Weekly</button>
                <button class="bg-blue-500 text-white px-4 py-2 rounded">Monthly</button>
                <button class="bg-blue-500 text-white px-4 py-2 rounded">Yearly</button>
            </div>

            <div class="flex flex-wrap gap-4 mt-4">
                <div
                    class="w-full sm:w-[calc(50%-8px)] md:w-[calc(25%-12px)] bg-gradient-to-r from-purple-500 to-purple-400 rounded-lg shadow-md p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-white">PURCHASE DUE</p>
                        <p class="text-2xl font-bold text-white">{{ dashboardData.purchaseDue }}</p>
                    </div>
                    <i class="fas fa-box fa-2x text-white"></i>
                </div>

                <div
                    class="w-full sm:w-[calc(50%-8px)] md:w-[calc(25%-12px)] bg-gradient-to-r from-red-500 to-red-400 rounded-lg shadow-md p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-white">SALES DUE</p>
                        <p class="text-2xl font-bold text-white">{{ dashboardData.salesDue }}</p>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x text-white"></i>
                </div>

                <div
                    class="w-full sm:w-[calc(50%-8px)] md:w-[calc(25%-12px)] bg-gradient-to-r from-green-500 to-green-400 rounded-lg shadow-md p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-white">CUSTOMERS</p>
                        <p class="text-2xl font-bold text-white">{{ dashboardData.customers }}</p>
                    </div>
                    <i class="fas fa-user-friends fa-2x text-white"></i>
                </div>

                <div
                    class="w-full sm:w-[calc(50%-8px)] md:w-[calc(25%-12px)] bg-gradient-to-r from-blue-500 to-blue-400 rounded-lg shadow-md p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-white">SUPPLIERS</p>
                        <p class="text-2xl font-bold text-white">{{ dashboardData.suppliers }}</p>
                    </div>
                    <i class="fas fa-truck fa-2x text-white"></i>
                </div>

                <div
                    class="w-full sm:w-[calc(50%-8px)] md:w-[calc(25%-12px)] bg-gradient-to-r from-teal-500 to-teal-400 rounded-lg shadow-md p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-white">INVOICES</p>
                        <p class="text-2xl font-bold text-white">{{ dashboardData.invoices }}</p>
                    </div>
                    <i class="fas fa-file-invoice-dollar fa-2x text-white"></i>
                </div>

                <div
                    class="w-full sm:w-[calc(50%-8px)] md:w-[calc(25%-12px)] bg-gradient-to-r from-orange-500 to-orange-400 rounded-lg shadow-md p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-white">SALES</p>
                        <p class="text-2xl font-bold text-white">{{ dashboardData.sales }}</p>
                    </div>
                    <i class="fas fa-chart-line fa-2x text-white"></i>
                </div>

                <div
                    class="w-full sm:w-[calc(50%-8px)] md:w-[calc(25%-12px)] bg-gradient-to-r from-pink-500 to-pink-400 rounded-lg shadow-md p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-white">EXPENSES</p>
                        <p class="text-2xl font-bold text-white">{{ dashboardData.expenses }}</p>
                    </div>
                    <i class="fas fa-money-bill-alt fa-2x text-white"></i>
                </div>

                <div
                    class="w-full sm:w-[calc(50%-8px)] md:w-[calc(25%-12px)] bg-gradient-to-r from-indigo-500 to-indigo-400 rounded-lg shadow-md p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-white">PURCHASES</p>
                        <p class="text-2xl font-bold text-white">{{ dashboardData.purchases }}</p>
                    </div>
                    <i class="fas fa-shopping-basket fa-2x text-white"></i>
                </div>

                 <div
                    class="w-full sm:w-[calc(50%-8px)] md:w-[calc(25%-12px)] bg-gradient-to-r from-yellow-500 to-yellow-400 rounded-lg shadow-md p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-white">INVENTORY</p>
                        <p class="text-2xl font-bold text-white">{{ dashboardData.inventory }}</p>
                    </div>
                    <i class="fas fa-cogs fa-2x text-white"></i>
                </div>

                <div
                    class="w-full sm:w-[calc(50%-8px)] md:w-[calc(25%-12px)] bg-gradient-to-r from-indigo-600 to-indigo-500 rounded-lg shadow-md p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-white">PENDING ORDERS</p>
                        <p class="text-2xl font-bold text-white">{{ dashboardData.pendingOrders }}</p>
                    </div>
                    <i class="fas fa-hourglass-half fa-2x text-white"></i>
                </div>

                <div
                    class="w-full sm:w-[calc(50%-8px)] md:w-[calc(25%-12px)] bg-gradient-to-r from-teal-600 to-teal-500 rounded-lg shadow-md p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-white">REVENUE</p>
                        <p class="text-2xl font-bold text-white">{{ dashboardData.revenue | currency }}</p>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x text-white"></i>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4 mt-8">
                <div class="w-full md:w-2/3 bg-white rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-bold mb-4">Purchase, Sales & Expense Bar Chart</h2>
                    <canvas id="myChart"></canvas>
                </div>

                <div class="w-full md:w-1/3 bg-white rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-bold mb-4">Recently Added Items</h2>
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="py-2">Sl.No</th>
                                <th class="py-2">Item Name</th>
                                <th class="py-2">Item Sales Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in dashboardData.recentItems" :key="item.id">
                                <td class="border py-2 text-center">{{ item.slNo }}</td>
                                <td class="border py-2 text-center">{{ item.name }}</td>
                                <td class="border py-2 text-center">{{ item.price }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section> 
    </BackendLayout>
</template>

<style>
.table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
}

@media (max-width: 600px) {
    .grid {
        grid-template-columns: 1fr;
    }
}
</style> -->
