<script setup>
import { ref, onMounted, watch } from 'vue';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AlertMessage from '@/Components/AlertMessage.vue';
import { displayResponse, displayWarning } from '@/responseMessage.js';
import Multiselect from 'vue-multiselect';

const props = defineProps(['product', 'id', 'colors', 'categories', 'sizes']);

const form = useForm({
    name: props.product?.name ?? '',
    color_id: props.product?.colors?.map(color => color.id) ?? [],
    category_id: props.product?.category_id ?? '',
    sizes: props.product?.sizes?.map(size => ({ id: size.id, quantity: size.quantity ?? 1 })) ?? [{ id: '', quantity: 1 }],
    price: props.product?.price ?? '',
    image: '',
    photoPreview: props.product?.image ?? '',
    _method: props.product?.id ? 'put' : 'post',
});

const availableSizes = ref([]);

const fetchCategoryWiseSize = async () => {
    if (!form.category_id) {
        form.sizes = [{ id: '', quantity: 1 }];
        availableSizes.value = [];
        return;
    }

    try {
        const response = await axios.get(route("backend.product.categoryWiseSize", form.category_id));
        if (response.data && Array.isArray(response.data)) {
            availableSizes.value = response.data;
            form.sizes = form.sizes.filter(size => availableSizes.value.some(s => s.id === size.id));
        }
    } catch (error) {
        console.error("Error fetching sizes:", error);
        displayWarning(error);
    }
};

watch(() => form.category_id, fetchCategoryWiseSize);

onMounted(() => {
    if (form.category_id) fetchCategoryWiseSize();
});

const handleImageChange = (event) => {
    const file = event.target.files[0];
    form.image = file;
    const reader = new FileReader();
    reader.onload = (e) => form.photoPreview = e.target.result;
    reader.readAsDataURL(file);
};

const submit = () => {
    form.transform(data => ({
        ...data,
        color_id: form.color_id.map(color => color.id || color),
        sizes: form.sizes.map(size => ({ id: size.id, quantity: size.quantity })),
        remember: '',
        isDirty: false,
    })).post(props.id ? route('backend.product.update', props.id) : route('backend.product.store'), {
        onSuccess: (response) => {
            if (!props.id) form.reset();
            displayResponse(response);
        },
        onError: displayWarning,
    });
};
</script>

<template>
    <BackendLayout>
        <div class="w-full mt-3 bg-white border border-gray-200 rounded-lg shadow dark:bg-slate-900 dark:border-gray-700">
            <div class="flex items-center justify-between w-full text-gray-700 bg-gray-50 dark:bg-gray-800 rounded-t-lg">
                <h1 class="p-4 text-xl font-bold dark:text-white">{{ $page.props.pageTitle }}</h1>
            </div>
            <form @submit.prevent="submit" class="p-6">
                <AlertMessage />
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <!-- Column 1 -->
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="name" value="Product Name" class="mb-1" />
                            <input id="name" v-model="form.name" type="text" placeholder="Enter product name" 
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" />
                            <InputError :message="form.errors.name" class="mt-1" />
                        </div>
                        
                        <div>
                            <InputLabel for="category_id" value="Category" class="mb-1" />
                            <select id="category_id" v-model="form.category_id" 
                                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                <option value="">Select a category</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.category_id" class="mt-1" />
                        </div>
                        
                        <div>
                            <InputLabel for="price" value="Price" class="mb-1" />
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                <input id="price" v-model="form.price" type="text" placeholder="0.00" 
                                    class="w-full pl-8 p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" />
                            </div>
                            <InputError :message="form.errors.price" class="mt-1" />
                        </div>
                    </div>
                    
                    <!-- Column 2 -->
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="color_id" value="Available Colors" class="mb-1" />
                            <Multiselect
                                v-model="form.color_id" 
                                :options="colors" 
                                :multiple="true" 
                                label="name" 
                                track-by="id" 
                                placeholder="Select colors"
                                class="border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600" />
                            <InputError :message="form.errors.color_id" class="mt-1" />
                        </div>
                        
                        <div>
                            <InputLabel value="Sizes & Quantities" class="mb-1 initial-call" />
                            <div v-for="(size, index) in form.sizes" :key="index" class="flex items-center gap-3 mb-3">
                                <div class="flex-1">
                                    <Multiselect 
                                        v-model="size.id" 
                                        :options="availableSizes" 
                                        label="size" 
                                        track-by="id" 
                                        placeholder="Select size"
                                        class="border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600" />
                                </div>
                                <div class="w-24">
                                    <input 
                                        type="number" 
                                        v-model="size.quantity" 
                                        min="1" 
                                        placeholder="Qty"
                                        class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" />
                                </div>
                                <button 
                                    type="button" 
                                    @click="form.sizes.splice(index, 1)"
                                    class="p-2 text-red-500 hover:text-red-700 dark:hover:text-red-400"
                                    :disabled="form.sizes.length <= 1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                            <button 
                                type="button" 
                                @click="form.sizes.push({ id: '', quantity: 1 })"
                                class="flex items-center px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Size
                            </button>
                            <InputError :message="form.errors.sizes" class="mt-1" />
                        </div>
                    </div>
                    
                    <!-- Column 3 -->
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="image" value="Product Image" class="mb-1" />
                            <div class="relative w-full h-48 overflow-hidden border-2 border-dashed border-gray-300 rounded-lg dark:border-gray-600">
                                <img v-if="form.photoPreview" :src="form.photoPreview" alt="Product preview" class="object-cover w-full h-full" />
                                <div v-else class="flex flex-col items-center justify-center w-full h-full text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="mt-2 text-sm">Upload product image</span>
                                </div>
                            </div>
                            <label for="image" class="block w-full px-4 py-2 mt-2 text-sm font-medium text-center text-white bg-blue-600 rounded-lg cursor-pointer hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                                Choose File
                                <input id="image" type="file" @change="handleImageChange" class="hidden" />
                            </label>
                            <p class="mt-1 text-xs text-center text-gray-500 dark:text-gray-400">JPG, PNG or GIF (Max 4MB)</p>
                            <InputError :message="form.errors.image" class="mt-1" />
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end mt-6">
                    <PrimaryButton 
                        type="submit" 
                        :disabled="form.processing"
                        class="px-6 py-3 text-base font-medium">
                        <span v-if="form.processing">
                            <svg class="w-5 h-5 mr-2 -ml-1 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                        <span v-else>
                            {{ props.id ? 'Update Product' : 'Create Product' }}
                        </span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </BackendLayout>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>