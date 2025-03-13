<script setup>
import { ref, onMounted, watch } from 'vue';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AlertMessage from '@/Components/AlertMessage.vue';
import { displayResponse, displayWarning } from '@/responseMessage.js';

const props = defineProps(['product', 'id', 'roles', 'colors', 'categories', 'sizes']);

// Form setup
const form = useForm({
    name: props.product?.name ?? '',
    color_id: props.product?.color_id ?? '',
    category_id: props.product?.category_id ?? '',
    size_id: props.product?.size_id ?? '',
    price: props.product?.price ?? '',
    image: '',
    photoPreview: props.product?.image ?? '',
    _method: props.product?.id ? 'put' : 'post',
});

const availableSizes = ref([]);

const fetchCategoryWiseSize = async () => {
    const categoryId = form.category_id;

    console.log('categoryId:', categoryId);

    if (!categoryId) {
        form.size_id = '';  
        availableSizes.value = [];  
        return;
    }

    try {
        const response = await axios.get(route("backend.product.categoryWiseSize", categoryId));

        console.log('response:', response);

        if (response.data && Array.isArray(response.data)) {
            availableSizes.value = response.data;

            console.log('availableSizes:', availableSizes.value);

            if (form.size_id && !availableSizes.value.some(size => size.id === form.size_id)) {
                form.size_id = '';  
            }
        }
    } catch (error) {
        console.error("Error fetching sizes:", error);
        displayWarning(error);
    }
};

watch(() => form.category_id, fetchCategoryWiseSize);

onMounted(() => {
    if (form.category_id) {
        fetchCategoryWiseSize(); 
    }
});

const handleImageChange = (event) => {
    const file = event.target.files[0];
    form.image = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        form.photoPreview = e.target.result;
    };
    reader.readAsDataURL(file);
};

const submit = () => {
    const routeName = props.id ? route('backend.product.update', props.id) : route('backend.product.store');
    form.transform(data => ({
        ...data,
        remember: '',
        isDirty: false,
    })).post(routeName, {
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
        <div class="w-full mt-3 transition duration-1000 ease-in-out transform bg-white border border-gray-700 rounded-md shadow-lg shadow-gray-800/50 dark:bg-slate-900">
            <div class="flex items-center justify-between w-full text-gray-700 bg-gray-100 rounded-md shadow-md dark:bg-gray-800 dark:text-gray-200 shadow-gray-800/50">
                <div>
                    <h1 class="p-4 text-xl font-bold dark:text-white">{{ $page.props.pageTitle }}</h1>
                </div>
                <div class="p-4 py-2"></div>
            </div>

            <form @submit.prevent="submit" class="p-4">
                <AlertMessage />
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4">

                    <!-- Name Field -->
                    <div class="col-span-1 md:col-span-1">
                        <InputLabel for="name" value="Name" />
                        <input id="name"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.name" type="text" placeholder="Name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <!-- Color Dropdown -->
                    <div class="col-span-1 md:col-span-1">
                        <InputLabel for="color" value="Color" />
                        <select id="color"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.color_id">
                            <option value="">Select Color</option>
                            <option v-for="color in colors" :key="color.id" :value="color.id">{{ color.name }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.color" />
                    </div>

                    <!-- Category Dropdown -->
                    <div class="col-span-1 md:col-span-1">
                        <InputLabel for="category_id" value="Category" />
                        <select id="category_id"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.category_id">
                            <option value="">Select Category</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.category_id" />
                    </div>

                    <!-- Size Field -->
                    <div class="col-span-1 md:col-span-1">
                        <InputLabel for="size_id" value="Size" />
                        <select id="size_id"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.size_id">
                            <option value="">Select Size</option>
                            <option v-for="size in availableSizes" :key="size.id" :value="size.id">{{ size.size }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.size_id" />
                    </div>

                    <!-- Price Field -->
                    <div class="col-span-1 md:col-span-1">
                        <InputLabel for="price" value="Price" />
                        <input id="price"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.price" type="text" placeholder="Price" />
                        <InputError class="mt-2" :message="form.errors.price" />
                    </div>

                    <!-- Image Upload -->
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="image" value="Image" />
                        <div v-if="form.photoPreview">
                            <img :src="form.photoPreview" alt="Photo Preview" class="max-w-xs mt-2" height="60" width="60" />
                        </div>
                        <input id="image" type="file" accept="image/*"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            @change="handleImageChange" />
                        <InputError class="mt-2" :message="form.errors.image" />
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
