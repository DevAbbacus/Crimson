<template>
  <div class="mb-4 flex items-start">
    <div class="flex items-center h-5">
      <input
        :id="id"
        type="checkbox"
        :value="value"
        class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
        v-model="checkvalue"
        v-on:change="$emit('change', checkvalue)"
      />
    </div>
    <div class="ml-3 text-sm leading-5">
      <label :for="id" class="font-normal text-gray-700">{{ label }}</label>
    </div>
  </div>
</template>

<script>
export default {
  model: {
    prop: "checked",
    event: "change",
  },
  data() {
      return { checkvalue: [] }
  },
  props: {
    id: {
      type: String,
      default() {
        return `checkbox-input-${this._uid}`;
      },
    },
    value: [Number, String],
    label: String,
  },
};
</script>
