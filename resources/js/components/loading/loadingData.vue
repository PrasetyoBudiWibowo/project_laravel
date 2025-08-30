<template>
    <div
        v-if="visible"
        class="fixed inset-0 flex flex-col items-center justify-center bg-white/60 backdrop-blur-sm z-50 animate-fade-in"
    >
        <div class="relative">
            <div
                class="h-16 w-16 border-4 border-blue-300 rounded-full animate-spin border-t-transparent"
            ></div>
            <div
                class="absolute inset-2 bg-blue-500 rounded-full animate-pulse opacity-80"
            ></div>
        </div>
        <p class="mt-4 text-blue-700 font-semibold animate-bounce">
            {{ message }}
        </p>
    </div>
</template>
<script>
export default {
    name: "LaoadingData",
    props: {
        visible: {
            type: Boolean,
            default: false,
        },
        message: {
            type: String,
            default: "Memuat data...",
        },
    },
};
</script>
