<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-tl from-teal-400 to-blue-500">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-lg mt-6 px-6 py-6 bg-white shadow-2xl rounded-lg overflow-hidden">
            <slot />
        </div>
    </div>
</template>

<style scoped>
.bg-gradient-to-tl {
    background: linear-gradient(135deg, #64b5f6, #4caf50, #ffeb3b);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
}

.w-full {
    background: #ffffff;
    box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    padding: 24px;
}

.w-full:hover {
    box-shadow: 0px 10px 35px rgba(0, 0, 0, 0.15);
    transform: translateY(-5px);
}

button {
    background-color: #4caf50;
    color: #ffffff;
    font-weight: 600;
    padding: 12px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

button:hover {
    background-color: #388e3c;
    transform: translateY(-2px);
}

h1,
h2,
h3 {
    font-family: 'Roboto', sans-serif;
    font-weight: 600;
    color: #333;
}

p,
label {
    font-family: 'Arial', sans-serif;
    color: #666;
}

input,
select,
textarea {
    width: 100%;
    padding: 14px 16px;
    margin-top: 10px;
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    font-size: 16px;
    background-color: #f9f9f9;
    transition: border 0.3s ease;
}

input:focus,
select:focus,
textarea:focus {
    border-color: #4caf50;
    outline: none;
}

.pos-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.card {
    background: #ffffff;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.05);
    border-radius: 10px;
    padding: 20px;
    transition: all 0.3s ease;
}

.card:hover {
    transform: scale(1.02);
    box-shadow: 0px 6px 25px rgba(0, 0, 0, 0.1);
}

@media (max-width: 640px) {
    .w-full {
        padding: 16px;
    }

    .pos-container {
        grid-template-columns: 1fr;
    }
}
</style>