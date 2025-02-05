<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-r from-teal-300 via-pink-400 to-yellow-500 bg-repeat-x bg-[url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22 width=%22100%22 height=%22100%22%3E%3Cpath fill=%22none%22 stroke=%22%23ffffff%22 stroke-width=%221%22 d=%22M0 25Q25 10 50 25T100 25%22/%3E%3C/svg%3E']">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-gradient-to-r from-teal-200 to-pink-200 shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
