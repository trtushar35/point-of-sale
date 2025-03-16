<script setup>
import { ref } from "vue";
import { router } from '@inertiajs/vue3';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { displayResponse, displayWarning } from '@/responseMessage.js';

const props = defineProps({
    groupedSizes: Object,
    categories: Array,
    filters: Object,
});

const filters = ref({
    category_id: props.filters?.category_id || '',
    numOfData: props.filters?.numOfData || 10,
});

const applyFilter = () => {
    router.get(route('backend.Size.index'), filters.value, { preserveState: true });
};

const deleteSize = (sizeId) => {
    if (confirm('Are you sure you want to delete this size?')) {
        router.visit(route('backend.Size.delete', sizeId), {
            onSuccess: (response) => {
                displayResponse(response);
            },
            onError: (errorObject) => {
                displayWarning(errorObject);
            },
        });
    }
};

const editCategorySizes = (categoryId) => {
    router.visit(route('backend.Size.editByCategory', categoryId));
};
</script>

<template>
    <BackendLayout>
        <div
            class="w-full p-4 mt-3 duration-1000 ease-in-out bg-white rounded-md shadow-md shadow-gray-800/50 dark:bg-slate-900">
            <h1 class="py-2 text-xl font-bold dark:text-white">Size List</h1>

            <!-- Filter Section -->
            <div
                class="flex justify-between w-full p-2 py-3 space-x-2 text-gray-700 rounded-md shadow-md bg-slate-300 shadow-gray-800/50 dark:bg-gray-700 dark:text-gray-200">
                <div class="grid w-full grid-cols-1 gap-2 md:grid-cols-5">
                    <div class="flex space-x-2">
                        <div class="w-full">
                            <select id="category_id" @change="applyFilter" v-model="filters.category_id"
                                class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600">
                                <option value="">Select Category</option>
                                <option v-for="data in categories" :key="data.id" :value="data.id">{{ data.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horizontal Category Cards -->
            <div class="w-full my-3 overflow-x-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="(sizes, category) in groupedSizes" :key="category"
                        class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-slate-800 dark:to-slate-700 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                        <!-- Category Name -->
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">{{ category }}</h2>

                        <!-- Sizes Row -->
                        <div class="flex flex-wrap items-center gap-2 mb-4">
                            <div v-for="size in sizes" :key="size.id"
                                class="group relative text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-slate-700 px-3 py-1 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors duration-200">
                                {{ size.size }}
                                <!-- Cross Icon for Delete (Visible on Hover) -->
                                <button @click="deleteSize(size.id)"
                                    class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-red-500 hover:text-red-700" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Edit Button -->
                        <div class="flex items-center">
                            <button @click="editCategorySizes(sizes[0].category_id)"
                                class="text-sm text-blue-500 hover:text-blue-700 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BackendLayout>
</template>