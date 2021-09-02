<template>
    <div class="border rounded overflow-hidden flex">
        <input
            type="text"
            class="px-4 py-2"
            autocomplete="off"
            name="search"
            placeholder="Search..."
            :value="value"
            @input="$emit('input', $event.target.value)"
        />
        <button class="flex items-center justify-center px-4 border-l">
            <svg
                class="h-5 w-5 text-gray-800"
                fill="currentColor"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
            >
                <path
                    d="M16.32 14.9l5.39 5.4a1 1 0 0 1-1.42 1.4l-5.38-5.38a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"
                />
            </svg>
        </button>
    </div>
</template>

<script>
export default {
    props: {
        value: String
    }
};
</script>
