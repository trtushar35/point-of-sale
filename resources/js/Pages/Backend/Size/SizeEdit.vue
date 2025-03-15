<script setup>
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AlertMessage from '@/Components/AlertMessage.vue';
import { displayResponse, displayWarning } from '@/responseMessage.js';

const props = defineProps(['Size', 'id', 'categories']);

const form = useForm({
    category_id: props.Size?.category_id ?? '',
    size: props.Size?.size ?? '',

    _method: props.Size?.id ? 'put' : 'post',
});

// Handle form submission
const submit = () => {
    const routesize = props.id ? route('backend.Size.update', props.id) : route('backend.Size.store');
    form.transform(data => ({
        ...data,
        remember: '',
        isDirty: false,
    })).post(routesize, {
        onSuccess: (response) => {
            if (!props.id) form.reset();
            displayResponse(response);
        },
        onError: (errorObject) => {
            displayWarning(errorObject);
        },
    });
};
</script>

<template>
    <BackendLayout>
        <div
            class="w-full mt-3 transition duration-1000 ease-in-out transform bg-white border border-gray-700 rounded-md shadow-lg shadow-gray-800/50 dark:bg-slate-900">

            <div
                class="flex items-center justify-between w-full text-gray-700 bg-gray-100 rounded-md shadow-md dark:bg-gray-800 dark:text-gray-200 shadow-gray-800/50">
                <div>
                    <h1 class="p-4 text-xl font-bold dark:text-white">{{ $page.props.pageTitle }}</h1>
                </div>
                <div class="p-4 py-2"></div>
            </div>

            <form @submit.prevent="submit" class="p-4">
                <AlertMessage />
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4">

                    <!-- Category Dropdown -->
                    <div class="col-span-1 md:col-span-1">
                        <InputLabel for="category_id" value="Category Name" />
                        <select id="category_id"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.category_id">
                            <option value="">Select Category</option>
                            <option v-for="data in categories" :key="data.id" :value="data.id">{{ data.name }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.category_id" />
                    </div>

                    <!-- size Field -->
                    <div class="col-span-1 md:col-span-1">
                        <InputLabel for="size" value="Size" />
                        <input id="size"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.size" type="text" placeholder="Size" />
                        <InputError class="mt-2" :message="form.errors.size" />
                    </div>

                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton type="submit" class="ms-4" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        {{ props.id ? 'Update' : 'Create' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </BackendLayout>
</template>
