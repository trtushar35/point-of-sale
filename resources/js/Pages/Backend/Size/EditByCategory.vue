<script setup>
import { ref, onMounted } from "vue";
import { router } from '@inertiajs/vue3';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { displayResponse, displayWarning } from '@/responseMessage.js';

const props = defineProps({
    category: Object,
    sizes: Array,
    categories: Array,
});

const form = ref({
    sizes: props.sizes.map(size => ({ id: size.id, size: size.size })),
});

const submit = () => {
    const payload = {
        category_id: props.category.id,
        sizes: form.value.sizes.map(size => ({
            id: size.id, 
            size: size.size,
        })),
    };

    router.put(route('backend.Size.update', props.category.id), payload, {
        preserveScroll: true,
        onSuccess: (response) => {
            console.log(response);
            displayResponse(response);
        },
        onError: (errorObject) => {
            displayWarning(errorObject);
        },
    });
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
</script>

<template>
    <BackendLayout>
        <div
            class="w-full mt-3 p-4 duration-1000 ease-in-out bg-white rounded-md shadow-md shadow-gray-800/50 dark:bg-slate-900">
            <h1 class="py-2 text-xl font-bold dark:text-white">Edit Sizes for {{ category.name }}</h1>

            <form @submit.prevent="submit">
                <div class="space-y-4">
                    <div v-for="(size, index) in form.sizes" :key="size.id" class="flex items-center space-x-4">
                        <input v-model="size.size"
                            class="block w-40 p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            placeholder="Size" />
                            <svg @click="deleteSize(size.id)" xmlns="http://www.w3.org/2000/svg"
                            class="w-6 h-6 text-red-500 cursor-pointer hover:text-red-700" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M6 19C6 19.5523 6.44772 20 7 20H17C17.5523 20 18 19.5523 18 19V9H6V19ZM16 3H8V4H6C5.44772 4 5 4.44772 5 5V6H19V5C19 4.44772 18.5523 4 18 4H16V3Z" />
                        </svg>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </BackendLayout>
</template>