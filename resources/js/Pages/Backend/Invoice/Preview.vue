<script setup>
import { ref } from "vue";
import BackendLayout from '@/Layouts/BackendLayout.vue';
import BaseTable from '@/Components/BaseTable.vue';
import Pagination from '@/Components/Pagination.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    invoice: Object,
});

const printInvoice = () => {
    window.print();
};
</script>

<template>
    <BackendLayout>
        <div class="p-4 col-xs bg-white rounded-lg shadow-sm print:p-0 print:shadow-none">

            <div class="text-center mb-4">
                <h1 class="text-2xl font-bold">Invoice</h1>
                <p class="text-sm text-gray-600">Thank you for your purchase!</p>
            </div>

            <div class="mb-6">
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Invoice No:</span>
                    <span>{{ invoice.invoice_no }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Date:</span>
                    <span>{{ invoice.invoice_date }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Status:</span>
                    <span>{{ invoice.status }}</span>
                </div>
            </div>

            <table class="w-full mb-6">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2  text-center">S/N</th>
                        <th class="px-4 py-2  text-center">Product No</th>
                        <th class="px-4 py-2  text-center">Name</th>
                        <th class="px-4 py-2  text-center">Quantity</th>
                        <th class="px-4 py-2  text-center">Price</th>
                        <th class="px-4 py-2  text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(product, index) in invoice.products" :key="index" class="border-b">
                        <td class="px-4 py-2 text-center">{{ index+1 }}</td>
                        <td class="px-4 py-2 text-center">{{ product.product_no }}</td>
                        <td class="px-4 py-2 text-center">{{ product.name }}</td>
                        <td class="px-4 py-2 text-center">{{ product.quantity }}</td>
                        <td class="px-4 py-2 text-center">{{ product.price }}</td>
                        <td class="px-4 py-2 text-center">{{ product.total }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="text-right" style="margin-right: 60px;">
                <span class="font-semibold">Total:</span>
                <span class="text-xl font-bold">{{ invoice.total_price }}</span>
            </div>

            <div class="mt-6 text-center print:hidden">
                <button @click="printInvoice" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Print Invoice
                </button>
            </div>
        </div>
    </BackendLayout>
</template>

<style>
@media print {
    body {
        margin: 0;
        padding: 0;
    }

    .print\:p-0 {
        padding: 0 !important;
    }

    .print\:shadow-none {
        box-shadow: none !important;
    }

    .print-hidden {
        display: none !important;
    }
}
</style>