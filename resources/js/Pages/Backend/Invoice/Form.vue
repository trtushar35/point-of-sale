<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AlertMessage from '@/Components/AlertMessage.vue';
import { displayResponse, displayWarning } from '@/responseMessage.js';

const props = defineProps(['inventory', 'id', 'categories', 'colors', 'sizes']);

const form = useForm({
    // product_no: props.inventory?.product_no ?? '',
    // category_id: props.inventory?.category_id ?? '',
    // color_id: props.inventory?.color_id ?? '',
    // size_id: props.inventory?.size_id ?? '',
    quantity: 1,
    _method: props.inventory?.id ? 'put' : 'post',
});

const productDetails = ref(null);

const fetchProductDetails = async (productNo) => {
    try {
        const response = await axios.get(route('backend.product.details', { product_no: productNo }));

        productDetails.value = response.data;

        form.product_no = productDetails.value.product_no ?? '';
        form.name = productDetails.value.name ?? '';
        form.color_id = productDetails.value.color?.id ?? '';
        form.size_id = productDetails.value.size?.id ?? '';
        form.price = productDetails.value.price ?? '';
        form.total_price = productDetails.value.total_price ?? '';
        form.category_id = productDetails.value.category?.id ?? '';

        fetchCategoryWiseSize();
    } catch (error) {
        form.name = '';
        form.color_id = '';
        form.size_id = '';
        form.price = '';
        form.total_price = '';
        form.category_id = '';

        popupMessage.value = 'Product is not available. Please check the product number and try again.';
        popupType.value = 'error';
        showPopup.value = true;
        setTimeout(() => showPopup.value = false, 2000);
        console.error("Error fetching product details:", error);
    }
};

watch(() => form.product_no, (newProductNo) => {
    if (newProductNo) {
        fetchProductDetails(newProductNo);
    }
});

const submit = () => {
    const routeName = props.id ? route('backend.invoice.update', props.id) : route('backend.invoice.store');

    const formData = {
        products: productsTable.value,
        total_price: totalPrice.value,
    };

    form.transform(data => ({
        ...data,
        products: formData.products,
        total_price: formData.total_price
    })).post(routeName, {
        onSuccess: (response) => {
            form.reset();

            productsTable.value = [];

            if (!props.id) form.reset();
            displayResponse(response);
        },
        onError: (errorObject) => {
            displayWarning(errorObject);
        },
    });
};

const productsTable = ref([]);

const addProductToTable = () => {
    const existingProductIndex = productsTable.value.findIndex(
        (product) => product.product_no === form.product_no
    );

    if (existingProductIndex !== -1) {
        popupMessage.value = 'Product already exists in the table.';
        popupType.value = 'error';
        showPopup.value = true;
        setTimeout(() => showPopup.value = false, 2000);
        return;
    }

    const product = {
        product_no: form.product_no,
        name: form.name,
        color: form.color_id,
        size: form.size_id,
        category: form.category_id,
        price: form.price,
        quantity: form.quantity,
    };

    productsTable.value.push({ ...product });

    popupMessage.value = 'Product added successfully.';
    popupType.value = 'success';
    showPopup.value = true;
    setTimeout(() => showPopup.value = false, 2000);

    form.product_no = '';
    form.name = '';
    form.category_id = '';
    form.color_id = '';
    form.size_id = '';
    form.price = '';
    form.quantity = 1;
};

const showPopup = ref(false);
const popupMessage = ref('');
const popupType = ref('');

const totalPrice = computed(() => {
    return productsTable.value.reduce((total, product) => {
        return total + (product.price * product.quantity);
    }, 0);
});

const removeProduct = (index) => {
    productsTable.value.splice(index, 1);
};

const availableSizes = ref([]);

const fetchCategoryWiseSize = async () => {
    const categoryId = form.category_id;

    if (!categoryId) {
        form.size_id = '';
        availableSizes.value = [];
        return;
    }

    try {
        const response = await axios.get(route("backend.product.categoryWiseSize", categoryId));

        if (response.data && Array.isArray(response.data)) {
            availableSizes.value = response.data;

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

const getCategoryName = (categoryId) => {
    const category = props.categories.find(c => c.id === categoryId);
    return category ? category.name : 'N/A';
};

const getColorName = (colorId) => {
    const color = props.colors.find(c => c.id === colorId);
    return color ? color.name : 'N/A';
};

const getSizeName = (sizeId) => {
    const size = props.sizes.find(s => s.id === sizeId);
    return size ? size.size : 'N/A';
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

                <div v-if="showPopup"
                    :class="['fixed top-2 right-4 p-4 rounded-lg shadow-lg text-white', popupType === 'success' ? 'bg-green-500' : 'bg-red-500']">
                    {{ popupMessage }}
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4">

                    <!-- Product Number Field -->
                    <div class="col-span-1 md:col-span-1">
                        <InputLabel for="product_no" value="Product Number" />
                        <input id="product_no"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.product_no" type="text" placeholder="Product Number" />
                        <InputError class="mt-2" :message="form.errors.product_no" />
                    </div>

                    <!-- Product Name Field -->
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
                            <option v-for="category in categories" :key="category.id" :value="category.id">{{
                                category.name }}</option>
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
                            <option v-for="size in availableSizes" :key="size.id" :value="size.id">{{ size.size }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.size_id" />
                    </div>

                    <!-- Product Price Field -->
                    <div class="col-span-1 md:col-span-1">
                        <InputLabel for="price" value="Price" />
                        <input id="price"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.price" type="text" placeholder="Price" />
                        <InputError class="mt-2" :message="form.errors.price" />
                    </div>

                    <!-- Product Quantity Field -->
                    <div class="col-span-1 md:col-span-1">
                        <InputLabel for="quantity" value="Quantity" />
                        <input id="quantity"
                            class="w-14 p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600 text-start"
                            v-model="form.quantity" type="number" min="1" readonly />
                        <InputError class="mt-2" :message="form.errors.quantity" />
                    </div>

                    <div class="flex items-center justify-start mt-6">
                        <PrimaryButton type="button" @click="addProductToTable" class="ms-4">
                            Add Product
                        </PrimaryButton>
                    </div>
                </div>

                <!-- product added table -->
                <div class="overflow-x-auto mt-20">
                    <table
                        class="min-w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 rounded-md shadow-md">
                        <thead>
                            <tr class="text-center">
                                <th class="py-2 px-4 border-b">Product No</th>
                                <th class="py-2 px-4 border-b border-l">Name</th>
                                <th class="py-2 px-4 border-b border-l">Category</th>
                                <th class="py-2 px-4 border-b border-l">Color</th>
                                <th class="py-2 px-4 border-b border-l">Size</th>
                                <th class="py-2 px-4 border-b border-l">Price</th>
                                <th class="py-2 px-4 border-b border-l">Quantity</th>
                                <th class="py-2 px-4 border-b border-l" colspan="2">Total Price</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <tr v-for="(product, index) in productsTable" :key="index">
                                <td class="py-2 px-4 border-b border-l">{{ product.product_no }}</td>
                                <td class="py-2 px-4 border-b border-l">{{ product.name }}</td>
                                <td class="py-2 px-4 border-b border-l">{{ getCategoryName(product.category) }}</td>
                                <td class="py-2 px-4 border-b border-l">{{ getColorName(product.color) }}</td>
                                <td class="py-2 px-4 border-b border-l">{{ getSizeName(product.size) }}</td>
                                <td class="py-2 px-4 border-b border-l">{{ product.price }}</td>
                                <td class="py-2 px-4 border-b border-l">{{ product.quantity }}</td>
                                <td class="py-2 px-4 border-b border-l text-right" colspan="2">
                                    <div class="flex items-center justify-end space-x-2">
                                        <span>{{ (product.price *
                                            product.quantity).toFixed(2) }}</span>
                                        <svg @click="removeProduct(index)" xmlns="http://www.w3.org/2000/svg"
                                            class="w-6 h-6 text-red-500 cursor-pointer hover:text-red-700"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M6 19C6 19.5523 6.44772 20 7 20H17C17.5523 20 18 19.5523 18 19V9H6V19ZM16 3H8V4H6C5.44772 4 5 4.44772 5 5V6H19V5C19 4.44772 18.5523 4 18 4H16V3Z" />
                                        </svg>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="py-2 px-4 border-b text-center" colspan="7">Total Price</td>
                                <td class="py-2 px-4 border-b border-l text-right mr-6">{{ totalPrice.toFixed(2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton type="submit" class="ms-4" :class="{ 'opacity-25': form.processing }" @click="submit"
                        :disabled="form.processing">
                        Submit
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </BackendLayout>
</template>