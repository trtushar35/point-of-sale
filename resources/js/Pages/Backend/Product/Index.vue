<script setup>
import { ref } from "vue";
import BackendLayout from '@/Layouts/BackendLayout.vue';
import BaseTable from '@/Components/BaseTable.vue';
import Pagination from '@/Components/Pagination.vue';
import { router } from '@inertiajs/vue3';

let props = defineProps({
    filters: Object,
    roles: Array,
    colors: Array,
    categories: Array,
});

const filters = ref({
    name: props.filters?.name ?? '',
    color_id: props.filters?.color_id ?? '',
    category_id: props.filters?.category_id ?? '',
    priceFrom: props.filters?.priceFrom ?? '',
    priceTo: props.filters?.priceTo ?? '',
    numOfData: props.filters?.numOfData ?? 10,
});

const downloadFrom = ref('');
const downloadTo = ref('');

const applyFilter = () => {
    router.get(route('backend.product.index'), filters.value, { preserveState: true });
};

const downloadPdf = async () => {
    try {
        window.location = route('backend.product.downloadPdf', {
            from: downloadFrom.value,
            to: downloadTo.value,
        });
        downloadFrom.value = '';
        downloadTo.value = '';
    } catch (error) {
        console.error('Error downloading PDF:', error);
    }
};
</script>

<template>
    <BackendLayout>
        <div class="w-full p-4 mt-3 duration-1000 ease-in-out bg-white rounded-md shadow-md shadow-gray-800/50 dark:bg-slate-900">
            <h1 class="py-2 text-xl font-bold dark:text-white">{{ $page.props.pageTitle }}</h1>

            <div class="flex justify-between w-full p-2 py-3 space-x-2 text-gray-700 rounded-md shadow-md bg-slate-300 shadow-gray-800/50 dark:bg-gray-700 dark:text-gray-200">
                <div class="grid w-full grid-cols-1 gap-2 md:grid-cols-4">
                    <div class="flex space-x-2">
                        <div class="w-full">
                            <select v-model="filters.category_id" @change="applyFilter" class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600">
                                <option value="">Choose Category</option>
                                <template v-for="data in categories">
                                    <option :value="data.id">{{ data.name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="block min-w-24 md:hidden">
                            <select v-model="filters.numOfData" @change="applyFilter" class="w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600">
                                <option value="10">Show 10</option>
                                <option value="20">Show 20</option>
                                <option value="30">Show 30</option>
                                <option value="40">Show 40</option>
                                <option value="100">Show 100</option>
                                <option value="150">Show 150</option>
                                <option value="500">Show 500</option>
                            </select>
                        </div>
                    </div>

                    <div class="w-full">
                        <select v-model="filters.color_id" @change="applyFilter" class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600">
                            <option value="">Choose Color</option>
                            <template v-for="data in colors">
                                <option :value="data.id">{{ data.name }}</option>
                            </template>
                        </select>
                    </div>

                    <div class="w-full flex col-span-2">
                        <input v-model="filters.priceFrom" type="number" placeholder="Price From" class="block w-full p-2 text-sm text-left rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600 placeholder:text-gray-400 dark:placeholder:text-gray-500" @input="applyFilter" />
                        <input v-model="filters.priceTo" type="number" placeholder="Price To" class="block w-full p-2 text-sm text-left rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600 placeholder:text-gray-400 dark:placeholder:text-gray-500" @input="applyFilter" />
                    </div>
                </div>

                <div class="flex justify-end space">
                    <input v-model="downloadFrom" type="number" placeholder="From" class="block w-24 p-2 text-sm text-left rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600 placeholder:text-gray-400 dark:placeholder:text-gray-500" />
                    <input v-model="downloadTo" type="number" placeholder="To" class="block w-24 p-2 text-sm text-left rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600 placeholder:text-gray-400 dark:placeholder:text-gray-500" />
                    <button @click="downloadPdf" class="bg-green-500 text-white px-3 rounded-md">
                        PDF
                    </button>
                </div>

                <div class="hidden min-w-24 md:block">
                    <select v-model="filters.numOfData" @change="applyFilter" class="w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600">
                        <option value="10">Show 10</option>
                        <option value="20">Show 20</option>
                        <option value="30">Show 30</option>
                        <option value="40">Show 40</option>
                        <option value="100">Show 100</option>
                        <option value="150">Show 150</option>
                        <option value="500">Show 500</option>
                    </select>
                </div>
            </div>

            <div class="w-full my-3 overflow-x-auto">
                <BaseTable />
            </div>

            <Pagination />
        </div>
    </BackendLayout>
</template>