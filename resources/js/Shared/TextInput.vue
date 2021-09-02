<template>
    <div>
        <label
            v-if="label"
            class="my-2 block font-bold text-sm text-gray-900"
            :for="id"
            >{{ label }}:</label
        >
        <input
            :id="id"
            ref="input"
            v-bind="$attrs"
            class="form-input bg-gray-100 rounded-none shadow-sm"
            :class="{ error: error }"
            :type="type"
            :value="value"
            @input="$emit('input', $event.target.value)"
        />
        <div v-if="error" class="text-sm text-red-600">{{ error }}</div>
    </div>
</template>

<script>
export default {
    inheritAttrs: false,
    props: {
        id: {
            type: String,
            default() {
                return `text-input-${this._uid}`;
            }
        },
        type: {
            type: String,
            default: "text"
        },
        value: [Number, String],
        label: String,
        error: String
    },
    methods: {
        focus() {
            this.$refs.input.focus();
        },
        select() {
            this.$refs.input.select();
        },
        setSelectionRange(start, end) {
            this.$refs.input.setSelectionRange(start, end);
        }
    },
    created() { 
    }
};
</script>
