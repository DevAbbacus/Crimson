<template>
    <button
        :disabled="loading"
        class="inline-flex items-center justify-center px-4 py-2 bg-red-500 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 focus:outline-none focus:border-red-700 focus:shadow-outline-red active:bg-red-600 transition ease-in-out duration-150"
    >
        <div v-if="loading" class="btn-spinner mr-2" />
        <slot />
    </button>
</template>

<script>
export default {
    props: {
        loading: Boolean
    }
};
</script>
